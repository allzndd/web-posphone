<!-- Sidebar Component - 100% Horizon Template -->
<div id="sidebar" class="sidebar-custom-scroll sm:none duration-175 linear fixed !z-50 flex min-h-full max-h-screen flex-col bg-white pb-10 shadow-2xl shadow-white/5 transition-all dark:!bg-navy-800 dark:text-white md:!z-50 lg:!z-50 xl:!z-0 overflow-y-auto" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-96 xl:translate-x-0'">
    
    <span class="absolute top-4 right-4 block cursor-pointer xl:hidden" @click="sidebarOpen = false">
        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 20 20" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
        </svg>
    </span>

    <div class="mx-[56px] mt-[50px] flex items-center">
        <div class="mt-1 ml-1 h-2.5 font-poppins text-[26px] font-bold uppercase text-navy-700 dark:text-white">
            PosPhone <span class="font-medium">POS</span>
        </div>
    </div>
    <div class="mt-[58px] mb-7 h-px bg-gray-300 dark:bg-white/30"></div>
    
    <!-- Nav Menu -->
    <ul class="mb-auto pt-1">
        
        @if(auth()->user()->isOwner() || auth()->user()->isAdmin())
        <!-- Dashboard -->
        <li class="relative mb-3 flex hover:cursor-pointer">
            <a href="{{ route('dashboard') }}" class="w-full">
                <div class="my-[3px] flex cursor-pointer items-center px-8">
                    <span class="{{ Request::is('dashboard') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"></path></svg>
                    </span>
                    <p class="leading-1 ml-4 flex {{ Request::is('dashboard') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        Dashboard
                    </p>
                </div>
            </a>
            @if(Request::is('dashboard'))
                <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
            @endif
        </li>
        @endif

        @if(auth()->user()->isSuperadmin())
        <!-- Dashboard Superadmin -->
        <li class="relative mb-3 flex hover:cursor-pointer">
            <a href="{{ route('dashboard-superadmin') }}" class="w-full">
                <div class="my-[3px] flex cursor-pointer items-center px-8">
                    <span class="{{ Request::is('dashboard-superadmin') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"></path></svg>
                    </span>
                    <p class="leading-1 ml-4 flex {{ Request::is('dashboard-superadmin') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        Dashboard Superadmin
                    </p>
                </div>
            </a>
            @if(Request::is('dashboard-superadmin'))
                <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
            @endif
        </li>

        <!-- Manage Owners -->
        <li class="relative mb-3 flex hover:cursor-pointer">
            <a href="{{ route('kelola-owner.index') }}" class="w-full">
                <div class="my-[3px] flex cursor-pointer items-center px-8">
                    <span class="{{ Request::is('kelola-owner*') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M16.5 12c1.38 0 2.49-1.12 2.49-2.5S17.88 7 16.5 7C15.12 7 14 8.12 14 9.5s1.12 2.5 2.5 2.5zM9 11c1.66 0 2.99-1.34 2.99-3S10.66 5 9 5C7.34 5 6 6.34 6 8s1.34 3 3 3zm7.5 3c-1.83 0-5.5.92-5.5 2.75V19h11v-2.25c0-1.83-3.67-2.75-5.5-2.75zM9 13c-2.33 0-7 1.17-7 3.5V19h7v-2.25c0-.85.33-2.34 2.37-3.47C10.5 13.1 9.66 13 9 13z"></path></svg>
                    </span>
                    <p class="leading-1 ml-4 flex {{ Request::is('kelola-owner*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        Manage Owners
                    </p>
                </div>
            </a>
            @if(Request::is('kelola-owner*'))
                <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
            @endif
        </li>
        @endif

        <!-- Chat Analisis -->
        @if(auth()->user()->isOwner())
        <li class="relative mb-3 flex hover:cursor-pointer">
            <a href="{{ route('chat.index') }}" class="w-full">
                <div class="my-[3px] flex cursor-pointer items-center px-8">
                    <span class="{{ Request::is('chat-analisis') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M3.5 18.49l6-6.01 4 4L22 6.92l-1.41-1.41-7.09 7.97-4-4L2 16.99z"></path></svg>
                    </span>
                    <p class="leading-1 ml-4 flex {{ Request::is('chat-analisis') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        Chat Analisis
                    </p>
                </div>
            </a>
            @if(Request::is('chat-analisis'))
                <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
            @endif
        </li>
        @endif

        @if(auth()->user()->isOwner())
        <!-- POS Users (pos_pengguna) -->
        <li class="relative mb-3 flex hover:cursor-pointer">
            <a href="{{ route('pos-pengguna.index') }}" class="w-full">
                <div class="my-[3px] flex cursor-pointer items-center px-8">
                    <span class="{{ Request::is('pos-pengguna*') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></svg>
                    </span>
                    <p class="leading-1 ml-4 flex {{ Request::is('pos-pengguna*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        POS Users
                    </p>
                </div>
            </a>
            @if(Request::is('pos-pengguna*'))
                <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
            @endif
        </li>

        <!-- POS Roles (pos_role) -->
        <li class="relative mb-3 flex hover:cursor-pointer">
            <a href="{{ route('pos-role.index') }}" class="w-full">
                <div class="my-[3px] flex cursor-pointer items-center px-8">
                    <span class="{{ Request::is('pos-role*') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"></path></svg>
                    </span>
                    <p class="leading-1 ml-4 flex {{ Request::is('pos-role*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        User Roles
                    </p>
                </div>
            </a>
            @if(Request::is('pos-role*'))
                <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
            @endif
        </li>
        @endif

        @if(auth()->user()->isAdmin())
        <!-- POS Users for Admin (pos_pengguna) -->
        <li class="relative mb-3 flex hover:cursor-pointer">
            <a href="{{ route('pos-pengguna.index') }}" class="w-full">
                <div class="my-[3px] flex cursor-pointer items-center px-8">
                    <span class="{{ Request::is('pos-pengguna*') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></svg>
                    </span>
                    <p class="leading-1 ml-4 flex {{ Request::is('pos-pengguna*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        Users
                    </p>
                </div>
            </a>
            @if(Request::is('pos-pengguna*'))
                <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
            @endif
        </li>
        @endif

        @if(auth()->user()->isOwner())
        <!-- Stores (pos_toko) -->
        <li class="relative mb-3 flex hover:cursor-pointer">
            <a href="{{ route('toko.index') }}" class="w-full">
                <div class="my-[3px] flex cursor-pointer items-center px-8">
                    <span class="{{ Request::is('toko*') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M20 4H4v2h16V4zm1 10v-2l-1-5H4l-1 5v2h1v6h10v-6h4v6h2v-6h1zm-9 4H6v-4h6v4z"></path></svg>
                    </span>
                    <p class="leading-1 ml-4 flex {{ Request::is('toko*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        Stores
                    </p>
                </div>
            </a>
            @if(Request::is('toko*'))
                <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
            @endif
        </li>

        <!-- Products (pos_produk) Dropdown -->
        <li class="relative mb-3" x-data="{ open: {{ Request::is('produk*') || Request::is('pos-produk-merk*') || Request::is('produk-stok*') || Request::is('log-stok*') ? 'true' : 'false' }} }">
            <div class="flex hover:cursor-pointer" @click="open = !open">
                <div class="w-full">
                    <div class="my-[3px] flex cursor-pointer items-center px-8">
                        <span class="{{ Request::is('produk*') || Request::is('pos-produk-merk*') || Request::is('produk-stok*') || Request::is('log-stok*') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M20 6h-2.18c.11-.31.18-.65.18-1a2.996 2.996 0 00-5.5-1.65l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"></path></svg>
                        </span>
                        <p class="leading-1 ml-4 flex {{ Request::is('produk*') || Request::is('pos-produk-merk*') || Request::is('produk-stok*') || Request::is('log-stok*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                            Products
                        </p>
                        <span class="ml-auto mr-3 text-gray-600 transition-transform duration-200" :class="{ 'rotate-180': open }">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M7 10l5 5 5-5z"></path></svg>
                        </span>
                    </div>
                </div>
            </div>
            @if(Request::is('produk*') || Request::is('pos-produk-merk*') || Request::is('produk-stok*') || Request::is('log-stok*'))
                <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
            @endif
            <!-- Submenu -->
            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 transform scale-y-95" x-transition:enter-end="opacity-100 transform scale-y-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 transform scale-y-100" x-transition:leave-end="opacity-0 transform scale-y-95" class="overflow-hidden" @if(!Request::is('produk*') && !Request::is('pos-produk-merk*') && !Request::is('produk-stok*') && !Request::is('log-stok*')) style="display: none;" @endif>
                <ul class="my-[3px]">
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('produk.index') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('produk') && !Request::is('produk/create') && !Request::is('produk/*/edit') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    All Products
                                </p>
                            </div>
                        </a>
                    </li>
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('pos-produk-merk.index') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('pos-produk-merk*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    Product Name
                                </p>
                            </div>
                        </a>
                    </li>
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('produk-stok.index') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('produk-stok*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    Stock Management
                                </p>
                            </div>
                        </a>
                    </li>
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('log-stok.index') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('log-stok*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    Stock History
                                </p>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Suppliers (pos_supplier) -->
        <li class="relative mb-3 flex hover:cursor-pointer">
            <a href="{{ route('supplier.index') }}" class="w-full">
                <div class="my-[3px] flex cursor-pointer items-center px-8">
                    <span class="{{ Request::is('supplier*') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"></path></svg>
                    </span>
                    <p class="leading-1 ml-4 flex {{ Request::is('supplier*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        Suppliers
                    </p>
                </div>
            </a>
            @if(Request::is('supplier*'))
                <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
            @endif
        </li>

        <!-- Services (pos_service) -->
        <li class="relative mb-3 flex hover:cursor-pointer">
            <a href="{{ route('service.index') }}" class="w-full">
                <div class="my-[3px] flex cursor-pointer items-center px-8">
                    <span class="{{ Request::is('service*') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.4 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"></path></svg>
                    </span>
                    <p class="leading-1 ml-4 flex {{ Request::is('service*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        Services
                    </p>
                </div>
            </a>
            @if(Request::is('service*'))
                <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
            @endif
        </li>

        <!-- Customers (pos_pelanggan) -->
        <li class="relative mb-3 flex hover:cursor-pointer">
            <a href="{{ route('pelanggan.index') }}" class="w-full">
                <div class="my-[3px] flex cursor-pointer items-center px-8">
                    <span class="{{ Request::is('pelanggan*') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"></path></svg>
                    </span>
                    <p class="leading-1 ml-4 flex {{ Request::is('pelanggan*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        Customers
                    </p>
                </div>
            </a>
            @if(Request::is('pelanggan*'))
                <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
            @endif
        </li>
        @endif

        @if(auth()->user()->isOwner())
        <!-- Trade-In (pos_tukar_tambah) - OWNER ONLY -->
        <li class="relative mb-3 flex hover:cursor-pointer">
            <a href="{{ route('tukar-tambah.index') }}" class="w-full">
                <div class="my-[3px] flex cursor-pointer items-center px-8">
                    <span class="{{ Request::is('tukar-tambah*') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M7 11H1v2h6v-2zm2.17-3.24L7.05 5.64 5.64 7.05l2.12 2.12 1.41-1.41zM13 1h-2v6h2V1zm5.36 6.05l-1.41-1.41-2.12 2.12 1.41 1.41 2.12-2.12zM17 11v2h6v-2h-6zm-5-2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3zm2.83 7.24l2.12 2.12 1.41-1.41-2.12-2.12-1.41 1.41zm-9.19.71l1.41 1.41 2.12-2.12-1.41-1.41-2.12 2.12zM11 23h2v-6h-2v6z"></path></svg>
                    </span>
                    <p class="leading-1 ml-4 flex {{ Request::is('tukar-tambah*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        Trade-In
                    </p>
                </div>
            </a>
            @if(Request::is('tukar-tambah*'))
                <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
            @endif
        </li>
        @endif

        @if(auth()->user()->isOwner() || auth()->user()->isAdmin())
        <!-- Transactions Dropdown (pos_transaksi) -->
        <li class="relative mb-3" x-data="{ open: {{ Request::is('transaksi*') ? 'true' : 'false' }} }">
            <div class="flex hover:cursor-pointer" @click="open = !open">
                <div class="w-full">
                    <div class="my-[3px] flex cursor-pointer items-center px-8">
                        <span class="{{ Request::is('transaksi*') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"></path></svg>
                        </span>
                        <p class="leading-1 ml-4 flex {{ Request::is('transaksi*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                            Transactions
                        </p>
                        <span class="ml-auto mr-3 text-gray-600 transition-transform duration-200" :class="{ 'rotate-180': open }">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M7 10l5 5 5-5z"></path></svg>
                        </span>
                    </div>
                </div>
            </div>
            @if(Request::is('transaksi*'))
                <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
            @endif
            <!-- Submenu -->
            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 transform scale-y-95" x-transition:enter-end="opacity-100 transform scale-y-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 transform scale-y-100" x-transition:leave-end="opacity-0 transform scale-y-95" class="overflow-hidden" @if(!Request::is('transaksi*')) style="display: none;" @endif>
                <ul class="my-[3px]">
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('transaksi.masuk.index') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('transaksi/masuk*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    Incoming Transactions
                                </p>
                            </div>
                        </a>
                    </li>
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('transaksi.keluar.index') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('transaksi/keluar*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    Outgoing Transactions
                                </p>
                            </div>
                        </a>
                    </li>
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('transaksi.index') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('transaksi') && !Request::is('transaksi/masuk*') && !Request::is('transaksi/keluar*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    History Transaction
                                </p>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Reports Dropdown -->
        <li class="relative mb-3" x-data="{ open: {{ Request::is('reports*') || Request::is('laporan*') ? 'true' : 'false' }} }">
            <div class="flex hover:cursor-pointer" @click="open = !open">
                <div class="w-full">
                    <div class="my-[3px] flex cursor-pointer items-center px-8">
                        <span class="{{ Request::is('reports*') || Request::is('laporan*') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 2h6a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1zm0 2v16h6V4H9zM5 10H3v10a1 1 0 0 0 1 1h2v-2H5V10zm14 0v9h-1v2h2a1 1 0 0 0 1-1V10h-2z"></path>
                            </svg>
                        </span>
                        <p class="leading-1 ml-4 flex {{ Request::is('reports*') || Request::is('laporan*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                            Laporan
                        </p>
                        <span class="ml-auto mr-3 text-gray-600 transition-transform duration-200" :class="{ 'rotate-180': open }">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M7 10l5 5 5-5z"></path></svg>
                        </span>
                    </div>
                </div>
            </div>
            @if(Request::is('reports*') || Request::is('laporan*'))
                <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
            @endif
            <!-- Submenu -->
            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 transform scale-y-95" x-transition:enter-end="opacity-100 transform scale-y-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 transform scale-y-100" x-transition:leave-end="opacity-0 transform scale-y-95" class="overflow-hidden" @if(!Request::is('reports*') && !Request::is('laporan*')) style="display: none;" @endif>
                <ul class="my-[3px]">
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('reports.index') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('reports') || Request::is('laporan') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    Semua Laporan
                                </p>
                            </div>
                        </a>
                    </li>
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('reports.sales') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('reports/sales') || Request::is('laporan/penjualan') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    Laporan Penjualan
                                </p>
                            </div>
                        </a>
                    </li>
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('reports.trade-in') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('reports/trade-in') || Request::is('laporan/tukar-tambah') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    Laporan Tukar Tambah
                                </p>
                            </div>
                        </a>
                    </li>
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('reports.products') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('reports/products') || Request::is('laporan/produk') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    Laporan Produk
                                </p>
                            </div>
                        </a>
                    </li>
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('reports.stock') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('reports/stock') || Request::is('laporan/stok') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    Laporan Stok
                                </p>
                            </div>
                        </a>
                    </li>
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('reports.customers') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('reports/customers') || Request::is('laporan/pelanggan') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    Laporan Pelanggan
                                </p>
                            </div>
                        </a>
                    </li>
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('reports.financial') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('reports/financial') || Request::is('laporan/keuangan') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    Ringkasan Keuangan
                                </p>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        @endif

        @if(auth()->user()->isAdmin())
        <!-- Products Dropdown for Admin -->
        <li class="relative mb-3" x-data="{ open: {{ Request::is('produk*') ? 'true' : 'false' }} }">
            <div class="flex hover:cursor-pointer" @click="open = !open">
                <div class="w-full">
                    <div class="my-[3px] flex cursor-pointer items-center px-8">
                        <span class="{{ Request::is('produk*') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M20 6h-2.18c.11-.31.18-.65.18-1a2.996 2.996 0 00-5.5-1.65l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"></path></svg>
                        </span>
                        <p class="leading-1 ml-4 flex {{ Request::is('produk*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                            Products
                        </p>
                        <span class="ml-auto mr-3 text-gray-600 transition-transform duration-200" :class="{ 'rotate-180': open }">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M7 10l5 5 5-5z"></path></svg>
                        </span>
                    </div>
                </div>
            </div>
            @if(Request::is('produk*'))
                <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
            @endif
            <!-- Submenu -->
            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 transform scale-y-95" x-transition:enter-end="opacity-100 transform scale-y-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 transform scale-y-100" x-transition:leave-end="opacity-0 transform scale-y-95" class="overflow-hidden" @if(!Request::is('produk*')) style="display: none;" @endif>
                <ul class="my-[3px]">
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('produk.index') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('produk') && !Request::is('produk/create') && !Request::is('produk/*/edit') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    All Products
                                </p>
                            </div>
                        </a>
                    </li>
                    {{-- <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('produk.create') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('produk/create') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    New Product
                                </p>
                            </div>
                        </a>
                    </li> --}}
                </ul>
            </div>
        </li>
        @endif

        @if(auth()->user()->isSuperadmin())
        <!-- Services -->
        <li class="relative mb-3 flex hover:cursor-pointer">
            <a href="{{ route('layanan.index') }}" class="w-full">
                <div class="my-[3px] flex cursor-pointer items-center px-8">
                    <span class="{{ Request::is('layanan*') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600' }}">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"></path></svg>
                    </span>
                    <p class="leading-1 ml-4 flex {{ Request::is('layanan*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600' }}">
                        Services
                    </p>
                </div>
                @if(Request::is('layanan*'))
                    <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
                @endif
            </a>
        </li>

        <!-- Service Packages -->
        <li class="relative mb-3 flex hover:cursor-pointer">
            <a href="{{ route('paket-layanan.index') }}" class="w-full">
                <div class="my-[3px] flex cursor-pointer items-center px-8">
                    <span class="{{ Request::is('paket-layanan*') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600' }}">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"></path></svg>
                    </span>
                    <p class="leading-1 ml-4 flex {{ Request::is('paket-layanan*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600' }}">
                        Service Packages
                    </p>
                </div>
                @if(Request::is('paket-layanan*'))
                    <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
                @endif
            </a>
        </li>

        <!-- Payments -->
        <li class="relative mb-3 flex hover:cursor-pointer">
            <a href="{{ route('pembayaran.index') }}" class="w-full">
                <div class="my-[3px] flex cursor-pointer items-center px-8">
                    <span class="{{ Request::is('pembayaran*') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600' }}">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"></path></svg>
                    </span>
                    <p class="leading-1 ml-4 flex {{ Request::is('pembayaran*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600' }}">
                        Payments
                    </p>
                </div>
                @if(Request::is('pembayaran*'))
                    <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
                @endif
            </a>
        </li>
        @endif

        @if(auth()->user()->isSuperadmin())
        <!-- Data Master Dropdown -->
        <li class="relative mb-3" x-data="{ open: {{ Request::is('pos-penyimpanan*') || Request::is('pos-warna*') || Request::is('pos-ram*') ? 'true' : 'false' }} }">
            <div class="flex hover:cursor-pointer" @click="open = !open">
                <div class="w-full">
                    <div class="my-[3px] flex cursor-pointer items-center px-8">
                        <span class="{{ Request::is('pos-penyimpanan*') || Request::is('pos-warna*') || Request::is('pos-ram*') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path></svg>
                        </span>
                        <p class="leading-1 ml-4 flex {{ Request::is('pos-penyimpanan*') || Request::is('pos-warna*') || Request::is('pos-ram*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                            Data Master
                        </p>
                        <span class="ml-auto mr-3 text-gray-600 transition-transform duration-200" :class="{ 'rotate-180': open }">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M7 10l5 5 5-5z"></path></svg>
                        </span>
                    </div>
                </div>
            </div>
            @if(Request::is('pos-penyimpanan*') || Request::is('pos-warna*') || Request::is('pos-ram*'))
                <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
            @endif
            <!-- Submenu -->
            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 transform scale-y-95" x-transition:enter-end="opacity-100 transform scale-y-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 transform scale-y-100" x-transition:leave-end="opacity-0 transform scale-y-95" class="overflow-hidden" @if(!Request::is('pos-penyimpanan*') && !Request::is('pos-warna*') && !Request::is('pos-ram*')) style="display: none;" @endif>
                <ul class="my-[3px]">
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('pos-penyimpanan.index') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('pos-penyimpanan*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    Penyimpanan
                                </p>
                            </div>
                        </a>
                    </li>
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('pos-warna.index') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('pos-warna*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    Warna
                                </p>
                            </div>
                        </a>
                    </li>
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('pos-ram.index') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('pos-ram*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    RAM
                                </p>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Manage Profile Dropdown -->
        <li class="relative mb-3" x-data="{ open: {{ Request::is('manage-profil*') ? 'true' : 'false' }} }">
            <div class="flex hover:cursor-pointer" @click="open = !open">
                <div class="w-full">
                    <div class="my-[3px] flex cursor-pointer items-center px-8">
                        <span class="{{ Request::is('manage-profil*') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600' }}">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z"></path></svg>
                        </span>
                        <p class="leading-1 ml-4 flex {{ Request::is('manage-profil*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600' }}">
                            Manage Profile
                        </p>
                        <span class="ml-auto mr-3 text-gray-600 transition-transform duration-200" :class="{ 'rotate-180': open }">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M7 10l5 5 5-5z"></path></svg>
                        </span>
                    </div>
                </div>
            </div>
            @if(Request::is('manage-profil*'))
                <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
            @endif
            <!-- Submenu -->
            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 transform scale-y-95" x-transition:enter-end="opacity-100 transform scale-y-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 transform scale-y-100" x-transition:leave-end="opacity-0 transform scale-y-95" class="overflow-hidden" @if(!Request::is('manage-profil*')) style="display: none;" @endif>
                <ul class="my-[3px]">
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('manage-profil.contact-admin.index') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('manage-profil*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    Contact Admin
                                </p>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        @endif

    </ul>
    
</div>
