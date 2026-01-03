<?php

namespace App\Livewire\ClientType;

use App\Models\ClientType;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CreateClientType extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('type_name')
                    ->label(__('Type Name'))
                    ->rules(['required','string','min:3']),
                ])
            ->statePath('data')
            ->model(ClientType::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $record = ClientType::create($data);

        $this->form->model($record)->saveRelationships();
         Notification::make()
            ->title('Client type created successfully!')
            ->success()
            ->send();
        $this->reset();    
    }

    public function render(): View
    {
        return view('livewire.client-type.create-client-type');
    }
}
