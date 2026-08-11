<?php

namespace App\Http\Requests\Backend\Operations\Tours;

use App\Models\TourPrices;

class UpdateTourPriceAdminRequest extends StoreTourPriceAdminRequest
{
    protected function overlapExceptPriceId(): ?int
    {
        $price = $this->route('tourPrice');

        return $price instanceof TourPrices ? (int) $price->id : null;
    }
}
