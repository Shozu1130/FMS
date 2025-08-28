<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProfessorAccountCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $professorName;
    public $professorEmail;
    public $professorId;
    public $tempPassword;

    public function __construct($professorName, $professorEmail, $professorId, $tempPassword)
    {
        $this->professorName = $professorName;
        $this->professorEmail = $professorEmail;
        $this->professorId = $professorId;
        $this->tempPassword = $tempPassword;
    }

    public function build()
    {
        return $this->subject('Your Professor Account - Bestlink College')
                    ->view('emails.professor_created');
    }
}