// ----------------------------------------------------
// 11. ADMIN ACTIONS
// ----------------------------------------------------

async function changeRole(userId, currentRole) {
  const newRole = currentRole === "admin" ? "user" : "admin";
  if (
    !confirm(
      `Θέλετε να αλλάξετε τον ρόλο του χρήστη σε ${newRole.toUpperCase()};`,
    )
  )
    return;

  try {
    const response = await fetch(BASE_URL + "admin/admin_change_role.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ user_id: userId, new_role: newRole }),
    });
    const result = await response.json();
    if (response.ok) {
      location.reload(); // Ανανέωση για να φανεί η αλλαγή
    } else {
      alert(result.message);
    }
  } catch (error) {
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
    const response = await fetch(BASE_URL + "admin/admin_delete_user.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ user_id: userId }),
    });
    const result = await response.json();
    if (response.ok) {
      location.reload();
    } else {
      alert(result.message);
    }
  } catch (error) {
    alert("Σφάλμα επικοινωνίας.");
  }
}

// ----------------------------------------------------
// 12. DYNAMIC SEARCH & FILTER LOGIC
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
    // Λήψη δεδομένων από τις στήλες (Username: 2η, Email: 3η, Role: 4η, Status: 5η)
    const username = row.cells[1].textContent.toLowerCase();
    const email = row.cells[2].textContent.toLowerCase();
    const role = row.cells[3].textContent.toLowerCase();
    const statusText = row.cells[4].textContent.trim(); // "Ενεργός" ή "Εκκρεμεί"

    // Μετατροπή λεκτικού status σε τιμή φίλτρου
    const status = statusText === "Ενεργός" ? "active" : "inactive";

    const matchesSearch =
      username.includes(searchTerm) || email.includes(searchTerm);
    const matchesRole = roleValue === "all" || role.includes(roleValue);
    const matchesStatus = statusValue === "all" || status === statusValue;

    // Εμφάνιση ή απόκρυψη της σειράς
    if (matchesSearch && matchesRole && matchesStatus) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });
}

// Listeners για κάθε αλλαγή στα φίλτρα
if (searchInput) searchInput.addEventListener("input", filterUsers);
if (roleFilter) roleFilter.addEventListener("change", filterUsers);
if (statusFilter) statusFilter.addEventListener("change", filterUsers);

// ----------------------------------------------------
// 13. TABLE SORTING LOGIC
// ----------------------------------------------------

document
  .querySelectorAll("#userTableHeader th[data-sort]")
  .forEach((header) => {
    header.addEventListener("click", () => {
      const table = header.closest("table");
      const tbody = table.querySelector("tbody");
      const rows = Array.from(tbody.querySelectorAll("tr"));
      const index = Array.from(header.parentNode.children).indexOf(header);
      const type = header.getAttribute("data-sort");
      const isAscending = header.classList.contains("sort-asc");

      // Ταξινόμηση των σειρών
      rows.sort((rowA, rowB) => {
        const cellA = rowA.cells[index].textContent.trim();
        const cellB = rowB.cells[index].textContent.trim();

        if (type === "number") {
          return isAscending ? cellB - cellA : cellA - cellB;
        }

        if (type === "date") {
          // Μετατροπή dd/mm/yy σε αντικείμενο Date για σύγκριση
          const parseDate = (str) => {
            const [d, m, y] = str.split("/");
            return new Date(`20${y}`, m - 1, d);
          };
          return isAscending
            ? parseDate(cellB) - parseDate(cellA)
            : parseDate(cellA) - parseDate(cellB);
        }

        // Default: String comparison (localeCompare για σωστά Ελληνικά)
        return isAscending
          ? cellB.localeCompare(cellA, "el")
          : cellA.localeCompare(cellB, "el");
      });

      // Αφαίρεση παλιών σειρών και προσθήκη των νέων ταξινομημένων
      while (tbody.firstChild) {
        tbody.removeChild(tbody.firstChild);
      }
      tbody.append(...rows);

      // Εναλλαγή κλάσεων για την επόμενη φορά (Toggle Asc/Desc)
      document
        .querySelectorAll("#userTableHeader th")
        .forEach((th) => th.classList.remove("sort-asc", "sort-desc"));
      header.classList.add(isAscending ? "sort-desc" : "sort-asc");

      // Προαιρετικά: Ενημέρωση των βελών (content) αν θέλεις πιο εξελιγμένο UI
    });
  });

// ----------------------------------------------------
// 14. PAGINATION LOGIC
// ----------------------------------------------------

let currentPage = 1;
const rowsPerPage = 10; // Μπορείς να το αλλάξεις σε 20 ή 50

function updatePagination() {
  // Εστιάζουμε ΜΟΝΟ στον πίνακα των λογαριασμών χρησιμοποιώντας το ID του tbody
  const tbody = document.getElementById("accounts-table-body");
  if (!tbody) return;

  const rows = Array.from(tbody.querySelectorAll("tr")).filter(
    (row) => row.getAttribute("data-filtered") !== "true",
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
      // Scroll στην κορυφή του πίνακα για καλύτερο UX
      tbody.scrollIntoView({ behavior: "smooth", block: "nearest" });
    });
    container.appendChild(btn);
  }
}

function displayPage() {
  const tbody = document.getElementById("accounts-table-body");
  if (!tbody) return;

  const rows = Array.from(tbody.querySelectorAll("tr")).filter(
    (row) => row.getAttribute("data-filtered") !== "true",
  );

  const start = (currentPage - 1) * rowsPerPage;
  const end = start + rowsPerPage;

  // Εμφάνιση μόνο των σειρών που ανήκουν στην τρέχουσα σελίδα
  rows.forEach((row, index) => {
    if (index >= start && index < end) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });

  updatePagination();
}

// ----------------------------------------------------
// Τροποποίηση της filterUsers για να συνεργάζεται με το Pagination
// ----------------------------------------------------
function filterUsers() {
  const searchTerm = searchInput.value.toLowerCase();
  const roleValue = roleFilter.value.toLowerCase();
  const statusValue = statusFilter.value.toLowerCase();

  tableRows.forEach((row) => {
    const username = row.cells[1].textContent.toLowerCase();
    const email = row.cells[2].textContent.toLowerCase();
    const role = row.cells[3].textContent.toLowerCase();
    const statusText = row.cells[4].textContent.trim();
    const status = statusText === "Ενεργός" ? "active" : "inactive";

    const matchesSearch =
      username.includes(searchTerm) || email.includes(searchTerm);
    const matchesRole = roleValue === "all" || role.includes(roleValue);
    const matchesStatus = statusValue === "all" || status === statusValue;

    if (matchesSearch && matchesRole && matchesStatus) {
      row.setAttribute("data-filtered", "false");
    } else {
      row.setAttribute("data-filtered", "true");
      row.style.display = "none"; // Κρύβουμε αμέσως τα φιλτραρισμένα
    }
  });

  currentPage = 1; // Επαναφορά στην 1η σελίδα μετά από κάθε φιλτράρισμα
  displayPage();
}

// Αρχική εκτέλεση κατά το φόρτωμα
document.addEventListener("DOMContentLoaded", () => {
  displayPage();
});

function initAccountsTab() {
  console.log("Initializing Accounts Tab...");
  filterUsers(); // Τρέχει το αρχικό φιλτράρισμα και displayPage()
}
