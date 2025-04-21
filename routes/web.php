<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;
use App\Filament\User\Pages\Auth\Login;
use App\Filament\User\Pages\Auth\Register;
use App\Filament\User\Pages\Auth\RequestPasswordReset;
use App\Filament\User\Pages\Auth\ResetPassword;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\PdfController;
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::get('pdf/{application}', PdfController::class)->name('pdf'); 
});

// Add a redirect for the standard 'login' route that Laravel's auth system looks for
Route::get('/login', function () {
    return redirect()->route('filament.user.auth.login');
})->name('login');

require __DIR__.'/auth.php';

