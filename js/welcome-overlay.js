// welcome-overlay.js
// Purpose: Show a short welcome overlay, then fade it out and re-enable scrolling.
document.addEventListener("DOMContentLoaded", () => {
  const overlay = document.getElementById("welcome-overlay");
  const welcomeLogo = document.querySelector(".welcome-logo");
  const HEARTBEAT_ANIMATION = "welcomeHeartbeatSoft";
  const MAIN_REVEAL_DELAY_MS = 200;
  const CROSSFADE_DURATION_MS = 420;
  const CINEMATIC_EASING = "cubic-bezier(0.22, 1, 0.36, 1)";
  let overlayClosed = false;

  const closeOverlay = () => {
    if (overlayClosed) return;
    overlayClosed = true;

    document.body.classList.add("is-revealing-main");
    overlay.style.transition = [
      `opacity ${CROSSFADE_DURATION_MS}ms ${CINEMATIC_EASING}`,
      `filter ${CROSSFADE_DURATION_MS}ms ${CINEMATIC_EASING}`,
      `transform ${CROSSFADE_DURATION_MS}ms ${CINEMATIC_EASING}`,
    ].join(", ");
    overlay.style.opacity = "0";
    overlay.style.filter = "blur(7px)";
    overlay.style.transform = "scale(1.015)";

    setTimeout(() => {
      overlay.style.display = "none";
      document.body.style.overflow = "auto";
      document.body.classList.remove(
        "has-welcome-overlay",
        "is-revealing-main",
      );
    }, CROSSFADE_DURATION_MS);
  };

  if (!overlay) return;

  document.body.classList.add("has-welcome-overlay");

  if (welcomeLogo) {
    welcomeLogo.addEventListener("animationend", (event) => {
      if (event.animationName === "fadeIn") {
        welcomeLogo.classList.remove("animate__fadeIn");
        welcomeLogo.classList.add("welcome-logo-heartbeat");
      }

      if (event.animationName === HEARTBEAT_ANIMATION) {
        setTimeout(closeOverlay, MAIN_REVEAL_DELAY_MS);
      }
    });
  }

  // Show overlay and disable scrolling
  document.body.style.overflow = "hidden";

  // Safety fallback in case some animation event does not fire.
  setTimeout(() => {
    closeOverlay();
  }, 5000);
});
