<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $stats = \Illuminate\Support\Facades\Cache::remember('admin_dashboard_stats', 300, function () {
            return [
                'total_alumni' => \App\Models\User::where('role', 'alumni')->where('status', 'active')->count(),
                'pending_alumni' => \App\Models\User::where('role', 'alumni')->where('status', 'pending')->count(),
                'total_events' => \App\Models\NewsEvent::count(),
                'total_gallery' => \App\Models\GalleryAlbum::count(),
            ];
        });

        return view('admin.dashboard', compact('stats'));
    }
}
