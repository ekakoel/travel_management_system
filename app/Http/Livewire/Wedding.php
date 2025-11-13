<?php
  
namespace App\Http\Livewire;
use App\Models\Team;
use App\Models\Brides;
use Livewire\Component;
use App\Models\OrderWedding;
  
class Wedding extends Component
{
    public $currentStep = 1;
    public $groom, $groom_chinese, $groom_contact, $bride, $bride_chinese, $bride_contact, $checkin, $wedding_date, $number_of_guests, $status = "1";
    public $successMsg = '';
  
    /**
     * Write code on Method
     */
    public function render()
    {
        return view('livewire.wedding');
    }
  
    /**
     * Write code on Method
     */
    public function firstStepSubmit()
    {
        $validatedData = $this->validate([
            'groom' => 'required',
            'groom_contact' => 'numeric|required',
            'bride' => 'required',
            'bride_contact' => 'numeric|required',
        ]);
 
        $this->currentStep = 2;
    }
  
    /**
     * Write code on Method
     */
    public function secondStepSubmit()
    {
        $validatedData = $this->validate([
            'checkin' => 'required',
            'wedding_date' => 'required',
            'number_of_guests' => 'numeric|required',
        ]);
  
        $this->currentStep = 3;
    }
  
    /**
     * Write code on Method
     */
    public function submitForm()
    {
        Brides::create([
            'groom' => $this->groom,
            'groom_chinese' => $this->groom_chinese,
            'groom_contact' => $this->groom_contact,
            'bride' => $this->bride,
            'bride_chinese' => $this->bride_chinese,
            'bride_contact' => $this->bride_contact,
        ]);
        OrderWedding::create([
            'wedding_id' => $this->wedding_id,
            'hotel_id' => $this->hotel_id,
            'duration' => $this->duration,
            'brides_id' => $this->brides_id,
            'number_of_invitation' => $this->number_of_invitation,
            'wedding_venue_id' => $this->wedding_venue_id,
            'wedding_makeup_id' => $this->wedding_makeup_id,
            'wedding_documentation_id' => $this->wedding_documentation_id,
            'wedding_decoration_id' => $this->wedding_decoration_id,
            'wedding_dinner_venue_id' => $this->wedding_dinner_venue_id,
            'wedding_entertainment_id' => $this->wedding_entertainment_id,
            'wedding_transport_id' => $this->wedding_transport_id,
            'wedding_other_id' => $this->wedding_other_id,
            'venue_price' => $this->venue_price,
            'makeup_price' => $this->makeup_price,
            'room_price' => $this->room_price,
            'documentation_price' => $this->documentation_price,
            'decoration_price' => $this->decoration_price,
            'dinner_venue_price' => $this->dinner_venue_price,
            'entertainment_price' => $this->entertainment_price,
            'transport_price' => $this->transport_price,
            'other_price' => $this->other_price,
            'wedding_date' => $this->wedding_date,
        ]);
        Orders::create([
            'user_id'=>$this->user_id,
            'rsv_id'=>$this->rsv_id,
            'order_no'=>$this->order_no,
            'name'=>$this->name,
            'email'=>$this->email,
            'servicename'=>$this->servicename,
            'service'=>$this->service,
            'service_type'=>$this->service_type,
            'service_id'=>$this->service_id,
            'subservice'=>$this->subservice,
            'subservice_id'=>$this->subservice_id,
            'checkin'=>$this->checkin,
            'checkout'=>$this->checkout,
            'travel_date'=>$this->travel_date,
            'location'=>$this->location,
            'src'=>$this->src,
            'dst'=>$this->dst,
            'tour_type'=>$this->tour_type,
            'itinerary'=>$this->itinerary,
            'number_of_guests'=>$this->number_of_guests,
            'number_of_guests_room'=>$this->number_of_guests_room,
            'guest_detail'=>$this->guest_detail,
            'request_quotation'=>$this->request_quotation,
            'wedding_order_id'=>$this->wedding_order_id,
            'wedding_date'=>$this->wedding_date,
            'bride_name'=>$this->bride_name,
            'groom_name'=>$this->groom_name,
            'special_day'=>$this->special_day,
            'special_date'=>$this->special_date,
            'extra_bed'=>$this->extra_bed,
            'capacity'=>$this->capacity,
            'benefits'=>$this->benefits,
            'booking_code'=>$this->booking_code,
            'include'=>$this->include,
            'additional_info'=>$this->additional_info,
            'destinations'=>$this->destinations,
            'msg'=>$this->msg,
            'number_of_room'=>$this->number_of_room,
            'duration'=>$this->duration,
            'price_pax'=>$this->price_pax,
            'normal_price'=>$this->normal_price,
            'kick_back'=>$this->kick_back,
            'kick_back_per_pax'=>$this->kick_back_per_pax,
            'extra_bed_id'=>$this->extra_bed_id,
            'extra_bed_price'=>$this->extra_bed_price,
            'extra_bed_total_price'=>$this->extra_bed_total_price,
            'price_total'=>$this->price_total,
            'optional_price'=>$this->optional_price,
            'alasan_discounts'=>$this->alasan_discounts,
            'discounts'=>$this->discounts,
            'bookingcode'=>$this->bookingcode,
            'bookingcode_disc'=>$this->bookingcode_disc,
            'promotion'=>$this->promotion,
            'promotion_disc'=>$this->promotion_disc,
            'additional_service_date'=>$this->additional_service_date,
            'additional_service'=>$this->additional_service,
            'additional_service_qty'=>$this->additional_service_qty,
            'additional_service_price'=>$this->additional_service_price,
            'airport_shuttle_price'=>$this->airport_shuttle_price,
            'wedding_tax'=>$this->wedding_tax,
            'final_price'=>$this->final_price,
            'package_name'=>$this->package_name,
            'promo_name'=>$this->promo_name,
            'book_period_start'=>$this->book_period_start,
            'book_period_end'=>$this->book_period_end,
            'period_start'=>$this->period_start,
            'period_end'=>$this->period_end,
            'status'=>$this->status,
            'sales_agent'=>$this->sales_agent,
            'arrival_flight'=>$this->arrival_flight,
            'arrival_time'=>$this->arrival_time,
            'airport_shuttle_in'=>$this->airport_shuttle_in,
            'departure_flight'=>$this->departure_flight,
            'departure_time'=>$this->departure_time,
            'airport_shuttle_out'=>$this->airport_shuttle_out,
            'notification'=>$this->notification,
            'note'=>$this->note,
            'cancellation_policy'=>$this->cancellation_policy,
            'verified_by'=>$this->verified_by,
            'driver_id'=>$this->driver_id,
            'guide_id'=>$this->guide_id,
            'pickup_name'=>$this->pickup_name,
            'pickup_date'=>$this->pickup_date,
            'pickup_location'=>$this->pickup_location,
            'dropoff_date'=>$this->dropoff_date,
            'dropoff_location'=>$this->dropoff_location,
        ]);
  
        $this->successMsg = 'Team successfully created.';
  
        $this->clearForm();
  
        $this->currentStep = 1;
    }
  
    /**
     * Write code on Method
     */
    public function back($step)
    {
        $this->currentStep = $step;    
    }
  
    /**
     * Write code on Method
     */
    public function clearForm()
    {
        $this->groom = '';
        $this->groom_chinese = '';
        $this->groom_contact = '';
        $this->bride = '';
        $this->bride_chinese = '';
        $this->bride_contact = '';
        $this->checkin = '';
        $this->wedding_date = '';
        $this->number_of_guests = '';
    }
}