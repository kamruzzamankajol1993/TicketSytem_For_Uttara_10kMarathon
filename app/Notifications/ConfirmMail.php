<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ConfirmMail extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
   public $code;
   public $name;
   public $lname;
    public function __construct($code,$name,$lname)
    {
    
    $this->code=$code;
    $this->name=$name;
    $this->lname=$lname;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
         $url = url('/invoice/'.$this->code);

    return (new MailMessage)
                ->subject('UTTARA10K Registration Confirmation Email')
                ->greeting('Hello!')
                ->line($this->name.' '.$this->lname)
                ->line('Your Unique Code is '.$this->code)
                ->line('Thank You for Registering for UTTARA10K')
                ->line('Your BIB Collection location Info will be sent to you later.')
                ->line('Please bring this email with you, you need to show this email at the BIB collection point to collect your Race Kit');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
