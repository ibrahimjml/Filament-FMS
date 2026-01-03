<div class="p-6  rounded-lg shadow max-w-lg mx-auto">
    {{ $this->form }}

    @if($clientTypes->count() > 0)
    <ul class="space-y-2 mt-4">
      <p>Delete Client Type</p>
        @foreach($clientTypes as $type)
            <li class="flex justify-between items-center bg-gray-800/90 p-3 rounded-md shadow-sm" wire:key="client-type-{{ $type->type_id }}">
                <span class="font-medium text-gray-200">{{ $type->type_name }}</span>
                <x-filament::button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 mr-4 rounded-md" 
                        wire:click="deleteType({{ $type->type_id }})">
                    Delete
                </x-filament::button>
            </li>
        @endforeach
      </ul>
      @endif
    <x-filament::button wire:click="edit" class="mt-3">
        {{ __("Save") }}
    </x-filament::button>
</div>
