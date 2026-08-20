// js/newsletter-admin.js
// Purpose: Admin UI helpers for newsletter subscribers & campaigns.

// ── Μεταβλητές Pagination & Filtering (Newsletter Specific) ──────
let newsletterAllSubscribers = [];
let newsletterFilteredSubscribers = [];
let newsletterCurrentPage = 1;
const newsletterRowsPerPage = 10; // ← Αριθμός εγγραφών ανά σελίδα

// ── 1. Φόρτωση Συνδρομητών ──────────────────────────────────
async function loadNewsletterSubscribers() {
  const tableBody = document.getElementById("newsletter-table-body");
  const countEl = document.getElementById("newsletterCount");

  if (!tableBody || !countEl) return;

  tableBody.innerHTML = '<tr><td colspan="4">Φόρτωση...</td></tr>';

  try {
    const response = await fetch("get_newsletter_subscribers.php");
    const data = await response.json();

    if (!data.success) {
      tableBody.innerHTML = '<tr><td colspan="4">Σφάλμα φόρτωσης.</td></tr>';
      return;
    }

    newsletterAllSubscribers = data.subscribers || [];
    const stats = data.stats || {};

    // Ενημέρωση στατιστικών
    countEl.innerHTML = `
      Σύνολο: <strong>${stats.total ?? 0}</strong> &nbsp;|&nbsp;
      Ενεργοί: <strong style="color: #2d8a4e">${stats.active ?? 0}</strong> &nbsp;|&nbsp;
      Unsubscribed: <strong style="color: #c0392b">${stats.inactive ?? 0}</strong>
    `;

    // Εφαρμογή φίλτρων (καλεί αυτόματα το render)
    filterNewsletterTable();
  } catch (error) {
    tableBody.innerHTML = '<tr><td colspan="4">Σφάλμα φόρτωσης.</td></tr>';
  }
}

// ── 2. Φιλτράρισμα Πίνακα ───────────────────────────────────
function filterNewsletterTable() {
  const searchInput = document.getElementById("newsletterSearch");
  const statusFilter = document.getElementById("newsletterStatusFilter");

  const query = searchInput ? searchInput.value.toLowerCase() : "";
  const selectedStatus = statusFilter ? statusFilter.value : "";

  // Φιλτράρισμα των δεδομένων
  newsletterFilteredSubscribers = newsletterAllSubscribers.filter((sub) => {
    const email = (sub.email || "").toLowerCase();
    const isActive = sub.is_active == 1;
    const status = isActive ? "active" : "inactive";

    const matchesSearch = email.includes(query);
    const matchesStatus = selectedStatus === "" || status === selectedStatus;

    return matchesSearch && matchesStatus;
  });

  // Επαναφορά στην 1η σελίδα σε κάθε αλλαγή φίλτρου
  newsletterCurrentPage = 1;
  renderNewsletterTableAndPagination();
}

// ── 2A. Render Πίνακα & Κουμπιών Pagination ─────────────────
function renderNewsletterTableAndPagination() {
  const tableBody = document.getElementById("newsletter-table-body");
  const paginationContainer = document.getElementById("newsletterPagination");

  if (newsletterFilteredSubscribers.length === 0) {
    tableBody.innerHTML =
      '<tr><td colspan="4">Δεν βρέθηκαν συνδρομητές.</td></tr>';
    if (paginationContainer) paginationContainer.innerHTML = "";
    return;
  }

  // Υπολογισμός τεμαχίου (slice) για την τρέχουσα σελίδα
  const startIndex = (newsletterCurrentPage - 1) * newsletterRowsPerPage;
  const endIndex = startIndex + newsletterRowsPerPage;
  const paginatedData = newsletterFilteredSubscribers.slice(
    startIndex,
    endIndex,
  );

  // Render Γραμμών (Rows)
  tableBody.innerHTML = paginatedData
    .map((sub) => {
      const date = sub.subscribed_at ? new Date(sub.subscribed_at) : null;
      const formattedDate = date ? date.toLocaleString("el-GR") : "-";

      const isActive = sub.is_active == 1;
      const statusBadge = isActive
        ? '<span style="color:#2d8a4e; font-weight:600;">✔ Ενεργός</span>'
        : '<span style="color:#c0392b; font-weight:600;">✘ Unsubscribed</span>';

      return `
        <tr>
          <td>${escapeHtml(sub.email)}</td>
          <td>${statusBadge}</td>
          <td>${formattedDate}</td>
          <td style="text-align: center;">
            <button class="action-btn btn-danger" style="padding: 4px 8px; font-size: 12px;" onclick="deleteSubscriber('${escapeHtml(sub.email)}')">🗑️ Διαγραφή</button>
          </td>
        </tr>
      `;
    })
    .join("");

  // Υπολογισμός & Render κουμπιών Pagination
  const totalPages = Math.ceil(
    newsletterFilteredSubscribers.length / newsletterRowsPerPage,
  );

  if (!paginationContainer) return;

  if (totalPages <= 1) {
    paginationContainer.innerHTML = ""; // Απόκρυψη αν υπάρχει μόνο 1 σελίδα
    return;
  }

  let paginationHTML = "";

  // Κουμπί "Προηγούμενο"
  paginationHTML += `<button class="action-btn btn-secondary" style="padding: 5px 10px;" onclick="changeNewsletterPage(${newsletterCurrentPage - 1})" ${newsletterCurrentPage === 1 ? "disabled" : ""}>&laquo;</button>`;

  // Κουμπιά Σελίδων
  for (let i = 1; i <= totalPages; i++) {
    const activeClass =
      i === newsletterCurrentPage ? "btn-primary" : "btn-secondary";
    paginationHTML += `<button class="action-btn ${activeClass}" style="padding: 5px 10px;" onclick="changeNewsletterPage(${i})">${i}</button>`;
  }

  // Κουμπί "Επόμενο"
  paginationHTML += `<button class="action-btn btn-secondary" style="padding: 5px 10px;" onclick="changeNewsletterPage(${newsletterCurrentPage + 1})" ${newsletterCurrentPage === totalPages ? "disabled" : ""}>&raquo;</button>`;

  paginationContainer.innerHTML = paginationHTML;
}

// ── 2B. Αλλαγή Σελίδας ──────────────────────────────────────
function changeNewsletterPage(page) {
  const totalPages = Math.ceil(
    newsletterFilteredSubscribers.length / newsletterRowsPerPage,
  );
  if (page < 1 || page > totalPages) return;

  newsletterCurrentPage = page;
  renderNewsletterTableAndPagination();
}

// ── 3. Διαγραφή Συνδρομητή ─────────────────────────────────
async function deleteSubscriber(email) {
  if (
    !confirm(
      `Είσαι σίγουρος ότι θέλεις να διαγράψεις οριστικά το email "${email}";`,
    )
  ) {
    return;
  }

  try {
    const formData = new FormData();
    formData.append("email", email);

    const response = await fetch("delete_subscriber.php", {
      method: "POST",
      body: formData,
    });
    const data = await response.json();

    if (data.success) {
      loadNewsletterSubscribers();
    } else {
      alert(data.message || "Αποτυχία διαγραφής.");
    }
  } catch (error) {
    alert("Σφάλμα κατά τη διαγραφή.");
  }
}

// ── 4. Φόρτωση Ιστορικού Καμπανιών ─────────────────────────
async function loadNewsletterLogs() {
  const logsBody = document.getElementById("newsletter-logs-body");
  if (!logsBody) return;

  logsBody.innerHTML = '<tr><td colspan="4">Φόρτωση ιστορικού...</td></tr>';

  try {
    const response = await fetch("get_newsletter_logs.php");
    const data = await response.json();

    if (!data.success) {
      logsBody.innerHTML =
        '<tr><td colspan="4">Σφάλμα φόρτωσης ιστορικού.</td></tr>';
      return;
    }

    const logs = data.logs || [];

    if (logs.length === 0) {
      logsBody.innerHTML =
        '<tr><td colspan="4">Δεν υπάρχουν προηγούμενες αποστολές.</td></tr>';
      return;
    }

    logsBody.innerHTML = logs
      .map((log) => {
        const date = log.sent_at ? new Date(log.sent_at) : null;
        const formattedDate = date ? date.toLocaleString("el-GR") : "-";

        return `
          <tr>
            <td><strong>${escapeHtml(log.subject)}</strong></td>
            <td><span style="color: #2d8a4e; font-weight: bold;">${log.sent_count}</span></td>
            <td><span style="color: #c0392b; font-weight: bold;">${log.failed_count}</span></td>
            <td>${formattedDate}</td>
          </tr>
        `;
      })
      .join("");
  } catch (error) {
    logsBody.innerHTML =
      '<tr><td colspan="4">Σφάλμα φόρτωσης ιστορικού.</td></tr>';
  }
}

// ── 5. Αποστολή Newsletter σε Όλους ────────────────────────
async function sendNewsletter(e) {
  e.preventDefault();

  if (
    !confirm(
      "Είσαι σίγουρος ότι θέλεις να στείλεις αυτό το email σε ΟΛΟΥΣ τους ενεργούς συνδρομητές;",
    )
  ) {
    return;
  }

  const form = document.getElementById("newsletterSendForm");
  const statusEl = document.getElementById("newsletterSendStatus");
  const sendBtn = document.getElementById("newsletterSendBtn");

  if (!form || !statusEl || !sendBtn) return;

  statusEl.style.display = "none";
  sendBtn.disabled = true;

  try {
    const formData = new FormData(form);
    const response = await fetch("send_newsletter.php", {
      method: "POST",
      body: formData,
    });
    const data = await response.json();

    if (data.success) {
      statusEl.textContent = `Εστάλη επιτυχώς σε ${data.sent} emails (Αποτυχίες: ${data.failed}).`;
      statusEl.className = "form-message success";
      form.reset();
      loadNewsletterLogs(); // Ανανέωση ιστορικού
    } else {
      statusEl.textContent = data.message || "Αποτυχία αποστολής.";
      statusEl.className = "form-message error";
    }
  } catch (error) {
    statusEl.textContent = "Σφάλμα δικτύου κατά την αποστολή.";
    statusEl.className = "form-message error";
  } finally {
    statusEl.style.display = "block";
    sendBtn.disabled = false;
  }
}

// ── 6. Δοκιμαστική Αποστολή (Test Send) ────────────────────
async function sendTestNewsletter() {
  const subject = document.getElementById("newsletterSubject").value.trim();
  const message = document.getElementById("newsletterMessage").value.trim();
  const statusEl = document.getElementById("newsletterSendStatus");
  const testBtn = document.getElementById("newsletterTestSendBtn");

  if (!subject || !message) {
    alert("Παρακαλώ συμπλήρωσε Θέμα και Μήνυμα πριν την δοκιμαστική αποστολή.");
    return;
  }

  statusEl.style.display = "none";
  testBtn.disabled = true;

  try {
    const formData = new FormData();
    formData.append("subject", subject);
    formData.append("message", message);

    const response = await fetch("send_test_newsletter.php", {
      method: "POST",
      body: formData,
    });
    const data = await response.json();

    statusEl.textContent = data.message;
    statusEl.className = data.success
      ? "form-message success"
      : "form-message error";
  } catch (error) {
    statusEl.textContent = "Σφάλμα κατά τη δοκιμαστική αποστολή.";
    statusEl.className = "form-message error";
  } finally {
    statusEl.style.display = "block";
    testBtn.disabled = false;
  }
}

// ── 7. Live Preview Modal ──────────────────────────────────
function openNewsletterPreview() {
  const subject = document.getElementById("newsletterSubject").value.trim();
  const message = document.getElementById("newsletterMessage").value.trim();

  if (!subject && !message) {
    alert("Συμπλήρωσε Θέμα ή Μήνυμα για να δεις την προεπισκόπηση.");
    return;
  }

  document.getElementById("previewSubject").textContent =
    subject || "(Χωρίς θέμα)";
  document.getElementById("previewBody").innerHTML = escapeHtml(
    message,
  ).replace(/\n/g, "<br>");
  document.getElementById("newsletterPreviewModal").style.display = "flex";
}

function closeNewsletterPreview() {
  document.getElementById("newsletterPreviewModal").style.display = "none";
}

// Helper για HTML Escaping
function escapeHtml(text) {
  const div = document.createElement("div");
  div.textContent = text ?? "";
  return div.innerHTML;
}

// ── Event Listeners ─────────────────────────────────────────
document.addEventListener("DOMContentLoaded", () => {
  const refreshBtn = document.getElementById("refreshNewsletterBtn");
  const sendForm = document.getElementById("newsletterSendForm");
  const previewBtn = document.getElementById("newsletterPreviewBtn");
  const testSendBtn = document.getElementById("newsletterTestSendBtn");
  const newsletterTabLink = document.getElementById("newsletter-tab-link");

  if (refreshBtn) {
    refreshBtn.addEventListener("click", () => {
      loadNewsletterSubscribers();
      loadNewsletterLogs();
    });
  }

  if (sendForm) {
    sendForm.addEventListener("submit", sendNewsletter);
  }

  if (previewBtn) {
    previewBtn.addEventListener("click", openNewsletterPreview);
  }

  if (testSendBtn) {
    testSendBtn.addEventListener("click", sendTestNewsletter);
  }

  if (newsletterTabLink) {
    newsletterTabLink.addEventListener("click", () => {
      loadNewsletterSubscribers();
      loadNewsletterLogs();
    });
  }

  // Αρχική φόρτωση
  loadNewsletterSubscribers();
  loadNewsletterLogs();
});
