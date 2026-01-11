<x-filament::dropdown>
  
  <x-slot name="trigger">
    <x-filament::avatar 
     id="filament-language-switcher" size="md" 
     src="https://cdn.jsdelivr.net/gh/hampusborgos/country-flags@main/svg/{{config('languages')[app()->getLocale()]['flag']?:null}}.svg" />
  </x-slot>

  <x-filament::dropdown.list>
    @foreach ($languages as $code => $lang)
      @php $isCurrent = app()->getLocale() === $code; @endphp
      <x-filament::dropdown.list.item
           :image="'https://cdn.jsdelivr.net/gh/hampusborgos/country-flags@main/svg/'.$lang['flag'].'.svg'"
           wire:click="switchLang('{{ $code }}')">

          <span @class(['font-semi-bold text-green-500' => $isCurrent])>{{ __('translation-lang.lang.' . $code) }}</span>
      </x-filament::dropdown.list.item>
    @endforeach
  </x-filament::dropdown.list>

</x-filament::dropdown>