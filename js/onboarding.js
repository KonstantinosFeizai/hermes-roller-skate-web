// js/onboarding.js
// Purpose: Step-by-step wizard logic για το onboarding νέων χρήστων.

document.addEventListener("DOMContentLoaded", () => {
  const S = window.OB_STRINGS || {};

  // ── State ───────────────────────────────────────────────
  let currentStep = 1;
  let selectedRole = null;
  let athletesAdded = 0;
  const MAX_ATHLETES = 2;

  // ── Elements ────────────────────────────────────────────
  const progressFill = document.getElementById("progressFill");
  const stepLabel = document.getElementById("stepLabel");
  const stepTitle = document.getElementById("stepTitle");
  const stepSubtitle = document.getElementById("stepSubtitle");
  const btnNext = document.getElementById("ob-btn-next");
  const btnBack = document.getElementById("ob-btn-back");
  const footer = document.getElementById("onboarding-footer");

  // ── Step Config ─────────────────────────────────────────
  const stepMeta = {
    1: {
      label: S.step1_label || "Βήμα 1 από 3",
      title: S.step1_title || "Βασικά Στοιχεία",
      subtitle: S.step1_subtitle || "Πες μας λίγα πράγματα για σένα",
      progress: 25,
    },
    2: {
      label: S.step2_label || "Βήμα 2 από 3",
      title: S.step2_title || "Ο Ρόλος μου",
      subtitle: S.step2_subtitle || "Πώς θέλεις να συμμετέχεις;",
      progress: 55,
    },
    "3-athlete": {
      label: S.step3a_label || "Βήμα 3 από 3",
      title: S.step3a_title || "Στοιχεία Αθλητή",
      subtitle: S.step3a_subtitle || "Συμπλήρωσε τα στοιχεία σου",
      progress: 80,
    },
    "3-parent": {
      label: S.step3b_label || "Βήμα 3 από 3",
      title: S.step3b_title || "Προσθήκη Αθλητή",
      subtitle: S.step3b_subtitle || "Πρόσθεσε τα στοιχεία του παιδιού σου",
      progress: 80,
    },
    "3-coach": {
      label: S.step3c_label || "Βήμα 3 από 3",
      title: S.step3c_title || "Σχεδόν έτοιμος!",
      subtitle: S.step3c_subtitle || "Θα επικοινωνήσουμε μαζί σου σύντομα.",
      progress: 80,
    },
    "3-none": {
      label: S.step3d_label || "Βήμα 3 από 3",
      title: S.step3d_title || "Ολοκλήρωση",
      subtitle:
        S.step3d_subtitle || "Μπορείς πάντα να ενημερώσεις το προφίλ σου.",
      progress: 80,
    },
    success: {
      label: S.success_label || "Ολοκληρώθηκε!",
      title: "",
      subtitle: "",
      progress: 100,
    },
  };

  // ── Helpers ──────────────────────────────────────────────

  function showError(id, msg) {
    const el = document.getElementById(id);
    if (!el) return;
    el.textContent = msg;
    el.style.display = "block";
  }

  function hideError(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = "none";
  }

  function setLoading(loading) {
    btnNext.disabled = loading;
    btnNext.textContent = loading
      ? S.loading || "Παρακαλώ περιμένετε..."
      : nextLabel();
  }

  function nextLabel() {
    if (currentStep === "success") return "";
    if (
      currentStep === 2 &&
      (selectedRole === "coach" || selectedRole === "none")
    )
      return S.btn_finish || "Ολοκλήρωση";
    if (currentStep === 1) return S.btn_next || "Επόμενο →";
    return S.btn_next || "Συνέχεια";
  }

  function updateUI(stepKey) {
    const meta = stepMeta[stepKey];
    if (!meta) return;

    progressFill.style.width = meta.progress + "%";
    stepLabel.textContent = meta.label;
    stepTitle.textContent = meta.title;
    stepSubtitle.textContent = meta.subtitle;

    // Κρύβουμε όλα τα steps
    document
      .querySelectorAll(".onboarding-step")
      .forEach((s) => s.classList.remove("active"));

    // Εμφανίζουμε το σωστό
    const stepEl = document.getElementById(
      stepKey === "success"
        ? "step-success"
        : stepKey === 1 || stepKey === 2
          ? `step-${stepKey}`
          : `step-${stepKey}`,
    );
    if (stepEl) stepEl.classList.add("active");

    // Back button
    btnBack.style.display =
      stepKey === 1 || stepKey === "success" ? "none" : "";

    // Next button
    if (stepKey === "success") {
      const extraFinish = document.getElementById("ob-btn-finish");
      if (extraFinish) extraFinish.remove();
      btnNext.innerHTML = `<i class="fa-solid fa-house"></i> ${S.btn_home || "Μεταφορά στην Αρχική"}`;
      btnNext.disabled = false;
    } else if (stepKey === "3-parent") {
      // ✨ ΕΙΔΙΚΗ ΛΟΓΙΚΗ ΓΙΑ STEP 3b (Parent) ✨
      const parentForm = document.getElementById("parent-athlete-form");
      const isFormHidden = parentForm && parentForm.style.display === "none";

      if (athletesAdded > 0 && isFormHidden) {
        // Αν υπάρχει ήδη 1 αθλητής και η φόρμα είναι κρυμμένη -> Finish
        btnNext.textContent = S.btn_finish || "Finish";
      } else {
        // Αν η φόρμα είναι ανοιχτή -> Next / Save & Continue
        btnNext.textContent = nextLabel();
      }
    } else {
      btnNext.textContent = nextLabel();
    }

    footer.style.display = "flex";
  }
  // ── STEP 1 — Αποθήκευση βασικών στοιχείων ──────────────
  async function saveStep1() {
    hideError("step1-error");

    const first_name = document.getElementById("ob_first_name").value.trim();
    const last_name = document.getElementById("ob_last_name").value.trim();
    const phone = document.getElementById("ob_phone").value.trim();
    const age = document.getElementById("ob_age").value.trim();
    const region = document.getElementById("ob_region").value.trim();

    if (!first_name || !last_name || !phone) {
      showError(
        "step1-error",
        S.error_required_step1 ||
          "Παρακαλώ συμπλήρωσε τα υποχρεωτικά πεδία (*).",
      );
      return false;
    }

    setLoading(true);
    try {
      const res = await fetch(`${BASE_URL}user/personal_info_handler.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ first_name, last_name, phone, age, region }),
      });
      const data = await res.json();
      if (data.status !== "success") {
        showError(
          "step1-error",
          data.message || S.error_save || "Σφάλμα αποθήκευσης.",
        );
        return false;
      }
      return true;
    } catch {
      showError(
        "step1-error",
        S.error_connection || "Αδυναμία σύνδεσης. Δοκιμάστε ξανά.",
      );
      return false;
    } finally {
      setLoading(false);
    }
  }

  // ── STEP 2 — Αποθήκευση ρόλου ──────────────────────────
  async function saveStep2() {
    hideError("step2-error");

    const roleInput = document.querySelector('input[name="role_type"]:checked');
    if (!roleInput) {
      showError(
        "step2-error",
        S.error_required_role || "Παρακαλώ επίλεξε έναν ρόλο.",
      );
      return false;
    }

    selectedRole = roleInput.value;
    setLoading(true);

    try {
      const res = await fetch(`${BASE_URL}api/save_role_type.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ role_type: selectedRole }),
      });
      const data = await res.json();
      if (data.status !== "success") {
        showError(
          "step2-error",
          data.message || S.error_save || "Σφάλμα αποθήκευσης.",
        );
        return false;
      }
      return true;
    } catch {
      showError(
        "step2-error",
        S.error_connection || "Αδυναμία σύνδεσης. Δοκιμάστε ξανά.",
      );
      return false;
    } finally {
      setLoading(false);
    }
  }

  async function goToStep3Athlete() {
    // Αυτόματη συμπλήρωση τηλεφώνου από το Step 1
    const step1Phone = document.getElementById("ob_phone").value.trim();
    const athletePhoneInput = document.getElementById("ob_athlete_phone");
    const banner = document.getElementById("ob-athlete-exists-banner");
    const obAthleteId = document.getElementById("ob_athlete_id");

    if (athletePhoneInput && !athletePhoneInput.value.trim() && step1Phone) {
      athletePhoneInput.value = step1Phone;
    }

    // Ελέγχουμε στο backend αν υπάρχει ήδη ενεργή self-athlete κάρτα για αυτόν τον χρήστη
    try {
      const res = await fetch(`${BASE_URL}api/get_athletes.php`);
      if (!res.ok) return;
      const list = await res.json();
      if (!Array.isArray(list)) return;

      const selfActive = list.find(
        (a) =>
          a.user_id &&
          (a.parent_id === null || a.parent_id === "" || a.parent_id === 0) &&
          Number(a.is_active) === 1,
      );

      if (selfActive) {
        // Εμφάνιση banner με επιλογές
        if (banner) {
          banner.style.display = "block";
          banner.innerHTML = `
            <div style="background:#fff3cd;border:1px solid #ffeeba;padding:10px;border-radius:6px;display:flex;align-items:center;justify-content:space-between;gap:10px;">
              <div style="flex:1;color:#856404">Υπάρχει ήδη ενεργή κάρτα αθλητή για αυτόν τον λογαριασμό. Μπορείτε να επεξεργαστείτε την υπάρχουσα κάρτα ή να δημιουργήσετε καινούργια (αν επιτρέπεται).</div>
              <div style="display:flex;gap:8px;">
                <button id="ob-edit-existing" class="ob-btn ob-btn-ghost">Επεξεργασία υπάρχουσας</button>
                <button id="ob-create-new" class="ob-btn ob-btn-primary">Δημιουργία νέας</button>
              </div>
            </div>
          `;

          document
            .getElementById("ob-edit-existing")
            .addEventListener("click", (e) => {
              // Γεμίζουμε τη φόρμα με τα δεδομένα του υπάρχοντος αθλητή και θέτουμε hidden athlete_id
              obAthleteId.value = selfActive.id;
              document.getElementById("ob_birth_date").value =
                selfActive.birth_date || "";
              document.getElementById("ob_athlete_phone").value =
                selfActive.phone || step1Phone || "";
              document.getElementById("ob_location").value =
                selfActive.location_id || "";
              document.getElementById("ob_shoe_size").value =
                selfActive.shoe_size || "";
              document.getElementById("ob_shirt_size").value =
                selfActive.shirt_size || "";
              document.getElementById("ob_interest_rides").checked =
                !!selfActive.interest_rides;
              document.getElementById("ob_interest_races").checked =
                !!selfActive.interest_races;
              document.getElementById("ob_interest_ski").checked =
                !!selfActive.interest_ski;
              document.getElementById("ob_interest_skating").checked =
                !!selfActive.interest_skating;
              document.getElementById("ob_interest_hockey").checked =
                !!selfActive.interest_hockey;
              document.getElementById("ob_amka").value = selfActive.amka || "";
              document.getElementById("ob_afm").value = selfActive.afm || "";

              // Κρύβουμε το banner μετά την επιλογή
              banner.style.display = "none";
            });

          document
            .getElementById("ob-create-new")
            .addEventListener("click", (e) => {
              // Δημιουργία νέας: διαγραφή hidden athlete_id και φόρμα καθαρή
              obAthleteId.value = "";
              // Δεν καθαρίζουμε τα ονόματα γιατί παίρνονται από step1
              document.getElementById("ob_birth_date").value = "";
              document.getElementById("ob_athlete_phone").value =
                step1Phone || "";
              document.getElementById("ob_location").value = "";
              document.getElementById("ob_shoe_size").value = "";
              document.getElementById("ob_shirt_size").value = "";
              document.getElementById("ob_interest_rides").checked = false;
              document.getElementById("ob_interest_races").checked = false;
              document.getElementById("ob_interest_ski").checked = false;
              document.getElementById("ob_interest_skating").checked = false;
              document.getElementById("ob_interest_hockey").checked = false;
              document.getElementById("ob_amka").value = "";
              document.getElementById("ob_afm").value = "";

              banner.style.display = "none";
            });
        }
      } else {
        if (banner) banner.style.display = "none";
      }
    } catch (err) {
      // ignore errors - non blocking
      console.warn("goToStep3Athlete check failed", err);
    }
  }

  // ── STEP 3a — Αποθήκευση αθλητή (ο ίδιος) ──────────────
  async function saveStep3Athlete() {
    hideError("step3a-error");

    const phone = document.getElementById("ob_athlete_phone").value.trim();
    const location_id = document.getElementById("ob_location").value;
    const obAthleteId = document.getElementById("ob_athlete_id").value || null;

    // 1. Έλεγχος Υποχρεωτικού Location
    if (!location_id) {
      showError(
        "step3a-error",
        (window.pt && window.pt.error_required_location) ||
          S.error_required_location ||
          "Παρακαλώ επιλέξτε περιοχή/τοποθεσία.",
      );
      return false;
    }

    // 2. Έλεγχος Υποχρεωτικού Τηλεφώνου
    if (!phone) {
      showError(
        "step3a-error",
        (window.pt && window.pt.error_required_phone) ||
          S.error_required_phone ||
          "Παρακαλώ συμπληρώστε το τηλέφωνο επικοινωνίας.",
      );
      return false;
    }

    // 3. Έλεγχος Μορφής Τηλεφώνου (π.χ. 7 έως 20 ψηφία/συμβολα ή 10ψηφιο)
    const phoneRegex = /^[0-9+\s\-]{7,20}$/;
    if (!phoneRegex.test(phone)) {
      showError(
        "step3a-error",
        (window.pt && window.pt.error_invalid_phone) ||
          S.error_invalid_phone ||
          "Παρακαλώ εισάγετε έναν έγκυρο αριθμό τηλεφώνου.",
      );
      return false;
    }

    const payload = {
      athlete_id: obAthleteId,
      first_name: document.getElementById("ob_first_name").value.trim(),
      last_name: document.getElementById("ob_last_name").value.trim(),
      birth_date: document.getElementById("ob_birth_date").value || null,
      phone: phone,
      region: document.getElementById("ob_region").value.trim(),
      location_id: location_id,
      shoe_size: document.getElementById("ob_shoe_size").value.trim(),
      shirt_size: document.getElementById("ob_shirt_size").value,
      interest_rides: document.getElementById("ob_interest_rides").checked,
      interest_races: document.getElementById("ob_interest_races").checked,
      interest_ski: document.getElementById("ob_interest_ski").checked,
      interest_skating: document.getElementById("ob_interest_skating").checked,
      interest_hockey: document.getElementById("ob_interest_hockey").checked,
      amka: document.getElementById("ob_amka").value.trim() || null,
      afm: document.getElementById("ob_afm").value.trim() || null,
    };

    setLoading(true);
    try {
      const res = await fetch(`${BASE_URL}api/save_athlete.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
      const data = await res.json();

      if (data.status === "success") {
        return true;
      }

      // If backend says 'exists' (active self-athlete already present), set athlete_id and auto-retry as an UPDATE
      if (data.status === "exists") {
        const existingId = data.athlete_id;
        document.getElementById("ob_athlete_id").value = existingId;

        // Optional: try to prefill details (non-blocking)
        try {
          const r2 = await fetch(`${BASE_URL}api/get_athletes.php`);
          if (r2.ok) {
            const all = await r2.json();
            const self = all.find((x) => Number(x.id) === Number(existingId));
            if (self) {
              document.getElementById("ob_birth_date").value =
                self.birth_date || "";
              document.getElementById("ob_athlete_phone").value =
                self.phone || "";
              document.getElementById("ob_location").value =
                self.location_id || "";
              document.getElementById("ob_shoe_size").value =
                self.shoe_size || "";
              document.getElementById("ob_shirt_size").value =
                self.shirt_size || "";
              document.getElementById("ob_interest_rides").checked =
                !!self.interest_rides;
              document.getElementById("ob_interest_races").checked =
                !!self.interest_races;
              document.getElementById("ob_interest_ski").checked =
                !!self.interest_ski;
              document.getElementById("ob_interest_skating").checked =
                !!self.interest_skating;
              document.getElementById("ob_interest_hockey").checked =
                !!self.interest_hockey;
              document.getElementById("ob_amka").value = self.amka || "";
              document.getElementById("ob_afm").value = self.afm || "";
            }
          }
        } catch (err) {
          // ignore
        }

        // Immediately retry as an UPDATE by including athlete_id in payload
        const retryPayload = Object.assign({}, payload, {
          athlete_id: existingId,
        });
        try {
          const r3 = await fetch(`${BASE_URL}api/save_athlete.php`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(retryPayload),
          });
          const d3 = await r3.json();
          if (d3.status === "success") {
            return true; // proceed to success
          }

          // If retry failed, display message and stop
          showError(
            "step3a-error",
            d3.message || S.error_save || "Σφάλμα αποθήκευσης.",
          );
          return false;
        } catch (err) {
          showError(
            "step3a-error",
            S.error_connection || "Αδυναμία σύνδεσης. Δοκιμάστε ξανά.",
          );
          return false;
        }
      }

      showError(
        "step3a-error",
        data.message || S.error_save || "Σφάλμα αποθήκευσης.",
      );
      return false;
    } catch {
      showError(
        "step3a-error",
        S.error_connection || "Αδυναμία σύνδεσης. Δοκιμάστε ξανά.",
      );
      return false;
    } finally {
      setLoading(false);
    }
  }

  function goToStep3Parent() {
    const step1Phone = document.getElementById("ob_phone").value.trim();
    const childPhoneInput = document.getElementById("ob_child_phone");

    if (childPhoneInput && !childPhoneInput.value.trim() && step1Phone) {
      childPhoneInput.value = step1Phone;
    }
  }

  // ── STEP 3b — Αποθήκευση παιδιού (γονέας) ──────────────
  async function addChildAthlete() {
    hideError("step3b-error");

    const first_name = document
      .getElementById("ob_child_first_name")
      .value.trim();
    const last_name = document
      .getElementById("ob_child_last_name")
      .value.trim();
    const location_id = document.getElementById("ob_child_location").value;
    const phone = document.getElementById("ob_child_phone").value.trim();

    // 1. Validation Υποχρεωτικών Πεδίων
    if (!first_name || !last_name) {
      showError(
        "step3b-error",
        S.error_required_child || "Όνομα και επώνυμο αθλητή είναι υποχρεωτικά.",
      );
      return false;
    }

    if (!location_id) {
      showError(
        "step3b-error",
        S.error_required_location || "Παρακαλώ επιλέξτε περιοχή/τοποθεσία.",
      );
      return false;
    }

    // 2. Validation Τηλεφώνου
    if (phone) {
      const phoneRegex = /^[0-9+\s\-]{7,20}$/;
      if (!phoneRegex.test(phone)) {
        showError(
          "step3b-error",
          S.error_invalid_phone ||
            "Παρακαλώ εισάγετε έναν έγκυρο αριθμό τηλεφώνου.",
        );
        return false;
      }
    }

    // 3. Προετοιμασία Payload
    const payload = {
      first_name,
      last_name,
      birth_date: document.getElementById("ob_child_birth_date").value || null,
      phone: phone,
      region: null,
      location_id: location_id,
      shoe_size: document.getElementById("ob_child_shoe_size").value.trim(),
      shirt_size: document.getElementById("ob_child_shirt_size").value,
      interest_rides: document.getElementById("ob_child_rides").checked,
      interest_races: document.getElementById("ob_child_races").checked,
      interest_ski: document.getElementById("ob_child_ski").checked,
      interest_skating: document.getElementById("ob_child_skating").checked,
      interest_hockey: document.getElementById("ob_child_hockey").checked,
      amka: document.getElementById("ob_child_amka")?.value.trim() || null,
      afm: document.getElementById("ob_child_afm")?.value.trim() || null,
    };

    setLoading(true);
    try {
      const res = await fetch(`${BASE_URL}api/save_athlete.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
      const data = await res.json();

      if (data.status !== "success") {
        showError(
          "step3b-error",
          data.message || S.error_save || "Σφάλμα αποθήκευσης.",
        );
        return false;
      }

      // 4. Ενημέρωση UI με τον νέο αθλητή
      athletesAdded++;
      const container = document.getElementById("ob-athletes-added");
      const div = document.createElement("div");
      div.className = "ob-athlete-added";
      div.innerHTML = `<span>✅</span><span class="ob-athlete-added-name">${first_name} ${last_name}</span>`;
      container.appendChild(div);

      const countTpl = (
        S.athletes_added_count || ":count/:max αθλητές προστέθηκαν"
      )
        .replace(":count", athletesAdded)
        .replace(":max", MAX_ATHLETES);
      document.getElementById("ob-athlete-count").textContent = countTpl;

      // 5. Καθαρισμός Φόρμας
      [
        "ob_child_first_name",
        "ob_child_last_name",
        "ob_child_birth_date",
        "ob_child_phone",
        "ob_child_location",
        "ob_child_shoe_size",
        "ob_child_shirt_size",
        "ob_child_rides",
        "ob_child_races",
        "ob_child_ski",
        "ob_child_skating",
        "ob_child_hockey",
        "ob_child_amka",
        "ob_child_afm",
      ].forEach((id) => {
        const el = document.getElementById(id);
        if (el) {
          if (el.type === "checkbox") el.checked = false;
          else el.value = "";
        }
      });

      goToStep3Parent();

      // 6. Απόκρυψη Φόρμας & Αλλαγή Κουμπιού σε "Finish / Ολοκλήρωση"
      document.getElementById("parent-athlete-form").style.display = "none";
      btnNext.textContent = S.btn_finish || "Finish";

      // 7. Εμφάνιση του κουμπιού "+ Add another" αν δεν έχουμε φτάσει το MAX
      const showSecondBtn = document.getElementById("ob-btn-show-second-child");
      if (showSecondBtn) {
        showSecondBtn.style.display =
          athletesAdded < MAX_ATHLETES ? "block" : "none";
      }

      return true;
    } catch {
      showError(
        "step3b-error",
        S.error_connection || "Αδυναμία σύνδεσης. Δοκιμάστε ξανά.",
      );
      return false;
    } finally {
      setLoading(false);
    }
  }

  // Listener για το κουμπί "+ Add another"
  const showSecondBtn = document.getElementById("ob-btn-show-second-child");
  if (showSecondBtn) {
    showSecondBtn.addEventListener("click", () => {
      document.getElementById("parent-athlete-form").style.display = "block";
      showSecondBtn.style.display = "none";
      btnNext.textContent = S.btn_save_continue || S.btn_next || "Next";
    });
  }

  // ── Navigation ───────────────────────────────────────────

  function goToSuccess() {
    currentStep = "success";
    updateUI("success");
    // Mark onboarding as completed in DB
    fetch(`${BASE_URL}api/complete_onboarding.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
    }).catch(() => {}); // fire-and-forget, non-blocking
  }

  async function handleNext() {
    if (currentStep === "success") {
      window.location.href = `${BASE_URL}user/profile`;
      return;
    }

    if (currentStep === 1) {
      const ok = await saveStep1();
      if (!ok) return;
      currentStep = 2;
      updateUI(2);
      return;
    }

    if (currentStep === 2) {
      const ok = await saveStep2();
      if (!ok) return;

      if (selectedRole === "athlete") {
        currentStep = "3-athlete";
        updateUI("3-athlete");
        goToStep3Athlete();
      } else if (selectedRole === "parent") {
        currentStep = "3-parent";
        updateUI("3-parent");
        goToStep3Parent();
      } else {
        goToSuccess();
      }
      return;
    }

    if (currentStep === "3-athlete") {
      const ok = await saveStep3Athlete();
      if (!ok) return;
      goToSuccess();
      return;
    }

    if (currentStep === "3-parent") {
      // ✨ ΝΕΑ ΛΟΓΙΚΗ HANDLE NEXT ✨
      const formEl = document.getElementById("parent-athlete-form");
      const isFormVisible = formEl && formEl.style.display !== "none";

      // Αν η φόρμα είναι ανοιχτή, προσπαθούμε να αποθηκεύσουμε τον αθλητή
      if (isFormVisible) {
        const ok = await addChildAthlete();
        // Αν έχουμε ήδη τουλάχιστον 1 αθλητή και η φόρμα είναι κρυμμένη πλέον, δεν φεύγουμε αμέσως στο success.
        // Ο χρήστης μπορεί να πατήσει "Ολοκλήρωση" την επόμενη φορά.
        return;
      }

      // Αν η φόρμα είναι κρυμμένη (άρα έχει ήδη προστεθεί τουλάχιστον 1 αθλητής) -> Ολοκλήρωση
      if (athletesAdded > 0) {
        goToSuccess();
      }
      return;
    }
  }

  function handleBack() {
    if (currentStep === 2) {
      currentStep = 1;
      updateUI(1);
    } else if (
      currentStep === "3-athlete" ||
      currentStep === "3-parent" ||
      currentStep === "3-coach" ||
      currentStep === "3-none"
    ) {
      currentStep = 2;
      updateUI(2);
    }
  }

  // ── Event Listeners ──────────────────────────────────────
  btnNext.addEventListener("click", handleNext);
  btnBack.addEventListener("click", handleBack);

  document.querySelectorAll('input[name="role_type"]').forEach((radio) => {
    radio.addEventListener("change", () => {
      selectedRole = radio.value;
      const isLeaf = selectedRole === "coach" || selectedRole === "none";
      btnNext.textContent = isLeaf
        ? S.btn_finish || "Ολοκλήρωση"
        : S.btn_next || "Επόμενο →";
    });
  });

  // ── Init ─────────────────────────────────────────────────
  updateUI(1);
});
