# Backend Legacy UI Deep Cleanup Roadmap

Roadmap ini menjadi checklist lanjutan setelah backend form/button standardization. Fokusnya bukan lagi sekadar class form, tetapi membersihkan struktur UI legacy yang masih berat: `card-box`, inline script, inline style, table Bootstrap/DataTables legacy, dan behavior JavaScript yang masih ditanam langsung di Blade.

## Standard Final

- Halaman backend memakai shared UI: `x-backend.page-hero`, `backend-page-toolbar`, `backend-panel`, `backend-section-header`, `backend-detail-layout`, `backend-table`, `backend-status-badge`, `backend-form-*`, dan `backend-button-*`.
- Behavior halaman tidak berada di inline `<script>` Blade. Semua behavior dipindahkan ke `resources/backend/js/{domain}` sesuai asset architecture.
- Style halaman/domain tidak memakai inline `style=""`; gunakan shared utility/component atau SCSS domain yang hanya mengatur layout spesifik.
- `card-box`, `card-box-title`, `card-box-footer`, `data-table table ...`, `onclick`, `onkeyup`, dan `onchange` legacy harus dipindahkan bertahap.
- Refactor dilakukan bertahap per halaman besar agar risiko regression operasional tetap rendah.

## Phase LUI-1 - Create Hotel Order Cleanup

- [x] Audit `resources/views/admin/create-hotel-order.blade.php`.
- [x] Pindahkan behavior add/remove room dari inline `<script>` ke `resources/backend/js/operations/orders-admin/create-hotel-order.js`.
- [x] Daftarkan asset create-hotel-order di `webpack.mix.js`.
- [x] Ganti inline `onchange` room guest dengan data attribute.
- [x] Ganti card utama ke `backend-panel`.
- [x] Kurangi inline style utama dengan shared utility class.
- [x] Jalankan acceptance Phase LUI-1.

## Phase LUI-2 - Reservation Detail Cleanup

- [x] Audit section dan modal terbesar di `resources/views/admin/reservation_detail.blade.php`.
- [x] Petakan section besar: Reservation, Flight, Agent, Guest, Guide, Driver, Accommodation, Activity/Tour, Transport, Restaurant, Include, Exclude, Remark, Sidebar Attention/Notes.
- [ ] Pecah section utama ke partial backend/admin yang lebih kecil.
- [x] Pindahkan confirm/delete behavior ke asset backend domain.
- [ ] Pindahkan search/toggle behavior ke asset backend domain ketika section terkait mulai dipisah.
- [ ] Ganti card-box ke shared panel secara bertahap.
- [ ] Ganti table legacy ke shared backend table.

## Phase LUI-3 - Wedding Order Detail Cleanup

- [ ] Audit `resources/views/admin/order/order-wedding-detail.blade.php`.
- [ ] Pecah payment, note, service request, itinerary, accommodation, dan invoice section ke partial.
- [ ] Pindahkan toggle card, preview image, confirm, dan submit behavior ke asset backend domain.
- [ ] Ganti card-box/table legacy ke shared UI.

## Phase LUI-4 - Update Wedding Services Cleanup

- [ ] Audit `resources/views/admin/update-wedding-services.blade.php`.
- [ ] Pecah service modal per kategori ke partial.
- [ ] Ganti `card-box` modal ke shared modal/panel structure.
- [ ] Pindahkan behavior add/update service ke JS domain.

## Phase LUI-5 - Final Legacy UI Acceptance

- [ ] `php artisan view:cache` berhasil.
- [ ] `npm run development` berhasil.
- [ ] Guard test legacy UI cleanup berhasil.
- [ ] Audit global backend target tidak menemukan `card-box`, inline `style`, inline event handler, atau inline `<script>` pada halaman yang sudah selesai difasekan.
