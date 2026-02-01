
import { resolve } from 'path'
import { defineConfig } from 'vite'

export default defineConfig({
    root: '.',
    publicDir: 'public',
    build: {
        outDir: 'dist',
        rollupOptions: {
            input: {
                main: resolve(__dirname, 'index.html'),
                login: resolve(__dirname, 'auth/login.html'),
                admin: resolve(__dirname, 'modules/admin/index.html'),
                teacher: resolve(__dirname, 'modules/teacher/index.html'),
                // Add other entry points as migrated
            }
        }
    },
    server: {
        port: 3000,
        open: true
    }
})
