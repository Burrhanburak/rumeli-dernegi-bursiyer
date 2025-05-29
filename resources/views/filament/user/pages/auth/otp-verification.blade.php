<x-filament-panels::page.simple>
    <div class="mx-auto w-full max-w-md space-y-8 p-8 bg-white dark:bg-gray-800 rounded-xl shadow-md">
        <div class="text-center">
            <h2 class="text-2xl font-bold tracking-tight text-primary-600 dark:text-primary-400">
                {{ __('OTP Doğrulama') }}
            </h2>
            <p class="mt-3 text-gray-500 dark:text-gray-400">
                {{ __('E-posta adresinize bir doğrulama kodu gönderildi:') }}
            </p>
            <div class="mt-4 mb-6 py-3 px-4 bg-gray-50 dark:bg-black/30 rounded-lg inline-block">
                <p class="font-medium text-gray-900 dark:text-white">{{ auth()->user()->email }}</p>
            </div>
        </div>

        <div class="mt-8">
            <form wire:submit.prevent="verify" class="space-y-6">
                <div>
                    <label for="otp-input" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('OTP Kodunuzu Girin') }}
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input 
                            id="otp-input"
                            type="text" 
                            wire:model="otp" 
                            placeholder="******" 
                            class="text-center text-xl tracking-widest font-mono" 
                            required 
                            autofocus 
                            maxlength="6" />
                    </x-filament::input.wrapper>

                    @error('otp')
                        <p class="mt-2 text-sm text-danger-600 dark:text-danger-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6">
                    <x-filament::button type="submit" class="w-full justify-center py-2.5">
                        {{ __('Doğrula') }}
                    </x-filament::button>
                </div>
            </form>

            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex items-center justify-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Kodu almadınız mı?') }}</span>
                <button 
                    wire:click="resendNotification" 
                    class="ml-2 text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300">
                    {{ __('Yeniden Gönder') }}
                </button>
            </div>

            @if (session('message'))
                <div class="mt-4 p-3 bg-success-50 dark:bg-success-900/20 text-success-600 dark:text-success-400 text-sm rounded-lg">
                    {{ session('message') }}
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page.simple>