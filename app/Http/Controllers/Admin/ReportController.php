<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AlumniProfile;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $courses = Course::all();
        return view('admin.reports.index', compact('courses'));
    }

    public function generate(Request $request)
    {
        $type = $request->query('type');
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');

        $query = AlumniProfile::with(['user', 'course']);

        if ($fromDate) {
            $query->whereHas('user', function ($q) use ($fromDate) {
                $q->whereDate('created_at', '>=', $fromDate);
            });
        }
        if ($toDate) {
            $query->whereHas('user', function ($q) use ($toDate) {
                $q->whereDate('created_at', '<=', $toDate);
            });
        }

        // Primary Filters
        if ($request->query('course_id')) {
            $query->where('course_id', $request->query('course_id'));
        }
        if ($request->query('batch_year')) {
            $query->where('batch_year', $request->query('batch_year'));
        }

        // Professional Filters
        if ($request->query('field_of_work')) {
            $query->where('field_of_work', $request->query('field_of_work'));
        }
        if ($request->query('work_status')) {
            $query->where('work_status', $request->query('work_status'));
        }
        if ($request->query('establishment_type')) {
            $query->where('establishment_type', $request->query('establishment_type'));
        }
        if ($request->query('work_location')) {
            $query->where('work_location', $request->query('work_location'));
        }

        $data = [];
        $view = '';

        switch ($type) {
            case 'graduates_by_course':
                $data = $query->orderBy('course_id')->orderBy('batch_year')->get();
                $view = 'admin.reports.partials._graduates_by_course';
                break;
            case 'employment_status':
                $data = $query->get()->groupBy('employment_status');
                $view = 'admin.reports.partials._employment_status';
                break;
            case 'location':
                $data = $query->get()->groupBy('address');
                $view = 'admin.reports.partials._location';
                break;
            case 'statistical_summary':
                // Base data for simple charts
                $totalAlumni = (clone $query)->count();
                $employedCount = (clone $query)->where('employment_status', 'Employed')->count();
                $overseasCount = (clone $query)->where('work_location', 'Overseas')->count();
                $dominantField = (clone $query)->whereNotNull('field_of_work')
                    ->select('field_of_work', DB::raw('count(*) as count'))
                    ->groupBy('field_of_work')
                    ->orderBy('count', 'desc')
                    ->first();

                $incompleteProfiles = (clone $query)->where(function ($q) {
                    $q->whereNull('field_of_work')
                        ->orWhereNull('work_status')
                        ->orWhereNull('establishment_type')
                        ->orWhereNull('work_location');
                })->count();

                $data = [
                    'all_courses' => Course::all(),
                    'summary' => [
                        'total_count' => $totalAlumni,
                        'employed_count' => $employedCount,
                        'employment_rate' => $totalAlumni > 0 ? round(($employedCount / $totalAlumni) * 100, 1) : 0,
                        'overseas_rate' => $employedCount > 0 ? round(($overseasCount / $employedCount) * 100, 1) : 0,
                        'dominant_field' => $dominantField ? $dominantField->field_of_work : 'None',
                        'integrity_score' => $totalAlumni > 0 ? round((($totalAlumni - $incompleteProfiles) / $totalAlumni) * 100, 1) : 100
                    ],
                    'by_course' => Course::withCount([
                        'alumni' => function ($q) use ($request) {
                            if ($request->query('batch_year'))
                                $q->where('batch_year', $request->query('batch_year'));
                        }
                    ])->get()->map(function ($course) use ($request) {
                        $baseQ = $course->alumni();
                        if ($request->query('batch_year'))
                            $baseQ->where('batch_year', $request->query('batch_year'));

                        return [
                            'code' => $course->code,
                            'alumni_count' => $course->alumni_count,
                            'employed' => (clone $baseQ)->where('employment_status', 'Employed')->count(),
                            'unemployed' => (clone $baseQ)->where('employment_status', 'Unemployed')->count(),
                        ];
                    })->sortByDesc('alumni_count')->take(10)->values(),

                    'by_employment' => (clone $query)->select('employment_status', DB::raw('count(*) as count'))
                        ->groupBy('employment_status')->get(),

                    'by_batch' => (clone $query)->select('batch_year', DB::raw('count(*) as count'))
                        ->groupBy('batch_year')->orderBy('batch_year')->get(),

                    'registration_trend' => User::where('role', 'alumni')
                        ->select(DB::raw("DATE_FORMAT(created_at, '%b %Y') as month"), DB::raw('count(*) as count'))
                        ->groupBy('month')->orderBy('created_at', 'asc')->take(6)->get(),

                    'by_gender' => (clone $query)->select('gender', DB::raw('count(*) as count'))
                        ->groupBy('gender')->get(),

                    'by_civil_status' => (clone $query)->select('civil_status', DB::raw('count(*) as count'))
                        ->groupBy('civil_status')->get(),

                    'by_work_status' => (clone $query)->whereNotNull('work_status')
                        ->select('work_status', DB::raw('count(*) as count'))
                        ->groupBy('work_status')->get(),

                    'by_establishment' => (clone $query)->whereNotNull('establishment_type')
                        ->select('establishment_type', DB::raw('count(*) as count'))
                        ->groupBy('establishment_type')->get(),

                    'by_work_location' => (clone $query)->whereNotNull('work_location')
                        ->select('work_location', DB::raw('count(*) as count'))
                        ->groupBy('work_location')->get(),

                    'top_fields' => (clone $query)->whereNotNull('field_of_work')
                        ->select('field_of_work', DB::raw('count(*) as count'))
                        ->groupBy('field_of_work')
                        ->orderBy('count', 'desc')
                        ->take(10)->get(),

                    // Complex Data for Stacked/Grouped Charts
                    'stability_matrix' => Course::with([
                        'alumni' => function ($q) {
                            $q->select('course_id', 'work_status', DB::raw('count(*) as count'))
                                ->whereNotNull('work_status')
                                ->groupBy('course_id', 'work_status');
                        }
                    ])->get()->map(function ($course) {
                        return [
                            'program' => $course->code,
                            'Permanent' => $course->alumni->where('work_status', 'Permanent')->first()->count ?? 0,
                            'Contractual' => $course->alumni->where('work_status', 'Contractual')->first()->count ?? 0,
                            'Job Order' => $course->alumni->where('work_status', 'Job Order')->first()->count ?? 0,
                        ];
                    }),

                    'establishment_matrix' => Course::with([
                        'alumni' => function ($q) {
                            $q->select('course_id', 'establishment_type', DB::raw('count(*) as count'))
                                ->whereNotNull('establishment_type')
                                ->groupBy('course_id', 'establishment_type');
                        }
                    ])->get()->map(function ($course) {
                        return [
                            'program' => $course->code,
                            'Public' => $course->alumni->where('establishment_type', 'Public')->first()->count ?? 0,
                            'Private' => $course->alumni->where('establishment_type', 'Private')->first()->count ?? 0,
                        ];
                    }),

                    'location_matrix' => Course::with([
                        'alumni' => function ($q) {
                            $q->select('course_id', 'work_location', DB::raw('count(*) as count'))
                                ->whereNotNull('work_location')
                                ->groupBy('course_id', 'work_location');
                        }
                    ])->get()->map(function ($course) {
                        return [
                            'program' => $course->code,
                            'Local' => $course->alumni->where('work_location', 'Local')->first()->count ?? 0,
                            'Overseas' => $course->alumni->where('work_location', 'Overseas')->first()->count ?? 0,
                        ];
                    }),

                    'combination_data' => User::where('role', 'alumni')
                        ->select(
                            DB::raw("DATE_FORMAT(created_at, '%b %Y') as month"),
                            DB::raw('count(*) as total_registrants'),
                            DB::raw("SUM(CASE WHEN EXISTS (SELECT 1 FROM alumni_profiles WHERE alumni_profiles.user_id = users.id AND employment_status = 'Employed') THEN 1 ELSE 0 END) as employed_count")
                        )
                        ->groupBy('month')
                        ->orderBy('created_at', 'asc')
                        ->take(6)->get()
                ];
                $view = 'admin.reports.partials._statistical_summary';
                break;
            case 'detailed_labor':
                $data = $query->with('course')->get();
                $view = 'admin.reports.partials._detailed_labor';
                break;
            case 'tracer_study':
                $data = $query->get();
                $view = 'admin.reports.partials._tracer_study';
                break;
            default:
                return response()->json(['error' => 'Invalid report type'], 400);
        }

        if ($request->ajax()) {
            return view($view, compact('data'))->render();
        }

        return view($view, compact('data'));
    }
}
