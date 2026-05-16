// js/cookie-banner.js
// Purpose: Διαχείριση cookie consent banner.
//          - Εμφανίζει το banner αν δεν υπάρχει συναίνεση
//          - Στέλνει POST στο api/save_consent.php
//          - Φορτώνει δυναμικά το Google Analytics αν δοθεί συναίνεση

document.addEventListener("DOMContentLoaded", function () {
  // ── Elements ──────────────────────────────────────────────
  const banner = document.getElementById("cookie-banner");
  const btnAccept = document.getElementById("cookie-accept-all");
  const btnReject = document.getElementById("cookie-reject-all");

  // Αν το banner δεν υπάρχει στο DOM (hasConsented() = true από PHP) → τέλος
  if (!banner) return;

  // ── GA Config ─────────────────────────────────────────────
  const GA_ID = "AW-17685814149"; // ← το δικό σου GA ID

  // ── Helpers ───────────────────────────────────────────────

  /**
   * Στέλνει την επιλογή συναίνεσης στο backend και αποκρύπτει το banner.
   * @param {boolean} analytics
   * @param {boolean} third_party
   */
  function saveConsent(analytics, third_party) {
    fetch(window.BASE_URL + "api/save-prefs.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ analytics, third_party }),
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error("HTTP error: " + response.status);
        }
        return response.json();
      })
      .then((data) => {
        if (data.success) {
          hideBanner();

          // Φόρτωσε GA μόνο αν δόθηκε συναίνεση για analytics
          if (data.analytics) {
            loadGoogleAnalytics(GA_ID);
          }
        }
      })
      .catch((error) => {
        console.error("Cookie consent error:", error);
        // Ακόμα και αν αποτύχει το αίτημα, κρύβουμε το banner
        // για να μην εμποδίζει τον χρήστη
        hideBanner();
      });
  }

  /**
   * Φορτώνει δυναμικά το Google Analytics script.
   * Καλείται ΜΟΝΟ μετά από ρητή συναίνεση.
   * @param {string} gaId
   */
  function loadGoogleAnalytics(gaId) {
    // Αποφυγή διπλού φορτώματος
    if (document.getElementById("ga-script")) return;

    const script = document.createElement("script");
    script.id = "ga-script";
    script.async = true;
    script.src = "https://www.googletagmanager.com/gtag/js?id=" + gaId;
    document.head.appendChild(script);

    script.onload = function () {
      window.dataLayer = window.dataLayer || [];
      function gtag() {
        dataLayer.push(arguments);
      }
      window.gtag = gtag;
      gtag("js", new Date());
      gtag("config", gaId);
    };
  }

  /**
   * Αποκρύπτει το banner με animation.
   */
  function hideBanner() {
    banner.style.transition = "transform 0.3s ease, opacity 0.3s ease";
    banner.style.transform = "translateY(100%)";
    banner.style.opacity = "0";

    setTimeout(() => {
      banner.style.display = "none";
    }, 300);
  }

  // ── Event Listeners ───────────────────────────────────────

  // "Αποδοχή Όλων" → analytics + third_party = true
  btnAccept.addEventListener("click", function () {
    saveConsent(true, true);
  });

  // "Μόνο Απαραίτητα" → analytics + third_party = false
  btnReject.addEventListener("click", function () {
    saveConsent(false, false);
  });
});
