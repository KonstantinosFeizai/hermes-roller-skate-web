// alerts.js
// Purpose: Dismiss alert banners with a small fade/slide animation.
document.addEventListener("click", (event) => {
  // Find a close button inside an alert
  const closeBtn = event.target.closest('[data-dismiss="alert"]');
  if (!closeBtn) return;

  // Locate the alert container
  const alertEl = closeBtn.closest(".alert");
  if (!alertEl) return;

  // Animate out
  alertEl.style.opacity = "0";
  alertEl.style.transform = "translateY(-4px)";
  alertEl.style.transition = "opacity 150ms ease, transform 150ms ease";

  // Remove after animation
  window.setTimeout(() => {
    alertEl.remove();
  }, 160);
});
