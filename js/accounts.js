// ----------------------------------------------------
// ACCOUNTS TAB — profile modal, role/status/edit/delete actions
// ----------------------------------------------------

let _activeProfileUser = null;

function openUserProfile(btn) {
  const row = btn.closest("tr");
  const raw = row.getAttribute("data-user");
  if (!raw) return;
  const user = JSON.parse(raw);
  _activeProfileUser = user;
  _renderProfileView(user);
  exitProfileEditMode();
  document.getElementById("userProfileModal").style.display = "flex";
}

function _renderProfileView(user) {
  const fullName =
    [user.first_name, user.last_name].filter(Boolean).join(" ") ||
    user.username;
  const initials = (
    (user.first_name?.[0] || user.username[0]) + (user.last_name?.[0] || "")
  ).toUpperCase();

  document.getElementById("profileAvatar").textContent = initials || "?";
  document.getElementById("profileFullName").textContent = fullName;
  document.getElementById("profileUsernameDisplay").textContent =
    "@" + user.username;
  document.getElementById("profileEmail").textContent = user.email || "—";
  document.getElementById("profilePhone").textContent = user.phone || "—";
  document.getElementById("profileRegion").textContent = user.region || "—";
  document.getElementById("profileAge").textContent = user.age || "—";

  const roleEl = document.getElementById("profileRoleDisplay");
  roleEl.textContent = user.role.toUpperCase();
  roleEl.className = "profile-info-value role-badge role-" + user.role;

  const rtLabels = {
    athlete: "Αθλητής",
    parent: "Γονέας",
    coach: "Προπονητής",
  };
  const rtEl = document.getElementById("profileRoleTypeDisplay");
  if (rtEl) {
    const rt = user.role_type || "";
    if (rtLabels[rt]) {
      rtEl.innerHTML = `<span class="role-type-badge role-type-${rt}">${rtLabels[rt]}</span>`;
    } else {
      rtEl.textContent = "—";
    }
  }

  _refreshProfileStatusUI(user.is_active);

  const d = new Date(user.created_at);
  document.getElementById("profileCreatedAt").textContent =
    d.toLocaleDateString("el-GR", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
    });
}

function _refreshProfileStatusUI(isActive) {
  const statusEl = document.getElementById("profileStatusDisplay");
  statusEl.textContent = isActive ? "Ενεργός" : "Εκκρεμεί";
  statusEl.className =
    "profile-info-value " + (isActive ? "status-confirmed" : "status-pending");

  const toggleBtn = document.getElementById("profileToggleStatusBtn");
  toggleBtn.textContent = isActive ? "⏸ Απενεργοποίηση" : "▶ Ενεργοποίηση";
}

function closeUserProfileModal() {
  exitProfileEditMode();
  document.getElementById("userProfileModal").style.display = "none";
  _activeProfileUser = null;
  const listEl = document.getElementById("profileAthletesList");
  if (listEl) listEl.innerHTML = "";
}

// ----------------------------------------------------
// EDIT MODE
// ----------------------------------------------------

function enterProfileEditMode() {
  if (!_activeProfileUser) return;
  const u = _activeProfileUser;

  document.getElementById("editFirstName").value = u.first_name || "";
  document.getElementById("editLastName").value = u.last_name || "";
  document.getElementById("editEmail").value = u.email || "";
  document.getElementById("editPhone").value = u.phone || "";
  document.getElementById("editLocationId").value = u.location_id || "";
  document.getElementById("editAge").value = u.age || "";

  const msgEl = document.getElementById("profileEditMessage");
  msgEl.style.display = "none";
  msgEl.textContent = "";

  document.getElementById("profileInfoGrid").style.display = "none";
  document.getElementById("profileEditForm").style.display = "";
  document.getElementById("profileViewActions").style.display = "none";
  document.getElementById("profileEditActions").style.display = "";
}

function exitProfileEditMode() {
  document.getElementById("profileInfoGrid").style.display = "";
  document.getElementById("profileEditForm").style.display = "none";
  document.getElementById("profileViewActions").style.display = "";
  document.getElementById("profileEditActions").style.display = "none";
}

async function saveProfileEdit() {
  if (!_activeProfileUser) return;

  const payload = {
    user_id: _activeProfileUser.id,
    first_name: document.getElementById("editFirstName").value.trim(),
    last_name: document.getElementById("editLastName").value.trim(),
    email: document.getElementById("editEmail").value.trim(),
    phone: document.getElementById("editPhone").value.trim(),
    location_id: document.getElementById("editLocationId").value || "",
    age: document.getElementById("editAge").value || "",
  };

  try {
    const res = await fetch("update_user_profile.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });
    const result = await res.json();

    if (result.success) {
      Object.assign(_activeProfileUser, payload);
      exitProfileEditMode();
      _renderProfileView(_activeProfileUser);
      _updateTableRow(_activeProfileUser);
    } else {
      const msgEl = document.getElementById("profileEditMessage");
      msgEl.textContent = result.message || "Σφάλμα αποθήκευσης.";
      msgEl.className = "form-message form-message--error";
      msgEl.style.display = "";
    }
  } catch {
    alert("Σφάλμα επικοινωνίας.");
  }
}

function _updateTableRow(user) {
  document.querySelectorAll("#accounts-table-body tr").forEach((row) => {
    try {
      const u = JSON.parse(row.getAttribute("data-user") || "{}");
      if (u.id !== user.id) return;

      const fullName =
        [user.first_name, user.last_name].filter(Boolean).join(" ") ||
        user.username;
      const nameCell = row.cells[1];
      const fnSpan = nameCell.querySelector(".user-fullname");
      const unSpan = nameCell.querySelector(".user-username");
      if (fnSpan) fnSpan.textContent = fullName;
      if (unSpan) unSpan.textContent = "@" + user.username;

      row.cells[2].textContent = user.email;
      row.cells[3].textContent = user.phone || "—";

      // Sync embedded data attribute
      Object.assign(u, user);
      row.setAttribute("data-user", JSON.stringify(u));
    } catch (_) {}
  });
}

// ----------------------------------------------------
// STATUS TOGGLE
// ----------------------------------------------------

function _updateRowStatusBadge(userId, isActive) {
  document.querySelectorAll("#accounts-table-body tr").forEach((row) => {
    try {
      const u = JSON.parse(row.getAttribute("data-user") || "{}");
      if (u.id !== userId) return;
      const span = row.cells[5]?.querySelector("span");
      if (span) {
        span.className = isActive ? "status-confirmed" : "status-pending";
        span.textContent = isActive ? "Ενεργός" : "Εκκρεμεί";
      }
      u.is_active = isActive;
      row.setAttribute("data-user", JSON.stringify(u));
    } catch (_) {}
  });
}

async function toggleUserStatusFromProfile() {
  if (!_activeProfileUser) return;
  const { id, is_active } = _activeProfileUser;
  const label = is_active ? "απενεργοποιήσετε" : "ενεργοποιήσετε";
  if (!confirm(`Θέλετε να ${label} αυτόν τον χρήστη;`)) return;

  try {
    const res = await fetch("toggle_user_status.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ user_id: id }),
    });
    const result = await res.json();
    if (result.success) {
      _activeProfileUser.is_active = result.is_active;
      _refreshProfileStatusUI(result.is_active);
      _updateRowStatusBadge(id, result.is_active);
    } else {
      alert(result.message || "Σφάλμα.");
    }
  } catch {
    alert("Σφάλμα επικοινωνίας.");
  }
}

// ----------------------------------------------------
// PASSWORD RESET
// ----------------------------------------------------

async function sendPasswordResetFromProfile() {
  if (!_activeProfileUser) return;
  if (
    !confirm(
      `Αποστολή email επαναφοράς κωδικού στο ${_activeProfileUser.email};`,
    )
  )
    return;

  try {
    const res = await fetch("admin_send_password_reset.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ user_id: _activeProfileUser.id }),
    });
    const result = await res.json();
    alert(result.message || (result.success ? "Email εστάλη." : "Σφάλμα."));
  } catch {
    alert("Σφάλμα επικοινωνίας.");
  }
}

// ----------------------------------------------------
// ROLE CHANGE & DELETE
// ----------------------------------------------------

async function changeRoleFromProfile() {
  if (!_activeProfileUser) return;
  await changeRole(_activeProfileUser.id, _activeProfileUser.role);
}

async function deleteUserFromProfile() {
  if (!_activeProfileUser) return;
  closeUserProfileModal();
  await deleteUser(_activeProfileUser.id);
}

async function changeRole(userId, currentRole) {
  const newRole = currentRole === "admin" ? "user" : "admin";
  if (!confirm(`Θέλετε να αλλάξετε τον ρόλο σε ${newRole.toUpperCase()};`))
    return;

  try {
    const res = await fetch("admin_change_role.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ user_id: userId, new_role: newRole }),
    });
    const result = await res.json();
    if (res.ok) {
      location.reload();
    } else {
      alert(result.message);
    }
  } catch {
    alert("Σφάλμα επικοινωνίας.");
  }
}

async function deleteUser(userId) {
  if (
    !confirm(
      "Είστε σίγουροι ότι θέλετε να ΔΙΑΓΡΑΨΕΤΕ αυτόν τον χρήστη; Η ενέργεια δεν αναιρείται.",
    )
  )
    return;

  try {
    const res = await fetch("admin_delete_user.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ user_id: userId }),
    });
    const result = await res.json();
    if (res.ok) {
      location.reload();
    } else {
      alert(result.message);
    }
  } catch {
    alert("Σφάλμα επικοινωνίας.");
  }
}

// ----------------------------------------------------
// USER ATHLETES (admin view)
// ----------------------------------------------------

async function loadUserAthletes() {
  if (!_activeProfileUser) return;
  const listEl = document.getElementById("profileAthletesList");
  if (!listEl) return;

  listEl.innerHTML = '<p style="color:#888;font-size:0.82rem;">Φόρτωση...</p>';

  try {
    const res = await fetch("get_user_athletes.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ user_id: _activeProfileUser.id }),
    });
    const data = await res.json();

    if (data.status !== "success") {
      listEl.innerHTML = `<p style="color:#e74c3c;font-size:0.82rem;">${data.message || "Σφάλμα."}</p>`;
      return;
    }

    const athletes = data.athletes || [];
    if (athletes.length === 0) {
      listEl.innerHTML =
        '<p style="color:#888;font-size:0.82rem;">Δεν υπάρχουν καρτέλες αθλητή.</p>';
      return;
    }

    const rtLabels = {
      athlete: "Αθλητής",
      parent: "Γονέας",
      coach: "Προπονητής",
    };

    listEl.innerHTML = athletes
      .map((a) => {
        const name = [a.first_name, a.last_name].filter(Boolean).join(" ");
        const birth = a.birth_date ? `🎂 ${a.birth_date}` : "";
        const loc = a.location_name ? `📍 ${a.location_name}` : "";
        const shoe = a.shoe_size ? `👟 ${a.shoe_size}` : "";
        const shirt = a.shirt_size ? `👕 ${a.shirt_size}` : "";
        const meta = [birth, loc, shoe, shirt].filter(Boolean).join("  ·  ");

        const interests = [];
        if (a.interest_rides) interests.push("🛼 Βόλτες");
        if (a.interest_races) interests.push("🏁 Αγώνες");
        if (a.interest_ski) interests.push("⛷️ Σκι");
        if (a.interest_skating) interests.push("⛸️ Πατινάζ");
        if (a.interest_hockey) interests.push("🏒 Χόκεϊ");

        return `
        <div class="modal-athlete-card">
          <div>
            <div class="modal-athlete-name">${name}</div>
            ${meta ? `<div class="modal-athlete-meta">${meta}</div>` : ""}
            ${interests.length ? `<div class="modal-athlete-meta">${interests.join("  ")}</div>` : ""}
          </div>
        </div>`;
      })
      .join("");
  } catch {
    listEl.innerHTML =
      '<p style="color:#e74c3c;font-size:0.82rem;">Αδυναμία σύνδεσης.</p>';
  }
}

// ----------------------------------------------------
// SEARCH & FILTER
// ----------------------------------------------------

const searchInput = document.getElementById("userSearchInput");
const roleFilter = document.getElementById("roleFilter");
const statusFilter = document.getElementById("statusFilter");
const tableRows = document
  .getElementById("accounts-table-body")
  .querySelectorAll("tr");

function filterUsers() {
  const searchTerm = searchInput.value.toLowerCase();
  const roleValue = roleFilter.value.toLowerCase();
  const statusValue = statusFilter.value.toLowerCase();

  tableRows.forEach((row) => {
    let u = {};
    try {
      u = JSON.parse(row.getAttribute("data-user") || "{}");
    } catch (_) {}

    const name = (
      (u.first_name || "") +
      " " +
      (u.last_name || "")
    ).toLowerCase();
    const username = (u.username || "").toLowerCase();
    const email = (u.email || "").toLowerCase();
    const role = (u.role || "").toLowerCase();
    const status = u.is_active ? "active" : "inactive";

    const matchesSearch =
      !searchTerm ||
      name.includes(searchTerm) ||
      username.includes(searchTerm) ||
      email.includes(searchTerm);
    const matchesRole = roleValue === "all" || role === roleValue;
    const matchesStatus = statusValue === "all" || status === statusValue;

    if (matchesSearch && matchesRole && matchesStatus) {
      row.setAttribute("data-filtered", "false");
    } else {
      row.setAttribute("data-filtered", "true");
      row.style.display = "none";
    }
  });

  currentPage = 1;
  displayPage();
}

if (searchInput) searchInput.addEventListener("input", filterUsers);
if (roleFilter) roleFilter.addEventListener("change", filterUsers);
if (statusFilter) statusFilter.addEventListener("change", filterUsers);

// ----------------------------------------------------
// TABLE SORTING
// ----------------------------------------------------

document
  .querySelectorAll("#userTableHeader th[data-sort]")
  .forEach((header) => {
    header.addEventListener("click", () => {
      const tbody = header.closest("table").querySelector("tbody");
      const rows = Array.from(tbody.querySelectorAll("tr"));
      const index = Array.from(header.parentNode.children).indexOf(header);
      const type = header.getAttribute("data-sort");
      const isAsc = header.classList.contains("sort-asc");

      rows.sort((a, b) => {
        const cellA = a.cells[index].textContent.trim();
        const cellB = b.cells[index].textContent.trim();
        if (type === "number") return isAsc ? cellB - cellA : cellA - cellB;
        if (type === "date") {
          const parse = (s) => {
            const [d, m, y] = s.split("/");
            return new Date(`20${y}`, m - 1, d);
          };
          return isAsc
            ? parse(cellB) - parse(cellA)
            : parse(cellA) - parse(cellB);
        }
        return isAsc
          ? cellB.localeCompare(cellA, "el")
          : cellA.localeCompare(cellB, "el");
      });

      while (tbody.firstChild) tbody.removeChild(tbody.firstChild);
      tbody.append(...rows);

      document
        .querySelectorAll("#userTableHeader th")
        .forEach((th) => th.classList.remove("sort-asc", "sort-desc"));
      header.classList.add(isAsc ? "sort-desc" : "sort-asc");
    });
  });

// ----------------------------------------------------
// PAGINATION
// ----------------------------------------------------

let currentPage = 1;
const rowsPerPage = 10;

function updatePagination() {
  const tbody = document.getElementById("accounts-table-body");
  if (!tbody) return;
  const rows = Array.from(tbody.querySelectorAll("tr")).filter(
    (r) => r.getAttribute("data-filtered") !== "true",
  );
  const totalPages = Math.ceil(rows.length / rowsPerPage);
  const container = document.getElementById("paginationControls");
  container.innerHTML = "";
  if (totalPages <= 1) return;

  for (let i = 1; i <= totalPages; i++) {
    const btn = document.createElement("button");
    btn.innerText = i;
    btn.className = "page-num-btn" + (i === currentPage ? " active" : "");
    btn.addEventListener("click", () => {
      currentPage = i;
      displayPage();
      tbody.scrollIntoView({ behavior: "smooth", block: "nearest" });
    });
    container.appendChild(btn);
  }
}

function displayPage() {
  const tbody = document.getElementById("accounts-table-body");
  if (!tbody) return;
  const rows = Array.from(tbody.querySelectorAll("tr")).filter(
    (r) => r.getAttribute("data-filtered") !== "true",
  );
  const start = (currentPage - 1) * rowsPerPage;
  const end = start + rowsPerPage;
  rows.forEach((row, i) => {
    row.style.display = i >= start && i < end ? "" : "none";
  });
  updatePagination();
}

document.addEventListener("DOMContentLoaded", () => {
  displayPage();

  const modal = document.getElementById("userProfileModal");
  if (modal) {
    modal.addEventListener("click", (e) => {
      if (e.target === modal) closeUserProfileModal();
    });
  }
});

function initAccountsTab() {
  filterUsers();
}
