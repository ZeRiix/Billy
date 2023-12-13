const flashesBox = document.getElementById("flashes-box");
const flashes = document.querySelectorAll(".flash");
let autoClose;

// Auto close flash messages after 5 seconds
if(flashesBox){
	autoClose = setTimeout(() => {
		flashes.forEach((flash) => {
	  flash.remove();
		});
	}, 5000);
}

const closeBtns = document.querySelectorAll(".close-btn");

// Close flash message on click
closeBtns.forEach((closeBtn) => {
	closeBtn.addEventListener("click", (e) => {
		e.target.parentElement.remove();
		clearTimeout(autoClose);
	});
});
