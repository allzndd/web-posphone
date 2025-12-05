<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no"
        name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') &mdash; MIPHONE GROUP</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Bootstrap for legacy compatibility -->
    <link rel="stylesheet" href="{{ asset('library/bootstrap/dist/css/bootstrap.min.css') }}">

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        'white': '#ffffff',
                        'lightPrimary': '#F4F7FE',
                        'blueSecondary': '#4318FF',
                        'brandLinear': '#868CFF',
                        'gray': {
                            50: '#f8f9fa',
                            100: '#edf2f7',
                            200: '#e9ecef',
                            300: '#cbd5e0',
                            400: '#a0aec0',
                            500: '#adb5bd',
                            600: '#343b4fff',
                            700: '#707eae',
                            800: '#252f40',
                            900: '#1b2559',
                        },
                        'navy': {
                            50: '#d0dcfb',
                            100: '#aac0fe',
                            200: '#a3b9f8',
                            300: '#728fea',
                            400: '#3652ba',
                            500: '#1b3bbb',
                            600: '#24388a',
                            700: '#1B254B',
                            800: '#111c44',
                            900: '#0b1437',
                        },
                        'brand': {
                            50: '#E9E3FF',
                            100: '#C0B8FE',
                            200: '#A195FD',
                            300: '#8171FC',
                            400: '#7551FF',
                            500: '#422AFB',
                            600: '#3311DB',
                            700: '#2111A5',
                            800: '#190793',
                            900: '#11047A',
                        },
                        'shadow': {
                            500: 'rgba(112, 144, 176, 0.08)',
                        },
                    },
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif'],
                        'dm': ['DM Sans', 'sans-serif'],
                    },
                    boxShadow: {
                        '3xl': '14px 17px 40px 4px',
                        'inset': 'inset 0px 18px 22px',
                        'darkinset': '0px 4px 4px inset',
                    },
                    borderRadius: {
                        'primary': '20px',
                    },
                }
            }
        }
    </script>

    <!-- Alpine.js for dropdowns and interactions -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'DM Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
            letter-spacing: -0.5px;
        }
        
        /* Remove underline from all links - Horizon style */
        a {
            text-decoration: none !important;
        }
        
        a:hover {
            text-decoration: none !important;
        }
        
        /* Alpine.js cloak - prevent flash before JS loads */
        [x-cloak] {
            display: none !important;
        }
        
        /* Prevent scrolling when sidebar is open on mobile */
        @media (max-width: 1279px) {
            body:has([x-data*="sidebarOpen: true"]) {
                overflow: hidden;
            }
        }
    </style>

    <!-- Dark Mode Script -->
    <script>
        // Initialize dark mode from localStorage
        if (localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        // Toggle dark mode function
        function toggleDarkMode() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('darkMode', 'false');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('darkMode', 'true');
            }
        }
    </script>

    @stack('style')

    <!-- Start GA -->
    <script async
        src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-94034622-3');
    </script>
    <!-- END GA -->
</head>

<body class="bg-white">
    <div id="app" class="flex h-full w-full" x-data="{ sidebarOpen: false }">
        <!-- Overlay untuk mobile/tablet -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak class="fixed inset-0 z-40 bg-gray-900 bg-opacity-50 xl:hidden" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
        
        <!-- Sidebar -->
        @include('components.sidebar')

        <!-- Navbar & Main Content -->
        <div class="h-full w-full bg-lightPrimary dark:!bg-navy-900">
            <!-- Main Content -->
            <main class="mx-[12px] h-full flex-none transition-all xl:ml-[325px] xl:mr-[12px]">
                <!-- Routes / Content -->
                <div class="h-full">
                    <!-- Navbar -->
                    @include('components.header')
                    
                    <!-- Page Content -->
                    <div class="pt-5s mx-auto mb-auto h-full min-h-[84vh] p-2">
                        @yield('main')
                    </div>
                    
                    <!-- Footer -->
                    <div class="p-3">
                        @include('components.footer')
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- General JS Scripts -->
    <script src="{{ asset('library/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('library/popper.js/dist/umd/popper.js') }}"></script>
    <script src="{{ asset('library/tooltip.js/dist/umd/tooltip.js') }}"></script>
    <script src="{{ asset('library/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('library/jquery.nicescroll/dist/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('library/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('js/stisla.js') }}"></script>
    <!-- SweetAlert2 for confirmations -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')

    <!-- Template JS File -->
    <script src="{{ asset('js/scripts.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>

    <script>
        // Global confirm before delete using SweetAlert2
        document.addEventListener('DOMContentLoaded', function () {
            $(document).on('click', '.confirm-delete', function (e) {
                e.preventDefault();
                const $btn = $(this);
                const $form = $btn.closest('form');

                Swal.fire({
                    title: 'Hapus data?',
                    text: 'Tindakan ini tidak dapat dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-check"></i> Hapus',
                    cancelButtonText: '<i class="fas fa-times"></i> Batal',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'btn btn-danger ml-2',
                        cancelButton: 'btn btn-secondary mr-2'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        $form.trigger('submit');
                    }
                });
            });
        });
    </script>
</body>

</html>
