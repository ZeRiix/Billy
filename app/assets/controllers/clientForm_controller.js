import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

	connect() {
		let isEntreprise = this.data.get("entrepriseValue");

		if(isEntreprise !== "null"){
			this.toggleField(isEntreprise === "1")
			return
		}

		let checkboxInput = this.element.querySelector('#clientToggle');
		checkboxInput.addEventListener('change', () => {
			this.toggleField(checkboxInput.checked)
			this.resetField()
		});
		this.toggleField(checkboxInput.checked)
	}

	toggleField(isEntreprise) {
		document.querySelectorAll('.E-field')
		.forEach((field) => {
			if (isEntreprise) {
				field.classList.remove('hidden');
			} else {
				field.classList.add('hidden');
			}
		});

		document.querySelectorAll('.P-field')
		.forEach((field) => {
			if (isEntreprise) {
				field.classList.add('hidden');
			} else {
				field.classList.remove('hidden');
			}
		});
	}

	resetField(){
		this.element.querySelectorAll("input, textarea")
		.forEach((field) => {
			field.value = ""
		})

	}
}