/**
 * Script para página de login
 * Usa los módulos compartidos de auth y utils
 */

import '../../assets/css/tailwind.css'
import { supabase } from '../../config/supabase-client.js'
import { AuthManager } from './auth.js'
import { showError, redirectByRole } from '../../shared/scripts/utils.js'

const authManager = new AuthManager(supabase)

// Verificar si ya está autenticado
authManager.isAuthenticated().then(isAuth => {
    if (isAuth) {
        redirectByRole(supabase)
    }
})

// Manejar submit del formulario
document.getElementById('login-form').addEventListener('submit', handleLogin)

// Manejar visibilidad de contraseña
const togglePasswordBtn = document.getElementById('toggle-password')
if (togglePasswordBtn) {
    togglePasswordBtn.addEventListener('click', () => {
        const passwordInput = document.getElementById('password')
        const icon = togglePasswordBtn.querySelector('.material-symbols-outlined')

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text'
            icon.textContent = 'visibility_off'
        } else {
            passwordInput.type = 'password'
            icon.textContent = 'visibility'
        }
    })
}

async function handleLogin(e) {
    e.preventDefault()

    const email = document.getElementById('email').value
    const password = document.getElementById('password').value
    const submitBtn = document.getElementById('submit-btn')
    const btnText = document.getElementById('btn-text')
    const errorDiv = document.getElementById('error-message')
    const errorText = document.getElementById('error-text')

    // Ocultar error previo
    errorDiv.classList.add('hidden')

    // Deshabilitar botón
    submitBtn.disabled = true
    btnText.textContent = 'Verificando...'

    try {
        // Intentar login
        await authManager.login(email, password)

        // Actualizar UI
        btnText.textContent = 'Redirigiendo...'

        // Redirigir según rol
        await redirectByRole(supabase)

    } catch (error) {
        console.error('Error:', error)

        // Mostrar error con diseño nuevo
        let errorMessage = 'Error al iniciar sesión'

        if (error.message === 'Invalid login credentials') {
            errorMessage = 'Credenciales inválidas. Verifica tu correo y contraseña.'
        } else if (error.message.includes('Email not confirmed')) {
            errorMessage = 'Debes confirmar tu correo electrónico primero.'
        } else {
            errorMessage = error.message
        }

        errorText.textContent = errorMessage
        errorDiv.classList.remove('hidden')

        // Rehabilitar botón
        submitBtn.disabled = false
        btnText.textContent = 'Iniciar Sesión'
    }
}
