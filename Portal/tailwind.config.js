/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "selector",
    content: [
      "./resources/**/*.blade.php",
      "./resources/**/*.js",
      "./resources/**/*.vue",
    ],
    theme: {
      extend: {
        fontFamily: {
            'RethinkSans' : ["Rethink Sans", "sans-serif"]
        }
      },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('tailwind-scrollbar-hide')
    ],
  }
