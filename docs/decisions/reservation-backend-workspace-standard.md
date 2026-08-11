# Reservation Backend Workspace Standard

Status: active
Updated: 2026-08-10

## Scope

Dokumen ini mengatur halaman backend `/reservation` untuk posisi `developer`,
`reservation`, dan `weddingRsv`. Dokumen ini tidak mengubah lifecycle order,
payment, invoice, fulfillment, SPK, atau status Reservation yang sudah dibuat
oleh flow masing-masing public service.

## Work Queue Contract

- `/reservation` adalah assigned work queue. Record yang ditampilkan wajib
  memiliki `reservations.adm_id = authenticated admin id`, `status = Active`,
  dan belum dihapus. Pending, Draft, Canceled, serta status non-Active lain
  tidak boleh masuk ke projection operasional ini.
- Query index memakai eager loading `agent` dan `invoice`, serta relation count
  untuk guest, order, dan SPK. Blade tidak melakukan query atau memuat seluruh
  tabel pendukung ke memori.
- KPI dan overdue indicator hanya merupakan operational projection. Keduanya
  tidak menulis atau menormalisasi status.
- Detail canonical memakai named route `view.reservation.detail` dengan URL
  `/reservation/{id}` dan numeric constraint. Direct access ke reservation
  non-Active harus dialihkan ke work queue dengan pesan yang jelas sebelum
  Blade operasional dimuat.
- Recent activity dashboard hanya boleh membuat link detail untuk reservation
  Active yang belum dihapus.

## Calendar Projection Contract

- Kalender `/reservation` adalah read-only operational projection dari
  collection assigned + Active yang sama dengan work queue. Kalender tidak
  membuat query per tanggal, endpoint mutasi, atau request AJAX tambahan.
- `checkin` menjadi event start. `checkout` diperlakukan inklusif untuk user,
  lalu dikirim ke FullCalendar sebagai exclusive end `checkout + 1 day` sesuai
  kontrak all-day calendar internasional.
- Event menampilkan reference dan service. Detail event menampilkan agent,
  service period, guest/SPK count, invoice/due date, dan operational note tanpa
  nilai finansial atau data pribadi guest.
- Note memakai `reservations.additional_info` yang sudah di-strip dari HTML dan
  dibatasi panjangnya. Jika kosong, sistem menampilkan note operasional
  terlokalisasi berdasarkan upcoming, in-service, active, atau overdue.
- Kalender wajib read-only, memakai event limit/popover untuk tanggal padat,
  month/week/list view, tampilan list pada mobile, legend yang tidak hanya
  bergantung pada warna, dan detail modal yang dapat menuju route canonical.
- Detail modal memakai shared `showBackendModal` dan satu
  `<x-backend.modal-close>` pada kanan atas; footer hanya memuat action membuka
  detail reservation.
- Search dan service filter memperbarui table/card dan event source dari memory
  dengan debounce; tidak boleh mengulang query database.

## Manual Reservation Contract

- Input operator hanya `agn_id`, `checkin`, dan `checkout`.
- Agent harus user aktif dengan `type = user`, `position = agent`, dan memiliki
  code. Actor/admin, service, action log, initial status, serta reservation
  number ditentukan server-side.
- Tanggal request canonical adalah `Y-m-d`; checkout tidak boleh mendahului
  checkin.
- Generator number memakai prefix agent + tanggal pembuatan dan suffix
  alphabetic tanpa batas satu huruf (`A` sampai `Z`, lalu `AA`, `AB`, dst.) di
  dalam transaction dan row lock.
- Manual reservation mempertahankan initial compatibility status `Draft`.
  Dokumen ini tidak mengubah status record dari flow public service. Karena
  work queue bersifat Active-only, Draft tidak dibuka melalui detail
  operasional dan baru tampil setelah diaktifkan oleh flow yang berwenang.

## Delete Safety

Hard delete melalui workspace hanya tersedia untuk manual Reservation kosong
dengan status `Draft`, tanpa invoice, order, atau guest. Assigned operator dapat
menghapus Draft miliknya; developer dapat menangani Draft operator lain.
Reservation public-service, aktif, atau yang telah memiliki data finansial dan
operasional tidak boleh dihapus melalui action ini.

## UI Contract

- Gunakan shared backend hero, breadcrumb toolbar, KPI, filter, panel, table,
  responsive card list, status badge, empty state, modal, form, action, required
  marker, date picker, dan submit guard.
- Kalender memakai FullCalendar yang sudah tersedia pada backend. Dilarang
  menambahkan CDN/library kalender kedua atau mengaktifkan drag, resize, create,
  dan mutation dari kalender operasional.
- Copy tersedia dalam English, Chinese Simplified, dan Chinese Traditional.
- Filter search dan service bersifat presentation-only dan tidak mengubah
  server data. Filter status tidak ditampilkan karena server contract sudah
  menetapkan status Active.
- Inline handler, query di Blade, hidden financial/authorization input, serta
  URL detail berbentuk `/reservation-{id}` tidak diperbolehkan.

### Detail Workspace

- Detail Active reservation canonical berada di
  `backend.operations.reservations.detail` dan memakai
  `x-backend.detail-layout`. Overview, manifest, linked services, serta trip
  notes berada pada main column; quick action, section navigation, invoice
  readiness, assignment context, dan admin note berada pada context side panel.
- Hero, breadcrumb toolbar, KPI, feedback, status badge, panel, button, form
  submit guard, action loading, dan modal close memakai primitive backend
  shared. Create invoice memakai route server-authoritative
  `view.reservation.invoice.store`; reference, actor, agent, bank default,
  invoice date, dan deadline 48 jam tidak diterima dari hidden input. Operasi
  ini idempotent dan tidak membuat invoice kedua untuk reservation yang sama.
- Data detail disiapkan oleh `ReservationDetailService`. Controller hanya
  melakukan Active/non-deleted guard lalu menyerahkan projection ke view.
  Projection mengambil satu collection order milik reservation, eager-load
  relation yang digunakan, dan tidak boleh memuat seluruh tabel Orders, Users,
  Optional Rates, Guide, atau Driver.
- KPI detail bersifat read-only: durasi layanan, manifest guest, linked order,
  dan SPK tidak menulis status atau nilai finansial.
- Detail menampilkan seluruh linked order non-Deleted, termasuk service lampau
  dan mendatang. Detail finansial/service-specific tetap dikelola melalui named
  route `admin.order.show`; halaman agregat tidak menduplikasi kalkulasi JSON,
  price engine, modal order, atau mutation manifest.
- Blade canonical dipecah menjadi partial overview, manifest, services,
  trip-notes, dan context. Blade tidak melakukan query, `json_decode`, atau
  pencarian collection untuk membangun projection.
- Navigasi section dan print summary ditangani asset domain reservation tanpa
  inline handler. View canonical tidak memuat modal legacy.
- Print Summary mengikuti kontrak global `.print-area` karena stylesheet legacy
  menyembunyikan seluruh `body` saat media print. Hasil cetak memakai header
  reservation terlokalisasi, A4 portrait, tanpa hero, toolbar, KPI, context
  action, duplicate mobile card, atau action linked order; main overview,
  manifest, linked services, dan trip notes tetap terlihat.

## Verification

- Route, middleware, Form Request authorization, date/agent validation.
- Assigned + Active scope dan eager-loaded projection.
- Redirect aman untuk direct access ke Pending/Draft/non-Active.
- Number generation setelah suffix `Z`.
- Server actor dan immutable service/action defaults.
- Delete guard untuk non-Draft atau Reservation yang memiliki dependency.
- Blade compile, asset compile, responsive table/card, filter, modal restore,
  required marker, dan duplicate-submit guard.
- Inclusive/exclusive date projection, sanitized note, Active-only event source,
  localized calendar controls, read-only behavior, mobile list view, dan event
  limit untuk tanggal padat.
