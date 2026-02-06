<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvaluationForm;
use App\Models\EvaluationResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // Base Query
        $statsQuery = EvaluationResponse::query()
            ->whereHas('form', function ($q) {
                $q->where('title', 'CHED Graduate Tracer Survey (GTS)');
            });

        // 1. Stats Calculation (Global or Filtered? usually Global for dashboard-like feel, but filtered is nice too)
        // Let's do Global stats for the cards, and let the table be filtered.
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
        $query->with(['user', 'form']); // Eager load

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

        $responses = $query->latest()->paginate(10);

        // Get unique departments for filter dropdown
        $departments = EvaluationResponse::select('department_name')
            ->distinct()
            ->whereNotNull('department_name')
            ->pluck('department_name');

        // Get unique sections for export dropdown
        $sections = \App\Models\EvaluationQuestion::select('section')
            ->distinct()
            ->whereNotNull('section')
            ->pluck('section');

        return view('admin.tracer.index', compact('responses', 'departments', 'totalResponses', 'deptStats', 'sections'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $response = EvaluationResponse::with([
            'user',
            'form.questions' => function ($q) {
                $q->orderBy('order');
            },
            'answers'
        ])->findOrFail($id);

        return view('admin.tracer.show', compact('response'));
    }

    /**
     * Export responses to CSV.
     */
    public function destroy($id)
    {
        EvaluationResponse::destroy($id);
        return redirect()->back()->with('success', 'Response deleted successfully. The aumni can now retake the survey.');
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

        $fileName = 'gts_responses_' . ($singleId ? 'individual_' . $singleId . '_' : '') . ($section !== 'all' ? 'section_' : '') . date('Y-m-d_H-i');
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

        // 1. Setup Questions
        $form = EvaluationForm::where('title', 'CHED Graduate Tracer Survey (GTS)')->first();
        if (!$form)
            return;

        $questionsQuery = $form->questions()->orderBy('order');

        if ($section !== 'all') {
            $questionsQuery->where('section', $section);
        }

        $questions = $questionsQuery->get();

        // Headers
        $headers = ['Response ID', 'User ID', 'Name', 'Email', 'Department', 'Date Submitted'];
        $questionMap = [];

        foreach ($questions as $q) {
            $qNum = $q->options['question_number'] ?? '';
            // Clean header text
            $qText = $qNum ? "$qNum. $q->question_text" : $q->question_text;
            $headers[] = $qText;
            $questionMap[$q->id] = $q; // Store full object for type check
        }

        // Output Headers
        if ($format === 'csv') {
            fputcsv($handle, $headers);
        } else {
            echo "<tr>";
            foreach ($headers as $h)
                echo "<th style='background-color: #f3f4f6; padding: 10px; text-align: left;'>" . htmlspecialchars($h) . "</th>";
            echo "</tr>";
        }

        // 2. Data Rows
        $query = EvaluationResponse::query()
            ->where('form_id', $form->id)
            ->with(['user', 'answers']);

        if ($singleId) {
            $query->where('id', $singleId);
        }

        $query->chunk(100, function ($responses) use ($handle, $questionMap, $format) {
            foreach ($responses as $response) {
                $row = [
                    $response->id,
                    $response->user_id,
                    $response->user->name,
                    $response->user->email,
                    $response->department_name,
                    $response->created_at->format('Y-m-d H:i:s'),
                ];

                $answers = $response->answers->pluck('answer_text', 'question_id');

                foreach ($questionMap as $qId => $question) {
                    $ans = $answers[$qId] ?? null;
                    $formattedAns = $this->formatAnswerForExport($ans, $question);
                    $row[] = $formattedAns;
                }

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

    private function formatAnswerForExport($rawAnswer, $question)
    {
        if ($rawAnswer === null || $rawAnswer === '') {
            return 'N/A';
        }

        // Try Decode
        $decoded = json_decode($rawAnswer, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            return $rawAnswer; // Plain text
        }

        // 1. Simple Array (Checkboxes)
        if (array_is_list($decoded) && is_scalar($decoded[0] ?? '')) {
            return implode(", ", $decoded);
        }

        // 2. Dynamic Table (Array of objects)
        if ($question->type === 'dynamic_table') {
            $lines = [];
            foreach ($decoded as $rowIndex => $row) {
                $rowStr = [];
                foreach ($row as $col => $val) {
                    $rowStr[] = "$col: $val";
                }
                if (!empty($rowStr)) {
                    $lines[] = "[" . ($rowIndex + 1) . "] " . implode(" | ", $rowStr);
                }
            }
            return implode("\n", $lines); // Excel handles newlines well
        }

        // 3. Date Group
        if ($question->type === 'date_group') {
            $m = $decoded['month'] ?? '';
            $d = $decoded['day'] ?? '';
            $y = $decoded['year'] ?? '';
            return "$m $d, $y";
        }

        return json_encode($decoded); // Fallback
    }
    public function exportPdf($id)
    {
        $response = EvaluationResponse::with([
            'user',
            'form.questions' => function ($q) {
                // Ensure questions are loaded
            },
            'answers'
        ])->findOrFail($id);

        return view('admin.tracer.pdf', compact('response'));
    }
}
