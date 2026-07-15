@if ($order->service == "Hotel" or $order->service == "Hotel Promo" or $order->service == "Hotel Package")
    @include('frontend.home.orders.details.partials.hotel-detail-modern')
@elseif($order->service == "Private Villa")
    @include('frontend.home.orders.details.villa')
@elseif($order->service == "Tour Package")
    @include('frontend.home.orders.details.tour-modern')
@elseif($order->service == "Activity")
    @include('frontend.home.orders.details.activity')
@elseif($order->service == "Transport")
    @include('frontend.home.orders.details.transport-modern')
@elseif($order->service == "Wedding Package")
    @include('frontend.home.orders.weddings.detail')
@endif
            
