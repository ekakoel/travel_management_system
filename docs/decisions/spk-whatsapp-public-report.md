# SPK WhatsApp Public Report

Status: active
Updated: 2026-07-27

Dokumen ini menjelaskan flow SPK public report dan WhatsApp sharing yang sedang aktif/dalam pengerjaan.

## Scope

- Backend SPK/transport management menyiapkan SPK, destination/check-in, dan action WhatsApp.
- Public report SPK memakai token publik agar driver/operator dapat membuka report tanpa membocorkan ID internal.
- WhatsApp endpoint aplikasi juga tersedia di API dengan proteksi `apikey`.

## Flow Utama

1. Staff/admin membuat atau mengelola SPK melalui transport management.
2. SPK memiliki identifier internal dan public token.
3. Route public report menerima token, bukan ID polos.
4. WhatsApp message membagikan link report token/canonical.
5. Driver/operator membuka public report dan melakukan check-in sesuai route yang tersedia.

## File Penting

- `routes/web.php`
- `routes/api.php`
- `app/Http/Controllers/TransportManagementController.php`
- `app/Http/Controllers/SpkReportController.php`
- `app/Http/Controllers/SpkWhatsappController.php`
- `app/Http/Controllers/WhatsAppController.php`
- `app/Models/Spks.php`
- `app/Models/SpkDestinations.php`
- `app/Models/SpksCheckins.php`
- `database/migrations/2026_07_24_111848_add_public_token_to_spks_table.php`
- `resources/views/admin/transportmanagement/spks`
- `resources/views/frontend/spks`

## Security Rules

- Public report harus memakai token yang sulit ditebak.
- Jangan expose credential WhatsApp, API key, atau data internal sensitif.
- Endpoint API WhatsApp tetap memakai middleware `apikey`.
- Backend action tetap harus berada di route yang punya auth/position check.
- Jangan hanya menyembunyikan tombol di Blade; authorization harus tetap di backend.

## Database Rules

- Tambahan kolom public token harus lewat migration baru.
- Backfill token harus idempotent dan tidak menghapus data SPK lama.
- Unique constraint hanya ditambahkan setelah data existing valid.

## Verification

- `php -l` pada controller/model/migration terkait.
- `php artisan route:list` filter `spk|whatsapp` sebagai audit read-only.
- Test database hanya boleh dijalankan setelah DB testing terisolasi.
