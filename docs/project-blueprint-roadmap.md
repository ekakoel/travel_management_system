# Project Blueprint and Separation Roadmap

Dokumen ini menjadi blueprint tingkat produk dan arsitektur experience untuk project `balikamitour`, dengan fokus pada:

1. posisi produk sebagai platform B2B travel
2. pemisahan yang jelas antara frontend dan backend
3. standardisasi UI/UX, tema, dan flow
4. roadmap implementasi bertahap yang realistis terhadap codebase saat ini

Dokumen ini melengkapi:

- `README.md`
- `docs/project-understanding-rules.md`
- `docs/frontend-ui-standards.md`
- `docs/asset-architecture-blueprint.md`
- `docs/asset-migration-inventory.md`

## 1. Product Vision

`balikamitour` pada dasarnya adalah platform B2B travel management yang menghubungkan:

- travel agent luar negeri sebagai buyer atau reseller
- vendor dan partner lokal Indonesia sebagai penyedia layanan
- tim internal sebagai operator, curator, finance, reservation, dan quality control

Nilai utama platform ini bukan hanya katalog layanan wisata, tetapi kemampuan untuk:

- menemukan produk pariwisata Indonesia yang terpercaya
- menggabungkan beberapa layanan menjadi itinerary atau order yang operasional
- mengelola booking, approval, payment, contract, dan komunikasi vendor dalam satu sistem
- menjaga kualitas presentasi produk luxury agar cocok untuk pasar internasional

## 2. Core Business Model

Model bisnis inti yang paling cocok untuk project ini adalah:

### Buyer Side

- travel agent internasional
- B2B wholesaler
- destination specialist
- wedding planner atau luxury event planner
- corporate trip arranger

### Supply Side

- luxury accommodation
- transport provider
- tour operator
- guide dan aktivitas
- wedding venue dan wedding service provider
- local partner atau DMC-style supplier

### Internal Operating Layer

- reservation team
- contracting team
- content and product curation team
- finance and payment confirmation team
- operations and transport coordination team
- admin atau developer untuk governance dan configuration

## 3. Product Positioning

Posisi produk yang sebaiknya dipertegas ke depan:

`A curated B2B Indonesia travel sourcing and operations platform for international travel agents.`

Artinya, website ini bukan sekadar marketplace open listing. Platform ini harus terasa seperti:

- curated supplier network
- premium service sourcing portal
- operational workspace untuk pemesanan dan follow-up
- trust layer antara buyer internasional dan vendor lokal

## 4. Experience Pillars

Blueprint experience project ini perlu berdiri di atas 5 pilar:

1. `Trust`
   Semua halaman harus terasa aman, profesional, dan kredibel untuk partner internasional.
2. `Clarity`
   User harus cepat paham produk, harga, syarat, dan tindakan berikutnya.
3. `Operational readiness`
   Flow tidak boleh berhenti di katalog; harus nyambung ke order, approval, payment, dan confirmation.
4. `Curated luxury presentation`
   Akomodasi, transport, dan tour harus tampil premium, bukan seperti data dump supplier.
5. `Role-appropriate complexity`
   Frontend harus mudah dipakai agent. Backend boleh lebih kompleks, tetapi tetap efisien untuk tim internal.

## 5. Current Project Reality

Berdasarkan struktur route, controller, dan view saat ini, project sudah memuat fondasi besar, tetapi experience layer masih bercampur.

### Kekuatan Saat Ini

- public frontend untuk home, accommodation, transport, tour, policy, review, dan agent registration
- auth, profile completeness, approval, dan intended redirect flow sudah ada
- area internal yang kuat untuk reservation, hotel, transport, wedding, users, promotions, downloads, dan payment
- frontend modernization sudah mulai berjalan di beberapa halaman penting
- asset source frontend baru sudah mulai dipindah ke `resources/frontend/*`

### Tantangan Saat Ini

- view masih tersebar di beberapa domain campuran:
  - `resources/views/frontend/*`
  - `resources/views/home/*`
  - `resources/views/main/*`
  - `resources/views/form/*`
  - `resources/views/backend/*`
  - `resources/views/admin/*`
- route public, route user-facing after login, dan route internal masih hidup dalam file route yang sangat besar
- naming dan flow antar halaman belum selalu memisahkan “sales experience” dan “operations workspace”
- standar visual modern baru konsisten di sebagian family halaman
- beberapa flow masih terasa diwarisi dari panel operasional lama

## 6. Blueprint Experience Architecture

Target experience architecture yang disarankan:

### A. Frontend Public and Agent Experience

Area ini ditujukan untuk:

- visitor publik
- calon agent
- agent yang sudah login tetapi sedang bekerja di sisi browsing, comparing, dan ordering

Tujuan utama:

- discovery
- trust building
- product comparison
- rate checking
- order initiation
- order tracking ringan

Konten utama:

- homepage B2B
- about, contact, services
- accommodation directory and detail
- transport directory and detail
- tour package directory and detail
- legal pages dan FAQ
- login, register, forgot password
- order history dan order detail versi agent-friendly

Karakter UI:

- premium, clean, international
- content-first
- CTA jelas
- shareable URL
- mobile-friendly

### B. Backend Operations Workspace

Area ini ditujukan untuk:

- admin
- reservation
- operations
- contracting
- finance
- content/product team
- developer

Tujuan utama:

- manage inventory
- manage supplier data
- create dan follow up order
- payment verification
- contract, invoice, download, and reporting
- task execution dan operational control

Konten utama:

- dashboard
- hotel, room, promo, package management
- transport management
- tour management
- wedding management
- user and approval management
- booking code, bank account, tax, policy management
- notifications, logs, export, reporting

Karakter UI:

- dense but organized
- form-heavy but efficient
- role-aware
- table, filter, action panel, and process status centric

### C. Shared System Layer

Layer bersama yang dipakai kedua domain:

- authentication
- profile completeness
- approval flow
- language switching
- global notification
- document generation
- helper dan canonical redirect logic

## 7. Information Architecture Target

### Frontend Navigation Target

Navigasi frontend sebaiknya dipusatkan pada 6 kelompok:

1. `Home`
2. `Accommodations`
3. `Transportations`
4. `Tour Packages`
5. `Why Partner With Us`
6. `Help / Policy / FAQ`

Area user login di frontend sebaiknya dipisahkan menjadi:

1. `My Orders`
2. `My Profile`
3. `Saved / Recent Context` jika nanti dibutuhkan

### Backend Navigation Target

Navigasi backend sebaiknya dibagi berdasarkan domain kerja, bukan campuran entity acak:

1. `Dashboard`
2. `Product Management`
   Accommodation, transport, tours, activities, wedding products
3. `Orders and Reservations`
   Hotel, transport, tour, wedding, payment confirmation
4. `Partners and Agents`
   Agents, vendors, partners, contracts
5. `Finance and Documents`
   Invoice, contract, downloads, tax, bank accounts
6. `Content and Experience`
   Policy, FAQ, homepage content, promotions, reviews, UI config
7. `System and Governance`
   Users, approval, notifications, logs, configuration

## 8. UI/UX Standardization Blueprint

## Frontend Theme Direction

Tema frontend yang direkomendasikan:

- luxury modern
- clean editorial
- B2B professional
- Indonesia destination richness without becoming visually noisy

Visual language:

- warm neutral base
- tropical-luxury accent secukupnya
- premium photography
- spacious layout
- restrained motion
- concise copy

Prinsip visual:

1. Jangan terasa seperti panel admin yang dipoles.
2. Jangan terasa seperti marketplace mass listing.
3. Harus terasa curated, premium, dan trusted.
4. CTA harus fokus pada next action: check price, request quote, reserve, view details.

## Backend Theme Direction

Tema backend yang direkomendasikan:

- operational clarity
- fast scanning
- status-heavy but readable
- lower ornament, higher efficiency

Prinsip visual:

1. Gunakan hierarchy yang kuat untuk status, priority, approval, dan payment state.
2. Kurangi dekorasi yang tidak membantu decision making.
3. Standarkan form, filter bar, tab process, summary cards, dan right action rail.
4. Bedakan jelas halaman entry data, halaman detail entity, dan halaman workflow.

## UX Flow Principles

Standar flow yang harus dipakai ke depan:

1. Setiap halaman harus jelas menjawab:
   - user sedang ada di mana
   - data apa yang sedang dilihat
   - tindakan utama berikutnya apa
2. Semua flow user-facing harus URL-safe, refresh-safe, dan language-safe.
3. Flow login harus kembali ke konteks asal bila memang user datang dari CTA tertentu.
4. Form panjang harus dipecah menjadi step yang ringan.
5. Empty state dan no-result state harus mendorong next action, bukan berhenti.
6. Order summary harus selalu terlihat saat user akan commit action penting.
7. Status order, payment, approval, dan document harus memakai bahasa yang konsisten.

## 9. Page Separation Blueprint

Pemisahan halaman yang direkomendasikan:

### Frontend Pages

Lokasi target:

- `resources/views/frontend/layouts/*`
- `resources/views/frontend/pages/*`
- `resources/views/frontend/partials/*`
- `resources/views/frontend/components/*`

Yang seharusnya masuk frontend:

- home
- services landing
- accommodation directory/detail/check price
- transport directory/detail
- tour package directory/detail
- public legal pages
- auth public shell
- profile ringan agent
- order history dan order detail agent-facing

### Backend Pages

Lokasi target:

- `resources/views/backend/layouts/*`
- `resources/views/backend/pages/*`
- `resources/views/backend/partials/*`
- `resources/views/backend/components/*`

Yang seharusnya masuk backend:

- CRUD internal
- inventory management
- pricing management
- promo dan package management
- reservation workspace
- finance
- reports
- notifications
- admin settings

### Transitional / Shared Pages

Masuk `resources/views/shared/*` bila benar-benar dipakai lintas domain:

- auth fragments tertentu
- common modal partial
- document shell generik
- generic table/status component

Catatan penting:

`resources/views/home/*`, `resources/views/main/*`, dan sebagian `resources/views/form/*` saat ini sebaiknya dianggap area transisi, bukan struktur final.

## 10. Route and Controller Blueprint

Tanpa mengubah seluruh sistem sekaligus, arah ideal route adalah:

### Public Frontend

- public discovery pages
- detail pages
- legal pages
- agent registration
- auth entry pages

### Agent Frontend

- authenticated user-facing pages
- profile
- order history
- order detail
- booking entry with frontend shell

### Backend Operations

- authenticated
- `profile.complete`
- `approve`
- role or position gated

Karena `routes/web.php` saat ini sangat besar, roadmap refactor sebaiknya bergerak menuju pemisahan konseptual berikut:

1. public frontend routes
2. auth and account routes
3. agent workspace routes
4. backend operations routes
5. admin and governance routes

Controller juga sebaiknya dibedakan semakin tegas:

- frontend presentation controller
- agent workspace controller
- backend management controller
- shared action controller

## 11. Asset and Design System Blueprint

Struktur asset target tetap mengikuti arah dokumen arsitektur yang sudah ada:

- `resources/frontend/*`
- `resources/backend/*`
- `resources/shared/*`
- `public/build/*`

### Frontend Design System

Minimal punya layer:

1. tokens
2. base
3. page shell
4. layout primitives
5. components
6. forms
7. domain modifiers

### Backend Design System

Minimal punya layer:

1. tokens
2. base
3. app shell
4. data table and filter components
5. form system
6. workflow status components
7. dashboard modules

## 12. Roadmap Separation and Standardization

Roadmap ini disusun agar realistis terhadap codebase sekarang dan tidak memaksa big-bang rewrite.

## Phase 0 - Governance Lock

Status target: immediate

Tujuan:

- menyepakati arah produk dan domain experience
- mencegah perubahan baru masuk ke struktur lama tanpa kontrol

Deliverables:

- blueprint ini dipakai sebagai acuan
- semua fitur baru menentukan sejak awal: frontend, backend, atau shared
- view baru tidak lagi dibuat di folder transisi bila target folder final sudah jelas

Success metric:

- tidak ada halaman baru user-facing yang masuk ke folder legacy campuran tanpa alasan

## Phase 1 - Domain Mapping and Audit

Status target: 1 sampai 2 minggu

Tujuan:

- memetakan seluruh halaman aktif ke domain frontend, backend, atau shared
- menentukan mana yang agent-facing dan mana yang operational

Deliverables:

- inventory semua route utama
- inventory semua view aktif
- matrix page ownership:
  - frontend public
  - frontend agent
  - backend operations
  - shared or transitional

Prioritas audit:

- `resources/views/home/*`
- `resources/views/main/*`
- `resources/views/form/*`
- order family
- transport family
- tour package family

Success metric:

- seluruh halaman prioritas tinggi punya status domain yang jelas

## Phase 2 - Frontend Shell Standardization

Status target: 2 sampai 4 minggu

Tujuan:

- menyamakan shell, hierarchy, breadcrumb, CTA, dan content rhythm semua halaman frontend penting

Prioritas halaman:

1. homepage
2. accommodation directory and detail
3. transport directory and detail
4. tour package directory and detail
5. auth pages
6. legal and FAQ pages
7. order history dan order detail

Deliverables:

- satu keluarga shell frontend resmi
- summary card standard
- sidebar CTA standard
- empty state standard
- frontend copy pattern standard

Success metric:

- user bisa berpindah antar domain layanan tanpa merasa pindah ke website yang berbeda

## Phase 3 - Backend Workspace Standardization

Status target: 3 sampai 6 minggu

Tujuan:

- membuat backend terasa seperti workspace operasional yang konsisten

Prioritas modul:

1. dashboard
2. hotel and pricing management
3. transport management
4. tour management
5. order and payment flows
6. policy and content management
7. user and approval management

Deliverables:

- app shell backend standar
- filter and action bar standard
- detail page standard
- CRUD form standard
- workflow status badge and timeline standard

Success metric:

- waktu orientasi user internal pada modul baru berkurang

## Phase 4 - Physical View Separation

Status target: 4 sampai 8 minggu, bertahap

Tujuan:

- memindahkan halaman aktif dari folder transisi ke struktur domain final

Urutan rekomendasi:

1. halaman frontend aktif dari `home/*` dan `main/*`
2. halaman order agent-facing dari `order/*` dan `form/*`
3. halaman backend CRUD dari `form/*` campuran
4. partial shared yang memang reusable

Deliverables:

- `resources/views/frontend/pages/*`
- `resources/views/backend/pages/*`
- `resources/views/shared/*`
- referensi include dan layout yang lebih bersih

Success metric:

- developer baru bisa mengenali domain halaman hanya dari path file

## Phase 5 - Route and Controller Refactor

Status target: paralel bertahap setelah Phase 2 dan 4 stabil

Tujuan:

- memisahkan lapisan presentation dan operations lebih rapi

Deliverables:

- grouping route konseptual lebih jelas
- controller frontend tidak menanggung logic internal berlebihan
- controller backend fokus pada management workflow

Success metric:

- perubahan frontend tidak sering menyentuh controller backend management tanpa alasan

## Phase 6 - UX Flow Hardening

Status target: ongoing

Tujuan:

- menyederhanakan flow penting agar lebih mudah dipakai agent dan tim internal

Flow yang wajib dipoles:

1. login to intended destination
2. accommodation check price to booking
3. transport detail to reservation
4. tour package detail to order
5. order review to payment confirmation
6. approval pending to next action
7. policy and FAQ discovery

Deliverables:

- audit friction point
- standar wizard multi-step
- standar sticky review panel
- standar success, error, dan pending states

Success metric:

- lebih sedikit kebingungan user di langkah submit, payment, dan return flow

## 13. Recommended Priority Order

Jika tim ingin fokus ke area dengan impact tertinggi, urutan prioritas bisnis dan UX yang disarankan:

1. `Accommodation` karena ini kemungkinan core commercial funnel terbesar
2. `Transport` karena sering menjadi add-on operasional dan butuh clarity tinggi
3. `Tour Packages` karena paling membutuhkan presentasi premium dan storytelling
4. `Agent auth and profile flow` karena ini gerbang semua konversi
5. `Order history and order detail` karena menyangkut trust setelah transaksi
6. `Backend order and payment workspace` karena menyangkut eksekusi layanan

## 14. Design and Product Rules Going Forward

Mulai sekarang, setiap perubahan besar sebaiknya menjawab 5 pertanyaan ini terlebih dahulu:

1. Halaman ini milik frontend, backend, atau shared?
2. User utama halaman ini siapa?
3. Tindakan utama yang harus diselesaikan user di halaman ini apa?
4. Apakah visual dan flow-nya sudah satu keluarga dengan domainnya?
5. Apakah perubahan ini memperjelas atau justru mencampur domain experience?

## 15. Definition of Done for Future Page Work

Sebuah halaman dianggap selesai dengan standar blueprint ini bila:

1. domain experience-nya jelas
2. route dan middleware flow-nya jelas
3. layout dan asset domain-nya benar
4. CTA dan next action-nya jelas
5. UI mengikuti design system domain terkait
6. copy dan status text mudah dipahami user
7. URL, redirect, dan return flow aman
8. dokumentasi roadmap relevan ikut diperbarui

## 16. Final Direction

Arah terbaik project ini bukan menjadi “website travel umum”, tetapi menjadi:

platform B2B travel sourcing dan operations untuk layanan pariwisata Indonesia yang curated, premium, dan operasional-ready.

Pemisahan frontend dan backend harus mengikuti arah bisnis tersebut:

- frontend menjual kepercayaan, discovery, dan conversion
- backend mengeksekusi operasional, kontrol, dan governance
- shared layer menjaga auth, data, language, dan workflow tetap konsisten
