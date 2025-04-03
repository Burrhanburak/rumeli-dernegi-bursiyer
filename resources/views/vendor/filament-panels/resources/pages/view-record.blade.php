<x-filament-panels::page
    @class([
        'fi-resource-view-record-page',
        'fi-resource-' . str_replace('/', '-', $livewire->getResource()::getSlug()),
        'fi-resource-record-' . $record->getKey(),
    ])
>
    @php
        $relationManagers = $livewire->getRelationManagers();
        $hasCombinedRelationManagerTabsWithContent = $livewire->hasCombinedRelationManagerTabsWithContent();
    @endphp

    @if ((! $hasCombinedRelationManagerTabsWithContent) || (! count($relationManagers)))
        @if ($livewire->hasInfolist())
            {{ $livewire->infolist }}
        @else
            <div
                wire:key="{{ $livewire->getId() }}.forms.{{ $livewire->getFormStatePath() }}"
            >
                {{ $livewire->form }}
            </div>
        @endif
    @endif

    @if (count($relationManagers))
        <x-filament-panels::resources.relation-managers
            :active-locale="isset($activeLocale) ? $activeLocale : null"
            :active-manager="$livewire->activeRelationManager ?? ($hasCombinedRelationManagerTabsWithContent ? null : array_key_first($relationManagers))"
            :content-tab-label="$livewire->getContentTabLabel()"
            :content-tab-icon="$livewire->getContentTabIcon()"
            :content-tab-position="$livewire->getContentTabPosition()"
            :managers="$relationManagers"
            :owner-record="$record"
            :page-class="static::class"
        >
            @if ($hasCombinedRelationManagerTabsWithContent)
                <x-slot name="content">
                    @if ($livewire->hasInfolist())
                        {{ $livewire->infolist }}
                    @else
                        {{ $livewire->form }}
                    @endif
                </x-slot>
            @endif
        </x-filament-panels::resources.relation-managers>
    @endif
</x-filament-panels::page>
