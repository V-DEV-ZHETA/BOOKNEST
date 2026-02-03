# TODO - Hapus Laporan dan Pengaturan Resources

## Files to Delete

### Laporan Resource Files:
- [x] app/Filament/Resources/LaporanResource.php
- [x] app/Filament/Resources/LaporanResource/Pages/ListLaporans.php
- [x] app/Filament/Resources/LaporanResource/Pages/CreateLaporan.php
- [x] app/Filament/Resources/LaporanResource/Pages/EditLaporan.php
- [x] app/Models/Laporan.php
- [x] database/migrations/2026_01_23_020218_create_laporans_table.php
- [x] app/Policies/LaporanPolicy.php

### Pengaturan Resource Files:
- [x] app/Filament/Resources/PengaturanResource.php
- [x] app/Filament/Resources/PengaturanResource/Pages/ListPengaturans.php
- [x] app/Filament/Resources/PengaturanResource/Pages/CreatePengaturan.php
- [x] app/Filament/Resources/PengaturanResource/Pages/EditPengaturan.php
- [x] app/Models/Pengaturan.php
- [x] database/migrations/2026_01_23_020213_create_pengaturans_table.php
- [x] app/Policies/PengaturanPolicy.php

## Post-Deletion Steps:
- [ ] Run `php artisan migrate:fresh` or create rollback migrations (to drop tables)
- [ ] Clear config cache: `php artisan config:clear`
- [ ] Clear route cache: `php artisan route:clear`

## Status: COMPLETED ✓
Semua resource Laporan dan Pengaturan telah dihapus dari sistem.

