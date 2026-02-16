# RBAC Tidak Berjalan - Analisis & Solusi

## Masalah yang Ditemukan

### 1. **Bug di PermissionService.php (PALING KRITIS)** ❌
**File:** `app/Services/PermissionService.php` (Line 38-61)

**Masalah:**
Logic sebelumnya SALAH – ketika permission belum ada di database, sistem ALLOW akses (return true), padahal seharusnya DENY.

```php
// Logic Lama (SALAH):
if (!$hasPermission) {
    $permissionExists = \DB::table('permissions')->where('nama', $permission)->exists();
    return !$permissionExists;  // Return TRUE jika permission belum dibuat = MEMBOLEHKAN AKSES!
}
```

**Akibatnya:**
- User akses `transaksi.masuk.read`
- Permission belum ada di database (migration belum di-run)
- PermissionService check → tidak ada di package → cek database → tidak ada → **return TRUE = BOLEH AKSES** (SALAH!)
- Padahal user tidak punya izin di paket-layanan!

**Solusi:** ✅ SUDAH DIPERBAIKI
```php
// Logic Baru (BENAR):
if (!$hasPermission) {
    return false;  // DENY – permission tidak dikonfigurasi di paket
}
```

---

## Langkah-Langkah Memperbaiki (WAJIB DILAKUKAN)

### Step 1: Jalankan Migration ⚡
```bash
php artisan migrate
```

Ini akan menambahkan **8 permissions baru** ke database:
- `transaksi.masuk.read` – Melihat transaksi masuk
- `transaksi.masuk.create` – Membuat transaksi masuk
- `transaksi.masuk.update` – Mengedit transaksi masuk
- `transaksi.masuk.delete` – Menghapus transaksi masuk
- `transaksi.keluar.read` – Melihat transaksi keluar
- `transaksi.keluar.create` – Membuat transaksi keluar
- `transaksi.keluar.update` – Mengedit transaksi keluar
- `transaksi.keluar.delete` – Menghapus transaksi keluar

**File Migration:** `database/migrations/2026_02_14_add_transaksi_masuk_keluar_permissions.php`

---

### Step 2: Konfigurasi di Paket-Layanan 🎯

1. Buka halaman **Paket-Layanan** (Admin)
2. Edit paket yang ingin memberikan akses (misal: "TRIAL")
3. Centang permissions yang diinginkan:
   - ✅ `transaksi.masuk.read` → User bisa lihat daftar
   - ✅ `transaksi.masuk.create` → User bisa buat transaksi
   - ✅ `transaksi.masuk.update` → User bisa edit
   - ✅ `transaksi.masuk.delete` → User bisa hapus
4. Simpan perubahan

---

### Step 3: Verifikasi Langganan Owner ✔️

Pastikan owner/user memiliki:
1. **Langganan aktif** (`is_active = 1`)
2. **Tanggal langganan belum expired** (`end_date >= hari ini`)
3. **Paket-layanan yang sudah dikonfigurasi** dengan permissions yang tepat

**Cara cek di Database:**
```sql
SELECT * FROM langganan 
WHERE owner_id = [ID_OWNER] 
AND is_active = 1 
AND end_date >= CURDATE();
```

---

## Perubahan yang Sudah Dibuat

### 1. ✅ TransaksiController.php
**Update:** Mengubah permission dari `transaksi.read/create/update/delete` → `transaksi.masuk.read`, `transaksi.masuk.create`, dll

**Methods yang diupdate (14 total):**
- `indexMasuk()` → `transaksi.masuk.read`
- `createMasuk()` → `transaksi.masuk.create`
- `storeMasuk()` → `transaksi.masuk.create`
- `editMasuk()` → `transaksi.masuk.update`
- `updateMasuk()` → `transaksi.masuk.update`
- `destroyMasuk()` → `transaksi.masuk.delete`
- `bulkDestroyMasuk()` → `transaksi.masuk.delete`
- `indexKeluar()` → `transaksi.keluar.read`
- `createKeluar()` → `transaksi.keluar.create`
- `storeKeluar()` → `transaksi.keluar.create`
- `editKeluar()` → `transaksi.keluar.update`
- `updateKeluar()` → `transaksi.keluar.update`
- `destroyKeluar()` → `transaksi.keluar.delete`
- `bulkDestroyKeluar()` → `transaksi.keluar.delete`

### 2. ✅ PermissionService.php
**Update:** Perbaiki logic pengecakan permission (line 38-61)

**Perubahan:**
- Sebelum: Jika belum ada permission di database → ALLOW (SALAH)
- Sesudah: Jika tidak ada di package permissions → DENY (BENAR)

### 3. ✅ Migration: `2026_02_14_add_transaksi_masuk_keluar_permissions.php`
**File baru** yang menambahkan 8 permission baru ke database
- Up(): Menambahkan permissions
- Down(): Menghapus permissions (untuk rollback)

### 4. ✅ PermissionController Create View
**Update:** Menambahkan instruksi untuk format custom modul (contoh: `transaksi.masuk`, `transaksi.keluar`)

---

## Bagaimana RBAC Sekarang Bekerja? 🔄

### Flow Akses User ke Halaman Transaksi Masuk:

```
User buka /transaksi/masuk
    ↓
TransaksiController->indexMasuk()
    ↓
Check: PermissionService::check('transaksi.masuk.read')
    ↓
    ├─ Is Superadmin/Admin? → YES → ALLOW (bypass semua)
    │
    └─ Is Owner?
        ├─ Punya langganan aktif? → NO → DENY
        │
        └─ YES → Check package permissions
            ├─ 'transaksi.masuk.read' ada di paket? → YES → ALLOW
            └─ NO → DENY (overlay + opacity-30)
```

---

## Konfigurasi View (Sudah OK) ✅

### Paket-Layanan Views (create.blade.php & edit.blade.php)
- Sudah **dynamic groupBy('modul')**
- Otomatis menampilkan semua permissions dari database
- Ketika migration dijalankan, permissions baru akan otomatis muncul di form
- **Tidak perlu update manual!**

### Controller Views (transaksi masuk/keluar index)
- Sudah include `access-denied-overlay` component
- Sudah menggunakan `$hasAccessRead` untuk control opacity
- **Sudah OK!**

---

## Testing Checklist ✅

Setelah menjalankan langkah-langkah di atas:

```
[ ] 1. Jalankan: php artisan migrate
[ ] 2. Buka Permissions halaman → Lihat 8 permission baru
[ ] 3. Buka Paket-Layanan > Edit TRIAL
[ ] 4. Centang: transaksi.masuk.read, transaksi.masuk.create, dll
[ ] 5. Simpan
[ ] 6. Login sebagai owner dengan paket TRIAL
[ ] 7. Buka /transaksi/masuk → HARUS BISA AKSES (tanpa overlay)
[ ] 8. Buka /transaksi/keluar → HARUS TIDAK BISA (overlay + opacity-30)
     (karena transaksi.keluar.read tidak dicentang)
[ ] 9. Ubah paket TRIAL → centang transaksi.keluar.read
[ ] 10. Refresh /transaksi/keluar → HARUS BISA AKSES sekarang
```

---

## Untuk Menambah Permissions Module Lain

Jika ingin tambah permissions untuk module baru (seperti "payment.masuk", dll):

### Opsi 1: Pakai UI Permission Create ✅
1. Buka Admin → Permissions → Add Permission
2. Ketik modul: `payment.masuk` (atau custom format apapun)
3. Centang aksi: create, read, update, delete
4. Simpan
5. Permissions otomatis ter-generate

### Opsi 2: Pakai Migration (Recommended) ✅
1. Buat file migration:
   ```bash
   php artisan make:migration add_payment_permissions
   ```
2. Isi dengan:
   ```php
   DB::table('permissions')->insertOrIgnore([
       ['nama' => 'payment.masuk.read', 'modul' => 'payment', 'aksi' => 'read', 'created_at' => now()],
       // ... dll
   ]);
   ```
3. Run: `php artisan migrate`

---

## Summary

| Item | Status | Catatan |
|------|--------|---------|
| TransaksiController | ✅ Updated | 14 methods updated |
| PermissionService | ✅ Fixed | Logic bug already corrected |
| Migration | ✅ Created | Ready to run: `php artisan migrate` |
| Permission Create View | ✅ Updated | Instruksi untuk custom modul |
| Paket-Layanan Views | ✅ OK | Dynamic, tidak perlu update |
| Transaksi Views | ✅ OK | Overlay sudah ada |

**Langkah berikutnya:** **JALANKAN: `php artisan migrate`** untuk active permissions ke database!
