# Project Structure Migration Todo

Status: active
Updated: 2026-07-27

Tracker ringkas untuk migrasi struktur folder dari legacy menuju standar di `docs/architecture.md`.

## Status Saat Ini

- Public frontend utama sudah banyak berada di `resources/views/frontend/landing-page`.
- Authenticated frontend order/profile sudah banyak berada di `resources/views/frontend/home`.
- Backend operations modern sudah tersedia untuk hotels, tours, activities, transports, orders-admin, reservations, drivers, guides, dan transport management.
- Masih ada route/view legacy aktif di `resources/views/admin`, `resources/views/layouts`, `resources/views/main`, dan controller root namespace.
- Jangan migrasi semua domain sekaligus. Kerjakan satu domain, verifikasi, lalu update tracker.

## Domain Aktif Yang Sudah Dimigrasi

- Transport public views/assets.
- Activities public views/assets.
- Tours public views/assets.
- Accommodations public views/assets.
- Static public pages.
- Profile.
- Orders dashboard/detail/history/edit.
- Hotel availability/check price.
- Booking order forms.
- Review views.
- Wedding order package sections.
- Backend hotels/tours/activities/transports baseline.

## Domain Legacy Yang Perlu Audit Lanjutan

- `resources/views/main` untuk wedding, hotel/activity/transport legacy, chat, calendar, dan halaman lama lain.
- `resources/views/admin` yang belum dipindah ke `resources/views/backend`.
- Controller root namespace yang sudah punya target `App\Http\Controllers\Backend\...` atau `Frontend\...` tapi belum seluruhnya dipindahkan.
- Route name legacy `view.*`, `func.*`, dan URL transisional yang masih dipakai Blade/controller.

## Transport Domain Inventory

Status: migrated for public frontend and backend operations baseline.

Aktif:

- `resources/views/frontend/landing-page/transports`
- `resources/frontend/js/landing-page/transports`
- `resources/frontend/scss/landing-page/transports`
- `resources/views/backend/operations/transports`
- `resources/backend/js/operations/transports`
- `resources/backend/scss/operations/transports`

## Next Execution Plan

1. Pilih satu domain legacy.
2. Cari route, controller return view, include, asset, language key, dan test terkait.
3. Pindahkan view/asset ke folder target.
4. Update `webpack.mix.js`, route helper, include/extends, dan docs.
5. Jalankan verifikasi aman: syntax, route/list, view/cache atau test terarah jika DB testing aman.

## Guard Rule Untuk File Baru

- Public tanpa login -> `frontend/landing-page`.
- User login/customer -> `frontend/home`.
- Staff/admin/internal -> `backend`.
- Shared hanya jika minimal dua consumer nyata.
- File baru tidak boleh masuk `resources/views/home`, `resources/views/form`, `resources/views/order`, atau legacy page folder tanpa alasan migrasi.
