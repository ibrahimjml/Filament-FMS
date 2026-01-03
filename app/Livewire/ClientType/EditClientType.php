<?php

namespace App\Livewire\ClientType;

use App\Models\Client;
use App\Models\ClientType;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class EditClientType extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ClientType $record;
    public ?array $data = [];
    public Collection $clientTypes ;
    public function mount(?ClientType $record = null): void
    {
        $this->record = $record ?? new ClientType();
        $this->form->fill($this->record->attributesToArray());
        $this->clientTypes = ClientType::all();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('client_type_id')
                    ->label(__('Type Name'))
                    ->required()
                    ->options(ClientType::pluck('type_name','type_id')),
                TextInput::make('new_type_name')
                   ->label(__('New Type Name'))    
                   ->rules(['required','string','min:3']),

            ])
            ->statePath('data')
            ->model($this->record);
    }

    public function edit(): void
    {
        $data = $this->form->getState();

        $type = ClientType::find($data['client_type_id']);
        if(! $type) return;
        $type->type_name = $data['new_type_name'];
        $type->save();
        $this->clientTypes = ClientType::all();

         Notification::make()
        ->title(__('Client type updated successfully'))
        ->success()
        ->send();

        $this->data = [];
        $this->form->fill();
    }
   public function deleteType($typeId)
     {
         $type = ClientType::find($typeId);
         if ($type) {
           $type->delete();

           Notification::make()
              ->title('Client type deleted successfully!')
              ->success()
              ->send();
         $this->clientTypes = $this->clientTypes->filter(fn($t) => $t->type_id !== $typeId);
    }

    }
    public function render(): View
    {
        return view('livewire.client-type.edit-client-type');
    }
}
