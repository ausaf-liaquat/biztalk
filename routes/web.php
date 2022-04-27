<?php

use App\Http\Controllers\Backend\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/cache', function () {
    $exitCode = Artisan::call('config:cache');
    return "ok";
});
Route::get('/', function () {

    //     $iv_real = "ahc/2u6F0Yvww12fyQiZWA==";
    // // $decoded_iv = base64_decode($iv_real);

    //     $iv_hex         = bin2hex($iv_real);
    // $plaintext_shared_secret = "9b8a3e600073de05e5d095b5d909043e50f5047ffcd0048c01c65ca690b7b4e981e51b59641d4ffd5a140c27f25a761ab0f99e601b59c5ae3427c751bfae9331";
    // echo "Shared secret: {$plaintext_shared_secret}\r\n";
    // $ciphertext_hex = bin2hex($plaintext_shared_secret);
    // $aes_key = hash("sha256", "s@keypact.appa62f1bed41166b2c455d82337222723b0287d920");
    // $encrypted_shared_secret = openssl_encrypt(
    //     $ciphertext_hex,
    //     "aes-256-cbc",
    //     $aes_key,
    //     OPENSSL_RAW_DATA,
    //     $iv_hex //Binary data
    // );
    // //Base64 encoded, encryped shared secret
    // echo "\r\nEncrypted, base64_encoded, shared secret\r\n";
    // var_dump($encrypted_shared_secret);

    //     // $password = 'lbwyBzfgzUIvXZFShJuikaWvLJhIVq36';
    //     $password = "9564d4c7d28ebf750410e1982f561329fdc55ae2963018d4c7fb3f7e900726a8";

    //  $AES_METHOD = 'aes-256-cbc';
    //  $message = "biztalk";
    //     if (OPENSSL_VERSION_NUMBER <= 268443727) {
    //         throw new RuntimeException('OpenSSL Version too old, vulnerability to Heartbleed');
    //     }

    //     $iv_size        = openssl_cipher_iv_length($AES_METHOD);
    //     $iv             = openssl_random_pseudo_bytes($iv_size);
    //     $ciphertext     = openssl_encrypt($message, $AES_METHOD, $password, OPENSSL_RAW_DATA, $iv);
    //     $ciphertext_hex = bin2hex($ciphertext);
    //     $iv_hex         = bin2hex($iv);
    //     dd("$iv_hex:$ciphertext_hex");

    //     //Decrypt

    // // $ciphered = "$iv:$ciphertext";
    // //     $iv_size    = openssl_cipher_iv_length($AES_METHOD);
    // //     $data       = explode(":", $ciphered);
    // //     // $iv         = hex2bin($data[0]);
    // //     // $ciphertext = hex2bin($data[1]);
    // //     $iv         = $data[0];
    // //     $ciphertext = $data[1];
    // //      dd(openssl_decrypt($ciphertext, $AES_METHOD, $password, OPENSSL_RAW_DATA, $iv));
    return view('welcome');
});
Route::get('/status', function () {
    return view('Backend.pages.status-display');
})->name('status');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/user', [DashboardController::class, 'userindex'])->name('user.index');
    Route::get('/user/data', [DashboardController::class, 'userdata'])->name('user.data');
    Route::get('/user/edit/{id}', [DashboardController::class, 'useredit'])->name('user.edit');
    Route::post('/user/update', [DashboardController::class, 'userupdate'])->name('user.update');
    Route::post('/user/status', [DashboardController::class, 'userstatus'])->name('user.status');
    Route::post('/user/check/email', [DashboardController::class, 'useremail'])->name('user.checkemail');
    Route::get('/user/delete', [DashboardController::class, 'userdelete'])->name('user.delete');

    Route::get('/videos', [DashboardController::class, 'videos'])->name('video.index');
    Route::get('/videos/data', [DashboardController::class, 'videosdata'])->name('video.data');
    Route::get('/videos/edit/{id}', [DashboardController::class, 'videoedit'])->name('video.edit');
    Route::post('/video/status', [DashboardController::class, 'videostatus'])->name('video.status');
    Route::get('/video/details/{id}', [DashboardController::class, 'videodetails'])->name('video.details');

    Route::get('/banners/show', [DashboardController::class, 'banners'])->name('banners.index');
    Route::post('/banner/store', [DashboardController::class, 'bannerstore'])->name('banners.store');

    Route::get('/hashtags', [DashboardController::class, 'hashtags'])->name('hashtags.index');
    Route::get('/hashtags/data', [DashboardController::class, 'hashtagsdata'])->name('hashtags.data');

    Route::get('/video/streaming/{path}', [DashboardController::class, 'videoStreaming'])->name('video.streaming');

    Route::get('/category', [DashboardController::class, 'category'])->name('category.index');
    Route::post('/category/store', [DashboardController::class, 'categoryStore'])->name('category.store');
    Route::get('/category/data', [DashboardController::class, 'categoryData'])->name('category.data');
    Route::get('/edit/{id}/category', [DashboardController::class, 'categoryedit'])->name('category.edit');
    Route::post('/category/update', [DashboardController::class, 'categoryUpdate'])->name('category.update');
    Route::get('/delete/category', [DashboardController::class, 'categorydelete'])->name('category.delete');
    Route::post('/check/category', [DashboardController::class, 'categoryDuplicate'])->name('category.duplicate');

});
require __DIR__ . '/auth.php';
