<?php
namespace App\Mail;
use App\Models\User;
use App\Models\Hotels;
use App\Models\HotelPromo;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
class HotelPromoMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $hotel;
    public $promo;
    public $link;
    public $title;
    public $title_mandarin;
    public $suggestion;
    public $suggestion_mandarin;
    public function __construct(User $user, Hotels $hotel, HotelPromo $promo, string $link, string $title, string $title_mandarin, string $suggestion, string $suggestion_mandarin)
    {
        $this->user = $user;
        $this->hotel = $hotel;
        $this->promo = $promo;
        $this->link = $link;
        $this->title = $title;
        $this->title_mandarin = $title_mandarin;
        $this->suggestion = $suggestion;
        $this->suggestion_mandarin = $suggestion_mandarin;
    }
    public function build()
    {
        return $this->subject('專屬優惠 | '.$this->hotel->name.' 精選促銷等你來享！')
            ->view('emails.promoEmailBlast')
            ->with([
                'promo'=>$this->promo,
                'hotel'=>$this->hotel,
                'user'=>$this->user,
                'link'=>$this->link,
                'title'=>$this->title,
                'title_mandarin'=>$this->title_mandarin,
                'suggestion'=>$this->suggestion,
                'suggestion_mandarin'=>$this->suggestion_mandarin,
            ]);
    }
}

