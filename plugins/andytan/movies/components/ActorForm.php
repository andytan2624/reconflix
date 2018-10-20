<?php namespace Andytan\Movies\Components;


use Cms\Classes\ComponentBase;
use Input;
use System\Models\File;
use Validator;
use Redirect;
use Andytan\Movies\Models\Actor;
use Flash;
use ValidationException;

class ActorForm extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'Actor Form',
            'description' => 'Enter actors'
        ];
    }

    public function onSubmit() {

        $validator = Validator::make(
            $form = Input::all(), [
                'name' => 'required',
                'lastname' => 'required'
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $actor = new Actor();
        $actor->name = Input::get('name');
        $actor->lastname = Input::get('lastname');
        $actor->actorimage = Input::file('actorimage');
        $actor->save();
        Flash::success('Actor added!');
    }

    public function onImageUpload() {
        $image = Input::all();

        $file = (new File())->fromPost($image['actorimage']);

        return [
            '#imageResult' => '<img src="'. $file->getThumb(200,200,['mode' => 'crop']). '" />'
        ];
    }
}