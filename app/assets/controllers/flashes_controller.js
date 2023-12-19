import {Controller} from "@hotwired/stimulus";

export default class extends Controller{
	connect(){
		const flashes = document.querySelectorAll(".flash");
		let autoClose;
		
		// Auto close flash messages after 5 seconds
		if(this.element && flashes.length > 0){
			autoClose = setTimeout(() => {
				flashes[0].style.opacity = 0;
				setTimeout(() => {
					flashes[0].remove();
				}
				, 700);
			}, 5000);
		}

		const closeBtns = document.querySelectorAll(".close-btn");

		// Close flash message on click
		closeBtns.forEach((closeBtn) => {
			closeBtn.addEventListener("click", (e) => {
				e.target.parentElement.style.opacity = 0;
				setTimeout(() => {
					e.target.parentElement.remove();
				}
				, 700);
				clearTimeout(autoClose);
			});
		});
	}
}
