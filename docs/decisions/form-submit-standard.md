# Form Submit Standard

Status: active
Updated: 2026-07-27

Standar ini berlaku untuk form create/update penting seperti order, booking, reservation, payment confirmation, profile update, dan admin operation yang mengubah state.

## Standar Wajib

- Gunakan pola `POST -> Redirect -> GET`.
- Form create penting memakai token submit sekali pakai.
- Backend harus idempotent: token yang sama tidak boleh membuat record ganda.
- Sukses submit redirect ke halaman final/canonical yang refresh-safe dan shareable.
- Submit harus menampilkan spinner atau overlay processing yang jelas.
- Tombol/action terkait harus locked selama submit.
- Error validasi ditampilkan dekat field jika memungkinkan.
- Browser Back setelah submit harus aman dari resubmit buta.

## Building Blocks Aktif

- Hidden token partial: `resources/views/partials/form-submission-token.blade.php`.
- Backend helper trait: `app/Http/Controllers/Concerns/InteractsWithFormSubmissions.php`.
- Frontend guard: `resources/frontend/js/components/form-submission-guard.js`.
- Backend shared guard: `resources/backend/js/app.js`; form mutasi otomatis
  mendapat spinner sejak klik, sedangkan action non-form memakai
  `data-backend-action-loading` dan `window.setBackendActionLoading`.
- Spinner action backend canonical adalah `backend-action-spinner`: diposisikan
  sebagai overlay tepat di tengah control, mempertahankan dimensi tombol, dan
  menyembunyikan ikon/label lama selama state `is-submitting` agar tidak overlap.
- Spinner action frontend canonical adalah `frontend-action-spinner` dengan
  state `is-processing`. Spinner fullscreen/content tetap memakai komponen
  loading sesuai konteks dan tidak boleh ditempatkan berdampingan dengan ikon
  action yang masih terlihat.
- Loading content backend memakai `<x-backend.loading-spinner>`; halaman tidak
  boleh membuat spinner Bootstrap/Font Awesome atau mengganti `innerHTML`
  tombol secara lokal jika shared submit guard sudah aktif.

## Checklist Implementasi

1. Ada `@csrf`.
2. Ada `submission_token` untuk create penting.
3. Controller memvalidasi token.
4. Duplicate token diarahkan ke record yang sudah dibuat.
5. Sukses redirect ke route detail/final.
6. Submit button punya spinner/label processing.
7. Overlay fullscreen dipakai untuk flow modal/order penting.
8. State gagal mengembalikan kontrol aktif.
9. Copy processing memakai language key.
10. Perubahan frontend dicatat di `docs/decisions/frontend-roadmap.md`.
