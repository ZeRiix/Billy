import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
	connect() {
		this.element.addEventListener("click", () => {
			const content = this.element.querySelector(".content");
			content.classList.toggle("hidden");

			const icon = this.element.querySelectorAll("span")[this.element.querySelectorAll("span").length - 1];
			icon.classList.toggle("mdi-chevron-up");
			icon.classList.toggle("mdi-chevron-down");
		});
	}
}