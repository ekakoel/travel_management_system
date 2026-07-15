# Frontend Order Modal Standard

Dokumen ini menetapkan modal order pada halaman Activity Detail sebagai standar visual dan struktur mutlak untuk seluruh modal form order layanan di frontend `balikamitour`.

Penempatan file baru wajib mengikuti `docs/project-structure-standard.md`. Modal order publik berada di area `frontend/landing-page`, sedangkan order dashboard/detail user login berada di area `frontend/home`.

## Status Mutlak

Reference implementation:

- View: `resources/views/frontend/activities/detail.blade.php`
- Shared style: `resources/frontend/scss/components/frontend-order-modal.scss`
- Activity behavior: `resources/frontend/js/pages/activity-detail.js`
- Shared submit overlay markup: `resources/views/partials/form-submit-overlay.blade.php`

Modal order baru tidak boleh membuat shell, service header, tab navigation, action area, atau submit overlay dengan style mandiri. Gunakan kontrak class shared pada dokumen ini dan pertahankan field domain di dalam panel masing-masing.

## Cakupan

Standar ini wajib untuk modal frontend yang membuat order atau reservation layanan, termasuk:

- Activity Detail
- Tour Detail
- Transport Detail
- modal order layanan frontend baru yang ditambahkan kemudian

Modal preview dokumen, invoice, receipt, gallery, konfirmasi pembayaran, dan dialog informasi bukan modal form order layanan. Modal tersebut mengikuti standar modal sesuai fungsinya dan tidak wajib memakai wizard order.

## Urutan Struktur Wajib

```text
Modal Order
  Service Detail
    Image
    Service name
    Service metadata
    Price summary
  Tab Navigation
  Active Tab
    Title and one short description
    Input form area
    Local validation/error surface
  Action Area
  Fullscreen Submit Overlay
```

Informasi layanan harus berada di atas tab navigation. Informasi dan instruksi tidak boleh diduplikasi di dalam tab yang sama.

Area review order wajib memakai separator yang konsisten lintas modal. Gunakan separator horizontal pada setiap item review dan jangan memakai separator kiri berbasis urutan kolom, karena item review bisa tampil/sembunyi dinamis sesuai tipe layanan.

## Kontrak Class Wajib

Gunakan class berikut pada markup modal:

- Root: `frontend-order-modal`
- Dialog: `frontend-order-modal__dialog`
- Surface: `frontend-order-modal__surface`
- Form: `frontend-order-modal__form`
- Close action: `frontend-order-modal__close`
- Service block: `frontend-order-modal__service`
- Service image: `frontend-order-modal__media`
- Service content: `frontend-order-modal__service-content`
- Eyebrow and title: `frontend-order-modal__eyebrow`, `frontend-order-modal__title`
- Metadata: `frontend-order-modal__summary`, `frontend-order-modal__summary-card`
- Price: `frontend-order-modal__price-card`
- Navigation: `frontend-order-modal__nav`, `frontend-order-modal__nav-item`
- Panel: `frontend-order-modal__panel`
- Panel heading: `frontend-order-modal__heading`, `frontend-order-modal__heading-eyebrow`
- Actions: `frontend-order-modal__actions`
- Submit overlay: `frontend-order-modal__overlay`

Class domain seperti `activity-*`, `tour-*`, atau `transport-*` tetap boleh dipakai untuk behavior dan styling data yang benar-benar spesifik. Class domain tidak boleh mengubah fondasi visual shared di atas tanpa keputusan desain yang didokumentasikan.

## Asset Rule

Page entry yang memiliki modal order wajib mengimpor komponen shared setelah style domain agar kontrak shared menjadi final visual layer:

```scss
@import '../components/frontend-order-modal';
```

Jangan menyalin isi `frontend-order-modal.scss` ke file page-level.

## Submit Behavior Wajib

1. Overlay dipindahkan ke `document.body` sebelum request dikirim.
2. Overlay memakai `position: fixed`, `inset: 0`, dan `z-index: 2147483647`.
3. Spinner card berada tepat di tengah viewport.
4. `html` dan `body` memakai `frontend-order-submit-locked` selama processing.
5. Form memakai `aria-busy="true"` dan `inert` selama processing.
6. Tombol navigation dan submit dinonaktifkan selama request.
7. Modal tidak boleh ditutup ketika submit sedang diproses.
8. State harus dipulihkan dengan aman saat history restore atau validation response.
9. Setiap form order/reservation frontend wajib memiliki checkbox konfirmasi `terms_accepted` dengan teks: "I confirm the booking details are correct and agree to the applicable cancellation policy and terms." Checkbox ini wajib memakai partial `resources/views/partials/order-confirmation-checkbox.blade.php`, field `required`, dan validasi backend `accepted`.
10. Submit final tidak boleh diproses sebelum `terms_accepted` dicentang. Flow custom submit wajib memanggil validasi browser atau helper validasi khusus sebelum menampilkan processing overlay.

## Responsive Standard

1. Desktop memakai service detail dua kolom dan navigation tiga kolom.
2. Mobile mengubah service detail, metadata, navigation, heading, dan actions menjadi satu kolom.
3. Action button memenuhi lebar container pada mobile.
4. Modal dan overlay tidak boleh menyebabkan horizontal scroll.
5. Root modal order (`frontend-order-modal` atau root custom modal layanan) wajib menjadi area scroll vertikal ketika tinggi modal melebihi monitor.
6. Surface modal (`frontend-order-modal__surface`) wajib menampilkan isi secara utuh dan tidak boleh menjadi scroll container internal.
7. Jangan menambahkan scroll vertikal mandiri pada dialog, `modal-content`, `modal-body`, atau panel tab untuk order modal layanan.
8. Scroll modal wajib memakai `overscroll-behavior: contain` agar scroll tidak diteruskan ke halaman di belakang modal.

## Catatan Implementasi Domain

- Tour Detail review price summary (`Price/Pax` dan `Total Price`) wajib tampil sebagai baris grid horizontal yang mudah discan sebelum checkbox konfirmasi terms. Gunakan pola `display: grid`, `grid-auto-flow: column`, `justify-content: space-between`, `align-content: center`, dan padding horizontal 18px pada item harga.
- Transport Detail wajib menempatkan semua field yang menentukan layanan di tab `Service`, termasuk `flight type`, `flight number`, `flight date/service date`, `duration`, `pickup location`, dan `drop-off location`. Tab `Guest Details` hanya untuk data tamu. Untuk Daily Rent, `service date`, `duration`, `pickup location`, dan `drop-off location` wajib berada dalam satu grup field tanpa section separator; `service date` dan `duration` tampil satu baris, `pickup location` dan `drop-off location` tampil satu baris, dengan gap kolom `18px`.
- Seluruh teks frontend Transport Detail, termasuk modal order dan teks dinamis JavaScript, wajib memakai language key. Gunakan `resources/lang/{locale}/transports.php` untuk copy domain transport dan kirim label JS melalui atribut `data-transport-*` pada root halaman.

## Checklist Review

1. Apakah seluruh kontrak class wajib sudah dipakai?
2. Apakah detail layanan berada di atas navigation?
3. Apakah setiap tab hanya memiliki satu title dan satu penjelasan singkat?
4. Apakah informasi yang sama tidak tampil dua kali dalam tab yang sama?
5. Apakah action area berada setelah input form?
6. Apakah overlay menutup seluruh viewport dan berada di atas modal?
7. Apakah scroll, click, touch, dan fokus form terkunci selama processing?
8. Apakah checkbox `terms_accepted` tampil sebelum action submit final dan wajib dicentang?
9. Apakah layout tetap usable pada desktop dan mobile?
10. Apakah feature test dan `FrontendOrderModalStandardTest` lulus?
11. Apakah asset frontend berhasil dikompilasi?
