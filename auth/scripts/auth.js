/**
 * Módulo de autenticación con Supabase
 */

import { showError, redirectByRole } from '/shared/scripts/utils.js'

export class AuthManager {
    constructor(supabase) {
        this.supabase = supabase
    }

    /**
     * Iniciar sesión
     */
    async login(email, password) {
        const { data, error } = await this.supabase.auth.signInWithPassword({
            email,
            password
        })

        if (error) throw error
        return data
    }

    /**
     * Cerrar sesión
     */
    async logout() {
        const { error } = await this.supabase.auth.signOut()
        if (error) throw error
        window.location.href = '/auth/login.html'
    }

    /**
     * Verificar si está autenticado
     */
    async isAuthenticated() {
        const { data: { session } } = await this.supabase.auth.getSession()
        return !!session
    }

    /**
     * Obtener usuario actual
     */
    async getCurrentUser() {
        const { data: { user }, error } = await this.supabase.auth.getUser()
        if (error) throw error
        return user
    }

    /**
     * Cambiar contraseña
     */
    async changePassword(newPassword) {
        const { data, error } = await this.supabase.auth.updateUser({
            password: newPassword
        })

        if (error) throw error
        return data
    }

    /**
     * Solicitar reseteo de contraseña
     */
    async requestPasswordReset(email) {
        const { error } = await this.supabase.auth.resetPasswordForEmail(email, {
            redirectTo: `${window.location.origin}/auth/reset-password.html`
        })

        if (error) throw error
    }
}
