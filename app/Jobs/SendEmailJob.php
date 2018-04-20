<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(){
        $headers = $this->data['headers'];
        $attachments = $this->data['attachments'];

        $this->data['content_data']['logo'] = url('logo.png');

        Mail::send($this->data['template'], $this->data['content_data'], function ($mail) use($headers, $attachments) {
            $mail->from(env('MAIL_USERNAME'), env('APP_NAME'));
            $mail->subject($headers['subject']);

            foreach($headers['to'] as $to)
                $mail->to($this->emailReceiver($to['email']), $to['name']);

            if(isset($headers['cc']))
                foreach($headers['cc'] as $cc)
                    $mail->cc($this->emailReceiver($cc['email']), $cc['name']);

            if(env('APP_MAILING_BCC_DEV'))
                $mail->bcc(env('APP_MAILING_DEV_ADDRESS'));
            if($attachments !== null)
                foreach($attachments as $att)
                    $mail->attach(public_path($att));
        });
    }

    function emailReceiver($email){
        if(env('APP_MAILING_ENV')=='development')
            return env('APP_MAILING_DEV_ADDRESS');

        return $email;
    }
}