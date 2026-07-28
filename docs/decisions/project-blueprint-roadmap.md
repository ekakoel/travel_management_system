# Project Blueprint Roadmap

Status: active
Updated: 2026-07-27

Balikami Tour adalah travel management system untuk public service browsing, booking/order customer atau agent, dan operasi internal staff.

## Stack Saat Ini

- Laravel 10, PHP compatible `^7.3|^8.0`.
- MySQL/MariaDB.
- Vue 2 dan Laravel Mix.
- Backend UI: AdminLTE legacy plus shared backend components.
- Frontend UI: Blade, SCSS/JS compiled assets, shared frontend components.
- Auth/API: Laravel UI auth, Passport, Sanctum.
- Integrations: DomPDF, Excel import/export, Pusher, Firebase, Google API, WhatsApp bot proxy.

## Domain Utama

- Public website: home, accommodations/hotels, activities, tours, transports, villas, static pages, reviews, agent registration.
- Authenticated frontend: profile, orders dashboard, order detail/edit/history, booking/payment flows.
- Backend operations: products/services, hotel/tour/activity/transport management, reservations, orders, SPK transport management, invoices/reports, agents/users/roles/modules, currency, content settings.
- API: profile/product/category/tag, review link, Doku webhook, WhatsApp proxy.

## Arsitektur Target

- Business logic berpindah dari controller gemuk ke Form Request, service, view model, dan helper domain.
- View aktif mengikuti `resources/views/frontend/...` dan `resources/views/backend/...`.
- Asset source berada di `resources/frontend` dan `resources/backend`.
- Language key lengkap untuk semua UI aktif.
- Database schema berubah hanya lewat migration baru.

## Prioritas Berikutnya

1. Stabilkan perubahan SPK public report dan WhatsApp sharing.
2. Kurangi route/controller legacy secara bertahap per domain.
3. Lanjutkan backend UI cleanup pada halaman operasi yang masih memakai markup legacy.
4. Lengkapi test database isolation sebelum menjalankan suite feature besar.
5. Audit language key untuk backend legacy yang masih hardcoded.

## Bukan Aturan Aktif

Roadmap ini memberi arah. Aturan implementasi tetap berada di dokumen standar seperti `docs/coding-standards.md`, `docs/architecture.md`, dan standar UI/database terkait.
