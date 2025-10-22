/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./src/**/*.{html,js,svelte,ts}",
    "./node_modules/flowbite-svelte/**/*.{html,js}",
    './node_modules/flowbite/**/*.{html,js}'
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'], // gunakan Inter
      },
    },
  },
  plugins: [
    require('flowbite/plugin')
  ],
};
