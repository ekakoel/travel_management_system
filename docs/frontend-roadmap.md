# Frontend Roadmap

Dokumen ini adalah roadmap hidup untuk seluruh pengembangan frontend project `balikamitour`.

## Aturan Wajib

1. Setiap perubahan frontend wajib dicatat di dokumen ini.
2. Setiap entry baru harus ditambahkan, bukan mengganti histori lama tanpa alasan.
3. Jika sebuah task selesai, status harus diperbarui.
4. Jika sebuah standar berubah, dokumen standar terkait juga harus diperbarui bersamaan.
5. Roadmap ini menjadi referensi utama untuk:
   - progress frontend
   - standardisasi UI/UX
   - daftar reusable pattern
   - backlog penyempurnaan berikutnya

## Format Entry

Gunakan format berikut untuk setiap perubahan:

```text
## YYYY-MM-DD - Judul Perubahan
- Status: planned | in progress | done | blocked
- Area: halaman / komponen / asset / flow
- Summary: ringkasan singkat perubahan
- Impact: dampak pada UI, UX, reusability, performance, atau consistency
- Files: file utama yang terpengaruh
- Follow-up: langkah lanjutan jika ada
```

## Template Entry Siap Pakai

Salin template ini setiap kali ada perubahan frontend baru:

```text
## YYYY-MM-DD - Judul Perubahan
- Status: planned | in progress | done | blocked
- Area: halaman / komponen / asset / flow
- Summary: jelaskan apa yang diubah dalam 1-3 kalimat
- Impact: jelaskan pengaruhnya terhadap UI, UX, consistency, reusability, atau performance
- Files:
  - `path/file-1`
  - `path/file-2`
- Follow-up: tulis langkah berikutnya, atau `none` jika tidak ada
```

## Template Entry Khusus Frontend UI

Gunakan variasi ini jika perubahan fokus pada UI/UX:

```text
## YYYY-MM-DD - Nama Halaman / Fitur
- Status: planned | in progress | done | blocked
- Area: frontend UI/UX
- Summary: perubahan visual, struktur halaman, flow, atau interaction yang dilakukan
- Impact: dampak pada baseline `hotelavailability`, consistency, mobile usability, CTA clarity, atau readability
- Files:
  - `resources/views/...`
  - `public/css/...`
  - `public/frontend/js/...`
  - `app/Http/Controllers/...`
- Follow-up: alignment page lain / audit translation / cleanup reusable component / none
```

## Baseline Resmi Saat Ini

- Baseline UI/UX frontend resmi: `hotelavailability`
- Standar UI frontend: `docs/frontend-ui-standards.md`
- Standar Blade asset separation: `docs/blade-asset-rules.md`

## Active Principles

1. Halaman frontend agent harus terasa seperti website product frontend, bukan admin panel.
2. `hotelavailability` adalah acuan utama untuk page shell, breadcrumb, summary, CTA layout, modal detail, dan section hierarchy.
3. Semua halaman frontend baru harus mendorong reusable CSS, reusable Blade partial, dan data shaping di controller.
4. URL final harus shareable, refresh-safe, dan language-switch-safe.

## Frontend Change Log

## 2026-07-02 - Shared Infinite Swiper Standard
- Status: done
- Area: frontend UI/UX, shared component, reusable asset system
- Summary: Menetapkan pattern Swiper frontend reusable berbasis wrapper Blade shared, CSS shared, dan JS shared untuk kasus slider card dengan item sedikit tetapi harus tetap terasa penuh dan bisa digeser terus tanpa mentok.
- Impact: Logic infinite-repeat Swiper tidak lagi terkunci di section `Exclusive Hotel Promotions`, lebih mudah dipakai ulang pada halaman lain untuk data berbeda, dan standard penggunaan Swiper project kini terdokumentasi sebagai pattern frontend resmi.
- Files:
  - `resources/views/partials/frontend-loop-swiper.blade.php`
  - `resources/views/frontend/home/partials/hotel-promotion.blade.php`
  - `resources/views/frontend/home/partials/hotel-promotion-slide.blade.php`
  - `public/css/components/frontend-swiper.css`
  - `public/frontend/js/components/frontend-loop-swiper.js`
  - `public/frontend/css/style.css`
  - `resources/views/frontend/layouts/app.blade.php`
  - `docs/frontend-ui-standards.md`
  - `docs/frontend-roadmap.md`
- Follow-up: Terapkan wrapper shared ini ke kebutuhan slider frontend lain yang masih memakai inisialisasi Swiper manual agar behavior, markup, dan asset separation tetap konsisten.

## 2026-07-01 - Standard and Package Booking Alignment to Promo Flow
- Status: done
- Area: hotel booking frontend UI/UX
- Summary: Menyelaraskan halaman `order-hotel-normal` dan `order-hotel-package` ke struktur UI/UX `order-hotel-promo` dengan section block reusable, fact cards, transfer section, review section, dan price breakdown yang seirama.
- Impact: Seluruh family order hotel kini memiliki hierarchy konten, ritme visual, dan pola interaksi yang lebih konsisten, sehingga pengalaman agent terasa seperti satu flow produk yang utuh.
- Files:
  - `resources/views/form/order-hotel-normal.blade.php`
  - `resources/views/form/order-hotel-package.blade.php`
  - `docs/frontend-roadmap.md`
- Follow-up: Audit wording dan label antar ketiga halaman order hotel agar copywriting tiap langkah juga sepenuhnya konsisten.

## 2026-07-01 - Booking Hero Hotel-Only Data Focus
- Status: done
- Area: hotel booking frontend UI/UX, shared topband partial
- Summary: Menyederhanakan `hotel-booking-hero` pada halaman order hotel agar hanya menampilkan informasi hotel, sementara data booking seperti order number, tanggal order, offer, room, dan stay tetap berada di area form tempat user memang mengisinya.
- Impact: Hero menjadi lebih bersih, tidak mengulang informasi booking yang sudah muncul di form, dan hierarchy halaman order terasa lebih konsisten dengan prinsip frontend yang menempatkan ringkasan entity utama di hero.
- Files:
  - `resources/views/partials/hotel-booking-topband.blade.php`
  - `docs/frontend-roadmap.md`
- Follow-up: Jika perlu, rapikan copy hero booking agar lebih terasa sebagai property overview daripada intro flow booking.

## 2026-07-01 - Promotion Booking Section Hierarchy Alignment
- Status: done
- Area: hotel promo booking frontend UI/UX
- Summary: Merapikan isi halaman `order-hotel-promo` agar mengikuti hierarchy frontend lain melalui section block reusable, fact cards, transport section, review section, dan price breakdown yang lebih konsisten dengan family visual `hotelavailability` dan `accommodation detail`.
- Impact: Halaman promo booking kini tidak hanya memakai warna dan shell yang sama, tetapi juga struktur konten, ritme section, dan keterbacaan yang lebih seragam dengan halaman frontend utama lainnya.
- Files:
  - `resources/views/form/order-hotel-promo.blade.php`
  - `public/css/components/frontend-components.css`
  - `public/css/pages/hotel-booking.css`
  - `docs/frontend-roadmap.md`
- Follow-up: Terapkan section hierarchy reusable yang sama ke `order-hotel-normal` dan `order-hotel-package` agar seluruh booking family benar-benar seragam.

## 2026-07-01 - Global Frontend Design System Foundation
- Status: done
- Area: frontend UI/UX, shared CSS architecture, reusable asset system
- Summary: Menambahkan design system CSS global yang dimuat langsung dari layout frontend, lalu mulai mengekstrak styling berulang dari `hotelavailability`, `order-hotel-*`, dan `accommodation detail` ke layer shared agar semua halaman frontend memakai sumber visual yang sama.
- Impact: Konsistensi UI/UX lintas halaman meningkat, duplikasi CSS berkurang, dan pengembangan halaman frontend berikutnya kini punya fondasi reusable yang lebih jelas daripada menambah style per halaman secara terpisah.
- Files:
  - `resources/views/frontend/layouts/app.blade.php`
  - `public/css/components/frontend-tokens.css`
  - `public/css/components/frontend-base.css`
  - `public/css/components/frontend-page-shell.css`
  - `public/css/components/frontend-layout.css`
  - `public/css/components/frontend-components.css`
  - `public/css/components/frontend-forms.css`
  - `public/css/components/frontend-availability-family.css`
  - `public/css/components/hotel-check-price-card.css`
  - `public/css/pages/hotel-availability.css`
  - `public/css/pages/hotel-booking.css`
  - `public/css/pages/accommodation-detail.css`
  - `resources/views/main/hotelavailability.blade.php`
  - `resources/views/form/order-hotel-normal.blade.php`
  - `resources/views/form/order-hotel-promo.blade.php`
  - `resources/views/form/order-hotel-package.blade.php`
  - `resources/views/frontend/accommodations/detail.blade.php`
  - `resources/views/partials/hotel-check-price-card.blade.php`
  - `docs/frontend-ui-standards.md`
  - `docs/frontend-roadmap.md`
- Follow-up: Lanjutkan migrasi service pages frontend lain ke design system global yang sama, lalu audit `frontend/css/style.css` dan `frontend/css/custom_style.css` agar legacy visual drift ikut berkurang.

## 2026-07-01 - Promotion Booking Availability Family Alignment
- Status: done
- Area: hotel promo booking frontend UI/UX, shared frontend asset
- Summary: Menyelaraskan halaman `order-hotel-promo` ke family visual `hotelavailability` dengan mengekstrak style hero dan surface availability ke component CSS shared, lalu menerapkannya pada topband dan shell promo booking tanpa menempelkan inline style atau merusak halaman booking hotel lain.
- Impact: Halaman promo booking kini terasa satu keluarga dengan baseline frontend resmi, reusable CSS meningkat karena pattern availability tidak lagi terkunci di page CSS tunggal, dan perubahan tetap mengikuti aturan asset separation project.
- Files:
  - `public/css/components/frontend-availability-family.css`
  - `resources/views/main/hotelavailability.blade.php`
  - `resources/views/form/order-hotel-promo.blade.php`
  - `resources/views/partials/hotel-booking-topband.blade.php`
- Follow-up: Jika normal dan package booking juga ingin disamakan penuh ke family `hotelavailability`, teruskan variant shared ini ke dua halaman tersebut agar seluruh booking family konsisten.

## 2026-06-30 - Frontend Baseline Standardization
- Status: done
- Area: global frontend standard
- Summary: Menetapkan halaman `hotelavailability` sebagai baseline resmi UI/UX frontend untuk project ini.
- Impact: Menyatukan acuan visual dan interaction pattern agar pengembangan frontend berikutnya tidak bercabang tanpa standar.
- Files:
  - `docs/frontend-ui-standards.md`
  - `docs/frontend-roadmap.md`
  - `docs/blade-asset-rules.md`
- Follow-up: Seluruh halaman frontend lain harus diselaraskan bertahap ke baseline ini.

## 2026-06-30 - Frontend Governance Templates
- Status: done
- Area: documentation, process, frontend governance
- Summary: Menambahkan template entry roadmap siap pakai dan checklist PR frontend agar tim tidak lupa mencatat perubahan frontend ke roadmap.
- Impact: Proses pengembangan frontend menjadi lebih disiplin, lebih mudah direview, dan lebih kecil kemungkinan melewatkan update dokumentasi wajib.
- Files:
  - `docs/frontend-roadmap.md`
  - `docs/frontend-roadmap-entry-template.md`
  - `.github/PULL_REQUEST_TEMPLATE/frontend.md`
  - `docs/frontend-ui-standards.md`
  - `README.md`
- Follow-up: Jika tim memakai workflow PR aktif, pastikan template ini digunakan sebagai default template frontend.

## 2026-06-30 - Accommodation Detail and Check Price Visual Alignment
- Status: done
- Area: accommodation detail, check price
- Summary: Menyamakan bahasa visual antara halaman detail accommodation dan check price dengan shell frontend reusable, modern breadcrumb, dan hierarchy yang lebih konsisten.
- Impact: User agent mendapatkan pengalaman yang lebih konsisten antara tahap melihat detail dan tahap memeriksa harga.
- Files:
  - `resources/views/frontend/accommodations/detail.blade.php`
  - `resources/views/main/hotelavailability.blade.php`
  - `public/css/components/frontend-page-shell.css`
  - `public/css/pages/accommodation-detail.css`
  - `public/css/pages/hotel-availability.css`
- Follow-up: Samakan halaman frontend service lain ke shell yang sama.

## 2026-06-30 - Dedicated Hotel Check Price Flow
- Status: done
- Area: hotel check price routing and flow
- Summary: Merapikan flow check price ke route dedicated berbasis controller dan URL final shareable.
- Impact: Flow menjadi lebih aman untuk production, login redirect, refresh browser, dan language switching.
- Files:
  - `routes/web.php`
  - `app/Http/Controllers/HotelsController.php`
- Follow-up: Terapkan pola canonical URL yang sama pada flow frontend lain yang masih mengandalkan route transisional.

## 2026-06-30 - Canonical Language Redirect Flow
- Status: done
- Area: language switcher, frontend navigation
- Summary: Menambahkan helper canonical redirect untuk switch language agar tidak lagi mengarah ke URL POST atau URL transisional.
- Impact: Pergantian bahasa menjadi konsisten, aman, dan tidak memicu 404 pada halaman frontend dinamis.
- Files:
  - `app/Helpers/helpers.php`
  - `resources/views/frontend/layouts/navbar.blade.php`
  - `resources/views/layouts/home/navbar.blade.php`
  - `resources/views/component/menu.blade.php`
- Follow-up: Audit seluruh link language lain di project yang belum memakai helper canonical.

## 2026-06-30 - Hotel Availability Booking CTA Flow
- Status: done
- Area: hotel availability, booking flow, rate cards
- Summary: Menambahkan tombol order atau reserve pada setiap rate card di halaman hotel availability dan menghubungkannya ke entry booking form yang sesuai untuk rate standard, promotion, dan package.
- Impact: Flow booking menjadi lebih natural dengan pola `select rate -> review stay -> enter guest details -> submit booking request`, lebih dekat dengan standard international hotel booking flow tanpa memutus logic order yang sudah ada.
- Files:
  - `app/Http/Controllers/HotelsController.php`
  - `resources/views/partials/hotel-availability-rate-card.blade.php`
  - `public/css/pages/hotel-availability.css`
  - `resources/lang/en/messages.php`
  - `resources/lang/zh/messages.php`
  - `resources/lang/zh-CN/messages.php`
- Follow-up: Rapikan halaman form order hotel lama agar bahasa visualnya juga selaras dengan baseline frontend baru.

## 2026-06-30 - Hotel Booking Form Family Redesign
- Status: done
- Area: hotel booking frontend UI/UX
- Summary: Menyatukan tiga halaman form booking hotel untuk rate standard, promotion, dan package ke satu family UI frontend yang konsisten dengan baseline `hotelavailability`.
- Impact: Flow booking terasa lebih modern dan lebih dekat ke standard booking international melalui shell frontend reusable, breadcrumb kanonik, hero summary, progress stepper, dan surface form yang lebih konsisten tanpa mengubah logic booking lama.
- Files:
  - `resources/views/form/order-hotel-normal.blade.php`
  - `resources/views/form/order-hotel-promo.blade.php`
  - `resources/views/form/order-hotel-package.blade.php`
  - `resources/views/partials/hotel-booking-topband.blade.php`
  - `public/css/pages/hotel-booking.css`
  - `public/frontend/js/pages/hotel-booking.js`
- Follow-up: Pindahkan kalkulasi inline per halaman ke file JS terpisah per flow agar asset separation pada booking form juga sepenuhnya mengikuti standar frontend project.

## 2026-06-30 - Hotel Booking Wizard Flow
- Status: done
- Area: hotel booking flow, frontend UX, form optimization
- Summary: Mengubah tiga halaman order hotel menjadi form wizard bertahap agar agent hanya melihat input yang relevan per langkah, dengan urutan `stay and guests -> transfers and notes -> review and submit`.
- Impact: Form terasa lebih ringan, lebih cepat dipahami, dan lebih dekat ke standard booking international tanpa memutus proses submit dan perhitungan harga yang sudah ada.
- Files:
  - `resources/views/form/order-hotel-normal.blade.php`
  - `resources/views/form/order-hotel-promo.blade.php`
  - `resources/views/form/order-hotel-package.blade.php`
  - `public/css/pages/hotel-booking.css`
  - `public/frontend/js/pages/hotel-booking.js`
  - `docs/frontend-ui-standards.md`
- Follow-up: Pindahkan semua kalkulasi inline dari Blade ke file JS page-level atau module terpisah agar optimasi asset booking lebih lengkap.

## 2026-06-30 - Hotel Availability Data Refactor
- Status: done
- Area: controller shaping, availability cards
- Summary: Memindahkan data shaping utama dari Blade ke controller untuk halaman hotel availability.
- Impact: View menjadi lebih ringan, lebih mudah di-maintain, dan siap untuk sorting seperti lowest price, promotion first, atau package only.
- Files:
  - `app/Http/Controllers/HotelsController.php`
  - `resources/views/main/hotelavailability.blade.php`
  - `resources/views/partials/hotel-availability-rate-card.blade.php`
- Follow-up: Tambahkan sorting dan filtering berbasis data card yang sudah dibentuk di controller.

## 2026-06-30 - Modal Refactor for Hotel Availability
- Status: done
- Area: hotel availability modal system
- Summary: Mengganti pola modal detail yang bermasalah dengan shared modal pattern yang lebih stabil.
- Impact: Memperbaiki issue modal tertutup backdrop, posisi overlay, dan konsistensi detail include atau benefits antar card.
- Files:
  - `resources/views/partials/hotel-rate-detail-trigger.blade.php`
  - `resources/views/partials/hotel-rate-detail-modal.blade.php`
  - `public/frontend/js/pages/hotel-availability.js`
  - `public/css/pages/hotel-availability.css`
- Follow-up: Terapkan modal shared pattern ke halaman frontend lain yang punya detail popup serupa.

## 2026-06-30 - Multilanguage Availability Foundation
- Status: done
- Area: locale, hotel detail, check price
- Summary: Menambahkan fondasi preferred language user, locale middleware yang rapi, dan localizable hotel availability content.
- Impact: Halaman detail hotel dan check price kini dapat mengikuti bahasa user dengan fallback yang lebih aman.
- Files:
  - `app/Http/Middleware/SetLocale.php`
  - `app/Http/Controllers/LocalizationController.php`
  - `app/Http/Controllers/HotelsController.php`
  - `app/Http/Controllers/FrontEndController.php`
  - `resources/lang/en/messages.php`
  - `resources/lang/zh/messages.php`
  - `resources/lang/zh-CN/messages.php`
- Follow-up: Audit seluruh halaman frontend agar translation coverage lebih merata.

## 2026-06-30 - Hotel Booking Form Refactor and Asset Separation
- Status: done
- Area: hotel booking frontend UI/UX, controller shaping, performance
- Summary: Merapikan halaman booking hotel dengan memindahkan data shaping dari Blade ke controller, mengekstrak interaksi form ke file JS eksternal, dan membersihkan inline style agar view lebih ringan dan mudah dirawat.
- Impact: Halaman booking menjadi lebih maintainable, lebih ringan di Blade, lebih siap untuk penambahan sorting atau pricing rule berikutnya, dan flow `Add More Room` kini memakai clone-based room card yang lebih stabil.
- Files:
  - `app/Http/Controllers/OrderController.php`
  - `resources/views/form/order-hotel-normal.blade.php`
  - `resources/views/form/order-hotel-promo.blade.php`
  - `resources/views/form/order-hotel-package.blade.php`
  - `resources/views/partials/hotel-booking-room-card.blade.php`
  - `public/css/pages/hotel-booking.css`
  - `public/frontend/js/pages/hotel-booking.js`
- Follow-up: Tambahkan QA browser pass untuk seluruh varian booking hotel dan lanjut audit dead code pada legacy hotel order form yang belum dipakai flow frontend baru.

## 2026-06-30 - Hotel Detail Frontend Alignment
- Status: done
- Area: hotel detail frontend UI/UX, controller shaping, performance
- Summary: Menyelaraskan halaman `hoteldetail` ke family visual frontend resmi dengan topband, summary cards, hero overview, sticky check-price CTA, dan room preview modal shared yang lebih ringan.
- Impact: Halaman detail hotel tidak lagi memakai shell admin lama, beban Blade berkurang karena data shaping utama dipindah ke controller, dan section room kini menghindari pola modal besar berulang per card.
- Files:
  - `app/Http/Controllers/HotelsController.php`
  - `resources/views/main/hoteldetail.blade.php`
  - `resources/views/partials/hotel-detail-room-card.blade.php`
  - `resources/views/partials/hotel-detail-room-modal.blade.php`
  - `public/css/pages/hotel-detail.css`
  - `public/frontend/js/pages/hotel-detail.js`
  - `resources/lang/en/messages.php`
  - `resources/lang/zh/messages.php`
  - `resources/lang/zh-CN/messages.php`
- Follow-up: Audit halaman hotel detail frontend lain yang masih memakai shell lama agar family UI hotel seluruhnya konsisten.

## 2026-07-01 - Accommodation Detail Login Return Flow
- Status: done
- Area: accommodation detail, login redirect flow
- Summary: Merapikan flow sidebar `Check Price` pada halaman accommodation detail agar user yang belum login diarahkan ke login lalu kembali ke halaman accommodation detail yang sama dengan panel check price langsung terbuka.
- Impact: CTA guest menjadi lebih mulus, context halaman tidak hilang setelah login, dan flow detail ke check price lebih konsisten untuk partner yang belum terautentikasi.
- Files:
  - `app/Http/Controllers/HotelsController.php`
  - `docs/frontend-roadmap.md`
- Follow-up: Audit CTA frontend lain yang masih mengirim guest ke login tanpa menyimpan context halaman asal.

## 2026-07-01 - Project Understanding Governance Rules
- Status: done
- Area: documentation, process, project governance
- Summary: Menambahkan dokumen aturan wajib agar AI dan developer memahami flow project secara menyeluruh sebelum melakukan perubahan, dengan penekanan pada route, route group, middleware, redirect, dan hubungan controller-view-helper.
- Impact: Mengurangi risiko perubahan berbasis asumsi parsial, membantu menjaga konsistensi flow lintas frontend dan backend, serta menjadikan README langsung mengarahkan pembaca ke aturan kerja yang benar.
- Files:
  - `README.md`
  - `docs/project-understanding-rules.md`
  - `docs/frontend-roadmap.md`
- Follow-up: Jika nanti ada area arsitektur baru seperti route file terpisah atau modul domain baru, tambahkan ke dokumen aturan ini agar checklist tetap relevan.

## 2026-07-01 - Accommodation Detail Check Price Access Alignment
- Status: done
- Area: accommodation detail, auth and access flow, frontend UX
- Summary: Menyelaraskan sidebar `Check Price` pada halaman accommodation detail dengan akses riil route project, sehingga form hanya muncul untuk user yang benar-benar lolos flow akses, sementara guest atau user yang masih tertahan verifikasi, profil, atau approval mendapat CTA lanjutan yang sesuai.
- Impact: Mengurangi mismatch antara UI dan middleware project, memperjelas langkah berikutnya bagi setiap status user, dan membuat flow check price dari halaman detail lebih dapat diprediksi.
- Files:
  - `app/Http/Controllers/FrontEndController.php`
  - `resources/views/frontend/accommodations/detail.blade.php`
  - `resources/lang/en/messages.php`
  - `resources/lang/zh/messages.php`
  - `resources/lang/zh-CN/messages.php`
  - `docs/frontend-roadmap.md`
- Follow-up: Audit halaman frontend lain yang menampilkan CTA partner-only agar state guest, verification, profile, dan approval juga konsisten.

## 2026-07-01 - Direct Login Entry for Accommodation Check Price
- Status: done
- Area: accommodation detail, login redirect flow
- Summary: Mengubah CTA guest `Login to Check Price` agar langsung menuju halaman login sambil membawa intended redirect kembali ke panel check price pada accommodation detail setelah login berhasil.
- Impact: Flow guest menjadi lebih jelas karena user langsung melihat halaman login, tetapi tetap kembali ke konteks check price yang benar setelah autentikasi.
- Files:
  - `app/Http/Controllers/Auth/LoginController.php`
  - `app/Http/Controllers/FrontEndController.php`
  - `docs/frontend-roadmap.md`
- Follow-up: Terapkan pola query redirect yang sama pada CTA login frontend lain bila membutuhkan return-to-context yang eksplisit.

## 2026-07-01 - Order Hotel Wizard Hardening and Shared Tab Nav
- Status: done
- Area: hotel booking create flow, frontend wizard behavior, shared Blade partials
- Summary: Memperkuat perilaku tab/wizard pada semua halaman `order-hotel` create flow dan mengekstrak markup tab step ke partial shared agar promo, normal, dan package mengikuti pola yang sama.
- Impact: Perpindahan step menjadi lebih stabil, panel aktif tetap sinkron dengan validasi form, dan drift markup antar halaman order-hotel berkurang.
- Files:
  - `public/frontend/js/pages/hotel-booking.js`
  - `resources/views/partials/hotel-booking-wizard-nav.blade.php`
  - `resources/views/form/order-hotel-promo.blade.php`
  - `resources/views/form/order-hotel-normal.blade.php`
  - `resources/views/form/order-hotel-package.blade.php`
  - `docs/frontend-roadmap.md`
- Follow-up: Browser-test ketiga flow order hotel untuk memastikan state review, add room, dan perhitungan price tetap sinkron setelah interaksi multi-step.

## 2026-07-02 - Home Services Section Redesign
- Status: done
- Area: frontend home services section
- Summary: Mendesain ulang section `Services` pada homepage frontend menjadi layout card yang lebih modern dengan shell visual baru, hierarchy heading yang lebih kuat, dan stylesheet terpisah khusus halaman.
- Impact: Area layanan utama di homepage kini terasa lebih premium, lebih jelas dibaca, dan lebih aman dirawat karena style tidak lagi bergantung pada inline markup.
- Files:
  - `resources/views/frontend/home/partials/services.blade.php`
  - `public/css/pages/frontend-home-services.css`
  - `docs/frontend-roadmap.md`
- Follow-up: Jika pola card ini dipakai kembali pada section frontend lain, ekstrak ke partial atau component shared agar hierarchy visual tetap konsisten.

## 2026-07-02 - Clean Surface Rule for Frontend Cards
- Status: done
- Area: frontend design standards, home services refinement
- Summary: Menetapkan aturan visual baru agar frontend menghindari pola `card di dalam card` tanpa alasan fungsional, lalu merapikan section `Services` agar media area tidak lagi tampil seperti nested card.
- Impact: Tampilan frontend bergerak ke arah yang lebih clean, ringan, dan modern dengan hierarchy surface yang lebih jelas.
- Files:
  - `docs/frontend-ui-standards.md`
  - `resources/views/frontend/home/partials/services.blade.php`
  - `public/css/pages/frontend-home-services.css`
  - `docs/frontend-roadmap.md`
- Follow-up: Audit section frontend lain yang masih memakai nested surface berlebihan dan sederhanakan ke pattern yang lebih ringan.

## 2026-07-02 - Services Cards Refined With Professional Imagery
- Status: done
- Area: frontend home services section
- Summary: Mendesain ulang ulang section `Services` agar hanya item layanan yang menjadi card utama, lalu mengganti visual tiap layanan dengan imagery yang lebih profesional dan relevan terhadap accommodation, transportation, dan tour package.
- Impact: Fokus visual section menjadi lebih bersih, lebih modern, dan lebih konsisten dengan arah UI frontend yang minim nested surface.
- Files:
  - `resources/views/frontend/home/partials/services.blade.php`
  - `public/css/pages/frontend-home-services.css`
  - `docs/frontend-roadmap.md`
- Follow-up: Bila section frontend lain masih memakai ikon lama atau ilustrasi yang tidak selevel, samakan pendekatan imagery-nya ke foto atau visual hero yang lebih premium.

## 2026-07-02 - Modern Informative Frontend Footer
- Status: done
- Area: shared frontend footer, newsletter subscribe interaction
- Summary: Mendesain ulang footer frontend menjadi layout yang lebih modern, lebih informatif, dan lebih sesuai standar website internasional dengan hierarchy brand, contact, navigation, services, newsletter, dan social links yang lebih jelas.
- Impact: Footer kini lebih mudah discan, lebih konsisten dengan arah visual clean-modern frontend, dan script subscribe tidak lagi inline di Blade.
- Files:
  - `resources/views/frontend/layouts/footer-modern.blade.php`
  - `resources/views/frontend/layouts/app.blade.php`
  - `public/css/components/frontend-footer.css`
  - `public/frontend/js/components/frontend-footer-subscribe.js`
  - `docs/frontend-roadmap.md`
- Follow-up: Jika section shared frontend lain masih menyimpan script inline atau styling lama global, pindahkan ke asset terpisah seperti pola footer ini.

## 2026-07-02 - Full B2B Homepage Redesign
- Status: done
- Area: frontend homepage, B2B messaging, shared home structure
- Summary: Mendesain ulang homepage frontend secara menyeluruh agar terasa lebih clean, modern, dan informatif untuk audience B2B dengan alur baru yang menekankan partner value proposition, workflow, service capability, live promotions, benefits, FAQ, dan final CTA.
- Impact: Homepage kini lebih fokus menjelaskan nilai bisnis platform, mengurangi section generik yang berulang, dan memberi struktur yang lebih meyakinkan untuk travel partners sejak first fold.
- Files:
  - `app/Http/Controllers/FrontEndController.php`
  - `resources/views/frontend/home/index.blade.php`
  - `resources/views/frontend/home/partials/hero-b2b.blade.php`
  - `resources/views/frontend/home/partials/services.blade.php`
  - `resources/views/frontend/home/partials/partner-flow.blade.php`
  - `resources/views/frontend/home/partials/hotel-promotion.blade.php`
  - `resources/views/frontend/home/partials/hotel-promotion-slide.blade.php`
  - `resources/views/frontend/home/partials/platform-overview.blade.php`
  - `resources/views/frontend/home/partials/benefits.blade.php`
  - `resources/views/frontend/home/partials/faqs-modern.blade.php`
  - `resources/views/frontend/home/partials/cta.blade.php`
  - `public/css/pages/frontend-home.css`
  - `docs/frontend-roadmap.md`
- Follow-up: Audit halaman service landing lain agar tone B2B, hierarchy content, dan visual quality-nya mengikuti baseline homepage baru ini.

## 2026-07-02 - Homepage Hero 100vh Adjustment
- Status: done
- Area: frontend homepage, hero sizing
- Summary: Menyetel section hero homepage agar memenuhi tinggi viewport desktop secara penuh dengan `height: 100vh`, sambil tetap menjaga perilaku mobile tetap fleksibel agar konten tidak terasa sempit.
- Impact: First fold homepage kini tampil lebih tegas, lebih presentable untuk audience B2B, dan terasa konsisten sebagai hero utama halaman.
- Files:
  - `public/css/pages/frontend-home.css`
  - `docs/frontend-roadmap.md`
- Verification: Dicek ulang pada viewport desktop 1440x900 dan tinggi `.home-hero` terbaca `900px`, sesuai tinggi viewport.

## 2026-07-02 - Homepage Market Positioning Refinement
- Status: done
- Area: frontend homepage, B2B messaging
- Summary: Menyelaraskan positioning homepage agar menekankan travel agent dan partner B2B dari seluruh dunia, tanpa menonjolkan market regional tertentu.
- Impact: Pesan bisnis menjadi lebih universal, lebih netral secara internasional, dan lebih sesuai untuk audience global yang menjadi target website.
- Files:
  - `resources/views/frontend/home/partials/hero-b2b.blade.php`
  - `resources/views/frontend/home/partials/partner-flow.blade.php`
  - `resources/views/frontend/home/partials/platform-overview.blade.php`
  - `resources/views/frontend/home/partials/benefits.blade.php`
  - `resources/views/frontend/home/partials/cta.blade.php`
  - `resources/views/frontend/layouts/footer-modern.blade.php`
  - `public/css/pages/frontend-home.css`
  - `docs/frontend-roadmap.md`

## 2026-07-02 - Homepage Final UI and UX Polish
- Status: done
- Area: frontend homepage, promotions, FAQ, services
- Summary: Merapikan section-section homepage yang masih terasa belum final, terutama promo hotel, FAQ, service cards, dan visual platform, agar hierarchy informasi lebih jelas dan tampilan lebih matang untuk audience B2B.
- Impact: Homepage kini lebih mudah dipindai, promo lebih informatif dan lebih besar secara visual, FAQ lebih rapi serta lebih mudah dipahami, dan keseluruhan ritme UI terasa lebih konsisten.
- Files:
  - `resources/views/frontend/home/partials/services.blade.php`
  - `resources/views/frontend/home/partials/hotel-promotion.blade.php`
  - `resources/views/frontend/home/partials/hotel-promotion-slide.blade.php`
  - `resources/views/frontend/home/partials/platform-overview.blade.php`
  - `resources/views/frontend/home/partials/faqs-modern.blade.php`
  - `public/css/pages/frontend-home.css`
  - `public/css/pages/frontend-home-services.css`
  - `docs/frontend-roadmap.md`

## 2026-07-02 - Homepage Translation Key Migration
- Status: done
- Area: frontend homepage, multilanguage content, lang cleanup
- Summary: Memigrasikan copy homepage aktif ke key translation terstruktur dalam file `home.php`, mengganti partial FAQ homepage ke versi clean berbasis key translation, dan memastikan section hero, services, partner flow, promotions, platform, benefits, FAQ, CTA, serta footer-home copy membaca konten dari file bahasa.
- Impact: Homepage kini lebih siap untuk multi-language tanpa hardcoded copy di section aktif, struktur lang menjadi lebih rapi untuk pengembangan homepage berikutnya, dan audit perubahan antar bahasa jadi lebih mudah.
- Files:
  - `resources/views/frontend/home/index.blade.php`
  - `resources/views/frontend/home/partials/hero-b2b.blade.php`
  - `resources/views/frontend/home/partials/services.blade.php`
  - `resources/views/frontend/home/partials/partner-flow.blade.php`
  - `resources/views/frontend/home/partials/hotel-promotion.blade.php`
  - `resources/views/frontend/home/partials/hotel-promotion-slide.blade.php`
  - `resources/views/frontend/home/partials/platform-overview.blade.php`
  - `resources/views/frontend/home/partials/benefits.blade.php`
  - `resources/views/frontend/home/partials/faqs-home.blade.php`
  - `resources/views/frontend/home/partials/cta.blade.php`
  - `resources/views/frontend/layouts/footer-modern.blade.php`
  - `resources/lang/en/home.php`
  - `resources/lang/zh/home.php`
  - `resources/lang/zh-CN/home.php`
  - `docs/frontend-roadmap.md`
- Follow-up: Audit shared footer, navbar, dan legacy partial homepage yang sudah tidak dipakai agar seluruh copy frontend utama konsisten memakai struktur translation modular yang sama.

## 2026-07-03 - Homepage Services Thumbnail Optimization
- Status: done
- Area: frontend homepage, image performance
- Summary: Mengubah source image pada section `Services` homepage agar mengambil cover representative dari data aktif hotel, transport, dan tour lalu merendernya melalui helper `getThumbnail()` alih-alih file statis `landing-page`.
- Impact: Homepage services kini memuat gambar hasil crop thumbnail dari `storage/public`, sehingga payload visual lebih efisien dan pendekatan optimasi gambar konsisten dengan pattern image project lainnya.
- Files:
  - `app/Http/Controllers/FrontEndController.php`
  - `resources/views/frontend/home/partials/services.blade.php`
  - `docs/frontend-roadmap.md`
- Follow-up: Jika diperlukan, tambahkan pre-generated thumbnail strategy atau pemilihan cover curated khusus homepage agar visual tetap paling representatif tanpa bergantung pada record aktif terbaru.

## 2026-07-03 - Shared Check Price Daterangepicker Fix
- Status: done
- Area: frontend accommodation detail, hotel availability, shared booking interaction
- Summary: Memindahkan inisialisasi daterangepicker `Check Price` ke script shared yang dipakai oleh card `partials.hotel-check-price-card`, sehingga panel yang sama bekerja konsisten pada halaman accommodation detail dan `hotel-price-{code}`.
- Impact: Flow pemilihan tanggal stay tidak lagi bergantung pada JS page tunggal, risiko daterangepicker tidak aktif di halaman lain berkurang, dan batas tanggal minimum mulai besok kini konsisten di seluruh card `Check Price` frontend.
- Files:
  - `resources/views/partials/hotel-check-price-card.blade.php`
  - `resources/views/frontend/layouts/app.blade.php`
  - `public/frontend/js/components/frontend-hotel-check-price.js`
  - `public/frontend/js/pages/accommodation-detail.js`
  - `public/css/components/hotel-check-price-card.css`
  - `docs/frontend-roadmap.md`
- Follow-up: Saat server lokal aktif, lakukan browser pass untuk memastikan picker muncul, tanggal hari ini tidak selectable, dan submit `checkin/checkout` sinkron pada halaman detail accommodation maupun hotel availability.

## 2026-07-03 - Hotel Package Price Duration Correction
- Status: done
- Area: hotel availability pricing, hotel package calculation
- Summary: Memperbaiki perhitungan `HotelPackage` agar `contract_rate` yang tersimpan sebagai harga per malam dikalikan dulu dengan `duration` paket sebelum konversi currency, penambahan markup, dan perhitungan tax.
- Impact: Nilai `Package total` pada `hotel-price-{code}` dan harga akhir pada flow order hotel package kini merefleksikan total stay duration yang benar, bukan harga single-night yang sebelumnya terpakai sebagai total paket.
- Files:
  - `app/Models/HotelPackage.php`
  - `docs/frontend-roadmap.md`
- Follow-up: Jika dibutuhkan, tambahkan breakdown visual `price per night x duration` pada card package agar user agent bisa memverifikasi komposisi total dengan lebih mudah.

## 2026-07-03 - Hotel Around Section UX Alignment
- Status: done
- Area: hotel availability supporting content, frontend consistency
- Summary: Mendesain ulang section `Hotel Around` pada halaman `hotel-price-{code}` agar mengikuti hierarchy visual `hotelavailability`, memakai surface section yang konsisten, grid related-hotel yang lebih modern, dan card pendukung yang lebih ringan daripada pola legacy card-box lama.
- Impact: Supporting content di bawah availability kini lebih mudah dipindai, lebih sejalan dengan standar frontend product interface, dan menjaga konsistensi visual antara result section utama dan related accommodation section.
- Files:
  - `resources/views/partials/near-hotel.blade.php`
  - `public/css/pages/hotel-availability.css`
  - `docs/frontend-roadmap.md`
- Follow-up: Jika section related-content serupa muncul pada halaman frontend lain, ekstrak pola ini ke partial atau modifier shared agar tidak terjadi drift antar halaman.

## Current Frontend Backlog

## F1 - Align Other Frontend Service Pages to Hotel Availability Shell
- Status: planned
- Area: services, transport detail, villa pages, tour package pages
- Summary: Menyamakan page shell, breadcrumb, summary cards, dan CTA hierarchy ke baseline `hotelavailability`.
- Impact: Konsistensi UI/UX antar domain layanan akan meningkat.
- Files:
  - `resources/views/home/...`
  - `resources/views/frontend/...`
- Follow-up: Tentukan prioritas halaman dengan traffic tertinggi terlebih dahulu.

## F2 - Introduce Shared Frontend Surface Tokens
- Status: planned
- Area: CSS shared components
- Summary: Mengekstrak token seperti radius, shadow, spacing, content width, and section gap ke level shared variables atau utility layer.
- Impact: Memudahkan konsistensi visual lintas halaman dan mengurangi style drift.
- Files:
  - `public/css/components/frontend-page-shell.css`
  - `public/css/pages/...`
- Follow-up: Audit style yang masih hardcoded per halaman.

## F3 - Canonical URL Audit for Dynamic Frontend Flows
- Status: planned
- Area: frontend route and redirect behavior
- Summary: Mengaudit halaman lain yang masih bergantung pada route transisional, POST-result URL, atau previous URL yang tidak stabil.
- Impact: Mengurangi risiko 404, refresh issue, dan redirect aneh pada flow yang kompleks.
- Files:
  - `routes/web.php`
  - `app/Http/Controllers/...`
  - `app/Helpers/helpers.php`
- Follow-up: Prioritaskan page yang punya form, filter, atau redirect login.

## F4 - Frontend Copy and Translation Coverage Audit
- Status: planned
- Area: translation completeness
- Summary: Merapikan seluruh copy frontend agar tone, terminology, dan translation coverage konsisten antar bahasa.
- Impact: Kualitas UX internasional meningkat dan mengurangi campuran bahasa yang membingungkan.
- Files:
  - `resources/lang/...`
  - `resources/views/frontend/...`
  - `resources/views/main/...`
- Follow-up: Buat daftar key translation yang masih hardcoded.

## F5 - Frontend QA Checklist Integration
- Status: planned
- Area: process and review
- Summary: Menjadikan checklist review frontend sebagai bagian wajib sebelum merge.
- Impact: Konsistensi implementasi tidak hanya bergantung pada ingatan developer.
- Files:
  - `docs/frontend-ui-standards.md`
  - `README.md`
- Follow-up: Jika perlu, tambahkan template PR atau release checklist.
