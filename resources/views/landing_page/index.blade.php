<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="PosPhone - Solusi Point of Sales modern untuk toko smartphone. Kelola penjualan, inventori, dan pelanggan dengan mudah. Subscription fleksibel dan dapat dikustomisasi.">
    <title>PosPhone - Solusi POS Modern untuk Toko Smartphone Anda</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        
        /* Liquid Animations - Enhanced */
        @keyframes liquid {
            0%, 100% { 
                transform: translate(-10%, -10%) scale(1) rotate(0deg);
                opacity: 0.8;
                filter: hue-rotate(0deg);
            }
            33% { 
                transform: translate(15%, 5%) scale(1.3) rotate(120deg);
                opacity: 0.6;
                filter: hue-rotate(45deg);
            }
            66% { 
                transform: translate(-5%, 15%) scale(1.1) rotate(240deg);
                opacity: 0.7;
                filter: hue-rotate(90deg);
            }
        }
        
        @keyframes liquidAlt {
            0%, 100% { 
                transform: translate(10%, 10%) scale(1.1) rotate(0deg);
                opacity: 0.7;
                filter: hue-rotate(0deg);
            }
            33% { 
                transform: translate(-15%, -5%) scale(1.2) rotate(-120deg);
                opacity: 0.8;
                filter: hue-rotate(-45deg);
            }
            66% { 
                transform: translate(5%, -15%) scale(1.4) rotate(-240deg);
                opacity: 0.5;
                filter: hue-rotate(-90deg);
            }
        }
        
        /* Floating Particles */
        @keyframes float {
            0%, 100% { 
                transform: translateY(0px) translateX(0px) rotate(0deg);
            }
            25% { 
                transform: translateY(-20px) translateX(10px) rotate(90deg);
            }
            50% { 
                transform: translateY(-30px) translateX(-10px) rotate(180deg);
            }
            75% { 
                transform: translateY(-20px) translateX(10px) rotate(270deg);
            }
        }
        
        @keyframes floatReverse {
            0%, 100% { 
                transform: translateY(0px) translateX(0px) scale(1);
            }
            50% { 
                transform: translateY(30px) translateX(20px) scale(1.5);
            }
        }
        
        /* Pulse & Glow Effects */
        @keyframes glow {
            0%, 100% { 
                opacity: 0.5;
                box-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
            }
            50% { 
                opacity: 1;
                box-shadow: 0 0 40px rgba(59, 130, 246, 1);
            }
        }
        
        @keyframes glowLine {
            0% { 
                transform: translateX(-100%);
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% { 
                transform: translateX(100%);
                opacity: 0;
            }
        }
        
        /* Spin & Orbit */
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        @keyframes spinSlow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        @keyframes orbit {
            0% {
                transform: rotate(0deg) translateX(100px) rotate(0deg);
            }
            100% {
                transform: rotate(360deg) translateX(100px) rotate(-360deg);
            }
        }
        
        /* Text Reveal & Glitch */
        @keyframes textReveal {
            0% {
                transform: translateY(100px);
                opacity: 0;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        @keyframes glitch {
            0% {
                text-shadow: 0.05em 0 0 rgba(255, 0, 0, 0.75),
                            -0.025em -0.05em 0 rgba(0, 255, 0, 0.75),
                            0.025em 0.05em 0 rgba(0, 0, 255, 0.75);
            }
            14% {
                text-shadow: 0.05em 0 0 rgba(255, 0, 0, 0.75),
                            -0.025em -0.05em 0 rgba(0, 255, 0, 0.75),
                            0.025em 0.05em 0 rgba(0, 0, 255, 0.75);
            }
            15% {
                text-shadow: -0.05em -0.025em 0 rgba(255, 0, 0, 0.75),
                            0.025em 0.025em 0 rgba(0, 255, 0, 0.75),
                            -0.05em -0.05em 0 rgba(0, 0, 255, 0.75);
            }
            49% {
                text-shadow: -0.05em -0.025em 0 rgba(255, 0, 0, 0.75),
                            0.025em 0.025em 0 rgba(0, 255, 0, 0.75),
                            -0.05em -0.05em 0 rgba(0, 0, 255, 0.75);
            }
            50% {
                text-shadow: 0.025em 0.05em 0 rgba(255, 0, 0, 0.75),
                            0.05em 0 0 rgba(0, 255, 0, 0.75),
                            0 -0.05em 0 rgba(0, 0, 255, 0.75);
            }
            99% {
                text-shadow: 0.025em 0.05em 0 rgba(255, 0, 0, 0.75),
                            0.05em 0 0 rgba(0, 255, 0, 0.75),
                            0 -0.05em 0 rgba(0, 0, 255, 0.75);
            }
            100% {
                text-shadow: -0.025em 0 0 rgba(255, 0, 0, 0.75),
                            -0.025em -0.025em 0 rgba(0, 255, 0, 0.75),
                            -0.025em -0.05em 0 rgba(0, 0, 255, 0.75);
            }
        }
        
        /* Gradient Shift */
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        /* Scale Pulse */
        @keyframes scalePulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        @keyframes scaleIn {
            0% { transform: scale(0) rotate(0deg); opacity: 0; }
            100% { transform: scale(1) rotate(360deg); opacity: 1; }
        }
        
        /* Wiggle Effect */
        @keyframes wiggle {
            0%, 100% { transform: rotate(-3deg); }
            50% { transform: rotate(3deg); }
        }
        
        /* Wave Animation */
        @keyframes wave {
            0% { transform: translateY(0px) translateX(0px); }
            25% { transform: translateY(-10px) translateX(10px); }
            50% { transform: translateY(0px) translateX(20px); }
            75% { transform: translateY(10px) translateX(10px); }
            100% { transform: translateY(0px) translateX(0px); }
        }
        
        /* Slide Animations */
        @keyframes slideInLeft {
            0% { transform: translateX(-100px); opacity: 0; }
            100% { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes slideInRight {
            0% { transform: translateX(100px); opacity: 0; }
            100% { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes slideInUp {
            0% { transform: translateY(100px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
        
        /* Classes */
        .animate-liquid {
            animation: liquid 25s ease-in-out infinite;
        }
        
        .animate-liquid-alt {
            animation: liquidAlt 30s ease-in-out infinite;
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        .animate-float-reverse {
            animation: floatReverse 5s ease-in-out infinite;
        }
        
        .animate-glow {
            animation: glow 2s ease-in-out infinite;
        }
        
        .animate-spin-slow {
            animation: spinSlow 20s linear infinite;
        }
        
        .animate-spin-slower {
            animation: spinSlow 30s linear infinite;
        }
        
        .animate-orbit {
            animation: orbit 15s linear infinite;
        }
        
        .animate-gradient {
            background-size: 200% 200%;
            animation: gradientShift 8s ease infinite;
        }
        
        .animate-pulse-scale {
            animation: scalePulse 3s ease-in-out infinite;
        }
        
        .animate-wiggle {
            animation: wiggle 1s ease-in-out infinite;
        }
        
        .animate-wave {
            animation: wave 4s ease-in-out infinite;
        }
        
        .animate-glow-line {
            animation: glowLine 3s ease-in-out infinite;
        }
        
        .glass-effect {
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
        }
        
        .text-shadow-glow {
            text-shadow: 0 0 40px rgba(59, 130, 246, 0.5);
        }
        
        .hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .hover-lift:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 60px rgba(59, 130, 246, 0.3);
        }
        
        /* Mouse follower */
        .cursor-glow {
            pointer-events: none;
            position: fixed;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, transparent 70%);
            transform: translate(-50%, -50%);
            z-index: 9999;
            transition: opacity 0.3s ease;
        }
        
        /* Parallax scroll effect */
        .parallax {
            transition: transform 0.1s ease-out;
        }
    </style>
</head>
<body class="bg-[#0A1A3D] text-white overflow-x-hidden">
    
    <!-- Custom Cursor Glow Effect -->
    <div class="cursor-glow" id="cursorGlow"></div>
    
    <!-- Liquid Blur Background Animation - Hero Only -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none -z-10" style="height: 100vh;">
        <!-- Blob 1 -->
        <div class="absolute top-0 left-0 w-[600px] h-[600px] bg-gradient-to-br from-blue-500 to-blue-600 rounded-full blur-3xl opacity-70 animate-liquid"></div>
        
        <!-- Blob 2 -->
        <div class="absolute top-1/2 right-0 w-[700px] h-[700px] bg-gradient-to-tr from-white to-blue-300 rounded-full blur-3xl opacity-50 animate-liquid-alt"></div>
        
        <!-- Blob 3 -->
        <div class="absolute bottom-0 left-1/3 w-[500px] h-[500px] bg-gradient-to-t from-[#0f172a] to-blue-400 rounded-full blur-3xl opacity-60 animate-liquid"></div>
        
        <!-- Overlay Gradient -->
        <div class="absolute inset-0 bg-gradient-to-b from-[#0A1A3D]/80 via-[#0A1A3D]/50 to-[#0A1A3D]/90"></div>
        
        <!-- Animated Grid Pattern -->
        <div class="absolute inset-0 opacity-10" style="background-image: linear-gradient(rgba(59, 130, 246, 0.3) 1px, transparent 1px), linear-gradient(90deg, rgba(59, 130, 246, 0.3) 1px, transparent 1px); background-size: 50px 50px;"></div>
    </div>
    
    <!-- Floating Geometric Shapes -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <!-- Spinning Rings -->
        <div class="absolute top-20 left-20 w-32 h-32 border-2 border-blue-500/20 rounded-full animate-spin-slow"></div>
        <div class="absolute top-24 left-24 w-24 h-24 border-2 border-blue-400/30 rounded-full animate-spin-slower" style="animation-direction: reverse;"></div>
        
        <div class="absolute bottom-40 right-32 w-40 h-40 border-2 border-white/10 rounded-full animate-spin-slow"></div>
        <div class="absolute bottom-44 right-36 w-32 h-32 border-2 border-blue-300/20 rounded-full animate-spin-slower" style="animation-direction: reverse;"></div>
        
        <!-- Floating Particles with Various Animations -->
        <div class="absolute top-1/4 left-1/4 w-2 h-2 bg-blue-400 rounded-full animate-float-reverse"></div>
        <div class="absolute top-1/3 right-1/3 w-3 h-3 bg-cyan-400 rounded-full animate-wave" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-1/4 left-1/2 w-2 h-2 bg-white rounded-full animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute top-2/3 right-1/4 w-3 h-3 bg-blue-500 rounded-full animate-float-reverse" style="animation-delay: 1.5s;"></div>
        
        <!-- Hexagons -->
        <div class="absolute top-1/2 left-10 w-16 h-16 border border-blue-500/20 rotate-45 animate-pulse-scale"></div>
        <div class="absolute bottom-1/3 right-20 w-20 h-20 border border-cyan-400/20 rotate-12 animate-wiggle"></div>
        
        <!-- Glowing Lines -->
        <div class="absolute top-1/3 left-0 w-full h-px overflow-hidden">
            <div class="w-64 h-full bg-gradient-to-r from-transparent via-blue-500 to-transparent animate-glow-line"></div>
        </div>
        <div class="absolute top-2/3 right-0 w-full h-px overflow-hidden">
            <div class="w-64 h-full bg-gradient-to-r from-transparent via-cyan-400 to-transparent animate-glow-line" style="animation-delay: 1.5s;"></div>
        </div>
    </div>

    <!-- Navbar Floating Glassmorphism -->
    <nav class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50 w-[95%] max-w-6xl">
        <div class="glass-effect bg-white/5 border border-white/20 rounded-3xl shadow-2xl shadow-black/20">
            <div class="flex items-center justify-between h-20 px-8">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/50">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-white">PosPhone</span>
                </div>

                <!-- Navigation Links - Centered -->
                <div class="hidden md:flex items-center space-x-8 absolute left-1/2 transform -translate-x-1/2">
                    <a href="#" class="text-white hover:text-blue-400 transition-all duration-300 font-medium">Home</a>
                    <a href="#features" class="text-white/70 hover:text-white transition-all duration-300 font-medium">Features</a>
                    <a href="#about" class="text-white/70 hover:text-white transition-all duration-300 font-medium">About</a>
                    <a href="#pricing" class="text-white/70 hover:text-white transition-all duration-300 font-medium">Pricing</a>
                </div>

                <!-- CTA Buttons -->
                <div class="flex items-center gap-4">
                    <a href="{{ route('login') }}" class="border-2 border-white text-white hover:bg-white hover:text-blue-600 px-6 py-2.5 rounded-full font-semibold transition-all duration-300 hover:scale-105 inline-block text-center">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="bg-[#3b82f6] hover:bg-blue-600 text-white px-6 py-3 rounded-full font-semibold transition-all duration-300 hover:scale-105 hover:shadow-lg hover:shadow-blue-500/50 inline-block text-center">
                        Coba Gratis
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center px-6 lg:px-8 pt-32">
        <div class="max-w-6xl mx-auto text-center relative z-10">
            
            <!-- Enhanced Floating Elements with More Variety -->
            <div class="absolute top-0 left-10 w-3 h-3 bg-blue-500 rounded-full animate-float animate-glow"></div>
            <div class="absolute top-20 right-20 w-2 h-2 bg-white rounded-full animate-float animate-glow" style="animation-delay: 1s;"></div>
            <div class="absolute bottom-40 left-32 w-4 h-4 bg-blue-400 rounded-full animate-float animate-glow" style="animation-delay: 2s;"></div>
            <div class="absolute bottom-20 right-40 w-2 h-2 bg-white/70 rounded-full animate-float animate-glow" style="animation-delay: 1.5s;"></div>
            
            <!-- NEW: Extra Interactive Elements -->
            <div class="absolute top-10 right-10 w-2 h-2 bg-cyan-400 rounded-full animate-float-reverse animate-glow" style="animation-delay: 0.5s;"></div>
            <div class="absolute bottom-10 left-10 w-3 h-3 bg-blue-300 rounded-full animate-wave animate-glow" style="animation-delay: 2.5s;"></div>
            <div class="absolute top-1/2 left-5 w-2 h-2 bg-white/80 rounded-full animate-float animate-glow" style="animation-delay: 3s;"></div>
            <div class="absolute top-1/2 right-5 w-3 h-3 bg-blue-400 rounded-full animate-float-reverse animate-glow" style="animation-delay: 3.5s;"></div>
            
            <!-- Glowing Lines - Enhanced -->
            <div class="absolute top-1/4 left-0 w-32 h-px bg-gradient-to-r from-transparent via-blue-500 to-transparent animate-glow"></div>
            <div class="absolute top-1/3 right-0 w-40 h-px bg-gradient-to-r from-transparent via-white to-transparent animate-glow" style="animation-delay: 1s;"></div>
            <div class="absolute bottom-1/3 left-10 w-36 h-px bg-gradient-to-r from-transparent via-cyan-400 to-transparent animate-glow" style="animation-delay: 2s;"></div>
            <div class="absolute bottom-1/4 right-10 w-28 h-px bg-gradient-to-r from-transparent via-blue-300 to-transparent animate-glow" style="animation-delay: 1.5s;"></div>
            
            <!-- NEW: Vertical Lines -->
            <div class="absolute top-1/4 right-1/4 w-px h-32 bg-gradient-to-b from-transparent via-blue-500 to-transparent animate-glow" style="animation-delay: 0.8s;"></div>
            <div class="absolute bottom-1/4 left-1/4 w-px h-28 bg-gradient-to-b from-transparent via-cyan-400 to-transparent animate-glow" style="animation-delay: 2.2s;"></div>

            <!-- Main Content -->
            <div class="space-y-8">
                <!-- Badge -->
                <div class="inline-flex items-center space-x-2 px-4 py-2 rounded-full glass-effect bg-white/5 border border-white/10 hover-lift">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    <span class="text-sm text-white/80 font-medium">Now Available - 2025</span>
                </div>

                <!-- Main Title with Gradient Animation -->
                <h1 class="text-6xl md:text-8xl font-extrabold text-white leading-tight text-shadow-glow">
                    Solusi POS untuk<br/>
                    <span class="bg-gradient-to-r from-blue-400 via-blue-500 to-blue-600 bg-clip-text text-transparent animate-gradient">
                        Toko Smartphone
                    </span>
                </h1>

                <!-- Subtitle -->
                <p class="text-xl md:text-2xl text-white/70 max-w-3xl mx-auto leading-relaxed font-light">
                    Kelola toko smartphone Anda dengan sistem Point of Sales modern, fleksibel, dan dapat dikustomisasi. Subscription bulanan dengan fitur lengkap untuk meningkatkan penjualan Anda.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-8">
                    <a href="{{ route('register') }}" class="group bg-[#3b82f6] hover:bg-blue-600 text-white px-10 py-5 rounded-2xl font-bold text-lg transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/30 flex items-center space-x-3">
                        <span>Mulai Gratis 15 Hari</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                    
                    <button class="group glass-effect bg-white/5 hover:bg-white/10 text-white px-10 py-5 rounded-2xl font-bold text-lg border-2 border-white/20 hover:border-white/40 transition-all duration-300 flex items-center space-x-3">
                        <span>Lihat Demo</span>
                        <svg class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                </div>

                <!-- Stats with Enhanced Hover -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 pt-16 max-w-4xl mx-auto">
                    <div class="glass-effect bg-white/5 border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all duration-300 hover-lift relative overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/0 via-blue-500/10 to-blue-500/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                        <div class="text-4xl font-bold text-blue-400 relative z-10">1000+</div>
                        <div class="text-white/60 mt-2 relative z-10">Toko Smartphone</div>
                    </div>
                    <div class="glass-effect bg-white/5 border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all duration-300 hover-lift relative overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/0 via-blue-500/10 to-blue-500/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                        <div class="text-4xl font-bold text-blue-400 relative z-10">Custom</div>
                        <div class="text-white/60 mt-2 relative z-10">Dapat Dikustomisasi</div>
                    </div>
                    <div class="glass-effect bg-white/5 border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-all duration-300 hover-lift relative overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/0 via-blue-500/10 to-blue-500/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                        <div class="text-4xl font-bold text-blue-400 relative z-10">24/7</div>
                        <div class="text-white/60 mt-2 relative z-10">Support Pelanggan</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator with Animation -->
        <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 animate-bounce">
            <div class="flex flex-col items-center space-y-2">
                <svg class="w-6 h-6 text-white/50 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
                <span class="text-xs text-white/40 tracking-wider">SCROLL</span>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="relative py-32 px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20">
                <h2 class="text-5xl md:text-6xl font-bold text-white mb-6">
                    Fitur <span class="text-blue-500">Lengkap</span>
                </h2>
                <p class="text-xl text-white/60 max-w-2xl mx-auto">
                    Semua yang Anda butuhkan untuk mengelola toko smartphone dengan efisien dan profesional
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="group glass-effect bg-white/5 border border-white/10 rounded-3xl p-8 hover:bg-white/10 transition-all duration-300 hover-lift hover:border-blue-500/50 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/0 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-12 transition-all duration-500 shadow-lg shadow-blue-500/50 relative z-10">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4 relative z-10">Manajemen Penjualan</h3>
                    <p class="text-white/60 leading-relaxed relative z-10">
                        Kelola transaksi penjualan dengan cepat dan akurat. Terima berbagai metode pembayaran, cetak struk otomatis, dan lacak riwayat transaksi.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="group glass-effect bg-white/5 border border-white/10 rounded-3xl p-8 hover:bg-white/10 transition-all duration-300 hover-lift hover:border-blue-500/50 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/0 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-12 transition-all duration-500 shadow-lg shadow-blue-500/50 relative z-10">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4 relative z-10">Inventori Otomatis</h3>
                    <p class="text-white/60 leading-relaxed relative z-10">
                        Pantau stok smartphone real-time, notifikasi stok menipis, dan kelola supplier dengan mudah. Sistem otomatis memperbarui inventori setiap transaksi.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="group glass-effect bg-white/5 border border-white/10 rounded-3xl p-8 hover:bg-white/10 transition-all duration-300 hover-lift hover:border-blue-500/50 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/0 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-12 transition-all duration-500 shadow-lg shadow-blue-500/50 relative z-10">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4 relative z-10">Laporan & Analisis</h3>
                    <p class="text-white/60 leading-relaxed relative z-10">
                        Dapatkan insight lengkap dengan laporan penjualan, profit, produk terlaris, dan performa toko. Dashboard interaktif untuk keputusan bisnis yang tepat.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="group glass-effect bg-white/5 border border-white/10 rounded-3xl p-8 hover:bg-white/10 transition-all duration-300 hover-lift hover:border-blue-500/50 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/0 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-12 transition-all duration-500 shadow-lg shadow-blue-500/50 relative z-10">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4 relative z-10">Akses Multi-Device</h3>
                    <p class="text-white/60 leading-relaxed relative z-10">
                        Akses sistem POS dari komputer, tablet, atau smartphone. Kelola toko kapan saja dan dimana saja dengan aplikasi responsive.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="group glass-effect bg-white/5 border border-white/10 rounded-3xl p-8 hover:bg-white/10 transition-all duration-300 hover-lift hover:border-blue-500/50 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/0 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-12 transition-all duration-500 shadow-lg shadow-blue-500/50 relative z-10">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4 relative z-10">Manajemen Karyawan</h3>
                    <p class="text-white/60 leading-relaxed relative z-10">
                        Atur hak akses karyawan, pantau performa sales, dan kelola shift kerja dengan mudah. Role-based access untuk keamanan data.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="group glass-effect bg-white/5 border border-white/10 rounded-3xl p-8 hover:bg-white/10 transition-all duration-300 hover-lift hover:border-blue-500/50 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/0 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-12 transition-all duration-500 shadow-lg shadow-blue-500/50 relative z-10">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4 relative z-10">Kustomisasi Penuh</h3>
                    <p class="text-white/60 leading-relaxed relative z-10">
                        Sesuaikan sistem dengan kebutuhan toko Anda. Custom field, kategori produk, template struk, dan fitur tambahan sesuai permintaan.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="relative py-32 px-6 lg:px-8 bg-gradient-to-b from-[#0A1A3D] to-[#051033]">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <!-- Left Content -->
                <div class="space-y-8">
                    <div class="inline-flex items-center space-x-2 px-4 py-2 rounded-full glass-effect bg-white/5 border border-white/10 hover-lift w-fit">
                        <span class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></span>
                        <span class="text-sm text-white/80 font-medium">Tentang Kami</span>
                    </div>

                    <div>
                        <h2 class="text-5xl md:text-6xl font-bold text-white mb-6 leading-tight">
                            Solusi POS <span class="text-blue-500">Terpercaya</span> untuk Bisnis Anda
                        </h2>
                        <p class="text-xl text-white/70 leading-relaxed mb-8">
                            Kami memahami tantangan yang dihadapi toko smartphone modern. Dengan pengalaman lebih dari 5 tahun di industri retail teknologi, kami menghadirkan sistem POS yang dirancang khusus untuk memenuhi kebutuhan spesifik bisnis smartphone Anda.
                        </p>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-4">
                        <div class="glass-effect bg-white/5 border border-white/10 rounded-2xl p-6">
                            <div class="text-3xl font-bold text-blue-400 mb-2">1000+</div>
                            <div class="text-sm text-white/60">Pengguna Aktif</div>
                        </div>
                        <div class="glass-effect bg-white/5 border border-white/10 rounded-2xl p-6">
                            <div class="text-3xl font-bold text-blue-400 mb-2">500M+</div>
                            <div class="text-sm text-white/60">Transaksi Terproses</div>
                        </div>
                        <div class="glass-effect bg-white/5 border border-white/10 rounded-2xl p-6">
                            <div class="text-3xl font-bold text-blue-400 mb-2">5+</div>
                            <div class="text-sm text-white/60">Tahun Berpengalaman</div>
                        </div>
                    </div>

                    <!-- Values -->
                    <div class="space-y-4 pt-4">
                        <div class="flex items-start space-x-4">
                            <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-white font-bold text-lg mb-1">Inovasi Berkelanjutan</h4>
                                <p class="text-white/60">Kami terus mengembangkan fitur baru berdasarkan feedback pelanggan</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-white font-bold text-lg mb-1">Dukungan 24/7</h4>
                                <p class="text-white/60">Tim support kami siap membantu Anda kapan saja dibutuhkan</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-white font-bold text-lg mb-1">Keamanan Data Terjamin</h4>
                                <p class="text-white/60">Enkripsi tingkat enterprise untuk melindungi data bisnis Anda</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Image/Illustration -->
                <div class="relative h-96 lg:h-full min-h-[500px] group">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 via-blue-600/10 to-transparent rounded-3xl group-hover:from-blue-500/30 group-hover:via-blue-600/20 transition-all duration-500"></div>
                    
                    <!-- Animated Shapes -->
                    <div class="absolute inset-0 rounded-3xl overflow-hidden">
                        <!-- Floating Cards Effect -->
                        <div class="absolute top-10 left-10 w-40 h-32 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 shadow-2xl shadow-blue-500/30 transform -rotate-6 group-hover:rotate-0 transition-transform duration-500">
                            <div class="text-white/80 text-sm mb-3">Penjualan Hari Ini</div>
                            <div class="text-white font-bold text-2xl">Rp 5.2M</div>
                        </div>

                        <div class="absolute bottom-20 right-10 w-40 h-32 glass-effect bg-white/10 border border-white/20 rounded-2xl p-6 shadow-2xl transform rotate-6 group-hover:rotate-0 transition-transform duration-500">
                            <div class="text-white/80 text-sm mb-3">Stok Produk</div>
                            <div class="text-white font-bold text-2xl">245 Unit</div>
                        </div>

                        <!-- Center Circle -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-32 h-32 bg-gradient-to-br from-blue-400 via-blue-500 to-blue-600 rounded-full opacity-80 blur-xl group-hover:scale-105 transition-transform duration-500"></div>
                            <svg class="absolute w-20 h-20 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="relative py-32 px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20">
                <div class="inline-flex items-center space-x-2 px-4 py-2 rounded-full glass-effect bg-white/5 border border-white/10 mb-6 hover-lift">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    <span class="text-sm text-white/80 font-medium">Paket Berlangganan</span>
                </div>
                <h2 class="text-5xl md:text-6xl font-bold text-white mb-6">
                    Harga <span class="text-blue-500">Terjangkau</span> untuk Semua
                </h2>
                <p class="text-xl text-white/60 max-w-2xl mx-auto">
                    Dapatkan semua fitur POS profesional dengan harga yang kompetitif. Tidak ada biaya tersembunyi, cukup bayar sesuai kebutuhan Anda.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <!-- Basic Plan -->
                <div class="group glass-effect bg-white/5 border border-white/10 rounded-3xl p-8 hover:bg-white/10 transition-all duration-300 hover-lift hover:border-blue-500/50 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/0 to-blue-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <div class="relative z-10">
                        <h3 class="text-2xl font-bold text-white mb-2">Paket Starter</h3>
                        <p class="text-white/60 text-sm mb-6">Cocok untuk toko kecil hingga menengah</p>
                        
                        <div class="mb-8">
                            <span class="text-5xl font-bold text-white">Rp 0</span>
                            <span class="text-white/60 ml-2">/bulan</span>
                            <p class="text-white/50 text-sm mt-2">Uji coba gratis 15 hari</p>
                        </div>

                        <ul class="space-y-4 mb-8">
                            <li class="flex items-center space-x-3">
                                <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-white/80">Manajemen Penjualan</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-white/80">Inventori Dasar</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-white/80">1 Pengguna</span>
                            </li>
                        </ul>

                        <a href="{{ route('register') }}" class="w-full glass-effect bg-white/10 hover:bg-white/20 text-white px-6 py-3 rounded-2xl font-bold border border-white/20 hover:border-white/40 transition-all duration-300 inline-block text-center">
                            Coba Gratis
                        </a>
                    </div>
                </div>

                <!-- Pro Plan - Featured -->
                <div class="group glass-effect bg-white/10 border-2 border-blue-500/50 rounded-3xl p-8 hover:bg-white/15 transition-all duration-300 hover-lift hover:border-blue-400 relative overflow-hidden md:scale-105 md:shadow-2xl md:shadow-blue-500/20">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-blue-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <!-- Popular Badge -->
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 px-4 py-1 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full text-white text-xs font-bold">
                        PALING POPULER
                    </div>

                    <div class="relative z-10">
                        <h3 class="text-2xl font-bold text-white mb-2">Paket Pro</h3>
                        <p class="text-white/60 text-sm mb-6">Paket lengkap untuk toko modern</p>
                        
                        <div class="mb-8">
                            <span class="text-5xl font-bold text-white">Rp 800K</span>
                            <span class="text-white/60 ml-2">/bulan</span>
                            <p class="text-white/50 text-sm mt-2">Hemat 20% dengan pembayaran tahunan</p>
                        </div>

                        <ul class="space-y-4 mb-8">
                            <li class="flex items-center space-x-3">
                                <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-white/80">Semua fitur di Starter</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-white/80">Laporan Lengkap</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-white/80">Hingga 5 Pengguna</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-white/80">Prioritas Support</span>
                            </li>
                        </ul>

                        <a href="{{ route('register') }}" class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-2xl font-bold transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/30 inline-block text-center">
                            Langganan Sekarang
                        </a>
                    </div>
                </div>

                <!-- Enterprise Plan -->
                <div class="group glass-effect bg-white/5 border border-white/10 rounded-3xl p-8 hover:bg-white/10 transition-all duration-300 hover-lift hover:border-blue-500/50 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/0 to-blue-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <div class="relative z-10">
                        <h3 class="text-2xl font-bold text-white mb-2">Paket Enterprise</h3>
                        <p class="text-white/60 text-sm mb-6">Untuk jaringan toko dengan kebutuhan khusus</p>
                        
                        <div class="mb-8">
                            <span class="text-5xl font-bold text-white">Custom</span>
                            <span class="text-white/60 ml-2">Hubungi Kami</span>
                            <p class="text-white/50 text-sm mt-2">Solusi disesuaikan dengan kebutuhan bisnis</p>
                        </div>

                        <ul class="space-y-4 mb-8">
                            <li class="flex items-center space-x-3">
                                <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-white/80">Unlimited Pengguna</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-white/80">Fitur Custom</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-white/80">Dedicated Support</span>
                            </li>
                            <li class="flex items-center space-x-3">
                                <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-white/80">SLA Guarantee</span>
                            </li>
                        </ul>

                        <a href="mailto:support@posphone.com?subject=Enterprise%20Plan%20Inquiry" class="w-full glass-effect bg-white/10 hover:bg-white/20 text-white px-6 py-3 rounded-2xl font-bold border border-white/20 hover:border-white/40 transition-all duration-300 inline-block text-center">
                            Hubungi Sales
                        </a>
                    </div>
                </div>
            </div>

            <!-- FAQ or Additional Info -->
            <div class="mt-20 text-center">
                <p class="text-white/60 mb-4">Semua paket termasuk:</p>
                <div class="flex flex-wrap justify-center gap-6">
                    <div class="flex items-center space-x-2 text-white/70">
                        <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>SSL Encryption</span>
                    </div>
                    <div class="flex items-center space-x-2 text-white/70">
                        <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>Daily Backups</span>
                    </div>
                    <div class="flex items-center space-x-2 text-white/70">
                        <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>99.9% Uptime</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <footer class="relative py-16 px-6 lg:px-8 border-t border-white/10">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <!-- Brand -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg shadow-blue-500/50">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-white">PosPhone</span>
                    </div>
                    <p class="text-white/60 leading-relaxed max-w-md">
                        Solusi Point of Sales terpercaya untuk toko smartphone di Indonesia. Tingkatkan efisiensi bisnis Anda dengan sistem yang fleksibel dan dapat dikustomisasi.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-white font-bold text-lg mb-4">Quick Links</h4>
                    <ul class="space-y-3">
                        <li><a href="#features" class="text-white/60 hover:text-white transition-colors duration-300">Features</a></li>
                        <li><a href="#about" class="text-white/60 hover:text-white transition-colors duration-300">About</a></li>
                        <li><a href="#pricing" class="text-white/60 hover:text-white transition-colors duration-300">Pricing</a></li>
                        <li><a href="#contact" class="text-white/60 hover:text-white transition-colors duration-300">Contact</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h4 class="text-white font-bold text-lg mb-4">Contact</h4>
                    <ul class="space-y-3">
                        <li class="text-white/60">support@posphone.com</li>
                        <li class="text-white/60">+1 (555) 123-4567</li>
                        <li class="text-white/60">Jakarta, Indonesia</li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="pt-8 border-t border-white/10 flex flex-col md:flex-row items-center justify-between">
                <p class="text-white/50 text-sm mb-4 md:mb-0">
                    © 2025 PosPhone. All rights reserved.
                </p>
                <div class="flex items-center space-x-6">
                    <a href="#" class="text-white/50 hover:text-white transition-colors duration-300">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-white/50 hover:text-white transition-colors duration-300">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-white/50 hover:text-white transition-colors duration-300">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Interactive JavaScript for Enhanced Animations -->
    <script>
        // Custom Cursor Glow Effect
        const cursorGlow = document.getElementById('cursorGlow');
        let mouseX = 0, mouseY = 0;
        let glowX = 0, glowY = 0;
        
        document.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;
        });
        
        function animateCursor() {
            const speed = 0.15;
            glowX += (mouseX - glowX) * speed;
            glowY += (mouseY - glowY) * speed;
            
            cursorGlow.style.left = glowX + 'px';
            cursorGlow.style.top = glowY + 'px';
            
            requestAnimationFrame(animateCursor);
        }
        animateCursor();
        
        // Parallax Effect on Scroll
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallaxElements = document.querySelectorAll('.parallax');
            
            parallaxElements.forEach((element, index) => {
                const speed = (index + 1) * 0.5;
                element.style.transform = `translateY(${scrolled * speed}px)`;
            });
        });
        
        // Intersection Observer for Fade In Animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);
        
        // Observe all feature cards
        document.querySelectorAll('.glass-effect').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });
        
        // Add 3D Tilt Effect on Cards
        document.querySelectorAll('.hover-lift').forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateX = (y - centerY) / 10;
                const rotateY = (centerX - x) / 10;
                
                card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-10px) scale(1.02)`;
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = '';
            });
        });
        
        // Random Particle Generation
        function createFloatingParticle() {
            const particle = document.createElement('div');
            particle.className = 'fixed w-1 h-1 bg-blue-400 rounded-full pointer-events-none';
            particle.style.left = Math.random() * window.innerWidth + 'px';
            particle.style.top = window.innerHeight + 'px';
            particle.style.opacity = '0.5';
            particle.style.zIndex = '0';
            
            document.body.appendChild(particle);
            
            const duration = 5000 + Math.random() * 5000;
            const drift = (Math.random() - 0.5) * 200;
            
            particle.animate([
                { 
                    transform: 'translateY(0px) translateX(0px)', 
                    opacity: 0.5 
                },
                { 
                    transform: `translateY(-${window.innerHeight + 100}px) translateX(${drift}px)`, 
                    opacity: 0 
                }
            ], {
                duration: duration,
                easing: 'linear'
            }).onfinish = () => {
                particle.remove();
            };
        }
        
        // Generate particles periodically
        setInterval(createFloatingParticle, 2000);
        
        // Navbar scroll effect
        const navbar = document.querySelector('nav');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.querySelector('.glass-effect').style.backgroundColor = 'rgba(255, 255, 255, 0.08)';
            } else {
                navbar.querySelector('.glass-effect').style.backgroundColor = 'rgba(255, 255, 255, 0.05)';
            }
        });
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Add ripple effect on button click
        document.querySelectorAll('button').forEach(button => {
            button.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.className = 'absolute bg-white/30 rounded-full pointer-events-none';
                
                ripple.animate([
                    { transform: 'scale(0)', opacity: 1 },
                    { transform: 'scale(2)', opacity: 0 }
                ], {
                    duration: 600,
                    easing: 'ease-out'
                }).onfinish = () => ripple.remove();
                
                this.appendChild(ripple);
            });
        });
    </script>

</body>
</html>
