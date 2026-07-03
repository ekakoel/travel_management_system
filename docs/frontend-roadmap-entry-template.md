# Frontend Roadmap Entry Template

Gunakan template ini setiap kali ada perubahan frontend dan salin hasilnya ke:

- `docs/frontend-roadmap.md`

## Template Umum

```text
## YYYY-MM-DD - Judul Perubahan
- Status: planned | in progress | done | blocked
- Area: halaman / komponen / asset / flow
- Summary: jelaskan apa yang diubah dalam 1-3 kalimat
- Impact: jelaskan pengaruhnya terhadap UI, UX, consistency, reusability, atau performance
- Files:
  - `path/file-1`
  - `path/file-2`
- Follow-up: tulis langkah berikutnya, atau `none` jika tidak ada
```

## Template Frontend UI/UX

```text
## YYYY-MM-DD - Nama Halaman / Fitur
- Status: planned | in progress | done | blocked
- Area: frontend UI/UX
- Summary: perubahan visual, struktur halaman, flow, atau interaction yang dilakukan
- Impact: dampak pada baseline `hotelavailability`, consistency, mobile usability, CTA clarity, atau readability
- Files:
  - `resources/views/...`
  - `public/css/...`
  - `public/frontend/js/...`
  - `app/Http/Controllers/...`
- Follow-up: alignment page lain / audit translation / cleanup reusable component / none
```

## Aturan Wajib

1. Jangan merge perubahan frontend tanpa menambahkan entry ke `docs/frontend-roadmap.md`.
2. Jika perubahan mengubah standar, update juga `docs/frontend-ui-standards.md`.
3. Jika perubahan menyentuh Blade atau asset separation, pastikan tetap sesuai `docs/blade-asset-rules.md`.
