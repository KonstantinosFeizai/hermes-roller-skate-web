// auth.js
document.addEventListener("DOMContentLoaded", () => {
  // ----------------------------------------------------
  // 1. ΒΑΣΙΚΕΣ ΜΕΤΑΒΛΗΤΕΣ DOM
  // ----------------------------------------------------
  const modal = document.getElementById("loginModal");
  const openLoginBtn = document.getElementById("login-modal-open-button"); // Κουμπί στο Navbar
  const closeBtn = document.getElementById("closeModalBtn");

  // Φόρμες και links
  const loginFormContainer = document.getElementById("loginForm");
  const signupFormContainer = document.getElementById("signupForm");
  const signupSuccess = document.getElementById("signupSuccess");
  const termsAcceptanceForm = document.getElementById("termsAcceptanceForm");

  const openSignupLinks = document.querySelectorAll("#openSignupLink");
  const openLoginLinks = document.querySelectorAll(
    "#openLoginLink, #openLoginLinkBottom",
  );
  const closeSuccessBtn = document.getElementById("closeSuccessBtn");

  // ----------------------------------------------------
  // 2. ΛΕΙΤΟΥΡΓΙΕΣ ΑΝΟΙΓΜΑΤΟΣ / ΚΛΕΙΣΙΜΑΤΟΣ / ΕΝΑΛΛΑΓΗΣ
  // ----------------------------------------------------

  const showModal = () => {
    modal.style.display = "flex";
    document.body.classList.add("modal-open");
    resetModal();
  };

  const closeModal = () => {
    modal.style.display = "none";
    document.body.classList.remove("modal-open");
    resetModal();
  };

  const showSignupForm = () => {
    loginFormContainer.classList.add("hidden");
    signupFormContainer.classList.remove("hidden");
    signupSuccess.classList.add("hidden");
    termsAcceptanceForm.classList.add("hidden");
    document.getElementById("signupGeneralError").textContent = "";
  };

  const showLoginForm = () => {
    loginFormContainer.classList.remove("hidden");
    signupFormContainer.classList.add("hidden");
    signupSuccess.classList.add("hidden");
    termsAcceptanceForm.classList.add("hidden");
    document.getElementById("loginGeneralError").textContent = "";
  };

  const showTermsForm = () => {
    loginFormContainer.classList.add("hidden");
    signupFormContainer.classList.add("hidden");
    signupSuccess.classList.add("hidden");
    termsAcceptanceForm.classList.remove("hidden");
  };

  const resetModal = () => {
    loginFormContainer.reset();
    signupFormContainer.reset();
    showLoginForm();
    document
      .querySelectorAll(".error-msg")
      .forEach((el) => (el.textContent = ""));
  };

  // ----------------------------------------------------
  // 3. EVENT LISTENERS
  // ----------------------------------------------------

  if (openLoginBtn) openLoginBtn.addEventListener("click", showModal);
  if (closeBtn) closeBtn.addEventListener("click", closeModal);
  if (closeSuccessBtn) closeSuccessBtn.addEventListener("click", closeModal);

  window.addEventListener("click", (event) => {
    if (event.target === modal) {
      closeModal();
    }
  });

  openSignupLinks.forEach((link) =>
    link.addEventListener("click", (e) => {
      e.preventDefault();
      showSignupForm();
    }),
  );
  openLoginLinks.forEach((link) =>
    link.addEventListener("click", (e) => {
      e.preventDefault();
      showLoginForm();
    }),
  );

  // ----------------------------------------------------
  // 4. SIGNUP LOGIC
  // ----------------------------------------------------
  const BASE_URL = window.BASE_URL || "/";
  const i18n = window.AUTH_I18N || {};

  if (signupFormContainer) {
    signupFormContainer.addEventListener("submit", async (e) => {
      e.preventDefault();

      // Καθαρισμός προηγούμενων σφαλμάτων
      document.querySelectorAll("#signupForm .error-msg").forEach((el) => {
        el.textContent = "";
        el.style.display = "none";
      });

      document.getElementById("signupBtn").disabled = true;

      // Συλλογή δεδομένων φόρμας & pre-v
      const form = e.target;
      const formData = new FormData(form);
      const data = Object.fromEntries(formData.entries());

      data.accepted_terms =
        document.getElementById("acceptTerms")?.checked ?? false;

      const htmlLang = (document.documentElement.lang || "").toLowerCase();
      data.lang = htmlLang.startsWith("el") ? "el" : "en";

      const termsError = document.getElementById("signupTermsError");
      if (!data.accepted_terms) {
        if (termsError) {
          termsError.textContent =
            i18n.terms_required || "You must accept the Terms of Use.";
          termsError.style.display = "block";
        }
        return;
      }
      if (termsError) termsError.style.display = "none";

      document.getElementById("signupBtn").disabled = true;
      document.getElementById("signupGeneralError").style.display = "none";

      try {
        const response = await fetch(BASE_URL + "auth/signup_handler.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(data),
        });

        const result = await response.json();

        if (response.ok) {
          document.getElementById("successEmail").textContent = data.email;
          signupFormContainer.classList.add("hidden");
          signupSuccess.classList.remove("hidden");
          document.getElementById("loginBtn").disabled = false;
        } else {
          const errEl = document.getElementById("signupGeneralError");
          if (errEl) {
            errEl.textContent = result.message || i18n.signup_unknown;
            errEl.style.display = "block";
          }
        }
      } catch (error) {
        const errEl = document.getElementById("signupGeneralError");
        if (errEl) {
          errEl.textContent = i18n.signup_network;
          errEl.style.display = "block";
        }
      } finally {
        document.getElementById("signupBtn").disabled = false;
      }
    });
  }

  // ----------------------------------------------------
  // 5. LOGIN LOGIC
  // ----------------------------------------------------
  if (loginFormContainer) {
    loginFormContainer.addEventListener("submit", async (e) => {
      e.preventDefault();
      const form = e.target;
      const formData = new FormData(form);
      const data = Object.fromEntries(formData.entries());

      document.getElementById("loginBtn").disabled = true;
      document.getElementById("loginGeneralError").style.display = "none";
      document.getElementById("loginGeneralError").textContent = "";

      try {
        const response = await fetch(BASE_URL + "auth/login_handler.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(data),
        });

        const result = await response.json();

        if (response.ok) {
          if (result.needs_terms_acceptance) {
            showTermsForm();
            return;
          }

          closeModal();

          if (result.redirect) {
            window.location.href = result.redirect;
          } else {
            window.location.reload();
          }
        } else {
          const errEl = document.getElementById("loginGeneralError");
          if (errEl) {
            errEl.textContent = result.message || i18n.login_unknown;
            errEl.style.display = "block";
          }
        }
      } catch (error) {
        const errEl = document.getElementById("loginGeneralError");
        if (errEl) {
          errEl.textContent = i18n.login_network;
          errEl.style.display = "block";
        }
      } finally {
        document.getElementById("loginBtn").disabled = false;
      }
    });
  }

  // ── Terms acceptance ──────────────────
  const acceptTermsBtn = document.getElementById("acceptTermsBtn");
  if (acceptTermsBtn) {
    acceptTermsBtn.addEventListener("click", async () => {
      const checkbox = document.getElementById("existingUserTerms");
      const termsError = document.getElementById("existingTermsError");

      if (!checkbox?.checked) {
        if (termsError) {
          termsError.textContent = "Πρέπει να αποδεχτείς τους Όρους Χρήσης.";
          termsError.style.display = "block";
        }
        return;
      }
      if (termsError) termsError.style.display = "none";

      acceptTermsBtn.disabled = true;

      try {
        const response = await fetch(BASE_URL + "api/accept_terms.php", {
          method: "POST",
        });
        const result = await response.json();

        if (response.ok && result.status === "success") {
          closeModal();
          window.location.reload();
        } else {
          if (termsError) {
            termsError.textContent = "Σφάλμα. Παρακαλώ δοκιμάστε ξανά.";
            termsError.style.display = "block";
          }
        }
      } catch (error) {
        if (termsError) {
          termsError.textContent = "Αδυναμία επικοινωνίας με τον server.";
          termsError.style.display = "block";
        }
      } finally {
        acceptTermsBtn.disabled = false;
      }
    });
  }

  // ----------------------------------------------------
  // 6. LOGOUT LOGIC
  // ----------------------------------------------------
  const logoutButton = document.getElementById("logout-button");
  if (logoutButton) {
    logoutButton.addEventListener("click", () => {
      const i18n = window.AUTH_I18N || {};
      const overlay = document.getElementById("logoutConfirmOverlay");
      const titleEl = document.getElementById("logoutModalTitle");
      const msgEl = document.getElementById("logoutModalMsg");
      const okBtn = document.getElementById("logoutConfirmOk");
      const cancelBtn = document.getElementById("logoutConfirmCancel");

      if (!overlay) return;

      titleEl.textContent = i18n.logout_confirm_title || "Log Out";
      msgEl.textContent =
        i18n.logout_confirm_msg || "Are you sure you want to log out?";
      okBtn.textContent = i18n.logout_confirm_ok || "Log Out";
      cancelBtn.textContent = i18n.logout_confirm_cancel || "Cancel";

      overlay.style.display = "flex";

      cancelBtn.onclick = () => {
        overlay.style.display = "none";
      };

      okBtn.onclick = async () => {
        overlay.style.display = "none";
        try {
          const response = await fetch(BASE_URL + "auth/logout.php", {
            method: "POST",
          });
          if (response.ok) {
            window.location.href = BASE_URL;
          } else {
            alert("Σφάλμα αποσύνδεσης. Δοκιμάστε να ανανεώσετε τη σελίδα.");
          }
        } catch (error) {
          alert("Αποτυχία επικοινωνίας με τον server.");
        }
      };
    });
  }

  // ----------------------------------------------------
  // 7. FORGOT PASSWORD LOGIC
  // ----------------------------------------------------
  const forgotPasswordForm = document.getElementById("forgotPasswordForm");
  if (forgotPasswordForm) {
    forgotPasswordForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const form = e.target;
      const formData = new FormData(form);
      const data = Object.fromEntries(formData.entries());

      const submitBtn = document.getElementById("forgotSubmitBtn");
      const btnText = document.getElementById("forgotBtnText");
      const btnSpinner = document.getElementById("forgotBtnSpinner");
      const alertDiv = document.getElementById("forgotPasswordAlert");

      if (alertDiv) alertDiv.style.display = "none";
      if (submitBtn) submitBtn.disabled = true;
      if (btnText) btnText.style.display = "none";
      if (btnSpinner) btnSpinner.style.display = "inline";

      try {
        const response = await fetch(
          BASE_URL + "auth/forgot_password_handler.php",
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data),
          },
        );

        const result = await response.json();

        if (response.ok) {
          const formPanel = document.getElementById("forgotFormPanel");
          const successPanel = document.getElementById("forgotSuccessPanel");
          if (formPanel) formPanel.style.display = "none";
          if (successPanel) successPanel.style.display = "block";
        } else {
          if (alertDiv) {
            alertDiv.className = "alert alert-error";
            alertDiv.textContent =
              result.message || "Προέκυψε σφάλμα κατά την αποστολή.";
            alertDiv.style.display = "block";
          }
        }
      } catch (error) {
        if (alertDiv) {
          alertDiv.className = "alert alert-error";
          alertDiv.textContent = "Αδυναμία επικοινωνίας με τον server.";
          alertDiv.style.display = "block";
        }
      } finally {
        if (submitBtn) submitBtn.disabled = false;
        if (btnText) btnText.style.display = "inline";
        if (btnSpinner) btnSpinner.style.display = "none";
      }
    });
  }

  // ----------------------------------------------------
  // 8. RESET PASSWORD LOGIC
  // ----------------------------------------------------
  const resetPasswordForm = document.getElementById("resetPasswordForm");
  if (resetPasswordForm) {
    resetPasswordForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const form = e.target;
      const formData = new FormData(form);
      const data = Object.fromEntries(formData.entries());

      const submitBtn = document.getElementById("resetSubmitBtn");
      const btnText = document.getElementById("resetBtnText");
      const btnSpinner = document.getElementById("resetBtnSpinner");
      const alertDiv = document.getElementById("resetPasswordAlert");

      if (alertDiv) alertDiv.style.display = "none";

      if (data.password !== data.confirm_password) {
        if (alertDiv) {
          alertDiv.className = "alert alert-error";
          alertDiv.textContent = "Οι κωδικοί πρόσβασης δεν ταιριάζουν.";
          alertDiv.style.display = "block";
        }
        return;
      }

      if (data.password.length < 8) {
        if (alertDiv) {
          alertDiv.className = "alert alert-error";
          alertDiv.textContent =
            "Ο κωδικός πρέπει να έχει τουλάχιστον 8 χαρακτήρες.";
          alertDiv.style.display = "block";
        }
        return;
      }

      if (submitBtn) submitBtn.disabled = true;
      if (btnText) btnText.style.display = "none";
      if (btnSpinner) btnSpinner.style.display = "inline";

      try {
        const response = await fetch(
          BASE_URL + "auth/reset_password_handler.php",
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data),
          },
        );

        const result = await response.json();

        if (response.ok) {
          const overlay = document.getElementById("resetSuccessOverlay");
          const btn = document.getElementById("resetSuccessBtn");
          if (overlay) overlay.style.display = "flex";
          if (btn)
            btn.onclick = () => {
              window.location.href = BASE_URL;
            };
        } else {
          if (alertDiv) {
            alertDiv.className = "alert alert-error";
            alertDiv.textContent =
              result.message || "Σφάλμα κατά την αλλαγή κωδικού.";
            alertDiv.style.display = "block";
          }
        }
      } catch (error) {
        if (alertDiv) {
          alertDiv.className = "alert alert-error";
          alertDiv.textContent = "Αδυναμία επικοινωνίας με τον server.";
          alertDiv.style.display = "block";
        }
      } finally {
        if (submitBtn) submitBtn.disabled = false;
        if (btnText) btnText.style.display = "inline";
        if (btnSpinner) btnSpinner.style.display = "none";
      }
    });
  }

  // Expose openLoginModal globally
  window.openLoginModal = showModal;
});
