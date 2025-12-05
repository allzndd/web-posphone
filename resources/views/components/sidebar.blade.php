<!-- Sidebar Component - 100% Horizon Template -->
<div id="sidebar" class="sm:none duration-175 linear fixed !z-50 flex min-h-full flex-col bg-white pb-10 shadow-2xl shadow-white/5 transition-all dark:!bg-navy-800 dark:text-white md:!z-50 lg:!z-50 xl:!z-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-96 xl:translate-x-0'">
    
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
        
        <!-- Dashboard -->
        <li class="relative mb-3 flex hover:cursor-pointer">
            <a href="{{ route('home') }}" class="w-full">
                <div class="my-[3px] flex cursor-pointer items-center px-8">
                    <span class="{{ Request::is('home') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"></path></svg>
                    </span>
                    <p class="leading-1 ml-4 flex {{ Request::is('home') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        Dashboard
                    </p>
                </div>
            </a>
            @if(Request::is('home'))
                <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
            @endif
        </li>

        @if(auth()->user()->isOwner())
        <!-- Chat Analisis -->
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

        <!-- Users -->
        <li class="relative mb-3 flex hover:cursor-pointer">
            <a href="{{ route('user.index') }}" class="w-full">
                <div class="my-[3px] flex cursor-pointer items-center px-8">
                    <span class="{{ Request::is('user*') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></svg>
                    </span>
                    <p class="leading-1 ml-4 flex {{ Request::is('user*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        Users
                    </p>
                </div>
            </a>
            @if(Request::is('user*'))
                <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
            @endif
        </li>

        <!-- Products Dropdown with Accordion -->
        <li class="relative mb-3" x-data="{ open: {{ Request::is('product*') || Request::is('storages*') || Request::is('colors*') || Request::is('product-name*') ? 'true' : 'false' }} }">
            <div class="flex hover:cursor-pointer" @click="open = !open">
                <div class="w-full">
                    <div class="my-[3px] flex cursor-pointer items-center px-8">
                        <span class="{{ Request::is('product*') || Request::is('storages*') || Request::is('colors*') || Request::is('product-name*') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M20 6h-2.18c.11-.31.18-.65.18-1a2.996 2.996 0 00-5.5-1.65l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"></path></svg>
                        </span>
                        <p class="leading-1 ml-4 flex {{ Request::is('product*') || Request::is('storages*') || Request::is('colors*') || Request::is('product-name*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                            Products
                        </p>
                        <span class="ml-auto mr-3 text-gray-600 transition-transform duration-200" :class="{ 'rotate-180': open }">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M7 10l5 5 5-5z"></path></svg>
                        </span>
                    </div>
                </div>
            </div>
            @if(Request::is('product*') || Request::is('storages*') || Request::is('colors*') || Request::is('product-name*'))
                <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
            @endif
            <!-- Submenu -->
            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 transform scale-y-95" x-transition:enter-end="opacity-100 transform scale-y-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 transform scale-y-100" x-transition:leave-end="opacity-0 transform scale-y-95" class="overflow-hidden" @if(!Request::is('product*') && !Request::is('storages*') && !Request::is('colors*') && !Request::is('product-name*')) style="display: none;" @endif>
                <ul class="my-[3px]">
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('product.index') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('product') && !Request::is('product/create') && !Request::is('product/*/edit') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    All Products
                                </p>
                            </div>
                        </a>
                    </li>
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('product.create') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('product/create') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    New Product
                                </p>
                            </div>
                        </a>
                    </li>
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('product-name.index') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('product-name*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    Nama Produk
                                </p>
                            </div>
                        </a>
                    </li>
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('storages.index') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('storages*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    Storage
                                </p>
                            </div>
                        </a>
                    </li>
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('colors.index') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('colors*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    Colors
                                </p>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Categories -->
        <li class="relative mb-3 flex hover:cursor-pointer">
            <a href="{{ route('category.index') }}" class="w-full">
                <div class="my-[3px] flex cursor-pointer items-center px-8">
                    <span class="{{ Request::is('category*') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M17.63 5.84C17.27 5.33 16.67 5 16 5L5 5.01C3.9 5.01 3 5.9 3 7v10c0 1.1.9 1.99 2 1.99L16 19c.67 0 1.27-.33 1.63-.84L22 12l-4.37-6.16z"></path></svg>
                    </span>
                    <p class="leading-1 ml-4 flex {{ Request::is('category*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        Categories
                    </p>
                </div>
            </a>
            @if(Request::is('category*'))
                <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
            @endif
        </li>

        <!-- Trade In -->
        <li class="relative mb-3 flex hover:cursor-pointer">
            <a href="{{ route('tradein.index') }}" class="w-full">
                <div class="my-[3px] flex cursor-pointer items-center px-8">
                    <span class="{{ Request::is('tradein*') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M6.99 11L3 15l3.99 4v-3H14v-2H6.99v-3zM21 9l-3.99-4v3H10v2h7.01v3L21 9z"></path></svg>
                    </span>
                    <p class="leading-1 ml-4 flex {{ Request::is('tradein*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                        Trade In
                    </p>
                </div>
            </a>
            @if(Request::is('tradein*'))
                <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
            @endif
        </li>
        @endif

        <!-- Transactions Dropdown with Accordion -->
        <li class="relative mb-3" x-data="{ open: {{ Request::is('transaction*') ? 'true' : 'false' }} }">
            <div class="flex hover:cursor-pointer" @click="open = !open">
                <div class="w-full">
                    <div class="my-[3px] flex cursor-pointer items-center px-8">
                        <span class="{{ Request::is('transaction*') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"></path></svg>
                        </span>
                        <p class="leading-1 ml-4 flex {{ Request::is('transaction*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                            Transactions
                        </p>
                        <span class="ml-auto mr-3 text-gray-600 transition-transform duration-200" :class="{ 'rotate-180': open }">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M7 10l5 5 5-5z"></path></svg>
                        </span>
                    </div>
                </div>
            </div>
            @if(Request::is('transaction*'))
                <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
            @endif
            <!-- Submenu -->
            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 transform scale-y-95" x-transition:enter-end="opacity-100 transform scale-y-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 transform scale-y-100" x-transition:leave-end="opacity-0 transform scale-y-95" class="overflow-hidden" @if(!Request::is('transaction*')) style="display: none;" @endif>
                <ul class="my-[3px]">
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('transaction.index') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('transaction') && !Request::is('transaction/create') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    All Transactions
                                </p>
                            </div>
                        </a>
                    </li>
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('transaction.create') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('transaction/create') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    New Transaction
                                </p>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        @if(auth()->user()->isAdmin())
        <!-- Products Dropdown for Admin with Accordion -->
        <li class="relative mb-3" x-data="{ open: {{ Request::is('product*') ? 'true' : 'false' }} }">
            <div class="flex hover:cursor-pointer" @click="open = !open">
                <div class="w-full">
                    <div class="my-[3px] flex cursor-pointer items-center px-8">
                        <span class="{{ Request::is('product*') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M20 6h-2.18c.11-.31.18-.65.18-1a2.996 2.996 0 00-5.5-1.65l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"></path></svg>
                        </span>
                        <p class="leading-1 ml-4 flex {{ Request::is('product*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                            Products
                        </p>
                        <span class="ml-auto mr-3 text-gray-600 transition-transform duration-200" :class="{ 'rotate-180': open }">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M7 10l5 5 5-5z"></path></svg>
                        </span>
                    </div>
                </div>
            </div>
            @if(Request::is('product*'))
                <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
            @endif
            <!-- Submenu -->
            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 transform scale-y-95" x-transition:enter-end="opacity-100 transform scale-y-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 transform scale-y-100" x-transition:leave-end="opacity-0 transform scale-y-95" class="overflow-hidden" @if(!Request::is('product*')) style="display: none;" @endif>
                <ul class="my-[3px]">
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('product.index') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('product') && !Request::is('product/create') && !Request::is('product/*/edit') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    All Products
                                </p>
                            </div>
                        </a>
                    </li>
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('product.create') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('product/create') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    New Product
                                </p>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        @endif

        <!-- Customers Dropdown with Accordion -->
        <li class="relative mb-3" x-data="{ open: {{ Request::is('customer*') ? 'true' : 'false' }} }">
            <div class="flex hover:cursor-pointer" @click="open = !open">
                <div class="w-full">
                    <div class="my-[3px] flex cursor-pointer items-center px-8">
                        <span class="{{ Request::is('customer*') ? 'font-bold text-brand-500 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"></path></svg>
                        </span>
                        <p class="leading-1 ml-4 flex {{ Request::is('customer*') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                            Customers
                        </p>
                        <span class="ml-auto mr-3 text-gray-600 transition-transform duration-200" :class="{ 'rotate-180': open }">
                            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M7 10l5 5 5-5z"></path></svg>
                        </span>
                    </div>
                </div>
            </div>
            @if(Request::is('customer*'))
                <div class="absolute right-0 top-px h-9 w-1 rounded-lg bg-brand-500 dark:bg-brand-400"></div>
            @endif
            <!-- Submenu -->
            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 transform scale-y-95" x-transition:enter-end="opacity-100 transform scale-y-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 transform scale-y-100" x-transition:leave-end="opacity-0 transform scale-y-95" class="overflow-hidden" @if(!Request::is('customer*')) style="display: none;" @endif>
                <ul class="my-[3px]">
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('customer.index') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('customer') && !Request::is('customer/create') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    All Customers
                                </p>
                            </div>
                        </a>
                    </li>
                    <li class="relative mb-2 flex hover:cursor-pointer">
                        <a href="{{ route('customer.create') }}" class="w-full">
                            <div class="my-[3px] flex cursor-pointer items-center py-2 pl-[60px] pr-8">
                                <p class="leading-1 flex text-sm {{ Request::is('customer/create') ? 'font-bold text-navy-700 dark:text-white' : 'font-medium text-gray-600 dark:text-gray-400' }}">
                                    New Customer
                                </p>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </li>

    </ul>
    
</div>
