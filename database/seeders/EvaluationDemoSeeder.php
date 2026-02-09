<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EvaluationForm;
use App\Models\EvaluationQuestion;
use App\Models\EvaluationResponse;
use App\Models\EvaluationAnswer;
use App\Models\User;
use App\Models\AlumniProfile;
use Carbon\Carbon;

class EvaluationDemoSeeder extends Seeder
{
    public function run()
    {
        // 1. Ensure Base Forms Exist (Tracer and SUS)
        $this->call(EvaluationSeeder::class);

        $tracer = EvaluationForm::where('type', 'tracer')->first();
        $sus = EvaluationForm::where('type', 'usability')->first();

        // 2. Create Departmental Forms
        $departments = [
            'CITE' => [
                'title' => 'CITE Technical Readiness Evaluation',
                'questions' => [
                    ['text' => 'How would you rate the laboratory facilities in CITE?', 'type' => 'scale'],
                    ['text' => 'The curriculum provided me with enough technical exposure for the industry.', 'type' => 'scale'],
                    ['text' => 'How relevant were the programming languages taught to your current role?', 'type' => 'radio', 'options' => ['Very Relevant', 'Somewhat Relevant', 'Not Relevant', 'N/A']],
                    ['text' => 'Which technologies do you wish were included in the curriculum?', 'type' => 'checkbox', 'options' => ['Cloud Computing', 'AI/ML', 'Mobile Dev', 'Cybersecurity', 'DevOps']],
                    ['text' => 'Any suggestions for the CITE department?', 'type' => 'textarea', 'required' => false],
                ]
            ],
            'CBA' => [
                'title' => 'CBA Industry Connection Survey',
                'questions' => [
                    ['text' => 'The business cases discussed in class were realistic.', 'type' => 'scale'],
                    ['text' => 'How would you rate the department\'s assistance in internship placement?', 'type' => 'scale'],
                    ['text' => 'Did the program prepare you for leadership roles?', 'type' => 'radio', 'options' => ['Yes', 'No', 'Partially']],
                    ['text' => 'Which business soft skills helped you the most?', 'type' => 'checkbox', 'options' => ['Negotiation', 'Public Speaking', 'Financial Literacy', 'Strategic Planning']],
                ]
            ],
            'COE' => [
                'title' => 'COE Board Preparation & Facilities Audit',
                'questions' => [
                    ['text' => 'The laboratory equipment was up-to-date and functional.', 'type' => 'scale'],
                    ['text' => 'The instructors were effective in teaching fundamental engineering concepts.', 'type' => 'scale'],
                    ['text' => 'Rate your satisfaction with the internal board review programs.', 'type' => 'scale'],
                    ['text' => 'Would you recommend this engineering program to others?', 'type' => 'radio', 'options' => ['Strongly Recommend', 'Recommend', 'Neutral', 'Do Not Recommend']],
                ]
            ]
        ];

        $deptForms = [];
        foreach ($departments as $deptCode => $info) {
            $form = EvaluationForm::firstOrCreate(
                ['title' => $info['title'], 'department_name' => $deptCode],
                [
                    'description' => "Department-specific feedback for {$deptCode}.",
                    'type' => 'evaluation',
                    'is_active' => true,
                    'is_draft' => false,
                    'version' => 1
                ]
            );

            if ($form->questions()->count() == 0) {
                foreach ($info['questions'] as $index => $q) {
                    EvaluationQuestion::create([
                        'form_id' => $form->id,
                        'question_text' => $q['text'],
                        'type' => $q['type'],
                        'options' => isset($q['options']) ? json_encode($q['options']) : null,
                        'order' => $index + 1,
                        'required' => $q['required'] ?? true
                    ]);
                }
            }
            $deptForms[$deptCode] = $form;
        }

        // 3. Generate Responses for Active Alumni
        $activeAlumni = User::where('role', 'alumni')->where('status', 'active')->with('alumniProfile')->get();

        foreach ($activeAlumni as $user) {
            $profile = $user->alumniProfile;
            if (!$profile)
                continue;

            // Randomly decide if this user has responded (70% chance)
            if (rand(1, 100) > 70)
                continue;

            // Date randomization within the last 6 months
            $respondedAt = Carbon::now()->subDays(rand(0, 180));

            // A. Response to SUS (Universal)
            $this->createWeightedResponse($sus, $user, $respondedAt, 'sus');

            // B. Response to Tracer (Universal) - REMOVED per user request
            // $this->createWeightedResponse($tracer, $user, $respondedAt, 'tracer', $profile);

            // C. Response to Departmental Form (Specific)
            if (isset($deptForms[$profile->department_name])) {
                $this->createWeightedResponse($deptForms[$profile->department_name], $user, $respondedAt, 'dept');
            }
        }
    }

    private function createWeightedResponse($form, $user, $date, $category, $profile = null)
    {
        // Don't duplicate responses
        if (EvaluationResponse::where('form_id', $form->id)->where('user_id', $user->id)->exists()) {
            return;
        }

        $response = EvaluationResponse::create([
            'form_id' => $form->id,
            'user_id' => $user->id,
            'department_name' => $user->alumniProfile->department_name ?? null,
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        foreach ($form->questions as $question) {
            $answer = $this->generateWeightedAnswer($question, $category, $profile);

            if ($answer !== null) {
                EvaluationAnswer::create([
                    'response_id' => $response->id,
                    'question_id' => $question->id,
                    'answer_text' => is_array($answer) ? json_encode($answer) : (string) $answer,
                ]);
            }
        }
    }

    private function generateWeightedAnswer($question, $category, $profile = null)
    {
        $type = $question->type;
        $options = $question->options ? json_decode($question->options, true) : [];

        if ($type === 'scale') {
            // SUS or Dept scale questions: Usually positive (4-5) but some 1-2 for frustration
            if ($category === 'sus') {
                // Alternating logic for SUS even questions (negative questions)
                if ($question->order % 2 == 0) {
                    return rand(1, 100) > 80 ? rand(3, 5) : rand(1, 2);
                }
                return rand(1, 100) > 20 ? rand(4, 5) : rand(3, 3);
            }
            return rand(1, 100) > 30 ? rand(4, 5) : rand(2, 3);
        }

        if ($type === 'radio') {
            if ($category === 'tracer' && $question->question_text === 'Employment Status') {
                // Sync with alumni profile if possible
                if ($profile && $profile->employment_status) {
                    return $profile->employment_status;
                }
                return 'Employed';
            }
            if ($category === 'tracer' && str_contains($question->question_text, 'related')) {
                return rand(1, 100) > 40 ? 'Yes' : (rand(1, 100) > 50 ? 'Partially' : 'No');
            }
            if ($category === 'tracer' && str_contains($question->question_text, 'how long')) {
                return rand(1, 100) > 60 ? '1-6 months' : 'Less than 1 month';
            }

            return !empty($options) ? $options[array_rand($options)] : 'N/A';
        }

        if ($type === 'checkbox') {
            if (empty($options))
                return [];
            $count = rand(1, min(3, count($options)));
            return (array) array_rand(array_flip($options), $count);
        }

        if ($type === 'textarea') {
            $comments = [
                'Generally satisfied with the support provided.',
                'The system is much better than the old manual process.',
                'Wish there were more job notifications.',
                'The course was relevant but could use more hands-on labs.',
                'Grateful for the opportunity to connect with fellow alumni.',
                'The SUS interface is clean and professional.',
                'Keep up the good work!'
            ];
            return $comments[array_rand($comments)];
        }

        return null;
    }
}
