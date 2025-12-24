<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Tuteur;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $tuteur = Tuteur::where('email', $request->email)->first();

        if (!$tuteur) {
            return back()->withErrors(['email' => 'هذا البريد الإلكتروني غير مسجل'])->withInput();
        }

        // Create a token
        $token = Str::random(64);

        // Store it in password_resets table
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => Carbon::now(),
            ]
        );

        // Send reset link by email
        Mail::send('emails.reset-password', ['token' => $token], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('إعادة تعيين كلمة المرور');
        });

        return back()->with('success', 'تم إرسال رابط إعادة التعيين إلى بريدك الإلكتروني');
    }
}
