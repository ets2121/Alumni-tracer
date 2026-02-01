<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        $admin = Auth::user();
        $activityLogs = ActivityLog::where('user_id', $admin->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.profile.edit', compact('admin', 'activityLogs'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $cacheKey = 'otp_email_update_' . $request->email . '_verified';
            if (!\Illuminate\Support\Facades\Cache::get($cacheKey)) {
                return back()->withErrors(['email' => 'Verification required for email change.']);
            }
            \Illuminate\Support\Facades\Cache::forget($cacheKey);
            $user->email_verified_at = null;
        }

        $user->save();

        $this->logActivity('Profile Update', 'Updated profile information (Name/Email)');

        return redirect()->route('admin.profile.edit')->with('success', 'Profile information updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = Auth::user();
        $cacheKey = 'otp_password_update_' . $user->email . '_verified';
        if (!\Illuminate\Support\Facades\Cache::get($cacheKey)) {
            return back()->withErrors(['password' => 'Identity verification required.']);
        }
        \Illuminate\Support\Facades\Cache::forget($cacheKey);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->logActivity('Password Change', 'Administrator changed their password');

        return redirect()->route('admin.profile.edit')->with('success', 'Password updated successfully.');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $path]);

            $this->logActivity('Avatar Update', 'Changed profile picture');
        }

        return redirect()->route('admin.profile.edit')->with('success', 'Profile picture updated successfully.');
    }

    private function logActivity($action, $description)
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
