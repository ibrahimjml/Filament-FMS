<?php

namespace App\Livewire\Invoice;

use App\Models\InvoiceSetting as ModelsInvoiceSetting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class InvoiceSetting extends Component implements HasForms
{
  use InteractsWithForms;
  protected ?ModelsInvoiceSetting $setting = null;
  public $name, $email, $phone, $address, $logo, $more_info;

  public function mount()
  {

    $this->setting = ModelsInvoiceSetting::firstOrCreate([], [
      'company_name' => '',
    ]);
    $this->name = $this->setting->company_name;
    $this->email = $this->setting->company_email;
    $this->phone = $this->setting->company_phone;
    $this->address = $this->setting->company_address;
    $this->more_info = $this->setting->footer;
    $this->form->fill(['logo' => $this->setting->getRawOriginal('logo'),]);
  }
  protected function getFormSchema(): array
  {
    return [
      FileUpload::make('logo')
        ->label(__('Logo'))
        ->avatar()
        ->image()
        ->disk('public')
        ->directory('logo')
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
      'name'  => ['sometimes', 'nullable', 'string', 'max:255'],
      'email' => ['sometimes', 'nullable', 'email', 'max:255'],
      'phone' => ['sometimes', 'nullable', 'string', 'max:15'],
      'address' => ['sometimes', 'nullable', 'string', 'max:255'],
      'more_info' => ['sometimes', 'nullable', 'string', 'max:255'],
    ];
  }
  public function save(): void
  {
    $this->validate();
    $data = $this->form->getState();

    $this->setting = ModelsInvoiceSetting::firstOrCreate([], [
      'company_name' => '',
    ]);


    $this->setting->update([
      'company_name'  => $this->name,
      'company_email' => $this->email,
      'company_phone' => $this->phone,
      'company_address' => $this->address,
      'footer' => $this->more_info,
      'logo' => $data['logo'] ?? null,
    ]);
    Notification::make()
      ->title('invoice setting updated.')
      ->success()
      ->send();
  }
  public function render()
  {
    return view('livewire.invoice.invoice-setting');
  }
}
