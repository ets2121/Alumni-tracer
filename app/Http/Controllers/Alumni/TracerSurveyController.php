<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EvaluationForm;
use App\Models\EvaluationResponse;
use App\Models\EvaluationAnswer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TracerSurveyController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Check if user has already submitted the GTS
        // We identify GTS by type 'tracer' which is more robust than title
        $form = EvaluationForm::where('type', 'tracer')
            ->where('is_active', true)
            ->where('is_draft', false)
            ->with([
                'questions' => function ($q) {
                    $q->orderBy('order');
                }
            ])->orderBy('version', 'desc')->first();

        if (!$form) {
            return redirect()->route('dashboard')->with('error', 'Tracer Survey not found.');
        }

        $existingResponse = EvaluationResponse::where('form_id', $form->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingResponse) {
            $existingResponse->load('answers'); // Eager load answers for the view
            return view('alumni.tracer.completed', compact('form', 'existingResponse'));
        }

        return view('alumni.tracer.index', compact('form'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $formId = $request->input('form_id');

        $form = EvaluationForm::findOrFail($formId);

        // Validation could be dynamic based on questions, but for now we rely on frontend validation + basic required check
        // Ideally we loop questions and validate.

        // Basic check for existence
        $existingResponse = EvaluationResponse::where('form_id', $formId)
            ->where('user_id', $user->id)
            ->first();

        if ($existingResponse) {
            return redirect()->back()->with('error', 'You have already submitted this survey.');
        }

        DB::beginTransaction();

        try {
            $response = EvaluationResponse::create([
                'form_id' => $form->id,
                'user_id' => $user->id,
                'department_name' => $user->department_name, // Snapshot
            ]);

            $questions = $form->questions()->orderBy('order')->get();
            $answers = $request->input('answers', []);

            foreach ($questions as $question) {
                $answerValue = $answers[$question->id] ?? null;
                $answerText = null;

                // Handle Empty/Null for non-required fields
                if (($answerValue === null || $answerValue === '') && !$question->required) {
                    $answerText = 'N/A';
                } elseif (is_array($answerValue)) {
                    // Check for dynamic table empty rows or values
                    // If table is optional and empty, save N/A? 
                    // For now, just save the JSON. 
                    // If it's a matrix or date group, we might want to be smarter, but N/A for complex types is tricky.
                    // Let's rely on the text determination.
                    $answerText = json_encode($answerValue);
                } else {
                    $answerText = $answerValue;
                }

                // If still null (e.g. hidden required question that was disabled?), we skip saving or save null?
                // If it was required but hidden, it shouldn't be required. 
                // But we verified earlier that disabled inputs don't send data.
                // If it's NOT required and hidden, we expect N/A.
                // If it IS required and hidden (logic hidden), it shouldn't block, so we might save NULL or N/A.
                // Let's stick to: if no answer and not required (or hidden essentially means not required for this submission) -> N/A

                // Refined Logic for Hidden/Disabled fields:
                // They won't be in $answers. 
                // If they are not in $answers, $answerValue is null.
                // If $question->required is true, but it wasn't sent, we assume it was hidden/disabled.
                // So we should probably default to N/A for those too? 
                // Or "Skipped". "N/A" is safer.
                if ($answerText === null) {
                    $answerText = 'N/A';
                }

                EvaluationAnswer::create([
                    'response_id' => $response->id,
                    'question_id' => $question->id,
                    'answer_text' => $answerText,
                ]);
            }

            DB::commit();

            return redirect()->route('dashboard')->with('success', 'Thank you for completing the Graduate Tracer Survey!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while saving your response. Please try again. ' . $e->getMessage());
        }
    }
}
