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
        // Get only the latest version of each form lineage OR show all independent forms
        // For simplicity: Show all forms, but grouped if they have parents. 
        // Better approach: Show "Active" and "Draft" forms.
        $forms = EvaluationForm::withCount('responses')
            ->where('type', '!=', 'tracer') // Exclude Tracer Surveys
            ->orderByRaw('parent_form_id IS NULL DESC') // Parents first
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.evaluations.index', compact('forms'));
    }

    public function create()
    {
        return view('admin.evaluations.create');
    }

    public function edit(EvaluationForm $evaluation)
    {
        $evaluation->load('questions');
        return view('admin.evaluations.edit', compact('evaluation'));
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
            'save_as_active' => 'boolean' // Checkbox in UI
        ]);

        $form = EvaluationForm::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'is_active' => $request->boolean('save_as_active'),
            'is_draft' => !$request->boolean('save_as_active'),
            'version' => 1,
            'parent_form_id' => null
        ]);

        $this->saveQuestions($form, $validated['questions']);

        return redirect()->route('admin.evaluations.index')->with('success', 'Evaluation form created successfully.');
    }

    public function duplicate(EvaluationForm $evaluation)
    {
        $newForm = $evaluation->replicate(['is_active', 'created_at', 'updated_at']);
        $newForm->title = "Copy of " . $evaluation->title;
        $newForm->is_active = false; // Draft by default
        $newForm->is_draft = true;
        $newForm->version = 1; // Reset version for new lineage
        $newForm->parent_form_id = null;
        $newForm->save();

        foreach ($evaluation->questions as $question) {
            $newQuestion = $question->replicate(['form_id', 'created_at', 'updated_at']);
            $newQuestion->form_id = $newForm->id;
            $newQuestion->save();
        }

        return redirect()->route('admin.evaluations.index')->with('success', 'Form duplicated as draft.');
    }

    // Custom update to handle versioning
    public function update(Request $request, EvaluationForm $evaluation)
    {
        // If form has responses, we MUST version it.
        if ($evaluation->responses()->exists()) {
            return $this->createVersion($request, $evaluation);
        }

        // Otherwise regular update
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|string',
            'questions.*.options' => 'nullable|array',
            'questions.*.required' => 'boolean',
            'is_active' => 'boolean'
        ]);

        $evaluation->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'is_active' => $request->boolean('is_active'),
            'is_draft' => !$request->boolean('is_active'),
        ]);

        // Replace questions (simplest way since no IDs tracked in UI yet)
        $evaluation->questions()->delete();
        $this->saveQuestions($evaluation, $validated['questions']);

        return redirect()->route('admin.evaluations.index')->with('success', 'Form updated successfully.');
    }

    protected function createVersion(Request $request, EvaluationForm $oldForm)
    {
        // Deactivate old form
        $oldForm->update(['is_active' => false]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|string',
            'questions.*.options' => 'nullable|array',
            'questions.*.required' => 'boolean',
            'is_active' => 'boolean'
        ]);

        $newForm = EvaluationForm::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'is_active' => $request->boolean('is_active'),
            'is_draft' => !$request->boolean('is_active'),
            'version' => $oldForm->version + 1,
            'parent_form_id' => $oldForm->parent_form_id ?? $oldForm->id // Maintain lineage
        ]);

        $this->saveQuestions($newForm, $validated['questions']);

        return redirect()->route('admin.evaluations.index')->with('success', 'New version created. Previous version archived.');
    }

    protected function saveQuestions($form, $questions)
    {
        foreach ($questions as $index => $q) {
            EvaluationQuestion::create([
                'form_id' => $form->id,
                'question_text' => $q['text'],
                'type' => $q['type'],
                'options' => isset($q['options']) ? json_encode($q['options']) : null,
                'order' => $index + 1,
                'required' => $q['required'] ?? true,
            ]);
        }
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
