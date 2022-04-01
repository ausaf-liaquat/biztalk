<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\DashboardController;

Route::get('/', function () {
 
    return view('welcome');
});




Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');
    Route::get('/user',[DashboardController::class,'userindex'])->name('user.index');
 
});
require __DIR__.'/auth.php';
