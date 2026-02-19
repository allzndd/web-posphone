<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <title>@yield('title') &mdash; M iPhone Group</title>
    <link rel="icon" href="{{ asset('img/favicon2.jpg') }}" type="image/jpeg">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'navy': {
                            700: '#1B254B',
                            800: '#111c44',
                            900: '#0b1437',
                        },
                        'brand': {
                            400: '#7551FF',
                            500: '#422AFB',
                            600: '#3311DB',
                            700: '#2111A5',
                        },
                        'lightPrimary': '#F4F7FE',
                    },
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif'],
                        'dm': ['DM Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'DM Sans', sans-serif;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(66, 42, 251, 0.3); }
            50% { box-shadow: 0 0 40px rgba(66, 42, 251, 0.6); }
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        .animate-float-delay {
            animation: float 8s ease-in-out infinite;
            animation-delay: 1s;
        }
    </style>

    @stack('style')
</head>

<body class="bg-white min-h-screen">

    <div class="relative min-h-screen w-full bg-white">
        <main class="mx-auto min-h-screen">
            <div class="relative flex">
                <div class="mx-auto flex min-h-screen w-full flex-col justify-start pt-12 md:max-w-[75%] lg:max-w-[1013px] lg:px-8 lg:pt-0 xl:max-w-[1383px] xl:px-0 xl:pl-[70px]">
                    <div class="mb-auto flex flex-col pl-5 pr-5 md:pr-0 md:pl-12 lg:max-w-[48%] lg:pl-0 xl:max-w-full">
                        <!-- Content Login/Register -->
                        @yield('main')
                    </div>
                    
                    <!-- Right Side Background -->
                    <div class="absolute right-0 hidden h-full min-h-screen md:block lg:w-[49vw] 2xl:w-[44vw]">
                        <div class="absolute flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-400 via-brand-500 to-brand-700 lg:rounded-bl-[120px] xl:rounded-bl-[200px] overflow-hidden">
                            <!-- Decorative Elements -->
                            <div class="absolute top-20 right-20 w-32 h-32 bg-white/10 rounded-full blur-xl animate-float"></div>
                            <div class="absolute bottom-32 left-16 w-40 h-40 bg-white/10 rounded-full blur-2xl animate-float-delay"></div>
                            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
                            
                            <!-- Central Content -->
                            <div class="relative z-10 text-center px-12">
                                <div class="mb-8">
                                    <div class="inline-flex items-center justify-center w-24 h-24 bg-white/20 backdrop-blur-sm rounded-3xl mb-6 animate-pulse">
                                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <h2 class="text-4xl font-bold text-white mb-4">Welcome to PosPhone</h2>
                                <p class="text-white/80 text-lg max-w-md mx-auto leading-relaxed">
                                    Enterprise-grade Point of Sales solution designed for smartphone retail businesses. Streamline operations with powerful tools and analytics.
                                </p>
                                
                                <!-- Feature Pills -->
                                <div class="mt-8 flex flex-wrap gap-3 justify-center">
                                    <div class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-white text-sm font-medium">
                                        Modern Interface
                                    </div>
                                    <div class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-white text-sm font-medium">
                                        Enterprise Security
                                    </div>
                                    <div class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-white text-sm font-medium">
                                        Advanced Analytics
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    @stack('scripts')

</body>
</html>