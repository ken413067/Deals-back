<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        // $request->validate([
            //     'email' => ['required', 'email'],
            // ]);
            
            // return $request;
            // We will send the password reset link to this user. Once we have attempted
            // to send the link, we will examine the response then see the message we
            // need to show to the user. Finally, we'll send out a proper response.
            $email = $request->email;
            // return response()->json([
                //     'aaa' => $request->only('email'),
                //     'bbb' => $email,
                // ]);
            $status = Password::sendResetLink($request->only('email'));
            // die("OK");
        // return response()->json([
        //     'message' => $status,
        // ]);
        
        if ($status === Password::RESET_LINK_SENT) {
            // 如果密碼重置發送成功
            return response()->json([
                'message' => '密碼重置發送成功',
            ]);
        } else {
            // 如果密碼重置發送失敗
            return response()->json([
                'message' => '密碼重置發送失敗',
            ]);
        }
    
        // return $status === Password::RESET_LINK_SENT
        // ? back()->with('status', __($status))
        // : back()->withErrors(['email' => __($status)]);
        // return response()->json([
        //     'message' => 'email send successfully',
        // ]);
    }
}
