<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Filament\Facades\Filament;

class VerifyEmailController extends Controller
{
    /**
     * Redirect to OTP verification instead of verifying directly.
     * We're using OTP verification instead of email link verification.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        // Always redirect to OTP verification page
        return redirect()->route('otp.verification');
    }
}
