# Multi-Language Standard

Dokumen ini menetapkan standar wajib untuk seluruh teks UI, pesan validasi, label tombol, status, notifikasi, dan copy halaman pada project `balikamitour`.

## Status Wajib

Project ini mendukung multi-language. Setiap perubahan UI baru wajib memakai language key dan tidak boleh menambahkan hardcoded copy di Blade, controller, service, atau JavaScript kecuali untuk nilai teknis yang tidak tampil ke user.

Locale aktif yang harus dijaga:

- `en`
- `zh`
- `zh-CN`

## Tujuan

- Menjaga pengalaman user internasional tetap konsisten.
- Mencegah campuran bahasa pada frontend dan backend.
- Membuat copy mudah diaudit, diterjemahkan, dan dipakai ulang.
- Menghindari hardcoded text yang tersebar di Blade, JavaScript, dan controller.

## Aturan Wajib

1. Semua teks yang tampil ke user wajib memakai `@lang(...)`, `__('...')`, atau data translation yang sudah dibentuk di controller/service.
2. Jangan menulis label, heading, button text, empty state, tooltip, status, alert, atau helper text secara hardcoded di Blade.
3. Jangan menulis teks user-facing secara hardcoded di JavaScript. Kirim teks melalui `data-*` attribute atau JSON translation payload minimal dari Blade.
4. Jangan menulis teks user-facing secara hardcoded di controller/service kecuali teks tersebut memang key teknis internal. Controller/service harus mengirim key atau hasil `__()`.
5. Setiap language key baru wajib ditambahkan ke semua locale aktif: `en`, `zh`, dan `zh-CN`.
6. Gunakan file domain language jika teks banyak atau spesifik modul.
7. Gunakan `messages.php` hanya untuk kata/frasa global yang benar-benar dipakai lintas domain.
8. Jangan mencampur English, Indonesian, Traditional Chinese, dan Simplified Chinese dalam satu UI kecuali konten database memang user-generated.
9. Copy backend tetap harus multi-language walaupun halaman hanya dipakai internal.
10. Tanggal, angka, currency, dan status harus memakai format/helper yang konsisten dengan domain halaman.

## Struktur Language File

Gunakan domain file untuk modul dengan banyak copy.

Contoh:

```text
resources/lang/en/admin-dashboard.php
resources/lang/zh/admin-dashboard.php
resources/lang/zh-CN/admin-dashboard.php

resources/lang/en/reviews.php
resources/lang/zh/reviews.php
resources/lang/zh-CN/reviews.php
```

Gunakan `resources/lang/{locale}/messages.php` untuk copy global seperti:

- `Dashboard`
- `Save`
- `Cancel`
- `Active`
- `Inactive`
- `Pending`
- `Print`

## Standar Penamaan Key

Gunakan key yang deskriptif dan stabil.

Contoh:

```php
return [
    'title' => 'Admin Dashboard',
    'subtitle' => 'Operational overview for internal teams.',
    'filters' => [
        'period' => 'Period',
        'today' => 'Today',
        'this_week' => 'This Week',
    ],
    'stats' => [
        'total_services' => 'Total Services',
        'active_services' => 'Active Services',
    ],
];
```

Hindari key yang terlalu umum di file domain:

```php
'text1' => '...',
'label' => '...',
'title2' => '...',
```

## Blade Standard

Gunakan:

```blade
<h1>@lang('admin-dashboard.title')</h1>
<p>{{ __('admin-dashboard.subtitle') }}</p>
```

Untuk attribute:

```blade
<button aria-label="{{ __('messages.Save') }}">
    @lang('messages.Save')
</button>
```

Untuk JavaScript:

```blade
<section
    data-empty-title="{{ __('admin-dashboard.empty.title') }}"
    data-empty-message="{{ __('admin-dashboard.empty.message') }}"
>
</section>
```

## Controller Dan Service Standard

Data shaping boleh dilakukan di controller/service, tetapi label user-facing tetap harus berasal dari translation.

```php
return [
    'label' => __('admin-dashboard.stats.total_services'),
    'value' => $totalServices,
];
```

Jika data akan dicache lintas locale, simpan key translation, bukan hasil string terjemahan.

## JavaScript Standard

JavaScript page-level harus membaca teks dari DOM dataset atau payload JSON yang sudah diterjemahkan.

```js
const emptyTitle = root.dataset.emptyTitle;
```

Jangan:

```js
alert('Data saved successfully');
```

Gunakan translation dari Blade:

```blade
<div data-success-message="{{ __('messages.Data saved successfully') }}"></div>
```

## Backend Multi-Language

Halaman backend wajib tetap mengikuti multi-language standard:

1. Sidebar, top navigation, breadcrumb, hero, card title, table header, empty state, modal, form label, helper text, dan action button wajib diterjemahkan.
2. Status operasional boleh berasal dari database, tetapi label tambahan seperti `Need review`, `Selected period`, atau `Payment follow-up` harus memakai language key.
3. Dashboard backend baru wajib membuat file domain language jika memiliki banyak KPI, section, dan empty state.

## Checklist Review

Sebelum perubahan dianggap selesai:

1. Apakah semua copy user-facing memakai language key?
2. Apakah key baru ada di `en`, `zh`, dan `zh-CN`?
3. Apakah JavaScript tidak memiliki hardcoded user-facing text?
4. Apakah controller/service tidak menyebarkan copy hardcoded?
5. Apakah Blade attribute seperti `title`, `aria-label`, dan placeholder ikut diterjemahkan?
6. Apakah empty state, validation message, dan processing label sudah diterjemahkan?
7. Apakah dokumentasi roadmap atau standar terkait diperbarui jika ada pattern baru?

## Status

Dokumen ini adalah aturan wajib untuk seluruh perubahan frontend, backend, dan shared UI pada project `balikamitour`.
