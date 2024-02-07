import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
	hidden = true;

	connect() {
		this.element.addEventListener('click', this.toggleField.bind(this));
	}

	toggleField() {
		const hiddenField = document.querySelectorAll('.hidden-field');

		hiddenField.forEach((field) => {
			if (this.hidden) {
				field.classList.remove('hidden');
			} else {
				field.classList.add('hidden');
			}
		});

		this.hidden = !this.hidden;
	}
}