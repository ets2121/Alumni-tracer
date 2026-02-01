<?php

namespace App\Http\Controllers;

use App\Models\NewsEvent;
use Illuminate\Http\Request;

class AlumniDashboardController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $newsEvents = NewsEvent::latest()->take(6)->get();

        if ($request->ajax()) {
            return view('partials._dashboard_news', compact('newsEvents'));
        }

        return view('dashboard', compact('newsEvents'));
    }
}
