<?php

namespace App\Livewire;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class LanguageSwitcher extends Component
{   
    public string $currentLang = 'en';
    public $languages;
    public function mount()
    {
       $this->currentLang = App::getLocale();
       $this->languages = config('languages');
    }
    public function switchLang($code)
    {
      if(array_key_exists($code, $this->languages))
      {
        Session::put('locale',$code);
        App::setLocale($code);
        $this->currentLang = $code;
        $this->js('window.location.reload();');
      }
    }
    public function render()
    {
        return view('livewire.language-switcher');
    }
}
