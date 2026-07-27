# Backend Legacy UI Audit

Status: active
Updated: 2026-07-27

Audit ringkas area backend yang masih perlu perhatian.

## Legacy Pattern Yang Harus Dihindari

- `card-box`
- `btn-view`, `btn-edit`, `btn-delete`
- `status-active`, `status-draft`
- `.data-table.table` sebagai style utama
- inline `<style>`, inline `<script>`, `onclick`, `onkeyup`
- page-specific form/button/status/modal primitive

## Domain Bersih Atau Sudah Punya Baseline

- Admin Dashboard.
- Hotels operations.
- Activities operations.
- Tours operations.
- Transports operations.
- Drivers/Guides.
- Admin content pages utama.

## Kandidat Cleanup Berikutnya

1. Wedding operations workspace.
2. Reservation detail/list lanjutan.
3. Orders admin legacy helpers.
4. SPK/transport management polish setelah public report token stabil.

## Cara Audit

Gunakan `rg` pada view dan SCSS domain:

```powershell
rg -n "card-box|btn-view|btn-edit|btn-delete|status-active|status-draft|<style|<script|onclick|onkeyup" resources\views resources\backend
```
