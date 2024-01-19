import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
	#orgMode = false
	#professionalOrParticularEelment = null
	#toggleButtonElement = null

	/**
	 * @return {Element}
	 */
	get professionalOrParticularEelment() {
		return this.#professionalOrParticularEelment ??
			(this.#professionalOrParticularEelment = this.element.querySelector("#professional-or-particular-field"))
	}

	/**
	 * @return {Element}
	 */
	get toggleButtonElement() {
		return this.#toggleButtonElement ??
			(this.#toggleButtonElement = this.element.querySelector("#toggle-button-element"))
	}

	toggleField() {
		this.#orgMode = !this.#orgMode
		if (this.#orgMode) {
			this.professionalOrParticularEelment.classList.add("hidden")
		} else {
			this.professionalOrParticularEelment.classList.remove("hidden")
		}
	}

	connect() {
		this.toggleButtonElement.addEventListener("click", this.toggleField.bind(this))
	}
}