@include('partials.hotel-rate-detail-trigger', [
    'detailId' => 'package-include-' . $package->id,
    'triggerEyebrow' => __('messages.Package'),
    'triggerLabel' => __('messages.Include'),
    'triggerIcon' => 'fa-check-circle-o',
    'modalEyebrow' => $package->name,
    'modalTitle' => __('messages.Include'),
    'modalIcon' => 'fa-check-circle-o',
    'modalContent' => localized_model_field($package, 'include'),
])
