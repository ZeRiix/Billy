document.addEventListener("DOMContentLoaded", function(){
	const accordionHeaders = document.querySelectorAll(".accordion");

	accordionHeaders.forEach(header => {
		header.addEventListener("click", function(){
			this.classList.toggle("text-white");

			const content = this.querySelector(".content");
			content.classList.toggle("hidden");

			const icon = this.querySelectorAll("span")[this.querySelectorAll("span").length - 1];
			icon.classList.toggle("mdi-chevron-up");
			icon.classList.toggle("mdi-chevron-down");
		});
	});
});
