<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Video;
use App\Notifications\SendOtp;
use App\Traits\ApiResponser;
use Helper;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
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
            'email' => 'nullable|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8|regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/',
            'phone_no' => 'nullable|unique:users|regex:/^\+[1-9]\d{1,14}$/|max:15',
            'location' => 'required',
            'country' => 'required',

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
        // if ($user->is_verified !== 'active') {
        //     return $this->error([
        //     ], 'Your account has been inactive/suspended by our admin, please contact support for further details', 401);
        // }
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
            return response(['status' => Helper::ApiErrorStatus(), 'errors' => $validator->errors()->all()], 422);
        }
        $response = Password::sendResetLink($input);
        if ($response == Password::RESET_LINK_SENT) {
            $message = "Mail send successfully";
        } else {
            $message = "Email could not be sent to this email address";
        }
        //$message = $response == Password::RESET_LINK_SENT ? 'Mail send successfully' : GLOBAL_SOMETHING_WANTS_TO_WRONG;
        $response = ["status" => Helper::ApiSuccessStatus(), 'data' => '', 'message' => $message];
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

        if (Helper::mac_check($token, $request->get('mac_id'))) {

            $user = Auth::user();
            return $this->success([$user], "Authenticated User Information", 200);

        } else {
            return $this->fail("UnAuthorized", 500);
        }

    }
    public function logout()
    {
        Auth::user()->tokens()->delete();

        return $this->success([], "Token Revoked", 200);
    }
    public function usernameValidation(Request $request)
    {

        $request->validate([
            'username' => 'required|string|max:255',
        ]);
        $user_name = $request->get('username');
        User::where('username', $user_name);
        // return \Response::json(array("status" => 200, "message" => "", "data" => array([$isExists])));
        if (User::where('username', $user_name)->count() > 0) {
            return $this->error("This Username already exists.", 422, [$user_name]);
        } else {
            return $this->success([$user_name], "valid username", 200);
        }

    }
    public function emailValidation(Request $request)
    {

        $request->validate([
            'email' => 'required|string|max:255',
        ]);
        $email = $request->get('email');
        User::where('email', $email);
        // return \Response::json(array("status" => 200, "message" => "", "data" => array([$isExists])));
        if (User::where('email', $email)->count() > 0) {
            return $this->error("This Email already exists.", 422);
        } else {
            return $this->success([], "valid email", 200);
        }

    }
    public function phoneValidation(Request $request)
    {

        $request->validate([
            'phone' => 'required|regex:/^\+[1-9]\d{1,14}$/|max:15',
        ]);
        $phone = $request->get('phone');
        User::where('phone_no', $phone);
        // return \Response::json(array("status" => 200, "message" => "", "data" => array([$isExists])));
        if (User::where('phone_no', $phone)->count() > 0) {
            return $this->error("This Phone no already exists.", 422);
        } else {
            return $this->success([], "valid phone no", 200);
        }

    }
    public function update_profileImage(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id'))) {
            try {

                $base64_image = $request->profile_image;
                if (preg_match('/^data:image\/(\w+);base64,/', $base64_image)) {

                    // $folderpath = "uploads/avtars/";

                    $data = substr($base64_image, strpos($base64_image, ',') + 1);

                    $imageName = $request->user()->id . '_' . strtolower($request->user()->username) . '_' . date('d-m-Y-H-i') . '.' . 'jpg';
                    $data = substr($base64_image, strpos($base64_image, ',') + 1);
                    $data = base64_decode($data);
                    Storage::disk('public')->put('avtars/' . $imageName, $data);

                    $id = Auth()->user()->id;
                    $User = User::find($id);
                    $User->profile_image = $imageName;
                    $User->save();

                    return $this->success([], "Profile updated", 200);
                }

            } catch (Exception $e) {
                return $this->fail($e->getMessage(), 500);
            }
        } else {
            return $this->fail("UnAuthorized", 500);
        }
    }
    public function profile_img_url(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id'))) {
            $profile_image = asset('uploads/avtars/' . Auth::user()->profile_image);
            return $this->success([$profile_image], "Profile Image", 200);
        } else {
            return $this->fail("UnAuthorized", 500);
        }
    }
    public function post_video(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id'))) {
            try {
                if ($request->hasFile('file')) {
                    $video = Video::create([

                        'user_id' => Auth::user()->id,
                        'is_flagged' => $request->get('is_flagged'),
                        'is_approved' => $request->get('is_approved'),
                        'video_title' => $request->get('video_title'),
                        'video_description' => $request->get('video_description'),
                        'hashtags' => $request->get('hashtags'),
                        'video_category' => $request->get('video_category'),
                        'investment_req' => $request->get('investment_req'),
                        'allow_comment' => $request->get('allow_comment'),
                        'is_active' => $request->get('is_active'),
                        'privacy' => $request->get('privacy'),
                        'location' => $request->get('location'),
                        'total_comments' => $request->get('total_comments'),
                        'total_shares' => $request->get('total_shares'),
                        'total_likes' => $request->get('total_likes'),

                    ]);
                    $file = $request->file('file');
                    $filename = Auth::user()->id . '_' . rand(000, 999) . '_' . $file->getClientOriginalName();
                    $data = file_get_contents($file);
                    Storage::disk('public')->put('videos/' . $filename, $data);
                    $video->video_name = $filename;
                    $video->save();
                }
            } catch (Exception $e) {
                return $this->error($e->getMessage(), 500);
            }

            return $this->success([], 'video uploaded', 200);
        } else {
            return $this->fail("UnAuthorized", 500);
        }
    }
    public function video_url(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id'))) {
            $videos = Video::where('user_id', Auth::user()->id)->get();
            $v_url = array();
            foreach ($videos as $i) {
                $v_url[] = asset('uploads/videos/' . $i->video_name);
            }

            return $this->success([$v_url], "Videos url", 200);
        } else {
            return $this->fail("UnAuthorized", 500);
        }
    }
    public function videos_list(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id'))) {

            $videos = Video::latest()->get();
            $v_url = array();
            foreach ($videos as $i) {
                $v_url[] = asset('uploads/videos/' . $i->video_name);
            }

            return $this->success([$v_url], 'Videos list', 200);
        } else {
            return $this->fail("UnAuthorized", 500);
        }
    }
}
