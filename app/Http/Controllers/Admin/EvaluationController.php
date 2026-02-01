<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvaluationForm;
use App\Models\EvaluationQuestion;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function index()
    {
        $forms = EvaluationForm::withCount('responses')->latest()->get();
        return view('admin.evaluations.index', compact('forms'));
    }

    public function create()
    {
        return view('admin.evaluations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|string',
            'questions.*.options' => 'nullable|array',
            'questions.*.required' => 'boolean',
        ]);

        $form = EvaluationForm::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'is_active' => true,
        ]);

        foreach ($validated['questions'] as $index => $q) {
            EvaluationQuestion::create([
                'form_id' => $form->id,
                'question_text' => $q['text'],
                'type' => $q['type'],
                'options' => isset($q['options']) ? json_encode($q['options']) : null,
                'order' => $index + 1,
                'required' => $q['required'] ?? true,
            ]);
        }

        return redirect()->route('admin.evaluations.index')->with('success', 'Evaluation form created successfully.');
    }

    public function show(EvaluationForm $evaluation)
    {
        $evaluation->load(['questions', 'responses.answers']);

        $analytics = [];
        foreach ($evaluation->questions as $question) {
            $data = [
                'question' => $question->question_text,
                'type' => $question->type,
                'total_responses' => $evaluation->responses->count(),
                'answers' => []
            ];

            if (in_array($question->type, ['radio', 'checkbox', 'scale'])) {
                // aggregate counts
                $counts = [];
                $options = json_decode($question->options, true) ?? [];

                // Initialize counts with 0 for all options (handle indexed array vs key-value for scale)
                if ($question->type == 'scale') {
                    // Keep it simple for scale: just distribution of values 1-5
                }

                $answers = \App\Models\EvaluationAnswer::where('question_id', $question->id)->get();

                foreach ($answers as $ans) {
                    $val = $ans->answer_text;
                    if (!isset($counts[$val]))
                        $counts[$val] = 0;
                    $counts[$val]++;
                }
                $data['stats'] = $counts;
            } else {
                // text answers - just last 5 for preview
                $data['recent_answers'] = \App\Models\EvaluationAnswer::where('question_id', $question->id)
                    ->latest()
                    ->take(5)
                    ->pluck('answer_text');
            }
            $analytics[] = $data;
        }

        return view('admin.evaluations.show', compact('evaluation', 'analytics'));
    }

    public function destroy(EvaluationForm $evaluation)
    {
        $evaluation->delete();
        return redirect()->route('admin.evaluations.index')->with('success', 'Form deleted successfully.');
    }
}
