<x-filament-panels::page>
    <form wire:submit.prevent="save">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button type="submit">
                Şifreyi Güncelle
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page> 