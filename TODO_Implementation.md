# Implementasi Sistem Perpustakaan Self-Service

## Fase 1: Database Migrations
- [ ] 1.1 Modify tabel peminjaman (tambah konfirmasi fields)
- [ ] 1.2 Modify tabel pengembalian (tambah konfirmasi fields)
- [ ] 1.3 Run migrations

## Fase 2: Model Updates
- [ ] 2.1 Update Peminjaman.php model
- [ ] 2.2 Update Pengembalian.php model
- [ ] 2.3 Update User.php model (relasi)

## Fase 3: Roles & Permissions
- [ ] 3.1 Setup roles: 'admin', 'staff', 'user'
- [ ] 3.2 Setup permissions untuk setiap role
- [ ] 3.3 Create seeder untuk dummy accounts

## Fase 4: Staff Panel Resources
- [ ] 4.1 Update PeminjamanResource.php (Approve/Reject actions)
- [ ] 4.2 Update PengembalianResource.php (Confirm actions)
- [ ] 4.3 Tambah halaman konfirmasi di Staff Panel

## Fase 5: User Panel Resources
- [ ] 5.1 Create UserPeminjamanResource.php
- [ ] 5.2 Create UserPengembalianResource.php
- [ ] 5.3 Create UserDashboard page
- [ ] 5.4 Create MyMemberCard page

## Fase 6: Registration & Profile
- [ ] 6.1 Setup Filament registration untuk user
- [ ] 6.2 Create member card generation
- [ ] 6.3 Update AdminPanelProvider

## Fase 7: Seeder - Dummy Accounts
- [ ] 7.1 Staff account: staff@booknest.com / password123
- [ ] 7.2 User account: user@booknest.com / password123

## Fase 8: Testing
- [ ] 8.1 Test workflow lengkap
- [ ] 8.2 Verify permissions

