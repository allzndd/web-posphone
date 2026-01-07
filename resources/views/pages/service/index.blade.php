@extends('layouts.app')

@section('title', 'Services')

@section('main')
<div class="mt-3 px-[11px] pr-[10px]">
    <!-- Services Card -->
    <div class="!z-5 relative flex flex-col rounded-[20px] bg-white bg-clip-border shadow-3xl shadow-shadow-500 dark:!bg-navy-800 dark:text-white dark:shadow-none">
        <!-- Card Header -->
        <div class="flex items-center justify-between p-6 pb-4">
            <div>
                <h4 class="text-xl font-bold text-navy-700 dark:text-white">Services</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $services->total() }} total services
                </p>
            </div>
            
            <!-- Add New Button -->
            <div>
                <a href="{{ route('service.create') }}" 
                   class="flex items-center gap-2 rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                        <path fill="none" d="M0 0h24v24H0z"></path>
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path>
                    </svg>
                    Add New Service
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="px-6 pb-4">
            <form method="GET" action="{{ route('service.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-3">
                <!-- Search Name -->
                <input type="text" name="nama" value="{{ request('nama') }}" placeholder="Search service name..." 
                       class="rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-2 text-sm font-medium text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400 placeholder:text-gray-400 dark:placeholder:text-gray-500" />

                <!-- Store Filter -->
                <select name="pos_toko_id" onchange="this.form.submit()" 
                        class="rounded-xl border border-gray-200 dark:border-white/10 bg-white/100 dark:bg-navy-900/100 px-4 py-2 text-sm font-medium text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400">
                    <option value="">All Stores</option>
                    @foreach($tokos as $toko)
                        <option value="{{ $toko->id }}" {{ request('pos_toko_id') == $toko->id ? 'selected' : '' }}>
                            {{ $toko->nama }}
                        </option>
                    @endforeach
                </select>

                <div class="flex gap-2">
                    <button type="submit" 
                            class="flex items-center justify-center gap-2 rounded-xl bg-brand-500 px-5 py-2 text-sm font-bold text-white transition duration-200 hover:bg-brand-600 active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Search
                    </button>
                    @if(request()->hasAny(['nama', 'pos_toko_id']))
                        <a href="{{ route('service.index') }}" 
                           class="flex items-center justify-center rounded-xl bg-gray-100 px-5 py-2 text-sm font-bold text-navy-700 transition duration-200 hover:bg-gray-200 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto px-6 pb-6">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-white/10">
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Service Name</p>
                        </th>
                        <th class="py-3 text-left">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Store</p>
                        </th>
                        <th class="py-3 text-right">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Price</p>
                        </th>
                        <th class="py-3 text-center">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Duration</p>
                        </th>
                        <th class="py-3 text-center">
                            <p class="text-sm font-bold text-gray-600 dark:text-white uppercase">Actions</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                    <tr class="border-b border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-white/5 cursor-pointer transition" 
                        data-href="{{ route('service.edit', $service->id) }}"
                        onclick="window.location = this.dataset.href">
                        <!-- Service Name -->
                        <td class="py-4">
                            <div>
                                <p class="text-sm font-bold text-navy-700 dark:text-white">{{ $service->nama }}</p>
                                @if($service->keterangan)
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $service->keterangan }}</p>
                                @endif
                            </div>
                        </td>
                        
                        <!-- Store -->
                        <td class="py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $service->toko->nama ?? '-' }}</p>
                        </td>
                        
                        <!-- Price -->
                        <td class="py-4 text-right">
                            <p class="text-sm font-bold text-brand-500 dark:text-brand-400">{{ $service->formatted_harga }}</p>
                        </td>
                        
                        <!-- Duration -->
                        <td class="py-4 text-center">
                            @if($service->durasi)
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-blue-100 dark:bg-blue-500/20 px-3 py-1 text-xs font-bold text-blue-600 dark:text-blue-300">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $service->formatted_durasi }}
                                </span>
                            @else
                                <span class="text-sm text-gray-400 dark:text-gray-600">-</span>
                            @endif
                        </td>
                        
                        <!-- Actions -->
                        <td class="py-4 text-center">
                            <div class="flex items-center justify-center gap-2" onclick="event.stopPropagation()">
                                <a href="{{ route('service.edit', $service->id) }}" 
                                   class="flex items-center justify-center rounded-lg bg-lightPrimary p-2 text-brand-500 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                                        <path fill="none" d="M0 0h24v24H0z"></path>
                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34a.9959.9959 0 00-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('service.destroy', $service->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('Are you sure you want to delete this service?')"
                                            class="flex items-center justify-center rounded-lg bg-red-100 p-2 text-red-500 transition duration-200 hover:bg-red-200 dark:bg-red-500/20 dark:text-red-300 dark:hover:bg-red-500/30">
                                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                                            <path fill="none" d="M0 0h24v24H0z"></path>
                                            <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-lg font-medium text-gray-600 dark:text-gray-400">No services found</p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-500">Start by adding a new service</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex flex-col sm:flex-row items-center justify-between border-t border-gray-200 dark:border-white/10 px-6 py-4 gap-4">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-sm text-gray-600 dark:text-gray-400">Items per page:</span>
                <form method="GET" action="{{ route('service.index') }}" id="perPageForm" class="inline-block">
                    <input type="hidden" name="nama" value="{{ request('nama') }}">
                    <input type="hidden" name="pos_toko_id" value="{{ request('pos_toko_id') }}">
                    <select name="per_page" onchange="this.form.submit()" 
                            class="rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:!bg-navy-800 px-3 py-1.5 text-sm text-navy-700 dark:text-white outline-none focus:border-brand-500 dark:focus:border-brand-400 [&>option]:!bg-white [&>option]:dark:!bg-navy-800 [&>option]:!text-navy-700 [&>option]:dark:!text-white">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Showing {{ $services->firstItem() ?? 0 }} to {{ $services->lastItem() ?? 0 }} of {{ $services->total() }} results
                </span>
            </div>
            <div class="flex items-center gap-1">
                @if ($services->onFirstPage())
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-gray-400 dark:bg-navy-700 dark:text-gray-600 cursor-not-allowed">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                            <path fill="none" d="M0 0h24v24H0z"></path>
                            <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"></path>
                        </svg>
                    </span>
                @else
                    <a href="{{ $services->appends(request()->except('page'))->previousPageUrl() }}" 
                       class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-brand-500 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                            <path fill="none" d="M0 0h24v24H0z"></path>
                            <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"></path>
                        </svg>
                    </a>
                @endif

                @foreach ($services->getUrlRange(max(1, $services->currentPage() - 2), min($services->lastPage(), $services->currentPage() + 2)) as $page => $url)
                    @if ($page == $services->currentPage())
                        <span class="flex h-9 min-w-[36px] items-center justify-center rounded-lg bg-brand-500 px-3 text-sm font-bold text-white dark:bg-brand-400">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $services->appends(request()->except('page'))->url($page) }}" 
                           class="flex h-9 min-w-[36px] items-center justify-center rounded-lg bg-lightPrimary px-3 text-sm font-medium text-navy-700 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                @if ($services->hasMorePages())
                    <a href="{{ $services->appends(request()->except('page'))->nextPageUrl() }}" 
                       class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-brand-500 transition duration-200 hover:bg-gray-100 dark:bg-navy-700 dark:text-white dark:hover:bg-white/20">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                            <path fill="none" d="M0 0h24v24H0z"></path>
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"></path>
                        </svg>
                    </a>
                @else
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-lightPrimary text-gray-400 dark:bg-navy-700 dark:text-gray-600 cursor-not-allowed">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg">
                            <path fill="none" d="M0 0h24v24H0z"></path>
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"></path>
                        </svg>
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
