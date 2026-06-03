
import { resolve } from 'path'
import { defineConfig } from 'vite'
import fs from 'fs'

function copyComponentsPlugin() {
    return {
        name: 'copy-components',
        closeBundle() {
            const targets = [
                { src: 'modules/shared/components', dest: 'dist/modules/shared/components' },
                { src: 'modules/admin/components', dest: 'dist/modules/admin/components' },
                { src: 'modules/teacher/components', dest: 'dist/modules/teacher/components' },
                { src: 'modules/student/components', dest: 'dist/modules/student/components' }
            ]

            targets.forEach(({ src, dest }) => {
                const srcPath = resolve(__dirname, src)
                const destPath = resolve(__dirname, dest)

                if (fs.existsSync(srcPath)) {
                    fs.mkdirSync(destPath, { recursive: true })
                    fs.cpSync(srcPath, destPath, { recursive: true })
                    console.log(`[copy-components] Copied ${src} to ${dest}`)
                }
            })
        }
    }
}

export default defineConfig({
    root: '.',
    publicDir: 'public',
    plugins: [copyComponentsPlugin()],
    build: {
        outDir: 'dist',
        rollupOptions: {
            input: {
                main: resolve(__dirname, 'index.html'),
                login: resolve(__dirname, 'auth/login.html'),
                admin: resolve(__dirname, 'modules/admin/index.html'),
                teacher: resolve(__dirname, 'modules/teacher/index.html'),
                student: resolve(__dirname, 'modules/student/index.html'),
            }
        }
    },
    server: {
        port: 3000,
        open: true
    }
})

