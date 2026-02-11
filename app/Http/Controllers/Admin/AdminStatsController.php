<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AlumniProfile;
use App\Models\NewsEvent;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminStatsController extends Controller
{
    public function getCounts()
    {
        $cacheKey = 'admin_dashboard_counts_' . auth()->id();

        return Cache::remember($cacheKey, 300, function () {
            $user = auth()->user();
            $query = User::query();
            $newsQuery = NewsEvent::query();

            // Handle Department Isolation via Trait implicitly or explicitly if needed
            // The Trait usually applies a global scope based on the user's department

            return [
                'alumni_total' => (clone $query)->where('role', 'alumni')->count(),
                'alumni_verified' => (clone $query)->where('role', 'alumni')->where('status', 'active')->count(),
                'alumni_pending' => (clone $query)->where('role', 'alumni')->where('status', 'pending')->count(),
                'dept_admins' => User::where('role', 'dept_admin')->count(),
                'total_departments' => Course::distinct()->count('department_name'),
                'active_events' => (clone $newsQuery)->where('type', 'event')
                    ->where('event_date', '>=', now())
                    ->where(function ($q) {
                        $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                    })->count(),
                'upcoming_events' => (clone $newsQuery)->where('type', 'event')
                    ->where('event_date', '>', now())
                    ->count(),
                'past_events' => (clone $newsQuery)->where('type', 'event')
                    ->where('event_date', '<', now())
                    ->count(),
            ];
        });
    }

    public function getCharts()
    {
        $cacheKey = 'admin_dashboard_charts_' . auth()->id();

        return Cache::remember($cacheKey, 300, function () {
            return [
                'registration_trends' => $this->getRegistrationTrends(),
                'employment_status' => $this->getEmploymentStatusDistribution(),
                'alumni_by_dept' => $this->getAlumniByDepartment(),
                'alumni_by_course' => $this->getAlumniByCourse(),
                'gender_distribution' => $this->getGenderDistribution(),
                'civil_status' => $this->getCivilStatusDistribution(),
                'employment_type' => $this->getEmploymentTypeDistribution(),
            ];
        });
    }

    public function getRecentUsers()
    {
        $cacheKey = 'admin_dashboard_recent_users_' . auth()->id();

        return Cache::remember($cacheKey, 300, function () {
            return [
                'verified' => User::where('role', 'alumni')
                    ->where('status', 'active')
                    ->with('alumniProfile')
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(fn($u) => [
                        'name' => $u->alumniProfile?->full_name ?? $u->name,
                        'email' => $u->email,
                        'department' => $u->department_name,
                        'avatar' => $u->avatar,
                        'created_at' => $u->created_at->diffForHumans(),
                    ]),
                'pending' => User::where('role', 'alumni')
                    ->where('status', 'pending')
                    ->with('alumniProfile')
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(fn($u) => [
                        'name' => $u->alumniProfile?->full_name ?? $u->name,
                        'email' => $u->email,
                        'department' => $u->department_name,
                        'avatar' => $u->avatar,
                        'created_at' => $u->created_at->diffForHumans(),
                    ]),
            ];
        });
    }

    private function getRegistrationTrends()
    {
        // Monthly registration for the last 12 months
        $trends = User::where('role', 'alumni')
            ->select(DB::raw('COUNT(id) as count'), DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"))
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'labels' => $trends->pluck('month'),
            'data' => $trends->pluck('count'),
        ];
    }

    private function getEmploymentStatusDistribution()
    {
        $dist = AlumniProfile::select('employment_status', DB::raw('count(*) as count'))
            ->groupBy('employment_status')
            ->get();

        return [
            'labels' => $dist->pluck('employment_status')->map(fn($s) => $s ?: 'Unknown'),
            'data' => $dist->pluck('count'),
        ];
    }

    private function getAlumniByDepartment()
    {
        $dist = AlumniProfile::select('department_name', DB::raw('count(*) as count'))
            ->whereNotNull('department_name')
            ->groupBy('department_name')
            ->get();

        return [
            'labels' => $dist->pluck('department_name'),
            'data' => $dist->pluck('count'),
        ];
    }

    private function getAlumniByCourse()
    {
        $dist = AlumniProfile::join('courses', 'alumni_profiles.course_id', '=', 'courses.id')
            ->select('courses.code', DB::raw('count(*) as count'))
            ->groupBy('courses.code')
            ->limit(10)
            ->get();

        return [
            'labels' => $dist->pluck('code'),
            'data' => $dist->pluck('count'),
        ];
    }

    private function getGenderDistribution()
    {
        $dist = AlumniProfile::select('gender', DB::raw('count(*) as count'))
            ->whereNotNull('gender')
            ->groupBy('gender')
            ->get();

        return [
            'labels' => $dist->pluck('gender'),
            'data' => $dist->pluck('count'),
        ];
    }

    private function getCivilStatusDistribution()
    {
        $dist = AlumniProfile::select('civil_status', DB::raw('count(*) as count'))
            ->whereNotNull('civil_status')
            ->groupBy('civil_status')
            ->get();

        return [
            'labels' => $dist->pluck('civil_status'),
            'data' => $dist->pluck('count'),
        ];
    }

    private function getEmploymentTypeDistribution()
    {
        // establishment_type is used for Contractual, Permanent, etc.
        $dist = AlumniProfile::select('establishment_type', DB::raw('count(*) as count'))
            ->whereNotNull('establishment_type')
            ->groupBy('establishment_type')
            ->get();

        return [
            'labels' => $dist->pluck('establishment_type'),
            'data' => $dist->pluck('count'),
        ];
    }
}
