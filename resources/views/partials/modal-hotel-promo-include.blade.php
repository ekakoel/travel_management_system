@include('partials.hotel-rate-detail-trigger', [
    'detailId' => 'promo-price-include-' . $hotel_promotion->id,
    'triggerEyebrow' => __('messages.' . $hotel_promotion->promotion_type),
    'triggerLabel' => __('messages.Include'),
    'triggerIcon' => 'fa-check-circle-o',
    'modalEyebrow' => __('messages.' . $hotel_promotion->promotion_type),
    'modalTitle' => __('messages.Include'),
    'modalIcon' => 'fa-check-circle-o',
    'modalContent' => localized_model_field($hotel_promotion, 'include'),
])
