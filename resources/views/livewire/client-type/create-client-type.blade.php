<div class="space-y-4 p-6">
  {{ $this->form }}

  <div class="flex justify-start mt-4 space-x-2">
    <x-filament::button wire:click="create">
    {{__("Save")}}
    </x-filament::button>

</div>

</div>