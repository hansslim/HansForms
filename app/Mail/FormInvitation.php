<?php

namespace App\Mail;

use App\Models\Form;
use App\Models\FormPrivateAccessToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FormInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $formPrivateAccessToken;
    public $form;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(FormPrivateAccessToken $formPrivateAccessToken, Form $form)
    {
        $this->formPrivateAccessToken = $formPrivateAccessToken;
        $this->form = $form;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.form_invitation', [
            'formName' => $this->form->name,
            'startTime' => date_format($this->form->start_time,"d.m.Y H:i:s"),
            'endTime' => date_format($this->form->end_time,"d.m.Y H:i:s"),
            'privateLink' => env('PRIVATE_FORM_BASE_URL').'/private_form/'.$this->formPrivateAccessToken->token
        ]);
    }
}
