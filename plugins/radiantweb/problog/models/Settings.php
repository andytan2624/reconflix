<?php namespace Radiantweb\Problog\Models;

use Model;
use Cms\Classes\Page;

class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'problog_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';

    public $attachOne = [
        'gapi_key' => ['System\Models\File', 'public' => false]
    ];

    public function getBlogPostOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getDefaultParentOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

}