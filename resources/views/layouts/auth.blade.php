<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <title>@yield('title') &mdash; M iPhone Group</title>

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
                    
                    <!-- Right Side Image -->
                    <div class="absolute right-0 hidden h-full min-h-screen md:block lg:w-[49vw] 2xl:w-[44vw]">
                        <div class="absolute flex h-full w-full items-center justify-center bg-brand-500 bg-cover bg-center lg:rounded-bl-[120px] xl:rounded-bl-[200px]"
                             style="background-image: url('{{ asset('img/logo-miphone-2.png') }}'); background-size: contain; background-repeat: no-repeat; background-position: center;">
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    @stack('scripts')

</body>
</html>