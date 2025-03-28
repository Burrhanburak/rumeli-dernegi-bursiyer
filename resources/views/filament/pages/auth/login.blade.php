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
                
                <div class="mt-4 text-center">
    <a href="{{ route('filament.user.auth.login') }}" class="text-primary-600 hover:text-primary-500">
        {{ __('Kullanıcı Girişi') }}
    </a>
</div>
               
            </div>
        </div>
    </div>
</x-filament-panels::page>