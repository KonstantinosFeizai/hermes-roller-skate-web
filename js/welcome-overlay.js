// welcome-overlay.js
// Purpose: Show a short welcome overlay, then fade it out and re-enable scrolling.
document.addEventListener("DOMContentLoaded", () => {
  const overlay = document.getElementById("welcome-overlay");
  if (!overlay) return;

  // Show overlay and disable scrolling
  document.body.style.overflow = "hidden";

  setTimeout(() => {
    overlay.classList.add("animate__fadeOut");
    // Wait for fade-out animation to finish, then hide overlay and re-enable scrolling
    overlay.addEventListener(
      "animationend",
      (event) => {
        // Ensure the fadeOut animation ended
        if (event.animationName === "fadeOut") {
          overlay.style.display = "none";
          document.body.style.overflow = "auto";
        }
      },
      { once: true },
    ); // ensure the event listener is removed after it's called
  }, 4300); // timeout for 4.3 seconds
});
