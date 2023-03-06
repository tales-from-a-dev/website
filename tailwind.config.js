/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./assets/**/*.js",
        "./templates/**/*.html.twig",

        // Enums
        "./src/Core/Enum/Alert.php",
        "./src/Domain/Blog/Enum/PublicationStatus.php",
        "./src/Domain/Project/Enum/ProjectType.php",
    ],
    theme: {
        extend: {},
    },
    plugins: [],
}
