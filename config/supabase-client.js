import { createClient } from '@supabase/supabase-js'

const SUPABASE_URL = import.meta.env.VITE_SUPABASE_URL
const SUPABASE_ANON_KEY = import.meta.env.VITE_SUPABASE_ANON_KEY

// Inicializar Supabase Client
export const supabase = createClient(SUPABASE_URL, SUPABASE_ANON_KEY)

// Helper: Obtener usuario actual
export async function getCurrentUser() {
    const { data: { user }, error } = await supabase.auth.getUser()
    if (error) throw error
    return user
}

// Helper: Verificar si usuario está autenticado
export async function isAuthenticated() {
    const { data: { session } } = await supabase.auth.getSession()
    return !!session
}

// Helper: Obtener datos del usuario desde tabla usuarios
export async function getUserProfile() {
    const user = await getCurrentUser()
    const { data, error } = await supabase
        .from('usuarios')
        .select('*, rol:roles(nombre)')
        .eq('auth_id', user.id)
        .single()

    if (error) throw error
    return data
}
