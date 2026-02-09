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
        $fields = AlumniProfile::whereNotNull('field_of_work')->distinct()->pluck('field_of_work');
        $evaluations = \App\Models\EvaluationForm::select('id', 'title')->get();
        return view('admin.reports.index', compact('courses', 'fields', 'evaluations'));
    }

    private function buildReportQuery(Request $request)
    {
        $type = $request->query('type');
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');

        $query = AlumniProfile::with(['user', 'course'])
            ->whereHas('user', function ($q) {
                $q->where('status', 'active');
            });

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
            // Check if value is from Employment Status (Employed/Unemployed) or Work Status (Permanent/etc)
            $val = $request->query('work_status');
            if (in_array($val, ['Employed', 'Unemployed', 'Ongoing Studies'])) {
                $query->where('employment_status', $val);
            } else {
                $query->where('work_status', $val);
            }
        }
        if ($request->query('establishment_type')) {
            $query->where('establishment_type', $request->query('establishment_type'));
        }
        if ($request->query('work_location')) {
            $query->where('work_location', $request->query('work_location'));
        }

        // Search and Subtype Logic
        if ($type === 'master_list' || $type === 'detailed_labor') {
            $subType = $request->query('sub_type', 'all');
            if ($subType === 'unemployed') {
                $query->where('employment_status', 'Unemployed');
            } elseif ($subType === 'never_employed') {
                $query->where('employment_status', 'Unemployed')
                    ->whereDoesntHave('user.employmentHistories');
            }

            if ($request->query('search')) {
                $search = $request->query('search');
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$search}%"]);
                });
            }
        }

        // Year Range Filters
        $fromYear = $request->query('from_year');
        $toYear = $request->query('to_year');
        if ($fromYear && $toYear) {
            $query->whereBetween('batch_year', [$fromYear, $toYear]);
        } elseif ($fromYear) {
            $query->where('batch_year', '>=', $fromYear);
        } elseif ($toYear) {
            $query->where('batch_year', '<=', $toYear);
        }

        return $query;
    }

    public function generate(Request $request)
    {
        $type = $request->query('type');
        $query = $this->buildReportQuery($request);

        $data = [];
        $view = '';

        switch ($type) {
            case 'graduates_by_course':
                $data = $query->orderBy('course_id')->orderBy('batch_year')->orderBy('alumni_profiles.id')->paginate(15)->withQueryString();
                $view = 'admin.reports.partials._graduates_by_course';
                break;
            case 'employment_status':
                $data = (clone $query)->select('employment_status', DB::raw('count(*) as count'))
                    ->groupBy('employment_status')
                    ->get();
                $view = 'admin.reports.partials._employment_status';
                break;
            case 'location':
                $data = (clone $query)->select('address', DB::raw('count(*) as count'))
                    ->groupBy('address')
                    ->get();
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
                            $q->whereHas('user', function ($uq) {
                                $uq->where('status', 'active');
                            });
                            if ($request->query('batch_year'))
                                $q->where('batch_year', $request->query('batch_year'));
                        }
                    ])->get()->map(function ($course) use ($request) {
                        $baseQ = $course->alumni()->whereHas('user', function ($uq) {
                            $uq->where('status', 'active');
                        });
                        if ($request->query('batch_year'))
                            $baseQ->where('batch_year', $request->query('batch_year'));

                        return [
                            'name' => $course->name,
                            'code' => $course->code,
                            'alumni_count' => $course->alumni_count,
                            'employed' => (clone $baseQ)->where('employment_status', 'Employed')->count(),
                            'unemployed' => (clone $baseQ)->where('employment_status', 'Unemployed')->count(),
                        ];
                    })->sortByDesc('alumni_count')->values(),

                    'by_employment' => (clone $query)->select('employment_status', DB::raw('count(*) as count'))
                        ->groupBy('employment_status')->get(),

                    'by_batch' => (clone $query)->select('batch_year', DB::raw('count(*) as count'))
                        ->groupBy('batch_year')->orderBy('batch_year')->get(),

                    'registration_trend' => User::where('role', 'alumni')
                        ->where('status', 'active')
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
                        ->get(),

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
                $data = $query->with('course')->orderBy('alumni_profiles.id')->paginate(15)->withQueryString();
                $view = 'admin.reports.partials._detailed_labor';
                break;
            case 'tracer_study':
                $data = $query->get();
                $view = 'admin.reports.partials._tracer_study';
                break;
            case 'master_list':
                $data = $query->orderBy('alumni_profiles.id')->paginate(15)->withQueryString();
                $view = 'admin.reports.partials._master_list';
                break;
            case 'annual_distribution':
                $subType = $request->query('sub_type', 'by_year');
                $distributionData = [];

                switch ($subType) {
                    case 'by_year':
                        $distributionData = (clone $query)
                            ->select('batch_year', DB::raw('count(*) as count'))
                            ->groupBy('batch_year')
                            ->orderBy('batch_year')
                            ->get();
                        break;
                    case 'by_course':
                        $distributionData = (clone $query)
                            ->join('courses', 'alumni_profiles.course_id', '=', 'courses.id')
                            ->select('courses.code as label', DB::raw('count(*) as count'))
                            ->groupBy('courses.code')
                            ->orderBy('count', 'desc')
                            ->get();
                        break;
                    case 'employment_by_year':
                        $distributionData = (clone $query)
                            ->select('batch_year', 'employment_status', DB::raw('count(*) as count'))
                            ->groupBy('batch_year', 'employment_status')
                            ->orderBy('batch_year')
                            ->get();
                        break;
                    case 'employment_by_course':
                        $distributionData = (clone $query)
                            ->join('courses', 'alumni_profiles.course_id', '=', 'courses.id')
                            ->select('courses.code as label', 'employment_status', DB::raw('count(*) as count'))
                            ->groupBy('courses.code', 'employment_status')
                            ->orderBy('label')
                            ->get();
                        break;
                    case 'location_by_year':
                        $distributionData = (clone $query)
                            ->select('batch_year', 'work_location', DB::raw('count(*) as count'))
                            ->groupBy('batch_year', 'work_location')
                            ->orderBy('batch_year')
                            ->get();
                        break;
                    case 'location_by_course':
                        $distributionData = (clone $query)
                            ->join('courses', 'alumni_profiles.course_id', '=', 'courses.id')
                            ->select('courses.code as label', 'work_location', DB::raw('count(*) as count'))
                            ->groupBy('courses.code', 'work_location')
                            ->orderBy('label')
                            ->get();
                        break;
                }

                $data = [
                    'distribution' => $distributionData,
                    'sub_type' => $subType,
                    'chart_type' => $request->query('chart_type', 'bar'),
                    'total_sample' => $distributionData->sum('count')
                ];
                $view = 'admin.reports.partials._annual_distribution';
                break;
            default:
                return response()->json(['error' => 'Invalid report type'], 400);
        }

        if ($request->ajax()) {
            return view($view, compact('data'))->render();
        }

        return view($view, compact('data'));
    }

    public function export(Request $request)
    {
        $type = $request->query('type');
        $format = $request->query('format', 'csv');
        $query = $this->buildReportQuery($request);

        // Fetch all data for export (no pagination)
        $data = $query->get();

        if ($format === 'csv') {
            return $this->exportCSV($data, $type);
        } elseif ($format === 'excel') {
            return $this->exportExcel($data, $type);
        }

        return response()->json(['error' => 'Invalid export format'], 400);
    }

    private function exportCSV($data, $type)
    {
        $filename = "AA_REPORT_{$type}_" . time() . ".csv";
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename=\"$filename\"");

        // Headers based on type
        $headers = $this->getExportHeaders($type);
        fputcsv($handle, $headers);

        foreach ($data as $alumnus) {
            fputcsv($handle, $this->formatAlumnusForExport($alumnus, $type));
        }

        fclose($handle);
        exit;
    }

    private function exportExcel($data, $type)
    {
        $filename = "AA_REPORT_{$type}_" . time() . ".xls";

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");

        $headers = $this->getExportHeaders($type);

        echo "<html><body><table border='1'>";
        echo "<tr>";
        foreach ($headers as $header) {
            echo "<th style='background-color: #f3f4f6; padding: 10px;'>$header</th>";
        }
        echo "</tr>";

        foreach ($data as $alumnus) {
            echo "<tr>";
            $row = $this->formatAlumnusForExport($alumnus, $type);
            foreach ($row as $cell) {
                echo "<td style='padding: 5px;'>$cell</td>";
            }
            echo "</tr>";
        }

        echo "</table></body></html>";
        exit;
    }

    private function getExportHeaders($type)
    {
        $headers = [
            'Alumni Name',
            'Program',
            'Batch',
            'Email',
            'Gender',
            'Civil Status',
            'Employment Status'
        ];

        // Add specific headers for deeper reports
        if (in_array($type, ['detailed_labor', 'tracer_study', 'master_list'])) {
            $headers = array_merge($headers, [
                'Work Status',
                'Establishment Type',
                'Work Location',
                'Field of Work',
                'Company Name',
                'Position',
                'Work Address'
            ]);
        }

        return $headers;
    }

    private function formatAlumnusForExport($alumnus, $type)
    {
        $row = [
            $alumnus->full_name,
            $alumnus->course->code ?? 'N/A',
            $alumnus->batch_year,
            $alumnus->user->email ?? 'N/A',
            $alumnus->gender,
            $alumnus->civil_status,
            $alumnus->employment_status
        ];

        // Fill specific data for deeper reports
        if (in_array($type, ['detailed_labor', 'tracer_study', 'master_list'])) {
            $row = array_merge($row, [
                $alumnus->work_status ?? 'N/A',
                $alumnus->establishment_type ?? 'N/A',
                $alumnus->work_location ?? 'N/A',
                $alumnus->field_of_work ?? 'N/A',
                $alumnus->company_name ?? 'N/A',
                $alumnus->position ?? 'N/A',
                $alumnus->work_address ?? 'N/A'
            ]);
        }

        return $row;
    }
}
