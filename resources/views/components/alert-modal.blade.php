<!-- Alert Modal Component -->
<div id="alertModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-[9999] hidden transition-opacity duration-300" onclick="if(event.target===this) closeAlertModal()">
    <div id="alertModalContent" class="bg-white dark:bg-navy-800 rounded-2xl shadow-2xl max-w-sm w-full mx-4 p-6 transform transition-all duration-300 scale-95 opacity-0">
        <!-- Icon -->
        <div class="flex justify-center mb-4">
            <!-- Success Icon -->
            <div id="alertIconSuccess" class="hidden h-14 w-14 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                <svg class="h-7 w-7 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <!-- Error Icon -->
            <div id="alertIconError" class="hidden h-14 w-14 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                <svg class="h-7 w-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <!-- Warning Icon -->
            <div id="alertIconWarning" class="hidden h-14 w-14 items-center justify-center rounded-full bg-orange-100 dark:bg-orange-900/30">
                <svg class="h-7 w-7 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <!-- Info Icon -->
            <div id="alertIconInfo" class="hidden h-14 w-14 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                <svg class="h-7 w-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <!-- Title -->
        <h3 id="alertModalTitle" class="text-lg font-bold text-navy-700 dark:text-white text-center mb-2"></h3>
        <!-- Message -->
        <p id="alertModalMessage" class="text-center text-sm text-gray-600 dark:text-gray-400 mb-6"></p>
        <!-- Close Button -->
        <button onclick="closeAlertModal()" id="alertModalBtn" class="w-full rounded-xl py-2.5 text-sm font-bold text-white transition duration-200">
            OK
        </button>
    </div>
</div>

<script>
    let alertModalTimer = null;

    function showAlertModal(type, title, message, autoClose = 5000) {
        const modal = document.getElementById('alertModal');
        const content = document.getElementById('alertModalContent');
        const titleEl = document.getElementById('alertModalTitle');
        const messageEl = document.getElementById('alertModalMessage');
        const btn = document.getElementById('alertModalBtn');

        // Hide all icons
        document.getElementById('alertIconSuccess').classList.add('hidden');
        document.getElementById('alertIconSuccess').classList.remove('flex');
        document.getElementById('alertIconError').classList.add('hidden');
        document.getElementById('alertIconError').classList.remove('flex');
        document.getElementById('alertIconWarning').classList.add('hidden');
        document.getElementById('alertIconWarning').classList.remove('flex');
        document.getElementById('alertIconInfo').classList.add('hidden');
        document.getElementById('alertIconInfo').classList.remove('flex');

        // Show appropriate icon and set button color
        const iconId = 'alertIcon' + type.charAt(0).toUpperCase() + type.slice(1);
        const iconEl = document.getElementById(iconId);
        if (iconEl) {
            iconEl.classList.remove('hidden');
            iconEl.classList.add('flex');
        }

        // Set button style based on type
        btn.className = 'w-full rounded-xl py-2.5 text-sm font-bold text-white transition duration-200 ';
        switch (type) {
            case 'success':
                btn.className += 'bg-green-500 hover:bg-green-600';
                break;
            case 'error':
                btn.className += 'bg-red-500 hover:bg-red-600';
                break;
            case 'warning':
                btn.className += 'bg-orange-500 hover:bg-orange-600';
                break;
            case 'info':
                btn.className += 'bg-blue-500 hover:bg-blue-600';
                break;
            default:
                btn.className += 'bg-brand-500 hover:bg-brand-600';
        }

        titleEl.textContent = title;
        messageEl.textContent = message;

        // Show modal
        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        });

        // Auto close
        if (alertModalTimer) clearTimeout(alertModalTimer);
        if (autoClose && autoClose > 0) {
            alertModalTimer = setTimeout(() => closeAlertModal(), autoClose);
        }
    }

    function closeAlertModal() {
        const modal = document.getElementById('alertModal');
        const content = document.getElementById('alertModalContent');

        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);

        if (alertModalTimer) {
            clearTimeout(alertModalTimer);
            alertModalTimer = null;
        }
    }

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('alertModal');
            if (modal && !modal.classList.contains('hidden')) {
                closeAlertModal();
            }
        }
    });
</script>
