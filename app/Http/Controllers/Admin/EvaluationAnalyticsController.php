<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EvaluationForm;

class EvaluationAnalyticsController extends Controller
{
    public function show(Request $request, EvaluationForm $evaluation)
    {
        if ($evaluation->type === 'tracer') {
            abort(404, 'Tracer Survey analytics are handled separately.');
        }

        $evaluation->load(['questions', 'responses.answers']); // Eager load for base stats

        // Filters
        $courseId = $request->input('course');
        $batchYear = $request->input('batch');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Allow filtering responses
        $responsesQuery = $evaluation->responses();

        if ($courseId) {
            $responsesQuery->whereHas('user.alumniProfile', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        if ($batchYear) {
            $responsesQuery->whereHas('user.alumniProfile', function ($q) use ($batchYear) {
                $q->where('batch_year', $batchYear);
            });
        }

        if ($startDate) {
            $responsesQuery->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $responsesQuery->whereDate('created_at', '<=', $endDate);
        }

        $filteredResponses = $responsesQuery->with('answers')->get();
        $totalResponses = $filteredResponses->count();
        $overallTotal = $evaluation->responses()->count(); // Unfiltered total

        // Summary Metrics
        $latestResponse = $filteredResponses->sortByDesc('created_at')->first();

        // Top Participating Course (in filtered set)
        $topCourseObj = \App\Models\AlumniProfile::whereIn('user_id', $filteredResponses->pluck('user_id'))
            ->select('course_id', \DB::raw('count(*) as total'))
            ->groupBy('course_id')
            ->orderByDesc('total')
            ->with('course')
            ->first();
        $topCourse = $topCourseObj ? $topCourseObj->course->code : 'N/A';


        $analytics = [];
        foreach ($evaluation->questions as $question) {
            $data = [
                'id' => $question->id,
                'question' => $question->question_text,
                'type' => $question->type,
                'total_responses' => $totalResponses, // Filtered count
                'answers' => []
            ];

            // Only process if we have responses
            if ($totalResponses > 0) {
                if (in_array($question->type, ['radio', 'checkbox', 'scale'])) {
                    // Aggregate counts from FILTERED responses
                    $counts = [];
                    $options = $question->options ? json_decode($question->options, true) : [];

                    if ($question->type == 'scale') {
                        for ($i = 1; $i <= 5; $i++) {
                            $counts[$i] = 0;
                        }
                    } elseif (!empty($options)) {
                        foreach ($options as $opt) {
                            $counts[$opt] = 0;
                        }
                    }

                    // Get answers matching filtering
                    $answers = \App\Models\EvaluationAnswer::whereIn('response_id', $filteredResponses->pluck('id'))
                        ->where('question_id', $question->id)
                        ->get();

                    foreach ($answers as $ans) {
                        $val = $ans->answer_text;
                        if ($question->type == 'checkbox') {
                            $vals = json_decode($val, true);
                            if (is_array($vals)) {
                                foreach ($vals as $v) {
                                    if (!isset($counts[$v]))
                                        $counts[$v] = 0;
                                    $counts[$v]++;
                                }
                            } else {
                                if (!isset($counts[$val]))
                                    $counts[$val] = 0;
                                $counts[$val]++;
                            }
                        } else {
                            if (!isset($counts[$val]))
                                $counts[$val] = 0;
                            $counts[$val]++;
                        }
                    }
                    $data['stats'] = $counts;
                } else {
                    // Text answers
                    $data['text_answers'] = \App\Models\EvaluationAnswer::whereIn('response_id', $filteredResponses->pluck('id'))
                        ->where('question_id', $question->id)
                        ->whereNotNull('answer_text')
                        ->where('answer_text', '!=', '')
                        ->latest()
                        ->take(50) // Limit display
                        ->get()
                        ->pluck('answer_text');
                }
            } else {
                // Initialize empty stats structure if filter yields no results
                if (in_array($question->type, ['radio', 'checkbox', 'scale'])) {
                    $data['stats'] = [];
                } else {
                    $data['text_answers'] = collect([]);
                }
            }
            $analytics[] = $data;
        }

        // Dropdown Data
        $courses = \App\Models\Course::select('id', 'code', 'name')->orderBy('code')->get();
        // Get distinct batch years from profiles
        $batches = \App\Models\AlumniProfile::distinct()->orderBy('batch_year', 'desc')->pluck('batch_year');

        // Trend Data (Responses per day for the filtered period)
        $trendQuery = clone $responsesQuery;
        $trendDataRaw = $trendQuery->selectRaw('DATE(created_at) as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $trendLabels = $trendDataRaw->pluck('date')->map(function ($date) {
            return \Carbon\Carbon::parse($date)->format('M d');
        });
        $trendValues = $trendDataRaw->pluck('count');

        // New Insights Data (Course Distribution)
        // Need to join user->alumniProfile
        $courseDist = \App\Models\AlumniProfile::whereIn('user_id', $filteredResponses->pluck('user_id'))
            ->select('course_id', \DB::raw('count(*) as total'))
            ->groupBy('course_id')
            ->with('course')
            ->orderByDesc('total')
            ->take(10)
            ->get();
        $courseDistLabels = $courseDist->map(fn($item) => $item->course ? $item->course->code : 'Unknown')->values();
        $courseDistValues = $courseDist->pluck('total')->values();

        // New Insights Data (Batch Distribution)
        $batchDist = \App\Models\AlumniProfile::whereIn('user_id', $filteredResponses->pluck('user_id'))
            ->select('batch_year', \DB::raw('count(*) as total'))
            ->groupBy('batch_year')
            ->orderBy('batch_year', 'desc')
            ->take(8)
            ->get();
        $batchDistLabels = $batchDist->pluck('batch_year')->values();
        $batchDistValues = $batchDist->pluck('total')->values();

        $data = compact(
            'evaluation',
            'analytics',
            'totalResponses',
            'overallTotal',
            'courses',
            'batches',
            'latestResponse',
            'topCourse',
            'trendLabels',
            'trendValues',
            'courseDistLabels',
            'courseDistValues',
            'batchDistLabels',
            'batchDistValues'
        );

        if ($request->ajax()) {
            return view('admin.evaluations.partials.analytics_body', $data);
        }

        return view('admin.evaluations.analytics', $data);
    }
}
