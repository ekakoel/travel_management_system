---
title: Public Service Completion Scheduler Proposal
status: proposed
updated_at: 2026-07-28
applies_to:
  - accommodation
  - public-transport
  - tour-package
  - activity
---

# Public Service Completion Scheduler Proposal

Dokumen ini adalah proposal registrasi scheduler, bukan konfigurasi aktif.
Kontrak fulfillment aktif berada di `docs/status-contract.md`.

Completion command untuk empat public service tersedia pada implementasi, tetapi
pemeriksaan read-only 2026-07-28 menemukan bahwa empat command berikut belum
didaftarkan di `app/Console/Kernel.php`. Task dokumentasi ini tidak mengaktifkan
atau mengubah scheduler.

Jika aktivasi disetujui pada task coding terpisah, kandidat registrasinya:

```php
$schedule->command('accommodation:complete-eligible-orders')
    ->everyFifteenMinutes()
    ->withoutOverlapping();

$schedule->command('transport:complete-eligible-orders')
    ->everyFifteenMinutes()
    ->withoutOverlapping();

$schedule->command('tour:complete-eligible-orders')
    ->everyFifteenMinutes()
    ->withoutOverlapping();

$schedule->command('activity:complete-eligible-orders')
    ->everyFifteenMinutes()
    ->withoutOverlapping();
```

Sebelum aktivasi wajib diverifikasi:

- hanya order `Paid` dengan `completed_at = null` yang eligible;
- aturan waktu completion setiap service sudah disetujui;
- reservation terkait menjadi `Completed`;
- order tetap `Paid`;
- completion atomic, idempotent, dan memiliki audit trail;
- timezone, deployment scheduler, overlap, logging, retry, dan monitoring sudah
  siap;
- focused test berjalan pada database testing terpisah.
