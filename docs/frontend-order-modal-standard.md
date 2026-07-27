# Frontend Order Modal Standard

Status: active
Updated: 2026-07-27

Modal order service frontend harus satu keluarga visual dan behavior, dengan Activity Detail sebagai baseline.

## Scope

Berlaku untuk modal order frontend pada activity, tour, transport, accommodation/hotel, villa, dan service sejenis. Tidak berlaku untuk modal dokumen, invoice, gallery, atau payment confirmation biasa.

## Struktur Modal

- Root memakai kontrak class `frontend-order-modal*`.
- Header menampilkan service image/name/metadata/price sebelum tab navigation.
- Setiap tab punya satu title, satu deskripsi pendek, field area, dan action area.
- Navigation step/tab harus konsisten dan tidak membuat flow meloncat tanpa validasi.
- Submit overlay memakai `frontend-order-modal__overlay` dan tampil fullscreen di atas konten.

## Behavior

- Submit memakai `POST -> Redirect -> GET`.
- Token submit/idempotency mengikuti `docs/form-submit-standard.md`.
- Tombol submit menampilkan spinner inline dan label processing.
- Semua action yang bisa membuat submit ganda dikunci.
- Error validasi membuka step yang memiliki error pertama.
- Copy modal dan label processing memakai language key.

## Asset

- Style shared: `resources/frontend/scss/components/frontend-order-modal.scss`.
- Behavior page-level boleh berbeda per domain, tetapi utility common harus reusable.
- Jangan membuat shell modal order baru di CSS halaman jika class shared sudah mencukupi.
