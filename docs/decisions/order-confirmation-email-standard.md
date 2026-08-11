# Order Confirmation Email Standard

Status: active
Updated: 2026-08-03

## Purpose

Order confirmation adalah email transaksi internasional, bukan salinan halaman
admin. Email harus dapat dipahami tanpa membuka attachment, tetapi tidak boleh
memuat payload internal, raw JSON, catatan operator, atau data identitas guest.

## Content Contract

Email confirmation wajib berisi:

- brand dan concise preheader;
- subject `Order Confirmed | {order reference} | {brand}`;
- status Confirmed, order reference, dan reservation reference;
- service/product, travel dates, pax, pickup, dan drop-off;
- invoice number, Total Price USD authoritative, Amount Due beserta currency,
  serta payment due date;
- penjelasan attachment, CTA menuju protected order detail, dan fallback URL;
- waktu konfirmasi, operator, serta contact channel bisnis.

Data harga Tour berasal dari `OrderPricingSnapshotReader` dan committed invoice.
Email tidak menghitung harga dari input frontend atau master price terbaru.

## Presentation Contract

- Lebar desktop maksimal 640px dan tetap responsif pada mobile.
- Struktur utama menggunakan presentation tables dan style email-safe. Jangan
  menggunakan CSS grid, flexbox, JavaScript, form, video, atau remote stylesheet.
- Informasi penting tetap terbaca ketika remote images dan CSS media query
  diblokir. Brand text adalah mandatory; logo bukan satu-satunya identitas.
- CTA mempunyai label action-oriented dan fallback URL yang dapat disalin.
- Warna, hierarchy, whitespace, dan copy harus profesional, ringkas, dan tidak
  memakai istilah internal seperti workflow, handler, raw status payload, atau
  “Confirm by”.
- Semua nilai dinamis dirender melalui escaped Blade output.

## Compatibility and Security

- Template reusable menerima payload dari
  `OrderConfirmationEmailDataService`; business/presentation mapping tidak
  dirakit di Blade.
- Link order dibentuk server dan tetap tunduk pada authentication serta
  ownership guard ketika dibuka.
- Link tidak boleh dirakit dari string generic. Builder memilih named route
  canonical berdasarkan service; Tour Package wajib memakai
  `view.detail-order-tour` (`/detail-order-tour/{id}`).
- Attachment invoice tetap memakai protected financial file flow. Email tidak
  membuat public financial URL.
- Transactional email tidak menyediakan unsubscribe karena dikirim sebagai
  bagian langsung dari order aktif, tetapi footer harus menjelaskan sifat
  transaksi dan melarang pembagian link/attachment secara publik.

## Verification

Minimal verification mencakup Blade compilation, payload service, authoritative
Tour total, selected payable currency, escaped output, CTA/fallback URL, absence
of grid/flex/form/script, dan regression test proses Confirm/Resend.
