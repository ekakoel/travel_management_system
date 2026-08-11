# PDF Localization Standard

Status: active
Updated: 2026-08-03

## Scope

Dokumen ini adalah kontrak canonical untuk PDF yang dibuat atau direfactor.
Implementasi pertama berlaku pada invoice Tour Package yang dibuat dari
workflow order admin. PDF legacy di layanan lain wajib dimigrasikan ke kontrak
ini ketika flow tersebut disentuh; dokumen historis tidak ditulis ulang secara
diam-diam.

## Locale Canonical

Setiap jenis PDF wajib tersedia dalam tiga locale berikut:

- `en`: English.
- `zh-CN`: Chinese Simplified.
- `zh`: Chinese Traditional. Kode legacy `zh` tetap berarti Traditional untuk
  menjaga kompatibilitas URL dan nama file yang telah digunakan.

Locale memakai allowlist exact. Locale tidak dikenal tidak boleh diam-diam
diarahkan ke English karena dapat membuat user mengunduh dokumen yang salah.

## Rendering dan Font

- Dokumen menyatakan UTF-8 dan atribut `lang` sesuai locale.
- PDF Chinese wajib memakai font CJK lokal yang terdaftar pada Dompdf. Standard
  project saat ini adalah family `notosans`, yang mengarah ke file lokal
  `storage/fonts/NotoSans-Light.ttf` dan `NotoSans-Bold.ttf`.
- Font Chinese tidak boleh bergantung pada font sistem client, CDN, atau remote
  URL. Font harus di-embed/subset oleh renderer agar glyph tidak menjadi kotak.
- English, Simplified Chinese, dan Traditional Chinese mempunyai copy yang
  benar-benar berbeda. Satu template Chinese boleh dipakai bersama hanya bila
  seluruh label dipilih dari dictionary locale explicit.

## Generation dan Delivery

- Satu aksi generate/regenerate invoice Tour menghasilkan `_en.pdf`,
  `_zh-CN.pdf`, dan `_zh.pdf` dalam satu rangkaian.
- Form generate/regenerate pada workflow admin wajib memakai named route dengan
  method `PUT`; URL action tidak boleh dirangkai manual di Blade.
- Invoice untuk regenerate wajib diambil melalui relasi canonical
  `Order -> Reservation -> InvoiceAdmin`. Query invoice terpisah berdasarkan
  urutan record tidak boleh digunakan karena invoice adalah milik reservation.
- Semua file finansial tetap di private storage dan hanya dikirim melalui
  controller terproteksi dengan ownership/role guard, `nosniff`, serta private
  no-store cache policy.
- Validation Actions menampilkan satu button explicit per bahasa. Button hanya
  tampil bila file locale tersebut tersedia.
- Invoice Tour lama yang belum lengkap tidak ditulis ulang otomatis. Pada order
  Approved/Paid, Validation Actions menyediakan Regenerate 3-language Invoice
  dan Send/Resend Confirmation menunggu sampai ketiga file tersedia.
- Email confirmation Tour melampirkan ketiga invoice. Regeneration tidak
  mengubah snapshot harga, invoice amount, status order, atau lifecycle.
- Invoice multi-currency selalu menampilkan nilai referensi USD authoritative
  bersama Amount Due dalam mata uang pembayaran. Nilai USD berasal dari
  snapshot/invoice historis dan tidak dihitung ulang memakai rate terbaru.

## Verification

Perubahan PDF memverifikasi minimal allowlist route, pemisahan filename ketiga
locale, signature `%PDF`, penggunaan registered local CJK family, representative
Simplified/Traditional copy, dan authorization preview/download.
