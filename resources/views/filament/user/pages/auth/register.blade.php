<x-filament-panels::page>
    @vite('resources/css/bursiyer-auth.css')
    
    <div class="flex items-center justify-center min-h-screen py-12">
        <div class="w-full max-w-2xl p-8 space-y-8">
            <div class="text-center space-y-2">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="bursiyer-logo mx-auto">
                
                <h1 class="text-3xl font-bold text-indigo-700">
                    {{ $this->getHeading() }}
                </h1>
                
                <p class="text-gray-600">
                    {{ $this->getSubheading() }}
                </p>
            </div>
            
            <div class="bg-white rounded-xl p-8 shadow-2xl">
                @if (method_exists($this, 'getRenderHookScopes'))
                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_REGISTER_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}
                @endif
                
                <x-filament-panels::form wire:submit="register">
                    {{ $this->form }}
                    
                    <x-filament-panels::form.actions
                        :actions="$this->getCachedFormActions()"
                        :full-width="true"
                        class="mt-6"
                    />
                </x-filament-panels::form>
                
                @if (method_exists($this, 'getRenderHookScopes'))
                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_REGISTER_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}
                @endif
                
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        {{ __('Zaten bir hesabınız var mı?') }}
                        <a href="{{ route('filament.user.auth.login') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                            {{ __('Giriş yap') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>