<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EvaluationForm;
use App\Models\EvaluationQuestion;

class EvaluationSeeder extends Seeder
{
    public function run()
    {
        // 1. Graduate Tracer Study
        $tracer = EvaluationForm::create([
            'title' => 'Graduate Tracer Study 2026',
            'description' => 'Official CHED-required tracer study to track alumni employment and career progress.',
            'type' => 'tracer',
            'is_active' => true,
        ]);

        $questionsTracker = [
            [
                'question_text' => 'What is your current employment status?',
                'type' => 'radio',
                'options' => json_encode(['Employed', 'Self-employed', 'Unemployed', 'Pursuing further studies']),
                'order' => 1
            ],
            [
                'question_text' => 'If employed, is your job related to your course?',
                'type' => 'radio',
                'options' => json_encode(['Yes', 'No']),
                'order' => 2
            ],
            [
                'question_text' => 'How long did it take you to find your first job?',
                'type' => 'radio',
                'options' => json_encode(['Less than a month', '1-6 months', '6 months - 1 year', 'More than 1 year']),
                'order' => 3
            ],
            [
                'question_text' => 'Name of your current company/organization',
                'type' => 'text',
                'options' => null,
                'order' => 4
            ],
            [
                'question_text' => 'Current Job Position',
                'type' => 'text',
                'options' => null,
                'order' => 5
            ]
        ];

        foreach ($questionsTracker as $q) {
            EvaluationQuestion::create(array_merge($q, ['form_id' => $tracer->id]));
        }

        // 2. System Usability Survey
        $usability = EvaluationForm::create([
            'title' => 'Alumni System Usability Feedback',
            'description' => 'Help us improve the alumni portal by rating your experience.',
            'type' => 'usability',
            'is_active' => true,
        ]);

        $questionsUsability = [
            [
                'question_text' => 'How easy is it to navigate the alumni portal?',
                'type' => 'scale', // 1-5
                'options' => json_encode(['1' => 'Very Difficult', '5' => 'Very Easy']),
                'order' => 1
            ],
            [
                'question_text' => 'How would you rate the "Job Hunting" feature?',
                'type' => 'scale',
                'options' => json_encode(['1' => 'Not Useful', '5' => 'Extremely Useful']),
                'order' => 2
            ],
            [
                'question_text' => 'What features would you like to see in the future?',
                'type' => 'text',
                'options' => null,
                'order' => 3
            ],
            [
                'question_text' => 'Would you recommend this portal to other alumni?',
                'type' => 'radio',
                'options' => json_encode(['Yes', 'No']),
                'order' => 4
            ]
        ];

        foreach ($questionsUsability as $q) {
            EvaluationQuestion::create(array_merge($q, ['form_id' => $usability->id]));
        }
    }
}
