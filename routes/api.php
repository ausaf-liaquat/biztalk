<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\CommentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::group(['prefix' => 'v1', 'namespace' => 'API'], function () {
    // Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    //     return $request->user();
    // });
    Route::post('/auth/register', [ApiAuthController::class, 'register']);
    Route::post('/auth/login', [ApiAuthController::class, 'login']);
    Route::post('/auth/logout', [ApiAuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/verify/otp', [App\Http\Controllers\Api\VerifyOTPController::class, 'store'])->middleware('auth:sanctum');
    Route::get('/code/resend', [App\Http\Controllers\Api\VerifyOTPController::class, 'resend'])->middleware('auth:sanctum');

    Route::post('/auth/forget-password', [ApiAuthController::class, 'forget_password']);
    Route::get('/reset-password/{token}', [ApiAuthController::class, 'newPassword'])->name('password.reset');
    Route::post('/reset-password', [ApiAuthController::class, 'newPasswordstore'])->name('password.update');
    Route::post('/auth/username', [ApiAuthController::class, 'usernameValidation'])->name('check.username');
    Route::post('/auth/email', [ApiAuthController::class, 'emailValidation'])->name('check.email');
    Route::post('/auth/phone', [ApiAuthController::class, 'phoneValidation'])->name('check.phone');

    Route::post('/send/otp/phone', [ApiAuthController::class, 'otpPhone']);
    Route::post('/verify/otp/phone', [ApiAuthController::class, 'VerifyotpPhone']);
    // Route::get('auth/login/facebook', [ApiAuthController::class, 'redirectToFacebook']);
    // Route::get('login/facebook/callback', [ApiAuthController::class, 'handleFacebookCallback']);

    Route::get('/login/{provider}', [ApiAuthController::class, 'redirectToProvider']);
    Route::get('/login/{provider}/callback', [ApiAuthController::class, 'handleProviderCallback']);

    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::post('/user', [ApiAuthController::class, 'userinfo']);
        Route::post('/videos/list', [ApiAuthController::class, 'videos_list']);
    });
    Route::post('/login/without/account', [ApiAuthController::class, 'loginWithoutAccount']);
    Route::group(['middleware' => ['auth:sanctum', 'apiverified']], function () {

        Route::post('update/profile-image', [ApiAuthController::class, 'update_profileImage']);
        Route::get('/profile/img', [ApiAuthController::class, 'profile_img_url']);

        Route::post('/post/video', [ApiAuthController::class, 'post_video']);
        Route::get('/video/url', [ApiAuthController::class, 'video_url']);

        Route::post('/comment/store', [CommentController::class, 'store']);
        Route::post('/reply/store', [CommentController::class, 'replyStore']);
        Route::post('/video/comment', [ApiAuthController::class, 'video_comment']);
        Route::post('/video/like', [ApiAuthController::class, 'video_like']);
        Route::post('/comment/like', [ApiAuthController::class, 'comment_like']);
        Route::post('/video/view', [ApiAuthController::class, 'video_view']);

        Route::post('/discover', [ApiAuthController::class, 'discover']);

        Route::post('/search', [ApiAuthController::class, 'search']);
        Route::post('/my/videos', [ApiAuthController::class, 'user_videos']);
        Route::post('/my/private/videos', [ApiAuthController::class, 'user_privatevideos']);
        Route::post('/my/liked/videos', [ApiAuthController::class, 'user_likedvideos']);

        Route::post('/follow', [ApiAuthController::class, 'follow']);
        Route::post('/follower/requests/list', [ApiAuthController::class, 'follow_requests']);
        Route::post('/following/requests/list', [ApiAuthController::class, 'followings_requests']);
        Route::post('/accept/follow/request', [ApiAuthController::class, 'acceptfollow_requests']);
        Route::post('/reject/follow/request', [ApiAuthController::class, 'rejectfollow_requests']);
        Route::post('/user/followers', [ApiAuthController::class, 'user_followers']);
        Route::post('/user/followings', [ApiAuthController::class, 'user_followings']);

        Route::post('/user/followings/videos/list', [ApiAuthController::class, 'user_followings_video_list']);

        Route::post('/video/user/details', [ApiAuthController::class, 'video_userdetails']);
        Route::post('/hashtag/search', [ApiAuthController::class, 'hashtag_search']);
        Route::post('/category/list', [ApiAuthController::class, 'category_list']);

        Route::post('/unfollow', [ApiAuthController::class, 'unfollow']);

        //Notification
        Route::get('unread/notifications/count', [ApiAuthController::class, 'notificationsCount']);
        Route::get('mark/as-read/all-notifications', [ApiAuthController::class, 'markAsRead']);
        Route::get('mark/as-read/{id}/notification', [ApiAuthController::class, 'markAsReadOne']);
        Route::get('all/notifications/list', [ApiAuthController::class, 'notificationsList']);
        Route::get('unread/notifications/list', [ApiAuthController::class, 'unreadNotificationsList']);

        Route::post('community/guidelines', [ApiAuthController::class, 'communityGuidelines']);

        Route::post('user/edit', [ApiAuthController::class, 'update_personalDetail']);

        Route::post('user/management', [ApiAuthController::class, 'update_management']);

        Route::post('suggestion', [ApiAuthController::class, 'suggestion']);

        //Contact
        Route::post('generate/contact', [ApiAuthController::class, 'generateContact']);

        

    });

});
