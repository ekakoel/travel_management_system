# Architecture and Project Structure

Status: active
Updated: 2026-07-28

Project sedang transisi dari namespace legacy ke struktur target. Jangan membuat file aktif baru di folder legacy kecuali sedang migrasi bertahap dengan alasan jelas.

## Struktur Target

```text
app/Http/Controllers/
  Frontend/LandingPage/
  Frontend/Home/
  Backend/Admin/
  Backend/Sales/
  Backend/Operations/
  Api/

resources/views/
  frontend/landing-page/
  frontend/home/
  frontend/auth/
  frontend/shared/
  backend/
  backend/shared/

resources/frontend/
  js/landing-page/
  js/home/
  js/shared/
  scss/landing-page/
  scss/home/
  scss/shared/

resources/backend/
  js/admin|sales|operations|shared/
  scss/admin|sales|operations|components/
```

## Area Ownership

- Public tanpa login: `resources/views/frontend/landing-page`.
- User login/customer: `resources/views/frontend/home`.
- Staff/admin/internal: `resources/views/backend` atau legacy `resources/views/admin` saat route lama belum dimigrasi.
- Shared frontend saja: `resources/views/frontend/shared`.
- Shared lintas frontend/backend hanya dibuat bila ada minimal dua consumer nyata.

## Route Dan Controller

- Public frontend target: `frontend.landing.*`.
- Authenticated frontend target: `frontend.home.*`.
- Backend target: `backend.*`.
- Route legacy seperti `view.*`, `func.*`, dan URL lama masih aktif di beberapa domain. Jangan rename tanpa audit referensi.
- Gunakan route name di Blade/controller, bukan URL hardcoded.

## Asset Build

- Source SCSS/JS baru masuk `resources/frontend` atau `resources/backend`.
- Output build dikelola Laravel Mix melalui `webpack.mix.js`.
- Jangan edit CSS hasil build sebagai source of truth kecuali project memang masih punya legacy public asset aktif untuk halaman tersebut.

## Legacy Folder Yang Tidak Boleh Ditambah

- `resources/views/home`
- `resources/views/form`
- `resources/views/order`
- `resources/views/main`
- folder page-level legacy lain di `public/css/pages` atau `public/frontend/js/pages` kecuali sedang hardening file aktif yang belum dimigrasi

## Table Standard

- Table Display Standard memakai prinsip multi-display.
- Backend baru/refactor harus memakai class `.backend-table-wrap`, `.backend-table`, `.backend-table-actions`, `.backend-table-empty`, `.backend-table-card-list`, `.backend-table-card`, `.backend-table-card__header`, dan `.backend-table-card-grid`.
- Tabel harus responsive untuk mobile, tablet, desktop, dan wide desktop.
- Hindari horizontal scroll sebagai pengalaman utama untuk backend baru.
- Gunakan `overflow-wrap: anywhere`, `table-layout: fixed`, dan alternate card/list view untuk data padat.
- Pastikan cell/panel memakai `min-width: 0` agar konten panjang tidak mendorong layout keluar viewport.
- Standard global frontend table berada di `resources/frontend/scss/components/frontend-components.scss`.

## Definition Of Done Per Domain

1. Route dan middleware masih benar.
2. Controller return view target yang benar.
3. Include/extends valid.
4. Asset terdaftar di `webpack.mix.js` bila memakai build.
5. Language key lengkap.
6. Test/route/view/build yang relevan dijalankan atau alasan tidak dijalankan dicatat.
7. Dokumentasi tracker diperbarui bila workflow, UI behavior, atau struktur berubah.

## Guard Rule Untuk File Baru

- Public tanpa login -> `frontend/landing-page`.
- User login/customer -> `frontend/home`.
- Staff/admin/internal -> `backend`.
- Shared hanya jika minimal dua consumer nyata.
