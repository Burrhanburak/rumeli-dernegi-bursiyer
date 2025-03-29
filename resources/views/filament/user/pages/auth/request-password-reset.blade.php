<x-filament-panels::page>
    <div class="mx-auto my-6 max-w-md">
        <x-filament-panels::form wire:submit="request">
            <x-filament::section>
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold tracking-tight mb-2">
                        {{ $this->getHeading() }}
                    </h2>
                    
                    <p class="text-gray-500">
                        {{ $this->getSubheading() }}
                    </p>
                </div>
                
                <div class="space-y-5">
                    {{ $this->form }}
                    
                    <div class="mt-5">
                        <button type="submit" class="filament-button filament-button-size-md inline-flex items-center justify-center py-2 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset w-full text-white bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-button-action">
                            Şifre Sıfırlama Bağlantısı Gönder
                        </button>
                    </div>
                </div>
            </x-filament::section>
        </x-filament-panels::form>
        
        <x-slot name="footer">
            <div class="text-center mt-4">
                <x-filament::link href="{{ route('filament.user.auth.login') }}">
                    Giriş Sayfasına Dön
                </x-filament::link>
            </div>
        </x-slot>
    </div>
</x-filament-panels::page>
