<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('Backend.pages.index');
})->middleware(['auth'])->name('dashboard');


// Route::group(['prefix' => 'admin'], function () {
//     Route::group(['middleware' => 'adminguest'], function () {
//         Route::get('login', [App\Http\Controllers\Admin\LoginController::class, 'LoginIndex'])->name('admin.authenticate');
//         Route::post('/login/redirect', [App\Http\Controllers\Admin\LoginController::class, 'LoginPost'])->name('admin.login');
//     });

//     Route::group(['middleware' => 'adminauth'], function () {
//         //Admin Logout
//         Route::post('/logout/redirect', [App\Http\Controllers\Admin\LoginController::class, 'Logout'])->name('admin.logout');

//     });

// });

require __DIR__.'/auth.php';
