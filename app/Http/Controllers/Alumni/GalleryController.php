<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\GalleryAlbum;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $albums = GalleryAlbum::withCount('photos')
            ->when($search, function ($q) use ($search) {
                return $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        if ($request->ajax()) {
            return view('alumni.gallery.partials._list', compact('albums'));
        }

        return view('alumni.gallery.index', compact('albums', 'search'));
    }

    public function show(GalleryAlbum $album)
    {
        $album->load('photos');
        return view('alumni.gallery.show', compact('album'));
    }
}
