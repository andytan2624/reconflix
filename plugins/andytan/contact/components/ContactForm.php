<?php namespace Andytan\Contact\Components;


use Cms\Classes\ComponentBase;
use ValidationException;
use Input;
use Mail;
use Validator;
use Redirect;

class ContactForm extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'Contact Form',
            'description' => 'Simple contact form'
        ];
    }

    public function onSend() {

        $data = post();


        $rules = [
                'name' => 'required|min:5',
                'email' => 'required|email' // |unique:users
        ];

        $validator = Validator::make($data, $rules);


        if ($validator->fails()) {

            throw new ValidationException($validator);

        } else {

            $vars = ['name' => Input::get('name'), 'email' => Input::get('email'), 'content' => Input::get('content')];

            Mail::send('andytan.contact::mail.message', $vars, function ($message) {
                $message->to('admin@domain.tld', 'Admin Person');
                $message->subject('New message from contact form.');
            });
        }
    }
}