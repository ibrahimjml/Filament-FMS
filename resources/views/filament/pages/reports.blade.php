<x-filament-panels::page>

     {{-- Income Table --}}
    <x-filament::section heading="{{ __('Incomes') }}">
        {{ $this->renderTable('income') }}
    </x-filament::section>

    {{-- Outcome Table --}}
    <x-filament::section heading="{{ __('Outcomes') }}">
        {{ $this->renderTable('outcome') }}
    </x-filament::section>

</x-filament-panels::page>
