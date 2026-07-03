## Frontend PR Checklist

### Summary

- Jelaskan secara singkat perubahan frontend yang dilakukan.

### UI/UX Scope

- Halaman atau komponen apa yang diubah:
- Flow apa yang berubah:
- Apakah perubahan ini mengikuti baseline `hotelavailability`:

### Required Checks

- [ ] Perubahan sudah mengikuti `docs/frontend-ui-standards.md`
- [ ] Perubahan sudah mengikuti `docs/blade-asset-rules.md`
- [ ] Jika ada pattern reusable baru, standard terkait sudah diperbarui
- [ ] `docs/frontend-roadmap.md` sudah diperbarui
- [ ] Jika perlu, entry baru juga disiapkan memakai `docs/frontend-roadmap-entry-template.md`
- [ ] CTA, breadcrumb, hierarchy, dan page shell sudah konsisten dengan baseline frontend
- [ ] Tidak ada inline CSS atau inline JavaScript baru di Blade
- [ ] Data shaping kompleks tidak diletakkan di Blade
- [ ] Flow login, redirect, refresh, dan language switch tetap aman
- [ ] Perubahan sudah dicek pada desktop dan mobile

### Roadmap Entry

- Tanggal entry roadmap:
- Judul entry roadmap:

### Files Changed

- `resources/views/...`
- `public/css/...`
- `public/frontend/js/...`
- `app/Http/Controllers/...`

### Notes

- Catatan tambahan, tradeoff, atau follow-up jika ada.
