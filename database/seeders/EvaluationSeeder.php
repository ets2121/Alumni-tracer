<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EvaluationForm;
use App\Models\EvaluationQuestion;

class EvaluationSeeder extends Seeder
{
    public function run()
    {
        // 1. GRADUATE TRACER STUDY
        $tracer = EvaluationForm::updateOrCreate(
            ['type' => 'tracer'],
            [
                'title' => 'CHED Graduate Tracer Survey (GTS)',
                'description' => 'Standardized survey to track employment status and curriculum relevance.',
                'is_active' => true,
                'is_draft' => false,
                'version' => 1
            ]
        );

        // Clear existing questions to be sure
        $tracer->questions()->delete();

        $questions = [
            [
                'text' => 'Employment Status',
                'type' => 'radio',
                'options' => ['Employed', 'Self-employed', 'Unemployed', 'Student'],
                'required' => true
            ],
            [
                'text' => 'Is your current job related to your academic program?',
                'type' => 'radio',
                'options' => ['Yes', 'No', 'Partially'],
                'required' => true
            ],
            [
                'text' => 'How long did it take you to find your first job?',
                'type' => 'radio',
                'options' => ['Less than 1 month', '1-6 months', '6-12 months', 'Over 1 year'],
                'required' => true
            ],
            [
                'text' => 'Which skills learned in college are most useful in your current job?',
                'type' => 'checkbox',
                'options' => ['Critical Thinking', 'Communication', 'Technical Skills', 'Teamwork', 'Leadership'],
                'required' => false
            ],
            [
                'text' => 'Please provide suggestions to improve the curriculum.',
                'type' => 'textarea',
                'options' => null,
                'required' => false
            ]
        ];

        foreach ($questions as $index => $q) {
            EvaluationQuestion::create([
                'form_id' => $tracer->id,
                'question_text' => $q['text'],
                'type' => $q['type'],
                'options' => isset($q['options']) ? json_encode(['options' => $q['options']]) : null,
                'order' => $index + 1,
                'required' => $q['required']
            ]);
        }

        // 1. SYSTEM USABILITY STUDY
        $sus = EvaluationForm::firstOrCreate(
            ['title' => 'System Usability Evaluation'],
            [
                'description' => 'Feedback on the alumni portal user experience.',
                'type' => 'usability',
                'is_active' => true,
                'is_draft' => false,
                'version' => 1
            ]
        );

        if ($sus->questions()->count() == 0) {
            $susQuestions = [
                ['text' => 'I think that I would like to use this system frequently.', 'type' => 'scale'],
                ['text' => 'I found the system unnecessarily complex.', 'type' => 'scale'],
                ['text' => 'I thought the system was easy to use.', 'type' => 'scale'],
                ['text' => 'I think that I would need the support of a technical person to be able to use this system.', 'type' => 'scale'],
                ['text' => 'I found the various functions in this system were well integrated.', 'type' => 'scale'],
                ['text' => 'I thought there was too much inconsistency in this system.', 'type' => 'scale'],
                ['text' => 'I would imagine that most people would learn to use this system very quickly.', 'type' => 'scale'],
                ['text' => 'I found the system very cumbersome to use.', 'type' => 'scale'],
                ['text' => 'I felt very confident using the system.', 'type' => 'scale'],
                ['text' => 'I needed to learn a lot of things before I could get going with this system.', 'type' => 'scale'],
                ['text' => 'Do you have any other comments or suggestions?', 'type' => 'textarea', 'required' => false],
            ];

            foreach ($susQuestions as $index => $q) {
                EvaluationQuestion::create([
                    'form_id' => $sus->id,
                    'question_text' => $q['text'],
                    'type' => $q['type'],
                    'options' => null,
                    'order' => $index + 1,
                    'required' => $q['required'] ?? true
                ]);
            }
        }
    }
}
