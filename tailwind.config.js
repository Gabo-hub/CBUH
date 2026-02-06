/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./index.html",
        "./modules/**/*.{html,js}",
        "./shared/**/*.{html,js}",
        "./components/**/*.{html,js}",
        "./auth/**/*.{html,js}",
        "./assets/js/**/*.js",
    ],
    theme: {
        extend: {
            colors: {
                'vinotinto': '#4A0E1C',
                'vinotinto-dark': '#2a0810',
                'gold': '#C9A961',
                'beige': '#F5F2EA',
                'beige-dark': '#E9E2D5',
                'charcoal-gray': '#333333',
                'background-dark': '#2a0810',
                'primary-dark': '#4A0E1C',
                'card-dark': '#2a0810',
            }
        },
    },
    plugins: [],
}
