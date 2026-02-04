// lessons.js
// Purpose: Admin UI logic for managing classes, participants, and attendance.

// Open/Close Class Modal
function openAddClassModal() {
  document.getElementById("addClassModal").style.display = "block";
}

// Close the add-class modal
function closeAddClassModal() {
  document.getElementById("addClassModal").style.display = "none";
}

// Handle Class Form Submission
document
  .getElementById("addClassForm")
  .addEventListener("submit", function (e) {
    e.preventDefault(); // Σταματάμε το κλασικό refresh της φόρμας

    const formData = {
      title: this.title.value,
      location: this.location.value,
      lesson_datetime: this.lesson_datetime.value,
    };

    fetch("add_class_handler.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(formData),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          alert(data.message);
          location.reload(); // Προσωρινά κάνουμε reload για να δούμε αν μπήκε
        } else {
          alert("Σφάλμα: " + data.message);
        }
      })
      .catch((error) => console.error("Error:", error));
  });

// Manage Class Modal Functions
let currentOpenClassId = null; // Κρατάμε το ID του μαθήματος που δουλεύουμε

// Open the manage modal for a specific class
function manageClass(classId) {
  currentOpenClassId = classId;
  document.getElementById("manageClassModal").style.display = "block";

  // Εδώ θα καλούμε μια συνάρτηση που θα φέρνει τους αθλητές μέσω AJAX
  // Για τώρα, απλά καθαρίζουμε τις λίστες
  document.getElementById("currentParticipants").innerHTML = "Φόρτωση...";
  loadClassData(classId);
}

// Close the manage modal and clear selection
function closeManageClassModal() {
  document.getElementById("manageClassModal").style.display = "none";
  currentOpenClassId = null;
}

// Load class details, suggestions, and participants
function loadClassData(classId) {
  fetch(`get_class_details.php?id=${classId}`)
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        // 1. Ενημέρωση στατικών στοιχείων
        document.getElementById("modalClassTitle").innerText =
          data.lesson.title;
        document.getElementById("modalClassDetails").innerText =
          `${data.lesson.location} | ${data.lesson.lesson_datetime}`;

        // 2. Προετοιμασία πεδίων επεξεργασίας (Input fields)
        document.getElementById("editTitle").value = data.lesson.title;
        document.getElementById("editLocation").value = data.lesson.location;
        // Μετατροπή ημερομηνίας για το input datetime-local
        const dt = data.lesson.lesson_datetime
          .replace(" ", "T")
          .substring(0, 16);
        document.getElementById("editDatetime").value = dt;

        // 3. Εμφάνιση Προτάσεων & Συμμετεχόντων (τα είχαμε ήδη)
        renderSuggestions(data.suggestions);
        renderParticipants(data.participants);
      }
    });
}

// Render participant list with attendance/payment controls
function renderParticipants(participants) {
  let partHTML = "";
  participants.forEach((p) => {
    // Ελέγχουμε αν είναι τσεκαρισμένα βάσει των δεδομένων από τη βάση
    const attendedChecked = p.attended == 1 ? "checked" : "";
    const paidChecked = p.payment_status === "paid" ? "checked" : "";

    partHTML += `
            <div style="padding: 10px; background: #f9f9f9; margin-bottom: 5px; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; border-left: 4px solid ${p.attended == 1 ? "#27ae60" : "#ccc"};">
                <span style="font-weight: bold;">${p.first_name} ${p.last_name}</span>
                <div style="display: flex; gap: 15px; align-items: center;">
                    <label style="font-size: 0.85em; cursor:pointer;">
                        <input type="checkbox" ${attendedChecked} onchange="updateStatus(${p.id}, 'attended', this.checked)"> Ήρθε
                    </label>
                    <label style="font-size: 0.85em; cursor:pointer;">
                        <input type="checkbox" ${paidChecked} onchange="updateStatus(${p.id}, 'payment', this.checked)"> Πληρώθηκε
                    </label>
                    <button onclick="removeFromClass(${p.id})" style="background: none; color: #e74c3c; border: none; cursor: pointer; font-weight: bold; margin-left: 10px;">&times;</button>
                </div>
            </div>`;
  });
  document.getElementById("currentParticipants").innerHTML =
    partHTML || "<p>Κανένας αθλητής ακόμα.</p>";
  document.getElementById("participantCount").innerText = participants.length;
}

// Add athlete to current class and refresh UI
function addAthleteToClass(athleteId) {
  if (!currentOpenClassId) return;

  fetch("add_athlete_to_class.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      lesson_id: currentOpenClassId,
      user_id: athleteId,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        const searchInput = document.getElementById("memberSearch");

        // 1. Αν ο χρήστης έχει γράψει κάτι στην αναζήτηση (πάνω από 1 χαρακτήρα)
        if (searchInput.value.trim().length >= 2) {
          // "Πυροδοτούμε" ξανά την αναζήτηση για να ανανεωθεί η αριστερή λίστα
          // χωρίς να χαθούν τα αποτελέσματα του 'Γιώργος'
          searchInput.dispatchEvent(new Event("input"));

          // Φέρνουμε μόνο τους συμμετέχοντες για να ανανεωθεί η δεξιά λίστα
          fetch(`get_class_details.php?id=${currentOpenClassId}`)
            .then((res) => res.json())
            .then((resData) => {
              renderParticipants(resData.participants);
              // Ενημέρωση του μετρητή στην κάρτα (στο background)
              const countSpan = document.getElementById(
                `card-count-${currentOpenClassId}`,
              );
              if (countSpan) {
                countSpan.innerText = `Συμμετοχές: ${resData.participants.length}`;
              }
            });
        } else {
          // 2. Αν δεν υπάρχει αναζήτηση, κάνουμε το κλασικό loadClassData
          loadClassData(currentOpenClassId);

          // Μικρή καθυστέρηση για να προλάβει το DOM να κάνει render πριν μετρήσουμε
          setTimeout(() => {
            const countSpan = document.getElementById(
              `card-count-${currentOpenClassId}`,
            );
            const currentCount = document.querySelectorAll(
              "#currentParticipants > div",
            ).length;
            if (countSpan) {
              countSpan.innerText = `Συμμετοχές: ${currentCount}`;
            }
          }, 100);
        }
      } else {
        alert(data.message);
      }
    });
}

// Remove athlete from current class
function removeFromClass(athleteId) {
  if (!confirm("Σίγουρα θέλετε να αφαιρέσετε τον αθλητή από το μάθημα;"))
    return;

  fetch("remove_athlete_from_class.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      lesson_id: currentOpenClassId,
      user_id: athleteId,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        const countSpan = document.getElementById(
          `card-count-${currentOpenClassId}`,
        );
        const currentCount = document.querySelectorAll(
          "#currentParticipants > div",
        ).length;
        if (countSpan) {
          countSpan.innerText = `Συμμετοχές: ${currentCount}`;
        }
        loadClassData(currentOpenClassId);
      }
    });
}

// Update attendance or payment status for an athlete
function updateStatus(athleteId, type, isChecked) {
  fetch("update_enrollment_status.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      lesson_id: currentOpenClassId,
      user_id: athleteId,
      type: type,
      value: isChecked,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        // Προαιρετικά: Ξαναφορτώνουμε τα δεδομένα για να ενημερωθεί το UI (π.χ. το χρώμα στο border)
        loadClassData(currentOpenClassId);
      } else {
        alert("Σφάλμα κατά την ενημέρωση");
      }
    });
}

// Live search athletes as user types
document.getElementById("memberSearch").addEventListener("input", function (e) {
  const query = e.target.value;

  // Αν το πεδίο είναι άδειο, ξαναφόρτωσε τις προτάσεις περιοχής
  if (query.length < 2) {
    loadClassData(currentOpenClassId);
    return;
  }

  fetch(`search_athletes.php?q=${query}&class_id=${currentOpenClassId}`)
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        let resultsHTML = "";
        data.results.forEach((athlete) => {
          resultsHTML += `
                    <div style="padding: 10px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; background: #fff4e6;">
                        <span>${athlete.first_name} ${athlete.last_name} <small>(${athlete.region})</small></span>
                        <button onclick="addAthleteToClass(${athlete.id})" style="background: #e67e22; color: white; border: none; padding: 2px 8px; border-radius: 3px; cursor: pointer;">+</button>
                    </div>`;
        });
        document.getElementById("suggestedAthletes").innerHTML =
          resultsHTML || '<p style="padding:10px;">Δεν βρέθηκε αθλητής.</p>';
      }
    });
});

// Delete the currently selected lesson
function deleteCurrentLesson() {
  if (!currentOpenClassId) return;

  if (
    !confirm(
      "ΠΡΟΣΟΧΗ: Θέλετε να διαγράψετε οριστικά αυτή την προπόνηση και όλα τα δεδομένα παρουσιών;",
    )
  ) {
    return;
  }

  fetch("delete_lesson.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      lesson_id: currentOpenClassId,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        alert(data.message);
        location.reload(); // Ανανέωση για να εξαφανιστεί η κάρτα
      } else {
        alert("Σφάλμα: " + data.message);
      }
    });
}

// Placeholder for additional edit logic (currently unused)
function editClassInfo() {
  const currentTitle = document.getElementById("modalClassTitle").innerText;
  const headerContainer = document.querySelector(
    "#manageClassModal .modal-content div:nth-child(2)",
  );
}

// Toggle between view and edit mode in the manage modal
function toggleEditMode(show) {
  document.getElementById("editableHeader").style.display = show
    ? "none"
    : "block";
  document.getElementById("editFieldsContainer").style.display = show
    ? "block"
    : "none";
}

// Save changes to the class details
function saveClassChanges() {
  const updatedData = {
    id: currentOpenClassId,
    title: document.getElementById("editTitle").value,
    location: document.getElementById("editLocation").value,
    datetime: document.getElementById("editDatetime").value,
  };

  fetch("update_class_details.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(updatedData),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        toggleEditMode(false);
        loadClassData(currentOpenClassId); // Φρεσκάρισμα δεδομένων στο Modal
        // Προαιρετικά alert('Ενημερώθηκε!');
      } else {
        alert("Σφάλμα: " + data.message);
      }
    });
}

// Render suggested athletes list
function renderSuggestions(suggestions) {
  let suggHTML = "";
  suggestions.forEach((athlete) => {
    suggHTML += `
            <div style="padding: 10px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between;">
                <span>${athlete.first_name} ${athlete.last_name} <small>(${athlete.region})</small></span>
                <button onclick="addAthleteToClass(${athlete.id})" style="background: #27ae60; color: white; border: none; padding: 2px 8px; border-radius: 3px; cursor: pointer;">+</button>
            </div>`;
  });
  document.getElementById("suggestedAthletes").innerHTML =
    suggHTML || '<p style="padding:10px;">Δεν βρέθηκαν προτεινόμενοι.</p>';
}
