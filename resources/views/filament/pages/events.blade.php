<x-filament-panels::page>
  <x-filament::section>
    <div class="flex justify-end gap-3">
      {{ $this->visitCaledar() }}
      {{ $this->createAction() }}
    </div>
  </x-filament::section>

    {{ $this->table }}
</x-filament-panels::page>
