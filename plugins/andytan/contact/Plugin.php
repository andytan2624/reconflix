<?php namespace Andytan\Contact;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            'Andytan\Contact\Components\ContactForm' => 'contactform'
        ];
    }

    public function registerSettings()
    {

    }
}
