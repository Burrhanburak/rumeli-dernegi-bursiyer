<!-- <!-- <x-filament-panels::page>
@vite('resources/css/bursiyer-auth.css')
    <div class="flex items-center justify-center min-h-screen py-12">
        <div class="w-full max-w-md p-8 space-y-8">
            <div class="text-center space-y-2">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="bursiyer-logo">
                
                <h1 class="text-3xl font-bold text-indigo-700"> 
                    {{ __('Bursiyer Basvuru') }}
                </h1>
                
                <p class="text-gray-600">
                    {{ __('Hesabınıza giriş yapın') }}
                </p>
            </div>

            <div class="bg-white rounded-xl p-8 shadow-2xl">
                {{ $this->form }}

                <x-filament-panels::form.actions    
                    :actions="$this->getCachedFormActions()"
                    :full-width="true"
                    class="mt-6"
                />
                
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        {{ __('Hesabınız yok mu?') }}
                        <a href="{{ route('filament.user.auth.register') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                            {{ __('Hesap oluştur') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page> -->
<x-filament-panels::page class="filament-login-page">
    <div class="flex items-center justify-center min-h-screen py-12">
        <div class="w-full max-w-md p-8 space-y-8">
            <div class="text-center space-y-2">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="mx-auto h-16">
                
                <h1 class="text-3xl font-bold text-indigo-700">
                    {{ $this->getHeading() }}
                </h1>
                
                <p class="text-gray-600">
                    {{ $this->getSubheading() }}
                </p>
            </div>
            
            <div class="bg-white rounded-xl p-8 shadow-2xl">
                @if (method_exists($this, 'getRenderHookScopes'))
                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}
                @endif
                
                <x-filament-panels::form wire:submit="authenticate">
                    {{ $this->form }}
                    
                    <x-filament-panels::form.actions
                        :actions="$this->getCachedFormActions()"
                        :full-width="$this->hasFullWidthFormActions()"
                        class="mt-6"
                    />
                </x-filament-panels::form>
                
                @if (method_exists($this, 'getRenderHookScopes'))
                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}
                @endif
                
                @if (filament()->hasRegistration())
                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600">
                            {{ __('Hesabınız yok mu?') }}
                            <a href="{{ route('filament.user.auth.register') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                {{ __('Hesap oluştur') }}
                            </a>
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>