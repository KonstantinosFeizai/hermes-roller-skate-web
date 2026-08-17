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

function closeAlertModal() {
  const modal = document.getElementById("alertModalOverlay");
  if (!modal) return;

  // Fade out animation
  modal.style.transition = "opacity 0.2s ease, transform 0.2s ease";
  modal.style.opacity = "0";

  setTimeout(() => {
    modal.remove();
  }, 200);
}

// Κλείσιμο αν ο χρήστης κάνει κλικ έξω από την κάρτα (στο overlay)
document.addEventListener("click", (event) => {
  const modal = document.getElementById("alertModalOverlay");
  if (modal && event.target === modal) {
    closeAlertModal();
  }
});
