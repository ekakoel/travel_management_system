# Form Submit Standard

Dokumen ini menetapkan standar wajib untuk seluruh flow submit form di project `balikamitour`, terutama form booking, reservation, checkout-like flow, dan form lain yang menghasilkan record baru atau perubahan state penting.

## Tujuan

- Mencegah double submit saat user klik berulang atau menekan tombol `Back`.
- Menjamin submit selalu berakhir pada URL final yang valid, refresh-safe, dan shareable.
- Menyamakan surface feedback loading, success, dan error di seluruh form.
- Mengurangi implementasi ad hoc yang berbeda antar domain hotel, tour, transport, villa, atau modul lain.

## Standar Wajib

1. Setiap form create/update penting harus memakai pola `POST -> Redirect -> GET`.
2. Form yang membuat data baru wajib memakai token submit unik sekali pakai.
3. Backend wajib menganggap token yang sama sebagai submit yang sama, bukan request baru.
4. Setelah submit sukses, user harus diarahkan ke halaman detail/final canonical, bukan tetap berada di URL POST atau halaman transisional.
5. Saat submit berjalan, tombol submit harus terkunci dan spinner/overlay harus muncul.
6. Jika user kembali dengan tombol browser `Back` setelah submit sukses, halaman form harus memulihkan state aman:
   - default standar: reload halaman sekali agar browser tidak memulihkan state lama dari cache
   - jika flow membutuhkan perilaku khusus, alasan penyimpangan harus jelas
7. Error backend harus sebisa mungkin ditampilkan dekat field terkait, bukan hanya alert global.
8. Submit standard tidak boleh bergantung hanya pada proteksi frontend; backend tetap harus idempotent.
9. Spinner/loading indicator adalah kewajiban mutlak, bukan opsional, untuk semua flow create, edit, update, confirm, reserve, checkout, resend, atau save yang membutuhkan waktu proses.
10. Men-disable tombol tanpa spinner atau processing label dianggap implementasi belum memenuhi standar project.

## Reusable Building Blocks

### 1. Hidden submission token

Gunakan partial:

- `resources/views/partials/form-submission-token.blade.php`

Contoh:

```blade
<form method="POST" action="...">
    @csrf
    @include('partials.form-submission-token')
</form>
```

Default field yang dikirim adalah `submission_token`.

### 2. Backend idempotency helper

Gunakan trait:

- `app/Http/Controllers/Concerns/InteractsWithFormSubmissions.php`

Helper ini menyimpan token submit yang sudah berhasil diproses ke session scope tertentu, lalu memungkinkan controller:

- mendeteksi submit ulang dengan token yang sama
- mengembalikan record/redirect yang sudah ada
- membatasi jumlah histori token yang disimpan

Contoh pola:

```php
$validated = $request->validate([
    'submission_token' => 'required|string|max:120',
]);

$existingId = $this->findProcessedFormSubmission('scope-name', $validated['submission_token']);

if ($existingId) {
    return redirect()->route('final.route', ['id' => $existingId]);
}

// create data baru

$this->rememberProcessedFormSubmission('scope-name', $validated['submission_token'], $createdModel->id);

return redirect()->route('final.route', ['id' => $createdModel->id]);
```

### 3. Frontend history restore guard

Gunakan shared utility:

- `resources/frontend/js/components/form-submission-guard.js`

Utility ini menangani:

- sessionStorage flag bahwa form sudah sempat disubmit
- deteksi `pageshow` dari history restore / back-forward cache
- reload halaman sekali agar form lama tidak dipakai ulang

Contoh pola:

```js
const { createFormSubmissionGuard } = require('../components/form-submission-guard');

const submissionGuard = createFormSubmissionGuard(form, {
    storageKey: form.dataset.submissionKey,
});

submissionGuard.markSubmitted();

submissionGuard.bindHistoryRestore(() => {
    window.location.reload();
});
```

## UX Standard Saat Submit

1. Tombol submit berubah ke state processing.
2. Spinner/overlay tampil sebelum `form.submit()` dijalankan.
3. Semua tombol next/prev atau action lain yang relevan ikut terkunci.
4. Scroll page dikunci hanya jika memang ada overlay penuh.
5. Jika submit gagal dan user kembali ke form dengan validation error:
   - overlay harus hilang
   - tombol kembali aktif
   - wizard harus membuka step yang punya error pertama
6. Untuk flow modal atau wizard, gunakan salah satu pola berikut:
   - spinner di dalam tombol submit + label processing
   - overlay viewport penuh jika flow memblokir interaksi submit utama
7. Untuk create order dan edit order, spinner harus dianggap requirement default sejak awal implementasi, bukan enhancement tambahan setelah flow selesai.
8. Jika ada lebih dari satu tombol action di area yang sama, semua action yang bisa memicu submit ganda harus ikut dikunci selama request berjalan.
9. Baseline visual spinner modal order yang wajib dijadikan referensi adalah spinner Activity Detail melalui komponen `frontend-order-modal`.
10. Jika sebuah flow modal order baru membutuhkan spinner submit, gunakan pola `frontend-order-modal__overlay`:
   - overlay card di tengah viewport
   - overlay harus fullscreen dan menutupi seluruh screen
   - spinner lingkaran dengan style yang sama
   - tombol submit berubah ke state processing dengan spinner inline
   - seluruh action submit terkait terkunci selama request berjalan

## Error Surface Standard

1. Validation error field-level harus tampil dengan `invalid-feedback` atau komponen sejenis dekat input.
2. Error kolektif seperti guest list, room matrix, atau repeater harus punya error surface lokal di area tersebut.
3. Alert global hanya dipakai untuk:
   - error sistem umum
   - error yang tidak bisa dipetakan ke field tunggal
   - success/info banner setelah redirect

## Redirect Standard

1. Target sukses harus halaman detail final yang bisa di-refresh dengan aman.
2. URL sukses harus bisa dibuka langsung tanpa mengandalkan `previous URL`.
3. Redirect tidak boleh kembali ke halaman create jika create sudah berhasil.

## Checklist Implementasi Form Baru

Sebelum merge form submit baru, pastikan:

1. Sudah ada `@csrf`.
2. Sudah ada `@include('partials.form-submission-token')`.
3. Backend memvalidasi `submission_token`.
4. Backend menyimpan token yang sukses diproses dengan scope yang jelas.
5. Duplicate submit dengan token sama tidak membuat record kedua.
6. Submit sukses redirect ke halaman final canonical.
7. Frontend menunjukkan spinner/overlay dan mengunci tombol saat submit.
8. Frontend menangani history restore/back button secara aman.
9. Error backend tampil dekat field terkait jika memungkinkan.
10. Perubahan dicatat di `docs/frontend-roadmap.md` bila menyentuh flow frontend.
11. Label processing yang tampil ke user sudah sesuai konteks, misalnya `Processing order...`, `Saving changes...`, atau `Updating profile...`, bukan selalu label generik jika konteks bisa dibuat lebih jelas.
12. Overlay spinner untuk submit penting benar-benar fullscreen dan spinner/card tampil tepat di tengah layar.

## Referensi Implementasi Saat Ini

- Shared modal order dan spinner baseline:
  - `docs/frontend-order-modal-standard.md`
  - `resources/views/frontend/activities/detail.blade.php`
  - `resources/frontend/scss/components/frontend-order-modal.scss`
  - `resources/frontend/js/pages/activity-detail.js`
- Tour package create order:
  - `resources/views/frontend/tours/detail-modern.blade.php`
  - `resources/frontend/js/pages/tour-detail.js`
  - `app/Http/Controllers/OrderController.php`

## Follow-up yang Direkomendasikan

1. Samakan flow order hotel, transport, dan villa agar memakai utility/trait yang sama.
2. Audit form admin atau operational yang membuat record penting agar mendapatkan proteksi submit setara.
3. Jika pola ini dipakai luas, pertimbangkan ekstraksi shared Blade component untuk tombol submit processing state.
