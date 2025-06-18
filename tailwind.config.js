/** @type {import("tailwindcss").Config} */

module.exports = {
  content: [
    // JS
    "./assets/**/*.js",

    // Twig
    "./templates/**/*.html.twig",
    "./vendor/tales-from-a-dev/flowbite-bundle/templates/**/*.html.twig",

    // PHP Form
    "./src/Ui/Form/**/*.php",
  ],
  theme: {
    extend: {
      fontFamily: {
        body: [
          "Inter",
          "-apple-system",
          "system-ui",
          "ui-sans-serif",
          "sans-serif",
          "Apple Color Emoji",
          "Segoe UI Emoji",
          "Segoe UI Symbol",
          "Noto Color Emoji",
        ],
        sans: [
          "Inter",
          "-apple-system",
          "system-ui",
          "ui-sans-serif",
          "sans-serif",
          "Apple Color Emoji",
          "Segoe UI Emoji",
          "Segoe UI Symbol",
          "Noto Color Emoji",
        ],
      },
    }
  },
  plugins: []
};
