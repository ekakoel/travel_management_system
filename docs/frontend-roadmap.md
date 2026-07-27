# Frontend Roadmap

Status: active
Updated: 2026-07-27

Roadmap ini menyimpan status frontend aktif secara ringkas. Entry historis panjang sudah dipadatkan agar agent bisa cepat memahami kondisi project.

## Current Baseline

- Standar UI frontend: `docs/frontend-ui-standards.md`.
- Standar Blade/asset: `docs/blade-asset-rules.md`.
- Standar submit: `docs/form-submit-standard.md`.
- Standar modal order: `docs/frontend-order-modal-standard.md`.
- Standar multi-language: `docs/multi-language-standard.md`.

## Status Aktif

- Public landing pages utama sudah memakai namespace `resources/views/frontend/landing-page`.
- Authenticated customer/order area sudah memakai namespace `resources/views/frontend/home` untuk banyak flow utama.
- Frontend public detail service memakai shared shell/card/modal pattern secara bertahap.
- Modal order Activity Detail menjadi baseline order modal.
- Hotel availability/check price menjadi baseline shell frontend.
- Shared Swiper tersedia melalui `frontend-loop-swiper`.
- Language coverage aktif untuk `en`, `zh`, dan `zh-CN`, tetapi legacy view masih perlu audit.

## Area Yang Masih Perlu Perhatian

- Beberapa view legacy masih berada di `resources/views/main` dan perlu audit domain sebelum migrasi.
- Beberapa JS/CSS legacy di `public/css/pages` dan `public/frontend/js/pages` masih mungkin aktif.
- Flow order hotel, transport, villa, dan wedding perlu terus disamakan dengan idempotent submit standard.
- Copy frontend legacy perlu audit hardcoded text.
- Setiap perubahan frontend baru harus menambah entry ringkas di bawah.

## Recent Entries

## 2026-07-27 - Documentation Compaction

- Status: done
- Files:
  - `docs/frontend-roadmap.md`
  - `docs/frontend-ui-standards.md`
  - `docs/blade-asset-rules.md`
  - `docs/form-submit-standard.md`
- Summary: Roadmap frontend dipadatkan dari log panjang menjadi status aktif, baseline, dan area follow-up.
- Impact: Agent dapat memahami arah frontend tanpa membaca changelog historis yang terlalu panjang.
- Verification: Markdown/link audit dan status git.

## Template

Gunakan `docs/frontend-roadmap-entry-template.md` untuk entry berikutnya.
