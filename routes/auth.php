<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Livewire\Auth\ConfirmPassword;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\VerifyEmail;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Filament\User\Pages\Auth\Login as FilamentLogin;
use App\Filament\User\Pages\Auth\Register as FilamentRegister;
use App\Filament\User\Pages\Auth\RequestPasswordReset as FilamentRequestPasswordReset;
use App\Filament\User\Pages\Auth\ResetPassword as FilamentResetPassword;



Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    
    // Oturumu yenile
    session()->regenerate();
    
    // Kullanıcıyı dashboard'a yönlendir
    return redirect()->intended(route('filament.user.pages.user-dashboard'));
})->middleware(['auth', 'signed'])->name('verification.verify');

// Route::middleware('guest')->group(function () {
//     Route::get('login', Login::class)->name('login');
//     Route::get('register', Register::class)->name('register');
//     Route::get('forgot-password', ForgotPassword::class)->name('password.request');
//     Route::get('reset-password/{token}', ResetPassword::class)->name('password.reset');
// });

// Route::middleware('auth')->group(function () {
//     Route::get('verify-email', VerifyEmail::class)
//         ->name('verification.notice');

//     Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
//         ->middleware(['signed', 'throttle:6,1'])
//         ->name('verification.verify');

//     Route::get('confirm-password', ConfirmPassword::class)
//         ->name('password.confirm');
// });

// Route::post('logout', App\Livewire\Actions\Logout::class)
//     ->name('logout');
