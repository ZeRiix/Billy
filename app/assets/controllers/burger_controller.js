import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
	connect() {
		const aside = document.getElementById("aside");

		this.element.addEventListener("click", (e) => {
			aside.classList.toggle("-left-64");
			aside.classList.toggle("left-0");
		});
	}
}