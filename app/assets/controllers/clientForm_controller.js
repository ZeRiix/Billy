import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

	connect() {
		let checkboxInput = this.element.querySelector('#toggle-button-element');
		checkboxInput.addEventListener('change', () => this.toggleField(checkboxInput.checked));
	}

	toggleField(isChecked) {
		const hiddenField = document.querySelectorAll('.hidden-field');

		hiddenField.forEach((field) => {
			if (isChecked) {
				field.classList.remove('hidden');
			} else {
				field.classList.add('hidden');
			}
		});
	}
}