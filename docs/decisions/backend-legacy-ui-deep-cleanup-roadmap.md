# Backend Legacy UI Deep Cleanup Roadmap

Status: active
Updated: 2026-07-27

Tracker ringkas cleanup UI backend legacy.

## Standard Final

- Halaman backend memakai shared UI: `x-backend.page-hero`, `backend-page-toolbar`, `backend-panel`, `backend-section-header`, `backend-detail-layout`, `backend-table`, `backend-status-badge`, `backend-form-*`, dan `backend-button-*`.
- Asset backend berada di `resources/backend/scss` dan `resources/backend/js`.
- Inline style/script dan visual primitive legacy dihapus saat halaman disentuh.

## Phase LUI-1 - Create Hotel Order Cleanup

- [x] Load backend global bundle.
- [x] Ganti card utama ke `backend-panel`.
- [x] Pindahkan behavior ke `resources/backend/js/operations/orders-admin/create-hotel-order.js`.
- [x] Tambahkan helper style shared untuk inline amount/help icon.

## Phase LUI-2 - Reservation Detail Cleanup

- [x] Audit section dan modal terbesar di `resources/views/admin/reservation_detail.blade.php`.
- [x] Petakan section besar: Reservation, Flight, Agent, Guest, Guide, Driver, Accommodation, Activity/Tour, Transport, Restaurant, Include, Exclude, Remark, Sidebar Attention/Notes.
- [x] Pindahkan confirm delete ke JS backend operations reservations detail.
- [x] Pindahkan confirm/delete behavior ke asset backend domain.
- [x] Standardisasi `/reservation` sebagai assigned operational queue dengan
  hero, breadcrumb toolbar, KPI, filter, multi-display table/card, empty state,
  modal, form, status badge, dan action shared backend.
- [x] Pindahkan filter, modal restore, dan delete confirmation index ke asset
  `resources/backend/js/operations/reservations/index.js`.
- [x] Pindahkan query/eager loading, summary, manual number generation, dan
  guard delete Draft ke `ReservationAdminService`.
- [x] Ganti link detail legacy `/reservation-{id}` pada flow Reservation dengan
  named route canonical `view.reservation.detail` (`/reservation/{id}`).
- [x] Audit keseluruhan markup detail reservation legacy dan pecah menjadi
  partial responsive pada namespace `backend.operations.reservations`.
- [x] Pindahkan section detail ke partial domain responsive dan retire Blade legacy.
- [x] Pindahkan section navigation/print behavior ke asset backend domain.

## Phase LUI-3 - Wedding Order Detail Cleanup

- [ ] Audit markup legacy dan pisahkan style/behavior.

## Phase LUI-4 - Update Wedding Services Cleanup

- [ ] Audit form, modal, table, dan rich text behavior.

## Phase LUI-5 - Final Legacy UI Acceptance

- [ ] Tidak ada halaman backend aktif yang membuat primitive visual baru untuk komponen yang sudah punya shared style.
- [ ] Semua perubahan tercatat di roadmap terkait.
