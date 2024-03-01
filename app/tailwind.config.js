/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ["./assets/**/*.{js,vue}", "./templates/**/*.html.twig", "./src/Form/**/*.php"],
    theme: {
        extend: {
            borderRadius: {
                large: "140px",
            },
            colors: {
				// light mode
                "light-darker-green": "#374B43",
                "light-green": "#87B5A2",
                "light-lighter-green": "#B1C3BB",
                "light-dark-white": "#EFEFEF",
                "light-darker-white": "#D3D3D3",
                "light-gray": "#71717A",
                "light-lighter-gray": "#768CA0",
				// dark mode
				"dark-light-black": "#181a1b",
				"dark-gray": "#9f968a80",
				"dark-light-gray": "#9f968a",
				"dark-light-white": "#212425",
				"dark-white": "#e8e6e3",
				"dark-darker-green": "#2c3c36",
				"dark-green": "#3a4c46",
				"dark-lighter-green": "#40685b",

				"dark-text": "#80807f"
            },
        },
    },
	darkMode: 'class',
    plugins: [],
};
