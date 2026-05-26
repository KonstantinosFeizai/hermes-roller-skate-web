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
  const resultsEl = document.getElementById("athleteSearchResults");

  if (term.length === 1) {
    resultsEl.innerHTML = "";
    return;
  }

  resultsEl.innerHTML = '<p class="loading-msg">Αναζήτηση…</p>';

  try {
    const res = await fetch("search_athletes_for_lesson.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ term, lesson_id: _currentLessonId }),
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
