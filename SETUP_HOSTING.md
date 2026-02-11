# Setup Trial System Untuk Hosting

Karena website sudah di hosting, silakan jalankan SQL berikut di **phpMyAdmin** atau database management tool hosting Anda:

## 1. Tambah Kolom `durasi_satuan` ke Tabel `tipe_layanan`

```sql
ALTER TABLE `tipe_layanan` 
ADD COLUMN `durasi_satuan` ENUM('hari', 'bulan', 'tahun') DEFAULT 'bulan' AFTER `durasi`;
```

## 2. Insert Paket Trial (Opsional - akan auto-create saat register)

```sql
INSERT INTO `tipe_layanan` (`nama`, `slug`, `harga`, `durasi`, `durasi_satuan`, `created_at`, `updated_at`) 
VALUES ('Trial', 'trial', 0, 15, 'hari', NOW(), NOW())
ON DUPLICATE KEY UPDATE 
    `harga` = 0, 
    `durasi` = 15, 
    `durasi_satuan` = 'hari';
```

## 3. Test Register

Setelah SQL dijalankan:

1. Buka halaman register website Anda
2. Daftar akun baru (owner)
3. Sistem akan otomatis:
   - Create user + owner
   - Create trial subscription 15 hari
   - Set is_trial = 1, is_active = 0
   
4. Setelah login, owner bisa akses dashboard dan fitur selama 15 hari
5. Setelah 15 hari expired, akan redirect ke halaman subscription expired

## Fitur Yang Sudah Aktif:

✅ **Auto Trial Creation** - User baru otomatis dapat trial 15 hari  
✅ **Subscription Check** - Middleware cek status langganan di setiap request  
✅ **Auto Expiration** - Sistem otomatis update status jadi inactive jika expired  
✅ **Blocking Access** - User yang expired tidak bisa akses dashboard  
✅ **Duration Flexibility** - Support durasi dalam hari/bulan/tahun  
✅ **Trial Package Auto-Create** - Jika belum ada, akan dibuat otomatis saat register pertama

## Testing:

1. Register user baru → otomatis trial 15 hari
2. Check tabel `langganan` → ada record baru dengan is_trial=1
3. Coba akses dashboard → bisa (karena masih trial)
4. Ubah end_date di database jadi kemarin → otomatis blocked

## Catatan:

- **Superadmin (role_id=1)** tidak terkena subscription check
- **Owner & Admin (role_id=2,3)** wajib punya subscription aktif
- Trial package akan otomatis dibuat saat user pertama register (tidak perlu insert manual)
