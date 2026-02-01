# Reusable Components Documentation

## Overview
Dokumentasi lengkap untuk komponen-komponen reusable yang telah dibuat untuk styling dan fungsionalitas tabel di seluruh aplikasi.

---

## Table of Contents
1. [CSS Components](#css-components)
2. [Pagination Component](#pagination-component)
3. [Action Dropdown Component](#action-dropdown-component)
4. [Implementation Examples](#implementation-examples)
5. [Troubleshooting](#troubleshooting)

---

## CSS Components

### File Location
`resources/css/table-components.css`

### Included Classes

#### 1. Column Width Classes
- **`.col-no`** - Fixed width column untuk nomor urut
  - Width: 60px
  - Min/Max: 60px
  - Implementasi: Gunakan pada `<th>` dan `<td>` di kolom No

```blade
<th class="py-3 text-left col-no">
    <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">No</p>
</th>
```

- **`.col-actions`** - Fixed width column untuk action buttons
  - Width: 50px
  - Min/Max: 50px
  - Implementasi: Gunakan pada `<th>` dan `<td>` di kolom Actions

```blade
<th class="py-3 text-center col-actions">
    <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Actions</p>
</th>
```

#### 2. Button Classes
- **`.btn-actions-menu`** - Button untuk membuka dropdown menu
  - Properties: No background, flex centered, opacity transition
  - Dark mode support: Automatic

```blade
<button class="btn-actions-menu" data-role-id="{{ $role->id }}" 
        data-role-edit="{{ route('pos-role.edit', $role) }}"
        data-role-destroy="{{ route('pos-role.destroy', $role) }}">
    <svg>...</svg>
</button>
```

#### 3. Dropdown Classes
- **`.actions-dropdown`** - Container untuk dropdown menu
  - Position: Fixed
  - Z-index: 1050
  - Background: White (light) / Navy-800 (dark)
  - Styling: Border radius, shadow, smooth transitions

- **`.actions-dropdown.show`** - Active state untuk dropdown

- **`.actions-dropdown-item`** - Individual menu item
  - Padding: 8px 16px
  - Font: 14px, medium weight
  - Hover effect: Background color change

##### Edit Item Variant
- **`.actions-dropdown-item.edit`**
  - Color: Blue-600 (light) / Blue-400 (dark)
  - Icon: Pencil/Edit icon

```html
<button class="actions-dropdown-item edit">
    <svg><!-- Edit icon --></svg>
    <span>Edit</span>
</button>
```

##### Delete Item Variant
- **`.actions-dropdown-item.delete`**
  - Color: Red-600 (light) / Red-400 (dark)
  - Icon: Trash/Delete icon
  - Border-top: 1px divider

```html
<button class="actions-dropdown-item delete">
    <svg><!-- Delete icon --></svg>
    <span>Delete</span>
</button>
```

### Dark Mode Support
Semua class memiliki dukungan dark mode melalui selector:
- `:root.dark`
- `[data-theme="dark"]`
- `.dark`

### Usage in HTML
```html
<link rel="stylesheet" href="{{ asset('css/table-components.css') }}">
```

---

## Pagination Component

### File Location
`resources/views/components/table/pagination.blade.php`

### Component Props

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| `items` | Paginator Object | Yes | Laravel paginator instance |
| `routeName` | String | Yes | Route name untuk pagination links |
| `searchParam` | Array\|null | No | Search parameter jika ada (format: `['name' => 'search_field', 'value' => 'search_value']`) |
| `perPageParam` | String | No | Query parameter untuk per-page (default: 'per_page') |

### Features
- ✅ Items per page selector (10, 25, 50, 100)
- ✅ Previous/Next buttons dengan disabled state
- ✅ Page number buttons dengan current page highlight
- ✅ Result info display ("Showing X to Y of Z results")
- ✅ Responsive design (flex column on mobile, row on desktop)
- ✅ Dark mode support
- ✅ Search parameter preservation

### Basic Implementation

```blade
<!-- resources/views/pages/pos-role/index.blade.php -->

@section('main')
    <div class="card">
        <!-- Table Content -->
        <table>
            <!-- ... -->
        </table>

        <!-- Pagination Component -->
        <div class="border-t border-gray-200 dark:border-white/10 px-6 py-4">
            @include('components.table.pagination', [
                'items' => $roles,
                'routeName' => 'pos-role.index',
                'searchParam' => null,
                'perPageParam' => 'per_page'
            ])
        </div>
    </div>
@endsection
```

### Advanced Implementation (dengan Search)

```blade
<!-- With search parameter preservation -->
<div class="border-t border-gray-200 dark:border-white/10 px-6 py-4">
    @include('components.table.pagination', [
        'items' => $roles,
        'routeName' => 'pos-role.index',
        'searchParam' => request('nama') ? ['name' => 'nama', 'value' => request('nama')] : null,
        'perPageParam' => 'per_page'
    ])
</div>
```

### Customization

#### Mengubah Items Per Page Options
Edit file `resources/views/components/table/pagination.blade.php` dan ubah bagian:
```blade
<select name="{{ $perPageParam ?? 'per_page' }}" onchange="this.form.submit()">
    <option value="10" ...>10</option>
    <option value="25" ...>25</option>
    <!-- Add or remove options here -->
</select>
```

#### Mengubah Styling
Kustomisasi Tailwind classes sesuai kebutuhan:
- Button classes: `h-9 w-9 rounded-lg bg-lightPrimary`
- Active page: `bg-brand-500 px-3`
- Hover state: `hover:bg-gray-100 dark:hover:bg-white/20`

---

## Action Dropdown Component

### File Location
- **View**: `resources/views/components/table-action-dropdown.blade.php`
- **JavaScript**: `public/js/table-action-dropdown.js`
- **CSS**: `resources/css/table-components.css`

### Component Props

Komponen ini tidak memiliki props yang wajib, namun mendukung default ID:
- `dropdownId` (default: 'actionDropdown')
- `editId` (default: 'editMenuItem')
- `deleteId` (default: 'deleteMenuItem')

### JavaScript Class: `TableActionDropdown`

#### Constructor Options

```javascript
new TableActionDropdown({
    dropdownSelector: '#actionDropdown',          // Selector untuk dropdown container
    buttonSelector: '.btn-actions-menu',          // Selector untuk action buttons
    editMenuSelector: '#editMenuItem',            // Selector untuk edit menu item
    deleteMenuSelector: '#deleteMenuItem',        // Selector untuk delete menu item
    zoomFactor: 0.9,                             // CSS zoom factor (penting!)
    confirmDeleteMessage: 'Confirm message?',     // Pesan konfirmasi delete
    onEditCallback: function(url) { },           // Optional callback untuk edit
    onDeleteCallback: function(url) { }          // Optional callback untuk delete
});
```

#### Key Features
- ✅ Fixed positioning dengan zoom factor compensation
- ✅ Keyboard support (Escape untuk close)
- ✅ Click-outside detection
- ✅ CSRF token integration
- ✅ Reusable di multiple pages
- ✅ Console logging untuk debugging

#### Important: Zoom Factor
**CRITICAL**: Jika di `resources/views/layouts/app.blade.php` ada CSS `zoom: 90%` atau nilai lain, HARUS disesuaikan dengan `zoomFactor` option!

```javascript
new TableActionDropdown({
    zoomFactor: 0.9  // Harus sama dengan zoom value di CSS
});
```

### Basic Implementation

#### Step 1: Include View Component
```blade
<!-- Include di file index -->
@include('components.table-action-dropdown')
```

#### Step 2: Create Action Buttons
```blade
<td class="py-4 col-actions">
    <button class="btn-actions-menu" 
            data-role-id="{{ $role->id }}"
            data-role-edit="{{ route('pos-role.edit', $role) }}"
            data-role-destroy="{{ route('pos-role.destroy', $role) }}">
        <!-- 3-dot icon SVG -->
        <svg>...</svg>
    </button>
</td>
```

#### Step 3: Initialize JavaScript
```blade
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new TableActionDropdown({
            dropdownSelector: '#actionDropdown',
            buttonSelector: '.btn-actions-menu',
            editMenuSelector: '#editMenuItem',
            deleteMenuSelector: '#deleteMenuItem',
            zoomFactor: 0.9,
            confirmDeleteMessage: 'Apakah Anda yakin ingin menghapus item ini?'
        });
    });
</script>
@endpush
```

#### Step 4: Load CSS
```blade
@push('style')
<link rel="stylesheet" href="{{ asset('css/table-components.css') }}">
@endpush
```

### Advanced: Custom Callbacks

```blade
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new TableActionDropdown({
            dropdownSelector: '#actionDropdown',
            buttonSelector: '.btn-actions-menu',
            zoomFactor: 0.9,
            onEditCallback: function(url) {
                // Custom action sebelum redirect
                console.log('Editing:', url);
                // Bisa tambahkan loading indicator dll
                window.location.href = url;
            },
            onDeleteCallback: function(url) {
                // Custom delete logic
                fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).then(response => {
                    if (response.ok) {
                        location.reload();
                    }
                });
            }
        });
    });
</script>
@endpush
```

### Button Dataset Attributes

```blade
<button class="btn-actions-menu"
        data-role-id="{{ $role->id }}"              <!-- Identifier -->
        data-role-name="{{ $role->name }}"          <!-- Display name -->
        data-role-edit="{{ route('pos-role.edit', $role) }}"     <!-- Edit URL -->
        data-role-destroy="{{ route('pos-role.destroy', $role) }}"> <!-- Delete URL -->
    <svg>...</svg>
</button>
```

---

## Implementation Examples

### Example 1: POS Role Index (Complete)

```blade
<!-- resources/views/pages/pos-role/index.blade.php -->

@extends('layouts.app')

@section('title', 'User Roles')

@push('style')
<link rel="stylesheet" href="{{ asset('css/table-components.css') }}">
@endpush

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 pb-4">
            <div>
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">User Roles</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $roles->total() }} total roles
                </p>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto px-6 pb-6">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-white/10">
                        <th class="py-3 text-left col-no">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">No</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Role Name</p>
                        </th>
                        <th class="py-3 text-center col-actions">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Actions</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                    <tr class="border-b border-gray-100 dark:border-white/10 hover:bg-lightPrimary dark:hover:bg-navy-700">
                        <td class="py-4 col-no">
                            <p class="text-sm font-bold text-navy-700 dark:text-white">{{ $loop->iteration }}</p>
                        </td>
                        <td class="py-4">
                            <p class="text-sm font-bold text-navy-700 dark:text-white">{{ $role->nama }}</p>
                        </td>
                        <td class="py-4 col-actions">
                            <button class="btn-actions-menu"
                                    data-role-id="{{ $role->id }}"
                                    data-role-edit="{{ route('pos-role.edit', $role) }}"
                                    data-role-destroy="{{ route('pos-role.destroy', $role) }}">
                                <svg><!-- 3-dot icon --></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-12 text-center">
                            <p class="text-gray-600 dark:text-gray-400">No roles found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="border-t border-gray-200 dark:border-white/10 px-6 py-4">
            @include('components.table.pagination', [
                'items' => $roles,
                'routeName' => 'pos-role.index',
                'searchParam' => request('nama') ? ['name' => 'nama', 'value' => request('nama')] : null,
                'perPageParam' => 'per_page'
            ])
        </div>
    </div>
</div>

<!-- Dropdown Component -->
@include('components.table-action-dropdown')

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new TableActionDropdown({
            dropdownSelector: '#actionDropdown',
            buttonSelector: '.btn-actions-menu',
            editMenuSelector: '#editMenuItem',
            deleteMenuSelector: '#deleteMenuItem',
            zoomFactor: 0.9,
            confirmDeleteMessage: 'Apakah Anda yakin ingin menghapus role ini?'
        });
    });
</script>
@endpush
```

### Example 2: Menggunakan di Page Lain

```blade
<!-- resources/views/pages/supplier/index.blade.php -->

@extends('layouts.app')
@section('title', 'Suppliers')

@push('style')
<link rel="stylesheet" href="{{ asset('css/table-components.css') }}">
@endpush

@section('main')
<div class="card">
    <table>
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th>Supplier Name</th>
                <th class="col-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($suppliers as $supplier)
            <tr>
                <td class="col-no">{{ $loop->iteration }}</td>
                <td>{{ $supplier->name }}</td>
                <td class="col-actions">
                    <button class="btn-actions-menu"
                            data-role-edit="{{ route('supplier.edit', $supplier) }}"
                            data-role-destroy="{{ route('supplier.destroy', $supplier) }}">
                        <svg><!-- icon --></svg>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination dengan search -->
    <div class="border-t px-6 py-4">
        @include('components.table.pagination', [
            'items' => $suppliers,
            'routeName' => 'supplier.index',
            'searchParam' => request('search') ? ['name' => 'search', 'value' => request('search')] : null
        ])
    </div>
</div>

@include('components.table-action-dropdown')
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new TableActionDropdown({
            buttonSelector: '.btn-actions-menu',
            zoomFactor: 0.9,
            confirmDeleteMessage: 'Hapus supplier?'
        });
    });
</script>
@endpush
```

---

## Troubleshooting

### Issue 1: Dropdown Positioning Salah
**Problem**: Dropdown muncul di posisi yang tidak sesuai dengan button

**Solution**:
1. Periksa CSS `zoom` value di `resources/views/layouts/app.blade.php`
2. Pastikan `zoomFactor` di JavaScript sesuai dengan CSS zoom
3. Contoh: Jika CSS punya `zoom: 90%`, gunakan `zoomFactor: 0.9`

```javascript
new TableActionDropdown({
    zoomFactor: 0.9  // Disesuaikan dengan zoom CSS
});
```

### Issue 2: Dropdown Tidak Muncul
**Problem**: Dropdown tidak terlihat saat button diklik

**Solution**:
1. Pastikan `@include('components.table-action-dropdown')` ada di file
2. Pastikan CSS dimuat dengan benar: `<link href="table-components.css">`
3. Cek console untuk error messages
4. Pastikan button memiliki class `.btn-actions-menu`

### Issue 3: Edit/Delete Tidak Bekerja
**Problem**: Klik edit/delete tidak ada response

**Solution**:
1. Pastikan button memiliki `data-role-edit` dan `data-role-destroy` attributes
2. Pastikan URL valid dengan: `route('pos-role.edit', $role)`
3. Periksa console untuk error atau redirect yang gagal

### Issue 4: Pagination Link Parameter Hilang
**Problem**: Saat klik pagination, search parameter hilang

**Solution**:
Pastikan saat include component, pass `searchParam` dengan benar:

```blade
@include('components.table.pagination', [
    'items' => $data,
    'routeName' => 'data.index',
    'searchParam' => request('search_field') ? ['name' => 'search_field', 'value' => request('search_field')] : null
])
```

### Issue 5: Dark Mode Tidak Bekerja
**Problem**: Dropdown warna masih terang di dark mode

**Solution**:
1. Pastikan dark mode trigger ada di layout
2. Periksa apakah menggunakan `.dark` class atau `[data-theme="dark"]`
3. CSS sudah support kedua selector, pastikan HTML element yang benar

---

## Best Practices

### 1. Always Include CSS
```blade
@push('style')
<link rel="stylesheet" href="{{ asset('css/table-components.css') }}">
@endpush
```

### 2. Initialization di @push('scripts')
```blade
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new TableActionDropdown({ /* options */ });
    });
</script>
@endpush
```

### 3. Proper Data Attributes
```blade
<button class="btn-actions-menu"
        data-role-id="{{ $item->id }}"
        data-role-edit="{{ route('item.edit', $item) }}"
        data-role-destroy="{{ route('item.destroy', $item) }}">
```

### 4. Search Parameter Handling
```blade
'searchParam' => request('search_field') 
    ? ['name' => 'search_field', 'value' => request('search_field')] 
    : null
```

### 5. Multiple Search Parameters
```blade
@php
    $searchParams = [];
    if (request('search')) {
        $searchParams[] = ['name' => 'search', 'value' => request('search')];
    }
    if (request('category')) {
        $searchParams[] = ['name' => 'category', 'value' => request('category')];
    }
@endphp

@include('components.table.pagination', [
    'items' => $items,
    'routeName' => 'items.index',
    'searchParams' => $searchParams ?? null
])
```

---

## File Structure Summary

```
resources/
├── css/
│   └── table-components.css          # All styling
├── views/
│   └── components/
│       ├── table/
│       │   └── pagination.blade.php   # Pagination component
│       └── table-action-dropdown.blade.php  # Dropdown component
└── views/
    └── pages/
        └── pos-role/
            └── index.blade.php        # Example implementation

public/
└── js/
    └── table-action-dropdown.js       # JavaScript class

resources/
└── views/
    └── layouts/
        └── app.blade.php              # Layout with CSS zoom config
```

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | Feb 2026 | Initial release: Pagination & Action Dropdown components |

---

## Support

Untuk pertanyaan atau issue, silakan cek:
1. Troubleshooting section di atas
2. Console browser untuk error messages
3. Network tab untuk failed requests
4. Inspect element untuk styling issues

---

**Last Updated**: February 2, 2026
