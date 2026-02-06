<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class GTSFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = database_path('data/ched_gts_form.json');

        if (!File::exists($jsonPath)) {
            $this->command->error("File not found: $jsonPath");
            return;
        }

        $jsonContent = File::get($jsonPath);
        $data = json_decode($jsonContent, true);

        if (!$data) {
            $this->command->error("Invalid JSON content");
            return;
        }

        DB::transaction(function () use ($data) {
            $metadata = $data['form_metadata'];
            $questions = $data['questions'];

            // Check if form exists and delete if necessary (or update)
            // For rigorous update, we delete and recreate to ensure structure match
            $existingForm = DB::table('evaluation_forms')->where('title', $metadata['name'])->first();

            if ($existingForm) {
                // Delete existing form and cascading questions/responses
                // Assuming foreign keys are set to cascade on delete
                DB::table('evaluation_forms')->where('id', $existingForm->id)->delete();
                $this->command->info("Deleted existing form: " . $metadata['name']);
            }

            // Create Form
            $formId = DB::table('evaluation_forms')->insertGetId([
                'title' => $metadata['name'],
                'description' => $metadata['description'], // This is the header text
                'type' => $metadata['type'],
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $this->command->info("Created form: " . $metadata['name']);

            // Create Questions
            foreach ($questions as $index => $q) {
                // Prepare options JSON
                // We store all extra fields in options column for flexibility
                $optionsData = [];

                if (isset($q['options']))
                    $optionsData['options'] = $q['options'];
                if (isset($q['sub_fields']))
                    $optionsData['sub_fields'] = $q['sub_fields'];
                if (isset($q['table_columns']))
                    $optionsData['table_columns'] = $q['table_columns'];
                if (isset($q['min_rows']))
                    $optionsData['min_rows'] = $q['min_rows'];
                if (isset($q['matrix_categories']))
                    $optionsData['matrix_categories'] = $q['matrix_categories'];
                if (isset($q['matrix_options']))
                    $optionsData['matrix_options'] = $q['matrix_options'];
                if (isset($q['other_option_label']))
                    $optionsData['other_option_label'] = $q['other_option_label'];
                if (isset($q['conditional_logic']))
                    $optionsData['conditional_logic'] = $q['conditional_logic'];
                if (isset($q['question_number']))
                    $optionsData['question_number'] = $q['question_number'];

                DB::table('evaluation_questions')->insert([
                    'form_id' => $formId,
                    'question_text' => $q['question_text'], // We might want to prepend question number if needed, but UI can handle it from `question_number` in options
                    'type' => $q['answer_type'],
                    'section' => $q['section'],
                    'options' => !empty($optionsData) ? json_encode($optionsData) : null,
                    'order' => $index + 1,
                    'required' => $q['is_required'] ?? false,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            $this->command->info("Inserted " . count($questions) . " questions.");
        });
    }
}
