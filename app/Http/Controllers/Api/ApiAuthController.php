<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoCollection;
use App\Models\Comment;
use App\Models\Hashtag;
use App\Models\OtpPhone;
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
use Laravel\Socialite\Facades\Socialite;
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
            'dob' => 'nullable',
            'gender' => 'nullable',

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
            'dob' => $attr['dob'],
            'gender' => $attr['gender'],
            'isaccount_public' => 1,
            'total_followings' => 0,
            'total_followers' => 0,
            'total_likes' => 0,
            'profile_image' => 'face1.jpg',

        ]);
        if (!empty($request->get('phone_no'))) {
            $user->is_verified = 'active';
            $user->save();

        }

        $url = asset('uploads/avtars/' . $user->profile_image);
        $token = $user->createToken('APIToken');
        $accessToken = $token->plainTextToken;
        $tokenid = $token->accessToken->id;
        DB::table('personal_access_tokens')
            ->where('id', $tokenid)->update(['mac_id' => $request->get('mac_id')]);

        if (!empty($request->get('email'))) {
            //OTP
            $user->generateOTP();

            $user->notify(new SendOtp());
        }

        $auth_token = explode('|', $accessToken)[1];
        return $this->success([
            'token' => $auth_token, 'profile_image' => $url,
        ], 'Registration Successfull', 200);
    }
    public function login(Request $request)
    {
        if (!empty($request->get('phone_no'))) {
            $attributes = $request->validate([
                'phone_no' => 'nullable',
                'password' => 'required',
            ]);
        } elseif (!empty($request->get('email'))) {
            $attributes = $request->validate([

                'email' => 'nullable',
                'password' => 'required',
            ]);
        } elseif (!empty($request->get('username'))) {
            $attributes = $request->validate([

                'username' => 'nullable',
                'password' => 'required',
            ]);
        }

        if (!Auth::attempt($attributes)) {
            return $this->error('Credentials not match', 401);
        }

        $user = Auth::user();

        $token = $user->createToken('APIToken');
        $accessToken = $token->plainTextToken;
        $tokenid = $token->accessToken->id;
        DB::table('personal_access_tokens')
            ->where('id', $tokenid)->update(['mac_id' => $request->get('mac_id')]);

        $auth_token = explode('|', $accessToken)[1];
        return $this->success([
            'token' => $auth_token,
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

            $user_videos = array();
            foreach (Auth::user()->videos as $video) {
                $user_videos[] = $video->id;
            }
            $like = DB::table('likeable_likes')->where('likeable_type', 'App\Models\Video')->whereIn('likeable_id', $user_videos)->count();

            $user[] = [
                'user_id' => Auth::user()->id,
                'first_name' => Auth::user()->first_name,
                'last_name' => Auth::user()->last_name,
                'username' => Auth::user()->username,
                'email' => Auth::user()->email,
                'phone_no' => Auth::user()->phone_no,
                'location' => Auth::user()->location,
                'country' => Auth::user()->country,
                'dob' => Auth::user()->dob,
                'gender' => Auth::user()->gender,
                'is_verified' => Auth::user()->is_verified,
                'total_like_received'=>$like,
                'profile_image' => asset('uploads/avtars/' . Auth::user()->profile_image),
            ];

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

                    $checkhashtag = Hashtag::pluck('name')->toArray();

                    if ($request->get('video_description') != null) {

                        $hashtag = Helper::hashtags($request->get('video_description'));
                        if (count($hashtag) > 0) {

                            $video->hashtags = implode(" ", $hashtag);
                            $video->save();

                            $tags = array_diff($hashtag, $checkhashtag);
                            if (count($tags) > 0) {
                                $tagsid = array();
                                foreach ($tags as $key) {
                                    $tagsid[] = Hashtag::create([
                                        'name' => $key,
                                    ])->id;
                                }
                            }
                            $ids = Hashtag::whereIn('name', $hashtag)->pluck('id')->toArray();
                            $gettags = Hashtag::find($ids);
                            $video->hashtags()->attach($gettags);
                        }

                    }

                }
            } catch (Exception $e) {
                return $this->error($e->getMessage(), 500);
            }
            // Hashtag::with('videos')->get();
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
            // $videos = Video::latest()->get();
            // $v_url = array();
            // foreach ($videos as $i) {

            //     $v_url[] = [
            //         'video_id' => $i->id,
            //         'title' => $i->video_title,
            //         'description' => $i->video_description,
            //         'investment_req' => $i->investment_req,
            //         'allow_comment' => $i->allow_comment,
            //         'user_id' => $i->users->id,
            //         'username' => $i->users->username,
            //         'user_name' => $i->users->first_name . ' ' . $i->users->last_name,
            //         'total_comments' => $i->allcomments->count(),
            //         'urls' => asset('uploads/videos/' . $i->video_name),
            //         'video_comments' => $i->comments,
            //     ];

            //     $users = array();
            //     foreach ($i->allcomments as $comment) {
            //         $comment->user->username;
            //         $user[] = ['username' => $comment->user->username,
            //             'profile_image' => $comment->user->profile_image,
            //         ];
            //     }
            //     $replies = array();
            //     foreach ($i->comments as $comment) {
            //         $replies[] = $comment->replies;
            //         $userreply = array();
            //         foreach ($comment->replies as $reply) {
            //             $nestreplies = array();
            //             foreach ($reply->childrenReplies as $key) {
            //                $nestreplies[]=$key;
            //             }
            //             $userreply[] = ['username' => $reply->username, 'profile_image' => $reply->profile_image];
            //         }
            //     }
            // }

            // return $this->success([$v_url], 'Videos list', 200);
            $videos = Video::with('comments')->latest()->get();

            return $this->success(new VideoCollection($videos), 'Videos list', 200);
            // return new  VideoCollection(Video::all());
        } else {
            return $this->fail("UnAuthorized", 500);
        }
    }
    public function video_comment($id)
    {
        $video = Video::find($id);
        if ($video != null) {
            $video->whereHas('comments', function ($query) {
                $query->where('user_id', Auth::user()->id);
            });
            $replies = array();
            foreach ($video->comments as $comment) {
                $replies[] = $comment->replies;
            }
            return $this->success(['video_comments' => $video->comments, $replies], 'video with comment');
        } else {
            return $this->error('No video found', 404);
        }

    }
    /**
     * Redirect the user to the Provider authentication page.
     *
     * @param $provider
     * @return JsonResponse
     */
    public function redirectToProvider(Request $request, $provider)
    {
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) {
            return $validated;
        }

        return Socialite::driver($provider)->with(['state' => 'event_slug=' . $request->get('mac_id')])->stateless()->redirect();
    }
    /**
     * Obtain the user information from Provider.
     *
     * @param $provider
     * @return JsonResponse
     */
    public function handleProviderCallback(Request $request, $provider)
    {
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) {
            return $validated;
        }
        try {
            $user = Socialite::driver($provider)->stateless()->user();
        } catch (ClientException $exception) {

            return $this->error("Invalid credentials provided.", 422, []);
        }

        $name = explode(" ", $user->getName());
        $first_name = $name[0];
        $last_name = $name[1];
        $userCreated = User::firstOrCreate(
            [
                'email' => $user->getEmail(),
            ],
            [
                'email_verified_at' => now(),
                'first_name' => $first_name,
                'last_name' => $last_name,
                'is_verified' => 'active',
            ]
        );
        $userCreated->providers()->updateOrCreate(
            [
                'provider' => $provider,
                'provider_id' => $user->getId(),
            ],
            [
                'avatar' => $user->getAvatar(),
            ]
        );

        $state = $request->input('state');
        parse_str($state, $result);

        $token = $userCreated->createToken('APIToken');
        $accessToken = $token->plainTextToken;
        $tokenid = $token->accessToken->id;
        DB::table('personal_access_tokens')
            ->where('id', $tokenid)->update(['mac_id' => $result['event_slug']]);
        $auth_token = explode('|', $accessToken)[1];
        return $this->success([
            'token' => $auth_token, $userCreated,
        ], 'Login Successfully', 200);
    }
    /**
     * @param $provider
     * @return JsonResponse
     */
    protected function validateProvider($provider)
    {
        if (!in_array($provider, ['facebook', 'google'])) {
            return $this->error("Please login using facebook, or google.", 422, []);

        }
    }
    public function video_like(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id'))) {
            $video = Video::find($request->get('id'));

            if ($video->liked(Auth::user()->id)) {
                return $this->success([], 'Video already liked', 200);
            } else {
                $video->like(Auth::user()->id);

                return $this->success([], 'video liked', 200);
            }
        } else {
            return $this->fail("UnAuthorized", 500);
        }

    }
    public function comment_like($id)
    {
        $comment = Comment::find($id);

        if ($comment->liked(Auth::user()->id)) {
            return $this->success([$comment->liked(Auth::user()->id)], 'Comment already liked', 200);
        } else {
            $comment->like(Auth::user()->id);
            return $this->success([], 'comment liked', 200);
        }
    }
    public function otpPhone(Request $request)
    {

        $exist = OtpPhone::where('phone', $request->get('phone_no'))->first();
        $macexist = OtpPhone::where('mac_id', $request->get('mac_id'))->first();
        if (empty($exist) && empty($macexist)) {
            $new_otp = OtpPhone::create([
                'code' => random_int(100000, 999999),
                'mac_id' => $request->get('mac_id'),
                'phone' => $request->get('phone_no'),
            ]);
            // Twilio Package for sending Activation Code

            $account_sid = config('services.twilio.sid');
            $auth_token = config('services.twilio.token');
            $twilio_number = config('services.twilio.number');
            $client = new Client($account_sid, $auth_token);
            $client->messages->create($new_otp->phone, ['from' => $twilio_number, 'body' => 'Your Verification code is ' . $new_otp->code]);
            return $this->success([], 'OTP sent', 200);

        } elseif (empty($exist) && !empty($macexist)) {
            $start_date = new \DateTime($macexist->updated_at);
            $since_start = $start_date->diff(new \DateTime(now()));
            if ($since_start->i > 1) {
                $new_otp1 = OtpPhone::create([
                    'code' => random_int(100000, 999999),
                    'mac_id' => $request->get('mac_id'),
                    'phone' => $request->get('phone_no'),
                ]);
                $account_sid = config('services.twilio.sid');
                $auth_token = config('services.twilio.token');
                $twilio_number = config('services.twilio.number');
                $client = new Client($account_sid, $auth_token);
                $client->messages->create($new_otp1->phone, ['from' => $twilio_number, 'body' => 'Your Verification code is ' . $new_otp1->code]);
                return $this->success([], 'New Generated OTP Sent', 200);
            } else {
                return $this->error('Please wait for 60 seconds before you try again', 500, []);
            }
        } elseif (!empty($exist) && empty($macexist)) {
            $start_date = new \DateTime($exist->updated_at);
            $since_start = $start_date->diff(new \DateTime(now()));
            if ($since_start->i > 1) {
                $phoneexist = OtpPhone::where('phone', $request->get('phone_no'))->first();
                $phoneexist->update([
                    'code' => random_int(100000, 999999),
                    'mac_id' => $request->get('mac_id'),
                ]);
                $account_sid = config('services.twilio.sid');
                $auth_token = config('services.twilio.token');
                $twilio_number = config('services.twilio.number');
                $client = new Client($account_sid, $auth_token);
                $client->messages->create($phoneexist->phone, ['from' => $twilio_number, 'body' => 'Your Verification code is ' . $phoneexist->code]);
                return $this->success([], 'New Generated OTP Sent', 200);
            } else {
                return $this->error('Please wait for 60 seconds before you try again', 500, []);
            }
        } elseif (!empty($exist) && !empty($macexist)) {
            $start_date = new \DateTime($exist->updated_at);
            $since_start = $start_date->diff(new \DateTime(now()));
            if ($since_start->i > 1) {
                $phoneexist1 = OtpPhone::where('phone', $request->get('phone_no'))->first();
                $phoneexist1->update([
                    'code' => random_int(100000, 999999),

                ]);
                $account_sid = config('services.twilio.sid');
                $auth_token = config('services.twilio.token');
                $twilio_number = config('services.twilio.number');
                $client = new Client($account_sid, $auth_token);
                $client->messages->create($phoneexist1->phone, ['from' => $twilio_number, 'body' => 'Your Verification code is ' . $phoneexist1->code]);
                return $this->success([], 'New Generated OTP Sent', 200);
            } else {
                return $this->error('Please wait for 60 seconds before you try again', 500, []);
            }
        }

    }
    public function VerifyotpPhone(Request $request)
    {
        $request->validate([
            'code' => 'integer|required',
            'mac_id' => 'required',
            'phone_no' => 'required',
        ]);
        $phone_otp = OtpPhone::where('phone', $request->get('phone_no'))->where('mac_id', $request->get('mac_id'))->first();
        if ($request->input('code') === $phone_otp->code) {

            return $this->success([], 'Code matched', 200);
        } else {
            return $this->error('Please enter the valid code', 500, []);
        }

    }

}
