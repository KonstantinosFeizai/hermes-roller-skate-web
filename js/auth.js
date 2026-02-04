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

  const openSignupLinks = document.querySelectorAll("#openSignupLink");
  const openLoginLinks = document.querySelectorAll(
    "#openLoginLink, #openLoginLinkBottom",
  );
  const closeSuccessBtn = document.getElementById("closeSuccessBtn");

  // ----------------------------------------------------
  // 2. ΛΕΙΤΟΥΡΓΙΕΣ ΑΝΟΙΓΜΑΤΟΣ / ΚΛΕΙΣΙΜΑΤΟΣ / ΕΝΑΛΛΑΓΗΣ
  // ----------------------------------------------------

  const showModal = () => {
    modal.style.display = "flex"; // Χρησιμοποιήσατε display: flex στο CSS
    document.body.classList.add("modal-open"); // Προαιρετικό: για να κλειδώσει το scroll
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
    document.getElementById("signupGeneralError").textContent = "";
  };

  const showLoginForm = () => {
    loginFormContainer.classList.remove("hidden");
    signupFormContainer.classList.add("hidden");
    signupSuccess.classList.add("hidden");
    document.getElementById("loginGeneralError").textContent = "";
  };

  const resetModal = () => {
    // Καθαρισμός όλων των φορμών και μηνυμάτων
    loginFormContainer.reset();
    signupFormContainer.reset();
    showLoginForm(); // Εμφάνιση της φόρμας Login ως default
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

  // Κλείσιμο όταν πατηθεί έξω από το Modal Content
  window.addEventListener("click", (event) => {
    if (event.target === modal) {
      closeModal();
    }
  });

  // Εναλλαγή Φορμών
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

  // Βοηθητική συνάρτηση για την εμφάνιση σφαλμάτων
  const displayError = (formId, fieldName, message) => {
    const errorElement = document.getElementById(formId + "GeneralError");
    if (errorElement) {
      errorElement.textContent = message;
      errorElement.style.display = "block";
    }
    // Προαιρετικά: Μπορείτε να προσθέσετε λογική για να δείχνει σφάλμα σε συγκεκριμένα πεδία
  };

  // ----------------------------------------------------
  // 4. SIGNUP LOGIC
  // ----------------------------------------------------
  const BASE_URL = window.BASE_URL || "/"; // Παίρνουμε τη BASE_URL

  signupFormContainer.addEventListener("submit", async (e) => {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    // Απενεργοποίηση κουμπιού και εμφάνιση loading (προαιρετικά)
    document.getElementById("signupBtn").disabled = true;
    document.getElementById("signupGeneralError").style.display = "none";

    try {
      const response = await fetch(BASE_URL + "auth/signup_handler.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      });

      const result = await response.json();

      if (response.ok) {
        // Επιτυχής εγγραφή (Status 201 Created)
        document.getElementById("successEmail").textContent = data.email;
        signupFormContainer.classList.add("hidden");
        signupSuccess.classList.remove("hidden");
        document.getElementById("loginBtn").disabled = false;
      } else {
        // Αποτυχία εγγραφής (Status 400, 409, 500)
        displayError(
          "signupForm",
          "general",
          result.message || "Παρουσιάστηκε άγνωστο σφάλμα.",
        );
      }
    } catch (error) {
      displayError("signupForm", "general", "Σφάλμα σύνδεσης. Δοκιμάστε ξανά.");
    } finally {
      document.getElementById("signupBtn").disabled = false;
    }
  });

  // ----------------------------------------------------
  // 5. LOGIN LOGIC
  // ----------------------------------------------------
  loginFormContainer.addEventListener("submit", async (e) => {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    document.getElementById("loginBtn").disabled = true;
    document.getElementById("loginFormGeneralError").style.display = "none";

    try {
      const response = await fetch(BASE_URL + "auth/login_handler.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      });

      const result = await response.json();

      if (response.ok) {
        // Επιτυχής σύνδεση (Status 200 OK)
        // alert("Welcome, " + result.username + "!"); // Προσωρινή ειδοποίηση
        closeModal();

        if (result.redirect) {
          window.location.href = result.redirect;
        } else {
          // Φόρτωση της σελίδας για να εμφανιστεί το κουμπί Logout
          window.location.reload();
        }
      } else {
        // Αποτυχία σύνδεσης (Status 401 Unauthorized)
        displayError(
          "loginForm",
          "general",
          result.message || "Λάθος όνομα χρήστη ή κωδικός.",
        );
      }
    } catch (error) {
      displayError("loginForm", "general", "Σφάλμα σύνδεσης. Δοκιμάστε ξανά.");
    } finally {
      document.getElementById("loginBtn").disabled = false;
    }
  });

  // ----------------------------------------------------
  // 6. LOGOUT LOGIC (Στο κουμπί του Navbar)
  // ----------------------------------------------------
  const logoutButton = document.getElementById("logout-button");

  if (logoutButton) {
    logoutButton.addEventListener("click", async () => {
      if (!confirm("Είστε σίγουροι ότι θέλετε να αποσυνδεθείτε;")) {
        return;
      }

      try {
        const response = await fetch(BASE_URL + "auth/logout.php", {
          method: "POST", // Χρησιμοποιούμε POST όπως συμφωνήσαμε
        });

        // Επειδή το logout.php επιστρέφει πάντα 200 OK και JSON success
        if (response.ok) {
          // Ανακατεύθυνση στη ρίζα ή επαναφόρτωση
          window.location.href = BASE_URL;
        } else {
          alert("Σφάλμα αποσύνδεσης. Δοκιμάστε να ανανεώσετε τη σελίδα.");
        }
      } catch (error) {
        alert("Αποτυχία επικοινωνίας με τον server.");
      }
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

      const submitButton = form.querySelector('button[type="submit"]');
      submitButton.disabled = true;

      // Καθαρισμός προηγούμενου σφάλματος
      const errorDiv = document.getElementById("forgotPasswordGeneralError");
      if (errorDiv) {
        errorDiv.style.display = "none";
      }

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
          // Επιτυχία: Εμφάνιση μηνύματος επιτυχίας (όχι alert, αλλά μέσα στη φόρμα)
          if (errorDiv) {
            errorDiv.style.color = "green";
            errorDiv.textContent = result.message; // Περιμένουμε μήνυμα επιτυχίας
            errorDiv.style.display = "block";
          }
        } else {
          // Αποτυχία: Εμφάνιση μηνύματος σφάλματος
          if (errorDiv) {
            errorDiv.style.color = "red";
            errorDiv.textContent =
              result.message || "Προέκυψε σφάλμα κατά την αποστολή.";
            errorDiv.style.display = "block";
          }
        }
      } catch (error) {
        console.error("Error:", error);
        if (errorDiv) {
          errorDiv.style.color = "red";
          errorDiv.textContent = "Αδυναμία επικοινωνίας με τον server.";
          errorDiv.style.display = "block";
        }
      } finally {
        submitButton.disabled = false;
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

      const submitButton = form.querySelector('button[type="submit"]');
      submitButton.disabled = true;

      const errorDiv = document.getElementById("resetPasswordGeneralError");
      if (errorDiv) {
        errorDiv.style.display = "none";
      }

      // 1. Έλεγχος αν οι κωδικοί ταιριάζουν
      if (data.password !== data.confirm_password) {
        if (errorDiv) {
          errorDiv.style.color = "red";
          errorDiv.textContent = "Οι κωδικοί πρόσβασης δεν ταιριάζουν.";
          errorDiv.style.display = "block";
        }
        submitButton.disabled = false;
        return;
      }

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
          // 2. Επιτυχία: Ανακατεύθυνση στη σελίδα login/index με μήνυμα
          alert(result.message);
          window.location.href = BASE_URL;
        } else {
          // 3. Αποτυχία: Εμφάνιση σφάλματος από το backend
          if (errorDiv) {
            errorDiv.style.color = "red";
            errorDiv.textContent =
              result.message || "Σφάλμα κατά την αλλαγή κωδικού.";
            errorDiv.style.display = "block";
          }
        }
      } catch (error) {
        console.error("Error:", error);
        if (errorDiv) {
          errorDiv.style.color = "red";
          errorDiv.textContent = "Αδυναμία επικοινωνίας με τον server.";
          errorDiv.style.display = "block";
        }
      } finally {
        submitButton.disabled = false;
      }
    });
  }

  // 9. PROFILE UPDATE LOGIC
  const profileUpdateForm = document.getElementById("profileUpdateForm");
  if (profileUpdateForm) {
    profileUpdateForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const formData = new FormData(profileUpdateForm);
      const data = Object.fromEntries(formData.entries());
      const messageDiv = document.getElementById("profileUpdateMessage");

      try {
        const response = await fetch(
          BASE_URL + "user/profile_update_handler.php",
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data),
          },
        );
        const result = await response.json();

        messageDiv.style.display = "block";
        messageDiv.textContent = result.message;
        messageDiv.style.color = response.ok ? "green" : "red";

        if (response.ok) {
          // Προαιρετικά: ανανέωση σελίδας μετά από 1.5 δευτερόλεπτο για να δει ο χρήστης την αλλαγή στο Navbar
          setTimeout(() => location.reload(), 1500);
        }
      } catch (error) {
        messageDiv.style.display = "block";
        messageDiv.textContent = "Σφάλμα επικοινωνίας.";
        messageDiv.style.color = "red";
      }
    });
  }

  // 10. CHANGE PASSWORD LOGIC
  const changePasswordForm = document.getElementById("changePasswordForm");
  if (changePasswordForm) {
    changePasswordForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const formData = new FormData(changePasswordForm);
      const data = Object.fromEntries(formData.entries());
      const messageDiv = document.getElementById("passwordChangeMessage");

      try {
        const response = await fetch(
          BASE_URL + "user/change_password_handler.php",
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data),
          },
        );
        const result = await response.json();

        messageDiv.style.display = "block";
        messageDiv.textContent = result.message;
        messageDiv.style.color = response.ok ? "green" : "red";

        if (response.ok) changePasswordForm.reset(); // Καθαρισμός φόρμας
      } catch (error) {
        messageDiv.style.display = "block";
        messageDiv.textContent = "Σφάλμα επικοινωνίας.";
        messageDiv.style.color = "red";
      }
    });
  }

  // 15. PERSONAL INFO UPDATE LOGIC
  const personalInfoForm = document.getElementById("personalInfoForm");
  if (personalInfoForm) {
    personalInfoForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const formData = new FormData(personalInfoForm);
      const data = Object.fromEntries(formData.entries());
      const messageDiv = document.getElementById("personalInfoMessage");

      try {
        const response = await fetch(
          BASE_URL + "user/personal_info_handler.php",
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data),
          },
        );
        const result = await response.json();

        messageDiv.style.display = "block";
        messageDiv.textContent = result.message;
        messageDiv.style.color = response.ok ? "green" : "red";
      } catch (error) {
        messageDiv.style.display = "block";
        messageDiv.textContent = "Σφάλμα επικοινωνίας με τον διακομιστή.";
        messageDiv.style.color = "red";
      }
    });
  }
});
