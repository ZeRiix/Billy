console.log("accordion.js loaded");

document.addEventListener("DOMContentLoaded", function(){
	const accordionHeaders = document.querySelectorAll(".bg-bdark-white");

	accordionHeaders.forEach(header => {
		header.addEventListener("click", function(){
			const content = this.querySelector(".content");
			content.classList.toggle("hidden");

			const icon = this.querySelectorAll("span")[this.querySelectorAll("span").length - 1];
			icon.classList.toggle("mdi-chevron-up");
			icon.classList.toggle("mdi-chevron-down");
		});
	});
});
