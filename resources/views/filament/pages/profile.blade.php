<x-filament-panels::page>
    <div class="space-y-8">
        <x-filament::section
            heading="Profile Information"
            description="Update your account's profile information and email address."
        >
            <livewire:profile.update-info />
        </x-filament::section>

        <x-filament::section
            heading="Update Password"
            description="Ensure your account is using a long, random password to stay secure."
        >
            <livewire:profile.update-password />
        </x-filament::section>
    </div>
</x-filament-panels::page>
