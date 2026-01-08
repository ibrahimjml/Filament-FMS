<form wire:submit.prevent="save" class="space-y-6 max-w-xl">
    <x-filament::fieldset label="Header">
       
        <div class="flex flex-col mb-4"><!-- company Logo  -->
            {{ $this->form }}
            @error('logo') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>
        
        
        <div class="flex flex-col"><!-- Company name -->
            <label for="name" class="mb-1 text-gray-700 dark:text-gray-300 font-medium">Company Name <span class="text-red-500">*</span></label>

            <input
                type="text"
                id="name"
                wire:model.defer="name"
                placeholder="Enter Company Name"
                required
                class="rounded-md my-2 border border-gray-300 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
            />

            @error('name') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>
        <div class="flex flex-col"><!-- Company email -->
            <label for="email" class="mb-1 text-gray-700 dark:text-gray-300 font-medium">Company Email <span class="text-red-500">*</span></label>

            <input
                type="text"
                id="email"
                wire:model.defer="email"
                placeholder="Enter Company Email"
                required
                class="rounded-md my-2 border border-gray-300 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
            />

            @error('email') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>
        <div class="flex flex-col"><!-- Company phone -->
            <label for="phone" class="mb-1 text-gray-700 dark:text-gray-300 font-medium">Company Phone <span class="text-red-500">*</span></label>

            <input
                type="text"
                id="phone"
                wire:model.defer="phone"
                placeholder="Enter Company Phone"
                required
                class="rounded-md my-2 border border-gray-300 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
            />

            @error('phone') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>
        <div class="flex flex-col"><!-- Company address -->
            <label for="address" class="mb-1 text-gray-700 dark:text-gray-300 font-medium">Company Address <span class="text-red-500">*</span></label>

            <input
                type="text"
                id="address"
                wire:model.defer="address"
                placeholder="Enter Company Address"
                required
                class="rounded-md my-2 border border-gray-300 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
            />

            @error('address') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

    </x-filament::fieldset>
  <x-filament::fieldset label="Footer">
  <div class="flex flex-col"><!-- footer description -->
            <label for="more_info" class="mb-1 text-gray-700 dark:text-gray-300 font-medium">More info<span class="text-red-500">*</span></label>

            <textarea
                type=""
                id="more_info"
                wire:model.defer="more_info"
                placeholder="Type an info for footer"
                rows="2"
                required
                class="rounded-md my-2 border border-gray-300 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
            ></textarea>

            @error('more_info') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>
  </x-filament::fieldset>
    <div class="flex justify-end">
        <x-filament::button type="submit" icon="heroicon-o-check">
            Save changes
        </x-filament::button>
    </div>
</form>
