<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Livewire\Component;

class OtpVerification extends Component
{
    protected static string $view = 'filament.user.pages.auth.otp-verification';

    public $otp = '';

    public function verify()
    {
        $user = auth()->user();
        if ($user->verifyOtp($this->otp)) {
            return redirect()->route('filament.user.pages.user-dashboard'); // Adjust redirect as needed
        } else {
            $this->addError('otp', 'Geçersiz veya süresi dolmuş OTP.');
        }
    }

    public function resendOtp()
    {
        $user = auth()->user();
        $otp = rand(100000, 999999);
        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();
        $user->notify(new \App\Notifications\OtpNotification($otp));
        session()->flash('message', 'Yeni OTP gönderildi.');
    }

    public function render()
    {
        return view('filament.user.pages.auth.otp-verification');
    }
}