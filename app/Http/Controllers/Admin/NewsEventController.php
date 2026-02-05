<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Models\GalleryAlbum;
use App\Models\GalleryPhoto;

class NewsEventController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $type = $request->query('type');
        $sort = $request->query('sort');

        $query = NewsEvent::query()->withCount(['reactions', 'comments']);

        // Search Filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Type Filter
        if ($type && in_array($type, ['news', 'event', 'announcement'])) {
            $query->where('type', $type);
        }

        // Sorting Logic
        if ($sort) {
            switch ($sort) {
                case 'oldest':
                    $query->oldest();
                    break;
                case 'event_date_asc':
                    $query->orderBy('event_date', 'asc');
                    break;
                case 'event_date_desc':
                    $query->orderBy('event_date', 'desc');
                    break;
                case 'latest':
                default:
                    $query->latest();
                    break;
            }
        } else {
            // Default Default Sorting (Category based)
            if ($type === 'event') {
                // Events: Upcoming first (ASC), then Past (DESC)
                $query->orderByRaw("CASE WHEN event_date >= NOW() THEN 0 ELSE 1 END ASC")
                    ->orderByRaw("CASE WHEN event_date >= NOW() THEN event_date END ASC")
                    ->orderByRaw("CASE WHEN event_date < NOW() THEN event_date END DESC");
            } elseif ($type === 'announcement') {
                // Announcements: Pinned first, then latest
                $query->orderByDesc('is_pinned')->latest();
            } else {
                // News or All: Latest
                $query->latest();
            }
        }

        $newsEvents = $query->paginate(10)->withQueryString();

        // RBAC: Dept Admin cannot see other Dept Admin's posts
        // This is handled by HasDepartmentIsolation trait automatically, 
        // but we ensure System Admin posts with visibility 'all' are also visible.

        if ($request->ajax()) {
            return view('admin.news_events.partials._table', compact('newsEvents'));
        }

        return view('admin.news_events.index', compact('newsEvents', 'search'));
    }

    public function create(Request $request)
    {
        $departments = \App\Models\Course::distinct()->pluck('department_name')->filter()->unique()->values();

        if ($request->ajax()) {
            return view('admin.news_events.partials._form', compact('departments'));
        }
        return view('admin.news_events.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'type' => 'required|in:news,event,announcement,job',
            'visibility_type' => 'required|in:all,department',
            'department_name' => 'nullable|string', // Used when visibility_type is department
            'image' => 'nullable|image|max:2048',
            'gallery_image_path' => 'nullable|string',
            'event_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'job_company' => 'nullable|string|max:255',
            'job_salary' => 'nullable|string|max:255',
            'job_link' => 'nullable|string|max:255',
            'job_deadline' => 'nullable|date',
            'author' => 'nullable|string|max:255',
            'registration_link' => 'nullable|url',
            'expires_at' => 'nullable|date',
            'photos.*' => 'nullable|image|max:5120',
        ]);

        if (Auth::user()->role === 'dept_admin') {
            $validated['visibility_type'] = $request->visibility_type === 'all' ? 'all' : 'department';
            if ($validated['visibility_type'] === 'department') {
                $validated['department_name'] = Auth::user()->department_name;
            }
        }

        $data = $request->except(['image', 'photos', 'gallery_image_path']);
        $data['slug'] = Str::slug($request->title) . '-' . uniqid();
        $data['author'] = $request->author ?? (Auth::check() ? Auth::user()->name : 'Admin');
        $data['is_pinned'] = $request->has('is_pinned');

        // Handle Image Selection (Upload vs Gallery Pick)
        if ($request->filled('gallery_image_path')) {
            $data['image_path'] = $request->gallery_image_path;
        } elseif ($request->hasFile('image')) {
            // Upload to default album logic
            $path = $request->file('image')->store('news_images', 'public');
            $data['image_path'] = $path;

            // Ensure "News & Events" Default Album exists
            $defaultAlbum = GalleryAlbum::firstOrCreate(
                ['is_default' => true],
                [
                    'name' => 'News & Events Uploads',
                    'description' => 'System folder for News and Event featured images.',
                    'category' => 'General',
                ]
            );

            // Add photo to default album
            $defaultAlbum->photos()->create(['image_path' => $path, 'caption' => $request->title]);
        }

        $newsEvent = NewsEvent::create($data);

        // Notify active alumni if it's an event based on targeting
        // Notify active alumni if it's an event
        if ($newsEvent->type === 'event') {
            try {
                $query = \App\Models\User::where('role', 'alumni')->where('status', 'active');

                // Apply visibility filtering to notifications
                if ($newsEvent->visibility_type === 'department') {
                    $query->where('department_name', $newsEvent->department_name);
                }

                $alumni = $query->get();
                if ($alumni->isNotEmpty()) {
                    \Illuminate\Support\Facades\Notification::send($alumni, new \App\Notifications\EventInvitation($newsEvent));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send targeted event invitations: " . $e->getMessage());
            }
        }

        // Handle Event Photos & Auto-Album Creation
        if ($request->hasFile('photos')) {
            // Create corresponding Gallery Album
            $album = null;
            if ($newsEvent->type === 'event') {
                // Use first category or 'Events' default
                $category = $request->filled('category') ? ($data['category'][0] ?? 'Events') : 'Events';

                $album = GalleryAlbum::create([
                    'name' => $newsEvent->title,
                    'description' => 'Auto-generated album for event: ' . $newsEvent->title,
                    'category' => $category,
                    'cover_image' => $newsEvent->image_path // Use event cover as album cover
                ]);
            }

            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('event_galleries/' . $newsEvent->id, 'public');

                // 1. Link to NewsEvent
                $newsEvent->photos()->create(['image_path' => $path]);

                // 2. Link to Gallery Album (if created)
                if ($album) {
                    $album->photos()->create([
                        'image_path' => $path,
                        'caption' => $newsEvent->title . ' Photo'
                    ]);
                }
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => 'Post created successfully.']);
        }

        return redirect()->route('admin.news_events.index')->with('success', 'Post created successfully.');
    }

    public function show(string $id)
    {
        $newsEvent = NewsEvent::with('photos')->findOrFail($id);
        return view('admin.news_events.show', compact('newsEvent'));
    }

    public function edit(Request $request, string $id)
    {
        $newsEvent = NewsEvent::with('photos')->findOrFail($id);
        $departments = \App\Models\Course::distinct()->pluck('department_name')->filter()->unique()->values();

        if ($request->ajax()) {
            return view('admin.news_events.partials._form', compact('newsEvent', 'departments'));
        }
        return view('admin.news_events.edit', compact('newsEvent', 'departments'));
    }

    public function update(Request $request, string $id)
    {
        $newsEvent = NewsEvent::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'type' => 'required|in:news,event,announcement,job',
            'visibility_type' => 'required|in:all,department',
            'department_name' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'gallery_image_path' => 'nullable|string',
            'event_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'job_company' => 'nullable|string|max:255',
            'job_salary' => 'nullable|string|max:255',
            'job_link' => 'nullable|string|max:255',
            'job_deadline' => 'nullable|date',
            'author' => 'nullable|string|max:255',
            'registration_link' => 'nullable|url',
            'expires_at' => 'nullable|date',
            'photos.*' => 'nullable|image|max:5120',
        ]);

        if (Auth::user()->role === 'dept_admin') {
            $validated['visibility_type'] = $request->visibility_type === 'all' ? 'all' : 'department';
            if ($validated['visibility_type'] === 'department') {
                $validated['department_name'] = Auth::user()->department_name;
            }
        }

        $data = $request->except(['image', 'photos', 'gallery_image_path']);
        $data['is_pinned'] = $request->has('is_pinned');

        // Handle Image Selection
        if ($request->filled('gallery_image_path')) {
            // If a new gallery image is picked, use it (and optionally delete old upload if needed, but keeping simple for now)
            $data['image_path'] = $request->gallery_image_path;
        } elseif ($request->hasFile('image')) {
            if ($newsEvent->image_path) {
                Storage::disk('public')->delete($newsEvent->image_path);
            }
            $path = $request->file('image')->store('news_images', 'public');
            $data['image_path'] = $path;

            // Ensure "News & Events" Default Album exists
            $defaultAlbum = GalleryAlbum::firstOrCreate(
                ['is_default' => true],
                [
                    'name' => 'News & Events Uploads',
                    'description' => 'System folder for News and Event featured images.',
                    'category' => 'General',
                ]
            );

            // Add photo to default album
            $defaultAlbum->photos()->create(['image_path' => $path, 'caption' => $request->title]);
        }

        $newsEvent->update($data);

        // Handle Event Photos (Addition)
        if ($request->hasFile('photos')) {
            // Check if album exists or create one (basic check by name for now)
            $album = GalleryAlbum::where('name', $newsEvent->title)->first();

            if (!$album && $newsEvent->type === 'event') {
                $category = $request->filled('category') ? ($data['category'][0] ?? 'Events') : 'Events';
                $album = GalleryAlbum::create([
                    'name' => $newsEvent->title,
                    'description' => 'Auto-generated album for event: ' . $newsEvent->title,
                    'category' => $category,
                    'cover_image' => $newsEvent->image_path
                ]);
            }

            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('event_galleries/' . $newsEvent->id, 'public');
                $newsEvent->photos()->create(['image_path' => $path]);

                if ($album) {
                    $album->photos()->create([
                        'image_path' => $path,
                        'caption' => $newsEvent->title . ' Photo'
                    ]);
                }
            }
        }

        if ($request->ajax()) {
            return response()->json(['success' => 'Post updated successfully.']);
        }

        return redirect()->route('admin.news_events.index')->with('success', 'Post updated successfully.');
    }

    public function destroy(Request $request, string $id)
    {
        $newsEvent = NewsEvent::with('photos')->findOrFail($id);

        if ($newsEvent->image_path) {
            Storage::disk('public')->delete($newsEvent->image_path);
        }

        // Delete gallery photos
        foreach ($newsEvent->photos as $photo) {
            Storage::disk('public')->delete($photo->image_path);
        }

        $newsEvent->delete();

        if ($request->ajax()) {
            return response()->json(['success' => 'Post deleted successfully.']);
        }

        return redirect()->route('admin.news_events.index')->with('success', 'Post deleted successfully.');
    }

    public function getGalleryPhotos(Request $request)
    {
        $albumId = $request->query('album_id');

        if ($albumId) {
            $photos = GalleryPhoto::where('album_id', $albumId)->latest()->paginate(24);
            return response()->json([
                'type' => 'photos',
                'data' => $photos,
                'album' => GalleryAlbum::find($albumId)
            ]);
        } else {
            $albums = GalleryAlbum::withCount('photos')->latest()->paginate(12);
            return response()->json([
                'type' => 'albums',
                'data' => $albums
            ]);
        }
    }

    public function broadcastForm(NewsEvent $news_event)
    {
        if ($news_event->type !== 'event') {
            return response()->json(['error' => 'Only events can be broadcasted.'], 400);
        }
        $courses = \App\Models\Course::orderBy('name')->get();
        return view('admin.news_events.partials._broadcast_form', compact('news_event', 'courses'));
    }

    public function broadcast(Request $request, NewsEvent $news_event)
    {
        $validated = $request->validate([
            'target_type' => 'required|in:all,batch,course,batch_course',
            'target_batch' => 'nullable|string',
            'target_course_id' => 'nullable|exists:courses,id',
        ]);

        $query = \App\Models\User::where('role', 'alumni')->where('status', 'active');

        if ($validated['target_type'] !== 'all') {
            $query->whereHas('alumniProfile', function ($q) use ($validated) {
                if ($validated['target_type'] === 'batch' || $validated['target_type'] === 'batch_course') {
                    $q->where('graduation_year', $validated['target_batch']);
                }
                if ($validated['target_type'] === 'course' || $validated['target_type'] === 'batch_course') {
                    $q->where('course_id', $validated['target_course_id']);
                }
            });
        }

        $alumni = $query->get();

        if ($alumni->isEmpty()) {
            return response()->json(['error' => 'No active alumni found matching the selected criteria.'], 422);
        }

        // Update the event's targeting for record keeping
        $news_event->update($validated);

        \Illuminate\Support\Facades\Notification::send($alumni, new \App\Notifications\EventInvitation($news_event));

        return response()->json(['success' => 'Invitations broadcasted to ' . $alumni->count() . ' alumni successfully!']);
    }

    public function moderate(NewsEvent $news_event)
    {
        $news_event->loadCount(['reactions', 'comments'])->load('photos');
        return view('admin.news_events.partials._moderate', compact('news_event'));
    }

    public function getReactions(NewsEvent $news_event)
    {
        $reactions = \App\Models\NewsEventReaction::where('news_event_id', $news_event->id)
            ->with('user')
            ->latest()
            ->paginate(20);

        return response()->json($reactions);
    }

    public function getComments(NewsEvent $news_event)
    {
        $comments = \App\Models\NewsEventComment::where('news_event_id', $news_event->id)
            ->with(['user', 'newsEvent'])
            ->latest()
            ->paginate(15);

        return response()->json($comments);
    }

    public function replyComment(Request $request, \App\Models\NewsEventComment $comment)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        // For now, replies are just regular comments on the same post
        // but can be prefixed or marked as admin replies
        \App\Models\NewsEventComment::create([
            'news_event_id' => $comment->news_event_id,
            'user_id' => Auth::id(),
            'content' => "@{$comment->user->name} " . $validated['content']
        ]);

        return response()->json(['success' => 'Reply sent successfully.']);
    }

    public function destroyComment(\App\Models\NewsEventComment $comment)
    {
        $comment->delete();
        return response()->json(['success' => 'Comment deleted successfully.']);
    }
}
