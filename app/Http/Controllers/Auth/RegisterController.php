<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class RegisterController extends Controller
{
    public function showRegisterForm($code = null)
    {
        if (!$code) {
            $code = request()->query('code');
        }

        $admin = User::where('invitation_code', $code)->first();

        return view('auth.register', [
            'invitationCode' => $admin ? $admin->invitation_code : ''
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'role' => 'required|in:admin,user',
            'admin_secret_key' => 'nullable|string',
            'admin_invitation_code' => 'nullable|string',
            'invitation_code' => 'nullable|string',
        ]);

        $invitationCode = null;
        $invitedBy = null;

        if ($request->role === 'admin') {
            $expectedKey = env('ADMIN_SECRET_KEY');
            if ($request->admin_secret_key !== $expectedKey) {
                return back()->withErrors(['admin_secret_key' => 'Invalid Admin Secret Key.']);
            }

            $adminCode = trim($request->admin_invitation_code);
            if ($adminCode) {
                $exists = User::where('invitation_code', $adminCode)->exists();
                if ($exists) {
                    return back()->withErrors(['admin_invitation_code' => 'Invitation code already taken.']);
                }
                $invitationCode = $adminCode;
            } else {
                $invitationCode = uniqid('jyoti_');
            }
        } elseif ($request->role === 'user') {
            if (empty($request->invitation_code)) {
                return back()->withErrors(['invitation_code' => 'Invitation code is required for users.']);
            }

            $inviter = User::where('invitation_code', $request->invitation_code)->first();

            if (!$inviter) {
                return back()->withErrors(['invitation_code' => 'Invalid invitation code.']);
            }

            $invitedBy = $inviter->id;
            $invitationCode = uniqid('user_');
        }

        // OTP setup
        $otp = rand(100000, 999999);
        $otpExpiresAt = Carbon::now()->addMinutes(10);

        session([
            'pending_user' => [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'password' => bcrypt($request->password),
                'invitation_code' => $invitationCode,
                'invited_by' => $invitedBy,
                'otp' => $otp,
                'otp_expires_at' => $otpExpiresAt,
            ]
        ]);

        Mail::raw("Your OTP is: $otp", function ($message) use ($request) {
            $message->to($request->email)->subject('Your Registration OTP');
        });

        return redirect()->route('otp.verify.form')->with('success', 'OTP sent to your email.');
    }

    public function showOtpForm()
    {
        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|numeric']);

        $pendingUser = session('pending_user');

        if (!$pendingUser) {
            return redirect()->route('register')->withErrors(['message' => 'Session expired. Please register again.']);
        }

        if ($pendingUser['otp'] != $request->otp || now()->gt($pendingUser['otp_expires_at'])) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        $user = User::create([
            'name' => $pendingUser['name'],
            'email' => $pendingUser['email'],
            'password' => $pendingUser['password'],
            'role' => $pendingUser['role'],
            'invitation_code' => $pendingUser['invitation_code'],
            'invited_by' => $pendingUser['invited_by'] ?? null,
        ]);

        Mail::raw("Hi {$user->name}, your registration is successful.", function ($message) use ($user) {
            $message->to($user->email)->subject('Registration Successful');
        });

        Auth::login($user);
        session()->forget('pending_user');

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')->with('success', 'Registration successful. Welcome!');
        }

        return redirect()->route('user.dashboard')->with('success', 'Registration successful. Welcome!');
    }
}
