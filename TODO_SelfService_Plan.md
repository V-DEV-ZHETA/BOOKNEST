# Transformasi Sistem Perpustakaan - Rencana Implementasi

## Gambaran Umum
Mengubah sistem dari "staff-controlled" menjadi "self-service" di mana:
- **User Panel**: Peminjaman dan pengembalian buku langsung oleh anggota
- **Staff Panel**: Menerima informasi dan mengkonfirmasi transaksi

---

## Struktur Saat Ini

### File yang Relevan:
- `app/Models/Peminjaman.php` - Model peminjaman
- `app/Models/Pengembalian.php` - Model pengembalian
- `app/Models/InventoriBuku.php` - Model buku
- `app/Models/User.php` - Model pengguna
- `database/migrations/2026_01_23_020157_create_peminjamen_table.php` - Migration peminjaman
- `database/migrations/2026_01_23_020207_create_pengembalians_table.php` - Migration pengembalian

---

## Perubahan yang Dibutuhkan

### 1. Database Migrations

#### A. Modifikasi Tabel `peminjamen`:
```php
// Tambahkan kolom:
- confirmation_status: enum['pending', 'approved', 'rejected'] -> default 'pending'
- confirmed_by: foreignId ke users -> nullable
- confirmed_at: timestamp -> nullable
- confirmed_notes: text -> nullable
```

#### B. Modifikasi Tabel `pengembalians`:
```php
// Tambahkan kolom:
- confirmation_status: enum['pending', 'approved', 'rejected'] -> default 'pending'
- confirmed_by: foreignId ke users -> nullable
- confirmed_at: timestamp -> nullable
- confirmed_notes: text -> nullable
```

### 2. Models Updates

#### `Peminjaman.php`:
```php
protected $fillable = [
    'user_id', 
    'inventori_buku_id', 
    'borrow_date', 
    'due_date', 
    'return_date', 
    'status', 
    'notes',
    'confirmation_status',    // NEW
    'confirmed_by',           // NEW
    'confirmed_at',           // NEW
    'confirmed_notes'         // NEW
];

// Tambahkan scope untuk filter status
// Tambahkan accessor untuk status human-readable
```

#### `Pengembalian.php`:
```php
protected $fillable = [
    'peminjaman_id',
    'user_id',                // NEW - siapa yang mengembalikan
    'inventori_buku_id',      // NEW - buku yang dikembalikan
    'return_date', 
    'condition', 
    'notes', 
    'fine_amount',
    'late_days',
    'confirmation_status',    // NEW
    'confirmed_by',           // NEW
    'confirmed_at',           // NEW
    'confirmed_notes'         // NEW
];
```

### 3. User Panel (Filament Resources)

#### A. `UserPeminjamanResource` (Baru):
- Halaman untuk user melihat buku yang tersedia
- Form untuk membuat permintaan peminjaman
- Tabel riwayat peminjaman user
- Status: pending/approved/rejected

#### B. `UserPengembalianResource` (Baru):
- Halaman untuk user mengembalikan buku
- Form untuk mengajukan pengembalian
- Tabel riwayat pengembalian user

### 4. Staff Panel Updates

#### Modifikasi `PeminjamanResource`:
- Tambah kolom konfirmasi (confirmation_status)
- Tambah action Approve/Reject
- Filter berdasarkan confirmation_status

#### Modifikasi `PengembalianResource`:
- Tambah kolom konfirmasi
- Tambah action Confirm
- Filter berdasarkan confirmation_status

### 5. Workflow Baru

```
ALUR PEMINJAMAN:
1. User memilih buku → Buat permintaan peminjaman (status: pending)
2. Staff menerima notifikasi → Review permintaan
3. Staff APPROVE → Buku dikurangi dari stok (quantity - 1)
   ATAU Staff REJECT → Permintaan ditolak dengan catatan

ALUR PENGEMBALIAN:
1. User mengklik "Kembalikan" pada peminjaman yang aktif
2. User mengisi form pengembalian (kondisi buku, dll) → status: pending
3. Staff menerima notifikasi → Memeriksa buku
4. Staff CONFIRM → Buku dikembalikan ke stok (quantity + 1)
```

---

## Langkah Implementasi

### Phase 1: Database Changes
1. Create migration untuk modify tabel peminjaman
2. Create migration untuk modify tabel pengembalian
3. Run migrations

### Phase 2: Model Updates
1. Update `Peminjaman.php` model
2. Update `Pengembalian.php` model
3. Update `User.php` model (relasi)

### Phase 3: User Panel Resources
1. Create `UserPeminjamanResource.php`
2. Create pages untuk user peminjaman
3. Create `UserPengembalianResource.php`
4. Create pages untuk user pengembalian
5. Update `AdminPanelProvider.php` untuk register user resources

### Phase 4: Staff Panel Updates
1. Update `PeminjamanResource.php` dengan fitur konfirmasi
2. Update `PengembalianResource.php` dengan fitur konfirmasi
3. Tambah action Approve/Reject

### Phase 5: Testing & Refinement
1. Test workflow lengkap
2. Fix bugs jika ada
3. User acceptance testing

---

## Estimasi Kompleksitas
- **Database Changes**: Easy
- **Model Updates**: Easy
- **User Panel**: Medium - Perlu buat resource baru
- **Staff Panel Updates**: Medium - Perlu modify existing resources
- **Total**: Medium complexity

---

## Pertimbangan Tambahan
1. Validasi stok buku saat approve peminjaman
2. Notifikasi ke staff saat ada permintaan baru
3. Perhitungan denda otomatis
4. History lengkap untuk audit

