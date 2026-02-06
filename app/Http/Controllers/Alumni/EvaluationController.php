<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\EvaluationForm;
use App\Models\EvaluationResponse;
use App\Models\EvaluationAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    public function index()
    {
        // Get forms that are active
        // Check if user has already responded
        $forms = EvaluationForm::where('is_active', true)
            ->where('type', '!=', 'tracer') // Exclude Tracer Surveys
            ->withCount([
                'responses' => function ($q) {
                    $q->where('user_id', Auth::id());
                }
            ])
            ->get();

        return view('alumni.evaluations.index', compact('forms'));
    }

    public function show(EvaluationForm $evaluation)
    {
        // Check if user already responded
        $response = EvaluationResponse::where('form_id', $evaluation->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($response) {
            return redirect()->route('alumni.evaluations.index')->with('info', 'You have already submitted this evaluation.');
        }

        $evaluation->load([
            'questions' => function ($q) {
                $q->orderBy('order');
            }
        ]);

        return view('alumni.evaluations.show', compact('evaluation'));
    }

    public function store(Request $request, EvaluationForm $evaluation)
    {
        $rules = [];
        foreach ($evaluation->questions as $question) {
            if ($question->required) {
                $rules["q_{$question->id}"] = 'required';
            }
        }

        $request->validate($rules);

        // check double submit again
        $exists = EvaluationResponse::where('form_id', $evaluation->id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($exists) {
            return redirect()->route('alumni.evaluations.index')->with('error', 'Already submitted.');
        }

        $response = EvaluationResponse::create([
            'form_id' => $evaluation->id,
            'user_id' => Auth::id(),
        ]);

        foreach ($evaluation->questions as $question) {
            $answerVal = $request->input("q_{$question->id}");

            if (is_array($answerVal)) {
                $answerVal = json_encode($answerVal);
            }

            if ($answerVal !== null) {
                EvaluationAnswer::create([
                    'response_id' => $response->id,
                    'question_id' => $question->id,
                    'answer_text' => $answerVal
                ]);
            }
        }

        return redirect()->route('alumni.evaluations.index')->with('success', 'Thank you for your feedback!');
    }
}
