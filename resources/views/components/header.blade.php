<!-- Navbar - 100% Horizon Template -->
<nav class="sticky top-4 z-40 flex flex-row flex-wrap items-center justify-between rounded-xl bg-white/10 p-2 backdrop-blur-xl dark:bg-[#0b14374d]">
    <!-- Mobile menu button -->
    <span class="flex cursor-pointer text-xl text-gray-600 dark:text-white xl:hidden" @click="sidebarOpen = !sidebarOpen">
        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"></path></svg>
    </span>
    
    <!-- Left: Breadcrumb & Title -->
    <div class="ml-[6px] hidden xl:block">
        <div class="h-6 w-[224px] pt-1">
            <a class="text-sm font-normal text-navy-700 dark:text-white dark:hover:text-white" href="{{ route('dashboard') }}">
                Pages
                <span class="mx-1 text-sm text-navy-700 hover:text-navy-700 dark:text-white"> / </span>
            </a>
            <a class="text-sm font-normal capitalize text-navy-700 dark:text-white dark:hover:text-white" href="#">
                @yield('title', 'Dashboard')
            </a>
        </div>
        <p class="shrink text-[33px] capitalize text-navy-700 dark:text-white">
            <a href="#" class="font-bold capitalize hover:text-navy-700 dark:hover:text-white">
                @yield('title', 'Dashboard')
            </a>
        </p>
    </div>

    <!-- Right: Search & Icons & Profile -->
    <div class="relative mt-[3px] flex h-[61px] w-full flex-grow items-center justify-around gap-2 rounded-full bg-white px-2 py-2 shadow-xl shadow-shadow-500 dark:!bg-navy-800 dark:shadow-none md:w-[365px] md:flex-grow-0 md:gap-1 xl:w-[365px] xl:gap-2 xl:ml-auto">
        
        <!-- Search -->
        <div class="hidden sm:flex h-full items-center rounded-full bg-lightPrimary text-navy-700 dark:bg-navy-900 dark:text-white xl:w-[225px]">
            <p class="pl-3 pr-2 text-xl">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" class="h-4 w-4 text-gray-400 dark:text-white" xmlns="http://www.w3.org/2000/svg"><path d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"></path></svg>
            </p>
            <input type="text" placeholder="Search..." class="block h-full w-full rounded-full bg-lightPrimary text-sm font-medium text-navy-700 outline-none placeholder:!text-gray-400 dark:bg-navy-900 dark:text-white dark:placeholder:!text-white sm:w-fit" />
        </div>

        <!-- Notification Icon -->
        <div x-data="{ open: false }" class="relative cursor-pointer">
            <p @click="open = !open" class="cursor-pointer">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4 text-gray-600 dark:text-white" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"></path></svg>
            </p>
            
            <!-- Notification Dropdown -->
            <div x-show="open" @click.away="open = false" style="display: none;"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                class="absolute -left-[230px] top-11 z-50 flex w-[360px] flex-col gap-3 rounded-[20px] bg-white p-4 shadow-xl shadow-shadow-500 dark:!bg-navy-700 dark:text-white dark:shadow-none sm:w-[460px] md:-left-[440px]">
                <div class="flex items-center justify-between">
                    <p class="text-base font-bold text-navy-700 dark:text-white">Notification</p>
                    <p class="text-sm font-bold text-navy-700 dark:text-white">Mark all read</p>
                </div>
                
                <button class="flex w-full items-center">
                    <div class="flex h-full w-[85px] items-center justify-center rounded-xl bg-gradient-to-b from-brandLinear to-brand-500 py-4 text-2xl text-white">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 16 16" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 12a.5.5 0 00.5-.5V5.707l2.146 2.147a.5.5 0 00.708-.708l-3-3a.5.5 0 00-.708 0l-3 3a.5.5 0 10.708.708L7.5 5.707V11.5a.5.5 0 00.5.5z" clip-rule="evenodd"></path></svg>
                    </div>
                    <div class="ml-2 flex h-full w-full flex-col justify-center rounded-lg px-1 text-sm">
                        <p class="mb-1 text-left text-base font-bold text-gray-900 dark:text-white">
                            New Update Available
                        </p>
                        <p class="font-base text-left text-xs text-gray-900 dark:text-white">
                            A new update for your system is available!
                        </p>
                    </div>
                </button>
            </div>
        </div>

        <!-- Info Icon -->
        <div x-data="{ open: false }" class="relative cursor-pointer">
            <p @click="open = !open" class="cursor-pointer">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-4 w-4 text-gray-600 dark:text-white" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"></path></svg>
            </p>
            
            <!-- Info Dropdown -->
            <div x-show="open" @click.away="open = false" style="display: none;"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                class="absolute right-0 top-11 z-50 flex w-[350px] flex-col gap-2 rounded-[20px] bg-white p-4 shadow-xl shadow-shadow-500 dark:!bg-navy-700 dark:text-white dark:shadow-none">
                <div class="mb-2 aspect-video w-full rounded-lg bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center">
                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" class="h-16 w-16 text-white opacity-50" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"></path></svg>
                </div>
                <a href="#" class="px-full linear flex cursor-pointer items-center justify-center rounded-xl bg-brand-500 py-[11px] font-bold text-white transition duration-200 hover:bg-brand-600 hover:text-white active:bg-brand-700 dark:bg-brand-400 dark:hover:bg-brand-300 dark:active:bg-brand-200">
                    Learn More
                </a>
                <a href="#" class="px-full linear flex cursor-pointer items-center justify-center rounded-xl border py-[11px] font-bold text-navy-700 transition duration-200 hover:bg-gray-200 hover:text-navy-700 dark:!border-white/10 dark:text-white dark:hover:bg-white/20 dark:hover:text-white dark:active:bg-white/10">
                    Documentation
                </a>
                <a href="https://horizon-ui.com" target="_blank" class="hover:text-navy-700 text-gray-600 px-full linear flex cursor-pointer items-center justify-center rounded-xl py-[11px] text-sm font-medium transition duration-200 dark:text-white dark:hover:text-white">
                    Try Horizon Free!
                </a>
            </div>
        </div>

        <!-- Theme Toggle (Moon/Sun) -->
        <div class="cursor-pointer text-gray-600" onclick="toggleDarkMode()">
            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" class="h-4 w-4 text-gray-600 dark:text-white" xmlns="http://www.w3.org/2000/svg"><path d="M283.211 512c78.962 0 151.079-35.925 198.857-94.792 7.068-8.708-.639-21.43-11.562-19.35-124.203 23.654-238.262-71.576-238.262-196.954 0-72.222 38.662-138.635 101.498-174.394 9.686-5.512 7.25-20.197-3.756-22.23A258.156 258.156 0 00283.211 0c-141.309 0-256 114.511-256 256 0 141.309 114.511 256 256 256z"></path></svg>
        </div>

        <!-- Profile Dropdown -->
        <div x-data="{ open: false }" class="relative cursor-pointer">
            <img @click="open = !open" class="h-10 w-10 rounded-full cursor-pointer" src="{{ asset('img/avatar/avatar-1.png') }}" alt="{{ auth()->user()->name }}">
            
            <!-- Profile Dropdown Menu -->
            <div x-show="open" @click.away="open = false" style="display: none;"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                class="absolute right-0 top-11 z-50 w-max rounded-xl bg-white shadow-xl dark:!bg-navy-700 dark:shadow-none">
                <div class="flex w-56 flex-col justify-start rounded-[20px] bg-white bg-cover bg-no-repeat shadow-xl shadow-shadow-500 dark:!bg-navy-700 dark:text-white dark:shadow-none">
                    <div class="p-4">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-bold text-navy-700 dark:text-white">👋 Hey, {{ auth()->user()->name }}</p>
                        </div>
                    </div>
                    <div class="h-px w-full bg-gray-200 dark:bg-white/20"></div>
                    
                    <div class="flex flex-col p-4">
                        <a href="#" class="text-sm text-gray-800 dark:text-white hover:dark:text-white">
                            Profile Settings
                        </a>
                        @if(auth()->user()->isOwner())
                        <a href="{{ route('settings.index') }}" class="mt-3 text-sm text-gray-800 dark:text-white hover:dark:text-white">
                            Currency Settings
                        </a>
                        @endif
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="mt-3 text-sm font-medium text-red-500 hover:text-red-500 transition duration-150 ease-out hover:ease-in">
                            Log Out
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>
</nav>
