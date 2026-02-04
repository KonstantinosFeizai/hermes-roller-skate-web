// contact-admin.js
// Purpose: Admin UI logic for viewing, filtering, paginating, and replying to contact messages.

let contactCurrentPage = 1;
const contactRowsPerPage = 10;

// Filter messages by search, category, and reply status
function filterMessages() {
  const searchTerm = document
    .getElementById("contactSearch")
    .value.toLowerCase();
  const categoryFilter = document.getElementById("contactCategoryFilter").value;
  const statusFilter = document.getElementById("contactStatusFilter").value; // Νέο

  const tbody = document.getElementById("contact-table-body");
  const rows = Array.from(tbody.querySelectorAll("tr.message-row"));

  rows.forEach((row) => {
    const name = row.cells[1].textContent.toLowerCase();
    const contactInfo = row.cells[2].textContent.toLowerCase();
    const subject = row.cells[4].textContent.toLowerCase();

    const category = row.getAttribute("data-category");
    const repliedStatus = row.getAttribute("data-replied"); // "0" ή "1"

    // Search match
    const matchesSearch =
      name.includes(searchTerm) ||
      contactInfo.includes(searchTerm) ||
      subject.includes(searchTerm);

    // Category match
    const matchesCategory =
      categoryFilter === "all" || category === categoryFilter;

    // Status match
    const matchesStatus =
      statusFilter === "all" || repliedStatus === statusFilter;

    // Combine all filters (AND)
    if (matchesSearch && matchesCategory && matchesStatus) {
      row.setAttribute("data-filtered", "false");
    } else {
      row.setAttribute("data-filtered", "true");
      row.style.display = "none";
    }
  });

  contactCurrentPage = 1;
  displayContactPage();
}

// Pagination: show rows for the current page
function displayContactPage() {
  const tbody = document.getElementById("contact-table-body");
  const visibleRows = Array.from(
    tbody.querySelectorAll("tr.message-row"),
  ).filter((row) => row.getAttribute("data-filtered") !== "true");

  const start = (contactCurrentPage - 1) * contactRowsPerPage;
  const end = start + contactRowsPerPage;

  visibleRows.forEach((row, index) => {
    row.style.display = index >= start && index < end ? "" : "none";
  });

  updateContactPagination(visibleRows.length);
}

// Render pagination buttons
function updateContactPagination(totalRows) {
  const totalPages = Math.ceil(totalRows / contactRowsPerPage);
  const container = document.getElementById("contactPagination");
  container.innerHTML = "";

  if (totalPages <= 1) return;

  for (let i = 1; i <= totalPages; i++) {
    const btn = document.createElement("button");
    btn.innerText = i;
    btn.className =
      "page-num-btn" + (i === contactCurrentPage ? " active" : "");
    btn.onclick = () => {
      contactCurrentPage = i;
      displayContactPage();
    };
    container.appendChild(btn);
  }
}

// Open message modal and populate data
function viewMessage(msg) {
  const modal = document.getElementById("messageModal");

  // 1. Base info
  document.getElementById("modalSubject").innerText = msg.subject;
  document.getElementById("modalDetails").innerHTML = `
        <strong>Από:</strong> ${msg.name} ${msg.surname}<br>
        <strong>Email:</strong> ${msg.email}<br>
        <strong>Τηλέφωνο:</strong> ${msg.phone || "-"}<br>
        <strong>Κατηγορία:</strong> ${msg.category}<br>
        <strong>Ημερομηνία:</strong> ${new Date(msg.created_at).toLocaleString("el-GR")}
    `;
  document.getElementById("modalMessageContent").innerText = msg.message;

  // 2. Reply section (show previous reply if exists)
  const prevReplySection = document.getElementById("previousReplySection");
  const replyBadge = document.getElementById("replyBadge");

  if (msg.is_replied == 1) {
    replyBadge.innerHTML =
      '<span style="background:#27ae60; color:white; padding:2px 8px; border-radius:4px; font-size:0.8em;">Απαντήθηκε</span>';
    prevReplySection.style.display = "block";
    document.getElementById("modalReplyContent").innerText = msg.reply_content;
    document.getElementById("modalReplyDate").innerText =
      "Στάλθηκε στις: " + new Date(msg.replied_at).toLocaleString("el-GR");
  } else {
    replyBadge.innerHTML =
      '<span style="background:#f39c12; color:white; padding:2px 8px; border-radius:4px; font-size:0.8em;">Εκκρεμεί</span>';
    prevReplySection.style.display = "none";
  }

  // 3. Reset reply form section on open
  document.getElementById("replyFormSection").style.display = "none";
  document.getElementById("replyText").value = "";

  // 4. Bind send button to current message
  document.getElementById("confirmSendBtn").onclick = () =>
    sendRealReply(msg.id, msg.email);

  // 5. Open reply form when clicking "Reply"
  document.getElementById("openReplyBtn").onclick = function () {
    const formSection = document.getElementById("replyFormSection");
    formSection.style.display = "block";
    this.style.display = "none";

    // Αυτόματο scroll προς τα κάτω για να φανεί η φόρμα
    formSection.scrollIntoView({ behavior: "smooth", block: "end" });
  };

  modal.style.display = "block";
}

// Close the message modal
function closeMessageModal() {
  document.getElementById("messageModal").style.display = "none";
}

// Close modal on outside click
window.onclick = function (event) {
  const modal = document.getElementById("messageModal");
  if (event.target == modal) {
    closeMessageModal();
  }
};

// Initialize filters on page load
document.addEventListener("DOMContentLoaded", () => {
  if (document.getElementById("contact-table-body")) {
    filterMessages();
  }
});

// Delete a message by id
async function deleteMessage(id) {
  if (!confirm("Είστε σίγουροι ότι θέλετε να διαγράψετε αυτό το μήνυμα;")) {
    return;
  }

  try {
    const response = await fetch("delete_contact.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id: id }),
    });

    const result = await response.json();

    if (result.success) {
      // Αντί για reload, απλά ανανεώνουμε τη σελίδα για να τραβήξει τα νέα δεδομένα η PHP
      location.reload();
    } else {
      alert("Σφάλμα κατά τη διαγραφή: " + result.message);
    }
  } catch (error) {
    console.error("Error:", error);
    alert("Σφάλμα επικοινωνίας με τον διακομιστή.");
  }
}

// Send reply email and save it to the database
async function sendRealReply(id, email) {
  const replyText = document.getElementById("replyText").value;
  const spinner = document.getElementById("sendSpinner");
  const sendBtn = document.getElementById("confirmSendBtn");

  if (!replyText.trim()) {
    alert("Παρακαλώ γράψτε μια απάντηση.");
    return;
  }

  // Show loading state and disable button
  spinner.style.display = "block";
  sendBtn.disabled = true;

  try {
    const response = await fetch("admin_send_reply.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        id: id,
        replyText: replyText,
        recipientEmail: email,
      }),
    });

    const result = await response.json();

    if (result.success) {
      alert("Η απάντηση στάλθηκε και αποθηκεύτηκε με επιτυχία!");
      location.reload(); // Ανανέωση για να δούμε το "Απαντήθηκε" στον πίνακα
    } else {
      alert("Σφάλμα: " + result.message);
    }
  } catch (error) {
    console.error("Error:", error);
    alert("Αποτυχία σύνδεσης με τον διακομιστή.");
  } finally {
    spinner.style.display = "none";
    sendBtn.disabled = false;
  }
}
