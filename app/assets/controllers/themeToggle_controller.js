import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
	connect() {
		const body = document.querySelector('html');
	
		this.element.addEventListener("click", () => {
			const icon = this.element.querySelector("span");
			icon.classList.toggle("mdi-weather-night");
			icon.classList.toggle("mdi-weather-sunny");
			
			body.classList.toggle("dark");
		});
	}
}