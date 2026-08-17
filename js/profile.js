// js/profile.js
// Purpose: Χειρισμός όλων των φορμών του profile page.
//          - Settings (username/email)
//          - Password change
//          - Personal info
//          - Role type
//          - Athletes (load, add, edit, delete)

document.addEventListener("DOMContentLoaded", () => {
  // ── Helpers ──────────────────────────────────────────────

  function showMsg(id, msg, success = true) {
    const el = document.getElementById(id);
    if (!el) return;
    el.textContent = msg;
    el.style.display = "block";
    el.style.color = success ? "#27ae60" : "#e74c3c";
    el.style.background = success ? "#eafaf1" : "#fdf2f2";
    el.style.padding = "10px 14px";
    el.style.borderRadius = "8px";
    setTimeout(() => {
      el.style.display = "none";
    }, 4000);
  }

  async function postJSON(url, data) {
    const res = await fetch(BASE_URL + url, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    });
    return res.json();
  }

  // ── Settings Form (username / email) ─────────────────────

  const profileUpdateForm = document.getElementById("profileUpdateForm");
  const toggleSettingsBtn = document.getElementById("toggleSettingsBtn");

  if (toggleSettingsBtn && profileUpdateForm) {
    toggleSettingsBtn.addEventListener("click", () => {
      const inputs = profileUpdateForm.querySelectorAll("input");
      const submitBtn = document.getElementById("settingsSubmitBtn");
      const editIcon = document.getElementById("settingsEditIcon");

      const isLocked = inputs[0].hasAttribute("disabled");

      if (isLocked) {
        inputs.forEach((input) => {
          input.dataset.oldValue = input.value;
          input.removeAttribute("disabled");
        });
        if (submitBtn) submitBtn.style.display = "block";
        if (editIcon) editIcon.className = "fa-solid fa-xmark";
        toggleSettingsBtn.setAttribute("title", "Cancel Editing");
      } else {
        inputs.forEach((input) => {
          if (input.dataset.oldValue !== undefined) {
            input.value = input.dataset.oldValue;
          }
          input.setAttribute("disabled", "true");
        });
        if (submitBtn) submitBtn.style.display = "none";
        if (editIcon) editIcon.className = "fa-regular fa-pen-to-square";
        toggleSettingsBtn.setAttribute("title", "Edit Settings");
      }
    });
  }

  if (profileUpdateForm) {
    profileUpdateForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const submitBtn = document.getElementById("settingsSubmitBtn");

      const usernameVal = document.getElementById("username").value.trim();
      const emailVal = document.getElementById("email").value.trim();

      // 💡 Έλεγχος αν δεν άλλαξε τίποτα
      const usernameInput = document.getElementById("username");
      const emailInput = document.getElementById("email");
      if (
        usernameInput.dataset.oldValue === usernameVal &&
        emailInput.dataset.oldValue === emailVal
      ) {
        // Απλώς κλειδώνουμε τη φόρμα χωρίς να στείλουμε request
        const inputs = profileUpdateForm.querySelectorAll("input");
        inputs.forEach((input) => input.setAttribute("disabled", "true"));
        if (submitBtn) submitBtn.style.display = "none";
        const editIcon = document.getElementById("settingsEditIcon");
        if (editIcon) editIcon.className = "fa-regular fa-pen-to-square";
        if (toggleSettingsBtn)
          toggleSettingsBtn.setAttribute("title", "Edit Settings");
        return;
      }

      try {
        const data = await postJSON("user/profile_update_handler.php", {
          username: usernameVal,
          email: emailVal,
        });

        let msgText = "";
        if (data.status === "success") {
          msgText =
            window.PT?.settings_success || "Το προφίλ ενημερώθηκε επιτυχώς!";
        } else {
          switch (data.code) {
            case "REQUIRED_FIELDS_MISSING":
              msgText =
                window.PT?.settings_required_fields ||
                "Όλα τα πεδία είναι υποχρεωτικά.";
              break;
            case "INVALID_EMAIL_FORMAT":
              msgText =
                window.PT?.settings_invalid_email || "Μη έγκυρη μορφή email.";
              break;
            case "EMAIL_EXISTS":
              msgText =
                window.PT?.settings_email_exists ||
                "Αυτό το email χρησιμοποιείται ήδη από άλλον λογαριασμό.";
              break;
            case "USERNAME_EXISTS":
              msgText =
                window.PT?.settings_username_exists ||
                "Αυτό το όνομα χρήστη (username) χρησιμοποιείται ήδη.";
              break;
            default:
              msgText =
                data.message ||
                window.PT?.error_generic ||
                "Σφάλμα κατά την αποθήκευση.";
          }
        }

        showMsg("profileUpdateMessage", msgText, data.status === "success");

        if (data.status === "success") {
          const inputs = profileUpdateForm.querySelectorAll("input");
          inputs.forEach((input) => {
            delete input.dataset.oldValue;
            input.setAttribute("disabled", "true");
          });
          if (submitBtn) submitBtn.style.display = "none";

          const editIcon = document.getElementById("settingsEditIcon");
          if (editIcon) editIcon.className = "fa-regular fa-pen-to-square";
          if (toggleSettingsBtn)
            toggleSettingsBtn.setAttribute("title", "Edit Settings");
        }
      } catch (err) {
        showMsg(
          "profileUpdateMessage",
          window.PT?.connection_error || "Αδυναμία σύνδεσης.",
          false,
        );
      }
    });
  }

  // ── Password Change Form ──────────────────────────────────

  const changePasswordForm = document.getElementById("changePasswordForm");
  const togglePasswordBtn = document.getElementById("togglePasswordBtn");

  if (togglePasswordBtn && changePasswordForm) {
    togglePasswordBtn.addEventListener("click", () => {
      const inputs = changePasswordForm.querySelectorAll(
        'input[type="password"]',
      );
      const submitBtn = document.getElementById("passwordSubmitBtn");
      const editIcon = document.getElementById("passwordEditIcon");

      const isLocked = inputs[0].hasAttribute("disabled");

      if (isLocked) {
        inputs.forEach((input) => input.removeAttribute("disabled"));
        if (submitBtn) submitBtn.style.display = "block";
        if (editIcon) editIcon.className = "fa-solid fa-xmark";
        togglePasswordBtn.setAttribute("title", "Cancel Change");
      } else {
        inputs.forEach((input) => input.setAttribute("disabled", "true"));
        if (submitBtn) submitBtn.style.display = "none";
        if (editIcon) editIcon.className = "fa-regular fa-pen-to-square";
        togglePasswordBtn.setAttribute("title", "Change Password");

        changePasswordForm.reset();
      }
    });
  }

  if (changePasswordForm) {
    changePasswordForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const submitBtn = document.getElementById("passwordSubmitBtn");
      const inputs = changePasswordForm.querySelectorAll(
        'input[type="password"]',
      );

      // Αποτροπή διπλής υποβολής
      if (submitBtn) submitBtn.disabled = true;

      try {
        const data = await postJSON("user/change_password_handler.php", {
          current_password: inputs[0].value,
          new_password: inputs[1].value,
          confirm_new_password: inputs[2].value,
        });

        let msgText = "";
        if (data.status === "success") {
          msgText =
            window.PT?.pass_success || "Ο κωδικός πρόσβασης άλλαξε επιτυχώς!";
        } else {
          switch (data.code) {
            case "REQUIRED_FIELDS_MISSING":
              msgText =
                window.PT?.pass_all_fields_required ||
                "Όλα τα πεδία είναι υποχρεωτικά.";
              break;
            case "INCORRECT_CURRENT":
              msgText =
                window.PT?.pass_incorrect_current ||
                "Ο τρέχων κωδικός είναι εσφαλμένος.";
              break;
            case "PASSWORDS_MISMATCH":
              msgText =
                window.PT?.pass_mismatch || "Οι νέοι κωδικοί δεν ταιριάζουν.";
              break;
            case "PASSWORD_TOO_SHORT":
              msgText =
                window.PT?.pass_min_length ||
                "Ο κωδικός πρέπει να έχει τουλάχιστον 8 χαρακτήρες.";
              break;
            default:
              msgText =
                data.message ||
                window.PT?.error_generic ||
                "Σφάλμα κατά την αποθήκευση.";
          }
        }

        showMsg("passwordChangeMessage", msgText, data.status === "success");

        if (data.status === "success") {
          changePasswordForm.reset();
          inputs.forEach((input) => input.setAttribute("disabled", "true"));
          if (submitBtn) submitBtn.style.display = "none";

          const editIcon = document.getElementById("passwordEditIcon");
          if (editIcon) editIcon.className = "fa-regular fa-pen-to-square";
          if (togglePasswordBtn)
            togglePasswordBtn.setAttribute("title", "Change Password");
        }
      } catch (err) {
        showMsg(
          "passwordChangeMessage",
          window.PT?.connection_error || "Αδυναμία σύνδεσης.",
          false,
        );
      } finally {
        if (submitBtn) submitBtn.disabled = false;
      }
    });
  }

  // ── Personal Info Form ────────────────────────────────────

  const personalInfoForm = document.getElementById("personalInfoForm");
  const toggleEditBtn = document.getElementById("toggleEditBtn");

  if (toggleEditBtn && personalInfoForm) {
    toggleEditBtn.addEventListener("click", () => {
      const inputs = personalInfoForm.querySelectorAll("input, select");
      const submitBtn = document.getElementById("submitBtn");
      const editIcon = document.getElementById("editIcon");
      const mockTextSpan = document.getElementById("pinfo-mock-text");

      // Determine current state based on first input
      const isLocked = inputs[0].hasAttribute("disabled");

      if (isLocked) {
        // UNLOCK & SAVE original values (including custom select mock text)
        inputs.forEach((input) => {
          input.dataset.oldValue = input.value;
          input.removeAttribute("disabled");
        });
        if (mockTextSpan) {
          mockTextSpan.dataset.oldText = mockTextSpan.innerText;
        }

        if (submitBtn) submitBtn.style.display = "block";

        // Change icon to an 'X' to cancel editing
        if (editIcon) editIcon.className = "fa-solid fa-xmark";
        toggleEditBtn.setAttribute("title", "Cancel Editing");
      } else {
        // LOCK BACK WITHOUT SAVING -> RESTORE old values
        inputs.forEach((input) => {
          if (input.dataset.oldValue !== undefined) {
            input.value = input.dataset.oldValue;
          }
          input.setAttribute("disabled", "true");
        });

        // Restore your custom select box visible label text
        if (mockTextSpan && mockTextSpan.dataset.oldText !== undefined) {
          mockTextSpan.innerText = mockTextSpan.dataset.oldText;
        }

        if (submitBtn) submitBtn.style.display = "none";

        // Change icon back to a pen
        if (editIcon) editIcon.className = "fa-regular fa-pen-to-square";
        toggleEditBtn.setAttribute("title", "Edit Profile");
      }
    });
  }

  if (personalInfoForm) {
    personalInfoForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const submitBtn = document.getElementById("submitBtn");

      try {
        const data = await postJSON("user/personal_info_handler.php", {
          first_name: document.getElementById("first_name").value.trim(),
          last_name: document.getElementById("last_name").value.trim(),
          age: document.getElementById("age").value,
          phone: document.getElementById("phone").value.trim(),
          region: document.getElementById("region").value.trim(),
          location_id: document.getElementById("location_id").value,
        });

        // Επιλογή κατάλληλου μεταφρασμένου μηνύματος βάσει του error code
        let msgText = "";
        if (data.status === "success") {
          msgText =
            window.PT?.pinfo_success ||
            "Personal information updated successfully!";
        } else {
          switch (data.code) {
            case "REQUIRED_FIELDS_MISSING":
              msgText =
                window.PT?.pinfo_required_fields ||
                "Please fill in all required fields.";
              break;
            case "INVALID_DATA":
              msgText =
                window.PT?.pinfo_invalid_data ||
                "Please enter a valid age and training area.";
              break;
            case "INVALID_PHONE":
              msgText =
                window.PT?.pinfo_invalid_phone ||
                "Please enter a valid phone number.";
              break;
            case "MISSING_LOCATION":
              msgText =
                window.PT?.pinfo_missing_location ||
                "Please select a training area.";
              break;
            case "REQUIRED_FIELDS_MISSING":
              msgText =
                window.PT?.pinfo_required_fields ||
                "Please fill in all required fields.";
              break;
            case "DB_ERROR":
              msgText =
                window.PT?.pinfo_db_error || "Database error during save.";
              break;
            default:
              msgText =
                data.message ||
                window.PT?.error_generic ||
                "An error occurred during save.";
          }
        }

        showMsg("personalInfoMessage", msgText, data.status === "success");

        // Αν η ενημέρωση είναι επιτυχής, κλειδώνουμε ξανά τη φόρμα
        if (data.status === "success") {
          const inputs = personalInfoForm.querySelectorAll("input, select");
          const mockTextSpan = document.getElementById("pinfo-mock-text");

          inputs.forEach((input) => {
            delete input.dataset.oldValue;
            input.setAttribute("disabled", "true");
          });
          if (mockTextSpan) delete mockTextSpan.dataset.oldText;

          if (submitBtn) submitBtn.style.display = "none";

          const editIcon = document.getElementById("editIcon");
          if (editIcon) editIcon.className = "fa-regular fa-pen-to-square";
          if (toggleEditBtn)
            toggleEditBtn.setAttribute("title", "Edit Profile");
        }
      } catch (err) {
        showMsg(
          "personalInfoMessage",
          window.PT?.connection_error || "Αδυναμία σύνδεσης.",
          false,
        );
      }
    });
  }
});

// ── Role Type Logic & Toggle Editing ──────────────────────────

document.addEventListener("DOMContentLoaded", () => {
  const toggleRoleEditBtn = document.getElementById("toggleRoleEditBtn");
  const roleEditIcon = document.getElementById("roleEditIcon");
  const saveRoleBtn = document.getElementById("saveRoleBtn");
  const roleRadios = document.querySelectorAll(
    'input[name="profile_role_type"]',
  );

  if (toggleRoleEditBtn) {
    toggleRoleEditBtn.addEventListener("click", () => {
      // Ελέγχουμε αν είναι κλειδωμένο (disabled)
      const isLocked = roleRadios[0].disabled;

      if (isLocked) {
        // --- UNLOCK (Επεξεργασία) ---
        // Αποθήκευση της τρέχουσας επιλογής για ενδεχόμενο Cancel
        const currentChecked = document.querySelector(
          'input[name="profile_role_type"]:checked',
        );
        toggleRoleEditBtn.dataset.oldValue = currentChecked
          ? currentChecked.value
          : "none";

        // Ξεκλείδωμα Radios
        roleRadios.forEach((radio) => (radio.disabled = false));

        // Εμφάνιση Save Button
        saveRoleBtn.style.display = "flex";

        // Αλλαγή Icon σε X (Cancel)
        roleEditIcon.className = "fa-solid fa-xmark";
        toggleRoleEditBtn.title = "Cancel Editing";
      } else {
        // --- CANCEL (Ακύρωση) ---
        const oldValue = toggleRoleEditBtn.dataset.oldValue;

        // Επαναφορά της παλιάς τιμής
        roleRadios.forEach((radio) => {
          radio.checked = radio.value === oldValue;
          radio.disabled = true;
        });

        // Απόκρυψη Save Button & Messages
        saveRoleBtn.style.display = "none";
        const msgEl = document.getElementById("roleMessage");
        if (msgEl) msgEl.style.display = "none";

        // Αλλαγή Icon πίσω σε Pencil
        roleEditIcon.className = "fa-solid fa-pen-to-square";
        toggleRoleEditBtn.title = "Edit Role";
      }
    });
  }
});

// Global Function (καλείται από το onclick του #saveRoleBtn)
async function saveRoleType() {
  const btn = document.getElementById("saveRoleBtn");
  const msgEl = document.getElementById("roleMessage");
  const toggleRoleEditBtn = document.getElementById("toggleRoleEditBtn");
  const roleEditIcon = document.getElementById("roleEditIcon");
  const roleRadios = document.querySelectorAll(
    'input[name="profile_role_type"]',
  );
  const checked = document.querySelector(
    'input[name="profile_role_type"]:checked',
  );

  // 1. Έλεγχος αν επιλέχθηκε ρόλος
  if (!checked) {
    msgEl.textContent =
      window.PT?.select_role_required || "Παρακαλώ επίλεξε έναν ρόλο.";
    msgEl.style.color = "#e74c3c";
    msgEl.style.background = "#fdf2f2";
    msgEl.style.padding = "10px 14px";
    msgEl.style.borderRadius = "8px";
    msgEl.style.display = "block";
    return;
  }

  btn.disabled = true;

  try {
    const res = await fetch(BASE_URL + "api/save_role_type.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ role_type: checked.value }),
    });
    const data = await res.json();

    // 2. Καθορισμός δυναμικού κειμένου ανάλογα με την απόκριση του Backend
    let displayMsg = "";
    if (data.status === "success") {
      displayMsg = window.PT?.saved_success || "Αποθηκεύτηκε επιτυχώς!";
    } else {
      displayMsg =
        data.message ||
        window.PT?.error_generic ||
        "Σφάλμα κατά την αποθήκευση.";
    }

    msgEl.textContent = displayMsg;
    msgEl.style.color = data.status === "success" ? "#27ae60" : "#e74c3c";
    msgEl.style.background = data.status === "success" ? "#eafaf1" : "#fdf2f2";
    msgEl.style.padding = "10px 14px";
    msgEl.style.borderRadius = "8px";
    msgEl.style.display = "block";

    if (data.status === "success") {
      window.USER_ROLE_TYPE = checked.value;

      // Κλειδώνουμε ξανά τα radios
      roleRadios.forEach((radio) => (radio.disabled = true));

      // Απόκρυψη κουμπιού & Επαναφορά Edit Icon
      btn.style.display = "none";
      if (toggleRoleEditBtn && roleEditIcon) {
        delete toggleRoleEditBtn.dataset.oldValue;
        roleEditIcon.className = "fa-solid fa-pen-to-square";
        toggleRoleEditBtn.title = "Edit Role";
      }

      // Ανανέωση λίστας αθλητών
      if (typeof loadAthletes === "function") {
        loadAthletes();
      }

      setTimeout(() => {
        msgEl.style.display = "none";
      }, 3000);
    }
  } catch {
    msgEl.textContent = window.PT?.connection_error || "Αδυναμία σύνδεσης.";
    msgEl.style.color = "#e74c3c";
    msgEl.style.background = "#fdf2f2";
    msgEl.style.padding = "10px 14px";
    msgEl.style.borderRadius = "8px";
    msgEl.style.display = "block";
  } finally {
    btn.disabled = false;
  }
}
// ── Athletes ──────────────────────────────────────────────────

async function loadAthletes() {
  const listEl = document.getElementById("athletes-list");
  const addBtn = document.getElementById("addAthleteBtn");
  if (!listEl) return;

  listEl.innerHTML = `<p style="color:#888">...</p>`;

  try {
    const res = await fetch(BASE_URL + "api/get_athletes.php");
    const data = await res.json();
    const athletes = data.athletes || [];
    const roleType = window.USER_ROLE_TYPE || "none";
    const maxAthletes = roleType === "parent" ? 2 : 1;

    // Ορατότητα κουμπιού προσθήκης
    if (addBtn) {
      const canAdd =
        (roleType === "athlete" || roleType === "parent") &&
        athletes.length < maxAthletes;
      addBtn.style.display = canAdd ? "block" : "none";
    }

    if (athletes.length === 0) {
      listEl.innerHTML = `<p style="color:#888;font-size:.9rem;">
        ${
          roleType === "none" || roleType === "coach"
            ? window.PT?.athlete_no_card_required ||
              "Athlete card is not required for your role."
            : window.PT?.athlete_not_added ||
              "You haven't added an athlete yet."
        }
      </p>`;
      return;
    }

    listEl.innerHTML = athletes
      .map((a) => {
        const name = `${a.first_name} ${a.last_name}`;

        const birth = a.birth_date
          ? `<i class="fa-solid fa-cake-candles"></i> ${a.birth_date}`
          : "";
        const loc = a.location_name
          ? `<i class="fa-solid fa-location-dot"></i> ${a.location_name}`
          : "";
        const shoe = a.shoe_size
          ? `<i class="fa-solid fa-shoe-prints fa-rotate-270"></i> ${a.shoe_size}`
          : "";
        const shirt = a.shirt_size
          ? `<i class="fa-solid fa-shirt"></i> ${a.shirt_size}`
          : "";
        const meta = [birth, loc, shoe, shirt].filter(Boolean).join("  ·  ");

        // Ενδιαφέροντα με μεταφράσεις από το window.PT
        const interests = [];
        if (a.interest_rides)
          interests.push(
            `<span class="athlete-badge">${window.PT?.athlete_interest_rides || "Rides"}</span>`,
          );
        if (a.interest_races)
          interests.push(
            `<span class="athlete-badge">${window.PT?.athlete_interest_races || "Races"}</span>`,
          );
        if (a.interest_ski)
          interests.push(
            `<span class="athlete-badge">${window.PT?.athlete_interest_ski || "Ski"}</span>`,
          );
        if (a.interest_skating)
          interests.push(
            `<span class="athlete-badge">${window.PT?.athlete_interest_skating || "Skating"}</span>`,
          );
        if (a.interest_hockey)
          interests.push(
            `<span class="athlete-badge">${window.PT?.athlete_interest_hockey || "Hockey"}</span>`,
          );

        return `
    <div class="athlete-card" data-id="${a.id}">
        <div class="athlete-card-info">
            <div class="athlete-card-name">
              <span>${name}</span>
              <span class="athlete-id-badge">ID: ${a.id}</span>
            </div>
            <div class="athlete-card-meta">${meta}</div>
            ${
              interests.length
                ? `<div class="athlete-interests-wrapper">${interests.join("")}</div>`
                : ""
            }
        </div>
        <div class="athlete-card-actions">
            <button class="profile-submit-btn athlete-btn-edit"
                onclick="editAthlete(${JSON.stringify(a).replace(/"/g, "&quot;")})">
                <i class="fa-solid fa-pen"></i> ${window.PT?.athletes_edit || "Edit"}
            </button>
            <button class="profile-submit-btn athlete-btn-delete"
                onclick="deleteAthlete(${a.id}, '${name}')">
                <i class="fa-solid fa-trash-can"></i> ${window.PT?.athletes_delete || "Delete"}
            </button>
        </div>
    </div>`;
      })
      .join("");
  } catch {
    listEl.innerHTML = `<p style="color:#e74c3c">${window.PT?.error_generic || "Error loading athletes."}</p>`;
  }
}

function showAthleteForm(title = null) {
  const wrap = document.getElementById("athlete-form-wrap");
  const tEl = document.getElementById("athlete-form-title");
  if (wrap) wrap.style.display = "block";
  if (tEl)
    tEl.textContent = title || window.PT?.athlete_form_add || "Add Athlete";
  wrap?.scrollIntoView({ behavior: "smooth", block: "start" });
}

function cancelAthleteForm() {
  document.getElementById("athlete-form-wrap").style.display = "none";
  clearAthleteForm();
}

function clearAthleteForm() {
  [
    "pf_athlete_id",
    "pf_first_name",
    "pf_last_name",
    "pf_birth_date",
    "pf_phone",
    "pf_shoe_size",
    "pf_amka",
    "pf_afm",
  ].forEach((id) => {
    const el = document.getElementById(id);
    if (el) el.value = "";
  });
  ["pf_rides", "pf_races", "pf_ski", "pf_skating", "pf_hockey"].forEach(
    (id) => {
      const el = document.getElementById(id);
      if (el) el.checked = false;
    },
  );
  const locEl = document.getElementById("pf_location");
  if (locEl) locEl.value = "";
  const shirtEl = document.getElementById("pf_shirt_size");
  if (shirtEl) shirtEl.value = "";

  const msgEl = document.getElementById("athleteFormMsg");
  if (msgEl) msgEl.style.display = "none";
}

function editAthlete(a) {
  document.getElementById("pf_athlete_id").value = a.id;
  document.getElementById("pf_first_name").value = a.first_name || "";
  document.getElementById("pf_last_name").value = a.last_name || "";
  document.getElementById("pf_birth_date").value = a.birth_date || "";
  document.getElementById("pf_phone").value = a.phone || "";
  document.getElementById("pf_shoe_size").value = a.shoe_size || "";
  document.getElementById("pf_amka").value = a.amka || "";
  document.getElementById("pf_afm").value = a.afm || "";

  const locEl = document.getElementById("pf_location");
  if (locEl) locEl.value = a.location_id || "";
  const shirtEl = document.getElementById("pf_shirt_size");
  if (shirtEl) shirtEl.value = a.shirt_size || "";

  document.getElementById("pf_rides").checked = !!a.interest_rides;
  document.getElementById("pf_races").checked = !!a.interest_races;
  document.getElementById("pf_ski").checked = !!a.interest_ski;
  document.getElementById("pf_skating").checked = !!a.interest_skating;
  document.getElementById("pf_hockey").checked = !!a.interest_hockey;

  showAthleteForm(window.PT?.athlete_form_edit || "Edit Athlete");
}

async function saveAthlete() {
  const btn = document.getElementById("saveAthleteBtn");
  const msgEl = document.getElementById("athleteFormMsg");

  // Πεδία
  const first = document.getElementById("pf_first_name").value.trim();
  const last = document.getElementById("pf_last_name").value.trim();
  const phone = document.getElementById("pf_phone").value.trim();
  const locationId = document.getElementById("pf_location").value;

  const showError = (text) => {
    msgEl.textContent = text;
    msgEl.style.color = "#e74c3c";
    msgEl.style.display = "block";
  };

  // 1. Έλεγχος Ονόματος & Επωνύμου
  if (!first || !last) {
    showError(
      window.PT?.val_athlete_name_req || "First and Last name are required.",
    );
    return;
  }

  // 2. Έλεγχος Area of Interest / Training Location
  if (!locationId || locationId === "") {
    showError(
      window.PT?.val_athlete_loc_req || "Please select an area of interest.",
    );
    return;
  }

  // 3. Έλεγχος Τηλεφώνου (αν έχει συμπληρωθεί)
  if (phone !== "") {
    const cleanPhone = phone.replace(/[\s\-\+\(\)]/g, "");
    const isDigitsOnly = /^\d+$/.test(cleanPhone);
    if (!isDigitsOnly || cleanPhone.length < 10) {
      showError(window.PT?.val_athlete_phone_inv || "Invalid phone number.");
      return;
    }
  }

  // Υποβολή
  btn.disabled = true;
  msgEl.style.display = "none";

  const isEdit = !!document.getElementById("pf_athlete_id").value;
  const payload = {
    athlete_id: document.getElementById("pf_athlete_id").value || null,
    first_name: first,
    last_name: last,
    birth_date: document.getElementById("pf_birth_date").value || null,
    phone: phone,
    location_id: locationId,
    shoe_size: document.getElementById("pf_shoe_size").value.trim(),
    shirt_size: document.getElementById("pf_shirt_size").value,
    interest_rides: document.getElementById("pf_rides").checked,
    interest_races: document.getElementById("pf_races").checked,
    interest_ski: document.getElementById("pf_ski").checked,
    interest_skating: document.getElementById("pf_skating").checked,
    interest_hockey: document.getElementById("pf_hockey").checked,
    amka: document.getElementById("pf_amka").value.trim() || null,
    afm: document.getElementById("pf_afm").value.trim() || null,
  };

  try {
    const res = await fetch(BASE_URL + "api/save_athlete.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });
    const data = await res.json();

    if (data.status === "success") {
      msgEl.textContent = isEdit
        ? window.PT?.athlete_updated_success || data.message
        : window.PT?.athlete_saved_success || data.message;
      msgEl.style.color = "#27ae60";
      msgEl.style.display = "block";

      setTimeout(() => {
        cancelAthleteForm();
        loadAthletes();
      }, 1200);
    } else if (data.status === "exists") {
      // Backend indicates an active athlete already exists for this account.
      // Treat as informational success: reload list and prompt user to edit the existing card.
      msgEl.textContent =
        data.message ||
        window.PT?.athlete_exists ||
        "An active athlete already exists.";
      msgEl.style.color = "#f39c12";
      msgEl.style.display = "block";

      // Set hidden athlete id so user can edit if they want
      const pid = document.getElementById("pf_athlete_id");
      if (pid) pid.value = data.athlete_id || "";

      setTimeout(() => {
        cancelAthleteForm();
        loadAthletes();
      }, 1200);
    } else {
      showError(
        data.message || window.PT?.error_generic || "Error while saving.",
      );
    }
  } catch {
    showError(window.PT?.connection_error || "Connection error.");
  } finally {
    btn.disabled = false;
  }
}

async function deleteAthlete(athleteId, name) {
  const confirmMsg = (
    window.PT?.athlete_delete_confirm || 'Delete athlete "%s"?'
  ).replace("%s", name);
  if (!confirm(confirmMsg)) return;

  try {
    const res = await fetch(BASE_URL + "api/delete_athlete.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ athlete_id: athleteId }),
    });
    const data = await res.json();
    if (data.status === "success") {
      loadAthletes();
    } else {
      alert(
        data.message || window.PT?.error_generic || "Error while deleting.",
      );
    }
  } catch {
    alert(window.PT?.connection_error || "Connection error.");
  }
}

// ── Delete Account ────────────────────────────────────────────

function openDeleteAccountModal() {
  document.getElementById("deleteAccountPassword").value = "";
  document.getElementById("deleteAccountError").style.display = "none";
  document.getElementById("deleteAccountModal").style.display = "flex";
  setTimeout(
    () => document.getElementById("deleteAccountPassword").focus(),
    100,
  );
}

function closeDeleteAccountModal() {
  document.getElementById("deleteAccountModal").style.display = "none";
}

async function confirmDeleteAccount() {
  const password = document.getElementById("deleteAccountPassword").value;
  const errorEl = document.getElementById("deleteAccountError");
  const btn = document.getElementById("deleteAccountConfirmBtn");

  if (!password) {
    errorEl.textContent = "Παρακαλώ εισάγετε τον κωδικό σας.";
    errorEl.style.display = "block";
    return;
  }

  btn.disabled = true;
  btn.textContent = "Διαγραφή...";
  errorEl.style.display = "none";

  try {
    const res = await fetch(BASE_URL + "api/delete_account.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ password }),
    });
    const data = await res.json();

    if (data.status === "success") {
      // Redirect στην αρχική με μήνυμα
      window.location.href = data.redirect + "?account_deleted=1";
    } else {
      errorEl.textContent = data.message || "Σφάλμα. Παρακαλώ δοκιμάστε ξανά.";
      errorEl.style.display = "block";
      btn.disabled = false;
      btn.textContent = "🗑️ Οριστική Διαγραφή";
    }
  } catch {
    errorEl.textContent = "Αδυναμία σύνδεσης. Δοκιμάστε ξανά.";
    errorEl.style.display = "block";
    btn.disabled = false;
    btn.textContent = "🗑️ Οριστική Διαγραφή";
  }
}

// Close modal on overlay click
document.addEventListener("click", (e) => {
  if (e.target === document.getElementById("deleteAccountModal")) {
    closeDeleteAccountModal();
  }
});

// ── Tab Navigation ────────────────────────────────────────────
function openTab(tabName, triggerEl = null) {
  document
    .querySelectorAll(".tab-content")
    .forEach((c) => c.classList.remove("active"));
  document
    .querySelectorAll(".tab-btn")
    .forEach((b) => b.classList.remove("active"));
  document.getElementById(tabName).classList.add("active");
  const btn =
    triggerEl ||
    (typeof event !== "undefined" ? event.currentTarget : null) ||
    document.querySelector(`.tab-btn[onclick*="'${tabName}'"]`);
  if (btn) btn.classList.add("active");

  if (tabName === "athletes") loadAthletes();
  if (tabName === "my-finance") loadMyFinance();
  if (tabName === "my-classes") loadMyClasses();
  if (tabName === "inbox") loadInbox();
}

document.addEventListener("DOMContentLoaded", () => {
  if (window.location.hash === "#profile") openTab("profile");
  if (window.location.hash === "#athletes") openTab("athletes");
  if (window.location.hash === "#my-finance") openTab("my-finance");
  if (window.location.hash === "#my-classes") openTab("my-classes");
  if (window.location.hash === "#inbox") openTab("inbox");

  initNotifications();
});

// ── Finance tab ───────────────────────────────────────────────
async function loadMyFinance() {
  const el = document.getElementById("myFinanceContent");
  el.innerHTML = '<p class="loading-msg">Φόρτωση...</p>';
  try {
    const res = await fetch("get_my_finance.php");
    const result = await res.json();
    if (result.status !== "success") {
      el.innerHTML = '<p style="color:red">Σφάλμα.</p>';
      return;
    }
    renderMyFinance(result.data);
  } catch {
    el.innerHTML = '<p style="color:red">Σφάλμα φόρτωσης.</p>';
  }
}

window.athleteFinancePages = window.athleteFinancePages || {};

function renderMyFinance(data) {
  const el = document.getElementById("myFinanceContent");

  const sesSingular = window.PT?.sessions_singular || "session";
  const sesPlural = window.PT?.sessions_plural || "sessions";

  if (!data.length) {
    el.innerHTML = `<p class="pprofile-empty">${PT.no_athletes}</p>`;
    return;
  }

  const typeL = {
    prepaid: PT.type_prepaid,
    free: PT.type_free,
    gift: PT.type_gift,
  };
  const methodL = {
    cash: PT.method_cash,
    card: PT.method_card,
    transfer: PT.method_transfer,
    other: PT.method_other,
  };

  let selectorHtml = `
    <div class="pfin-selector-container">
        <span class="pfin-selector-label">${PT.select_athlete}</span>
        <div class="pfin-selector-pills">
    `;
  data.forEach((d, idx) => {
    const activeClass = idx === 0 ? "active" : "";
    selectorHtml += `
        <button type="button" class="pfin-pill-btn ${activeClass}" onclick="switchAthleteFinance(${idx})">
            ${escH(d.athlete_name)}
        </button>
    `;
  });
  selectorHtml += `</div></div>`;

  const blocksHtml = data
    .map((d, idx) => {
      const b = d.balance;
      const rem = parseInt(b.lessons_remaining || 0);
      const cls = rem > 0 ? "pbal-pos" : rem < 0 ? "pbal-neg" : "pbal-zero";
      const displayStyle = idx === 0 ? "block" : "none";

      if (window.athleteFinancePages[idx] === undefined) {
        window.athleteFinancePages[idx] = 0;
      }

      const ITEMS_PER_PAGE = 4;
      const payments = d.payments || [];
      const totalPages = Math.ceil(payments.length / ITEMS_PER_PAGE);
      const currentPage = window.athleteFinancePages[idx];

      const payRows = payments.length
        ? payments
            .map((p, pIdx) => {
              const dt = new Date(p.payment_date).toLocaleDateString("el-GR", {
                day: "2-digit",
                month: "2-digit",
                year: "numeric",
              });
              const isFree = p.payment_type !== "prepaid";
              const hasReceipt =
                p.receipt_file_path && p.receipt_file_path.trim() !== "";

              const rowPage = Math.floor(pIdx / ITEMS_PER_PAGE);
              const isRowVisible =
                rowPage === currentPage ? "" : 'style="display:none;"';

              return `
            <div class="ppr-row" data-page="${rowPage}" ${isRowVisible}>
                <div class="ppr-left-side">
                    <div class="ppr-icon-wrapper">${p.payment_type === "free" ? "📋" : '<i class="fa-solid fa-plus"></i>'}</div>
                    <div class="ppr-info">
                        <span class="ppr-lessons">+${p.lessons_purchased} ${p.lessons_purchased === 1 ? sesSingular : sesPlural}</span>
                        <div class="ppr-meta"><i class="fa-solid fa-calendar-days"></i> ${dt} · ${escH(typeL[p.payment_type] || p.payment_type)} · ${escH(methodL[p.payment_method] || p.payment_method)}</div>
                    </div>
                </div>
                <div class="ppr-right-side">
                    <div class="ppr-amount-zone">
                        <span class="ppr-amount">${isFree ? PT.fin_free : Number(p.amount).toLocaleString("el-GR", { minimumFractionDigits: 2 }) + " €"}</span>
                        <span class="ppr-status-text badge-green"><i class="fa-regular fa-circle-check" style="color: rgb(99, 230, 190);"></i> ${isFree ? "PROMO APPLIED" : PT.success}</span>
                    </div>
                    ${
                      hasReceipt
                        ? `
                    <div class="ppr-actions">
                        <a href="../api/download_receipt.php?payment_id=${p.id}" target="_blank" class="ppr-download-btn">
                            <img src="../photo/validating-ticket.png" alt="" class="ppr-receipt-icon"> ${PT.receipt}
                        </a>
                    </div>`
                        : ""
                    }
                </div>
            </div>`;
            })
            .join("")
        : `<p class="pprofile-empty">${PT.no_payments}</p>`;

      let paginationControlsHtml = "";
      if (totalPages > 1) {
        let pageNumbersHtml = "";
        for (let pNum = 0; pNum < totalPages; pNum++) {
          if (
            totalPages <= 6 ||
            pNum === 0 ||
            pNum === totalPages - 1 ||
            Math.abs(pNum - currentPage) <= 1
          ) {
            const isPageActive = pNum === currentPage ? "active" : "";
            pageNumbersHtml += `
                    <button type="button" class="pfin-num-btn ${isPageActive}" data-target-page="${pNum}" onclick="jumpToFinancePage(${idx}, ${pNum})">
                        ${pNum + 1}
                    </button>
                `;
          } else if (
            (pNum === 1 && currentPage > 2) ||
            (pNum === totalPages - 2 && currentPage < totalPages - 3)
          ) {
            pageNumbersHtml += `<span class="pfin-page-dots">...</span>`;
            if (pNum === 1) pNum = currentPage - 2;
            else if (pNum === totalPages - 2) break;
          }
        }

        paginationControlsHtml = `
            <div class="pfin-pagination-wrapper">
                <div class="pfin-pagination">
                    <button type="button" class="pfin-nav-btn prev-btn" onclick="changeFinancePage(${idx}, -1)" ${currentPage === 0 ? "disabled" : ""}>
                        <span class="nav-arrow">‹</span> PREV
                    </button>
                    <div class="pfin-num-track">
                        ${pageNumbersHtml}
                    </div>
                    <button type="button" class="pfin-nav-btn next-btn" onclick="changeFinancePage(${idx}, 1)" ${currentPage === totalPages - 1 ? "disabled" : ""}>
                        NEXT <span class="nav-arrow">›</span>
                    </button>
                </div>
            </div>
        `;
      }

      const txtSingular = window.PT?.transactions_singular || "transaction";
      const txtPlural = window.PT?.transactions_plural || "transactions";
      const localizedWord = payments.length === 1 ? txtSingular : txtPlural;

      return `
        <div id="athlete-fin-block-${idx}" class="pfin-athlete-data-block" style="display: ${displayStyle};">
            <div class="pbs-container">
                <div class="pbs-card">
                    <div class="pbs-card-header">
                        <div class="pbs-text-group">
                            <span class="pbs-label">${PT.purchased ? PT.purchased.toUpperCase() : "PURCHASED"}</span>
                            <strong class="pbs-value">${b.lessons_purchased || 0}</strong>
                            <span class="pbs-subtext-bottom">${PT.total_sessions}</span>
                         </div>
                        <div class="pbs-icon-badge badge-gray">
                            <i class="fa-solid fa-cart-shopping"></i>
                         </div>
                      </div>
                  </div>

                <div class="pbs-card">
                    <div class="pbs-card-header">
                        <div class="pbs-text-group">
                            <span class="pbs-label">${PT.used ? PT.used.toUpperCase() : "USED"}</span>
                            <strong class="pbs-value">${String(b.lessons_used || 0).padStart(2, "0")}</strong>
                            <span class="pbs-subtext-bottom">${PT.this_period}</span>
                        </div>
                        <div class="pbs-icon-badge badge-blue">
                            <i class="fa-regular fa-circle-check"></i>
                        </div>
                    </div>
                </div>

                <div class="pbs-card">
                    <div class="pbs-card-header">
                        <div class="pbs-text-group">
                            <span class="pbs-label">${PT.balance ? PT.balance.toUpperCase() : "BALANCE"}</span>
                            <strong class="pbs-value value-green">${rem > 0 ? "+" : ""}${rem}</strong>
                            <span class="pbs-subtext-bottom">${PT.remaining_sessions}</span>
                        </div>
                        <div class="pbs-icon-badge badge-green">
                            <i class="fa-solid fa-arrow-trend-up"></i>
                        </div>
                    </div>
                </div>

                <div class="pbs-card">
                    <div class="pbs-card-header">
                        <div class="pbs-text-group">
                            <span class="pbs-label">${PT.total ? PT.total.toUpperCase() : "TOTAL"}</span>
                            <strong class="pbs-value">${Number(b.total_paid || 0).toLocaleString("el-GR", { minimumFractionDigits: 2 })} €</strong>
                            <span class="pbs-subtext-bottom">${PT.lifetime_value}</span>
                        </div>
                        <div class="pbs-icon-badge badge-orange">
                            <i class="fa-solid fa-euro-sign"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pprofile-pay-list">
                <div class="pfinance-history-header">
                    <h3 class="pfinance-history-title">${PT.payment_history}</h3>
                    <span class="pfinance-txn-badge">${payments.length} ${localizedWord}</span>
                 </div>
                ${payRows}
                ${paginationControlsHtml}
             </div>
        </div>
      `;
    })
    .join("");

  el.innerHTML =
    selectorHtml + `<div class="pfin-blocks-wrapper">${blocksHtml}</div>`;
}

function switchAthleteFinance(activeIndex) {
  document.querySelectorAll(".pfin-pill-btn").forEach((btn, idx) => {
    btn.classList.toggle("active", idx === activeIndex);
  });
  document
    .querySelectorAll(".pfin-athlete-data-block")
    .forEach((block, idx) => {
      block.style.display = idx === activeIndex ? "block" : "none";
    });
}

function changeFinancePage(athleteIdx, direction) {
  const container = document.getElementById(`athlete-fin-block-${athleteIdx}`);
  if (!container) return;

  const rows = container.querySelectorAll(".ppr-row");
  const totalPages = Math.ceil(rows.length / 4);

  let targetPage = (window.athleteFinancePages[athleteIdx] || 0) + direction;
  if (targetPage < 0 || targetPage >= totalPages) return;

  window.athleteFinancePages[athleteIdx] = targetPage;

  rows.forEach((row) => {
    const pageNum = parseInt(row.getAttribute("data-page"));
    row.style.display = pageNum === targetPage ? "flex" : "none";
  });

  const numButtons = container.querySelectorAll(".pfin-num-btn");
  numButtons.forEach((btn) => {
    const btnPage = parseInt(btn.getAttribute("data-target-page"));
    btn.classList.toggle("active", btnPage === targetPage);
  });

  const prevBtn = container.querySelector(".prev-btn");
  const nextBtn = container.querySelector(".next-btn");
  if (prevBtn) prevBtn.disabled = targetPage === 0;
  if (nextBtn) nextBtn.disabled = targetPage === totalPages - 1;
}

function jumpToFinancePage(athleteIdx, targetPage) {
  const currentActivePage = window.athleteFinancePages[athleteIdx] || 0;
  const direction = targetPage - currentActivePage;
  if (direction !== 0) {
    changeFinancePage(athleteIdx, direction);
  }
}

// ── Classes tab ───────────────────────────────────────────────
let classesDataCache = [];
let classesActiveAthleteIdx = 0;
let classesHistoryPages = {};

async function loadMyClasses() {
  const el = document.getElementById("myClassesContent");
  el.innerHTML = '<p class="loading-msg">Φόρτωση...</p>';
  try {
    const res = await fetch("get_my_classes.php");
    const result = await res.json();
    if (result.status !== "success") {
      el.innerHTML = '<p style="color:red">Σφάλμα.</p>';
      return;
    }
    classesDataCache = result.data;
    classesActiveAthleteIdx = 0;
    classesHistoryPages = {};
    renderMyClasses(result.data);
  } catch {
    el.innerHTML = '<p style="color:red">Σφάλμα φόρτωσης.</p>';
  }
}

function renderMyClasses(data) {
  const el = document.getElementById("myClassesContent");
  if (!data || !data.length) {
    el.innerHTML = `<p class="pprofile-empty">${PT.no_athletes}</p>`;
    return;
  }

  const typeIcons = {
    rollers: "🛼",
    iceskate: "⛸️",
    hockey: "🏒",
    ski: "⛷️",
    fitness: "🏋️",
  };
  const statusL = {
    scheduled: PT.status_scheduled,
    completed: PT.status_completed,
    cancelled: PT.status_cancelled,
  };
  const statusCls = {
    scheduled: "pcls-sched",
    completed: "pcls-done",
    cancelled: "pcls-cancel",
  };

  function lessonRow(l) {
    const dt = new Date(l.lesson_datetime).toLocaleDateString("el-GR", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
    });
    const tm = new Date(l.lesson_datetime).toLocaleTimeString("en-US", {
      hour: "2-digit",
      minute: "2-digit",
    });

    return `
        <div class="pcls-new-row">
            <div class="pcls-row-left">
                <div class="pcls-icon-badge">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <div class="pcls-row-meta">
                    <span class="pcls-row-time">${dt} · ${tm}</span>
                    ${l.location_name ? `<span class="pcls-row-loc"><i class="fa-solid fa-location-dot"></i> ${escH(l.location_name)}</span>` : ""}
                </div>
            </div>
            <div class="pcls-row-right">
                <span class="pcls-status-pill ${statusCls[l.status] || ""}">${(statusL[l.status] || l.status).toUpperCase()}</span>
                ${l.attended ? `<span class="pcls-attended-pill"><i class="fa-regular fa-circle-check"></i> ${(PT.attended || "ATTENDED").toUpperCase()}</span>` : ""}
            </div>
        </div>
    `;
  }

  let selectorHtml = "";
  if (data.length > 1) {
    selectorHtml = `
        <div class="pfin-selector-container">
            <span class="pfin-selector-label">${PT.select_athlete}</span>
            <div class="pfin-tabs">
                ${data
                  .map(
                    (d, idx) => `
                    <button class="pfin-pill-btn pcls-pill-btn ${idx === classesActiveAthleteIdx ? "active" : ""}" onclick="switchAthleteClasses(${idx})">
                        ${escH(d.athlete_name)}
                    </button>
                `,
                  )
                  .join("")}
            </div>
        </div>
    `;
  }

  const blocksHtml = data
    .map((d, i) => {
      const completedCount = d.past.filter(
        (l) => l.status === "completed",
      ).length;
      const upcomingCount = d.upcoming.length;

      const itemsPerPage = 4;
      if (classesHistoryPages[i] === undefined) classesHistoryPages[i] = 0;
      const currentPage = classesHistoryPages[i];
      const totalPages = Math.ceil(d.past.length / itemsPerPage);

      const startIdx = currentPage * itemsPerPage;
      const currentHistorySlice = d.past.slice(
        startIdx,
        startIdx + itemsPerPage,
      );

      const upHtml = d.upcoming.length
        ? d.upcoming.map(lessonRow).join("")
        : `<div class="pcls-empty-dashed-box">
            <i class="fa-solid fa-calendar-xmark pcls-empty-icon"></i>
            <span>${PT.no_upcoming}</span>
         </div>`;

      const pastHtml = d.past.length
        ? currentHistorySlice.map(lessonRow).join("")
        : `<div class="pcls-empty-dashed-box">
            <i class="fa-solid fa-clock-rotate-left pcls-empty-icon"></i>
            <span>${PT.no_history}</span>
         </div>`;

      let paginationControlsHtml = "";
      if (totalPages > 1) {
        let pageNumbersHtml = "";
        for (let pNum = 0; pNum < totalPages; pNum++) {
          if (
            totalPages <= 6 ||
            pNum === 0 ||
            pNum === totalPages - 1 ||
            Math.abs(pNum - currentPage) <= 1
          ) {
            const isPageActive = pNum === currentPage ? "active" : "";
            pageNumbersHtml += `
                    <button type="button" class="pfin-num-btn ${isPageActive}" onclick="jumpToClassesPage(${i}, ${pNum})">
                        ${pNum + 1}
                    </button>
                `;
          } else if (
            (pNum === 1 && currentPage > 2) ||
            (pNum === totalPages - 2 && currentPage < totalPages - 3)
          ) {
            pageNumbersHtml += `<span class="pfin-page-dots">...</span>`;
            if (pNum === 1) pNum = currentPage - 2;
            else if (pNum === totalPages - 2) break;
          }
        }

        paginationControlsHtml = `
            <div class="pfin-pagination-wrapper">
                <div class="pfin-pagination">
                    <button type="button" class="pfin-nav-btn" onclick="changeClassesPage(${i}, -1)" ${currentPage === 0 ? "disabled" : ""}>
                        <span class="nav-arrow">‹</span> PREV
                    </button>
                    <div class="pfin-num-track">
                        ${pageNumbersHtml}
                    </div>
                    <button type="button" class="pfin-nav-btn" onclick="changeClassesPage(${i}, 1)" ${currentPage === totalPages - 1 ? "disabled" : ""}>
                        NEXT <span class="nav-arrow">›</span>
                    </button>
                </div>
            </div>
        `;
      }

      const displayStyle = i === classesActiveAthleteIdx ? "block" : "none";

      return `
        <div class="pcls-athlete-data-block" id="pcls-athlete-block-${i}" style="display: ${displayStyle};">
            <div class="pcls-main-card">
                <div class="pcls-card-header">
                    <div class="pcls-avatar-circle">
                        <i class="fa-regular fa-user"></i>
                    </div>
                    <div class="pcls-header-text">
                        <h4 class="pcls-athlete-fullname">${escH(d.athlete_name)}</h4>
                        <span class="pcls-athlete-stats-subtext">${completedCount} ${PT.status_completed} · ${upcomingCount} ${PT.upcoming}</span>
                    </div>
                </div>
                <div class="pcls-section-group">
                    <h5 class="pcls-group-title"><i class="fa-solid fa-calendar-days"></i> ${PT.upcoming}</h5>
                    <div class="pcls-rows-stack">${upHtml}</div>
                </div>
                <div class="pcls-section-group" style="margin-top: 24px;">
                    <h5 class="pcls-group-title"><i class="fa-solid fa-clock-rotate-left"></i> ${PT.history}</h5>
                    <div class="pcls-rows-stack">${pastHtml}</div>
                    ${paginationControlsHtml}
                </div>
            </div>
        </div>`;
    })
    .join("");

  el.innerHTML = selectorHtml + blocksHtml;
}

window.switchAthleteClasses = function (activeIndex) {
  classesActiveAthleteIdx = activeIndex;
  renderMyClasses(classesDataCache);
};

window.jumpToClassesPage = function (athleteIdx, pageNum) {
  classesHistoryPages[athleteIdx] = pageNum;
  renderMyClasses(classesDataCache);
};

window.changeClassesPage = function (athleteIdx, direction) {
  const d = classesDataCache[athleteIdx];
  const itemsPerPage = 4;
  const totalPages = Math.ceil(d.past.length / itemsPerPage);

  let nextPage = (classesHistoryPages[athleteIdx] || 0) + direction;
  if (nextPage >= 0 && nextPage < totalPages) {
    classesHistoryPages[athleteIdx] = nextPage;
    renderMyClasses(classesDataCache);
  }
};

function escH(s) {
  return String(s)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

// ── Notifications bell ───────────────────────────────────────
async function initNotifications() {
  const bellBtn = document.getElementById("notificationBellBtn");
  const dropdown = document.getElementById("notificationDropdown");
  const listEl = document.getElementById("notificationList");
  const countEl = document.getElementById("notificationCount");
  const markAllBtn = document.getElementById("markAllReadBtn");
  const clearAllBtn = document.getElementById("clearAllNotificationsBtn");

  if (!bellBtn || !dropdown || !listEl || !countEl) return;

  bellBtn.addEventListener("click", function (e) {
    e.stopPropagation();
    if (dropdown.style.display === "block") {
      dropdown.style.display = "none";
    } else {
      dropdown.style.display = "block";
      loadNotifications();
    }
  });

  if (markAllBtn) {
    markAllBtn.addEventListener("click", async function (e) {
      e.preventDefault();
      await markAllNotificationsRead();
    });
  }

  if (clearAllBtn) {
    clearAllBtn.addEventListener("click", async function (e) {
      e.preventDefault();
      await clearAllNotifications();
    });
  }

  listEl.addEventListener("click", async function (e) {
    const item = e.target.closest(".notification-item");
    if (!item) return;
    const id = item.dataset.notificationId;
    const type = item.dataset.notificationType;
    await openNotification(id, type);
  });

  document.addEventListener("click", function (e) {
    if (!e.target.closest(".notification-wrap")) {
      dropdown.style.display = "none";
    }
  });

  await loadNotifications();
}

async function loadNotifications() {
  const listEl = document.getElementById("notificationList");
  const countEl = document.getElementById("notificationCount");

  try {
    const res = await fetch(BASE_URL + "api/get_notifications.php");
    const data = await res.json();

    if (data.status !== "success") return;

    const unreadCount = data.unread_count || 0;
    countEl.textContent = unreadCount;
    countEl.style.display = unreadCount > 0 ? "inline-flex" : "none";

    const notifications = data.notifications || [];
    if (!notifications.length) {
      listEl.innerHTML = `
                <div class="notification-empty">
                    <div class="notification-empty-icon">🔕</div>
                    <strong>${PT.no_notifications}</strong>
                    <p>${PT.caught_up}</p>
                </div>
            `;
      return;
    }

    listEl.innerHTML = notifications
      .map(
        (n) => `
            <button type="button" class="notification-item ${n.is_read ? "read" : "unread"}"
                    data-notification-id="${n.id}"
                    data-notification-type="${escH(n.type)}">
                <div class="notification-item-icon">${notificationIcon(n.type)}</div>
                <div class="notification-item-content">
                    <div class="notification-item-title">${escH(n.title)}</div>
                    ${n.body ? `<div class="notification-item-body">${escH(n.body)}</div>` : ""}
                    <div class="notification-item-meta">${timeAgo(n.created_at)}</div>
                </div>
            </button>
        `,
      )
      .join("");
  } catch {}
}

async function openNotification(notificationId, type) {
  if (!notificationId) return;

  try {
    await fetch(BASE_URL + "api/mark_notification_read.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ notification_id: parseInt(notificationId, 10) }),
    });
  } catch {}

  const map = {
    new_message: "inbox",
    message_reply: "inbox",
    payment_added: "my-finance",
    new_class: "my-classes",
    negative_balance: "my-finance",
    receipt_uploaded: "my-finance",
  };

  const tab = map[type];
  if (tab) openTab(tab);

  const dropdown = document.getElementById("notificationDropdown");
  if (dropdown) dropdown.style.display = "none";
  await loadNotifications();
}

async function markAllNotificationsRead() {
  try {
    const res = await fetch(BASE_URL + "api/get_notifications.php");
    const data = await res.json();
    if (data.status !== "success") return;

    const unread = (data.notifications || []).filter((n) => !n.is_read);
    for (const n of unread) {
      try {
        const markRes = await fetch(
          BASE_URL + "api/mark_notification_read.php",
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ notification_id: n.id }),
          },
        );
        if (!markRes.ok) {
          console.error(
            `Failed to mark notification ${n.id} as read:`,
            markRes.status,
            markRes.statusText,
          );
        }
      } catch (err) {
        console.error(`Error marking notification ${n.id} as read:`, err);
      }
    }

    await loadNotifications();
  } catch (err) {
    console.error("Error in markAllNotificationsRead:", err);
  }
}

async function clearAllNotifications() {
  try {
    const res = await fetch(BASE_URL + "api/get_notifications.php");
    const data = await res.json();
    if (data.status !== "success" || !(data.notifications || []).length) return;

    if (!window.confirm("Are you sure you want to clear all notifications?"))
      return;

    const clearRes = await fetch(BASE_URL + "api/clear_notifications.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({}),
    });
    const clearData = await clearRes.json();
    if (clearData.status !== "success") {
      console.error("Failed to clear notifications:", clearData.message);
      return;
    }

    await loadNotifications();
  } catch (err) {
    console.error("Error clearing notifications:", err);
  }
}

function notificationIcon(type) {
  if (type === "new_message") return '<i class="fa-solid fa-envelope"></i>';
  if (type === "payment_added")
    return '<i class="fa-solid fa-credit-card"></i>';
  if (type === "new_class") return '<i class="fa-solid fa-calendar-days"></i>';
  if (type === "message_reply") return '<i class="fa-solid fa-envelope"></i>';
  if (type === "negative_balance")
    return '<i class="fa-solid fa-circle-exclamation"></i>';
  if (type === "receipt_uploaded")
    return '<i class="fa-solid fa-file-invoice"></i>';
  return '<i class="fa-solid fa-bell"></i>';
}

function timeAgo(iso) {
  const diff = Math.max(0, Date.now() - new Date(iso).getTime());
  const mins = Math.floor(diff / 60000);
  if (mins < 1) return "Just now";
  if (mins < 60) return `${mins} min ago`;
  const hours = Math.floor(mins / 60);
  if (hours < 24) return `${hours} hour${hours > 1 ? "s" : ""} ago`;
  const days = Math.floor(hours / 24);
  return days === 1 ? "Yesterday" : `${days} days ago`;
}

// ── Inbox tab ───────────────────────────────────────────────
let allMessages = [];
let activeMessageId = null;
let currentInboxOffset = 0;
let hasMoreMessages = false;

function truncateText(str, maxLength = 20) {
  if (!str) return "";
  if (str.length <= maxLength) return str;
  return str.substring(0, maxLength) + "...";
}

async function loadInbox() {
  const listEl = document.getElementById("inbox-list-target");
  const paneEl = document.getElementById("inbox-reading-pane");

  if (listEl) listEl.innerHTML = '<p class="loading-msg">Φόρτωση...</p>';
  if (paneEl) paneEl.innerHTML = "";

  currentInboxOffset = 0;
  allMessages = [];

  try {
    const res = await fetch(
      `${BASE_URL}api/get_inbox.php?offset=${currentInboxOffset}`,
    );
    const result = await res.json();

    if (result.status !== "success") {
      if (listEl)
        listEl.innerHTML =
          '<p class="profile-error-msg">Σφάλμα φόρτωσης inbox.</p>';
      return;
    }

    allMessages = result.messages || [];
    hasMoreMessages = result.has_more || false;
    const unreadCount = result.unread || 0;

    const badge = document.getElementById("unread-count-badge");
    if (badge) badge.innerText = `${unreadCount} ${PT.unread}`;

    renderInboxList(allMessages);

    if (allMessages.length > 0) {
      const isMobile = window.innerWidth <= 768;
      if (isMobile) {
        activeMessageId = allMessages[0].message_id;
        renderInboxList(allMessages);
      } else {
        selectMessage(allMessages[0].message_id);
      }
    } else {
      renderEmptyReadingPane();
    }
  } catch {
    if (listEl)
      listEl.innerHTML = '<p class="profile-error-msg">Σφάλμα σύνδεσης.</p>';
  }
}

async function loadMoreInboxMessages() {
  const loadMoreBtn = document.getElementById("inbox-load-more-btn");
  if (loadMoreBtn) {
    loadMoreBtn.disabled = true;
    loadMoreBtn.innerText = "Φόρτωση...";
  }

  currentInboxOffset += 10;

  try {
    const res = await fetch(
      `${BASE_URL}api/get_inbox.php?offset=${currentInboxOffset}`,
    );
    const result = await res.json();

    if (result.status === "success" && result.messages.length > 0) {
      allMessages = allMessages.concat(result.messages);
      hasMoreMessages = result.has_more || false;
      renderInboxList(allMessages);
    } else {
      if (loadMoreBtn) loadMoreBtn.remove();
    }
  } catch (err) {
    console.error("Failed to fetch more messages", err);
    if (loadMoreBtn) {
      loadMoreBtn.disabled = false;
      loadMoreBtn.innerText = "Δοκιμάστε ξανά";
    }
  }
}

function renderInboxList(messages) {
  const listEl = document.getElementById("inbox-list-target");
  if (!listEl) return;

  if (!messages.length) {
    listEl.innerHTML = `<p class="profile-empty">${PT.inbox_empty}</p>`;
    return;
  }

  const itemsHtml = messages
    .map((m) => {
      const isSelected = m.message_id === activeMessageId ? "is-selected" : "";
      const stateClass = m.is_read ? "inbox-item--read" : "inbox-item--unread";
      const initial = m.sender_name
        ? m.sender_name.charAt(0).toUpperCase()
        : "A";

      let dateStr = "";
      if (m.sent_at) {
        const d = new Date(m.sent_at);
        dateStr = d.toLocaleDateString("el-GR", {
          hour: "2-digit",
          minute: "2-digit",
        });
      }

      const truncatedSubject = truncateText(m.subject || "", 20);
      const truncatedSnippet = truncateText(m.body || "", 20);

      return `
    <div class="inbox-item ${stateClass} ${isSelected}" id="inbox-item-${m.message_id}">
        <button type="button" class="inbox-item-clickable" onclick="selectMessage(${m.message_id})">
            <div class="inbox-avatar">
                <span>${initial}</span>
            </div>
            <div class="inbox-content">
                <div class="inbox-row">
                    <span class="inbox-sender">${escH(m.sender_name || "Admin")}</span>
                    <span class="inbox-date">${dateStr}</span>
                    ${!m.is_read ? '<span class="unread-dot"></span>' : ""}
                </div>
                <div class="inbox-subject">${escH(truncatedSubject)}</div>
                <div class="inbox-snippet">${escH(truncatedSnippet)}</div>
            </div>
        </button>
    </div>
    `;
    })
    .join("");

  let buttonHtml = "";
  if (hasMoreMessages) {
    buttonHtml = `
        <div class="load-more-container" style="text-align: center; padding: 10px 0;">
            <button type="button" id="inbox-load-more-btn" class="inbox-load-more" onclick="loadMoreInboxMessages()">
                ${PT.load_more || "Load More"}
            </button>
        </div>
    `;
  }

  listEl.innerHTML = itemsHtml + buttonHtml;
}

async function selectMessage(messageId) {
  activeMessageId = messageId;

  document
    .querySelectorAll(".inbox-item")
    .forEach((el) => el.classList.remove("is-selected"));
  const activeItem = document.getElementById(`inbox-item-${messageId}`);
  if (activeItem) activeItem.classList.add("is-selected");

  const message = allMessages.find((m) => m.message_id === messageId);
  if (!message) return;

  renderReadingPane(message);

  if (!message.is_read) {
    message.is_read = true;
    if (activeItem) {
      activeItem.classList.remove("inbox-item--unread");
      activeItem.classList.add("inbox-item--read");
      const dot = activeItem.querySelector(".unread-dot");
      if (dot) dot.remove();
    }

    const badge = document.getElementById("unread-count-badge");
    if (badge) {
      const currentCount = parseInt(badge.innerText) || 0;
      badge.innerText = `${Math.max(0, currentCount - 1)} unread`;
    }

    try {
      await fetch(BASE_URL + "api/mark_message_read.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ message_id: messageId }),
      });
    } catch {}
  }

  if (window.innerWidth <= 768) {
    document
      .getElementById("inbox-reading-pane")
      .classList.add("mobile-active");
  }
}

function renderReadingPane(m) {
  const paneEl = document.getElementById("inbox-reading-pane");
  if (!paneEl) return;

  const initial = m.sender_name ? m.sender_name.charAt(0).toUpperCase() : "A";
  const dateStr = m.sent_at ? new Date(m.sent_at).toLocaleString("el-GR") : "";

  paneEl.innerHTML = `
    <div class="pane-view-wrapper">
        <div class="pane-header">
            <button class="mobile-back-btn" onclick="closeMobilePane()"><i class="fa-solid fa-arrow-left"></i></button>
            <h1 class="pane-title">${escH(m.subject)}</h1>
            <div class="pane-actions">
                <button class="action-btn star-btn ${m.is_starred ? "starred" : ""}" onclick="toggleStar(${m.message_id})">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="${m.is_starred ? "#f59e0b" : "none"}" stroke="${m.is_starred ? "#f59e0b" : "currentColor"}" stroke-width="2">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
                </button>
                <button class="action-btn delete-btn" onclick="deleteMessage(${m.message_id})">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </div>
        </div>

        <div class="pane-chat-container">
            <div id="thread-container-${m.message_id}" class="thread-container">
                <p class="thread-empty">Φόρτωση μηνυμάτων...</p>
            </div>
        </div>

        <div class="pane-reply-bar">
            <textarea class="pane-reply-input" id="reply-input-${m.message_id}" placeholder="${window.PT?.write_reply || "Write a reply..."}" rows="1"></textarea>
            <button class="pane-reply-send-btn" onclick="sendPaneReply(${m.message_id}, ${CURRENT_USER_ID}, false)">
                <i class="fa-solid fa-paper-plane fa-sm"></i> ${window.PT?.send || "Reply"}
            </button>
        </div>
    </div>
        `;

  loadThreadHtml(m.message_id, CURRENT_USER_ID, false).then((html) => {
    const container = document.getElementById(
      `thread-container-${m.message_id}`,
    );
    if (container) {
      container.innerHTML = html;
      const chatContainer = container.closest(".pane-chat-container");
      if (chatContainer) chatContainer.scrollTop = chatContainer.scrollHeight;
    }
  });

  setTimeout(() => {
    const textarea = document.getElementById(`reply-input-${m.message_id}`);
    if (textarea) {
      textarea.addEventListener("keydown", (e) => {
        if (e.key === "Enter" && !e.shiftKey) {
          e.preventDefault();
          sendPaneReply(m.message_id, CURRENT_USER_ID, false);
        }
      });
    }
  }, 100);
}

async function sendPaneReply(messageId, recipientId, viewerIsAdmin) {
  const textarea = document.getElementById(`reply-input-${messageId}`);
  const btn = document.querySelector(".pane-reply-send-btn");
  if (!textarea) return;

  const body = textarea.value.trim();
  if (!body) {
    textarea.classList.add("pane-reply-input--error");
    setTimeout(
      () => textarea.classList.remove("pane-reply-input--error"),
      1000,
    );
    return;
  }

  btn.disabled = true;
  try {
    const res = await fetch(BASE_URL + "api/send_reply.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        message_id: messageId,
        recipient_id: recipientId,
        body,
      }),
    });
    const data = await res.json();
    if (data.status === "success") {
      textarea.value = "";
      const html = await loadThreadHtml(messageId, recipientId, viewerIsAdmin);
      const container = document.getElementById(
        `thread-container-${messageId}`,
      );
      if (container) {
        container.innerHTML = html;
        const chatContainer = container.closest(".pane-chat-container");
        if (chatContainer) chatContainer.scrollTop = chatContainer.scrollHeight;
      }
    }
  } catch (err) {
    console.error("sendPaneReply error:", err);
  } finally {
    btn.disabled = false;
  }
}

function renderEmptyReadingPane() {
  const paneEl = document.getElementById("inbox-reading-pane");
  if (paneEl) {
    const msg =
      window.PT && window.PT.select_message
        ? window.PT.select_message
        : "Select a message to read";
    paneEl.innerHTML = `<div class="pane-empty-state">${msg}</div>`;
  }
}

function filterMessages() {
  const query = document.getElementById("inbox-search").value.toLowerCase();
  const filtered = allMessages.filter(
    (m) =>
      (m.sender_name && m.sender_name.toLowerCase().includes(query)) ||
      (m.subject && m.subject.toLowerCase().includes(query)),
  );
  renderInboxList(filtered);
}

function toggleStar(msgId) {
  const msg = allMessages.find((m) => m.message_id === msgId);
  if (msg) {
    msg.is_starred = !msg.is_starred;
    renderReadingPane(msg);
  }
}

function deleteMessage(msgId) {
  allMessages = allMessages.filter((m) => m.message_id !== msgId);
  renderInboxList(allMessages);
  if (allMessages.length > 0) selectMessage(allMessages[0].message_id);
  else renderEmptyReadingPane();
}

function closeMobilePane() {
  document
    .getElementById("inbox-reading-pane")
    .classList.remove("mobile-active");
}
