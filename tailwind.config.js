/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./assets/**/*.js",
        "./templates/**/*.html.twig",
        "./node_modules/flowbite/**/*.js",
        "./node_modules/tom-select/**/*.js",

        // Enums
        "./src/Core/Enum/AlertStatus.php",
        "./src/Domain/Blog/Enum/PublicationStatus.php",
        "./src/Domain/Project/Enum/ProjectType.php",
    ],
    theme: {
        extend: {},
    },
    plugins: [
      require('flowbite/plugin')
    ],
}
