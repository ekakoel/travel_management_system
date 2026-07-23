# Backend Legacy UI Audit

Tanggal audit: 2026-07-22

Audit ini memindai view backend/admin untuk pola legacy yang harus dihindari pada standardisasi backend UI:

- `card-box`, `card-box-title`, `card-box-footer`
- `btn-view`, `btn-edit`, `btn-delete`, `btn btn-*`
- `alert alert-*`, `alert-form`
- inline `style=`, inline `<script>`, `onkeyup`, `onclick`
- `.data-table`, `status-active`, `status-draft`

## Status domain yang sudah bersih/ditutup

- [x] Activities backend sudah selesai sampai Phase 6.
- [x] Hotels workspace sudah selesai sampai Phase 8J.
- [x] Guides index sudah memakai asset architecture backend.
- [x] Drivers index sudah memakai asset architecture backend.
- [x] Dashboard, Company Profile, Footer Manager, Reviews, Terms, Currency, User Manager, Orders Admin index/detail, dan Transport Management utama sudah memiliki banyak shared UI guard.

## Temuan prioritas global

File dengan temuan legacy terbanyak:

| Prioritas | File | Jumlah temuan |
| --- | --- | ---: |
| 1 | `resources/views/admin/order/order-wedding-detail.blade.php` | 443 |
| 2 | `resources/views/admin/reservation_detail.blade.php` | 197 |
| 3 | `resources/views/admin/update-wedding-services.blade.php` | 87 |
| 4 | `resources/views/admin/wedding-hotel-detail.blade.php` | 67 |
| 5 | `resources/views/admin/transportmanagement/detail-spk.blade.php` | 63 |
| 6 | `resources/views/admin/partner-detail.blade.php` | 53 |
| 7 | `resources/views/admin/create-hotel-order.blade.php` | 51 |
| 8 | `resources/views/admin/promotion.blade.php` | 49 |
| 9 | `resources/views/admin/toursadmindetail.blade.php` | 48 |
| 10 | `resources/views/admin/villas/show.blade.php` | 47 |

## Kandidat domain berikutnya

### Opsi A - Wedding Operations Workspace

Alasan:

- Jumlah legacy paling besar ada di `order-wedding-detail`, `update-wedding-services`, `wedding-hotel-detail`, dan banyak form `backend/operations/weddings`.
- Domain ini tampaknya besar dan kompleks, sehingga paling perlu roadmap sendiri seperti Hotels/Activities.

Risiko:

- Scope besar; sebaiknya dipecah menjadi beberapa phase.
- Kemungkinan banyak coupling order/wedding/reservation.

Rekomendasi:

- [ ] Buat roadmap `Wedding Operations Backend Standardization`.
- [ ] Mulai dari audit route/controller/view/asset.
- [ ] Pindahkan index/detail utama ke `resources/views/backend/operations/weddings`.
- [ ] Buat wrapper legacy di `resources/views/admin/weddings*.blade.php`.
- [ ] Standarisasi hero/toolbar/KPI/panel/detail sebelum menyentuh CRUD form kompleks.

### Opsi B - Reservations Workspace

Alasan:

- `resources/views/admin/reservation_detail.blade.php` memiliki 197 temuan legacy.
- Banyak action reservation sudah berada di `resources/views/backend/operations/reservations/actions`.

Risiko:

- Reservation biasanya menyentuh order, invoice, booking code, hotel/activity/tour/transport sekaligus.

Rekomendasi:

- [ ] Mulai dari detail reservation.
- [ ] Pisahkan partial/action yang paling sering dipakai.
- [ ] Buat asset domain `resources/backend/js/operations/reservations`.

### Opsi C - Tours Workspace

Alasan:

- `backend/tours/create-tour.blade.php`, `backend/tours/update-tour.blade.php`, `admin/toursadmin.blade.php`, dan `admin/toursadmindetail.blade.php` masih memiliki legacy UI.
- Polanya mirip Activities, sehingga bisa distandarisasi lebih cepat daripada Wedding/Reservations.

Risiko:

- Ada repeater lokasi tour dengan inline script dan inline style yang perlu dipindah hati-hati ke JS domain.

Rekomendasi:

- [ ] Buat roadmap `Tours Backend Standardization`.
- [ ] Mulai dari route/view wrapper dan asset architecture.
- [ ] Setelah itu standardisasi index/detail, lalu create/update form.

## Rekomendasi pilihan berikutnya

Mulai dari **Tours Workspace** bila ingin progress cepat dan risiko lebih kecil, karena pola CRUD/service-nya paling dekat dengan Activities.

Mulai dari **Wedding Operations Workspace** bila ingin membersihkan area legacy paling besar dan berdampak paling luas.

Pilihan teknis terbaik saya: **Tours Workspace dulu**, lalu Wedding. Tours akan menjadi template tambahan setelah Activities untuk resource product backend, sehingga Wedding yang lebih rumit punya dua pembanding standard: Hotels dan Activities/Tours.
