<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        SPK {{ $spk->spk_number ?? $spk->id }}
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #f5f7fa;
        }

        .report-container {
            max-width: 850px;
            margin: 40px auto;
        }

        .report-card {
            border: 0;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        .report-header {
            border-bottom: 1px solid #e9ecef;
        }

        .label {
            color: #6c757d;
            font-size: 0.875rem;
            margin-bottom: 4px;
        }

        .value {
            font-weight: 600;
            margin-bottom: 0;
        }
    </style>
</head>

<body>
    @php
        $reservation = $spk->reservation;

        $customerName = $reservation->customer_name
            ?? $reservation->guest_name
            ?? $reservation->user?->name
            ?? '-';

        $orderNumber = $reservation->reservation_code
            ?? $reservation->rsv_no
            ?? '-';

        $serviceType = $reservation->service
            ?? $spk->type
            ?? '-';

        $driverName = $spk->driver?->name
            ?? $spk->driver_name
            ?? '-';

        $driverPhone = $spk->driver?->phone
            ?? $spk->driver_phone
            ?? '-';

        $vehicleBrand = $spk->vehicle?->brand ?? null;

        $vehicleModel = $spk->vehicle?->model
            ?? $spk->vehicle?->name
            ?? null;

        $vehicleName = collect([
            $vehicleBrand,
            $vehicleModel,
        ])->filter()->unique()->implode(' - ');

        $vehicleName = $vehicleName ?: '-';

        $policeNumber = $spk->vehicle?->police_number
            ?? $spk->vehicle?->license_plate
            ?? $spk->police_number
            ?? '-';

        $destination = $spk->destination
            ?? $spk->destinations?->first()?->destination_name
            ?? '-';
    @endphp

    <main class="container report-container">
        <div class="card report-card">
            <div class="card-body p-4 p-md-5">
                <div class="report-header pb-4 mb-4">
                    <div
                        class="d-flex flex-column flex-md-row
                               justify-content-between gap-3"
                    >
                        <div>
                            <h1 class="h3 mb-1">
                                Transport SPK
                            </h1>

                            <p class="text-muted mb-0">
                                online.balikamitour.com
                            </p>
                        </div>

                        <div class="text-md-end">
                            <div class="label">SPK Number</div>

                            <div class="value">
                                {{ $spk->spk_number ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                <h2 class="h5 mb-3">
                    Order Information
                </h2>

                <div class="row g-4 mb-5">
                    <div class="col-md-6">
                        <div class="label">Order Number</div>
                        <p class="value">{{ $orderNumber }}</p>
                    </div>

                    <div class="col-md-6">
                        <div class="label">Service Type</div>
                        <p class="value">{{ $serviceType }}</p>
                    </div>

                    <div class="col-md-6">
                        <div class="label">Date</div>

                        <p class="value">
                            {{ $spk->spk_date
                                ? \Carbon\Carbon::parse(
                                    $spk->spk_date
                                )->format('d M Y')
                                : '-' }}
                        </p>
                    </div>

                    <div class="col-md-6">
                        <div class="label">Guest Name</div>
                        <p class="value">{{ $customerName }}</p>
                    </div>

                    <div class="col-md-6">
                        <div class="label">Flight</div>

                        <p class="value">
                            {{ $spk->flight_number
                                ?? $reservation->flight_number
                                ?? '-' }}
                        </p>
                    </div>

                    <div class="col-md-6">
                        <div class="label">Destination</div>
                        <p class="value">{{ $destination }}</p>
                    </div>
                </div>

                <h2 class="h5 mb-3">
                    Transport Information
                </h2>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="label">Driver</div>
                        <p class="value">{{ $driverName }}</p>
                    </div>

                    <div class="col-md-6">
                        <div class="label">Driver Phone</div>

                        <p class="value">
                            @if ($driverPhone !== '-')
                                <a
                                    href="tel:{{ $driverPhone }}"
                                    class="text-decoration-none"
                                >
                                    {{ $driverPhone }}
                                </a>
                            @else
                                -
                            @endif
                        </p>
                    </div>

                    <div class="col-md-6">
                        <div class="label">Vehicle</div>
                        <p class="value">{{ $vehicleName }}</p>
                    </div>

                    <div class="col-md-6">
                        <div class="label">Police Number</div>
                        <p class="value">{{ $policeNumber }}</p>
                    </div>
                </div>

                @if ($spk->destinations?->isNotEmpty())
                    <hr class="my-5">

                    <h2 class="h5 mb-3">
                        Destinations
                    </h2>

                    <div class="list-group">
                        @foreach ($spk->destinations as $destinationItem)
                            <div class="list-group-item px-0">
                                <div class="fw-semibold">
                                    {{ $destinationItem->destination_name
                                        ?? '-' }}
                                </div>

                                @if ($destinationItem->destination_address)
                                    <div class="small text-muted">
                                        {{ $destinationItem
                                            ->destination_address }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </main>
</body>
</html>