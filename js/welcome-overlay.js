document.addEventListener("DOMContentLoaded", () => {
    const overlay = document.getElementById("welcome-overlay");
    if (!overlay) return;

    // show overlay and disable scrolling
    document.body.style.overflow = "hidden";

    setTimeout(() => {
        overlay.classList.add("animate__fadeOut");
        // listen for the end of the animation to hide the overlay and re-enable scrolling
        overlay.addEventListener('animationend', (event) => {
            // check if the animation that ended is fadeOut
            if (event.animationName === 'fadeOut') {
                overlay.style.display = "none";
                document.body.style.overflow = "auto";
            }
        }, { once: true }); // ensure the event listener is removed after it's called
    }, 4300); // timeout for 4.3 seconds 
});