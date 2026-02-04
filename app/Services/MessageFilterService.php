<?php

namespace App\Services;

use App\Models\BannedWord;
use App\Models\User;

class MessageFilterService
{
    /**
     * Check if content contains any banned words.
     * 
     * @param string $content
     * @param User $user
     * @return void
     * @throws \Exception
     */
    public function validateConfirmed(string $content, User $user)
    {
        // 1. Get Global Banned Words
        $globalWords = BannedWord::whereNull('department_name')->pluck('word')->toArray();

        // 2. Get Department Banned Words (if user belongs to one)
        $deptWords = [];
        if ($user->department_name) {
            $deptWords = BannedWord::where('department_name', $user->department_name)->pluck('word')->toArray();
        }

        $allBanned = array_merge($globalWords, $deptWords);

        if (empty($allBanned)) {
            return;
        }

        // 3. Check for matches (Case Insensitive)
        foreach ($allBanned as $word) {
            // Use word boundary to avoid false positives (e.g. "ass" in "class")
            // Escape the banned word for regex safety
            $pattern = '/\b' . preg_quote($word, '/') . '\b/i';

            if (preg_match($pattern, $content)) {
                throw new \Exception("Message contains a banned word: " . $word);
            }
        }
    }
}
