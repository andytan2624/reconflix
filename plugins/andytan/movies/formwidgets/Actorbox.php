<?php
namespace Andytan\Movies\FormWidgets;

use Andytan\Movies\Models\Actor;
use Backend\Classes\FormWidgetBase;
use Config;

class Actorbox extends FormWidgetBase {
    public function widgetDetails()
    {
        return [
            'name' => 'Actorbox',
            'description' => 'Field for adding actors'
        ];
    }

    public function render() {
        $this->prepareVars();

        return $this->makePartial('widget');
    }

    public function prepareVars() {
        $this->vars['id'] = $this->model->id;
        $this->vars['actors'] = Actor::all()->lists('full_name', 'id');
        $this->vars['name'] = $this->formField->getName().'[]';
        $this->vars['selectedValues'] = !empty($this->getLoadValue()) ? $this->getLoadValue() : [];
    }

    public function getSaveValue($actors)
    {
        $newArray = [];

        foreach ($actors as $actorID) {
            if (!is_numeric($actorID)) {
                $newActor = new Actor;
                $nameLastname = explode(' ', $actorID);
                $newActor->name = array_shift($nameLastname);
                $newActor->lastname = implode(' ', $nameLastname);
                $newActor->save();
                $newArray[] = $newActor->id;
            } else {
                $newArray[] = $actorID;
            }
        }

        return $newArray;
    }

    public function loadAssets()
    {
        $this->addCss('css/select2.css');
        $this->addJs('js/select2.js');
    }
}