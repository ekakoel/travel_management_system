# Blade Asset Separation Rules

Status: active
Updated: 2026-07-27

Blade harus fokus pada markup, binding, component, dan include. Styling, behavior, dan logic berat harus berada di tempat yang tepat.

## Larangan

- Jangan menambah `<style>` di Blade.
- Jangan menambah `<script>` inline di Blade.
- Jangan menambah handler inline seperti `onclick`, `onchange`, atau `onload`.
- Jangan menjalankan query database di Blade.
- Jangan melakukan shaping data berat, sorting/filtering kompleks, atau perhitungan domain di Blade.
- Jangan menaruh inline `style=""` kecuali nilai dinamis benar-benar tidak realistis dipindah ke class CSS.

## Pola Asset

- CSS frontend publik: `resources/frontend/scss/landing-page/...` atau component shared frontend.
- JS frontend publik: `resources/frontend/js/landing-page/...` atau component shared frontend.
- CSS/JS authenticated frontend: `resources/frontend/scss/home/...`, `resources/frontend/js/home/...`.
- CSS/JS backend: `resources/backend/scss/...`, `resources/backend/js/...`.
- Asset per halaman dimuat dengan `@push('styles')` dan `@push('scripts')`.
- Asset reusable dimasukkan ke shared component/layer global jika sudah punya minimal dua pemakai nyata.

## Data Untuk JavaScript

Gunakan `data-*` attribute atau JSON minimal dari Blade:

```blade
<div data-success-message="{{ __('messages.Saved successfully') }}"></div>
```

## Dokumentasi

Perubahan frontend yang menyentuh Blade, CSS, JavaScript, layout, komponen, copy, atau interaction harus dicatat di `docs/decisions/frontend-roadmap.md`.
