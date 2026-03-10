<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Spks;
use Illuminate\Http\Request;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\Http;

class WhatsAppController extends Controller
{
    protected $base;

    public function __construct()
    {
        $this->base = "127.0.0.1:3000";
    }

    // =========================================================
    // ✅ FORMAT NOMOR WA (WA WEB BUTUH @c.us)
    // =========================================================
    private function formatPhone($number)
    {
        $number = preg_replace('/[^0-9]/', '', $number);

        if (substr($number, 0, 1) === '0') {
            $number = '62' . substr($number, 1);
        }

        if (!str_ends_with($number, '@c.us')) {
            $number = $number . '@c.us';
        }

        return $number;
    }

    // =========================================================
    // ✅ SEND TO BOTH (DRIVER + OPERATOR)
    // =========================================================
    public function send_wa_both(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'spk'   => 'required|string'
        ]);

        $spk = Spks::with(['airport_shuttles', 'guests', 'operator', 'driver', 'transport', 'destinations'])
            ->find($request->spk);

        if (!$spk) {
            return response()->json(['success' => false, 'message' => 'SPK tidak ditemukan.']);
        }

        $operator_name = $spk->operator?->name ?? '-';
        $spk_date = date('d M Y', strtotime($spk->spk_date));

        $vehicle_brand = $spk->transport?->brand ?? '-';
        $vehicle_name  = $spk->transport?->name ?? '-';
        $vehicle_no    = $spk->plate_number ?? '-';

        $driver_name  = $spk->driver?->name ?? '-';
        $driver_phone = $spk->driver?->phone ?? '-';

        $spk_driver_link   = 'https://online.balikamitour.com/spk/' . $spk->id . '/' . $spk->spk_number;
        $spk_operator_link = 'https://online.balikamitour.com/spk-report/' . $spk->id;

        $guestList = $spk->guests->map(function ($guest) {
            return "- {$guest->name} ({$guest->name_mandarin})";
        })->implode("\n");

        $shuttleList = $spk->airport_shuttles->map(function ($shuttle) {
            $shuttle_date = date('d M Y (H:i)', strtotime($shuttle->date));
            return "- ({$shuttle_date}) {$shuttle->flight_number}";
        })->implode("\n");

        $dstList = $spk->destinations->map(function ($dst) {
            return "- {$dst->destination_name}";
        })->implode("\n");

        // ===========================
        // OPERATOR MESSAGE
        // ===========================
        $message_operator =
            "Halo {$operator_name},\n\n" .
            "*Your order*\n" .
            "*Order Number:* _{$spk->order_number}_\n" .
            "*Date:* _{$spk_date}_\n" .
            "*Type:* _{$spk->type}_\n\n";

        if ($spk->type === 'Airport Shuttle') {
            $message_operator .=
                "*Guest Name:*\n{$guestList}\n" .
                "*Flight:*\n{$shuttleList}\n" .
                "*Destination:*\n{$dstList}\n\n";
        } else {
            $message_operator .=
                "*Guest Name:*\n{$guestList}\n" .
                "*Destination:*\n{$dstList}\n\n";
        }

        $message_operator .=
            "*Driver:* _{$driver_name}_\n" .
            "*Hp:* +{$driver_phone}\n" .
            "*Vehicle:* _{$vehicle_brand} - {$vehicle_name}_\n" .
            "*Police Number:* _{$vehicle_no}_\n\n" .
            "Untuk melihat detail SPK, gunakan link berikut:\n" .
            "{$spk_operator_link}\n\n" .
            "Terima kasih,\n*online.balikamitour.com*";

        // ===========================
        // DRIVER MESSAGE
        // ===========================
        $message_driver =
            "Halo {$driver_name},\n\n" .
            "SPK untuk tanggal {$spk_date} telah diterbitkan pada link berikut:\n\n" .
            "{$spk_driver_link}\n\n" .
            "- _Pastikan kondisi kendaraan dalam keadaan bersih, aman, dan siap digunakan._\n" .
            "- _Tiba tepat waktu sesuai jadwal penjemputan yang telah ditentukan._\n" .
            "- _Selalu mengemudi dengan baik, hati-hati, dan mematuhi rambu lalu lintas._\n" .
            "- _Pastikan kenyamanan penumpang selalu menjadi prioritas utama._\n" .
            "- _Selalu lakukan Check-in di lokasi destinasi sesuai ketentuan yang tertera pada SPK._\n" .
            "- _Jaga sikap profesional, ramah, dan menjaga nama baik perusahaan._\n\n" .
            "Terima kasih,\n*Bali Kami Tour*";

        // ===========================
        // SEND
        // ===========================
        $wa = new WhatsappService();
        $results = [];

        if ($spk->operator?->phone) {
            $operatorPhone = $this->formatPhone($spk->operator->phone);
            $results['operator'] = $wa->send($operatorPhone, $message_operator);
        }

        if ($spk->driver?->phone) {
            $driverPhone = $this->formatPhone($spk->driver->phone);
            $results['driver'] = $wa->send($driverPhone, $message_driver);
        }

        $successCount = collect($results)->filter(fn($r) => $r && ($r['ok'] ?? false))->count();

        if ($successCount > 0) {
            $spk->update(['send_report' => 1]);
        }

        return response()->json([
            'success' => $successCount > 0,
            'message' => "Pesan terkirim ke {$successCount} kontak.",
            'data'    => $results,
        ]);
    }

    // =========================================================
    // ✅ SEND ONLY DRIVER
    // =========================================================
    public function send_wa_driver(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'spk'   => 'required|string',
        ]);

        $spk = Spks::with(['airport_shuttles', 'guests', 'operator', 'driver', 'transport', 'destinations'])
            ->find($request->spk);

        if (!$spk) {
            return response()->json(['success' => false, 'message' => 'SPK tidak ditemukan.']);
        }

        if (!$spk->driver?->phone) {
            return response()->json(['success' => false, 'message' => 'Nomor driver kosong.']);
        }

        $spk_date = date('d M Y', strtotime($spk->spk_date));
        $driver_name = $spk->driver?->name ?? '-';
        $spk_driver_link = 'https://online.balikamitour.com/spk/' . $spk->id . '/' . $spk->spk_number;

        $message_driver =
            "Halo {$driver_name},\n\n" .
            "SPK untuk tanggal {$spk_date} telah diterbitkan pada link berikut:\n\n" .
            "{$spk_driver_link}\n\n" .
            "- _Pastikan kondisi kendaraan dalam keadaan bersih, aman, dan siap digunakan._\n" .
            "- _Tiba tepat waktu sesuai jadwal penjemputan yang telah ditentukan._\n" .
            "- _Selalu mengemudi dengan baik, hati-hati, dan mematuhi rambu lalu lintas._\n" .
            "- _Pastikan kenyamanan penumpang selalu menjadi prioritas utama._\n" .
            "- _Selalu lakukan Check-in di lokasi destinasi sesuai ketentuan yang tertera pada SPK._\n" .
            "- _Jaga sikap profesional, ramah, dan menjaga nama baik perusahaan._\n\n" .
            "Terima kasih,\n*Bali Kami Tour*";

        $wa = new WhatsappService();

        $driverPhone = $this->formatPhone($spk->driver->phone);
        $result = $wa->send($driverPhone, $message_driver);

        return response()->json([
            'success' => $result && ($result['ok'] ?? false),
            'message' => ($result && ($result['ok'] ?? false)) ? "Pesan berhasil dikirim ke driver" : "Gagal kirim ke driver",
            'data'    => $result,
        ]);
    }

    // =========================================================
    // ✅ SEND ONLY OPERATOR
    // =========================================================
    public function send_wa_operator(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'spk'   => 'required|string'
        ]);

        $spk = Spks::with(['airport_shuttles', 'guests', 'operator', 'driver', 'transport', 'destinations'])
            ->find($request->spk);

        if (!$spk) {
            return response()->json(['success' => false, 'message' => 'SPK tidak ditemukan.']);
        }

        if (!$spk->operator?->phone) {
            return response()->json(['success' => false, 'message' => 'Nomor operator kosong.']);
        }

        $operator_name = $spk->operator?->name ?? '-';
        $spk_date = date('d M Y', strtotime($spk->spk_date));

        $driver_name = $spk->driver?->name ?? '-';
        $driver_phone = $spk->driver?->phone ?? '-';

        $vehicle_brand = $spk->transport?->brand ?? '-';
        $vehicle_name  = $spk->transport?->name ?? '-';
        $vehicle_no    = $spk->plate_number ?? '-';

        $spk_operator_link = 'https://online.balikamitour.com/spk-report/' . $spk->id;

        $guestList = $spk->guests->map(function ($guest) {
            return "- {$guest->name} ({$guest->name_mandarin})";
        })->implode("\n");

        $shuttleList = $spk->airport_shuttles->map(function ($shuttle) {
            $shuttle_date = date('d M Y (H:i)', strtotime($shuttle->date));
            return "- ({$shuttle_date}) {$shuttle->flight_number}";
        })->implode("\n");

        $dstList = $spk->destinations->map(function ($dst) {
            return "- {$dst->destination_name}";
        })->implode("\n");

        $message_operator =
            "Halo {$operator_name},\n\n" .
            "*Your order*\n" .
            "*Order Number:* _{$spk->order_number}_\n" .
            "*Date:* _{$spk_date}_\n" .
            "*Type:* _{$spk->type}_\n\n";

        if ($spk->type === 'Airport Shuttle') {
            $message_operator .=
                "*Guest Name:*\n{$guestList}\n\n" .
                "*Flight:*\n{$shuttleList}\n\n" .
                "*Destination:*\n{$dstList}\n\n";
        } else {
            $message_operator .=
                "*Guest Name:*\n{$guestList}\n\n" .
                "*Destination:*\n{$dstList}\n\n";
        }

        $message_operator .=
            "*Driver:* _{$driver_name}_\n" .
            "*Hp:* +{$driver_phone}\n" .
            "*Vehicle:* _{$vehicle_brand} - {$vehicle_name}_\n" .
            "*Police Number:* _{$vehicle_no}_\n\n" .
            "Untuk melihat detail SPK, gunakan link berikut:\n" .
            "{$spk_operator_link}\n\n" .
            "Terima kasih,\n*online.balikamitour.com*";

        $wa = new WhatsappService();

        $operatorPhone = $this->formatPhone($spk->operator->phone);
        $result = $wa->send($operatorPhone, $message_operator);

        return response()->json([
            'success' => $result && ($result['ok'] ?? false),
            'message' => ($result && ($result['ok'] ?? false)) ? "Pesan berhasil dikirim ke operator" : "Gagal kirim ke operator",
            'data'    => $result,
        ]);
    }

    // =========================================================
    // STATUS / QR / DISCONNECT
    // =========================================================
    public function updateStatus(Request $request)
    {
        file_put_contents(
            storage_path('app/wa_status.json'),
            json_encode([
                'connected' => $request->connected,
                'phone'     => $request->phone
            ])
        );

        return response()->json(['saved' => true]);
    }

    public function status()
    {
        try {
            $res = Http::timeout(5)->get($this->base . "/status");
            return response()->json($res->json());
        } catch (\Exception $e) {
            return response()->json([
                'connected' => false,
                'status'    => 'ERROR',
                'message'   => $e->getMessage()
            ]);
        }
    }

    public function qr()
    {
        try {
            $res = Http::timeout(5)->get($this->base . "/qr");
            return response()->json($res->json());
        } catch (\Exception $e) {
            return response()->json([
                'qr'      => null,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function disconnect()
    {
        try {

            $res = Http::timeout(10)->post($this->base . "/logout");

            return response()->json($res->json());

        } catch (\Exception $e) {

            return response()->json([
                'ok' => false,
                'message' => $e->getMessage()
            ]);

        }
    }
    public function reset()
    {
        try {

            $res = Http::timeout(10)->post($this->base . "/reset");

            return response()->json($res->json());

        } catch (\Exception $e) {

            return response()->json([
                'ok' => false,
                'message' => $e->getMessage()
            ]);

        }
    }

    // (opsional)
    public function connect()
    {
        $qrcode = WhatsappService::generateQRCode();
        if (!$qrcode) {
            return response()->json(['ok' => false, 'message' => 'QR not available yet.'], 500);
        }

        return response()->json(['ok' => true, 'qrcode' => $qrcode]);
    }

    // Print SPK dengan QR Code
    public function spk_report($id)
    {
        $now = Carbon::now()->format('Y-m-d');
        $expired_date = Carbon::now()->subDay()->format('Y-m-d');
        $spk = Spks::with('reservation', 'destinations', 'guests')->findOrFail($id);

        return view('admin.transportmanagement.spks.report_spk', compact('spk', 'now', 'expired_date'));
    }

    public function ping()
    {
        try {

            $res = Http::timeout(5)->get($this->base . "/health");

            return response()->json([
                'ok' => true,
                'server' => 'running',
                'data' => $res->json()
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'ok' => false,
                'server' => 'offline',
                'message' => $e->getMessage()
            ], 500);

        }
    }
    public function session()
    {
        try {

            $res = Http::timeout(5)->get($this->base . "/status");

            return response()->json([
                'ok' => true,
                'session' => $res->json()
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'ok' => false,
                'message' => $e->getMessage()
            ]);

        }
    }
    public function device()
    {
        try {

            $res = Http::timeout(5)->get($this->base . "/device");

            return response()->json([
                'ok' => true,
                'device' => $res->json()
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'ok' => false,
                'message' => $e->getMessage()
            ]);

        }
    }
    public function restart()
    {
        try {

            $res = Http::timeout(10)->post($this->base . "/reload");

            return response()->json([
                'ok' => true,
                'message' => 'WhatsApp restarting',
                'data' => $res->json()
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'ok' => false,
                'message' => $e->getMessage()
            ]);

        }
    }
}
