<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\DashboardController;

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




Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');
    Route::get('/user',[DashboardController::class,'userindex'])->name('user.index');
 
});
require __DIR__.'/auth.php';
