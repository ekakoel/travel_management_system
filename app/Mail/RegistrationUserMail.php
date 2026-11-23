<?php

namespace App\Mail;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Orders;
use App\Models\HotelRoom;
use App\Models\Transports;
use App\Models\VendorPackage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\AirportShuttle;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegistrationUserMail extends Mailable
{
    use Queueable, SerializesModels;
    public $id;
    
    public function __construct($id,$now)
    {
       $this->id = $id;
       $this->now = $now;
    }

    
    public function build()
    {
        $now = $this->now;
        $user = User::where('id', $this->id)->first();
        return $this->subject('New User Registration '.$user->id)
        ->view('emails.newUserRegister',[
            'user'=>$user,
         ]);
    }
}
