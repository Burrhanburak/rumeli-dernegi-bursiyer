<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordChangeController extends Controller
{
    /**
     * Display the password change form.
     */
    public function showChangeForm()
    {
        // Kullanıcının şifresini değiştirmesi gerekmiyorsa ana sayfaya yönlendir (opsiyonel)
        if (!Auth::user()->must_change_password) {
            // return redirect()->route('filament.user.pages.user-dashboard'); // Kullanıcı panelinin ana sayfa rotası
        }
        return view('auth.change-password');
    }

    /**
     * Handle an incoming password change request.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ], [
            'password.required' => 'Yeni şifre alanı zorunludur.',
            'password.confirmed' => 'Yeni şifreler eşleşmiyor.',
            'password.min' => 'Yeni şifre en az 8 karakter olmalıdır.',
            'password.mixedCase' => 'Yeni şifre en az bir büyük ve bir küçük harf içermelidir.',
            'password.numbers' => 'Yeni şifre en az bir rakam içermelidir.',
            'password.symbols' => 'Yeni şifre en az bir özel karakter içermelidir.',
        ]);

        $user = Auth::user();

        // Update the user's password
        $user->password = Hash::make($request->password);
        $user->must_change_password = false; // Mark password as changed
        $user->save();

        // Optionally, log the user out and ask them to log in again with new password
        // Auth::logout();
        // return redirect()->route('filament.user.auth.login')->with('status', 'Şifreniz başarıyla güncellendi. Lütfen yeni şifrenizle giriş yapın.');

        // Redirect to the user dashboard or a confirmation page
        return redirect()->route('filament.user.pages.user-dashboard') // Kullanıcı panelinin ana sayfa rotası
                         ->with('status', 'Şifreniz başarıyla güncellendi!');
    }
} 