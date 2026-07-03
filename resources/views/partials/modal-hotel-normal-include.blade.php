@include('partials.hotel-rate-detail-trigger', [
    'detailId' => 'normal-price-include-' . $normal_price_rooms->id,
    'triggerEyebrow' => __('messages.Standard'),
    'triggerLabel' => __('messages.Include'),
    'triggerIcon' => 'fa-check-circle-o',
    'modalEyebrow' => $normal_price_rooms->rooms,
    'modalTitle' => __('messages.Include'),
    'modalIcon' => 'fa-check-circle-o',
    'modalContent' => localized_model_field($normal_price_rooms, 'include'),
])
