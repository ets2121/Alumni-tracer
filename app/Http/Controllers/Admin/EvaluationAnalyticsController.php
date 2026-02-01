<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EvaluationForm;

class EvaluationAnalyticsController extends Controller
{
    public function show(EvaluationForm $evaluation)
    {
        $evaluation->load(['questions', 'responses.answers']);

        $analytics = [];
        foreach ($evaluation->questions as $question) {
            $data = [
                'id' => $question->id,
                'question' => $question->question_text,
                'type' => $question->type,
                'total_responses' => $evaluation->responses->count(),
                'answers' => []
            ];

            if (in_array($question->type, ['radio', 'checkbox', 'scale'])) {
                // Aggregate counts
                $counts = [];
                $options = $question->options ? json_decode($question->options, true) : [];

                // For scale, we might want 1-5 pre-filled
                if ($question->type == 'scale') {
                    for ($i = 1; $i <= 5; $i++) {
                        $counts[$i] = 0;
                    }
                } elseif (!empty($options)) {
                    foreach ($options as $opt) {
                        $counts[$opt] = 0;
                    }
                }

                $answers = \App\Models\EvaluationAnswer::where('question_id', $question->id)->get();

                foreach ($answers as $ans) {
                    // Checkbox answers might be JSON encoded arrays if stored that way, 
                    // dependent on how Alumni/EvaluationController stores them. 
                    // Assuming stored as individual rows or comma separated?
                    // Let's assume simplest case: single value or simple string match for now.
                    // Ideally, checkbox answers should be separate rows or JSON. 
                    // If JSON:
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
                // text answers - get all non-empty
                $data['text_answers'] = \App\Models\EvaluationAnswer::where('question_id', $question->id)
                    ->whereNotNull('answer_text')
                    ->where('answer_text', '!=', '')
                    ->latest()
                    ->get()
                    ->pluck('answer_text');
            }
            $analytics[] = $data;
        }

        return view('admin.evaluations.analytics', compact('evaluation', 'analytics'));
    }
}
