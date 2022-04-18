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
    });

});
