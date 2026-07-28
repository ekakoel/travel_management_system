# Frontend UI Standards

Status: active
Updated: 2026-07-28

Standar ini berlaku untuk public frontend dan authenticated customer/agent area.

## Baseline Aktif

- Hotel availability/check price menjadi baseline shell frontend.
- Activity Detail menjadi baseline modal order service.
- Shared component utama:
  - `resources/views/partials/frontend-loop-swiper.blade.php`
  - `resources/frontend/scss/components/frontend-buttons.scss`
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
- Semua submit penting wajib spinner/loading sesuai `docs/decisions/form-submit-standard.md`.

## Visual Rules

- Ikuti shell, spacing, surface, breadcrumb, card, modal, dan action pattern existing.
- Hindari visual backend di halaman frontend.
- Jangan membuat card bertumpuk tanpa fungsi jelas.
- CSS page-level hanya untuk styling unik halaman, bukan menduplikasi primitive global.
- Swiper/list looping memakai shared `frontend-loop-swiper`.

## Button System

Semua halaman frontend baru wajib memakai reusable button system dari:

```text
resources/frontend/scss/components/frontend-buttons.scss
```

Class standar untuk markup baru:

```html
<button type="button" class="ui-btn ui-btn--primary">Primary Action</button>
<a href="#" class="ui-btn ui-btn--secondary">Secondary Action</a>
<button type="button" class="ui-btn ui-btn--danger">Danger Action</button>
```

Modifier yang tersedia:

- `ui-btn--primary` untuk CTA utama.
- `ui-btn--secondary` untuk aksi sekunder, back, cancel ringan, atau outline-style action.
- `ui-btn--danger` untuk aksi destruktif atau pembatalan kuat.
- `ui-btn--success` untuk aksi konfirmasi positif bila berbeda dari CTA utama.
- `ui-btn--info` untuk aksi informatif.
- `ui-btn--sm` dan `ui-btn--lg` untuk ukuran.
- `ui-btn--block` untuk tombol full width.
- `ui-btn--link` untuk link action yang harus terlihat ringan.
- `ui-btn--icon` untuk tombol icon-only.

Aturan implementasi:

- Jangan membuat style button baru di SCSS page-level bila kebutuhan dapat dipenuhi oleh `ui-btn` dan modifier existing.
- Page-level class boleh ditambahkan untuk layout atau JS hook, tetapi tidak boleh mengganti warna, radius, typography, hover, focus, disabled, atau spacing utama button tanpa alasan yang terdokumentasi.
- Tombol submit penting tetap wajib mengikuti `docs/decisions/form-submit-standard.md` untuk loading state, idempotency, dan POST -> Redirect -> GET.
- Copy tombol tetap wajib memakai language key sesuai `docs/decisions/multi-language-standard.md`.
- Tombol icon-only wajib punya `aria-label`.
- Existing Bootstrap-style frontend buttons (`btn btn-primary`, `btn btn-outline-secondary`, dan sejenisnya) didukung sebagai compatibility layer, tetapi markup baru harus memakai `ui-btn`.
- Backend/admin tidak memakai `ui-btn`; backend tetap mengikuti `docs/decisions/backend-ui-standards.md`.

## Modal Order

Untuk modal order layanan, ikuti `docs/decisions/frontend-order-modal-standard.md`.

## Picker System

Detail lengkap: `docs/decisions/frontend-picker-standard.md`.

Semua input tanggal, waktu, dan rentang tanggal frontend baru wajib memakai global picker system:

```html
<input type="text" class="form-control" data-ui-picker="date" data-ui-picker-format="YYYY-MM-DD">
<input type="text" class="form-control" data-ui-picker="datetime" data-ui-picker-format="YYYY-MM-DD HH:mm">
<input type="text" class="form-control" data-ui-picker="range" data-ui-picker-format="YYYY-MM-DD">
```

Kontrak aktif:

- JS global: `resources/frontend/js/components/frontend-pickers.js`.
- SCSS global: `resources/frontend/scss/components/frontend-pickers.scss`.
- Library utama: Date Range Picker via `daterangepicker` dan `moment` yang dimuat oleh frontend layout.
- Mode standar: `date`, `range`, `datetime`, `time`, `month`, `year`.
- Tampilan tanggal frontend baru: `YYYY-MM-DD`; datetime: `YYYY-MM-DD HH:mm`.
- Date and time picker serta time picker memakai tombol Apply/Cancel secara default.
- Jangan menambah initializer `daterangepicker`, `flatpickr`, atau inline date script di Blade/page-level bila bisa memakai `data-ui-picker`.
- Compatibility lama `.datetimepicker`, `.date-picker`, `data-booking-datetime`, `data-transport-datetime`, dan `input[name="checkincout"]` di-auto-init oleh global system selama migrasi.
- Jangan mengubah format value yang dikirim ke backend tanpa audit controller/request terkait.

## Checklist

1. H1 hanya satu.
2. Breadcrumb canonical.
3. CTA utama jelas.
4. Responsive mobile/tablet/desktop.
5. Copy memakai language key.
6. CSS/JS terpisah dari Blade.
7. Submit/loading state ada untuk aksi penting.
8. Button memakai `ui-btn` dan modifier standar, atau compatibility class lama hanya saat migrasi legacy.
9. `docs/decisions/frontend-roadmap.md` diperbarui untuk perubahan frontend.
