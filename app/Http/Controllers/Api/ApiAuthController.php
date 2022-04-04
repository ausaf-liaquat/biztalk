<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\SendOtp;
use App\Traits\ApiResponser;
use Helper;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client;

class ApiAuthController extends Controller
{
    use ApiResponser;

    public function register(Request $request)
    {
        // DB::transaction(function () use (&$accessToken, $request) {

        //Validating Attributes
        $attr = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8|regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/',
            'phone_no' => 'required|unique:users|regex:/^\+[1-9]\d{1,14}$/|max:15',
            'location' => 'required',
            'country' => 'required',
            //   'mac_id' => 'required',
        ]);

        //User Registration
        $user = User::create([
            'first_name' => $attr['first_name'],
            'last_name' => $attr['last_name'],
            'username' => $attr['username'],
            'email' => $attr['email'],
            'phone_no' => $attr['phone_no'],
            'password' => Hash::make($attr['password']),
            'location' => $attr['location'],
            'country' => $attr['country'],

        ]);

        $user->save();

        $token = $user->createToken('APIToken');
        $accessToken = $token->plainTextToken;
        $tokenid = $token->accessToken->id;
        DB::table('personal_access_tokens')
            ->where('id', $tokenid)->update(['mac_id' => $request->get('mac_id')]);

        //OTP
        $user->generateOTP();

        $user->notify(new SendOtp());

        // Twilio Package for sending Activation Code
        $account_sid = config('services.twilio.sid');
        $auth_token = config('services.twilio.token');
        $twilio_number = config('services.twilio.number');
        $client = new Client($account_sid, $auth_token);
        $client->messages->create($user->phone_no, ['from' => $twilio_number, 'body' => 'Your Verification code is ' . $user->otp]);

        return $this->success([
            'token' => $accessToken,
        ], 'Registration Successfull', 200);
    }
    public function login(Request $request)
    {
        $attributes = $request->validate([
            'email' => 'required|string|email|',
            'password' => 'required',

        ]);

        if (!Auth::attempt($attributes)) {
            return $this->error('Credentials not match', 401);
        }

        $user = Auth::user();
        if ($user->is_verified !== 'active') {
            return response()->json(['status' => Helper::ApiErrorStatus(),
                'message' => 'Your account has been inactive/suspended by our admin, please contact support for further details',
            ], 401);
        }
        $token = $user->createToken('APIToken');
        $accessToken = $token->plainTextToken;
        $tokenid = $token->accessToken->id;
        DB::table('personal_access_tokens')
            ->where('id', $tokenid)->update(['mac_id' => $request->get('mac_id')]);

        return $this->success([
            'token' => $accessToken,
        ], 'Login Successfully', 200);
    }
    public function forget_password(Request $request)
    {

        $input = $request->only('email');
        $validator = Validator::make($input, [
            'email' => "required|email",
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $response = Password::sendResetLink($input);
        if ($response == Password::RESET_LINK_SENT) {
            $message = "Mail send successfully";
        } else {
            $message = "Email could not be sent to this email address";
        }
        //$message = $response == Password::RESET_LINK_SENT ? 'Mail send successfully' : GLOBAL_SOMETHING_WANTS_TO_WRONG;
        $response = ['data' => '', 'message' => $message];
        return response($response, 200);

    }
    public function newPassword(Request $request)
    {
        return $request;
    }
    public function newPasswordstore(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|confirmed|min:8|regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();
                event(new PasswordReset($user));
            }
        );
        if ($status == Password::PASSWORD_RESET) {
            return response()->json(["message" => "Password has been reset successfully", "data" => array()], 200);
        } else {
            return response()->json(["message" => ['email' => __($status)], "data" => array()], 422);
        }

    }
    public function userinfo(Request $request)
    {
        $token = $request->bearerToken();

        $pat = DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $token))->first();

        if ($request->get('mac_id') == $pat->mac_id ) {

            $user = Auth::user();
            return response()->json(["status" => Helper::ApiSuccessStatus(), "message" => "Authenticated User Information", "data" => array($user)], 200);

        } else {
            return response()->json(["status" => Helper::ApiFailedStatus(), "message" => "UnAuthorized"], 500);
        }

    }
}
