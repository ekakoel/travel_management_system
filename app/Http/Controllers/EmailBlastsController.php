<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Hotels;
use App\Models\HotelPromo;
use App\Models\BookingCode;
use App\Models\EmailBlasts;
use App\Mail\HotelPromoMail;
use Illuminate\Http\Request;
use App\Mail\HotelPromoSpecificMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreEmailBlastsRequest;
use App\Http\Requests\UpdateEmailBlastsRequest;

class EmailBlastsController extends Controller
{

    public function index()
    {
        $emails = EmailBlasts::all();
        $user = User::where('id',2)->first();
        $title = "Don't miss out on our exciting promo, book now and enjoy special offers!";
        $title_mandarin = "別錯過我們的精彩優惠，立刻預訂享受特價！";
        $promo = HotelPromo::find(51);
        $hotel = $promo->hotels;
        $room = $promo->rooms;
        $link = "http://127.0.0.1:3000/hotel-ozISSJtfc3KVRB6Q8mMZd177UL";
        $suggestion = "We are thrilled to offer you an Special Promotion for your next stay at Hotel Ayana. Don't miss out on these fantastic deals";
        $suggestion_mandarin = "我們很高興為您下次入住 Ayana 提供 Test Promo 優惠。切勿錯過這些超值優惠!";
        return view('emails.promoSpecificEmailBlast',[
            'hotel'=>$hotel,
            'emails'=>$emails,
            'user'=>$user,
            'title'=>$title,
            'title_mandarin'=>$title_mandarin,
            'promo'=>$promo,
            'room'=>$room,
            'link'=>$link,
            'suggestion'=>$suggestion,
            'suggestion_mandarin'=>$suggestion_mandarin,
        ]);
    }

    public function send_email_promo($id)
    {
        $now = Carbon::now();
        $hotel = Hotels::find($id);
        
        $promos = HotelPromo::where('hotels_id',$id)->where('status','Active')->where('book_periode_start','<=',$now)->where('book_periode_end','>=',$now)->get();
        return view('emails.send-email-promo',[
            'hotel'=>$hotel,
            'promos'=>$promos,
        ]);
    }
    public function send_specific_email_promo($id)
    {
        $now = Carbon::now();
        $hotel = Hotels::find($id);
        $bcodes = BookingCode::where('status','Active')
                            ->where('expired_date','>=',$now)
                            ->get();
        $promos = HotelPromo::where('hotels_id',$id)->where('status','Active')->where('book_periode_start','<=',$now)->where('book_periode_end','>=',$now)->get();
        return view('emails.send-specific-email-promo',[
            'hotel'=>$hotel,
            'promos'=>$promos,
            'bcodes'=>$bcodes,
        ]);
    }

    public function func_send_email_promo(Request $request, $id)
    {
        $promo = HotelPromo::findOrFail($id);
        $hotel = $promo->hotels;

        $link = $request->link;
        $title = $request->title;
        $title_mandarin = $request->title_mandarin;
        $suggestion = $request->suggestion;
        $suggestion_mandarin = $request->suggestion_mandarin;

        $subscribedUsers = User::where('is_subscribed', true)
                                ->where('is_approved', true)
                                ->get();

        foreach ($subscribedUsers as $user) {
            Mail::to($user->email)->queue(new HotelPromoMail(
                $user,
                $hotel,
                $promo,
                $link,
                $title,
                $title_mandarin,
                $suggestion,
                $suggestion_mandarin
            ));
        }

        $promo->update([
            'email_status' => 1,
        ]);

        return back()->with('success', 'Promo Email sent successfully!');
    }

    public function func_send_specific_email_promo(Request $request,$id)
    {
        $emails = $request->emails;
        $emailList = array_map('trim', explode(',', $emails));

        $promo = HotelPromo::find($id);
        $hotel = $promo->hotels;
        $link = $request->link;
        $title = $request->title;
        $title_mandarin = $request->title_mandarin;
        if ($request->bookingcode) {
            $bookingcode = $request->bookingcode;
        }else{
            $bookingcode = "none";
        }
        $suggestion = $request->suggestion;
        $suggestion_mandarin = $request->suggestion_mandarin;

        $promo->update([
            "send_to_specific_email"=>1,
            "specific_email"=>$emails,
        ]);
        foreach ($emailList as $user) {
            Mail::to($user)->queue(new HotelPromoSpecificMail($bookingcode, $user, $hotel, $promo, $link, $title, $title_mandarin, $suggestion, $suggestion_mandarin));
        }
        return back()->with('Promo Email sent successfully!');
    }
}
