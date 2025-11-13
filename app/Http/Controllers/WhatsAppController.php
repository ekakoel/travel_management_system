<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Spks;
use Illuminate\Http\Request;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\Http;

class WhatsAppController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'spk' => 'required|string',
            'url' => 'required|string',
        ]);
        
        $spk = Spks::with(['airport_shuttles','guests','operator','driver','transport','destinations'])
            ->find($request->spk);

        if (!$spk) {
            return response()->json(['success' => false, 'message' => 'SPK tidak ditemukan.']);
        }

        $url = $request->url;
        $operator_name = $spk->operator?->name;
        $spk_date = date('d M Y', strtotime($spk->spk_date));
        $vehicle_brand = $spk->transport?->brand;
        $vehicle_no = $spk->plate_number ?? '-';
        $vehicle_name = $spk->transport?->name;
        $driver_name = $spk->driver?->name;
        $driver_phone = $spk->driver?->phone;
        $spk_driver_link = $url;
        $spk_operator_link = 'https://online.balikamitour.com/spk-report/'.$spk->id;

        // === Daftar tamu, shuttle, dan destinasi ===
        $guestList = $spk->guests->map(function ($guest) {
            return "- {$guest->name} ({$guest->name_mandarin})";
        })->implode("\n");

        $shuttleList = $spk->airport_shuttles->map(function ($shuttle) {
            return "- ({date('d M Y H:i', strtotime($shuttle->date)}) {$shuttle->flight_number}";
        })->implode("\n");

        $dstList = $spk->destinations->map(function ($dst) {
            return "- {$dst->destination_name}";
        })->implode("\n");

        // === 1. Template untuk Operator ===
        $message_operator = 
            "Halo {$operator_name},\n\n" .
            "*Your order*\n" .
            "*Order Number:* _{$spk->order_number}_\n" .
            "*Date:* _{$spk_date}_\n" .
            "-------------------------------------------------------------\n";

        // tambahkan detail khusus jika type-nya "Airport Shuttle"
        if ($spk->type === 'Airport Shuttle') {
            $message_operator .=
                "*Guest Name:*\n{$guestList}\n" .
                "*Flight:*\n{$shuttleList}\n" .
                "*Destination:*\n{$dstList}\n" .
                "-------------------------------------------------------------\n";
        }else{
            $message_operator .=
                "*Guest Name:*\n{$guestList}\n" .
                "*Destination:*\n{$dstList}\n" .
                "-------------------------------------------------------------\n";
        }

        // lanjutkan pesan operator
        $message_operator .=
            "*Driver:* _{$driver_name}_\n" .
            "*Hp:* +_{$driver_phone}_\n" .
            "*Vehicle:* _{$vehicle_brand} - {$vehicle_name}_\n" .
            "*Police Number:* _{$vehicle_no}_\n" .
            "-------------------------------------------------------------\n" .
            "Untuk melihat detail SPK, gunakan link berikut:\n" .
            "{$spk_operator_link}\n\n" .
            "Terima kasih,\n*online.balikamitour.com*";

        // === 2. Template untuk Driver ===
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

        // === Kirim pesan via WhatsAppService ===
        $wa = new WhatsappService();
        $results = [];

        if ($spk->operator?->phone) {
            $results['operator'] = $wa->send($spk->operator->phone, $message_operator);
        }

        if ($spk->driver?->phone) {
            $results['driver'] = $wa->send($spk->driver->phone, $message_driver);
        }

        // === Cek hasil kirim ===
        $successCount = collect($results)->filter(fn($r) => $r && ($r['ok'] ?? false))->count();
        if ($successCount > 0) {
            $spk->update(['send_report' => 1]);
        }

        return response()->json([
            'success' => $successCount > 0,
            'message' => "Pesan terkirim ke {$successCount} kontak.",
            'data' => $results,
        ]);
    }

    
    // Print SPK dengan QR Code
    public function spk_report($id)
    {
        $now = Carbon::now()->format('Y-m-d');
        $expired_date = Carbon::now()->subDay()->format('Y-m-d');
        $spk = Spks::with('reservation', 'destinations', 'guests')->findOrFail($id);

        return view('admin.transportmanagement.spks.report_spk', compact('spk','now','expired_date'));
    }
}

