<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryAlbum;
use App\Models\GalleryPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $category = $request->query('category');
        $albums = GalleryAlbum::withCount('photos')
            ->when($search, function ($q) use ($search) {
                return $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($category && $category !== 'all', function ($q) use ($category) {
                return $q->where('category', $category);
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        if ($request->ajax()) {
            return view('admin.gallery.partials._grid', compact('albums'));
        }

        return view('admin.gallery.index', compact('albums', 'search'));
    }

    public function create(Request $request)
    {
        if ($request->ajax()) {
            return view('admin.gallery.partials._form');
        }
        return view('admin.gallery.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('gallery/covers', 'public');
        }

        \App\Models\GalleryAlbum::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => 'Album created successfully.']);
        }

        return redirect()->route('admin.gallery.index')->with('success', 'Album created successfully.');
    }

    public function show(\App\Models\GalleryAlbum $gallery)
    {
        $gallery->load('photos');
        return view('admin.gallery.show', compact('gallery'));
    }

    public function edit(Request $request, \App\Models\GalleryAlbum $gallery)
    {
        if ($request->ajax()) {
            return view('admin.gallery.partials._form', compact('gallery'));
        }
        return view('admin.gallery.edit', compact('gallery'));
    }

    public function update(Request $request, \App\Models\GalleryAlbum $gallery)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($gallery->cover_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($gallery->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('gallery/covers', 'public');
        }

        $gallery->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => 'Album updated successfully.']);
        }

        return redirect()->route('admin.gallery.index')->with('success', 'Album updated successfully.');
    }

    public function destroy(Request $request, \App\Models\GalleryAlbum $gallery)
    {
        if ($gallery->is_default) {
            return response()->json(['error' => 'System default albums cannot be deleted.'], 403);
        }
        if ($gallery->cover_image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($gallery->cover_image);
        }

        foreach ($gallery->photos as $photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($photo->image_path);
        }

        $gallery->delete();

        if ($request->ajax()) {
            return response()->json(['success' => 'Album deleted successfully.']);
        }

        return redirect()->route('admin.gallery.index')->with('success', 'Album deleted successfully.');
    }

    public function uploadPhotos(Request $request, GalleryAlbum $gallery)
    {
        $request->validate([
            'photos.*' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $path = $file->store('gallery/albums/' . $gallery->id, 'public');
                GalleryPhoto::create([
                    'album_id' => $gallery->id,
                    'image_path' => $path,
                ]);
            }
        }

        return back()->with('success', 'Photos uploaded successfully.');
    }

    public function deletePhoto(Request $request, \App\Models\GalleryPhoto $photo)
    {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($photo->image_path);
        $photo->delete();

        if ($request->ajax()) {
            return response()->json(['success' => 'Photo removed successfully.']);
        }

        return back()->with('success', 'Photo removed successfully.');
    }

    public function updatePhotoCaption(Request $request, \App\Models\GalleryPhoto $photo)
    {
        $validated = $request->validate([
            'caption' => 'nullable|string|max:255',
        ]);

        $photo->update($validated);

        return response()->json(['success' => 'Caption updated successfully.']);
    }
}
