<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\HashtagCollection;
use App\Http\Resources\UserCollection;
use App\Http\Resources\VideoCollection;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Comment;
use App\Models\CommunityGuideline;
use App\Models\Contact;
use App\Models\Hashtag;
use App\Models\OtpPhone;
use App\Models\User;
use App\Models\Video;
use App\Models\VideoView;
use App\Notifications\AcceptFollowNotification;
use App\Notifications\FollowNotification;
use App\Notifications\LikeCommentNotification;
use App\Notifications\LikeNotification;
// use FFMpeg\FFMpeg;
use App\Rules\MatchOldPassword;
use App\Traits\ApiResponser;
use Helper;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use ProtoneMedia\LaravelFFMpeg\MediaOpener;
use Twilio\Rest\Client;

class ApiAuthController extends Controller
{
    use ApiResponser;

    public function loginWithoutAccount(Request $request)
    {
        $without_hash_token = '2ncXyDP9aWyluql7Y9OHJ8eaCAaWTe9QGOs96hRU';
        $pat = DB::table('personal_access_tokens')->where('token', hash('sha256', $without_hash_token))->update(['mac_id' => $request->get('mac_id')]);

        return $this->success([
            'token' => Helper::token(),
        ], 'Login without account', 200);

    }
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

            $user->sendEmailVerificationNotification();
            // $user->notify(new SendOtp());
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

        $credentials = request()->validate(['email' => 'required|email']);

        Password::sendResetLink($credentials);

        return $this->success([], 'Reset password link sent on your email id', 200);

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

        if (Helper::mac_check($token, $request->get('mac_id')) && !Helper::token_check($token)) {

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
                'bio' => Auth::user()->bio,
                'gender' => Auth::user()->gender,
                'is_verified' => Auth::user()->is_verified,
                'isaccount_public' => Auth::user()->isaccount_public,
                'total_like_received' => $like,
                'followers_count' => Auth::user()->approvedFollowers()->count(),
                'followings_count' => Auth::user()->approvedFollowings()->count(),
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
        User::whereNot('id',0)->where('username', $user_name);
        // return \Response::json(array("status" => 200, "message" => "", "data" => array([$isExists])));
        if (User::whereNot('id',0)->where('username', $user_name)->count() > 0) {
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
        if (User::whereNot('id',0)->where('email', $email)->count() > 0) {
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
        if (User::whereNot('id',0)->where('phone_no', $phone)->count() > 0) {
            return $this->error("This Phone no already exists.", 422);
        } else {
            return $this->success([], "valid phone no", 200);
        }

    }
    public function update_profileImage(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id')) && !Helper::token_check($token)) {
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

        if (Helper::mac_check($token, $request->get('mac_id')) && !Helper::token_check($token)) {
            $profile_image = asset('uploads/avtars/' . Auth::user()->profile_image);
            return $this->success([$profile_image], "Profile Image", 200);
        } else {
            return $this->fail("UnAuthorized", 500);
        }
    }
    public function post_video(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id')) && !Helper::token_check($token)) {
            try {
                if ($request->hasFile('file')) {
                    $video = Video::create([

                        'user_id' => Auth::user()->id,
                        'is_flagged' => $request->get('is_flagged'),
                        'is_approved' => $request->get('is_approved'),
                        'video_title' => $request->get('video_title'),
                        'video_description' => $request->get('video_description'),
                        'category_id' => $request->get('category_id'),
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
                    $filename = 'short_video_' . Auth::user()->id . '_' . rand(000000, 999999) . '.' . $file->getClientOriginalExtension();
                    $data = file_get_contents($file);
                    Storage::disk('public')->put('videos/' . $filename, $data);
                    $video->video_name = $filename;
                    $video->save();

                    // $thumbnail_name = 'video_thumbnail_' . $video->id . '_' . rand(000000, 999999) . '.png';

                    // (new MediaOpener)
                    //     ->open($file)
                    //     ->getFrameFromSeconds(2)
                    //     ->export()
                    //     ->accurate()
                    //     ->toDisk('public')
                    //     ->save('thumbnail/' . $thumbnail_name);

                    // $video->video_poster = $thumbnail_name;
                    // $video->save();

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

        if (Helper::mac_check($token, $request->get('mac_id')) && !Helper::token_check($token)) {
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

        if (Helper::token(Helper::token_check($token) || !Helper::token_check($token))) {
            if (Helper::mac_check($token, $request->get('mac_id'))) {

                $videos = Video::where('is_approved', 1)->where('is_flagged', 0)->where('is_active', 1)->with('comments')->latest()->get();

                return $this->success(new VideoCollection($videos), 'Videos list', 200);
                // return new  VideoCollection(Video::all());
            } else {
                return $this->fail("UnAuthorized", 500);
            }
        }

    }
    public function video_comment(Request $request)
    {
        $video = Video::find($request->get('id'));
        if ($video != null) {

            return $this->success(['video_comments' => new CommentCollection($video->comments)], 'video with comments and replies');
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

            if ($request->get('response') == 1) {
                if ($video->liked(Auth::user()->id)) {
                    return $this->success([], 'Video already liked', 200);
                } else {
                    $video->like(Auth::user()->id);
                    $user = User::find($video->users->id);
                    if (Auth::user()->id != $user->id) {
                        $user->notify(new LikeNotification(Auth::user(), $video));
                    }

                    return $this->success([], 'video liked', 200);
                }
            } elseif ($request->get('response') == 0) {

                $video->unlike(Auth::user()->id);

                return $this->success([], 'video unliked', 200);

            }

        } else {
            return $this->fail("UnAuthorized", 500);
        }

    }
    public function comment_like(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id'))) {
            $comment = Comment::find($request->get('id'));

            if ($request->get('response') == 1) {
                if ($comment->liked(Auth::user()->id)) {

                    return $this->success([], 'Comment already liked', 200);
                } else {
                    $comment->like(Auth::user()->id);

                    if (Auth::user()->id != $comment->user->id) {
                        $comment->user->notify(new LikeCommentNotification(Auth::user(), $comment));
                    }

                    return $this->success([], 'comment liked', 200);
                }

            } elseif ($request->get('response') == 0) {

                $comment->unlike(Auth::user()->id);

                return $this->success([], 'comment unliked', 200);

            }
        } else {
            return $this->fail("UnAuthorized", 500);
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
    public function discover(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id'))) {

            //$hashtag = Hashtag::where('views','>=',2)->inRandomOrder()->get();

            $hashtag = Hashtag::where('views', '>', 2)->has('videos')->get()->sortByDesc('views');
            $banner = Banner::first();

            foreach (json_decode($banner->image_name) as $item) {
                $url = asset('uploads/banners/' . $item);
                $banner_image[] = $url;
            }

            // $banner_image = implode(' ', $banner_image);

            return $this->success(['banners' => $banner_image, 'hashtags' => new HashtagCollection($hashtag)], 'Discovers api', 200);
        } else {
            return $this->fail("UnAuthorized", 500);
        }

    }
    public function video_view(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id'))) {

            $user_id = $request->get('user_id');
            $video_id = $request->get('video_id');

            $videoview = VideoView::where('user_id', $user_id)->where('video_id', $video_id)->first();
            if (empty($videoview)) {
                $view = VideoView::create([
                    'user_id' => $user_id,
                    'video_id' => $video_id,
                ]);
                $view->viewed_on = now();
                $view->increment('views', 1);
                $view->save();
                $video = Video::find($video_id);
                $hashtags_arr = explode(" ", $video->hashtags);

                Hashtag::whereIn('name', $hashtags_arr)->increment('views', 1);
                return $this->success([$hashtags_arr], 'Hashtags', 200);
            } else {
                return $this->error("already viewed", 500);
            }

        } else {
            return $this->fail("UnAuthorized", 500);
        }
    }
    public function user_videos(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id'))) {

            $user_videos = Auth::user()->videos->where('is_active', 1);
            return $this->success(new VideoCollection($user_videos), 'user videos', 200);
        } else {
            return $this->fail("UnAuthorized", 500);
        }
    }
    public function user_privatevideos(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id'))) {

            $user_videos = Auth::user()->videos->where('is_active', 0);
            return $this->success(new VideoCollection($user_videos), 'users private videos', 200);
        } else {
            return $this->fail("UnAuthorized", 500);
        }
    }
    public function user_likedvideos(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id'))) {

            $liked_videos = Video::whereLikedBy(Auth::user()->id)->with('likeCounter')->orderBy('created_at', 'DESC')->get();
            return $this->success(new VideoCollection($liked_videos), 'users liked videos', 200);
        } else {
            return $this->fail("UnAuthorized", 500);
        }
    }
    public function search(Request $request)
    {
        $token = $request->bearerToken();
        if (Helper::token(Helper::token_check($token) || !Helper::token_check($token))) {
            if (Helper::mac_check($token, $request->get('mac_id'))) {
                //   $user =  User::where('username','like','%'.$withoutspace.'%')->get();

                $search = $request->get('search');
                $names = explode(" ", $request->get('search'));

                if ($search == trim($search) && str_contains($search, ' ')) {
                    $user = User::whereNot('id',0)->where(function ($query) use ($names) {
                        // $query->whereIn('first_name',  $names );
                        // $query->orWhere(function ($query) use ($names) {
                        //     $query->whereIn('last_name',  $names );
                        // });
                        for ($i = 0; $i < count($names); $i++) {
                            $query->orwhere('first_name', 'like', '%' . $names[$i] . '%');
                            $query->orwhere('last_name', 'like', '%' . $names[$i] . '%');
                        }
                    })->get();
                } else {
                    $user = User::whereNot('id',0)->where(function ($query) use ($search) {
                        $query->where('username', 'like', '%' . $search . '%');

                    })->get();
                }
                $video = Video::where(function ($query) use ($names) {
                    foreach ($names as $k) {
                        $query->orWhere('video_title', 'like', '%' . $k . '%');
                        $query->orWhere('video_description', 'like', '%' . $k . '%');
                        $query->orWhere('hashtags', 'like', "%" . $k . "%");
                    }
                })->where('is_approved', 1)->where('is_flagged', 0)->where('is_active', 1)->with('comments')->latest()->get();
                $hashtag = Hashtag::where('name', 'like', '%' . $search . '%')->get();
                return $this->success(['users' => new UserCollection($user), 'videos' => new VideoCollection($video), 'hashtags' => new HashtagCollection($hashtag)], 'video liked', 200);
            } else {
                return $this->fail("UnAuthorized", 500);
            }
        }
    }
    public function follow(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id'))) {

            $userid = User::find($request->get('user_id'));

            $auth_user = Auth::user();

            if ($auth_user->isFollowing($userid)) {
                return $this->success([], 'you already follow this user', 500);
            } elseif ($auth_user->hasRequestedToFollow($userid)) {
                return $this->success([], 'your follow request is still in pending', 500);
            } else {
                $res = $auth_user->follow($userid);
                $userid->notify(new FollowNotification($userid));
                if ($res) {
                    return $this->success([], 'your follow request has been sent', 200);
                }
                return $this->success([], 'you are now following', 200);
            }

        } else {
            return $this->fail("UnAuthorized", 500);
        }

    }
    public function unfollow(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id'))) {
            $isfollowing = Auth::user()->isFollowing(User::find($request->get('user_id')));

            if ($isfollowing) {

                Auth::user()->unfollow(User::find($request->get('user_id')));

                return $this->success([], 'Successfully unfollowed', 200);
            } else {
                return $this->error('Something went wrong', 500);
            }

        } else {
            return $this->fail("UnAuthorized", 500);
        }
    }
    public function follow_requests(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id'))) {
            $user = Auth::user();

            $list = $user->notApprovedFollowers()->get();

            return $this->success([new UserCollection($list)], 'not approved followers requests', 200);
        } else {
            return $this->fail("UnAuthorized", 500);
        }
    }
    public function followings_requests(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id'))) {
            $user = Auth::user();

            $list = $user->notApprovedFollowings()->get();

            return $this->success([new UserCollection($list)], 'not approved followings requests', 200);
        } else {
            return $this->fail("UnAuthorized", 500);
        }
    }
    public function acceptfollow_requests(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id'))) {
            $user = Auth::user();
            $user_id = User::find($request->get('user_id'));
            // $user->acceptFollowRequestFrom($user_id);
            $hasRequest = $user_id->hasRequestedToFollow($user);

            if ($hasRequest) {
                $user->acceptFollowRequestFrom($user_id);
                $user_id->notify(new AcceptFollowNotification($user));
                return $this->success([], 'Follow request accepted', 200);
            } else {
                return $this->error('no request found', 404);
            }

        } else {
            return $this->fail("UnAuthorized", 500);
        }
    }
    public function rejectfollow_requests(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id'))) {
            $user = Auth::user();
            $user_id = User::find($request->get('user_id'));
            // $user->acceptFollowRequestFrom($user_id);
            $hasRequest = $user_id->hasRequestedToFollow($user);

            if ($hasRequest) {
                $user->rejectFollowRequestFrom($user_id);

                return $this->success([], 'Follow request rejected', 200);
            } else {
                return $this->error('no request found', 404);
            }

        } else {
            return $this->fail("UnAuthorized", 500);
        }
    }
    public function user_followers(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id'))) {
            $user = User::find($request->get('user_id'));
            if($user != null){
                $userList = $user->approvedFollowers()->get();
            return $this->success(['followers_list' => new UserCollection($userList)], 'Followers', 200);
            }else{
                return $this->error("no user found", 404);
            }
            

        } else {
            return $this->fail("UnAuthorized", 500);
        }

    }
    public function user_followings(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id'))) {
            $user = User::find($request->get('user_id'));
            if($user != null){
            $userList = $user->approvedFollowings()->get();

            return $this->success(['followings_list' => new UserCollection($userList)], 'Followers', 200);
            
            }else{
                return $this->error("no user found", 404);
            }

        } else {
            return $this->fail("UnAuthorized", 500);
        }

    }
    public function user_followings_video_list(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id'))) {
            $user = Auth::user();
            $userList = $user->approvedFollowings()->get();
            $user_id = array();
            foreach ($userList as $item) {
                $user_id[] = $item->id;
            }
            $videos = Video::whereIn('user_id', $user_id)->where('is_approved', 1)->where('is_flagged', 0)->where('is_active', 1)->with('comments')->latest()->get();
            return $this->success(new VideoCollection($videos), 'Followings videos', 200);
        } else {
            return $this->fail("UnAuthorized", 500);
        }
    }
    public function video_userdetails(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id'))) {

            $user_id = $request->get('user_id');

            $userdetails = User::find($user_id);
            $user_videos = array();
            foreach ($userdetails->videos as $video) {
                $user_videos[] = $video->id;
            }
            $like = DB::table('likeable_likes')->where('likeable_type', 'App\Models\Video')->whereIn('likeable_id', $user_videos)->count();
            $user[] = [
                'user_id' => $userdetails->id,
                'first_name' => $userdetails->first_name,
                'last_name' => $userdetails->last_name,
                'username' => $userdetails->username,
                'email' => $userdetails->email,
                'phone_no' => $userdetails->phone_no,
                'location' => $userdetails->location,
                'country' => $userdetails->country,
                'dob' => $userdetails->dob,
                'bio' => $userdetails->bio,
                'gender' => $userdetails->gender,
                'is_verified' => $userdetails->is_verified,
                'isaccount_public' => $userdetails->isaccount_public,
                'total_like_received' => $like,
                'is_following' => Auth::user()->isFollowing(User::find($user_id)),
                'followers_count' => $userdetails->approvedFollowers()->count(),
                'followings_count' => $userdetails->approvedFollowings()->count(),
                'profile_image' => asset('uploads/avtars/' . $userdetails->profile_image),
            ];
            $user_videos_list = $userdetails->videos->where('is_approved', 1)->where('is_flagged', 0)->where('is_active', 1);

            $userliked_videos = Video::whereLikedBy($userdetails->id)->with('likeCounter')->orderBy('created_at', 'DESC')->get();

            return $this->success(['user_details' => $user, 'user_videos' => new VideoCollection($user_videos_list), 'user_liked_videos' => new VideoCollection($userliked_videos)], 'user detail', 200);

        } else {
            return $this->fail("UnAuthorized", 500);
        }

    }
    public function hashtag_search(Request $request)
    {
        $token = $request->bearerToken();
        $names = explode(" ", $request->get('hashtag_name'));
        if (Helper::mac_check($token, $request->get('mac_id'))) {

            $hashtags_videos = Hashtag::where(function ($query) use ($names) {
                foreach ($names as $k) {
                    $query->orWhere('name', 'like', "%" . $k . "%");
                }
            })->get();
            return $this->success(['hashtags' => new HashtagCollection($hashtags_videos)], 200);

        } else {
            return $this->fail("UnAuthorized", 500);
        }

    }
    public function category_list(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id'))) {

            $categories_list = Category::orderby('price_range', 'asc')->latest()->get();
            return $this->success(['category_list' => new CategoryCollection($categories_list)], 200);

        } else {
            return $this->fail("UnAuthorized", 500);
        }

    }
    public function notificationsCount(Request $request)
    {

        try {

            $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function () use ($request) {

                while (true) {
                    $notif_count = Auth::user()->unreadNotifications->count();

                    echo json_encode(['data' => $notif_count]) . "\n\n";
                    if (ob_get_level() > 0) {ob_flush();}
                    flush();
                    usleep(200000);
                }

            });
            $response->headers->set('Content-Type', 'text/event-stream');
            $response->headers->set('X-Accel-Buffering', 'no');
            $response->headers->set('Cach-Control', 'no-cache');
            return $response->send();
        } catch (\Exception$e) {
            return response()->json(['error' => $e->getMessage()]);
        }

        // $unreadNotification_count = Auth::user()->unreadNotifications->count();

        // return response()->json(['status' => 'Success', 'message' => 'Unread Notifications', 'data' => array(Auth::user()->unreadNotifications, $unreadNotification_count)], 200);

    }
    public function markAsReadOne($id)
    {
        $userUnreadNotification = auth()->user()->unreadNotifications->where('id', $id)->first();

        if ($userUnreadNotification) {
            $userUnreadNotification->markAsRead();
        }
        return $this->success([], 'Notification mark as read', 200);
    }
    public function markAsRead()
    {

        Auth::user()->notifications->markAsRead();

        return $this->success([], 'All notifications mark as read', 200);

    }
    public function notificationsList()
    {

        $all_notifications = Auth::user()->notifications;
        $notification_count = Auth::user()->notifications->count();

        return $this->success(['notification_count' => $notification_count, 'all_notifications' => $all_notifications], 'All Notifications List', 200);

    }
    public function unreadNotificationsList()
    {
        $unread_notifications = Auth::user()->unreadNotifications;
        $notif_count = Auth::user()->unreadNotifications->count();

        return $this->success(['unread_notification_count' => $notif_count, 'unread_notifications' => $unread_notifications], 'Unread Notifications List', 200);

    }
    public function communityGuidelines(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id'))) {
            $cg = CommunityGuideline::first();

            return $this->success(['community_guidelines' => $cg], 'community guidelines', 200);
        } else {
            return $this->fail("UnAuthorized", 500);
        }
    }
    public function update_personalDetail(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id'))) {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'username' => 'required|regex:/(^([a-zA-Z]+)(\d+)?$)/u',
                'bio' => 'string|max:255',
            ]);
            $user = User::findOrFail(Auth::user()->id);
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            if($request->get('username') != null){
            $user->username = $request->username;
            }
            $user->bio = $request->bio;
            $user->update();

            $base64_image = $request->profile_image;
            if (!empty($base64_image)) {
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

                }
            }

            return $this->success([], 'Profile updated', 200);
        } else {
            return $this->fail("UnAuthorized", 500);
        }
    }
    public function update_management(Request $request)
    {

        $user = User::find(auth()->user()->id);
        if (!empty($request->current_password)) {
            $request->validate([
                'current_password' => ['required', new MatchOldPassword],
                'password' => 'required|string|confirmed|min:8|different:current_password|regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/',
            ]);
            $user->password = Hash::make($request->password);
        }
        if (!empty($request->get('email'))) {
            $request->validate([
                'email' => 'nullable|string|email|max:255|unique:users',
            ]);
            $user->email = $request->get('email');
        }
        if (!empty($request->get('phone_no'))) {
            $request->validate([
                'phone_no' => 'nullable|unique:users|regex:/^\+[1-9]\d{1,14}$/|max:15',
            ]);

            $user->phone_no = $request->get('phone_no');
        }
        $user->update();

        return $this->success([], 'User management updated', 200);
    }
    public function suggestion(Request $request)
    {
        $token = $request->bearerToken();

        if (Helper::mac_check($token, $request->get('mac_id'))) {
            $userList = Auth::user()->approvedFollowers()->get();
            $followers_id = array();
            foreach ($userList as $key => $item) {
                $followers_id[] = $item->id;
            }
            $userfollowingsList = Auth::user()->approvedFollowings()->get();
            $followings_id = array();
            foreach ($userfollowingsList as $key => $list) {
                $followings_id[] = $list->id;
            }

            $notApprovedFollower = Auth::user()->notApprovedFollowers()->get();
            $notApprovedFollower_id = array();
            foreach ($notApprovedFollower as $key => $list) {
                $notApprovedFollower_id[] = $list->id;
            }

            $notApprovedFollowing = Auth::user()->notApprovedFollowings()->get();
            $notApprovedFollowing_id = array();
            foreach ($notApprovedFollowing as $key => $list) {
                $notApprovedFollowing_id[] = $list->id;
            }
            $suggestions = User::whereNot('id',0)->doesntHave('roles')
                ->whereNotIn('id', $followers_id)
                ->whereNotIn('id', $followings_id)
                ->whereNotIn('id', $notApprovedFollower_id)
                ->whereNotIn('id', $notApprovedFollowing_id)
                ->whereNot('id', Auth::user()->id)
                ->where('isaccount_public', 1)
                ->where('is_verified', 'active')->get();

            return $this->success([new UserCollection($suggestions)], "account suggestions", 200);
        } else {
            return $this->fail("UnAuthorized", 500);
        }
    }
    public function generateContact(Request $request)
    {
        $request->validate([
            'title' => ['required'],
            'message' => ['required'],
        ]);
        $contact = Contact::create([
            'title' => $request->title,
            'message' => $request->message,
            'user_id' => Auth::user()->id,
            'status' => 'open',
        ]);
        return $this->success([], "Message submitted Successfully", 200);

    }
}
