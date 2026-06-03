/**
 * Utilidades compartidas para toda la aplicación
 */

/**
 * Mostrar mensaje de error
 */
export function showError(message, containerId = 'error-message') {
    const errorDiv = document.getElementById(containerId)
    if (errorDiv) {
        errorDiv.textContent = message
        errorDiv.classList.remove('hidden')
        setTimeout(() => errorDiv.classList.add('hidden'), 5000)
    } else {
        alert(`Error: ${message}`)
    }
}

/**
 * Mostrar mensaje de éxito
 */
export function showSuccess(message, containerId = 'success-message') {
    const successDiv = document.getElementById(containerId)
    if (successDiv) {
        successDiv.textContent = message
        successDiv.classList.remove('hidden')
        setTimeout(() => successDiv.classList.add('hidden'), 3000)
    } else {
        console.log(`Success: ${message}`)
    }
}

/**
 * Redirigir usuario según su rol
 */
export async function redirectByRole(supabase) {
    try {
        const { data: { user } } = await supabase.auth.getUser()

        if (!user) {
            window.location.href = '/auth/login.html'
            return
        }

        // Obtener perfil del usuario
        const { data: profile, error } = await supabase
            .from('usuarios')
            .select('rol_id')
            .eq('auth_id', user.id)
            .single()

        if (error) throw error

        // Redirigir según rol
        switch (profile.rol_id) {
            case 1: // Administrador
            case 2: // Control de Estudio
                window.location.href = '/modules/admin/index.html'
                break
            case 3: // Docente
                window.location.href = '/modules/teacher/index.html'
                break
            case 4: // Estudiante
                window.location.href = '/modules/student/index.html'
                break
            default:
                throw new Error('Rol no válido')
        }
    } catch (error) {
        console.error('Error al redirigir:', error)
        showError('Error al cargar perfil de usuario')
    }
}

/**
 * Formatear fecha a formato local
 */
export function formatDate(dateString) {
    if (!dateString) return '-'
    const date = new Date(dateString)
    return date.toLocaleDateString('es-VE', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

/**
 * Formatear hora
 */
export function formatTime(timeString) {
    if (!timeString) return '-'
    const [hours, minutes] = timeString.split(':')
    const h = parseInt(hours)
    const ampm = h >= 12 ? 'PM' : 'AM'
    const displayHour = h > 12 ? h - 12 : (h === 0 ? 12 : h)
    return `${displayHour}:${minutes} ${ampm}`
}

/**
 * Debounce function
 */
export function debounce(func, wait) {
    let timeout
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout)
            func(...args)
        }
        clearTimeout(timeout)
        timeout = setTimeout(later, wait)
    }
}

/**
 * Verificar si usuario está autenticado
 */
export async function requireAuth(supabase) {
    const { data: { session } } = await supabase.auth.getSession()
    if (!session) {
        window.location.href = '/auth/login.html'
        return false
    }
    return true
}

/**
 * Obtener perfil completo del usuario actual
 */
export async function getCurrentUserProfile(supabase) {
    const { data: { user } } = await supabase.auth.getUser()

    const { data: profile, error } = await supabase
        .from('usuarios')
        .select(`
            *,
            rol:roles(nombre),
            sede:sedes(nombre, codigo)
        `)
        .eq('auth_id', user.id)
        .single()

    if (error) throw error
    return profile
}

/**
 * Calculadora de "hace cuánto tiempo"
 */
export function timeAgo(date) {
    const seconds = Math.floor((new Date() - new Date(date)) / 1000)

    let interval = Math.floor(seconds / 31536000)
    if (interval > 1) return `Hace ${interval} años`
    if (interval === 1) return 'Hace 1 año'

    interval = Math.floor(seconds / 2592000)
    if (interval > 1) return `Hace ${interval} meses`
    if (interval === 1) return 'Hace 1 mes'

    interval = Math.floor(seconds / 86400)
    if (interval > 1) return `Hace ${interval} días`
    if (interval === 1) return 'Hace 1 día'

    interval = Math.floor(seconds / 3600)
    if (interval > 1) return `Hace ${interval} horas`
    if (interval === 1) return 'Hace 1 hora'

    interval = Math.floor(seconds / 60)
    if (interval > 1) return `Hace ${interval} minutos`
    if (interval === 1) return 'Hace 1 minuto'

    return 'Justo ahora'
}
