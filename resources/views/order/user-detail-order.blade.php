@if ($order->service == "Hotel" or $order->service == "Hotel Promo" or $order->service == "Hotel Package")
    @include('order.detail-order-hotel')
@elseif($order->service == "Private Villa")
    @include('order.detail-order-villa')
@elseif($order->service == "Tour Package")
    @include('order.detail-order-tour')
@elseif($order->service == "Activity")
    @include('order.detail-order-activity')
@elseif($order->service == "Transport")
    @include('order.detail-order-transport')
@elseif($order->service == "Wedding Package")
    @include('order.detail-order-wedding')
@endif
            