// finance tab functions can go here
let selectedUserIdForPayment = null;
let financeCurrentPage = 1;
const recordsPerPage = 10;
let allFinanceData = [];

function refreshFinanceTable() {
  // Καθαρισμός αναζήτησης
  const searchInput = document.getElementById("financeSearch");
  if (searchInput) searchInput.value = "";

  fetch("get_finance_overview.php")
    .then((res) => res.json())
    .then((res) => {
      if (res.status === "success") {
        allFinanceData = res.data; // Αποθήκευση όλων των δεδομένων
        financeCurrentPage = 1; // Reset στη σελίδα 1
        displayFinancePage();
      }
    });
}

function displayFinancePage() {
  const tbody = document.getElementById("finance-table-body");
  tbody.innerHTML = "";

  // Υπολογισμός αρχής και τέλους για τη συγκεκριμένη σελίδα
  const start = (financeCurrentPage - 1) * recordsPerPage;
  const end = start + recordsPerPage;
  const paginatedData = allFinanceData.slice(start, end);

  paginatedData.forEach((row) => {
    const balance = parseInt(row.total_paid) - parseInt(row.total_attended);
    const balanceColor =
      balance < 0 ? "#e74c3c" : balance === 0 ? "#f39c12" : "#27ae60";
    const balanceWeight = balance < 0 ? "bold" : "normal";

    tbody.innerHTML += `
      <tr style="border-bottom: 1px solid #eee;">
        <td data-label="Αθλητής" style="padding: 12px;">${row.last_name} ${row.first_name}</td>
        <td data-label="Πληρωμένα" style="padding: 12px; text-align: center;">${row.total_paid}</td>
        <td data-label="Εκτελεσμένα" style="padding: 12px; text-align: center;">${row.total_attended}</td>
        <td data-label="Υπόλοιπο" style="padding: 12px; text-align: center; color: ${balanceColor}; font-weight: ${balanceWeight};">
          ${balance > 0 ? "+" : ""}${balance}
        </td>
        <td data-label="Ενέργειες" style="padding: 12px; text-align: right;">
          <button class="action-btn" onclick="openPaymentModal(${row.id}, '${row.last_name} ${row.first_name}')" title="Πληρωμή">
            ➕ Πληρωμή
          </button>
          <button class="action-btn role-btn" onclick="openHistoryModal(${row.id}, '${row.last_name} ${row.first_name}')" title="Ιστορικό/Καρτέλα">
            👁️ Καρτέλα
          </button>
        </td>
        </tr>
    `;
  });

  updatePaginationUI();
}

function openPaymentModal(userId, name) {
  selectedUserIdForPayment = userId;
  document.getElementById("paymentStudentName").innerText = name;
  document.getElementById("paymentModal").style.display = "block";
}

function closePaymentModal() {
  document.getElementById("paymentModal").style.display = "none";
}

// Εδώ θα χρειαστείς και ένα submitPayment() που θα στέλνει τα δεδομένα στην PHP
function submitPayment() {
  const amount = document.getElementById("payAmount").value;
  const lessons = document.getElementById("payLessons").value;
  const notes = document.getElementById("payNotes").value;

  if (!selectedUserIdForPayment || !amount || !lessons) {
    alert("Παρακαλώ συμπληρώστε ποσό και μαθήματα.");
    return;
  }

  fetch("submit_payment.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      user_id: selectedUserIdForPayment,
      amount: amount,
      lessons: lessons,
      notes: notes,
    }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.status === "success") {
        alert(data.message);
        closePaymentModal();
        refreshFinanceTable(); // Ανανέωση του πίνακα αμέσως

        // Καθαρισμός πεδίων για την επόμενη φορά
        document.getElementById("payAmount").value = 25;
        document.getElementById("payLessons").value = 4;
        document.getElementById("payNotes").value = "";
      } else {
        alert("Σφάλμα: " + data.message);
      }
    });
}
document.addEventListener("DOMContentLoaded", function () {
  // Αν το Finance Tab είναι το προεπιλεγμένο ή όταν πατηθεί
  refreshFinanceTable();
});

// athlete history card modal functions can go here
function openHistoryModal(userId, fullName) {
  document.getElementById("historyStudentName").innerText =
    `Καρτέλα: ${fullName}`;

  // Εμφάνιση του Modal
  document.getElementById("athleteHistoryModal").style.display = "block";

  // Φόρτωση δεδομένων από το backend
  fetch(`get_athlete_history.php?id=${userId}`)
    .then((res) => res.json())
    .then((data) => {
      if (data.status === "success") {
        // 1. Εμφάνιση Παρουσιών (Αριστερή Στήλη)
        const attendanceDiv = document.getElementById("attendanceList");
        if (data.attendance.length === 0) {
          attendanceDiv.innerHTML =
            '<p style="color: #999;">Καμία παρουσία ακόμα.</p>';
        } else {
          let attHTML = '<ul style="list-style: none; padding: 0;">';
          data.attendance.forEach((att) => {
            const date = new Date(att.lesson_datetime).toLocaleDateString(
              "el-GR",
            );
            attHTML += `
                            <li style="padding: 10px 0; border-bottom: 1px solid #f0f0f0; font-size: 0.9em;">
                                <strong>${date}</strong> - ${att.title} <br>
                                <small style="color: #666;">📍 ${att.location}</small>
                            </li>`;
          });
          attHTML += "</ul>";
          attendanceDiv.innerHTML = attHTML;
        }

        // 2. Εμφάνιση Πληρωμών (Δεξιά Στήλη με δυνατότητα διαγραφής)
        const paymentsDiv = document.getElementById("paymentsList");
        if (data.payments.length === 0) {
          paymentsDiv.innerHTML =
            '<p style="color: #999;">Καμία πληρωμή ακόμα.</p>';
        } else {
          let payHTML = '<ul style="list-style: none; padding: 0;">';
          data.payments.forEach((pay) => {
            const pDate = new Date(pay.payment_date).toLocaleDateString(
              "el-GR",
            );
            payHTML += `
                            <li style="padding: 10px 0; border-bottom: 1px solid #f0f0f0; font-size: 0.9em; display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <span style="color: #27ae60; font-weight: bold;">+${pay.lessons_added} μαθήματα</span> 
                                    (${pay.amount}€) <br>
                                    <small style="color: #666;">📅 ${pDate} ${pay.notes ? "- " + pay.notes : ""}</small>
                                </div>
                                <button onclick="deletePayment(${pay.id}, ${userId}, '${fullName}')" 
                                        style="background: none; border: none; color: #e74c3c; cursor: pointer; font-size: 1.2em; padding: 5px;" 
                                        title="Διαγραφή πληρωμής">
                                    🗑️
                                </button>
                            </li>`;
          });
          payHTML += "</ul>";
          paymentsDiv.innerHTML = payHTML;
        }

        // 3. Ενημέρωση Σύνοψης στο πάνω μέρος του Modal
        const totalPaid = data.payments.reduce(
          (acc, curr) => acc + parseInt(curr.lessons_added),
          0,
        );
        const totalAttended = data.attendance.length;
        const currentBalance = totalPaid - totalAttended;
        const balanceText =
          currentBalance >= 0 ? `+${currentBalance}` : currentBalance;

        document.getElementById("historySummary").innerHTML = `
                    Παρουσίες: <strong>${totalAttended}</strong> | 
                    Πληρωμένα: <strong>${totalPaid}</strong> | 
                    Υπόλοιπο: <strong style="color: ${currentBalance < 0 ? "#e74c3c" : "#27ae60"}">${balanceText}</strong>
                `;
      } else {
        alert("Σφάλμα κατά τη φόρτωση του ιστορικού.");
      }
    })
    .catch((err) => console.error("History Fetch Error:", err));
}

function closeHistoryModal() {
  document.getElementById("athleteHistoryModal").style.display = "none";
}

function filterFinanceTable() {
  const input = document.getElementById("financeSearch");
  const filter = input.value.toLowerCase();

  // 1. Φιλτράρουμε την αρχική λίστα δεδομένων
  const filteredData = allFinanceData.filter((user) => {
    const fullName = `${user.last_name} ${user.first_name}`.toLowerCase();
    return fullName.includes(filter);
  });

  // 2. Εμφανίζουμε τα αποτελέσματα
  const tbody = document.getElementById("finance-table-body");
  tbody.innerHTML = "";

  // Αν δεν γράφουμε τίποτα, ξαναγυρνάμε στο κανονικό pagination
  if (filter === "") {
    displayFinancePage();
    document.getElementById("pagination-controls").style.display = "flex";
    return;
  }

  // Κρύβουμε το pagination κατά την αναζήτηση για να βλέπουμε όλα τα αποτελέσματα που ταιριάζουν
  document.getElementById("pagination-controls").style.display = "none";

  filteredData.forEach((row) => {
    const balance = parseInt(row.total_paid) - parseInt(row.total_attended);
    const balanceColor =
      balance < 0 ? "#e74c3c" : balance === 0 ? "#f39c12" : "#27ae60";
    const balanceWeight = balance < 0 ? "bold" : "normal";

    tbody.innerHTML += `
      <tr style="border-bottom: 1px solid #eee;">
        <td data-label="Αθλητής" style="padding: 12px;">${row.last_name} ${row.first_name}</td>
        <td data-label="Πληρωμένα" style="padding: 12px; text-align: center;">${row.total_paid}</td>
        <td data-label="Εκτελεσμένα" style="padding: 12px; text-align: center;">${row.total_attended}</td>
        <td data-label="Υπόλοιπο" style="padding: 12px; text-align: center; color: ${balanceColor}; font-weight: ${balanceWeight};">
          ${balance > 0 ? "+" : ""}${balance}
        </td>
        <td data-label="Ενέργειες" style="padding: 12px; text-align: right;">
          <button class="action-btn" onclick="openPaymentModal(${row.id}, '${row.last_name} ${row.first_name}')">➕</button>
          <button class="action-btn role-btn" onclick="openHistoryModal(${row.id}, '${row.last_name} ${row.first_name}')">👁️</button>
        </td>
      </tr>
    `;
  });
}

function deletePayment(paymentId, userId, fullName) {
  if (
    !confirm(
      "Είστε σίγουροι ότι θέλετε να διαγράψετε αυτή την πληρωμή; Το υπόλοιπο του αθλητή θα ενημερωθεί αυτόματα.",
    )
  ) {
    return;
  }

  fetch("delete_payment.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      payment_id: paymentId,
    }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.status === "success") {
        // Ανανέωσε την καρτέλα για να φανεί η αλλαγή
        openHistoryModal(userId, fullName);
        // Ανανέωσε και τον κεντρικό πίνακα των οικονομικών
        refreshFinanceTable();
      } else {
        alert("Σφάλμα: " + data.message);
      }
    });
}

function updatePaginationUI() {
  const totalPages = Math.ceil(allFinanceData.length / recordsPerPage);
  document.getElementById("page-info").innerText =
    `Σελίδα ${financeCurrentPage}  από ${totalPages || 1}`;

  // Απενεργοποίηση κουμπιών αν δεν υπάρχει άλλη σελίδα
  document.getElementById("btn-prev").disabled = financeCurrentPage === 1;
  document.getElementById("btn-next").disabled =
    financeCurrentPage >= totalPages;
}

function prevPage() {
  if (financeCurrentPage > 1) {
    financeCurrentPage--;
    displayFinancePage();
  }
}

function nextPage() {
  const totalPages = Math.ceil(allFinanceData.length / recordsPerPage);
  if (financeCurrentPage < totalPages) {
    financeCurrentPage++;
    displayFinancePage();
  }
}
