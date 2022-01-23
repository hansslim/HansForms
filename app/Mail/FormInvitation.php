<?php

namespace App\Mail;

use App\Models\Form;
use App\Models\FormPrivateAccessToken;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FormInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $formPrivateAccessToken;
    public $form;
    public $convertStringDate;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $formPrivateAccessToken, Form $form, bool $convertStringDate = false)
    {
        $this->formPrivateAccessToken = $formPrivateAccessToken;
        $this->form = $form;
        $this->convertStringDate = $convertStringDate;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->convertStringDate) {
            //esthetic detail
            $start_time = date("d.m.Y H:i:s", strtotime($this->form->start_time));
            $end_time = date("d.m.Y H:i:s", strtotime($this->form->end_time));
            return $this->view('mails.form_invitation', [
                'formName' => $this->form->name,
                'startTime' => $start_time,
                'endTime' => $end_time,
                'privateLink' => env('PRIVATE_FORM_BASE_URL', 'http://example.com').'/private_form/'.$this->formPrivateAccessToken
            ]);
        }
        else {
            return $this->view('mails.form_invitation', [
                'formName' => $this->form->name,
                'startTime' => date_format($this->form->start_time,"d.m.Y H:i:s"),
                'endTime' => date_format($this->form->end_time,"d.m.Y H:i:s"),
                'privateLink' => env('PRIVATE_FORM_BASE_URL', 'http://example.com').'/private_form/'.$this->formPrivateAccessToken
            ]);
        }
        
    }
}
