<?php
namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
class Register extends Authenticatable
{
	
    use Notifiable;

 public function routeNotificationForMail($notification)
    {
        // Return email address only...
        //return $this->email_address;

        // Return name and email address...
        return [$this->email => $this->fname];
    }
}
