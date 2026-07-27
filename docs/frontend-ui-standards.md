# Frontend UI Standards

Status: active
Updated: 2026-07-27

Standar ini berlaku untuk public frontend dan authenticated customer/agent area.

## Baseline Aktif

- Hotel availability/check price menjadi baseline shell frontend.
- Activity Detail menjadi baseline modal order service.
- Shared component utama:
  - `resources/views/partials/frontend-loop-swiper.blade.php`
  - `resources/frontend/scss/components/frontend-order-modal.scss`
  - `resources/frontend/js/components/form-submission-guard.js`

## Struktur Halaman

1. Breadcrumb ringkas.
2. Topband/header dengan satu H1.
3. Summary metadata bila ada informasi utama.
4. Main content dengan hierarchy jelas.
5. Sidebar/action panel jika ada next action.
6. Related/supporting content jika relevan.

## UX Rules

- User harus langsung tahu halaman apa, data apa yang ditampilkan, dan action berikutnya.
- CTA primer harus jelas dan tidak bersaing dengan banyak tombol besar.
- Jangan mengulang nama entity secara berlebihan.
- Empty state harus membantu user lanjut.
- Semua tanggal frontend baru tampil `YYYY-MM-DD`; datetime `YYYY-MM-DD HH:mm`.
- Gunakan `dateFormat()` dan `dateTimeFormat()` di Blade bila tersedia.
- Semua submit penting wajib spinner/loading sesuai `docs/form-submit-standard.md`.

## Visual Rules

- Ikuti shell, spacing, surface, breadcrumb, card, modal, dan action pattern existing.
- Hindari visual backend di halaman frontend.
- Jangan membuat card bertumpuk tanpa fungsi jelas.
- CSS page-level hanya untuk styling unik halaman, bukan menduplikasi primitive global.
- Swiper/list looping memakai shared `frontend-loop-swiper`.

## Modal Order

Untuk modal order layanan, ikuti `docs/frontend-order-modal-standard.md`.

## Checklist

1. H1 hanya satu.
2. Breadcrumb canonical.
3. CTA utama jelas.
4. Responsive mobile/tablet/desktop.
5. Copy memakai language key.
6. CSS/JS terpisah dari Blade.
7. Submit/loading state ada untuk aksi penting.
8. `docs/frontend-roadmap.md` diperbarui untuk perubahan frontend.
