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
    return S.btn_save_continue || "Αποθήκευση & Συνέχεια";
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
      btnNext.textContent = S.btn_home || "🏠 Μεταφορά στην Αρχική";
      btnNext.disabled = false;
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

    if (!first_name || !last_name || !phone || !region) {
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

  // ── STEP 3a — Αποθήκευση αθλητή (ο ίδιος) ──────────────
  async function saveStep3Athlete() {
    hideError("step3a-error");

    const payload = {
      first_name: document.getElementById("ob_first_name").value.trim(),
      last_name: document.getElementById("ob_last_name").value.trim(),
      birth_date: document.getElementById("ob_birth_date").value || null,
      phone: document.getElementById("ob_athlete_phone").value.trim(),
      region: document.getElementById("ob_region").value.trim(),
      location_id: document.getElementById("ob_location").value || null,
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
      if (data.status !== "success") {
        showError(
          "step3a-error",
          data.message || S.error_save || "Σφάλμα αποθήκευσης.",
        );
        return false;
      }
      return true;
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

  // ── STEP 3b — Αποθήκευση παιδιού (γονέας) ──────────────
  async function addChildAthlete() {
    hideError("step3b-error");

    const first_name = document
      .getElementById("ob_child_first_name")
      .value.trim();
    const last_name = document
      .getElementById("ob_child_last_name")
      .value.trim();

    if (!first_name || !last_name) {
      showError(
        "step3b-error",
        S.error_required_child || "Όνομα και επώνυμο αθλητή είναι υποχρεωτικά.",
      );
      return false;
    }

    const payload = {
      first_name,
      last_name,
      birth_date: document.getElementById("ob_child_birth_date").value || null,
      phone: document.getElementById("ob_child_phone").value.trim(),
      location_id: document.getElementById("ob_child_location").value || null,
      shoe_size: document.getElementById("ob_child_shoe_size").value.trim(),
      shirt_size: document.getElementById("ob_child_shirt_size").value,
      interest_rides: document.getElementById("ob_child_rides").checked,
      interest_races: document.getElementById("ob_child_races").checked,
      interest_ski: document.getElementById("ob_child_ski").checked,
      interest_skating: document.getElementById("ob_child_skating").checked,
      interest_hockey: document.getElementById("ob_child_hockey").checked,
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

      // Καθαρισμός φόρμας για 2ο παιδί
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
      ].forEach((id) => {
        const el = document.getElementById(id);
        if (el.type === "checkbox") el.checked = false;
        else el.value = "";
      });

      if (athletesAdded >= MAX_ATHLETES) {
        document.getElementById("parent-athlete-form").style.display = "none";
        btnNext.textContent = (S.btn_finish || "Ολοκλήρωση") + " →";
      } else {
        btnNext.textContent = S.btn_add_another || "Προσθήκη ακόμα ενός +";
        if (!document.getElementById("ob-btn-finish")) {
          const finishBtn = document.createElement("button");
          finishBtn.id = "ob-btn-finish";
          finishBtn.className = "ob-btn-skip";
          finishBtn.textContent =
            S.btn_finish_without || "Ολοκλήρωση χωρίς άλλη προσθήκη";
          footer.insertBefore(finishBtn, btnNext);
          finishBtn.addEventListener("click", goToSuccess);
        }
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

  // ── Navigation ───────────────────────────────────────────

  function goToSuccess() {
    currentStep = "success";
    updateUI("success");
  }

  async function handleNext() {
    if (currentStep === "success") {
      window.location.href = BASE_URL;
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
      } else if (selectedRole === "parent") {
        currentStep = "3-parent";
        updateUI("3-parent");
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
      if (athletesAdded >= MAX_ATHLETES) {
        goToSuccess();
        return;
      }
      await addChildAthlete();
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
