<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class SupportInquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $inquiryType;
    public $subject;
    public $messageContent;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\User  $user
     * @param  string  $inquiryType
     * @param  string  $subject
     * @param  string  $messageContent
     * @return void
     */
    public function __construct(User $user, $inquiryType, $subject, $messageContent)
    {
        $this->user = $user;
        $this->inquiryType = $inquiryType;
        $this->subject = $subject;
        $this->messageContent = $messageContent;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Support Inquiry: ' . $this->subject)
                    ->view('emails.support_inquiry');
    }
}
