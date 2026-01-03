<x-filament-panels::page>

     {{-- Income Table --}}
    <x-filament::section heading="{{ __('Income') }}">
        {{ $this->renderTable('income') }}
    </x-filament::section>

    {{-- Outcome Table --}}
    <x-filament::section heading="{{ __('Outcome') }}">
        {{ $this->renderTable('outcome') }}
    </x-filament::section>

</x-filament-panels::page>
