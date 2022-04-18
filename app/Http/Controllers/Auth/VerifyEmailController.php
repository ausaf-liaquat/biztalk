<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\User;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(EmailVerificationRequest $request)
    {
        $user=User::find($request->route('id'));
        // dd($user);
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('status')->with('status','Your account is already verified with this link');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

       return redirect()->route('status')->with('status','Your account has been verified');
    }
}
