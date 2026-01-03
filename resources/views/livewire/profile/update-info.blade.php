<form wire:submit.prevent="save" class="space-y-6 max-w-xl">
    <x-filament::fieldset label="Basic Information">
       
        <div class="flex flex-col mb-4"><!-- Avatar  -->
            {{ $this->form }}
            @error('avatar') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>
        
        
        <div class="flex flex-col"><!-- Name -->
            <label for="name" class="mb-1 text-gray-700 dark:text-gray-300 font-medium">Full Name <span class="text-red-500">*</span></label>

            <input
                type="text"
                id="name"
                wire:model.defer="name"
                placeholder="Enter your name"
                required
                class="rounded-md my-2 border border-gray-300 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
            />

            @error('name') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

    
        <div class="flex flex-col"> <!-- Email -->
            <label for="email" class="mb-1 text-gray-700 dark:text-gray-300 font-medium">Email Address</label>

            <input
                type="email"
                id="email"
                wire:model.defer="email"
                placeholder="Enter your email"
                class="rounded-md my-2 border border-gray-300 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
            />

            @error('email') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>
    </x-filament::fieldset>

    <div class="flex justify-end">
        <x-filament::button type="submit" icon="heroicon-o-check">
            Save changes
        </x-filament::button>
    </div>
</form>
