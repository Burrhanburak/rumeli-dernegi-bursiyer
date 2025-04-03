<?php

// This is a simple script to test if emails are working
// Place this file in your project root and run it with: php debug_email.php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::where('email', 'your-email@example.com')->first();

if (!$user) {
    echo "User not found. Please update the email in this script.\n";
    exit(1);
}

try {
    // Generate a test OTP
    $otp = rand(100000, 999999);
    
    // Send a test notification
    $user->notify(new \App\Notifications\OtpNotification($otp));
    
    echo "Test email sent to {$user->email}! Check your inbox.\n";
    
    // Also log the email driver configuration
    echo "Mail driver: " . config('mail.default') . "\n";
    echo "From address: " . config('mail.from.address') . "\n";
    echo "Resend API key configured: " . (config('services.resend.key') ? 'Yes' : 'No') . "\n";
    
} catch (\Exception $e) {
    echo "Error sending email: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
