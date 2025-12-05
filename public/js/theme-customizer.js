// Theme Customizer JavaScript
class ThemeCustomizer {
    constructor() {
        this.currentMode = 'light';
        this.themes = {
            light: {
                default: {
                    name: 'Default',
                    primary: '#4318FF',
                    secondary: '#868CFF',
                    sidebar: '#FFFFFF',
                    navbar: '#FFFFFF',
                    background: '#F4F7FE',
                    text: '#1B2559',
                    gray: '#343b4f'
                },
                ocean: {
                    name: 'Ocean',
                    primary: '#0ea5e9',
                    secondary: '#38bdf8',
                    sidebar: '#FFFFFF',
                    navbar: '#FFFFFF',
                    background: '#f0f9ff',
                    text: '#0c4a6e',
                    gray: '#475569'
                },
                forest: {
                    name: 'Forest',
                    primary: '#22c55e',
                    secondary: '#4ade80',
                    sidebar: '#FFFFFF',
                    navbar: '#FFFFFF',
                    background: '#f0fdf4',
                    text: '#14532d',
                    gray: '#475569'
                },
                sunset: {
                    name: 'Sunset',
                    primary: '#f97316',
                    secondary: '#fb923c',
                    sidebar: '#FFFFFF',
                    navbar: '#FFFFFF',
                    background: '#fff7ed',
                    text: '#7c2d12',
                    gray: '#475569'
                },
                rose: {
                    name: 'Rose',
                    primary: '#e11d48',
                    secondary: '#f43f5e',
                    sidebar: '#FFFFFF',
                    navbar: '#FFFFFF',
                    background: '#fff1f2',
                    text: '#881337',
                    gray: '#475569'
                },
                midnight: {
                    name: 'Midnight',
                    primary: '#6366f1',
                    secondary: '#818cf8',
                    sidebar: '#FFFFFF',
                    navbar: '#FFFFFF',
                    background: '#eef2ff',
                    text: '#312e81',
                    gray: '#475569'
                }
            },
            dark: {
                default: {
                    name: 'Default Dark',
                    primary: '#4318FF',
                    secondary: '#868CFF',
                    sidebar: '#0b1437',
                    navbar: '#0b1437',
                    background: '#0b1437',
                    text: '#FFFFFF',
                    gray: '#a3aed0'
                },
                ocean: {
                    name: 'Ocean Dark',
                    primary: '#0ea5e9',
                    secondary: '#38bdf8',
                    sidebar: '#082f49',
                    navbar: '#082f49',
                    background: '#0c4a6e',
                    text: '#e0f2fe',
                    gray: '#94a3b8'
                },
                forest: {
                    name: 'Forest Dark',
                    primary: '#22c55e',
                    secondary: '#4ade80',
                    sidebar: '#14532d',
                    navbar: '#14532d',
                    background: '#052e16',
                    text: '#dcfce7',
                    gray: '#94a3b8'
                },
                sunset: {
                    name: 'Sunset Dark',
                    primary: '#f97316',
                    secondary: '#fb923c',
                    sidebar: '#431407',
                    navbar: '#431407',
                    background: '#7c2d12',
                    text: '#ffedd5',
                    gray: '#94a3b8'
                },
                rose: {
                    name: 'Rose Dark',
                    primary: '#e11d48',
                    secondary: '#f43f5e',
                    sidebar: '#4c0519',
                    navbar: '#4c0519',
                    background: '#881337',
                    text: '#ffe4e6',
                    gray: '#94a3b8'
                },
                midnight: {
                    name: 'Midnight Dark',
                    primary: '#6366f1',
                    secondary: '#818cf8',
                    sidebar: '#1e1b4b',
                    navbar: '#1e1b4b',
                    background: '#312e81',
                    text: '#e0e7ff',
                    gray: '#94a3b8'
                }
            }
        };
        
        this.init();
    }

    init() {
        console.log('🎨 Theme Customizer initializing...');
        this.loadSavedTheme();
        this.createCustomizerUI();
        this.attachEventListeners();
        this.updateCurrentMode();
        this.observeDarkModeChanges();
        console.log('✅ Theme Customizer ready!');
    }

    loadSavedTheme() {
        const savedTheme = localStorage.getItem('customTheme');
        if (savedTheme) {
            const theme = JSON.parse(savedTheme);
            // Ensure both modes exist
            if (!theme.light) theme.light = this.themes.light.default;
            if (!theme.dark) theme.dark = this.themes.dark.default;
            this.applyTheme(theme);
        } else {
            // Apply default theme for both modes
            this.applyTheme({
                light: this.themes.light.default,
                dark: this.themes.dark.default
            });
        }
    }

    createCustomizerUI() {
        console.log('Creating customizer UI...');
        
        const customizerHTML = `
            <!-- Theme Customizer Trigger -->
            <div class="theme-customizer-trigger" id="themeCustomizerTrigger" style="position:fixed;right:20px;bottom:20px;width:50px;height:50px;border-radius:50%;background:linear-gradient(135deg,#4318FF 0%,#868CFF 100%);box-shadow:0 4px 12px rgba(67,24,255,0.4);display:flex;align-items:center;justify-content:center;cursor:pointer;z-index:999;">
                <svg class="w-6 h-6 text-white" style="width:24px;height:24px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                </svg>
            </div>

            <!-- Overlay -->
            <div class="theme-customizer-overlay" id="themeCustomizerOverlay"></div>

            <!-- Customizer Modal -->
            <div class="theme-customizer-modal" id="themeCustomizerModal">
                <div class="theme-customizer-header">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-navy-700 dark:text-white">Theme Customizer</h3>
                        <button id="closeCustomizer" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="theme-customizer-content">
                    <!-- Mode Toggle -->
                    <div class="mode-toggle">
                        <button class="mode-toggle-btn active" data-mode="light">
                            <svg class="w-5 h-5 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                            </svg>
                            Light
                        </button>
                        <button class="mode-toggle-btn" data-mode="dark">
                            <svg class="w-5 h-5 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                            </svg>
                            Dark
                        </button>
                    </div>

                    <!-- Preset Themes -->
                    <div class="mb-6">
                        <h4 class="text-sm font-bold text-navy-700 dark:text-white mb-3">Preset Themes</h4>
                        <div class="preset-themes" id="presetThemes"></div>
                    </div>

                    <div class="section-divider"></div>

                    <!-- Custom Colors -->
                    <h4 class="text-sm font-bold text-navy-700 dark:text-white mb-3">Custom Colors</h4>
                    
                    <div class="color-picker-group">
                        <label class="color-picker-label">Primary Color</label>
                        <div class="color-picker-wrapper">
                            <input type="color" class="color-picker-input" id="primaryColor" value="#4318FF">
                            <input type="text" class="color-hex-input" id="primaryColorHex" value="#4318FF" maxlength="7">
                        </div>
                    </div>

                    <div class="color-picker-group">
                        <label class="color-picker-label">Secondary Color</label>
                        <div class="color-picker-wrapper">
                            <input type="color" class="color-picker-input" id="secondaryColor" value="#868CFF">
                            <input type="text" class="color-hex-input" id="secondaryColorHex" value="#868CFF" maxlength="7">
                        </div>
                    </div>

                    <div class="color-picker-group">
                        <label class="color-picker-label">Sidebar Background</label>
                        <div class="color-picker-wrapper">
                            <input type="color" class="color-picker-input" id="sidebarColor" value="#FFFFFF">
                            <input type="text" class="color-hex-input" id="sidebarColorHex" value="#FFFFFF" maxlength="7">
                        </div>
                    </div>

                    <div class="color-picker-group">
                        <label class="color-picker-label">Background Color</label>
                        <div class="color-picker-wrapper">
                            <input type="color" class="color-picker-input" id="backgroundColor" value="#F4F7FE">
                            <input type="text" class="color-hex-input" id="backgroundColorHex" value="#F4F7FE" maxlength="7">
                        </div>
                    </div>

                    <div class="color-picker-group">
                        <label class="color-picker-label">Text Gray Color</label>
                        <div class="color-picker-wrapper">
                            <input type="color" class="color-picker-input" id="grayColor" value="#343b4f">
                            <input type="text" class="color-hex-input" id="grayColorHex" value="#343b4f" maxlength="7">
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="theme-actions">
                        <button class="theme-btn theme-btn-secondary" id="resetTheme">Reset Default</button>
                        <button class="theme-btn theme-btn-primary" id="applyTheme">Apply Theme</button>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', customizerHTML);
        console.log('✓ Customizer HTML inserted');
        
        // Verify trigger button exists
        const trigger = document.getElementById('themeCustomizerTrigger');
        console.log('✓ Trigger button:', trigger ? 'Found' : 'NOT FOUND!');
        
        this.renderPresetThemes();
    }

    renderPresetThemes() {
        const container = document.getElementById('presetThemes');
        const themes = this.themes[this.currentMode];
        
        container.innerHTML = Object.keys(themes).map(key => {
            const theme = themes[key];
            return `
                <div class="preset-theme-card" data-theme="${key}">
                    <div class="preset-theme-colors">
                        <div class="preset-color-dot" style="background: ${theme.primary}"></div>
                        <div class="preset-color-dot" style="background: ${theme.secondary}"></div>
                        <div class="preset-color-dot" style="background: ${theme.sidebar}"></div>
                    </div>
                    <div class="preset-theme-name">${theme.name}</div>
                </div>
            `;
        }).join('');
    }

    attachEventListeners() {
        // Toggle customizer
        document.getElementById('themeCustomizerTrigger').addEventListener('click', () => this.toggleCustomizer());
        document.getElementById('themeCustomizerOverlay').addEventListener('click', () => this.toggleCustomizer());
        document.getElementById('closeCustomizer').addEventListener('click', () => this.toggleCustomizer());

        // Mode toggle
        document.querySelectorAll('.mode-toggle-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.switchMode(e.target.closest('.mode-toggle-btn').dataset.mode));
        });

        // Preset themes
        document.getElementById('presetThemes').addEventListener('click', (e) => {
            const card = e.target.closest('.preset-theme-card');
            if (card) {
                const themeKey = card.dataset.theme;
                this.loadPresetTheme(themeKey);
            }
        });

        // Color pickers sync
        this.syncColorInputs('primary');
        this.syncColorInputs('secondary');
        this.syncColorInputs('sidebar');
        this.syncColorInputs('background');
        this.syncColorInputs('gray');

        // Apply and reset
        document.getElementById('applyTheme').addEventListener('click', () => this.saveAndApplyCustomTheme());
        document.getElementById('resetTheme').addEventListener('click', () => this.resetToDefault());
    }

    syncColorInputs(name) {
        const colorInput = document.getElementById(`${name}Color`);
        const hexInput = document.getElementById(`${name}ColorHex`);

        colorInput.addEventListener('input', (e) => {
            hexInput.value = e.target.value.toUpperCase();
        });

        hexInput.addEventListener('input', (e) => {
            let value = e.target.value;
            if (value.startsWith('#') && value.length === 7) {
                colorInput.value = value;
            }
        });
    }

    toggleCustomizer() {
        const modal = document.getElementById('themeCustomizerModal');
        const overlay = document.getElementById('themeCustomizerOverlay');
        modal.classList.toggle('active');
        overlay.classList.toggle('active');
    }

    switchMode(mode) {
        this.currentMode = mode;
        document.querySelectorAll('.mode-toggle-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.mode === mode);
        });
        this.renderPresetThemes();
        this.loadCurrentColors();
    }

    loadPresetTheme(themeKey) {
        const theme = this.themes[this.currentMode][themeKey];
        document.getElementById('primaryColor').value = theme.primary;
        document.getElementById('primaryColorHex').value = theme.primary;
        document.getElementById('secondaryColor').value = theme.secondary;
        document.getElementById('secondaryColorHex').value = theme.secondary;
        document.getElementById('sidebarColor').value = theme.sidebar;
        document.getElementById('sidebarColorHex').value = theme.sidebar;
        document.getElementById('backgroundColor').value = theme.background;
        document.getElementById('backgroundColorHex').value = theme.background;
            document.getElementById('grayColor').value = theme.gray;
            document.getElementById('grayColorHex').value = theme.gray;
            
            // Highlight selected preset
            document.querySelectorAll('.preset-theme-card').forEach(card => {
                card.classList.toggle('active', card.dataset.theme === themeKey);
            });
        }

    loadCurrentColors() {
        const savedTheme = localStorage.getItem('customTheme');
        if (savedTheme) {
            const theme = JSON.parse(savedTheme);
            const modeTheme = theme[this.currentMode];
            
            document.getElementById('primaryColor').value = modeTheme.primary;
            document.getElementById('primaryColorHex').value = modeTheme.primary;
            document.getElementById('secondaryColor').value = modeTheme.secondary;
            document.getElementById('secondaryColorHex').value = modeTheme.secondary;
            document.getElementById('sidebarColor').value = modeTheme.sidebar;
            document.getElementById('sidebarColorHex').value = modeTheme.sidebar;
            document.getElementById('backgroundColor').value = modeTheme.background;
            document.getElementById('backgroundColorHex').value = modeTheme.background;
            document.getElementById('grayColor').value = modeTheme.gray;
            document.getElementById('grayColorHex').value = modeTheme.gray;
        } else {
            this.loadPresetTheme('default');
        }
    }

    saveAndApplyCustomTheme() {
        // Get existing saved theme or use defaults
        const existingTheme = localStorage.getItem('customTheme');
        const savedTheme = existingTheme ? JSON.parse(existingTheme) : {
            light: this.themes.light.default,
            dark: this.themes.dark.default
        };

        // Update only the mode being edited
        const customTheme = {
            light: this.currentMode === 'light' ? this.getCurrentColors() : (savedTheme.light || this.themes.light.default),
            dark: this.currentMode === 'dark' ? this.getCurrentColors() : (savedTheme.dark || this.themes.dark.default)
        };

        console.log('Saving theme:', customTheme);
        
        try {
            localStorage.setItem('customTheme', JSON.stringify(customTheme));
            console.log('✓ Saved to localStorage');
            
            // Verify save
            const verify = localStorage.getItem('customTheme');
            console.log('✓ Verification:', verify ? 'Success' : 'FAILED!');
        } catch (e) {
            console.error('❌ LocalStorage save failed:', e);
        }
        
        // Apply theme
        this.applyTheme(customTheme);
        
        this.toggleCustomizer();
    }

    getCurrentColors() {
        return {
            primary: document.getElementById('primaryColorHex').value,
            secondary: document.getElementById('secondaryColorHex').value,
            sidebar: document.getElementById('sidebarColorHex').value,
            background: document.getElementById('backgroundColorHex').value,
            gray: document.getElementById('grayColorHex').value
        };
    }

    applyTheme(theme) {
        console.log('Applying theme:', theme);
        
        const isDark = document.documentElement.classList.contains('dark');
        const currentTheme = isDark ? theme.dark : theme.light;

        // Apply CSS variables to root
        const root = document.documentElement;
        
        // Brand colors
        root.style.setProperty('--color-brand-primary', currentTheme.primary);
        root.style.setProperty('--color-brand-secondary', currentTheme.secondary);
        root.style.setProperty('--color-sidebar-bg', currentTheme.sidebar);
        root.style.setProperty('--color-page-bg', currentTheme.background);
        root.style.setProperty('--color-gray-600', currentTheme.gray);
        
        // Generate color variations
        root.style.setProperty('--color-brand-400', this.lightenColor(currentTheme.primary, 10));
        root.style.setProperty('--color-brand-500', currentTheme.primary);
        root.style.setProperty('--color-brand-600', this.darkenColor(currentTheme.primary, 10));
        
        // Apply to elements directly with !important - pass the full theme object
        this.applyStylesToElements(theme);
        
        console.log('✓ Theme applied for mode:', isDark ? 'dark' : 'light');
    }

    resetToDefault() {
        localStorage.removeItem('customTheme');
        this.loadPresetTheme('default');
        this.showNotification('Reset to default theme');
        setTimeout(() => window.location.reload(), 500);
    }

    updateCurrentMode() {
        const isDark = document.documentElement.classList.contains('dark');
        this.currentMode = isDark ? 'dark' : 'light';
    }

    applyStylesToElements(theme) {
        // Create or update dynamic style tag
        let styleTag = document.getElementById('dynamic-theme-styles');
        if (!styleTag) {
            styleTag = document.createElement('style');
            styleTag.id = 'dynamic-theme-styles';
            document.head.appendChild(styleTag);
            console.log('✓ Created dynamic style tag');
        }

        // Use the passed theme object which contains both light and dark
        const lightTheme = theme.light || this.themes.light.default;
        const darkTheme = theme.dark || this.themes.dark.default;
        
        console.log('Applying styles for Light:', lightTheme.primary);
        console.log('Applying styles for Dark:', darkTheme.primary);
        
        styleTag.textContent = `
            /* Light Mode */
            html:not(.dark) .text-brand-500 { color: ${lightTheme.primary} !important; }
            html:not(.dark) .bg-brand-500 { background-color: ${lightTheme.primary} !important; }
            html:not(.dark) #sidebar { background-color: ${lightTheme.sidebar} !important; }
            html:not(.dark) body { background-color: ${lightTheme.background} !important; }
            html:not(.dark) .text-gray-600:not(.dark\\:text-gray-400) { color: ${lightTheme.gray} !important; }
            html:not(.dark) .theme-customizer-trigger { background: linear-gradient(135deg, ${lightTheme.primary}, ${lightTheme.secondary}) !important; }
            
            /* Dark Mode */
            html.dark .text-brand-500 { color: ${darkTheme.primary} !important; }
            html.dark .bg-brand-500 { background-color: ${darkTheme.primary} !important; }
            html.dark #sidebar { background-color: ${darkTheme.sidebar} !important; }
            html.dark body { background-color: ${darkTheme.background} !important; }
            html.dark .text-gray-400, html.dark .dark\\:text-gray-400 { color: ${darkTheme.gray} !important; }
            html.dark .theme-customizer-trigger { background: linear-gradient(135deg, ${darkTheme.primary}, ${darkTheme.secondary}) !important; }
        `;
        
        console.log('✓ Styles injected');
    }

    resetToDefault() {
        console.log('Resetting to default theme...');
        localStorage.removeItem('customTheme');
        
        // Remove dynamic styles
        const styleTag = document.getElementById('dynamic-theme-styles');
        if (styleTag) {
            styleTag.remove();
        }
        
        // Apply default theme immediately
        const defaultTheme = {
            light: this.themes.light.default,
            dark: this.themes.dark.default
        };
        
        localStorage.setItem('customTheme', JSON.stringify(defaultTheme));
        this.applyTheme(defaultTheme);
        
        // Reload the color picker values
        this.loadPresetTheme('default');
        
        console.log('Reset complete');
    }

    updateCurrentMode() {
        const isDark = document.documentElement.classList.contains('dark');
        this.currentMode = isDark ? 'dark' : 'light';
    }

    observeDarkModeChanges() {
        // Watch for dark mode toggle
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class') {
                    const isDark = document.documentElement.classList.contains('dark');
                    const newMode = isDark ? 'dark' : 'light';
                    
                    if (newMode !== this.currentMode) {
                        this.currentMode = newMode;
                        // Re-apply theme when mode changes to ensure correct colors
                        const savedTheme = localStorage.getItem('customTheme');
                        if (savedTheme) {
                            const theme = JSON.parse(savedTheme);
                            if (!theme.light) theme.light = this.themes.light.default;
                            if (!theme.dark) theme.dark = this.themes.dark.default;
                            this.applyTheme(theme);
                        } else {
                            // Apply default if no saved theme
                            this.applyTheme({
                                light: this.themes.light.default,
                                dark: this.themes.dark.default
                            });
                        }
                    }
                }
            });
        });

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class']
        });
    }

    darkenColor(hex, percent) {
        const num = parseInt(hex.replace('#', ''), 16);
        const amt = Math.round(2.55 * percent);
        const R = (num >> 16) - amt;
        const G = (num >> 8 & 0x00FF) - amt;
        const B = (num & 0x0000FF) - amt;
        return '#' + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
            (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
            (B < 255 ? B < 1 ? 0 : B : 255))
            .toString(16).slice(1);
    }

    lightenColor(hex, percent) {
        const num = parseInt(hex.replace('#', ''), 16);
        const amt = Math.round(2.55 * percent);
        const R = (num >> 16) + amt;
        const G = (num >> 8 & 0x00FF) + amt;
        const B = (num & 0x0000FF) + amt;
        return '#' + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
            (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
            (B < 255 ? B < 1 ? 0 : B : 255))
            .toString(16).slice(1);
    }

    showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-[9999] transition-all transform translate-x-0';
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.transform = 'translateX(400px)';
            setTimeout(() => notification.remove(), 300);
        }, 2000);
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new ThemeCustomizer();
    });
} else {
    // DOM already loaded
    new ThemeCustomizer();
}
