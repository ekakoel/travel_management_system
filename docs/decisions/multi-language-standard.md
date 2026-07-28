# Multi-Language Standard

Status: active
Updated: 2026-07-27

Project mendukung locale aktif:

- `en`
- `zh`
- `zh-CN`

## Aturan Wajib

- Semua teks user-facing di Blade, controller, service, notification, validation, dan JavaScript harus berasal dari language key.
- Key baru wajib ditambahkan ke semua locale aktif.
- Gunakan file domain untuk copy spesifik modul, misalnya `tour-detail.php`, `transport-management.php`, atau `admin-orders.php`.
- Gunakan `messages.php` hanya untuk frasa global yang benar-benar lintas domain.
- JavaScript membaca copy dari `data-*` attribute atau payload JSON dari Blade, bukan string hardcoded.
- Backend/internal UI juga wajib multi-language.

## Pola Blade

```blade
<h1>@lang('tour-detail.title')</h1>
<button aria-label="{{ __('messages.Save') }}">
    @lang('messages.Save')
</button>
```

## Pola JavaScript

```blade
<section data-empty-title="{{ __('admin-orders.empty.title') }}"></section>
```

```js
const emptyTitle = root.dataset.emptyTitle;
```

## Checklist Review

1. Tidak ada copy user-facing hardcoded baru.
2. Key tersedia di `en`, `zh`, dan `zh-CN`.
3. Attribute seperti `placeholder`, `title`, `aria-label`, dan label processing ikut diterjemahkan.
4. Status, alert, empty state, modal, table header, breadcrumb, dan button memakai key.
