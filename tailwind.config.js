/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./assets/**/*.js",
        "./templates/**/*.html.twig",
        "./node_modules/flowbite/**/*.js",
        "./node_modules/tom-select/**/*.js",
        "./vendor/tales-from-a-dev/flowbite-bundle/templates/**/*.html.twig",
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('flowbite/plugin')
  ],
}
