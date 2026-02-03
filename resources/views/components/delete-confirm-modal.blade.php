<!-- Delete Confirmation Modal
     Usage: @include('components.delete-confirm-modal', [
         'modalId' => 'deleteConfirmModal',  // optional, default: deleteConfirmModal
         'title' => 'Konfirmasi Hapus',      // optional
         'message' => 'Apakah Anda yakin ingin menghapus item ini?'  // optional
     ])
-->

<div id="{{ $modalId ?? 'deleteConfirmModal' }}" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-navy-800 rounded-lg shadow-xl max-w-sm w-full mx-4">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-white/10">
            <h3 class="text-lg font-bold text-navy-700 dark:text-white">{{ $title ?? 'Konfirmasi Hapus' }}</h3>
            <button type="button" onclick="closeDeleteModal('{{ $modalId ?? 'deleteConfirmModal' }}')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <p class="text-gray-600 dark:text-gray-400 mb-2">
                {{ $message ?? 'Apakah Anda yakin ingin menghapus item ini?' }}
            </p>
            @if(!isset($hideWarning))
            <p class="text-sm text-gray-500 dark:text-gray-500">
                Tindakan ini tidak dapat dibatalkan.
            </p>
            @endif
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-white/10">
            <button type="button" onclick="closeDeleteModal('{{ $modalId ?? 'deleteConfirmModal' }}')" 
                    class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-navy-700 transition">
                {{ $cancelBtnText ?? 'Batal' }}
            </button>
            <button type="button" onclick="{{ $onConfirm ?? 'proceedDelete()' }}" 
                    class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition font-semibold">
                {{ $confirmBtnText ?? 'Hapus' }}
            </button>
        </div>
    </div>
</div>

<script>
    function closeDeleteModal(modalId = 'deleteConfirmModal') {
        document.getElementById(modalId).classList.add('hidden');
    }

    function openDeleteModal(modalId = 'deleteConfirmModal', message = null) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            if (message) {
                const messageEl = modal.querySelector('p.text-gray-600');
                if (messageEl) messageEl.textContent = message;
            }
        }
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const modals = document.querySelectorAll('[id*="Modal"]');
        modals.forEach(modal => {
            if (event.target === modal) {
                modal.classList.add('hidden');
            }
        });
    });
</script>
