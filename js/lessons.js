// js/lessons.js
// Classes tab: create/edit lessons, manage athletes, attendance.

// ── State ─────────────────────────────────────────────────────
let _currentLessonId = null;

// ── Create / Edit Modal ───────────────────────────────────────

function openAddClassModal() {
  document.getElementById("cf_lesson_id").value = "";
  document.getElementById("addClassForm").reset();
  document.getElementById("classFormMessage").style.display = "none";
  document.getElementById("classModalTitle").textContent = "Νέα Προπόνηση";
  document.getElementById("addClassModal").style.display = "flex";
}

function closeAddClassModal() {
  document.getElementById("addClassModal").style.display = "none";
}

function editLesson(btn) {
  const card = btn.closest(".class-card");
  let l = {};
  try {
    l = JSON.parse(card.getAttribute("data-lesson") || "{}");
  } catch (_) {}

  document.getElementById("cf_lesson_id").value = l.id || "";
  document.getElementById("cf_lesson_type").value = l.lesson_type || "rollers";
  document.getElementById("cf_location_id").value = l.location_id || "";
  document.getElementById("cf_title").value = l.title || "";
  document.getElementById("cf_status").value = l.status || "scheduled";
  document.getElementById("cf_weather").value = l.weather_condition || "";
  document.getElementById("cf_temperature").value =
    l.temperature !== null && l.temperature !== undefined ? l.temperature : "";
  document.getElementById("cf_notes").value = l.notes || "";

  // datetime-local needs "YYYY-MM-DDTHH:mm"
  document.getElementById("cf_datetime").value = l.lesson_datetime
    ? l.lesson_datetime.slice(0, 16)
    : "";

  document.getElementById("classFormMessage").style.display = "none";
  document.getElementById("classModalTitle").textContent =
    "✏️ Επεξεργασία Προπόνησης";
  document.getElementById("addClassModal").style.display = "flex";
}

document
  .getElementById("addClassForm")
  .addEventListener("submit", async (e) => {
    e.preventDefault();

    const msgEl = document.getElementById("classFormMessage");
    const btn = e.target.querySelector('[type="submit"]');
    btn.disabled = true;

    const tempVal = document.getElementById("cf_temperature").value;
    const payload = {
      lesson_id: document.getElementById("cf_lesson_id").value || null,
      title: document.getElementById("cf_title").value.trim(),
      lesson_type: document.getElementById("cf_lesson_type").value,
      location_id: document.getElementById("cf_location_id").value || null,
      lesson_datetime: document.getElementById("cf_datetime").value,
      weather_condition: document.getElementById("cf_weather").value,
      temperature: tempVal !== "" ? tempVal : null,
      notes: document.getElementById("cf_notes").value.trim(),
      status: document.getElementById("cf_status").value,
    };

    try {
      const res = await fetch("save_lesson.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
      const result = await res.json();

      msgEl.textContent = result.message;
      msgEl.style.color = result.status === "success" ? "#27ae60" : "#e74c3c";
      msgEl.style.display = "";

      if (result.status === "success") {
        setTimeout(() => location.reload(), 1000);
      }
    } catch {
      msgEl.textContent = "Σφάλμα επικοινωνίας.";
      msgEl.style.color = "#e74c3c";
      msgEl.style.display = "";
    } finally {
      btn.disabled = false;
    }
  });

// ── Delete Lesson ─────────────────────────────────────────────

async function deleteLesson(id) {
  if (
    !confirm("Διαγραφή προπόνησης; Θα αφαιρεθούν και όλες οι εγγραφές αθλητών.")
  )
    return;

  try {
    const res = await fetch("delete_lesson.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ lesson_id: id }),
    });
    const result = await res.json();
    if (result.status === "success") location.reload();
    else alert(result.message || "Σφάλμα διαγραφής.");
  } catch {
    alert("Σφάλμα επικοινωνίας.");
  }
}

// ── Manage Athletes Modal ─────────────────────────────────────

async function manageClass(lessonId) {
  _currentLessonId = lessonId;

  document.getElementById("manageClassTitle").textContent = "Φόρτωση…";
  document.getElementById("manageClassSubtitle").textContent = "";
  document.getElementById("enrolledAthletesList").innerHTML =
    '<p class="loading-msg">Φόρτωση αθλητών…</p>';
  document.getElementById("athleteSearchInput").value = "";
  if (document.getElementById("clearAthleteSearch")) {
    document.getElementById("clearAthleteSearch").style.display = "none";
  }

  if (document.getElementById("athleteSearchLocation")) {
    document.getElementById("athleteSearchLocation").value = ""; // Reset τοποθεσίας
  }
  document.getElementById("athleteSearchResults").innerHTML = "";
  document.getElementById("manageClassModal").style.display = "flex";

  try {
    const res = await fetch("get_lesson_details.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ lesson_id: lessonId }),
    });
    const result = await res.json();
    if (result.status !== "success") {
      alert(result.message);
      return;
    }

    const l = result.lesson;
    const dt = new Date(l.lesson_datetime);
    const dateStr = dt.toLocaleDateString("el-GR", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
    });
    const timeStr = dt.toLocaleTimeString("el-GR", {
      hour: "2-digit",
      minute: "2-digit",
    });

    const typeLabels = {
      rollers: "🛼 Rollers",
      iceskate: "⛸️ Ice Skate",
      hockey: "🏒 Hockey",
      ski: "⛷️ Ski",
      fitness: "🏋️ Fitness",
    };

    document.getElementById("manageClassTitle").textContent =
      typeLabels[l.lesson_type] || l.lesson_type;
    document.getElementById("manageClassSubtitle").textContent =
      (l.location_name ? l.location_name + "  ·  " : "") +
      dateStr +
      "  " +
      timeStr;

    renderEnrolledAthletes(result.athletes);
    _doSearch(); // pre-load available athletes
  } catch {
    document.getElementById("enrolledAthletesList").innerHTML =
      '<p style="color:red">Σφάλμα φόρτωσης.</p>';
  }
}

function renderEnrolledAthletes(athletes) {
  const list = document.getElementById("enrolledAthletesList");
  document.getElementById("enrolledCount").textContent = athletes.length;

  if (!athletes.length) {
    list.innerHTML =
      '<p class="empty-enrolled">Δεν υπάρχουν εγγεγραμμένοι αθλητές.</p>';
    return;
  }

  list.innerHTML = athletes
    .map(
      (a) => `
    <div class="enrolled-athlete-row" id="enrolled-${a.id}">
      <label class="attendance-label">
        <input type="checkbox" class="attendance-cb"
               ${a.attended ? "checked" : ""}
               onchange="toggleAttendance(${a.id}, this.checked)">
        <span class="attendance-dot ${a.attended ? "present" : "absent"}"></span>
      </label>
      <div class="enrolled-info">
        <span class="enrolled-name">${escHtml(a.first_name + " " + a.last_name)}</span>
        ${a.location_name ? `<span class="enrolled-meta">${escHtml(a.location_name)}</span>` : ""}
      </div>
      <button class="remove-athlete-btn" onclick="removeAthleteFromLesson(${a.id})" title="Αφαίρεση">✕</button>
    </div>
  `,
    )
    .join("");
}

function closeManageClassModal() {
  document.getElementById("manageClassModal").style.display = "none";
  _currentLessonId = null;
}

// ── Search & Add Athletes ─────────────────────────────────────

let _searchTimer = null;

function searchAthletesForLesson() {
  clearTimeout(_searchTimer);
  _searchTimer = setTimeout(_doSearch, 280);
}

async function _doSearch() {
  const term = document.getElementById("athleteSearchInput").value.trim();
  const locationId =
    document.getElementById("athleteSearchLocation")?.value || "";
  const resultsEl = document.getElementById("athleteSearchResults");

  resultsEl.innerHTML = '<p class="loading-msg">Αναζήτηση…</p>';

  try {
    const res = await fetch("search_athletes_for_lesson.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        term: term,
        lesson_id: _currentLessonId,
        location_id: locationId,
      }),
    });
    const result = await res.json();

    if (!result.athletes.length) {
      resultsEl.innerHTML = '<p class="no-results">Δεν βρέθηκαν αθλητές.</p>';
      return;
    }

    resultsEl.innerHTML = result.athletes
      .map(
        (a) => `
      <div class="search-result-row" onclick="addAthleteToLesson(${a.id}, '${escAttr(a.full_name)}')">
        <span class="search-result-name">${escHtml(a.full_name)}</span>
        ${a.location_name ? `<span class="search-result-meta">${escHtml(a.location_name)}</span>` : ""}
      </div>
    `,
      )
      .join("");
  } catch {
    resultsEl.innerHTML = '<p style="color:red">Σφάλμα αναζήτησης.</p>';
  }
}

async function addAthleteToLesson(athleteId) {
  try {
    const res = await fetch("add_athlete_to_lesson.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        lesson_id: _currentLessonId,
        athlete_id: athleteId,
      }),
    });
    const result = await res.json();
    if (result.status !== "success") {
      alert(result.message);
      return;
    }

    await _refreshEnrolled();
    _doSearch(); // refresh available list (removes just-added athlete)
    _updateCardCount();
  } catch {
    alert("Σφάλμα επικοινωνίας.");
  }
}

async function removeAthleteFromLesson(athleteId) {
  try {
    const res = await fetch("remove_athlete_from_lesson.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        lesson_id: _currentLessonId,
        athlete_id: athleteId,
      }),
    });
    const result = await res.json();
    if (result.status !== "success") {
      alert(result.message);
      return;
    }

    const row = document.getElementById("enrolled-" + athleteId);
    if (row) row.remove();

    const enrolled = document.querySelectorAll(
      "#enrolledAthletesList .enrolled-athlete-row",
    );
    document.getElementById("enrolledCount").textContent = enrolled.length;
    if (!enrolled.length) {
      document.getElementById("enrolledAthletesList").innerHTML =
        '<p class="empty-enrolled">Δεν υπάρχουν εγγεγραμμένοι αθλητές.</p>';
    }
    _doSearch(); // refresh available list (re-adds removed athlete)
    _updateCardCount();
  } catch {
    alert("Σφάλμα επικοινωνίας.");
  }
}

// ── Attendance ────────────────────────────────────────────────

async function toggleAttendance(athleteId, attended) {
  const dot = document.querySelector(`#enrolled-${athleteId} .attendance-dot`);

  try {
    const res = await fetch("toggle_attendance.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        lesson_id: _currentLessonId,
        athlete_id: athleteId,
        attended,
      }),
    });
    const result = await res.json();
    if (result.status === "success" && dot) {
      dot.className = "attendance-dot " + (attended ? "present" : "absent");
    }
  } catch {
    const cb = document.querySelector(`#enrolled-${athleteId} .attendance-cb`);
    if (cb) cb.checked = !attended;
  }
}

// ── Helpers ───────────────────────────────────────────────────

async function _refreshEnrolled() {
  try {
    const res = await fetch("get_lesson_details.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ lesson_id: _currentLessonId }),
    });
    const result = await res.json();
    if (result.status === "success") renderEnrolledAthletes(result.athletes);
  } catch {
    /* silent */
  }
}

function _updateCardCount() {
  const count = document.querySelectorAll(
    "#enrolledAthletesList .enrolled-athlete-row",
  ).length;
  const span = document.getElementById("card-count-" + _currentLessonId);
  if (span) span.textContent = "👥 " + count + " αθλητές";
}

function escHtml(str) {
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}
function escAttr(str) {
  return String(str).replace(/'/g, "\\'");
}

// ── Init ──────────────────────────────────────────────────────

document.addEventListener("DOMContentLoaded", () => {
  const addModal = document.getElementById("addClassModal");
  if (addModal)
    addModal.addEventListener("click", (e) => {
      if (e.target === addModal) closeAddClassModal();
    });

  const manageModal = document.getElementById("manageClassModal");
  if (manageModal)
    manageModal.addEventListener("click", (e) => {
      if (e.target === manageModal) closeManageClassModal();
    });
});

// 1. Μεταβλητή για το Timer του Debounce
let searchTimeout = null;

// 2. Συνάρτηση Debounce (περιμένει 250ms αφού σταματήσει η πληκτρολόγηση)
function debouncedSearch() {
  const input = document.getElementById("athleteSearchInput");
  const clearBtn = document.getElementById("clearAthleteSearch");

  // Εμφάνιση / Απόκρυψη του "X" ανάλογα με το αν υπάρχει κείμενο
  if (clearBtn) {
    clearBtn.style.display = input.value.length > 0 ? "block" : "none";
  }

  // Ακύρωση του προηγούμενου timer αν ο χρήστης συνεχίζει να πληκτρολογεί
  clearTimeout(searchTimeout);

  // Έναρξη νέου timer 250ms
  searchTimeout = setTimeout(() => {
    _doSearch();
  }, 250);
}

// 3. Συνάρτηση για καθαρισμό του πεδίου με το "X"
function clearSearchInput() {
  const input = document.getElementById("athleteSearchInput");
  const clearBtn = document.getElementById("clearAthleteSearch");

  if (input) {
    input.value = "";
    if (clearBtn) clearBtn.style.display = "none";
    _doSearch(); // Επανεκτέλεση αναζήτησης για να φέρει την αρχική λίστα
  }
}

// ── Search, Filtering & Pagination State ─────────────────────
let _classesCurrentPage = 1;
const _classesItemsPerPage = 9;
let _classesSearchTimeout = null;

// 1. Debounce για την αναζήτηση (250ms)
function debouncedClassesSearch() {
  const input = document.getElementById("classesSearchInput");
  const clearBtn = document.getElementById("clearClassesSearch");

  if (clearBtn) {
    clearBtn.style.display = input.value.length > 0 ? "block" : "none";
  }

  clearTimeout(_classesSearchTimeout);
  _classesSearchTimeout = setTimeout(() => {
    filterClasses();
  }, 250);
}

// 2. Καθαρισμός πεδίου αναζήτησης
function clearClassesSearchInput() {
  const input = document.getElementById("classesSearchInput");
  const clearBtn = document.getElementById("clearClassesSearch");

  if (input) {
    input.value = "";
    if (clearBtn) clearBtn.style.display = "none";
    filterClasses();
  }
}

// 3. Επιστρέφει τις φιλτραρισμένες κάρτες
function _getFilteredClassCards() {
  const container = document.getElementById("classes-container");
  if (!container) return [];

  const cards = Array.from(container.querySelectorAll(".class-card"));
  const searchVal = (document.getElementById("classesSearchInput")?.value || "")
    .toLowerCase()
    .trim();
  const locationVal =
    document.getElementById("classesLocationFilter")?.value || ""; // 📍 ΝΕΟ
  const typeVal = document.getElementById("classesTypeFilter")?.value || "";
  const statusVal = document.getElementById("classesStatusFilter")?.value || "";
  const timeVal = document.getElementById("classesTimeFilter")?.value || "";

  const todayStr = new Date().toISOString().slice(0, 10);

  return cards.filter((card) => {
    let l = {};
    try {
      l = JSON.parse(card.getAttribute("data-lesson") || "{}");
    } catch (_) {}

    // 1. Text Search (Τίτλος ή Σημειώσεις)
    if (searchVal) {
      const title = (
        l.title ||
        card.querySelector(".class-card-title")?.textContent ||
        ""
      ).toLowerCase();
      const notes = (l.notes || "").toLowerCase();

      if (!title.includes(searchVal) && !notes.includes(searchVal)) {
        return false;
      }
    }

    // 2. 📍 Location Filter
    if (locationVal && (l.location_name || "") !== locationVal) {
      return false;
    }

    // 3. Type Filter
    if (typeVal && l.lesson_type !== typeVal) {
      return false;
    }

    // 4. Status Filter
    if (statusVal && l.status !== statusVal) {
      return false;
    }

    // 5. Time Filter
    if (timeVal && l.lesson_datetime) {
      const lessonDate = l.lesson_datetime.slice(0, 10);
      if (timeVal === "today" && lessonDate !== todayStr) return false;
      if (timeVal === "upcoming" && lessonDate < todayStr) return false;
      if (timeVal === "past" && lessonDate >= todayStr) return false;
    }

    return true;
  });
}

// 4. Κύρια συνάρτηση φιλτραρίσματος
function filterClasses() {
  _classesCurrentPage = 1; // Επαναφορά στην 1η σελίδα
  _classesGoToPage(1);
}

// 5. Πλοήγηση σελίδων
function _classesGoToPage(page) {
  const container = document.getElementById("classes-container");
  if (!container) return;

  const allCards = Array.from(container.querySelectorAll(".class-card"));
  const filteredCards = _getFilteredClassCards();

  // Απόκρυψη όλων των καρτών αρχικά
  allCards.forEach((card) => (card.style.display = "none"));

  if (!filteredCards.length) {
    _renderClassesPagination(0, 1, _classesItemsPerPage);
    return;
  }

  const totalPages = Math.ceil(filteredCards.length / _classesItemsPerPage);
  _classesCurrentPage = Math.max(1, Math.min(page, totalPages));

  const startIdx = (_classesCurrentPage - 1) * _classesItemsPerPage;
  const endIdx = startIdx + _classesItemsPerPage;

  // Εμφάνιση μόνο των φιλτραρισμένων καρτών της τρέχουσας σελίδας
  filteredCards.slice(startIdx, endIdx).forEach((card) => {
    card.style.display = "";
  });

  _renderClassesPagination(
    filteredCards.length,
    _classesCurrentPage,
    _classesItemsPerPage,
  );
}

// 6. Render Pagination Controls
function _renderClassesPagination(totalItems, currentPage, itemsPerPage) {
  const paginationEl = document.getElementById("classesPagination");
  if (!paginationEl) return;

  if (totalItems === 0) {
    paginationEl.innerHTML =
      '<p class="empty-state" style="text-align:center; margin-top:20px;">Δεν βρέθηκαν προπονήσεις με τα συγκεκριμένα φίλτρα.</p>';
    return;
  }

  const totalPages = Math.ceil(totalItems / itemsPerPage);
  if (totalPages <= 1) {
    paginationEl.innerHTML = "";
    return;
  }

  let html =
    '<div style="display:flex; gap:8px; justify-content:center; flex-wrap:wrap; margin-top:20px;">';

  if (currentPage > 1) {
    html += `<button class="action-btn btn-secondary" onclick="_classesGoToPage(${currentPage - 1})">← Προηγ.</button>`;
  }

  const startPage = Math.max(1, currentPage - 2);
  const endPage = Math.min(totalPages, currentPage + 2);

  if (startPage > 1) {
    html += `<button class="action-btn" onclick="_classesGoToPage(1)">1</button>`;
    if (startPage > 2) html += '<span style="padding: 8px;">...</span>';
  }

  for (let i = startPage; i <= endPage; i++) {
    if (i === currentPage) {
      html += `<button class="action-btn btn-primary" style="background:#f39c12;color:white;">${i}</button>`;
    } else {
      html += `<button class="action-btn" onclick="_classesGoToPage(${i})">${i}</button>`;
    }
  }

  if (endPage < totalPages) {
    if (endPage < totalPages - 1)
      html += '<span style="padding: 8px;">...</span>';
    html += `<button class="action-btn" onclick="_classesGoToPage(${totalPages})">${totalPages}</button>`;
  }

  if (currentPage < totalPages) {
    html += `<button class="action-btn btn-secondary" onclick="_classesGoToPage(${currentPage + 1})">Επόμ. →</button>`;
  }

  html += "</div>";
  paginationEl.innerHTML = html;
}

function populateClassesLocationFilter() {
  const sel = document.getElementById("classesLocationFilter");
  const container = document.getElementById("classes-container");
  if (!sel || !container) return;

  const cards = Array.from(container.querySelectorAll(".class-card"));
  const locations = new Set();

  cards.forEach((card) => {
    try {
      const l = JSON.parse(card.getAttribute("data-lesson") || "{}");
      if (l.location_name) locations.add(l.location_name);
    } catch (_) {}
  });

  const sortedLocations = Array.from(locations).sort();
  sel.innerHTML =
    '<option value="">Όλες οι τοποθεσίες</option>' +
    sortedLocations
      .map((loc) => `<option value="${escAttr(loc)}">${escHtml(loc)}</option>`)
      .join("");
}

// Αρχικοποίηση κατά τη φόρτωση
document.addEventListener("DOMContentLoaded", () => {
  filterClasses();
  populateClassesLocationFilter();
});
