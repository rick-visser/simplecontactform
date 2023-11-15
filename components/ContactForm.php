<?php

namespace Exposia\SimpleContactForm\Components;

use Cms\Classes\ComponentBase;
use Validator;
use Redirect;
use Illuminate\Http\Request;
use Exposia\SimpleContactForm\Models\Contact;

class ContactForm extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'Contactformulier',
            'description' => 'Genereer een contactformulier'
        ];
    }

    public function onSend(Request $request)
    {
        // Get the form data
        $data = [
            'firstname' => $request->get('firstname'),
            'lastname' => $request->get('lastname'),
            'email' => $request->get('email'),
            'phone' => $request->get('phone'),
            'street' => $request->get('street'),
            'city' => $request->get('city'),
//            'state' => $request->get('state'),
            'zip' => $request->get('zip'),
            'company' => $request->get('company'),
            'content' => $request->get('content'),
        ];

        // Validation rules
        $rules = [
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email',
            'phone' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'street' => 'nullable',
            'city' => 'nullable',
//            'state' => 'nullable',
            'zip' => 'nullable',
            'company' => 'nullable',
            'content' => 'required',
        ];

        // Custom validation messages
        $customMessages = [
            'firstname.required' => 'Vul uw voornaam in',
            'lastname.required' => 'Vul uw achternaam in',
            'email.required' => 'Vul uw e-mailadres in',
            'email.email' => 'Vul een geldig e-mailadres in',
            'phone.regex' => 'Vul een geldig telefoonnummer in',
            'phone.min' => 'Vul een geldig telefoonnummer in',
            'content.required' => 'Vul een bericht in',
            'g-recaptcha-response.required' => 'Vul de captcha in',
            'g-recaptcha-response.recaptcha' => 'Vul de captcha in',
        ];

        // Validate the form
        $validator = Validator::make($data, $rules, $customMessages);

        // Check if the form passes the validation rules
        if ($validator->fails()) {
            // If validation fails, return to the page with the errors
            return Redirect::back()->withInput()->withErrors($validator);
        }

        // Your secret key provided by Google
        $secret_key = env('RECAPTCHA_SECRET_KEY');

        // The user's response token
        $response_token = $request->input('g-recaptcha-response');

        // The user's IP address (optional)
        $user_ip = $request->ip();

        // Validate the reCAPTCHA response
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            'secret' => $secret_key,
            'response' => $response_token,
            'remoteip' => $user_ip,
        )));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $response_data = json_decode($response);
        if (!$response_data->success) {
            // reCAPTCHA validation failed, handle error
            return redirect()->back()->withInput()->withErrors(['recaptcha' => 'reCAPTCHA validatie is mislukt. Probeer het opnieuw.']);
        }

        //  Store data in database
        Contact::create($request->all()); // Add a record to the database

        // Variables to be used in the email
        $vars = ['firstname' => $request->get('firstname'), 'lastname' => $request->get('lastname'), 'email' => $request->get('email'), 'phone' => $request->get('phone'), 'street' => $request->get('street'), 'city' => $request->get('city'), 'zip' => $request->get('zip'), 'company' => $request->get('company'), 'content' => $request->get('content')];

        // Send email to admin (later to be integrated into the backend)
        \Mail::send('exposia.simplecontactform::mail.message', $vars, function ($message) use ($request) {
            $message->from($request->email); // Message send from the email address of the user
            $message->to('rick@rapide.software', 'Admin')->subject('Nieuw bericht via boekholt-aircos.nl'); // Email address on which you want to receive emails and the subject
        });

        // Return back to the page with a success message
        return Redirect::back()->withErrors(['success' => 'Uw bericht is succesvol verzonden. Wij nemen zo spoedig mogelijk contact met u op.']);
    }

}
