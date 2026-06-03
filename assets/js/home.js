import '../css/tailwind.css';
import { supabase } from '../../config/supabase-client.js';

async function loadAuthButtons() {
    const { data: { session } } = await supabase.auth.getSession()
    const container = document.getElementById('auth-buttons')

    if (!container) return;

    if (session) {
        // Usuario logueado
        const { data: profile, error } = await supabase
            .from('usuarios')
            .select('rol_id')
            .eq('auth_id', session.user.id)
            .single()

        if (error) throw error

        let dashboardLink = '/modules/admin/index.html'
        if (profile) {
            if (profile.rol_id === 1 || profile.rol_id === 2) dashboardLink = '/modules/admin/index.html'
            else if (profile.rol_id === 3) dashboardLink = '/modules/teacher/index.html'
            else if (profile.rol_id === 4) dashboardLink = '/modules/student/index.html'
        }

        container.innerHTML = `
            <a href="${dashboardLink}" class="hidden sm:flex border border-vinotinto text-vinotinto font-bold text-xs uppercase tracking-widest px-6 py-2.5 hover:bg-vinotinto hover:text-white transition-all duration-300">
                Mi Dashboard
            </a>
            <button id="logout-btn" class="hidden sm:flex bg-vinotinto text-white font-bold text-xs uppercase tracking-widest px-6 py-2.5 hover:bg-vinotinto-dark transition-all duration-300 shadow-md">
                Cerrar Sesión
            </button>
        `

        const logoutBtn = document.getElementById('logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', async () => {
                await supabase.auth.signOut()
                window.location.reload()
            })
        }
    } else {
        // Usuario no logueado
        container.innerHTML = `
            <a href="/auth/login.html" class="hidden sm:flex border border-vinotinto text-vinotinto font-bold text-xs uppercase tracking-widest px-6 py-2.5 hover:bg-vinotinto hover:text-white transition-all duration-300">
                Iniciar Sesión
            </a>
        `
    }
}

// Cargar botones al iniciar
loadAuthButtons()
