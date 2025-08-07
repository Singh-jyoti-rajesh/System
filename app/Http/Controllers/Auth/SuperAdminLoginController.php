<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.superadmin-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt(array_merge($credentials, ['role' => 'super_admin']))) {
            return redirect()->route('superadmin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials or not a Super Admin.']);
    }
}
