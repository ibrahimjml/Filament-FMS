<form wire:submit.prevent="updatePassword" class="space-y-6 max-w-xl">
    <x-filament::fieldset label="update password">
        <!-- password -->
        <div class="flex flex-col">
              <input
                type="password"
                name="current_password"
                id="current_password"
                wire:model.defer="current_password"
                placeholder="Enter your password"
                required
                class="rounded-md my-2 border border-gray-300 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
            />
            @error('current_password') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <!-- new password -->
        <div class="flex flex-col">
              <input
                type="password"
                name="password"
                id="password"
                wire:model.defer="password"
                placeholder="Enter new password"
                required
                class="rounded-md my-2 border border-gray-300 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
            />
            @error('password') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>
          <!-- password confirmation -->
        <div class="flex flex-col">
            <input
                type="password"
                name="password_confirmation"
                id="password_confirmation"
                wire:model.defer="password_confirmation"
                placeholder="repeat new password"
                required
                class="rounded-md my-2 border border-gray-300 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
            />
            @error('password_confirmation') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>
    </x-filament::fieldset>

    <div class="flex justify-end">
        <x-filament::button type="submit" icon="heroicon-o-check">
            Save
        </x-filament::button>
    </div>
</form>
