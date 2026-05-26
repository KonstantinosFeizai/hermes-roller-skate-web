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
  if (profileUpdateForm) {
    profileUpdateForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const data = await postJSON("user/profile_update_handler.php", {
        username: document.getElementById("username").value.trim(),
        email: document.getElementById("email").value.trim(),
      });
      showMsg("profileUpdateMessage", data.message, data.status === "success");
    });
  }

  // ── Password Change Form ──────────────────────────────────

  const changePasswordForm = document.getElementById("changePasswordForm");
  if (changePasswordForm) {
    changePasswordForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const inputs = changePasswordForm.querySelectorAll(
        'input[type="password"]',
      );
      const data = await postJSON("user/change_password_handler.php", {
        current_password: inputs[0].value,
        new_password: inputs[1].value,
        confirm_new_password: inputs[2].value,
      });
      showMsg("passwordChangeMessage", data.message, data.status === "success");
      if (data.status === "success") changePasswordForm.reset();
    });
  }

  // ── Personal Info Form ────────────────────────────────────

  const personalInfoForm = document.getElementById("personalInfoForm");
  if (personalInfoForm) {
    personalInfoForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const data = await postJSON("user/personal_info_handler.php", {
        first_name: document.getElementById("first_name").value.trim(),
        last_name: document.getElementById("last_name").value.trim(),
        age: document.getElementById("age").value,
        phone: document.getElementById("phone").value.trim(),
        region: document.getElementById("region").value.trim(),
      });
      showMsg("personalInfoMessage", data.message, data.status === "success");
    });
  }
});

// ── Role Type ─────────────────────────────────────────────────
// (global γιατί καλείται από onclick inline)

async function saveRoleType() {
  const btn = document.getElementById("saveRoleBtn");
  const msgEl = document.getElementById("roleMessage");
  const checked = document.querySelector(
    'input[name="profile_role_type"]:checked',
  );

  if (!checked) {
    msgEl.textContent = "Παρακαλώ επίλεξε έναν ρόλο.";
    msgEl.style.color = "#e74c3c";
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

    msgEl.textContent =
      data.message || (data.status === "success" ? "Αποθηκεύτηκε!" : "Σφάλμα.");
    msgEl.style.color = data.status === "success" ? "#27ae60" : "#e74c3c";
    msgEl.style.background = data.status === "success" ? "#eafaf1" : "#fdf2f2";
    msgEl.style.padding = "10px 14px";
    msgEl.style.borderRadius = "8px";
    msgEl.style.display = "block";

    if (data.status === "success") {
      window.USER_ROLE_TYPE = checked.value;
      // Αν άλλαξε ρόλος → ανανέωσε το tab αθλητή
      loadAthletes();
      setTimeout(() => {
        msgEl.style.display = "none";
      }, 3000);
    }
  } catch {
    msgEl.textContent = "Αδυναμία σύνδεσης.";
    msgEl.style.color = "#e74c3c";
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

  listEl.innerHTML = '<p style="color:#888">Φόρτωση...</p>';

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
                      "Δεν απαιτείται καρτέλα αθλητή για τον ρόλο σου."
                    : window.PT?.athlete_not_added ||
                      "Δεν έχεις προσθέσει αθλητή ακόμα."
                }
            </p>`;
      return;
    }

    listEl.innerHTML = athletes
      .map((a) => {
        const name = `${a.first_name} ${a.last_name}`;
        const birth = a.birth_date ? `🎂 ${a.birth_date}` : "";
        const loc = a.location_name ? `📍 ${a.location_name}` : "";
        const shoe = a.shoe_size ? `👟 ${a.shoe_size}` : "";
        const shirt = a.shirt_size ? `👕 ${a.shirt_size}` : "";
        const meta = [birth, loc, shoe, shirt].filter(Boolean).join("  ·  ");

        // Ενδιαφέροντα
        const interests = [];
        if (a.interest_rides) interests.push("🛼 Βόλτες");
        if (a.interest_races) interests.push("🏁 Αγώνες");
        if (a.interest_ski) interests.push("⛷️ Σκι");
        if (a.interest_skating) interests.push("⛸️ Πατινάζ");
        if (a.interest_hockey) interests.push("🏒 Χόκεϊ");

        return `
            <div class="athlete-card" data-id="${a.id}">
                <div class="athlete-card-info">
                    <div class="athlete-card-name">${name}</div>
                    <div class="athlete-card-meta">${meta}</div>
                    ${interests.length ? `<div class="athlete-card-meta" style="margin-top:4px;">${interests.join("  ")}</div>` : ""}
                </div>
                <div class="athlete-card-actions">
                    <button class="profile-submit-btn athlete-btn-edit"
                        onclick="editAthlete(${JSON.stringify(a).replace(/"/g, "&quot;")})">
                        ✏️ ${window.PT?.athletes_edit || "Επεξεργασία"}
                    </button>
                    <button class="profile-submit-btn athlete-btn-delete"
                        onclick="deleteAthlete(${a.id}, '${name}')">
                        🗑️ ${window.PT?.athletes_delete || "Διαγραφή"}
                    </button>
                </div>
            </div>`;
      })
      .join("");
  } catch {
    listEl.innerHTML = '<p style="color:#e74c3c">Σφάλμα φόρτωσης.</p>';
  }
}

function showAthleteForm(title = null) {
  const wrap = document.getElementById("athlete-form-wrap");
  const tEl = document.getElementById("athlete-form-title");
  if (wrap) wrap.style.display = "block";
  if (tEl && title) tEl.textContent = title;
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

  showAthleteForm(window.PT?.athlete_form_edit || "✏️ Επεξεργασία Αθλητή");
}

async function saveAthlete() {
  const btn = document.getElementById("saveAthleteBtn");
  const msgEl = document.getElementById("athleteFormMsg");
  const first = document.getElementById("pf_first_name").value.trim();
  const last = document.getElementById("pf_last_name").value.trim();

  if (!first || !last) {
    msgEl.textContent = "Όνομα και επώνυμο είναι υποχρεωτικά.";
    msgEl.style.color = "#e74c3c";
    msgEl.style.display = "block";
    return;
  }

  btn.disabled = true;
  const payload = {
    athlete_id: document.getElementById("pf_athlete_id").value || null,
    first_name: first,
    last_name: last,
    birth_date: document.getElementById("pf_birth_date").value || null,
    phone: document.getElementById("pf_phone").value.trim(),
    location_id: document.getElementById("pf_location").value || null,
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

    msgEl.textContent = data.message;
    msgEl.style.color = data.status === "success" ? "#27ae60" : "#e74c3c";
    msgEl.style.display = "block";

    if (data.status === "success") {
      setTimeout(() => {
        cancelAthleteForm();
        loadAthletes();
      }, 1200);
    }
  } catch {
    msgEl.textContent = "Αδυναμία σύνδεσης.";
    msgEl.style.color = "#e74c3c";
    msgEl.style.display = "block";
  } finally {
    btn.disabled = false;
  }
}

async function deleteAthlete(athleteId, name) {
  if (!confirm(`Διαγραφή αθλητή "${name}";`)) return;

  try {
    const res = await fetch(BASE_URL + "api/delete_athlete.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ athlete_id: athleteId }),
    });
    const data = await res.json();
    if (data.status === "success") loadAthletes();
    else alert(data.message || "Σφάλμα διαγραφής.");
  } catch {
    alert("Αδυναμία σύνδεσης.");
  }
}
