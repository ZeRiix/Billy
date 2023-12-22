/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ["./assets/**/*.{js,vue}", "./templates/**/*.html.twig", "./src/Form/**/*.php"],
    theme: {
        extend: {
            borderRadius: {
                large: "140px",
            },
            colors: {
                "bdarker-green": "#374B43",
                bgreen: "#87B5A2",
                "blighter-green": "#B1C3BB",
                "bdark-white": "#EFEFEF",
                "bdarker-white": "#D3D3D3",
                bgrey: "#71717A",
                "blighter-grey": "#768CA0",
            },
        },
    },
    plugins: [],
};
