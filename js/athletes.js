// js/athletes.js
// Athletes tab: search, filter (by location), sort, add/edit/delete, profile modal.
// All data comes from the athletes table; rows carry data-athlete JSON.

// ── Athlete Profile Modal ─────────────────────────────────────

let _activeAthlete = null;

function openAthleteProfile(btn) {
  const row = btn.closest("tr");
  let a = {};
  try {
    a = JSON.parse(row.getAttribute("data-athlete") || "{}");
  } catch (_) {}
  _activeAthlete = a;

  const initials =
    ((a.first_name?.[0] || "") + (a.last_name?.[0] || "")).toUpperCase() || "?";
  document.getElementById("apAvatar").textContent = initials;
  document.getElementById("apFullName").textContent =
    (a.first_name + " " + a.last_name).trim() || "—";
  document.getElementById("apAccount").textContent = a.linked_username
    ? "@" + a.linked_username
    : "Χωρίς λογαριασμό";

  document.getElementById("apPhone").textContent = a.phone || "—";
  document.getElementById("apBirth").textContent = a.birth_date || "—";
  document.getElementById("apLocation").textContent = a.location_name || "—";
  document.getElementById("apShoe").textContent = a.shoe_size || "—";
  document.getElementById("apShirt").textContent = a.shirt_size || "—";
  document.getElementById("apAmka").textContent = a.amka || "—";
  document.getElementById("apAfm").textContent = a.afm || "—";

  const interests = [];
  if (a.interest_rides) interests.push("🛼 Βόλτες");
  if (a.interest_races) interests.push("🏁 Αγώνες");
  if (a.interest_ski) interests.push("⛷️ Σκι");
  if (a.interest_skating) interests.push("⛸️ Πατινάζ");
  if (a.interest_hockey) interests.push("🏒 Χόκεϊ");
  document.getElementById("apInterests").textContent = interests.length
    ? interests.join("  ·  ")
    : "—";

  // Parent section
  const parentSection = document.getElementById("apParentSection");
  const parentInfo = document.getElementById("apParentInfo");
  if (a.parent_id && a.parent_full_name) {
    document.getElementById("apParentName").textContent =
      a.parent_full_name || "—";
    document.getElementById("apParentPhone").textContent =
      a.parent_phone || "—";
    document.getElementById("apParentEmail").textContent =
      a.parent_email || "—";
    parentSection.style.display = "";
    parentInfo.style.display = "none";
    const toggleBtn = parentSection.querySelector("button");
    if (toggleBtn) toggleBtn.textContent = "Εμφάνιση";
  } else {
    parentSection.style.display = "none";
  }

  document.getElementById("athleteProfileModal").style.display = "flex";
}

function toggleParentInfo() {
  const info = document.getElementById("apParentInfo");
  const btn = document.querySelector("#apParentSection button");
  const shown = info.style.display !== "none";
  info.style.display = shown ? "none" : "";
  if (btn) btn.textContent = shown ? "Εμφάνιση" : "Απόκρυψη";
}

function closeAthleteProfileModal() {
  document.getElementById("athleteProfileModal").style.display = "none";
  _activeAthlete = null;
}

function editAthleteFromProfile() {
  if (!_activeAthlete) return;
  const a = _activeAthlete; // save before close nullifies it
  closeAthleteProfileModal();
  editAthleteData(a);
}

// ── Add / Edit modal open/close ───────────────────────────────

function openAddAthleteModal() {
  document.getElementById("af_athlete_id").value = "";
  document.getElementById("addAthleteForm").reset();
  document.getElementById("addAthleteMessage").style.display = "none";
  document.getElementById("athleteModalTitle").textContent =
    "Νέα Καταχώρηση Αθλητή";
  document.getElementById("addAthleteModal").style.display = "flex";
}

function closeAddAthleteModal() {
  document.getElementById("addAthleteModal").style.display = "none";
}

function editAthlete(btn) {
  const row = btn.closest("tr");
  let a = {};
  try {
    a = JSON.parse(row.getAttribute("data-athlete") || "{}");
  } catch (_) {}
  editAthleteData(a);
}

function editAthleteData(a) {
  document.getElementById("af_athlete_id").value = a.id || "";
  document.getElementById("af_first_name").value = a.first_name || "";
  document.getElementById("af_last_name").value = a.last_name || "";
  document.getElementById("af_birth_date").value = a.birth_date || "";
  document.getElementById("af_phone").value = a.phone || "";
  document.getElementById("af_location").value = a.location_id || "";
  document.getElementById("af_shoe_size").value = a.shoe_size || "";
  document.getElementById("af_shirt_size").value = a.shirt_size || "";
  document.getElementById("af_rides").checked = !!a.interest_rides;
  document.getElementById("af_races").checked = !!a.interest_races;
  document.getElementById("af_ski").checked = !!a.interest_ski;
  document.getElementById("af_skating").checked = !!a.interest_skating;
  document.getElementById("af_hockey").checked = !!a.interest_hockey;
  document.getElementById("af_amka").value = a.amka || "";
  document.getElementById("af_afm").value = a.afm || "";

  document.getElementById("addAthleteMessage").style.display = "none";
  document.getElementById("athleteModalTitle").textContent =
    "✏️ Επεξεργασία Αθλητή";
  document.getElementById("addAthleteModal").style.display = "flex";
}

// ── Save (add / update) ──────────────────────────────────────

document
  .getElementById("addAthleteForm")
  .addEventListener("submit", async (e) => {
    e.preventDefault();

    const msgEl = document.getElementById("addAthleteMessage");
    const btn = e.target.querySelector('[type="submit"]');
    btn.disabled = true;

    const payload = {
      athlete_id: document.getElementById("af_athlete_id").value || null,
      first_name: document.getElementById("af_first_name").value.trim(),
      last_name: document.getElementById("af_last_name").value.trim(),
      birth_date: document.getElementById("af_birth_date").value || null,
      phone: document.getElementById("af_phone").value.trim(),
      location_id: document.getElementById("af_location").value || null,
      shoe_size: document.getElementById("af_shoe_size").value.trim(),
      shirt_size: document.getElementById("af_shirt_size").value,
      interest_rides: document.getElementById("af_rides").checked,
      interest_races: document.getElementById("af_races").checked,
      interest_ski: document.getElementById("af_ski").checked,
      interest_skating: document.getElementById("af_skating").checked,
      interest_hockey: document.getElementById("af_hockey").checked,
      amka: document.getElementById("af_amka").value.trim() || null,
      afm: document.getElementById("af_afm").value.trim() || null,
    };

    try {
      const res = await fetch("save_admin_athlete.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
      const result = await res.json();

      msgEl.textContent = result.message;
      msgEl.style.color = result.status === "success" ? "#27ae60" : "#e74c3c";
      msgEl.style.display = "";

      if (result.status === "success") {
        setTimeout(() => location.reload(), 1200);
      }
    } catch {
      msgEl.textContent = "Σφάλμα επικοινωνίας.";
      msgEl.style.color = "#e74c3c";
      msgEl.style.display = "";
    } finally {
      btn.disabled = false;
    }
  });

// ── Delete ───────────────────────────────────────────────────

async function deleteAthlete(id, name) {
  if (!confirm(`Διαγραφή αθλητή "${name}";`)) return;

  try {
    const res = await fetch("delete_admin_athlete.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ athlete_id: id }),
    });
    const result = await res.json();
    if (result.status === "success") location.reload();
    else alert(result.message || "Σφάλμα διαγραφής.");
  } catch {
    alert("Σφάλμα επικοινωνίας.");
  }
}

// ── Filter + Search ──────────────────────────────────────────

let _activeLocationId = 0; // 0 = all, -1 = no location

function filterByRegion(locationId) {
  _activeLocationId = parseInt(locationId);

  // Συγχρονισμός του dropdown επιλογής περιοχής
  const selectEl = document.getElementById("regionFilter");
  if (selectEl && selectEl.value != _activeLocationId) {
    selectEl.value = _activeLocationId;
  }

  filterAthletes();
}

function filterAthletes() {
  const searchInput = document.getElementById("athleteSearch");
  const clearBtn = document.getElementById("clearSearchBtn");
  const term = searchInput.value.toLowerCase().trim();
  const locId = _activeLocationId;

  // Εμφάνιση / Απόκρυψη του 'X'
  if (clearBtn) {
    clearBtn.style.display = term.length > 0 ? "block" : "none";
  }

  const rows = document.querySelectorAll("#athletes-table-body tr.athlete-row");
  rows.forEach((row) => {
    const id = row.cells[0].textContent.toLowerCase();
    const name = row.cells[1].textContent.toLowerCase();
    const phone = row.cells[2].textContent.toLowerCase();
    const rowLoc = parseInt(row.getAttribute("data-location-id") || "0");

    const matchSearch =
      !term || id.includes(term) || name.includes(term) || phone.includes(term);
    const matchLoc =
      locId === 0 ||
      (locId === -1 && rowLoc === 0) ||
      (locId > 0 && rowLoc === locId);

    row.setAttribute(
      "data-filtered",
      matchSearch && matchLoc ? "false" : "true",
    );
    if (!matchSearch || !matchLoc) row.style.display = "none";
  });

  athleteCurrentPage = 1;
  displayAthletesPage();
}

function searchAthletes() {
  filterAthletes();
}

// ── Sort ─────────────────────────────────────────────────────

function sortAthletes() {
  const sortBy = document.getElementById("athleteSort").value;
  if (sortBy === "none") return;

  const tbody = document.getElementById("athletes-table-body");
  const rows = Array.from(tbody.querySelectorAll("tr.athlete-row"));

  rows.sort((a, b) => {
    if (sortBy === "name_asc" || sortBy === "name_desc") {
      const va = a.cells[1].textContent.trim(); // Index 1 λόγω της προσθήκης του ID
      const vb = b.cells[1].textContent.trim();
      return sortBy === "name_asc"
        ? va.localeCompare(vb, "el")
        : vb.localeCompare(va, "el");
    }
    if (sortBy === "birth_asc" || sortBy === "birth_desc") {
      const va = a.cells[3].textContent.trim(); // Index 3 λόγω της προσθήκης του ID
      const vb = b.cells[3].textContent.trim();
      const da = va === "—" ? 0 : new Date(va).getTime();
      const db = vb === "—" ? 0 : new Date(vb).getTime();
      return sortBy === "birth_asc" ? db - da : da - db;
    }
    if (sortBy === "loc_asc") {
      const va = a.getAttribute("data-location-name") || "";
      const vb = b.getAttribute("data-location-name") || "";
      return va.localeCompare(vb, "el");
    }
    return 0;
  });

  rows.forEach((row) => tbody.appendChild(row));
  displayAthletesPage();
}

function clearAthleteSearch() {
  const searchInput = document.getElementById("athleteSearch");
  searchInput.value = "";
  filterAthletes();
  searchInput.focus();
}

// ── Pagination ───────────────────────────────────────────────

let athleteCurrentPage = 1;
const athleteRowsPerPage = 10;

function displayAthletesPage() {
  const tbody = document.getElementById("athletes-table-body");
  const visible = Array.from(tbody.querySelectorAll("tr.athlete-row")).filter(
    (r) => r.getAttribute("data-filtered") !== "true",
  );

  const start = (athleteCurrentPage - 1) * athleteRowsPerPage;
  const end = start + athleteRowsPerPage;
  visible.forEach((row, i) => {
    row.style.display = i >= start && i < end ? "" : "none";
  });

  updateAthletesPagination(visible.length);
}

function updateAthletesPagination(total) {
  const totalPages = Math.ceil(total / athleteRowsPerPage);
  const container = document.getElementById("athletesPagination");
  container.innerHTML = "";
  if (totalPages <= 1) return;

  for (let i = 1; i <= totalPages; i++) {
    const btn = document.createElement("button");
    btn.innerText = i;
    btn.className =
      "page-num-btn" + (i === athleteCurrentPage ? " active" : "");
    btn.onclick = () => {
      athleteCurrentPage = i;
      displayAthletesPage();
      document
        .getElementById("athletesTable")
        .scrollIntoView({ behavior: "smooth", block: "nearest" });
    };
    container.appendChild(btn);
  }
}

// ── Init ─────────────────────────────────────────────────────

const savedTab = localStorage.getItem("activeTab") || "accounts-tab";
const menuLink = document.querySelector(`li[onclick*="${savedTab}"]`);
if (menuLink) {
  showTab({ currentTarget: menuLink }, savedTab);
} else {
  showTab(null, "accounts-tab");
}

document.addEventListener("DOMContentLoaded", () => {
  filterAthletes();

  const addModal = document.getElementById("addAthleteModal");
  if (addModal) {
    addModal.addEventListener("click", (e) => {
      if (e.target === addModal) closeAddAthleteModal();
    });
  }

  const profileModal = document.getElementById("athleteProfileModal");
  if (profileModal) {
    profileModal.addEventListener("click", (e) => {
      if (e.target === profileModal) closeAthleteProfileModal();
    });
  }
});
