<?php

namespace App\Livewire\Profile;

use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UpdateInfo extends Component implements HasForms
{
  use InteractsWithForms;

  protected ?User $user = null;
  public string $name;
  public string $email;
  public $avatar = null;

  public function mount()
  {
    $this->user = request()->user();
    $this->name = $this->user->name;
    $this->email = $this->user->email;

    $this->form->fill([ 'avatar' => $this->user->avatar ]);
  }
  protected function getFormSchema(): array
  {
    return [
      FileUpload::make('avatar')
        ->label('Avatar')
        ->avatar()
        ->image()
        ->disk('public')
        ->directory('avatars')
        ->visibility('public')
        ->maxSize(1024)
        ->deleteUploadedFileUsing(function ($file) {

          Storage::disk('public')->delete($file);
        })

    ];
  }
  protected function rules(): array
  {
    return [
      'name'  => ['required', 'string', 'max:255'],
      'email' => ['required', 'email', 'max:255', Rule::unique(User::class)->ignore(Auth::id())],
    ];
  }
  public function save(): void
  {
    $this->validate();
    $data = $this->form->getState();
    Auth::user()->update([
      'name'  => $this->name,
      'email' => $this->email,
      'avatar' => $data['avatar'] ?? null,
    ]);

    Notification::make()
      ->title('Profile updated successfully')
      ->success()
      ->send();
  }

  public function render()
  {
    return view('livewire.profile.update-info');
  }
}
