<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class PreRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $sortBy = $request->query('sort', 'name');
        $sortDir = $request->query('direction', 'asc');
        $tab = $request->query('tab', 'pending');

        $query = User::where('role', 'alumni')
            ->whereIn('status', ['pending', 'rejected', 'active'])
            ->with(['alumniProfile.course'])
            ->when($search, function ($q) use ($search) {
                return $q->where(function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });

        $data = [
            'pendingAlumni' => (clone $query)->where('status', 'pending')->orderBy('created_at', 'desc')->paginate(10, ['*'], 'pending_page')->withQueryString(),
            'approvedAlumni' => (clone $query)->where('status', 'active')->orderBy('updated_at', 'desc')->paginate(10, ['*'], 'approved_page')->withQueryString(),
            'rejectedAlumni' => (clone $query)->where('status', 'rejected')->orderBy('updated_at', 'desc')->paginate(10, ['*'], 'rejected_page')->withQueryString(),
            'search' => $search,
            'sortBy' => $sortBy,
            'sortDir' => $sortDir,
            'activeTab' => $tab,
        ];

        if ($request->ajax()) {
            return view('admin.pre_registration.partials._table_content', $data);
        }

        return view('admin.pre_registration.index', $data);
    }
}
