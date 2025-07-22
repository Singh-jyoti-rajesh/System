<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'role' => 'required|in:admin,user',
            'admin_secret_key' => 'nullable|string'
        ]);

        if ($request->role === 'admin') {
            $expectedKey = env('ADMIN_SECRET_KEY');
            if ($request->admin_secret_key !== $expectedKey) {
                return back()->withErrors(['admin_secret_key' => 'Invalid Admin Secret Key.']);
            }
        }

        $otp = rand(100000, 999999);
        $otpExpiresAt = Carbon::now()->addMinutes(10);

        session([
            'pending_user' => [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'password' => bcrypt($request->password),
                'otp' => $otp,
                'otp_expires_at' => $otpExpiresAt,
            ]
        ]);

        Mail::raw("Your OTP is: $otp", function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('Your Registration OTP');
        });

        return redirect()->route('otp.verify.form')->with('success', 'OTP sent to your email.');
    }

    public function showOtpForm()
    {
        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);

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
        ]);

        Mail::raw("Hi {$user->name}, your registration is successful. You can now login.", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Registration Successful');
        });

        session()->forget('pending_user');

        return redirect()->route('login')->with('success', 'Registration successful. Please login.');
    }
}
