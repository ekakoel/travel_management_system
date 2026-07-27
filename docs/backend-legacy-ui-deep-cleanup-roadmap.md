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

- [x] Pindahkan confirm delete ke JS backend operations reservations detail.
- [ ] Audit table/card/detail responsive saat halaman disentuh lagi.

## Phase LUI-3 - Wedding Order Detail Cleanup

- [ ] Audit markup legacy dan pisahkan style/behavior.

## Phase LUI-4 - Update Wedding Services Cleanup

- [ ] Audit form, modal, table, dan rich text behavior.

## Phase LUI-5 - Final Legacy UI Acceptance

- [ ] Tidak ada halaman backend aktif yang membuat primitive visual baru untuk komponen yang sudah punya shared style.
- [ ] Semua perubahan tercatat di roadmap terkait.
