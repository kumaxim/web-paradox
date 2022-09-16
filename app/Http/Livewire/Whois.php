<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Whois extends Component
{
    public $domainList;

    public $results;

    public $jsRunner;

    protected $rules = [
        'domainList' => 'required|min:3',
    ];

    public function render()
    {
        return view('livewire.whois');
    }

    public function submit()
    {
        $this->validate();

        $list = array_map('trim', explode(PHP_EOL, $this->domainList));

        $jsArray = sprintf(
            '[\'%s\']',
            implode('\', \'', array_unique($list))
        );

        $this->jsRunner = '<script type="text/javascript">queryWhoisData(queueDomains(' . $jsArray . '))</script>';
    }
}
