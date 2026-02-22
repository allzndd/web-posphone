@forelse ($produk as $item)
<tr class="border-b border-gray-100 dark:border-white/10 hover:bg-lightPrimary dark:hover:bg-navy-700 transition-colors cursor-pointer" data-href="{{ route('produk.show', $item) }}">
    <td class="py-4" style="width: 40px;" onclick="event.stopPropagation()">
        <input type="checkbox" class="produk-checkbox rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-navy-700 cursor-pointer" 
               value="{{ $item->id }}" 
               onchange="updateBulkDeleteButton()">
    </td>
    <td class="py-4 col-no">
        <p class="text-sm font-bold text-navy-700 dark:text-white">{{ ($produk->currentPage() - 1) * $produk->perPage() + $loop->iteration }}</p>
    </td>
    <td class="py-4">
        @if($item->nama)
            <p class="text-sm font-bold text-navy-700 dark:text-white">{{ $item->nama }}</p>
        @else
            <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-800/30 px-3 py-1 text-xs font-medium text-gray-600 dark:text-gray-400">
                Unnamed
            </span>
        @endif
    </td>
    <td class="py-4">
        @if($item->imei)
            <p class="text-sm font-mono text-gray-600 dark:text-gray-400">{{ $item->imei }}</p>
        @else
            <p class="text-sm text-gray-400 dark:text-gray-600">-</p>
        @endif
    </td>
    <td class="py-4">
        @if($item->pos_ram_id && $item->ram)
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->ram->kapasitas ?? '-' }} GB</p>
        @else
            <p class="text-sm text-gray-400 dark:text-gray-600">-</p>
        @endif
    </td>
    <td class="py-4">
        @if($item->pos_penyimpanan_id && $item->penyimpanan)
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->penyimpanan->kapasitas ?? '-' }} GB</p>
        @else
            <p class="text-sm text-gray-400 dark:text-gray-600">-</p>
        @endif
    </td>
    <td class="py-4">
        @if($item->pos_warna_id && $item->warna)
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->warna->warna ?? '-' }}</p>
        @else
            <p class="text-sm text-gray-400 dark:text-gray-600">-</p>
        @endif
    </td>
    <td class="py-4">
        @if($item->battery_health)
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->battery_health }}%</p>
        @else
            <p class="text-sm text-gray-400 dark:text-gray-600">-</p>
        @endif
    </td>
    <td class="py-4 text-left">
        <p class="text-sm font-bold text-green-500 dark:text-green-400">
            {{ get_currency_symbol() }} {{ number_format($item->harga_jual, 0, ',', '.') }}
        </p>
    </td>
    <td class="py-4 col-actions" onclick="event.stopPropagation()">
        <div class="flex items-center justify-center gap-2">
            @permission('produk.update')
            <a href="{{ route('produk.edit', $item) }}"
               class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-500 transition duration-200 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-400"
               title="Edit">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34a.9959.9959 0 00-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"></path>
                </svg>
            </a>
            @endpermission
            
            @permission('produk.delete')
            <button onclick="confirmDelete('{{ route('produk.destroy', $item) }}')"
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-100 text-red-500 transition duration-200 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400"
                    title="Delete">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path>
                </svg>
            </button>
            @endpermission
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="10" class="py-12 text-center">
        <div class="flex flex-col items-center justify-center">
            <svg class="h-16 w-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            <p class="text-lg font-medium text-gray-600 dark:text-gray-400">No products found</p>
            <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Try adjusting your search criteria</p>
        </div>
    </td>
</tr>
@endforelse
