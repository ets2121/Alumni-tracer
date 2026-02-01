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
        return view('admin.reports.index');
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

        // New Professional Filters
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
                $data = [
                    'by_course' => Course::withCount('alumni')->orderBy('alumni_count', 'desc')->take(6)->get(),
                    'by_employment' => AlumniProfile::select('employment_status', DB::raw('count(*) as count'))
                        ->groupBy('employment_status')->get(),
                    'by_batch' => AlumniProfile::select('batch_year', DB::raw('count(*) as count'))
                        ->groupBy('batch_year')->orderBy('batch_year')->get(),
                    'registration_trend' => User::where('role', 'alumni')
                        ->select(DB::raw("DATE_FORMAT(created_at, '%b %Y') as month"), DB::raw('count(*) as count'))
                        ->groupBy('month')->orderBy('created_at', 'asc')->take(6)->get(),
                    'by_gender' => AlumniProfile::select('gender', DB::raw('count(*) as count'))
                        ->groupBy('gender')->get(),
                    'by_civil_status' => AlumniProfile::select('civil_status', DB::raw('count(*) as count'))
                        ->groupBy('civil_status')->get(),
                    'by_work_status' => AlumniProfile::whereNotNull('work_status')
                        ->select('work_status', DB::raw('count(*) as count'))
                        ->groupBy('work_status')->get(),
                    'by_establishment' => AlumniProfile::whereNotNull('establishment_type')
                        ->select('establishment_type', DB::raw('count(*) as count'))
                        ->groupBy('establishment_type')->get(),
                    'by_work_location' => AlumniProfile::whereNotNull('work_location')
                        ->select('work_location', DB::raw('count(*) as count'))
                        ->groupBy('work_location')->get(),
                    'top_fields' => AlumniProfile::whereNotNull('field_of_work')
                        ->select('field_of_work', DB::raw('count(*) as count'))
                        ->groupBy('field_of_work')
                        ->orderBy('count', 'desc')
                        ->take(10)->get()
                ];
                $view = 'admin.reports.partials._statistical_summary';
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
