console.log("accordion.js loaded");

document.addEventListener("DOMContentLoaded", function(){
	const accordionHeaders = document.querySelectorAll(".bg-bdark-white");

	accordionHeaders.forEach(header => {
		header.addEventListener("click", function(){
			const content = this.querySelector(".content");
			content.classList.toggle("hidden");

			const icon = this.querySelector("span");
			icon.classList.toggle("mdi-chevron-down");
			icon.classList.toggle("mdi-chevron-up");
		});
	});
});
