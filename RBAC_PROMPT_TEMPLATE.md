# RBAC Implementation Prompt untuk Modul Lain

## Template Prompt untuk Apply RBAC ke Modul Baru

Gunakan prompt ini ketika mau implementasi RBAC ke modul baru (misal: Produk, Supplier, dll).

---

### 📋 TEMPLATE PROMPT

```
Saya ingin menerapkan RBAC (Role-Based Access Control) ke modul [NAMA_MODUL].

File-file yang perlu disesuaikan:

1. **Database**
   - Insert ke permissions table: [MODUL].create, [MODUL].read, [MODUL].update, [MODUL].delete
   - Insert ke package_permissions dengan max_records yang sesuai

2. **Controller**: app/Http/Controllers/[NamaModul]Controller.php
   - Import PermissionService
   - Di index(): Check 'modul.read', pass $hasAccessRead ke view (tanpa redirect)
   - Di create(): Check 'modul.create', pass $currentCount & $maxRecords
   - Di store(): Check 'modul.create', validate limit
   - Di edit(): Check 'modul.update'
   - Di update(): Check 'modul.update'
   - Di destroy(): Check 'modul.delete'
   - Di bulkDestroy() (jika ada): Check 'modul.delete'

3. **Views**
   - Copy pattern dari resources/views/pages/pelanggan/
   - Di index.blade.php: Include component `@include('components.access-denied-overlay', ['module' => '[NAMA_MODUL]', 'hasAccessRead' => $hasAccessRead])`
   - Replace @if($canCreate) dengan @permission('[modul].create')
   - Replace @if($canDelete) dengan @permission('[modul].delete')
   - Replace @if($canUpdate) dengan @permission('[modul].update')
   - Replace @if($hasActions) dengan nested @permission
   - Hapus emoji (ℹ️) dari semua info box
   - Update permission names sesuai modul

4. **Routes** (Optional)
   - Tambah middleware permission check di route group

Silakan update semua file sesuai pattern ini tanpa perlu dijelaskan lagi.
```

---

## 📌 Contoh Penggunaan

### Untuk Modul Produk:

```
Saya ingin menerapkan RBAC (Role-Based Access Control) ke modul Produk.

File-file yang perlu disesuaikan:

1. **Database**
   - Insert ke permissions table: produk.create, produk.read, produk.update, produk.delete
   - Insert ke package_permissions dengan max_records yang sesuai

2. **Controller**: app/Http/Controllers/ProdukController.php
   - Import PermissionService
   - Di index(): Check 'produk.read', pass $hasAccessRead ke view
   - Di create(): Check 'produk.create', pass $currentCount & $maxRecords
   - Di store(): Check 'produk.create', validate limit
   - Di edit(): Check 'produk.update'
   - Di update(): Check 'produk.update'
   - Di destroy(): Check 'produk.delete'
   - Di bulkDestroy(): Check 'produk.delete'

3. **Views** (resources/views/pages/produk/)
   - index.blade.php: Include component access-denied-overlay, @permission directives
   - create.blade.php: @permission('produk.create'), quota info tanpa emoji
   - edit.blade.php: @permission('produk.update'), @permission('produk.delete')

4. **Routes** (Optional)
   - Tambah middleware permission check untuk produk routes

Silakan update semua file sesuai pattern RBAC customer tanpa perlu dijelaskan lagi.
```

### Untuk Modul Supplier:

```
Saya ingin menerapkan RBAC (Role-Based Access Control) ke modul Supplier.

File-file yang perlu disesuaikan:

1. **Database**
   - Insert ke permissions table: supplier.create, supplier.read, supplier.update, supplier.delete
   - Insert ke package_permissions dengan max_records yang sesuai

2. **Controller**: app/Http/Controllers/SupplierController.php
   - Import PermissionService
   - Di semua action: Replace customer.* dengan supplier.*
   - Validate limit di store() method

3. **Views** (resources/views/pages/supplier/)
   - Copy pattern dari customer, replace permission names
   - Hapus semua emoji
   - Conditional render berdasarkan supplier permissions

4. **Routes** (Optional)
   - Tambah middleware permission check

Silakan update sesuai pattern RBAC customer.
```

---

## 🔄 Pattern Summary

### Permission Names:
```
Format: [modul].[action]

customer.create    → customer.read    → customer.update    → customer.delete
produk.create      → produk.read      → produk.update      → produk.delete
supplier.create    → supplier.read    → supplier.update    → supplier.delete
```

### Subscription Validation (IMPORTANT)
PermissionService sekarang melakukan validasi berlapis untuk owner:
1. Check apakah user punya owner relationship
2. Check apakah owner memiliki langganan yang AKTIF (`is_active = 1`)
3. Check apakah langganan belum expired (`end_date >= hari ini`)
4. Baru check permission existence di package

Jika langganan tidak aktif atau expired, permission akan DENIED meski permission record ada di database.

**Catatan**: Superadmin dan Admin bypass semua validasi subscription, hanya owner yang divalidasi langganan-nya.

### Controller Pattern:
```php
// index: Check read permission, pass flag ke view (tanpa redirect)
$hasAccessRead = PermissionService::check('[modul].read');
return view('pages.[modul].index', compact(..., 'hasAccessRead'));

// create/store: Check create permission & validate limit
if (!PermissionService::check('[modul].create')) {
    return redirect('/');
}
$maxRecords = PermissionService::getMaxRecords('[modul].create');
if (PermissionService::isReachedLimit('[modul].create', $currentCount)) {
    return redirect()->back()->with('error', 'Limit tercapai');
}

// edit/update: Check update permission
if (!PermissionService::check('[modul].update')) {
    return redirect('/');
}

// destroy: Check delete permission
if (!PermissionService::check('[modul].delete')) {
    return redirect('/');
}
```

### View Pattern (index.blade.php):
```blade
@section('main')
<!-- Access Denied Overlay Component (REUSABLE) -->
@include('components.access-denied-overlay', ['module' => '[NAMA_MODUL]', 'hasAccessRead' => $hasAccessRead])

<div class="mt-3 px-[11px] pr-[10px] @if(!$hasAccessRead) opacity-30 pointer-events-none @endif">
    <!-- Konten tabel di sini -->
    
    <!-- Conditional CRUD buttons dengan @permission -->
    @permission('[modul].create')
        <!-- Add New Button -->
    @endpermission

    @permission('[modul].delete')
        <!-- Bulk Delete Button -->
    @endpermission

    @permission('[modul].update')
        <!-- Edit actions -->
    @endpermission
</div>
@endsection
```

**Component Location**: `resources/views/components/access-denied-overlay.blade.php`
**Parameters**:
- `$module` - Display name modul (misal: "Customer", "Produk", "Supplier")
- `$hasAccessRead` - Boolean dari `PermissionService::check('[modul].read')`

**Features**:
- Overlay backdrop-blur otomatis muncul jika $hasAccessRead = false
- Pesan dinamis berdasarkan module name
- Tombol redirect ke dashboard
- Mobile responsive

---

## ✅ Checklist per Modul

- [ ] Database: Insert permissions untuk [modul].create, read, update, delete
- [ ] Database: Insert package_permissions dengan max_records
- [ ] Controller: Import PermissionService
- [ ] Controller: Tambah permission checks di semua action
- [ ] Controller: Validate max_records di store()
- [ ] Views: Replace @if($can*) dengan @permission()
- [ ] Views: Hapus semua emoji (ℹ️, ➕, ❌, dll)
- [ ] Views: Update permission names sesuai modul
- [ ] Views: Nested @permission untuk multi-level checks
- [ ] Routes: (Optional) Tambah middleware permission

---

## 📝 Notes

1. **PermissionService sudah universal**, tidak perlu modifikasi
2. **PermissionService melakukan subscription validation** - pastikan owner memiliki langganan aktif (is_active=1) dan belum expired (end_date >= today)
3. **AppServiceProvider sudah support infinite permissions**, tinggal inject nama permission baru ke database
4. **Blade directive @permission() universal**, bisa pakai untuk permission apapun
5. **Middleware CheckPermission universal**, bisa dipakai untuk route apapun
6. **Jangan pakai emoji**, gunakan text biasa untuk accessibility
7. **Access overlay component reusable** - gunakan `@include('components.access-denied-overlay', ['module' => 'NamaModul', 'hasAccessRead' => $hasAccessRead])`

---

## 🚀 Quick Apply Steps

1. **Insert permissions ke database** (SQL query untuk modul baru)
2. **Insert package_permissions** dengan langganan yang sudah aktif
3. **Copy PelangganController** → Rename sesuai modul
4. **Find-Replace** customer → [modul_name]
5. **Copy Pelanggan Views** → Create folder baru
6. **Find-Replace** permission names & hapus emoji
7. **Pastikan langganan owner sudah is_active=1** jika mau test permission
8. **Test setiap action**

Selesai! 🎉

---

## ⚠️ Troubleshooting Permission Denied

Jika permission sudah dikasih tapi tetap "Akses Ditolak":
1. Check: Apakah permission record ada di `permissions` table? ✓
2. Check: Apakah package_permissions linked dengan permissions? ✓
3. Check: **Apakah langganan owner is_active = 1?** ← MOST COMMON
4. Check: **Apakah langganan belum expired (end_date >= hari ini)?** ← MOST COMMON
5. Check: Clear cache/reload halaman

Solusi: Update `langganan` table set `is_active = 1` untuk owner yang permission-nya tidak berfungsi.

---

## Opsi B: Fallback Permission (Implemented)

Sistem sekarang menggunakan **Opsi B - Fallback Permission**:

**Logika**:
1. Jika permission ada di package_permissions → ALLOW (permission dikonfigurasi)
2. Jika permission TIDAK ada di package_permissions tapi EXISTS di permissions table → DENY (admin sengaja tidak kasih akses)
3. Jika permission TIDAK ada di permissions table sama sekali → ALLOW (temporary - belum dikonfigurasi, anggap akses terbuka untuk development)

**Benefit**:
- Modul baru yang belum dikonfigurasi di dashboard bisa tetap diakses (temporary)
- Admin bisa control akses lebih granular dengan setup di database
- Tidak perlu setup setiap modul langsung, bisa bertahap

**Contoh**:
- Menu "Produk" belum ada permission setting → ALLOWED (langsung bisa diakses)
- Menu "Supplier" sudah ada permission setting di database → Checked berdasarkan package permissions
- Menu "Services" sudah dikonfigurasi tapi owner tidak punya permission → DENIED (akses ditolak)
