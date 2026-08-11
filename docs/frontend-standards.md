# Frontend UI Standards

Status: active
Updated: 2026-07-28

Standar ini berlaku untuk public frontend dan authenticated customer/agent area.
Dokumen ini adalah kontrak aktif dan wajib untuk seluruh task frontend
berikutnya. Jika aturan page-level, implementasi legacy, atau kebiasaan lama
bertentangan dengan dokumen ini, dokumen ini menjadi acuan untuk markup dan
perubahan baru. Compatibility legacy tetap dipertahankan sampai penggunaannya
diaudit dan dimigrasikan dengan aman.

## Tujuan

- Menjaga public frontend modern, bersih, ringan, konsisten, responsive, dan
  reusable.
- Mengarahkan task frontend ke shared component dan design token existing,
  bukan membuat primitive visual baru per halaman.
- Mengurangi visual noise, nested card, duplicate initializer, dan style yang
  saling menimpa.
- Menjaga perubahan frontend tetap kecil, terarah, backward-compatible, dan
  dapat diverifikasi.

## Baseline Aktif

- Hotel availability/check price menjadi baseline shell frontend.
- Activity Detail menjadi baseline modal order service.
- Home aktif menjadi referensi arah visual public frontend untuk hierarchy,
  whitespace, typography, rhythm, responsive section, dan presentation yang
  ringan. Home bukan template yang harus disalin mentah dan bukan alasan untuk
  merombak seluruh halaman.
- Shared component utama:
  - `resources/views/partials/frontend-loop-swiper.blade.php`
  - `resources/frontend/scss/components/frontend-buttons.scss`
  - `resources/frontend/scss/components/frontend-order-modal.scss`
  - `resources/frontend/js/components/form-submission-guard.js`

## Arah Desain Wajib

Public frontend harus:

- modern;
- bersih;
- ringan;
- konsisten;
- responsive pada mobile, tablet, dan desktop;
- reusable;
- mengikuti visual direction Home tanpa menyalin seluruh struktur Home;
- menggunakan design token existing dari
  `resources/frontend/scss/components/frontend-tokens.scss`.

Gunakan hierarchy, spacing, typography, divider, background section, grid, dan
alignment sebelum menambah border, radius, shadow, atau container visual baru.
Jangan membuat standar baru jika shared component atau pola canonical existing
sudah memenuhi kebutuhan.

Warna, spacing, radius, shadow, ukuran form, dan ukuran button harus mengambil
token existing. Arbitrary value hanya boleh dipakai jika tidak ada token yang
sesuai, kebutuhan tersebut benar-benar khusus, dan alasannya dicatat dalam
dokumentasi task.

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
- CSS page-level hanya untuk styling unik halaman, bukan menduplikasi primitive global.
- Swiper/list looping memakai shared `frontend-loop-swiper`.

### Runtime Color System

Source canonical warna public frontend berada di
`resources/frontend/scss/components/frontend-tokens.scss` dan memakai dua
lapisan:

1. SCSS `!default` variables sebagai fallback build-time dan nilai default
   resmi.
2. CSS custom properties `--ui-*` pada `:root` sebagai integration point
   runtime.

Core runtime palette:

```text
--ui-primary
--ui-primary-hover
--ui-secondary
--ui-accent
--ui-surface
--ui-background
--ui-text
--ui-muted
--ui-border
--ui-success
--ui-warning
--ui-danger
--ui-info
```

Token tambahan seperti soft surface, hover tone, foreground-on-primary, dan
focus/icon ring boleh tersedia jika dibutuhkan shared component untuk
mempertahankan state visual existing. Setiap declaration yang memakai runtime
variable wajib memiliki fallback yang sama dengan nilai visual aktif.

Ketentuan:

- Prefix `--ui-*` adalah kontrak integrasi theme runtime untuk public
  frontend.
- Prefix legacy `--frontend-shell-*` tetap menjadi compatibility alias selama
  consumer lama masih aktif; jangan dihapus tanpa audit.
- Shared button, frontend form base, picker/icon, dan order modal harus memakai
  runtime palette untuk warna canonical yang telah diverifikasi.
- Warna domain-specific, illustration color, translucent overlay, status-soft
  tone, atau hardcoded legacy yang belum dipetakan tidak boleh dipaksa masuk
  palette hanya karena nilainya mirip.
- Backend theme configuration di masa depan harus memakai allowlist token,
  validasi format warna, dan menghasilkan override `--ui-*` setelah
  `app.css`. Backend tidak boleh menghasilkan selector component atau
  mengubah compiled CSS.
- Theme switcher, penyimpanan database, controller, dan halaman backend bukan
  bagian foundation ini dan memerlukan task tersendiri.

### Cardless Design

Gunakan pendekatan cardless selama batas dan hierarchy konten masih dapat
dibentuk dengan:

- spacing;
- typography;
- divider;
- background section;
- grid;
- alignment.

Card atau surface hanya digunakan jika benar-benar diperlukan untuk
mengelompokkan satu unit informasi mandiri, satu item berulang, satu action
group, atau satu item interaktif yang membutuhkan boundary jelas.

Dilarang:

- card di dalam card;
- surface di dalam surface tanpa fungsi atau alasan yang terdokumentasi;
- panel berlapis;
- border, radius, dan shadow bertumpuk;
- wrapper visual berulang yang tidak menambah hierarchy, interaction boundary,
  atau makna.

Jika halaman sudah memiliki section container atau surface utama, komponen di
dalamnya tidak boleh otomatis dibungkus card tambahan. Gunakan layout cardless
di dalam surface tersebut kecuali setiap child memang merupakan unit mandiri.

Sebelum membuat card baru, periksa shared primitive existing seperti
`.frontend-surface-card`, `.frontend-fact-card`, availability family, dan
detail/order component yang relevan. Nama domain-specific boleh dipakai untuk
layout atau hook, tetapi tidak boleh menduplikasi seluruh visual primitive.

## Komponen Canonical

Task frontend berikutnya wajib mencari dan memakai kontrak canonical berikut
sebelum membuat komponen baru:

| Kebutuhan | Kontrak canonical |
| --- | --- |
| Button | `.ui-btn` dan modifier dari `frontend-buttons.scss` |
| Form control | Bootstrap-compatible `.form-control` / `.form-select` dengan shared frontend form dan picker styling |
| Card/surface | Cardless terlebih dahulu; jika perlu gunakan shared surface/fact/availability component yang paling dekat |
| Badge/status | Gunakan semantic tone existing (`success`, `warning`, `danger`, `info/secondary`, `muted`) dan design token shell; jangan membuat warna atau keluarga badge page-specific baru |
| Alert/notification | Untuk generic session/validation alert gunakan `resources/views/partials/alerts.blade.php`; `partials/alert` dan `partials/msg` adalah compatibility legacy, bukan pola untuk markup baru |
| Modal order service | `.frontend-order-modal*` sesuai `docs/decisions/frontend-order-modal-standard.md` |
| Modal non-order | Gunakan struktur modal library existing dan shared frontend styling; jangan membuat library/modal engine baru |
| Loading submit | `form-submission-guard.js` dan `resources/views/partials/form-submit-overlay.blade.php` sesuai form submit standard |
| Empty state | Gunakan hierarchy icon/title/message/action existing tanpa nested card tambahan |
| Page section | `.frontend-page-shell`, `.frontend-content-section`, shared topband/intro, dan section rhythm existing |
| Icon | Gunakan icon library yang sudah dimuat atau icon shared component; icon-only action wajib memiliki accessible label |

Ketentuan:

- Komponen baru hanya boleh dibuat jika kontrak existing tidak dapat memenuhi
  kebutuhan.
- Alasan, consumer, perbedaan kebutuhan, dan alasan mengapa extension existing
  tidak aman harus didokumentasikan.
- Jika komponen dipakai oleh dua atau lebih halaman, implementasinya harus
  dipindahkan ke shared Blade/SCSS/JavaScript sesuai jenis komponennya.
- Jangan membuat variasi visual baru hanya untuk membedakan halaman. Variasi
  harus mewakili state, hierarchy, atau behavior yang nyata.
- Badge/status baru harus memetakan makna ke semantic tone existing. Visual
  status tidak boleh mengubah atau menafsirkan business lifecycle.
- Alert baru tidak boleh memakai inline dismiss handler atau raw HTML message
  tanpa sanitization contract yang jelas.
- Compatibility class lama boleh dipertahankan selama migrasi, tetapi tidak
  menjadi contoh untuk markup baru.

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

Semua input tanggal dan waktu frontend wajib memakai global picker system,
termasuk datepicker, daterangepicker, datetime picker, timepicker, month picker,
year picker, date-time range, dan picker tanggal/waktu lain.

Markup yang sudah didukung:

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
- Seluruh mode memakai satu theme, ukuran input, typography, border, radius,
  shadow, spacing, dan state visual yang dikelola shared SCSS.
- Hover, focus, selected, range, today, disabled, readonly, dan invalid state
  tidak boleh dibuat berbeda per halaman.
- Calendar, time, clear, previous, dan next icon dikelola oleh shared picker
  theme atau centralized initializer. Blade tidak boleh menambahkan versi icon
  sendiri.
- Icon hanya bersifat presentation/control affordance. Penambahan icon tidak
  boleh mengubah `name`, `value`, format, event, validation, atau request
  contract input.
- Initializer harus centralized dan idempotent, menolak duplicate
  initialization, serta mendukung modal dan dynamic DOM.
- Konfigurasi variasi menggunakan project-prefixed `data-ui-picker-*`, bukan
  initializer baru.
- Jangan menambah initializer `daterangepicker`, `flatpickr`, atau inline date script di Blade/page-level bila bisa memakai `data-ui-picker`.
- Compatibility lama `.datetimepicker`, `.date-picker`, `data-booking-datetime`, `data-transport-datetime`, dan `input[name="checkincout"]` di-auto-init oleh global system selama migrasi.
- Jangan mengubah format value yang dikirim ke backend tanpa audit controller/request terkait.

Dedicated date-time range belum tersedia sebagai mode aktif pada initializer
saat ini. Kebutuhan tersebut harus ditambahkan sebagai enhancement pada
`FrontendPickerSystem` dan shared theme, disertai audit format backend dan test;
task tidak boleh mengimplementasikannya sebagai initializer page-level.

## Larangan

Dilarang:

- membuat warna baru secara sembarangan;
- memakai arbitrary hex jika design token tersedia;
- membuat button variant page-specific tanpa kebutuhan dan alasan
  terdokumentasi;
- membuat style form yang menduplikasi shared form control;
- membuat picker initializer baru pada setiap halaman;
- menambahkan inline CSS atau inline JavaScript;
- mengedit compiled CSS jika SCSS adalah maintained source;
- mengganti library frontend yang masih aktif tanpa approval;
- menghapus style atau compatibility layer legacy sebelum seluruh consumer
  diverifikasi;
- melakukan redesign seluruh halaman atau banyak flow dalam satu task;
- memaksakan visual Home secara literal ke semua halaman;
- menghapus domain-specific behavior hanya demi menyeragamkan tampilan.

## Aturan Eksekusi Task Frontend

Setiap task frontend berikutnya harus:

1. membaca dokumen ini dan decision record yang relevan;
2. mengidentifikasi shared component dan token existing;
3. menentukan apakah konten dapat tetap cardless;
4. membatasi perubahan pada satu flow atau keluarga komponen yang dapat diuji;
5. menjaga compatibility legacy di luar scope;
6. memeriksa desktop, tablet, mobile, focus keyboard, loading, error, disabled,
   dan empty state yang relevan;
7. meninjau diff agar tidak ada primitive, initializer, dependency, atau warna
   baru yang tidak diperlukan;
8. memperbarui roadmap/dokumentasi hanya jika ada keputusan visual, shared
   component, dependency, atau migration state yang berubah.

## Checklist

1. H1 hanya satu.
2. Breadcrumb canonical.
3. CTA utama jelas.
4. Responsive mobile/tablet/desktop.
5. Copy memakai language key.
6. CSS/JS terpisah dari Blade.
7. Submit/loading state ada untuk aksi penting.
8. Button memakai `ui-btn` dan modifier standar, atau compatibility class lama hanya saat migrasi legacy.
9. Cardless sudah diprioritaskan dan tidak ada nested card tanpa fungsi.
10. Shared component, design token, dan icon existing sudah diperiksa.
11. Tidak ada initializer, dependency, inline style/script, atau visual
    primitive duplikat.
12. Picker memakai global system dan backend format tetap kompatibel.
13. `docs/decisions/frontend-roadmap.md` diperbarui jika migration state atau
    kontrak frontend berubah.

## Catatan Keputusan

Tanggal: 2026-07-28

Pekerjaan:

- Menetapkan arah desain Home sebagai referensi, bukan template redesign.
- Menetapkan cardless sebagai pendekatan default dan melarang nested visual
  container tanpa fungsi.
- Menetapkan kewajiban memakai komponen canonical, design token, shared
  initializer, dan shared picker theme.
- Menetapkan batas compatibility legacy dan larangan duplikasi untuk task
  frontend berikutnya.

Efek:

- Seluruh task frontend setelah tanggal ini wajib menilai reuse, cardless
  hierarchy, token, dan centralized behavior sebelum menambah styling atau
  komponen.
- Dokumen ini tidak memigrasikan implementasi legacy dan tidak membuktikan
  seluruh halaman saat ini sudah patuh.

Risiko tersisa:

- Generic badge/status dan alert/notification masih memiliki beberapa pola
  legacy; konsolidasinya memerlukan audit consumer dan task implementasi
  terpisah.
- Dedicated date-time range masih memerlukan enhancement centralized.
- Style legacy tidak boleh dihapus hanya berdasarkan kontrak dokumentasi ini.
