# Asset Migration Inventory

Status: active
Updated: 2026-07-27

Inventory ringkas asset aktif dan legacy.

## Aktif Frontend

- Source: `resources/frontend/js`, `resources/frontend/scss`.
- Public legacy yang masih mungkin aktif: `public/css/pages`, `public/frontend/js/pages`.
- Shared frontend components: tokens/base/page-shell/layout/components/forms/swiper/order-modal.

## Aktif Backend

- Source: `resources/backend/js`, `resources/backend/scss`.
- Shared backend components: `resources/backend/scss/components`.
- Backend global bundle: `resources/backend/scss/app.scss` dan `resources/backend/js/app.js`.

## Runtime / Vendor Yang Tidak Dimigrasikan

- Upload user/runtime di `public/storage` atau path storage terkait.
- Vendor static seperti CKEditor, plugin assets, dan package-provided readme/license.
- Generated build output kecuali manifest perlu ikut berubah setelah build.

## Legacy Candidate

Audit sebelum hapus:

- `public/css/pages/*`
- `public/frontend/js/pages/*`
- `resources/views/main/*`
- `resources/views/admin/*` yang belum dipindah

Gunakan `rg` pada Blade, controllers, `webpack.mix.js`, dan `public/mix-manifest.json` sebelum cleanup.
