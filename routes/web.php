<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\TransactionController;

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

Route::get('/', [HomeController::class, 'index'])->name('home');
// Route::get('/', function () {
//     return view('landing');
// })->name('home');

Auth::routes(['verify' => true]);

Route::resource('categories', CategoryController::class);
Route::resource('sub_categories', SubCategoryController::class);
Route::resource('transactions', TransactionController::class);

Route::middleware('guest')->group(function () {
    // OTP routes
    Route::get('otp/verify', [RegisterController::class, 'showOtpVerificationForm'])->name('otp.verify');
    Route::post('otp/verify', [RegisterController::class, 'verifyOtp']);
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

