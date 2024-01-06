const flashesBox = document.getElementById("flashes-box");
const flashes = document.querySelectorAll(".flash");
const closeBtns = document.querySelectorAll(".close-btn");
let autoClose;

// Auto close flash messages after 5 seconds
if (flashes.length > 0) {
    autoClose = setTimeout(() => {
        flashes[0].style.opacity = 0;
        setTimeout(() => {
            flashes[0].remove();
        }, 700);
    }, 5000);
}

// Close flash message on click
closeBtns.forEach((closeBtn) => {
    closeBtn.addEventListener("click", (e) => {
        e.target.parentElement.style.opacity = 0;
        setTimeout(() => {
            e.target.parentElement.remove();
        }, 700);
        clearTimeout(autoClose);
    });
});
