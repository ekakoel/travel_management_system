<?php

namespace App\Http\Controllers;

use App\Models\Spks;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\URL;

class SpkWhatsAppController extends Controller
{
    public function send(Spks $spk): RedirectResponse
    {
        $spk->loadMissing([
            'reservation',
            'driver',
            'vehicle',
        ]);

        $reservation = $spk->reservation;
        $flights = $spk->airport_shuttles;

        if (!$reservation) {
            return back()->with(
                'error',
                'Reservation untuk SPK ini tidak ditemukan.'
            );
        }

        $recipientPhone = $reservation->customer_phone
            ?? $reservation->agent?->phone
            ?? null;

        if (!$recipientPhone) {
            return back()->with(
                'error',
                'Nomor WhatsApp penerima belum tersedia.'
            );
        }

        $recipientPhone = $this->normalizeWhatsAppNumber($recipientPhone);

        if (!$recipientPhone) {
            return back()->with(
                'error',
                'Format nomor WhatsApp penerima tidak valid.'
            );
        }

        if (blank($spk->public_token)) {
            $spk->public_token =
                Spks::generateUniquePublicToken();

            $spk->save();
        }

        $reportUrl = route('spks.public-report', [
            'token' => $spk->public_token,
        ]);

        $message = $this->buildMessage(
            spk: $spk,
            reportUrl: $reportUrl
        );

        $whatsappUrl = 'https://wa.me/'
            . $recipientPhone
            . '?text='
            . rawurlencode($message);

        return redirect()->away($whatsappUrl);
    }

    public function send_to_driver(Spks $spk): RedirectResponse
    {
        $spk->loadMissing([
            'reservation',
            'driver',
            'vehicle',
        ]);

        $reservation = $spk->reservation;

        if (!$reservation) {
            return back()->with(
                'error',
                'Reservation untuk SPK ini tidak ditemukan.'
            );
        }

        $driverPhone = $spk->driver?->phone
            ?? null;

        if (!$driverPhone) {
            return back()->with(
                'error',
                'Nomor WhatsApp penerima belum tersedia.'
            );
        }

        $driverPhone = $this->normalizeWhatsAppNumber($driverPhone);

        if (!$driverPhone) {
            return back()->with(
                'error',
                'Format nomor WhatsApp penerima tidak valid.'
            );
        }

        if (blank($spk->public_token)) {
            $spk->public_token =
                Spks::generateUniquePublicToken();
            $spk->save();
        }

        $reportUrl = route('spks.public-report', [
            'token' => $spk->public_token,
        ]);

        $message = $this->buildDriverMessage(
            spk: $spk
        );

        $whatsappUrl = 'https://wa.me/'
            . $driverPhone
            . '?text='
            . rawurlencode($message);

        return redirect()->away($whatsappUrl);
    }
    

    private function buildFlightMessage(Spks $spk): string
    {
        $airportShuttles = $spk->airport_shuttles
            ->filter(function ($airportShuttle) {
                return filled($airportShuttle->nav);
            })
            ->values();

        if ($airportShuttles->isEmpty()) {
            return "-";
        }

        if ($airportShuttles->count() === 1) {
            $airportShuttle = $airportShuttles->first();

            if ($airportShuttle->nav === 'In') {
                $airportShuttleNav = 'Arrival';
            }else{
                $airportShuttleNav = 'Departure';
            }

            $flightNumber = $airportShuttle->flight_number
                    ?? null;

            $flightTime = $airportShuttle->date
            ? Carbon::parse($airportShuttle->date)->format('d M Y (H:i)')
            : null;

            return "{$airportShuttleNav} - {$flightNumber} - {$flightTime}";
        }


        $airportShuttleLines = $airportShuttles
            ->map(function ($airportShuttle, $index) {
                $number = $index + 1;
                if ($airportShuttle->nav === 'In') {
                    $airportShuttleNav = 'Arrival';
                }else{
                    $airportShuttleNav = 'Departure';
                }

                $flightNumber = $airportShuttle->flight_number
                    ?? null;

                $flightTime = $airportShuttle->date
                ? Carbon::parse($airportShuttle->date)->format('d M Y H:i')
                : null;

                if (filled($flightNumber)) {
                    return "{$number}. {$airportShuttleNav} - {$flightNumber} ({$flightTime})";
                }

                return "{$number}. {$airportShuttleNav}";
            })
            ->implode("\n");

        return "{$airportShuttleLines}";
    }


    private function buildDestinationMessage(Spks $spk): string
    {
        $destinations = $spk->destinations
            ->filter(function ($destination) {
                return filled($destination->destination_name);
            })
            ->values();

        if ($destinations->isEmpty()) {
            return "-";
        }

        if ($destinations->count() === 1) {
            $destination = $destinations->first();

            $destinationName = $destination->destination_name;

            return "{$destinationName}";
        }


        $destinationLines = $destinations
            ->map(function ($destination, $index) {
                $number = $index + 1;

                $destinationName = $destination->destination_name;

                $description = $destination->description
                    ?? $destination->notes
                    ?? null;

                if (filled($description)) {
                    return "{$number}. {$destinationName} ({$description})";
                }

                return "{$number}. {$destinationName}";
            })
            ->implode("\n");

        return "{$destinationLines}";
    }


    private function buildGuestMessage(Spks $spk): string
    {
        $guests = $spk->guests
            ->filter(function ($guest) {
                return filled($guest->name);
            })
            ->values();

        if ($guests->isEmpty()) {
            return "-";
        }


        if ($guests->count() === 1) {
            $guest = $guests->first();

            $guestName = $guest->name;

            return "{$guestName}";
        }


        $guestLines = $guests
            ->map(function ($guest, $index) {
                $number = $index + 1;

                $guestName = $guest->name;

                $nameMandarin = $guest->name_mandarin
                    ?? null;

                if (filled($nameMandarin)) {
                    return "{$number}. {$guestName} ({$nameMandarin})";
                }

                return "{$number}. {$guestName}";
            })
            ->implode("\n");

        return "{$guestLines}";
    }

    /**
     * Membuat isi pesan WhatsApp.
     */
    private function buildMessage(
        Spks $spk,
        string $reportUrl
    ): string {
        $reservation = $spk->reservation;

        $customerName = $reservation->customer_name
            ?? $reservation->agent?->name
            ?? 'Customer';

        $orderNumber = $reservation->reservation_code
            ?? $reservation->rsv_no
            ?? $reservation->order?->order_number
            ?? '-';

        $serviceType = $spk->type
            ?? '-';

        $spkDate = $this->formatDate(
            $spk->spk_date
                ?? $reservation->reservation_date
                ?? null
        );


        $guestName = $this->buildGuestMessage($spk);

        $flightNumber = $this->buildFlightMessage($spk);

        $destinationMessage = $this->buildDestinationMessage($spk);

        $driverName = $spk->driver?->name
            ?? $spk->driver_name
            ?? '-';

        $driverPhone = $spk->driver?->phone
            ?? $spk->driver_phone
            ?? '-';

        $driverPhone = $this->formatDisplayPhone($driverPhone);

        $vehicleBrand = $spk->vehicle?->brand
            ?? $spk->vehicle_brand
            ?? null;

        $vehicleModel = $spk->vehicle?->model
            ?? $spk->vehicle?->name
            ?? $spk->vehicle_name
            ?? null;

        $vehicleName = collect([
            $vehicleBrand,
            $vehicleModel,
        ])
            ->filter()
            ->unique()
            ->implode(' - ');

        if (!$vehicleName) {
            $vehicleName = '-';
        }

        $policeNumber = $spk->plate_number
            ?? '-';

        return implode("\n", [
            "Halo {$customerName},",
            '',
            '*Your Order*',
            "Order Number: *{$orderNumber}*",
            "Date: {$spkDate}",
            "Type: {$serviceType}",
            '',
            '*Guest Information*',
            $guestName,
            '',
            '*Flight Information*',
            $flightNumber,
            '',
            '*Destination*',
            $destinationMessage,
            '',
            '*Transport Information*',
            "Driver: {$driverName}",
            "Hp: {$driverPhone}",
            "Vehicle: {$vehicleName}",
            "Police Number: {$policeNumber}",
            '',
            '*SPK Detail*',
            'Untuk melihat detail SPK, gunakan link berikut:',
            $reportUrl,
            '',
            'Terima kasih,',
            'online.balikamitour.com',
        ]);
    }

    /**
     * Membuat isi pesan WhatsApp untuk Driver.
     */
    private function buildDriverMessage(Spks $spk): string {
        $reservation = $spk->reservation;

        $driverName = $spk->driver?->name
            ?? 'Driver';

        $spkDate = $this->formatDate(
            $spk->spk_date
                ?? $reservation->reservation_date
                ?? null
        );
        $spkLinkDriver = 'https://online.balikamitour.com/spk/' . $spk->id . '/' . $spk->spk_number;

        return implode("\n", [
            "Halo {$driverName},",
            '',
            "SPK untuk tanggal {$spkDate} telah diterbitkan pada link berikut:",
            "{$spkLinkDriver}",
            '',
            '',
            "- Pastikan kondisi kendaraan dalam keadaan bersih, aman, dan siap digunakan.",
            "- Tiba tepat waktu sesuai jadwal penjemputan yang telah ditentukan.",
            "- Selalu mengemudi dengan baik, hati-hati, dan mematuhi rambu lalu lintas.",
            "- Pastikan kenyamanan penumpang selalu menjadi prioritas utama.",
            "- Selalu lakukan Check-in di lokasi destinasi sesuai ketentuan yang tertera pada SPK.",
            "- Jaga sikap profesional, ramah, dan menjaga nama baik perusahaan.",
            '',
            "Terima kasih",
            "*Bali Kami Tour*"
        ]);
    }

    /**
     * Mengubah nomor Indonesia menjadi format WhatsApp.
     *
     * Contoh:
     * 085847357369     => 6285847357369
     * +6285847357369   => 6285847357369
     * 6285847357369    => 6285847357369
     */
    private function normalizeWhatsAppNumber(
        ?string $phone
    ): ?string {
        if (!$phone) {
            return null;
        }

        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (!$phone) {
            return null;
        }

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        if (str_starts_with($phone, '620')) {
            $phone = '62' . substr($phone, 3);
        }

        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        if (strlen($phone) < 10 || strlen($phone) > 16) {
            return null;
        }

        return $phone;
    }

    /**
     * Menampilkan nomor driver dengan awalan +.
     */
    private function formatDisplayPhone(
        ?string $phone
    ): string {
        if (!$phone || $phone === '-') {
            return '-';
        }

        $normalized = $this->normalizeWhatsAppNumber($phone);

        return $normalized
            ? '+' . $normalized
            : $phone;
    }

    /**
     * Format tanggal menjadi 31 Jul 2026.
     */
    private function formatDate(
        mixed $date
    ): string {
        if (!$date) {
            return '-';
        }

        try {
            return Carbon::parse($date)->format('d M Y');
        } catch (\Throwable) {
            return (string) $date;
        }
    }
}
