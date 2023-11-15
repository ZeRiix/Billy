/** @type {import('tailwindcss').Config} */
module.exports = {
	content: [
		"./assets/**/*.{js,vue}",
		"./templates/**/*.html.twig",
	],
	theme: {
		extend: {
			borderRadius: {
				"large": "140px"
			},
			colors: {
				"darker-green": "#374B43",
				"green": "#87B5A2",
				"lighter-green": "#B1C3BB",
				"darker-white": "#EFEFEF",
			},
		}
	},
	plugins: []
};
