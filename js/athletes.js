function openAddAthleteModal() {
  document.getElementById("addAthleteModal").style.display = "block";
}

function closeAddAthleteModal() {
  document.getElementById("addAthleteModal").style.display = "none";
  document.getElementById("addAthleteForm").reset();
  document.getElementById("addAthleteMessage").style.display = "none";
}

// Διαχείριση της υποβολής της φόρμας
document
  .getElementById("addAthleteForm")
  .addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());

    // Ορισμός του messageDiv για να μην πετάξει error αν δεν έχει οριστεί έξω
    const messageDiv = document.getElementById("addAthleteMessage");

    // Επιλογή αρχείου: αν έχει ID πάμε για update, αλλιώς για add
    const targetFile = data.athlete_id
      ? "update_athlete_handler.php"
      : "add_athlete_handler.php";

    try {
      const response = await fetch(targetFile, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      });

      const result = await response.json();

      messageDiv.style.display = "block";
      messageDiv.textContent = result.message;
      messageDiv.style.color = response.ok ? "green" : "red";

      if (response.ok) {
        // Περιμένουμε 1.5 δευτερόλεπτο για να δει ο χρήστης το πράσινο μήνυμα
        setTimeout(() => {
          location.reload();
        }, 1500);
      }
    } catch (error) {
      messageDiv.style.display = "block";
      messageDiv.textContent = "Σφάλμα επικοινωνίας.";
      messageDiv.style.color = "red";
      console.error("Fetch error:", error);
    }
  });

function editAthlete(id) {
  // 1. Βρίσκουμε τη γραμμή του πίνακα
  const row = event.target.closest("tr");

  // 2. Παίρνουμε τα δεδομένα από τα κελιά (ή attributes)
  // Υποθέτουμε ότι το ονοματεπώνυμο είναι στο 1ο κελί (index 0)
  const fullName = row.cells[0].innerText.split(" ");
  const firstName = fullName[0];
  const lastName = fullName.slice(1).join(" "); // Για περιπτώσεις με διπλό επίθετο
  const phone = row.cells[1].innerText;
  const age = row.cells[2].innerText;
  const region = row.getAttribute("data-region");

  // 3. Συμπληρώνουμε τη φόρμα
  document.getElementById("athlete_id").value = id;
  document.querySelector('[name="first_name"]').value = firstName;
  document.querySelector('[name="last_name"]').value = lastName;
  document.querySelector('[name="phone"]').value = phone === "-" ? "" : phone;
  document.querySelector('[name="age"]').value = age === "-" ? "" : age;
  document.querySelector('[name="region"]').value = region;

  // 4. Αλλάζουμε την εμφάνιση του Modal
  document.getElementById("modalTitle").innerText = "Επεξεργασία Αθλητή";
  document.getElementById("addAthleteModal").style.display = "block";
}

// Μην ξεχάσεις να καθαρίζεις το ID όταν ανοίγεις για "Νέα Προσθήκη"
function openAddAthleteModal() {
  document.getElementById("athlete_id").value = ""; // Καθαρισμός ID
  document.getElementById("addAthleteForm").reset();
  document.getElementById("modalTitle").innerText = "Νέα Καταχώρηση Αθλητή";
  document.getElementById("addAthleteModal").style.display = "block";
}

async function deleteAthlete(id, name) {
  if (
    confirm(
      `Είστε σίγουροι ότι θέλετε να διαγράψετε οριστικά τον αθλητή: ${name};`,
    )
  ) {
    try {
      const response = await fetch("delete_athlete_handler.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          athlete_id: id,
        }),
      });

      const result = await response.json();

      if (response.ok) {
        alert(result.message);
        location.reload(); // Ανανέωση για να φύγει η γραμμή
      } else {
        alert("Σφάλμα: " + result.message);
      }
    } catch (error) {
      console.error("Error:", error);
      alert("Σφάλμα επικοινωνίας με τον διακομιστή.");
    }
  }
}

// Αυτό εκτελείται αμέσως μόλις διαβαστεί το script
const savedTab = localStorage.getItem("activeTab") || "accounts-tab";
const menuLink = document.querySelector(`li[onclick*="${savedTab}"]`);

if (menuLink) {
  // Καλούμε τη showTab για να γίνουν όλα τα απαραίτητα
  showTab(
    {
      currentTarget: menuLink,
    },
    savedTab,
  );
} else {
  showTab(null, "accounts-tab");
}

// --- ΑΝΑΖΗΤΗΣΗ ΑΘΛΗΤΩΝ ---
function searchAthletes() {
  const input = document.getElementById("athleteSearch").value.toLowerCase();
  // Παίρνουμε μόνο τις γραμμές του tbody για να μην κρύψουμε την κεφαλίδα
  const rows = document.querySelectorAll("#athletesTable tbody tr.athlete-row");

  rows.forEach((row) => {
    const name = row.cells[0].innerText.toLowerCase();
    const phone = row.cells[1].innerText.toLowerCase();

    // Αν το όνομα ή το τηλέφωνο περιέχει αυτό που γράφουμε
    if (name.includes(input) || phone.includes(input)) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  });
}

// --- ΤΑΞΙΝΟΜΗΣΗ ΑΘΛΗΤΩΝ ---
function sortAthletes() {
  const table = document.getElementById("athletesTable");
  const tbody = table.querySelector("tbody");
  const rows = Array.from(tbody.querySelectorAll("tr.athlete-row"));
  const sortBy = document.getElementById("athleteSort").value;

  if (sortBy === "none") return;

  const sortedRows = rows.sort((a, b) => {
    let valA, valB;

    if (sortBy.includes("name")) {
      // Κελί 0: Ονοματεπώνυμο
      valA = a.cells[0].innerText.toLowerCase();
      valB = b.cells[0].innerText.toLowerCase();
    } else if (sortBy.includes("age")) {
      // Κελί 2: Ηλικία (μετατροπή σε αριθμό)
      valA = parseInt(a.cells[2].innerText) || 0;
      valB = parseInt(b.cells[2].innerText) || 0;
    }

    if (sortBy.endsWith("asc")) {
      return valA > valB ? 1 : -1;
    } else {
      return valA < valB ? 1 : -1;
    }
  });

  // Καθαρισμός και επανατοποθέτηση των ταξινομημένων γραμμών
  rows.forEach((row) => tbody.removeChild(row));
  sortedRows.forEach((row) => tbody.appendChild(row));
}

let athleteCurrentPage = 1;
const athleteRowsPerPage = 10;

// Κεντρική συνάρτηση που ελέγχει ΤΑ ΠΑΝΤΑ (Search + Chips)
function filterAthletes() {
  const searchTerm = document
    .getElementById("athleteSearch")
    .value.toLowerCase();
  // Βρίσκουμε ποιο chip είναι ενεργό
  const activeChip = document.querySelector(".chip.active").innerText;
  const tbody = document.getElementById("athletes-table-body");
  const rows = Array.from(tbody.querySelectorAll("tr"));

  rows.forEach((row) => {
    const name = row.cells[0].textContent.toLowerCase();
    const phone = row.cells[1].textContent.toLowerCase();
    const rowRegion = row.getAttribute("data-region");

    const matchesSearch =
      name.includes(searchTerm) || phone.includes(searchTerm);
    const matchesRegion = activeChip === "Όλοι" || rowRegion === activeChip;

    // Αντί για style.display, βάζουμε το attribute για το pagination
    if (matchesSearch && matchesRegion) {
      row.setAttribute("data-filtered", "false");
    } else {
      row.setAttribute("data-filtered", "true");
      row.style.display = "none";
    }
  });

  athleteCurrentPage = 1; // Επαναφορά στην 1η σελίδα
  displayAthletesPage();
}

// Εμφάνιση της συγκεκριμένης σελίδας
function displayAthletesPage() {
  const tbody = document.getElementById("athletes-table-body");
  const allVisibleRows = Array.from(tbody.querySelectorAll("tr")).filter(
    (row) => row.getAttribute("data-filtered") !== "true",
  );

  const start = (athleteCurrentPage - 1) * athleteRowsPerPage;
  const end = start + athleteRowsPerPage;

  allVisibleRows.forEach((row, index) => {
    row.style.display = index >= start && index < end ? "" : "none";
  });

  updateAthletesPagination(allVisibleRows.length);
}

// Δημιουργία των κουμπιών Pagination
function updateAthletesPagination(totalRows) {
  const totalPages = Math.ceil(totalRows / athleteRowsPerPage);
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

// Η νέα filterByRegion που καλεί την κεντρική filterAthletes
function filterByRegion(region) {
  const chips = document.querySelectorAll(".chip");
  chips.forEach((chip) => {
    chip.classList.remove("active");
    if (
      chip.innerText === region ||
      (region === "all" && chip.innerText === "Όλοι")
    ) {
      chip.classList.add("active");
    }
  });

  filterAthletes(); // Καλεί την κεντρική λογική
}

// Συνάρτηση για το Search
function searchAthletes() {
  filterAthletes();
}

// Ταξινόμηση (Sorting)
function sortAthletes() {
  const sortBy = document.getElementById("athleteSort").value;
  const tbody = document.getElementById("athletes-table-body");
  const rows = Array.from(tbody.querySelectorAll("tr"));

  rows.sort((a, b) => {
    let valA, valB;
    if (sortBy.includes("name")) {
      valA = a.cells[0].textContent;
      valB = b.cells[0].textContent;
      return sortBy === "name_asc"
        ? valA.localeCompare(valB, "el")
        : valB.localeCompare(valA, "el");
    } else if (sortBy.includes("age")) {
      valA = parseInt(a.cells[2].textContent) || 0;
      valB = parseInt(b.cells[2].textContent) || 0;
      return sortBy === "age_asc" ? valA - valB : valB - valA;
    }
    return 0;
  });

  rows.forEach((row) => tbody.appendChild(row));
  displayAthletesPage(); // Ανανεώνουμε τη σελίδα μετά την ταξινόμηση
}
