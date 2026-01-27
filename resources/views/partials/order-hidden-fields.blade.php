<input type="hidden" name="status" value="Pending">
<input type="hidden" name="page" value="hotel-detail">
<input type="hidden" name="action" value="Create Order">
<input type="hidden" name="order_id" value="{{ $order->id }}">
<input type="hidden" name="orderno" value="{{ $order->orderno }}">
<input type="hidden" name="author" value="{{ Auth::user()->id }}">
<input type="hidden" name="name" value="{{ Auth::user()->name }}">
<input type="hidden" name="email" value="{{ Auth::user()->email }}">
<input type="hidden" name="method" value="Submit Order">
@if ($order->service == "Hotel")
    <input type="hidden" name="service" value="Hotel">
    <input type="hidden" name="normal_price" value="{{ $order->price_pax }}">
    <input type="hidden" name="kick_back" value="{{ $order->kick_back }}">
    <input type="hidden" name="include" value="{{ $order->include }}">
    <input type="hidden" name="additional_info" value="{{ $order->additional_info }}">
@elseif ($order->service == "Hotel Promo")
    <input type="hidden" name="booking_code" value="{{ $order->booking_code }}">
    <input type="hidden" name="service" value="Hotel Promo">
    <input type="hidden" name="normal_price" value="{{ $order->price_pax }}">
    <input type="hidden" name="promo_name" value="{{ $order->name }}">
    <input type="hidden" name="benefits" value="{{ $order->benefits }}">
    <input type="hidden" name="book_period_start" value="{{ dateFormat($order->book_periode_start) }}">
    <input type="hidden" name="book_period_end" value="{{ dateFormat($order->book_periode_end) }}">
    <input type="hidden" name="period_start" value="{{ dateFormat($order->periode_start) }}">
    <input type="hidden" name="period_end" value="{{ dateFormat($order->periode_end) }}">
    <input type="hidden" name="include" value="{{ $order->include }}">
    <input type="hidden" name="additional_info" value="{{ $order->additional_info }}">
@elseif ($order->service == "Hotel Package")
    <input type="hidden" name="normal_price" value="{{ $order->price_pax }}">
    <input type="hidden" name="booking_code" value="{{ $order->booking_code }}">
    <input type="hidden" name="service" value="Hotel Package">
    <input type="hidden" name="package_name" value="{{ $order->name }}">
    <input type="hidden" name="benefits" value="{{ $order->benefits }}">
    <input type="hidden" name="include" value="{{ $order->include }}">
    <input type="hidden" name="additional_info" value="{{ $order->additional_info }}">
@endif
<input type="hidden" name="guest_detail" value="{{ $order->guest_detail }}">
<input type="hidden" name="servicename" value="{{ $order->name }}">
<input type="hidden" name="subservice" value="{{ $order->rooms }}">
<input type="hidden" name="service_id" value="{{ $order->id }}">
<input type="hidden" name="subservice_id" value="{{ $order->id }}">

<input type="hidden" name="price_pax" value="{{ $order->price_pax }}">
<input type="hidden" name="optional_price" value="{{ $order->optional_price }}">
<input type="hidden" name="final_price" value="{{ $order->price_total + $order->optional_price  }}">

<input type="hidden" name="duration" value="{{ $order->duration }}">

<input type="hidden" name="capacity" value="{{ $order->capacity }}">
<input type="hidden" name="checkin" value="{{ $order->checkin }}">
<input type="hidden" name="checkout" value="{{ $order->checkout }}">

<input type="hidden" name="cancellation_policy" value="{{ $order->cancellation_policy }}">
<input type="hidden" name="location" value="{{ $order->region }}">