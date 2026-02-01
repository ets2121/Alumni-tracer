<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = $request->user();

        // OTP for password update check
        $cacheKey = 'otp_password_update_' . $user->email . '_verified';
        if (!\Illuminate\Support\Facades\Cache::get($cacheKey)) {
            return back()->withErrors(['password' => 'Please verify your identity with the code sent to your email.'], 'updatePassword');
        }
        \Illuminate\Support\Facades\Cache::forget($cacheKey);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}
