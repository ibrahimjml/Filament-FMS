<x-filament::dropdown>
  <x-slot name="trigger">
    <x-filament::button color="gray" outlined>
      @if(!empty($languages[$currentLang]['emoji']))
        <span class="mr-2">{{ $languages[$currentLang]['emoji'] }}</span>
      @endif

      {{ $languages[$currentLang]['name'] }}
    </x-filament::button>
  </x-slot>
  <x-filament::dropdown.list>
    @foreach ($languages as $code => $lang)
      <x-filament::dropdown.list.item
       wire:click="switchLang('{{ $code }}')">
        @if(!empty($lang['emoji']))
          <span class="mr-2">{{ $lang['emoji'] }}</span>
        @endif
        {{ $lang['name'] }}
      </x-filament::dropdown.list.item>
    @endforeach
  </x-filament::dropdown.list>
</x-filament::dropdown>