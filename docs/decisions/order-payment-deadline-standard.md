# Order Payment Deadline Standard

Status: active
Updated: 2026-08-10

## Scope

Standard ini berlaku untuk seluruh commercial order pada tabel `orders`,
termasuk Accommodation (`Hotel`, `Hotel Promo`, `Hotel Package`), Transport,
Tour Package, Activity, dan service lama pada tabel yang sama. Wedding order
memiliki aggregate dan lifecycle terpisah sehingga tidak otomatis memakai
transisi ini.

## Deadline

- Payment window adalah tepat 48 jam (`2 x 24 jam`) sejak order disetujui dan
  invoice dibuat.
- `invoice_admins.due_date` adalah timestamp authoritative yang ditulis oleh
  backend. Nilai deadline dari request atau hidden input tidak dipercaya.
- Invoice yang dibuat ulang tidak memperpanjang deadline yang sudah berjalan.
- Invoice legacy dinormalisasi menjadi tepat 48 jam dari `inv_date`, dengan
  `created_at` sebagai fallback. Ini memperbaiki deadline lama yang lebih panjang
  maupun lebih pendek (misalnya aturan lama `travel date - 7 hari`).
- Nilai tanggal legacy yang tidak dapat diparse tidak boleh menghentikan batch;
  service memakai fallback timestamp yang valid lalu menormalisasi `due_date`.
- Tidak ada grace period tambahan.

## Auto-Cancel Eligibility

Scheduler hanya membatalkan ketika seluruh syarat berikut terpenuhi:

1. `orders.status = Approved`.
2. Order belum memiliki `completed_at`.
3. Reservation dan invoice yang terhubung valid.
4. Effective invoice deadline sudah tercapai.
5. Invoice masih memiliki outstanding balance.
6. Tidak ada payment confirmation berstatus `Pending`, `Valid`, atau `Paid`.
7. Reservation belum `Completed`.

Payment `Invalid` tidak memperpanjang deadline. Partial payment yang tercatat
sebagai `Valid`, atau receipt yang sedang `Pending`, melindungi order dari
auto-cancel sampai settlement/review selesai.

## Side Effects

Auto-cancel dilakukan dalam satu database transaction dengan row locking:

- order menjadi `Canceled`;
- reservation `Pending|Active` menjadi `Canceled`;
- invoice dan payment history tidak dihapus;
- satu `order_logs` row dibuat dengan action
  `Auto Cancel Payment Deadline` dan actor system;
- eksekusi ulang tidak membuat perubahan atau log duplikat.

Command `orders:auto-cancel-expired-payments` dijalankan setiap 15 menit dengan
`withoutOverlapping`. Request-time check pada halaman detail/payment memakai
service yang sama sehingga hasilnya konsisten dengan scheduler.

Environment production wajib menjalankan Laravel scheduler (`php artisan
schedule:run` setiap menit, atau scheduler worker yang setara). Registrasi di
`Console\Kernel` saja tidak mengeksekusi command tanpa process scheduler server.
