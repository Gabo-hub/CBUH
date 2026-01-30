/**
 * Sistema de Notificaciones (Toast)
 * Uso: NotificationSystem.show(mensaje, tipo);
 * tipo: 'success', 'error', 'info', 'warning'
 */
const NotificationSystem = {
    container: null,

    init() {
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.id = 'toast-container';
            this.container.className = 'fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none';
            document.body.appendChild(this.container);
        }
    },

    show(message, type = 'info') {
        this.init();

        const toast = document.createElement('div');
        toast.className = `
            pointer-events-auto flex items-center gap-3 px-6 py-4 rounded-xl shadow-2xl 
            transform translate-y-10 opacity-0 transition-all duration-300 ease-out
            border border-white/10 backdrop-blur-md
            min-w-[300px] max-w-md
        `;

        // Colores basados en el tipo
        let bgClass, iconName, iconColor;
        switch (type) {
            case 'success':
                bgClass = 'bg-primary-dark/90 border-green-500/30';
                iconName = 'check_circle';
                iconColor = 'text-green-400';
                break;
            case 'error':
                bgClass = 'bg-primary-dark/90 border-red-500/30';
                iconName = 'error';
                iconColor = 'text-red-400';
                break;
            case 'warning':
                bgClass = 'bg-primary-dark/90 border-gold/30';
                iconName = 'warning';
                iconColor = 'text-gold';
                break;
            default:
                bgClass = 'bg-primary-dark/90 border-white/20';
                iconName = 'info';
                iconColor = 'text-white/60';
        }

        toast.classList.add(...bgClass.split(' '));

        toast.innerHTML = `
            <span class="material-symbols-outlined ${iconColor} text-xl">${iconName}</span>
            <p class="text-sm font-bold text-white flex-1 leading-snug">${message}</p>
            <button onclick="this.parentElement.remove()" class="text-white/20 hover:text-white transition-colors">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        `;

        this.container.appendChild(toast);

        // Animar entrada
        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-10', 'opacity-0');
        });

        // Auto eliminar
        setTimeout(() => {
            toast.classList.add('translate-y-10', 'opacity-0');
            setTimeout(() => {
                if (toast.parentElement) toast.remove();
            }, 300);
        }, 5000);
    }
};

window.NotificationSystem = NotificationSystem;
