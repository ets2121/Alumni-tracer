<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChedGtsResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TracerSurveyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $department = $request->input('department');
        $year = $request->input('year');

        // Base Query using new standalone GTS model
        $statsQuery = ChedGtsResponse::query();

        // 1. Stats Calculation
        $totalResponses = (clone $statsQuery)->count();

        $deptStats = (clone $statsQuery)
            ->select('department_name', DB::raw('count(*) as total'))
            ->whereNotNull('department_name')
            ->groupBy('department_name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // 2. Filtered List for Table
        $query = clone $statsQuery;
        $query->with(['user']); // Eager load user

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($department) {
            $query->where('department_name', $department);
        }

        if ($year) {
            $query->whereYear('created_at', $year);
        }

        $responses = $query->latest()->paginate(15);

        // Get unique departments for filter dropdown
        $departments = ChedGtsResponse::select('department_name')
            ->distinct()
            ->whereNotNull('department_name')
            ->pluck('department_name');

        // Sections for export (hardcoded or from JSON if needed, but 'all' is common)
        $sections = [
            'General Information',
            'Educational Background',
            'Advanced Studies',
            'Employment Data',
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'responses' => $responses,
                'departments' => $departments,
                'totalResponses' => $totalResponses,
                'deptStats' => $deptStats,
                'sections' => $sections
            ]);
        }

        return view('admin.tracer.index', compact('responses', 'departments', 'totalResponses', 'deptStats', 'sections'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $response = ChedGtsResponse::with(['user'])->findOrFail($id);

        return view('admin.tracer.show', [
            'response' => $response,
            'data' => $response->response_data
        ]);
    }

    /**
     * Delete the specified response.
     */
    public function destroy($id)
    {
        ChedGtsResponse::destroy($id);
        return redirect()->back()->with('success', 'Response deleted successfully. The alumni can now retake the survey.');
    }

    public function exportIndividual($id)
    {
        return $this->exportProcess(request(), $id);
    }

    public function export(Request $request)
    {
        return $this->exportProcess($request);
    }

    private function exportProcess(Request $request, $singleId = null)
    {
        $format = $request->input('format', 'csv');
        $section = $request->input('section', 'all');

        $fileName = 'ched_gts_responses_' . ($singleId ? 'individual_' . $singleId . '_' : '') . date('Y-m-d_H-i');
        $fileName .= $format === 'excel' ? '.xls' : '.csv';

        // Excel Wrapper
        if ($format === 'excel') {
            return response()->streamDownload(function () use ($section, $singleId, $format) {
                echo "<html><head><meta charset='UTF-8'></head><body><table border='1'>";
                $this->exportData($format, $section, $singleId);
                echo "</table></body></html>";
            }, $fileName, [
                "Content-Type" => "application/vnd.ms-excel",
                "Content-Disposition" => "attachment; filename=\"$fileName\""
            ]);
        }

        // CSV
        return response()->streamDownload(function () use ($section, $singleId, $format) {
            $this->exportData($format, $section, $singleId);
        }, $fileName);
    }

    private function exportData($format, $section, $singleId = null)
    {
        $handle = $format === 'csv' ? fopen('php://output', 'w') : null;

        // UTF-8 BOM for CSV Excel compatibility
        if ($format === 'csv')
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Define Headers based on the CHED GTS structure
        $headers = [
            'Response ID',
            'User ID',
            'Name',
            'Email',
            'Department',
            'Date Submitted',
            'Q1_Name',
            'Q2_Address',
            'Q3_Email',
            'Q4_Tel',
            'Q5_Mobile',
            'Q6_Civil_Status',
            'Q7_Sex',
            'Q8_Birthday',
            'Q9_Region',
            'Q10_Province',
            'Q11_Location',
            'Q12_Degrees',
            'Q13_Prof_Exams',
            'Q14_Reasons_for_Course',
            'Q15_Trainings',
            'Q16_Employed',
            'Q17_Employment_Status',
            'Q18_Present_Job_Relation',
            'Q19_Occupation',
            'Q20_Business_Line',
            'Q21_Place_of_Work',
            'Q22_Is_First_Job',
            'Q23_Reason_for_Staying',
            'Q24_Time_to_Find_First_Job',
            'Q25_How_Found_First_Job',
            'Q26_How_Long_Found_First_Job',
            'Q27_Is_Course_Related_to_First_Job',
            'Q28_Reason_for_Accepting_First_Job',
            'Q29_Reason_for_Changing_Job',
            'Q30_How_Long_in_First_Job',
            'Q31_Curriculum_Relevance',
            'Q32_Useful_Skills',
            'Q33_Competencies',
            'Q34_Suggestions'
        ];

        // Output Headers
        if ($format === 'csv') {
            fputcsv($handle, $headers);
        } else {
            echo "<tr>";
            foreach ($headers as $h)
                echo "<th style='background-color: #f3f4f6; padding: 10px; text-align: left;'>" . htmlspecialchars($h) . "</th>";
            echo "</tr>";
        }

        // Fetch Data
        $query = ChedGtsResponse::query()->with(['user']);
        if ($singleId) {
            $query->where('id', $singleId);
        }

        $query->chunk(100, function ($responses) use ($handle, $format) {
            foreach ($responses as $response) {
                $data = $response->response_data;

                $row = [
                    $response->id,
                    $response->user_id,
                    $response->user->name ?? 'N/A',
                    $response->user->email ?? 'N/A',
                    $response->department_name,
                    $response->created_at->format('Y-m-d H:i:s'),
                ];

                // Map JSON data to flat row
                $row[] = $data['q1_name'] ?? 'N/A';
                $row[] = $data['q2_address'] ?? 'N/A';
                $row[] = $data['q3_email'] ?? 'N/A';
                $row[] = $data['q4_tel'] ?? 'N/A';
                $row[] = $data['q5_mobile'] ?? 'N/A';
                $row[] = $data['q6_civil_status'] ?? 'N/A';
                $row[] = $data['q7_sex'] ?? 'N/A';
                $row[] = ($data['q8_month'] ?? '') . '/' . ($data['q8_day'] ?? '') . '/' . ($data['q8_year'] ?? '');
                $row[] = $data['q9_region'] ?? 'N/A';
                $row[] = $data['q10_province'] ?? 'N/A';
                $row[] = $data['q11_location'] ?? 'N/A';

                // Q12 Degrees (Array)
                $q12 = $data['q12'] ?? [];
                $q12Str = [];
                foreach ($q12 as $d)
                    $q12Str[] = ($d['degree'] ?? '') . ' (' . ($d['college'] ?? '') . ', ' . ($d['year'] ?? '') . ')';
                $row[] = implode(" | ", $q12Str);

                // Q13 Prof Exams (Array)
                $q13 = $data['q13'] ?? [];
                $q13Str = [];
                foreach ($q13 as $e)
                    $q13Str[] = ($e['name'] ?? '') . ' (' . ($e['date'] ?? '') . ', ' . ($e['rating'] ?? '') . ')';
                $row[] = implode(" | ", $q13Str);

                $row[] = $data['q14_others'] ?? 'N/A';

                // Q15 Trainings (Array)
                $q15a = $data['q15a'] ?? [];
                $q15Str = [];
                foreach ($q15a as $t)
                    $q15Str[] = ($t['title'] ?? '') . ' (' . ($t['institution'] ?? '') . ')';
                $row[] = implode(" | ", $q15Str);

                $row[] = $data['q16_employed'] ?? 'N/A';
                $row[] = $data['q17_employment_status'] ?? 'N/A';
                $row[] = $data['q18_job_relation'] ?? 'N/A';
                $row[] = $data['q19_occupation'] ?? 'N/A';
                $row[] = $data['q20_business_line'] ?? 'N/A';
                $row[] = $data['q21_work_place'] ?? 'N/A';
                $row[] = $data['q22_first_job'] ?? 'N/A';
                $row[] = $data['q23_staying_reason'] ?? 'N/A';
                $row[] = $data['q24_time_to_find'] ?? 'N/A';
                $row[] = $data['q25_how_found'] ?? 'N/A';
                $row[] = $data['q26_how_long_found'] ?? 'N/A';
                $row[] = $data['q27_course_related'] ?? 'N/A';
                $row[] = $data['q28_accept_reason'] ?? 'N/A';
                $row[] = $data['q29_change_reason'] ?? 'N/A';
                $row[] = $data['q30_long_in_first'] ?? 'N/A';
                $row[] = $data['q31_curriculum_relevance'] ?? 'N/A';
                $row[] = $data['q32_useful_skills'] ?? 'N/A';
                $row[] = $data['q33_others'] ?? 'N/A';
                $row[] = $data['q34_suggestions'] ?? 'N/A';

                if ($format === 'csv') {
                    fputcsv($handle, $row);
                } else {
                    echo "<tr>";
                    foreach ($row as $cell)
                        echo "<td style='padding: 5px; vertical-align: top;'>" . nl2br(htmlspecialchars($cell)) . "</td>";
                    echo "</tr>";
                }
            }
        });

        if ($format === 'csv')
            fclose($handle);
    }

    public function exportPdf($id)
    {
        $response = ChedGtsResponse::with(['user'])->findOrFail($id);

        return view('admin.tracer.pdf', [
            'response' => $response,
            'data' => $response->response_data
        ]);
    }
}
