<?php

namespace App\Livewire\Profile;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class UpdatePassword extends Component
{
   public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';
      protected function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
    public function updatePassword(): void
    {
        $this->validate();

        $user = Auth::user();

        if (!Hash::check($this->current_password, $user->password)) {
            Notification::make()
                ->title('Current password is incorrect')
                ->danger()
                ->send();
        $this->current_password = $this->password = $this->password_confirmation = '';

            return;
        }

        $user->update([
            'password' => $this->password,
        ]);

      
        $this->current_password = $this->password = $this->password_confirmation = '';

        Notification::make()
            ->title('Password updated successfully')
            ->success()
            ->send();
    }
    public function render()
    {
        return view('livewire.profile.update-password');
    }
}
