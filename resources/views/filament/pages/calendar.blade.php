<x-filament-panels::page>
  
  <x-filament::section>
    <div class="flex justify-end">
      {{ $this->createAction() }}
    </div>
  </x-filament::section>

    <x-filament::section>
    <div wire:ignore
         x-data="{
             init() {
               const fullcalendar = () => {
                 let calendar = new FullCalendar.Calendar(this.$el, {
                    editable: true,
                    selectable: true,
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,listWeek'
                    },

                    select: function(info) {
                    $wire.mountAction('createAction', { startDate: info.startStr, endDate: info.endStr });
                  },
                    
                    eventClick: function(info){
                     $wire.mountAction('viewAction',{id: info.event.id});
                    },

                    eventDrop: function(info){
                    $wire.mountAction('dropAction',{id: info.event.id, startDate: info.event.startStr, endDate: info.event.endStr});
                    },

                    events: $wire.get('events'),
                });
                calendar.render();

                }
                document.addEventListener('livewire:navigated',()=>{
                     fullcalendar()
                 })

                $wire.on('refresh_events',() => {
                    fullcalendar()
                })
                
            }
              
        }">
      </div>
    </x-filament::section>

    @assets
    <script src="{{ asset('js/fullcalendar.js') }}" data-navigate-once></script>
    @endassets

</x-filament-panels::page>
