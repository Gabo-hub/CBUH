const NotificationSystem = {
    container: null,

    init() {
        if (!this.container) {
            console.log('[NotificationSystem] Initializing...');
            this.container = document.createElement('div');
            this.container.id = 'toast-container';
            // Ensure z-index is extremely high and position is correct
            this.container.className = 'fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none';
            document.body.appendChild(this.container);
        }
    },

    show(message, type = 'info') {
        this.init();
        console.log(`[NotificationSystem] Showing ${type}: ${message}`);

        const toast = document.createElement('div');
        // Using backdrop-blur and specific colors. 
        // We use hex colors in classes to avoid conflicts with !important rules in theme.css
        toast.className = `
            pointer-events-auto flex items-center gap-3 px-6 py-4 rounded-xl shadow-2xl 
            transform translate-y-10 opacity-0 transition-all duration-300 ease-out
            border border-white/10 backdrop-blur-md
            min-w-[300px] max-w-md
        `;

        // Style based on type
        let bgClass, iconName, iconColor;
        switch (type) {
            case 'success':
                bgClass = 'bg-[#4A0E1C]/95 border-green-500/30';
                iconName = 'check_circle';
                iconColor = 'text-green-400';
                break;
            case 'error':
                bgClass = 'bg-[#4A0E1C]/95 border-red-500/30';
                iconName = 'error';
                iconColor = 'text-red-400';
                break;
            case 'warning':
                bgClass = 'bg-[#4A0E1C]/95 border-[#C9A961]/30';
                iconName = 'warning';
                iconColor = 'text-[#C9A961]';
                break;
            default:
                bgClass = 'bg-[#4A0E1C]/95 border-white/20';
                iconName = 'info';
                iconColor = 'text-white/60';
        }

        toast.classList.add(...bgClass.split(' '));

        toast.innerHTML = `
            <span class="material-symbols-outlined ${iconColor} text-xl">${iconName}</span>
            <p class="text-sm font-bold text-white flex-1 leading-snug">${message}</p>
            <button class="text-white/20 hover:text-white transition-colors ml-2 flex shrink-0">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        `;

        // Add close event listener manually
        const closeBtn = toast.querySelector('button');
        closeBtn.onclick = () => {
            toast.classList.add('translate-y-10', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        };

        this.container.appendChild(toast);

        // Animate entrance
        requestAnimationFrame(() => {
            setTimeout(() => {
                toast.classList.remove('translate-y-10', 'opacity-0');
            }, 10);
        });

        // Auto remove
        setTimeout(() => {
            if (toast.parentElement) {
                toast.classList.add('translate-y-10', 'opacity-0');
                setTimeout(() => {
                    if (toast.parentElement) toast.remove();
                }, 300);
            }
        }, 5000);
    },

    /**
     * Reemplaza al confirm() nativo con una versión estilizada
     */
    confirm(title, message, options = {}) {
        const {
            confirmText = 'Confirmar',
            cancelText = 'Cancelar',
            type = 'warning'
        } = options;

        return new Promise((resolve) => {
            // Modal Overlay
            const overlay = document.createElement('div');
            overlay.className = 'fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity duration-300 opacity-0';

            // Modal Card
            const card = document.createElement('div');
            card.className = 'bg-[#4A0E1C] border border-white/10 rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden transform scale-90 transition-transform duration-300';

            // Icon logic
            let icon = 'help_outline';
            if (type === 'danger') icon = 'report_problem';

            // Content
            card.innerHTML = `
                <div class="p-8 text-center">
                    <div class="size-16 rounded-full bg-[#C9A961]/10 flex items-center justify-center mx-auto mb-6 border border-[#C9A961]/20">
                        <span class="material-symbols-outlined text-3xl text-[#C9A961]">${icon}</span>
                    </div>
                    <h3 class="text-xl font-black text-white uppercase tracking-tight mb-3">${title}</h3>
                    <p class="text-sm text-white/50 leading-relaxed font-medium">${message}</p>
                </div>
                <div class="flex border-t border-white/5">
                    <button id="noti-cancel" class="flex-1 px-6 py-5 text-[10px] font-black text-white/40 uppercase tracking-[0.2em] hover:bg-white/5 transition-colors">
                        ${cancelText}
                    </button>
                    <button id="noti-confirm" class="flex-1 px-6 py-5 text-[10px] font-black text-[#C9A961] uppercase tracking-[0.2em] bg-white/5 hover:bg-white/10 transition-colors border-l border-white/5">
                        ${confirmText}
                    </button>
                </div>
            `;

            overlay.appendChild(card);
            document.body.appendChild(overlay);

            // Animate in
            requestAnimationFrame(() => {
                setTimeout(() => {
                    overlay.classList.add('opacity-100');
                    card.classList.add('scale-100');
                }, 10);
            });

            const close = (result) => {
                overlay.classList.remove('opacity-100');
                card.classList.remove('scale-100');
                setTimeout(() => {
                    overlay.remove();
                    resolve(result);
                }, 300);
            };

            overlay.querySelector('#noti-cancel').onclick = () => close(false);
            overlay.querySelector('#noti-confirm').onclick = () => close(true);

            // Close on backdrop click
            overlay.onclick = (e) => {
                if (e.target === overlay) close(false);
            };
        });
    }
};

window.NotificationSystem = NotificationSystem;
