# Asset Architecture Blueprint

Status: active
Updated: 2026-07-27

Blueprint ini menjelaskan arah asset source dan build project.

## Prinsip

- Source asset aktif berada di `resources/frontend` atau `resources/backend`.
- Output build berada di `public/build` dan dicatat di `public/mix-manifest.json`.
- Legacy public asset boleh disentuh hanya jika halaman aktif belum dimigrasi.
- Shared component dibuat saat ada minimal dua consumer nyata.
- Jangan membuat CSS/JS inline di Blade.

## Struktur Target

```text
resources/frontend/
  js/landing-page/
  js/home/
  js/shared/
  scss/landing-page/
  scss/home/
  scss/components/
  scss/shared/

resources/backend/
  js/admin/
  js/operations/
  js/shared/
  scss/admin/
  scss/operations/
  scss/components/
```

## Layer Shared

- Frontend tokens/base/layout/components/forms/swiper/order-modal.
- Backend theme/hero/breadcrumb/sidebar/KPI/panel/filter/action/status/alert/list/empty/modal/form/richtext/detail-layout.

## Jangan Dilakukan

- Jangan edit compiled CSS/JS sebagai source utama bila source SCSS/JS tersedia.
- Jangan membuat bundle global kedua untuk backend.
- Jangan menaruh backend selector di frontend asset atau sebaliknya.
- Jangan memindahkan upload/runtime files ke source asset.
