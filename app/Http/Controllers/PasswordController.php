<?php

namespace App\Http\Controllers;

use App\Mail\SendOtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordController extends Controller
{
    public function showChangeForm()
    {
        return view('change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Password changed successfully.');
    }

    public function forgotForm()
    {
        return view('forgot-password');
    }

    public function sendOtp(Request $request)
    {
        
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Email not found.');
        }

        $otp = rand(100000, 999999);

        session([
            'reset_email' => $request->email,
            'reset_otp' => $otp,
            'reset_otp_expires_at' => now()->addMinutes(10)->timestamp,
        ]);

        Mail::to($request->email)->send(new SendOtpMail($otp));
          
        return redirect()->route('verify-otp.form')->with('success', 'OTP sent to your email.');
    }

    public function verifyForm()
    {
        if (!session('reset_email')) {
            return redirect()->route('forgot-password')->with('error', 'Please enter your email first.');
        }

        return view('otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        if (!session('reset_otp') || !session('reset_otp_expires_at')) {
            return redirect()->route('forgot-password')->with('error', 'OTP session expired.');
        }

        if (now()->timestamp > session('reset_otp_expires_at')) {
            session()->forget(['reset_email', 'reset_otp', 'reset_otp_expires_at']);
            return redirect()->route('forgot-password')->with('error', 'OTP expired.');
        }

        if ($request->otp != session('reset_otp')) {
            return back()->with('error', 'Invalid OTP.');
        }

        session(['otp_verified' => true]);

        return back()->with('otp_success', 'OTP verified. Now set a new password.');
    }

    public function updateForgotPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        if (!session('reset_email') || !session('otp_verified')) {
            return redirect()->route('forgot-password')->with('error', 'Unauthorized request.');
        }

        $user = User::where('email', session('reset_email'))->first();

        if (!$user) {
            return redirect()->route('forgot-password')->with('error', 'User not found.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        session()->forget([
            'reset_email',
            'reset_otp',
            'reset_otp_expires_at',
            'otp_verified',
        ]);

        return redirect()->route('login')->with('success', 'Password reset successful.');
    }
}