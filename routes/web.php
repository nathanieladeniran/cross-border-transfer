<?php

use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('register',function(){
    $frontend_url = config('frontend_url.react_url') . '/signup';
    return redirect($frontend_url);
})->name('web_register');

Route::get('login', function () {
    $signin = config('frontend_url.react_url').'/login';
    return redirect($signin);
})->name('signin.index');

Route::group(['prefix'=>'reset_password', 'as'=>'reset.password.'],function () {
    Route::get('link', [AuthController::class, 'sendResetLink'])->name('reset_link');
});