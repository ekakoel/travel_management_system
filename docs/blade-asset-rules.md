# Blade Asset Separation Rules

Dokumen ini adalah aturan wajib untuk pengembangan view pada project `balikamitour`.

## Tujuan

- Menjaga file Blade tetap fokus pada struktur markup dan data binding.
- Mengurangi inline CSS dan inline JavaScript yang membuat view sulit dirawat.
- Menghindari duplikasi style dan script antar halaman.
- Membuat asset lebih mudah di-cache oleh browser dan lebih stabil saat halaman bertambah besar.

## Aturan Wajib

1. Jangan menulis `<style>...</style>` langsung di file Blade.
2. Jangan menulis JavaScript inline langsung di Blade, termasuk `<script>...</script>`, event seperti `onclick`, `onchange`, `onload`, dan sejenisnya.
3. Blade hanya boleh berisi:
   - HTML markup
   - directive Blade
   - komponen/partial include
   - data attribute yang dibutuhkan JavaScript
4. CSS halaman atau komponen harus dipindahkan ke file terpisah di bawah `public/css/...` atau file asset terkompilasi yang dikelola build pipeline.
5. JavaScript halaman atau komponen harus dipindahkan ke file terpisah di bawah `public/js/...`, `public/frontend/js/...`, atau asset bundle yang relevan.
6. Asset yang hanya dipakai oleh halaman tertentu harus di-load per halaman menggunakan `@push('styles')` dan `@push('scripts')`, bukan dimuat global tanpa alasan.
7. Asset yang dipakai lintas banyak halaman boleh dimasukkan ke file global, tetapi hanya jika benar-benar shared.
8. Logic presentasi kompleks tidak boleh diletakkan di Blade.
   - Hindari perhitungan besar, filter collection berulang, query, atau transformasi data berat di view.
   - Pindahkan ke controller, view model, helper, atau service.
9. Query database tidak boleh dijalankan dari Blade.
10. Inline style seperti `style="..."` tidak boleh dipakai, kecuali benar-benar diperlukan untuk nilai dinamis yang tidak realistis dipindahkan ke class CSS.
11. Jika ada kebutuhan nilai dinamis untuk JavaScript, kirim melalui `data-*` attribute atau JSON terstruktur yang minimal, bukan script panjang di Blade.
12. Setiap view baru harus mempertimbangkan cacheability asset:
   - CSS/JS reusable harus berada di file terpisah
   - hindari membuat script unik kecil-kecil jika bisa digabung secara masuk akal per domain halaman

## Pola Implementasi

### CSS per halaman

Gunakan:

```blade
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/example-page.css') }}">
@endpush
```

### JS per halaman

Gunakan:

```blade
@push('scripts')
    <script src="{{ asset('frontend/js/pages/example-page.js') }}"></script>
@endpush
```

### Komponen Blade reusable

Jika partial atau komponen butuh asset sendiri:

- partial tetap hanya output markup
- partial boleh melakukan `@push('styles')` atau `@push('scripts')` dengan `@once`
- asset fisiknya tetap berada di file CSS/JS terpisah

## Struktur yang Direkomendasikan

Gunakan penempatan asset seperti berikut:

```text
public/
  css/
    components/
    pages/
  js/
    pages/
  frontend/
    js/
      pages/
```

Jika nanti tim ingin merapikan lebih jauh, asset baru bisa dipindahkan bertahap ke `resources/` lalu dikompilasi melalui Laravel Mix.

## Aturan Review

Setiap perubahan view harus ditolak atau direvisi bila:

- menambahkan tag `<style>` di Blade
- menambahkan tag `<script>` inline tanpa alasan yang sangat kuat
- menambahkan event handler inline
- menaruh logic yang seharusnya ada di controller/service ke dalam Blade
- membuat partial baru yang hanya nyaman dipakai sekarang tetapi sulit di-reuse

## Pengecualian Terbatas

Pengecualian hanya boleh dilakukan jika semua kondisi ini terpenuhi:

1. benar-benar ada kebutuhan dinamis yang tidak masuk akal dipindahkan ke file asset
2. implementasi alternatif lebih rumit dan lebih berisiko
3. pengecualian diberi komentar singkat yang menjelaskan alasannya

Jika masih bisa dipindahkan ke file asset, maka wajib dipindahkan.

## Status

Mulai sekarang dokumen ini adalah aturan default untuk seluruh perubahan Blade di repository ini.

## Kewajiban Dokumentasi Perubahan

Setiap perubahan frontend yang menyentuh Blade, CSS, JavaScript, layout, komponen, flow, copy penting, atau interaction behavior wajib dicatat di:

- `docs/frontend-roadmap.md`

Catatan minimal harus memuat:

1. tanggal perubahan
2. area atau halaman yang diubah
3. ringkasan perubahan
4. dampak terhadap UI/UX, reusable component, atau flow
5. status pekerjaan

Perubahan frontend dianggap belum lengkap jika implementasi selesai tetapi roadmap belum diperbarui.
