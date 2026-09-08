<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomFormBuilderMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data = [];
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $args)
    {
        $this->data = $args;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //$mail = $this->from(get_static_option('site_global_email'), get_static_option('site_'.get_default_language().'_title'))
          $mail = $this->from(
            config('mail.from.address'),
            config('mail.from.name')
        )->subject($this->data['subject'])
            ->view('mail.custom-form');

        if (!empty($this->data['data']['attachments'])){
            foreach ($this->data['data']['attachments'] as $field_name => $attached_file){
                if (file_exists($attached_file)){
                    $mail->attach($attached_file);
                }
            }
        }
        //write code for attachments
            return $mail;
    }
}
