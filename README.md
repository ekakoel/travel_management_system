# Balikami Tour Travel Management System

Status: active
Updated: 2026-07-28

Balikami Tour adalah aplikasi Laravel untuk public travel service browsing, customer/agent booking, dan operasional internal travel agency.

## Wajib Dibaca Sebelum Mengubah Kode

1. `AGENTS.md`
2. `docs/README.md`
3. `docs/architecture.md`
4. `docs/coding-standards.md`
5. Dokumen standar/modul yang relevan
6. `docs/testing.md` sebelum test, migration, seeder, tinker, atau command database

Project ini memiliki database aktif. Jangan menjalankan command destructive seperti `migrate:fresh`, `migrate:refresh`, `db:wipe`, `truncate`, atau mass delete.

## Stack

- Laravel 10
- PHP compatible `^7.3|^8.0` sesuai `composer.json`
- MySQL/MariaDB
- Vue 2, Vue Router, Laravel Mix
- AdminLTE/Bootstrap legacy plus shared backend UI components
- Laravel Passport dan Sanctum
- DomPDF, Maatwebsite Excel, Livewire, Pusher, Firebase, Google API, WhatsApp bot proxy

## Area Aplikasi

- Public frontend: home, accommodations/hotels, activities, tours, transports, villas, static pages, reviews, agent registration.
- Authenticated frontend: profile, orders dashboard, detail/edit/history, booking/payment flow.
- Backend/internal: dashboard, users/roles/modules, hotels, tours, activities, transports, reservations, orders, finance, reports, SPK transport management, content settings.
- API: profile/product/category/tag, review links, Doku webhook, WhatsApp endpoints.

## Struktur Aktif

- Public frontend views: `resources/views/frontend/landing-page`
- Authenticated frontend views: `resources/views/frontend/home`
- Backend views: `resources/views/backend` and legacy `resources/views/admin`
- Frontend assets: `resources/frontend`
- Backend assets: `resources/backend`
- Routes: `routes/web.php`, `routes/api.php`
- Language files: `resources/lang/en`, `resources/lang/zh`, `resources/lang/zh-CN`

Beberapa route/controller/view legacy masih aktif. Jangan memindahkan atau menghapus tanpa audit referensi.

## Documentation

Mulai dari `docs/README.md`. Dokumen project sudah dipadatkan menjadi standar aktif, tracker ringkas, dan dokumen modul.

## Safe Verification

Aman untuk audit awal:

```bash
php -l path/to/file.php
php artisan route:list
composer validate
```

Sebelum menjalankan PHPUnit, migration, seeder, atau tinker, verifikasi database testing mengikuti `docs/testing.md`.

## WhatsApp API

Konfigurasi:

```env
WHATSAPP_BOT_URL=http://127.0.0.1:3000
WA_API_KEY=your-secret-key
```

Endpoint WhatsApp API berada di `POST /api/whatsapp/*` dan dilindungi header `X-API-KEY`.

Contoh endpoint:

```text
GET  /api/whatsapp/status
GET  /api/whatsapp/qr
POST /api/whatsapp/connect
POST /api/whatsapp/disconnect
POST /api/whatsapp/reload
POST /api/whatsapp/restart
POST /api/whatsapp/reset
POST /api/whatsapp/send
POST /api/whatsapp/send-driver
POST /api/whatsapp/send-operator
POST /api/whatsapp/send-both
```
