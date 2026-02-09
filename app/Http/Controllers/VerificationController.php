<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use App\Notifications\VerificationCodeNotification;

class VerificationController extends Controller
{
    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'type' => 'required|in:signup,email_update,password_update'
        ]);

        // Check if email already exists for signup
        if ($request->type === 'signup' && \App\Models\User::where('email', $request->email)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This email is already registered. Please log in instead.'
            ], 422);
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $cacheKey = 'otp_' . $request->type . '_' . $request->email;

        // Store code in cache for 5 minutes
        Cache::put($cacheKey, $code, 300);

        try {
            // Send notification to the email (even if user doesn't exist yet for signup)
            Notification::route('mail', $request->email)
                ->notify(new VerificationCodeNotification($code, $request->type));

            return response()->json([
                'success' => true,
                'message' => 'Verification code sent! Please check your inbox (and spam folder).'
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send verification code: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email. Please try again later.'
            ], 500);
        }
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
            'type' => 'required|in:signup,email_update,password_update'
        ]);

        $cacheKey = 'otp_' . $request->type . '_' . $request->email;
        $storedCode = Cache::get($cacheKey);

        if ($storedCode && $storedCode === $request->code) {
            // Mark as verified in cache for 10 minutes to allow the main action to proceed
            Cache::put($cacheKey . '_verified', true, 600);

            return response()->json([
                'success' => true,
                'message' => 'Code verified successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid or expired verification code.'
        ], 422);
    }
}
