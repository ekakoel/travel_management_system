@include('partials.hotel-rate-detail-trigger', [
    'detailId' => 'package-benefit-' . $package->id,
    'triggerEyebrow' => __('messages.Package'),
    'triggerLabel' => __('messages.Benefits'),
    'triggerIcon' => 'fa-star-o',
    'modalEyebrow' => $package->name,
    'modalTitle' => __('messages.Benefits'),
    'modalIcon' => 'fa-star-o',
    'modalContent' => localized_model_field($package, 'benefits'),
])
