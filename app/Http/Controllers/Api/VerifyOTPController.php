<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Notifications\SendOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponser;

class VerifyOTPController extends Controller
{
    use ApiResponser;

    public function store(Request $request)
    {
        $request->validate([
            'otp' => 'integer|required',
        ]);
        $user = Auth::user();
        // if ($user->is_verified !== 'active') {
        //     return response()->json([
        //         'message' => 'Your account has been inactive/suspended by our admin, please contact support for further details'
        //     ], 401);
        // }
        if ($request->input('otp') === $user->otp) {
            $user->is_verified = 'active';
           
            $user->email_verified_at = \Carbon\Carbon::now();
            $user->update();

            $user->resetOTP();

            return $this->success([], 'Congrats!! Account verified', 200);
        } else {
            return $this->error('Invalid code!! please enter the correct one', 403);
           
        }

    }
    //  Resend Two Factor code
    public function resend()
    {
        
        $user = auth()->user();
        $user->generateOTP();
        $user->sendEmailVerificationNotification();
        // $user->notify(new SendOtp());

        return $this->success([], 'The verification code and link has been sent again', 200);
    }
}
