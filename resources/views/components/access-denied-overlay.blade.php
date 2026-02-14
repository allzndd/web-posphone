<!-- Access Denied Overlay Component -->
@if(!$hasAccessRead)
<div class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white dark:bg-navy-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 p-8">
        <div class="flex justify-center mb-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                <svg class="h-8 w-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm6-10V7a3 3 0 00-6 0v4a3 3 0 006 0z"/>
                </svg>
            </div>
        </div>
        <h3 class="text-xl font-bold text-navy-700 dark:text-white text-center mb-2">Akses Ditolak</h3>
        <p class="text-center text-gray-600 dark:text-gray-400 mb-2">
            Anda tidak memiliki akses untuk membuka halaman {{ $module ?? 'ini' }}.
        </p>
        <p class="text-center text-red-600 dark:text-red-400 font-semibold mb-6">
            Silahkan melakukan upgrade layanan untuk mendapatkan akses.
        </p>
        <a href="{{ route('dashboard') ?? '/' }}" class="w-full block text-center bg-brand-500 hover:bg-brand-600 text-white font-bold py-3 rounded-xl transition duration-200">
            Kembali ke Dashboard
        </a>
    </div>
</div>
@endif
