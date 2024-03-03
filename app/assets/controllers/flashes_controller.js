import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
	static targets = ["flash", "closeBtn"];

	connect() {
		if (this.hasFlashTarget) {
			this.autoClose = setTimeout(() => {
				this.flashTarget.classList.add("fade-out");
			}, 5000);
		}

		this.closeBtnTargets.forEach((closeBtn) => {
			closeBtn.addEventListener("click", (e) => {
				e.target.parentElement.classList.add("fade-out");
				clearTimeout(this.autoClose);
			});
		});
	}

	removeFlash(e) {
		if (e.propertyName !== "opacity" || !e.target.classList.contains("fade-out")) {
			return;
		}

		e.target.remove();
	}
}