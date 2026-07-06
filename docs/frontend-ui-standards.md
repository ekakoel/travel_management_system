# Frontend UI Standards

Dokumen ini adalah standar wajib untuk seluruh halaman frontend pada project `balikamitour` agar UI, UX, struktur markup, dan pola implementasi tetap konsisten.

## Status Wajib

Mulai sekarang halaman `hotelavailability` menjadi referensi resmi dan baseline utama untuk UI/UX frontend project ini.

Reference implementation:

- view: `resources/views/main/hotelavailability.blade.php`
- shared shell CSS: `public/css/components/frontend-page-shell.css`
- page CSS: `public/css/pages/hotel-availability.css`
- page JS: `public/frontend/js/pages/hotel-availability.js`
- reusable rate card: `resources/views/partials/hotel-availability-rate-card.blade.php`
- shared modal pattern: `resources/views/partials/hotel-rate-detail-modal.blade.php`

Halaman frontend baru wajib mengikuti bahasa visual, hirarki informasi, spacing, dan interaction model dari baseline tersebut kecuali ada alasan bisnis yang jelas.

## Tujuan

- Menyatukan bahasa visual halaman frontend.
- Menjadikan pengalaman agent dan visitor terasa satu produk yang konsisten.
- Mengurangi halaman yang terlihat seperti hasil style ad hoc per fitur.
- Memastikan halaman baru reusable, scalable, dan mudah dirawat.
- Menjaga agar perubahan frontend tetap kompatibel dengan pattern project yang sudah dibakukan.

## Prinsip Inti

1. Frontend harus terasa seperti product interface, bukan panel admin yang dipindahkan ke sisi user.
2. Informasi utama harus cepat dipahami dalam 3 area pertama layar:
   - breadcrumb dan konteks
   - headline dan summary
   - action utama
3. CTA utama harus jelas dan relevan dengan tujuan halaman.
4. Data penting tidak boleh diulang berlebihan.
5. Layout, spacing, dan hierarchy harus konsisten antar halaman service, detail, dan availability.
6. Data shaping dan perhitungan UI tidak boleh dibebankan ke Blade.

## Baseline Hotel Availability

Halaman `hotelavailability` menjadi patokan untuk aspek berikut:

1. Shell halaman frontend.
2. Modern breadcrumb.
3. Topband yang menyatu dengan intro halaman.
4. Summary metadata cards.
5. Sectioned results layout.
6. Sidebar action pattern.
7. Card-based information grouping.
8. Modal detail pattern.
9. Agent-friendly flow yang menempatkan navigasi frontend di atas.
10. Copywriting yang jelas, singkat, dan menjelaskan tindakan berikutnya.

Halaman booking hotel turunan dari `hotelavailability` juga wajib mengikuti family visual yang sama untuk:

1. topband dan breadcrumb kanonik
2. hero summary sebelum form
3. progress stepper untuk flow booking
4. surface form dan price review yang konsisten
5. wizard flow yang memecah input panjang menjadi langkah yang jelas dan ringan untuk agent

## Aturan Wajib

1. Setiap halaman frontend detail, search result, availability, dan service landing harus memakai shell halaman yang konsisten.
2. Shell frontend standar terdiri dari:
   - topband atau header band
   - reusable breadcrumb
   - title dan supporting text
   - summary cards bila ada metadata penting
   - main content section
   - sidebar CTA jika konteksnya membutuhkan action berikutnya
3. Gunakan file reusable untuk shell bersama.
4. Gunakan file page CSS terpisah untuk styling spesifik halaman.
5. Jangan membuat style header, breadcrumb, spacing, card layout, atau modal dari nol jika polanya sudah ada.
6. Jangan mencampur visual backend atau panel admin ke halaman frontend agent.
7. Gunakan typography, radius, surface, border, dan spacing yang harmonis dengan baseline `hotelavailability`.
8. Jika halaman mempunyai flow detail menuju action, action panel harus mudah ditemukan tanpa mengganggu konten utama.
9. Jika ada list rate, package, promo, atau offer lain, gunakan card pattern yang informatif, ringkas, dan konsisten.
10. Informasi entity utama tidak boleh ditampilkan berulang tanpa alasan.
11. Jika halaman membutuhkan slider Swiper untuk list card, gunakan pattern shared `frontend-loop-swiper`, bukan inisialisasi Swiper ad hoc per halaman.

## Standar Struktur Halaman

Gunakan pola ini untuk halaman frontend:

```text
Frontend Page
  Topband
    Breadcrumb
    Eyebrow jika perlu
    H1
    Supporting text
    Summary cards
  Main Content
    Hero / overview surface
    Content sections
    Sidebar CTA jika perlu
  Supporting Content
    Related items / nearby items / additional trust content
```

## Standar UX

1. H1 hanya satu kali per halaman.
2. User harus langsung tahu:
   - dia sedang berada di halaman apa
   - halaman ini untuk apa
   - tindakan utama berikutnya apa
3. CTA primer harus paling menonjol.
4. Teks pendukung harus menjelaskan nilai atau langkah berikutnya, bukan mengulang judul.
5. Form harus sesingkat mungkin dan fokus pada input yang benar-benar dibutuhkan.
6. Empty state harus jelas dan membantu user melanjutkan.
7. Feedback error harus informatif dan tidak generik.
8. Flow multi-step harus punya entry point dan destination yang jelas.
9. Redirect setelah login, language switch, atau submit form harus menuju URL final yang valid dan shareable.
10. Halaman frontend harus tetap usable pada desktop dan mobile.

Untuk booking form, pola default yang harus diutamakan adalah:

1. `step 1`: stay details dan guest details
2. `step 2`: transfer, arrival, departure, dan note hanya bila relevan
3. `step 3`: price review dan final submit

## Standar Breadcrumb

1. Breadcrumb frontend harus memakai style reusable, bukan default plain Bootstrap.
2. Breadcrumb harus singkat dan berurutan logis.
3. Item terakhir harus active state tanpa link.
4. Jangan menaruh terlalu banyak level jika tidak membantu user.
5. Detail dan check price harus memakai pola breadcrumb yang satu keluarga secara visual.

## Standar Konten

1. Jangan ulangi nama hotel, nama produk, atau nama service pada banyak tempat jika konteksnya sudah jelas dari H1.
2. Section title harus menjelaskan isi section, bukan mengulang nama halaman.
3. Informasi ringkas sebaiknya ditaruh di summary cards, badges, meta rows, atau supporting note.
4. Deskripsi panjang harus ditempatkan pada section yang memang relevan.
5. Gunakan CTA copy yang action-oriented dan mudah dipahami.

## Standar Tanggal dan Waktu

1. Semua tanggal yang ditampilkan di halaman frontend harus memakai format internasional `YYYY-MM-DD`.
2. Semua tanggal dengan waktu harus memakai format `YYYY-MM-DD HH:mm` dengan jam 24-hour.
3. Blade frontend harus memakai helper `dateFormat()` dan `dateTimeFormat()` kecuali ada alasan teknis yang jelas.
4. JavaScript frontend harus memakai format Moment/display `YYYY-MM-DD` dan `YYYY-MM-DD HH:mm`.
5. Hindari format ambigu seperti `MM/DD/YYYY`, `DD/MM/YYYY`, `d M Y`, atau format 12-hour AM/PM untuk tampilan frontend baru.

## Standar Card dan Surface

1. Surface card harus memakai family style yang konsisten:
   - radius
   - border softness
   - shadow depth
   - vertical rhythm
2. Card harus dibedakan berdasarkan fungsi, bukan berdasarkan variasi style yang acak.
3. Hindari pola visual `card di dalam card di dalam card` jika lapisan tambahan itu tidak membawa fungsi baru yang jelas.
4. Jika sebuah section sudah punya surface utama, elemen di dalamnya sebaiknya memakai layout yang lebih ringan seperti media block, meta row, divider, badge, atau grid, bukan menambah card bertumpuk.
5. Gunakan nested surface hanya bila memang dibutuhkan untuk memisahkan fungsi yang berbeda, misalnya modal detail, sticky summary, atau panel aksi yang terpisah konteks.
6. Card offer atau rate harus memuat:
   - label type
   - nama room atau offer
   - metadata penting
   - price summary
   - detail trigger bila ada isi lanjutan
7. Modal detail harus menggunakan pattern shared yang sama, bukan modal custom baru di setiap halaman.

## Standar Reusability

1. Jika pola visual muncul di dua halaman atau lebih, pindahkan ke file shared component CSS.
2. Jika pola markup berulang di banyak Blade, pindahkan ke partial atau component.
3. Jika data transformasi untuk UI cukup kompleks, bentuk datanya di controller terlebih dahulu.
4. Blade tidak boleh menjadi tempat perhitungan meta card, filtering utama, sorting utama, atau data shaping berat.
5. Jika halaman baru membutuhkan shell serupa dengan `hotelavailability`, mulai dari shell yang ada lalu extend, jangan rebuild.

## Standar Teknis Frontend

1. CSS shared taruh di `public/css/components/...`.
2. CSS spesifik halaman taruh di `public/css/pages/...`.
3. JS spesifik halaman taruh di `public/frontend/js/pages/...` atau lokasi asset per-domain yang setara.
4. Partial boleh `@push` asset dengan `@once`, tetapi asset fisiknya tetap harus terpisah.
5. State frontend berbasis interaksi harus memakai `data-*` attribute yang jelas.
6. Jangan membuat dependency visual pada route transisional atau URL POST.
7. Semua halaman frontend harus mewarisi design system global dari layout frontend sebelum menambah CSS page-level sendiri.
8. Layer design system global frontend saat ini dibagi menjadi:
   - `public/css/components/frontend-tokens.css`
   - `public/css/components/frontend-base.css`
   - `public/css/components/frontend-page-shell.css`
   - `public/css/components/frontend-layout.css`
   - `public/css/components/frontend-components.css`
   - `public/css/components/frontend-forms.css`
9. CSS page-level hanya boleh menyimpan styling yang benar-benar unik untuk halaman tersebut, bukan menduplikasi shell, hero, surface, card, form, atau spacing global.
10. Jika dua halaman memakai pola visual yang sama, pindahkan dulu ke layer component/global sebelum menambah override baru.
11. Standar Swiper frontend shared saat ini terdiri dari:
   - Blade wrapper reusable: `resources/views/partials/frontend-loop-swiper.blade.php`
   - Shared CSS shell: `public/css/components/frontend-swiper.css`
   - Shared JS runtime: `public/frontend/js/components/frontend-loop-swiper.js`
12. Untuk kebutuhan data sedikit tetapi slider harus terasa tak terbatas, ulangi item di Blade wrapper shared dan lakukan normalisasi index di JS shared, bukan menulis ulang loop logic di halaman lain.
13. Halaman yang memakai Swiper harus menaruh styling unik slide-card di CSS halaman atau modifier class, sementara shell slider, navigation, dan infinite-repeat behavior tetap berasal dari layer shared.

## Aturan Untuk Pengembangan Kedepan

1. Setiap halaman frontend baru wajib direview terhadap baseline `hotelavailability`.
2. Jika sebuah perubahan sengaja menyimpang dari baseline, penyimpangan itu wajib dijelaskan di pull request atau catatan perubahan.
3. Jika pattern baru ternyata lebih baik dan akan dipakai ulang, dokumen ini harus diperbarui sebelum atau bersamaan dengan implementasi berikutnya.
4. Setiap perubahan UI/UX frontend wajib dicatat pada roadmap project.

Roadmap resmi:

- `docs/frontend-roadmap.md`
- `docs/frontend-roadmap-entry-template.md`
- `.github/PULL_REQUEST_TEMPLATE/frontend.md`

## Checklist Review

Sebelum merge perubahan frontend, pastikan:

1. Apakah halaman memakai shell frontend standar?
2. Apakah halaman konsisten dengan baseline `hotelavailability`?
3. Apakah breadcrumb sudah konsisten?
4. Apakah H1 hanya satu?
5. Apakah nama entity utama ditampilkan berulang secara tidak perlu?
6. Apakah spacing, card radius, surface, dan hierarchy konsisten?
7. Apakah CTA utama jelas?
8. Apakah style dan script sudah dipisah ke file reusable atau file page yang benar?
9. Apakah data shaping sudah dipindah ke controller jika kompleks?
10. Apakah perubahan ini sudah dicatat di `docs/frontend-roadmap.md`?
