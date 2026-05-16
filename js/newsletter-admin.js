// newsletter-admin.js
// Purpose: Admin UI helpers for newsletter subscribers (load, search, send).

// Fetch subscribers and render the table
async function loadNewsletterSubscribers() {
  const tableBody = document.getElementById("newsletter-table-body");
  const countEl = document.getElementById("newsletterCount");

  if (!tableBody || !countEl) return;

  tableBody.innerHTML = '<tr><td colspan="3">Φόρτωση...</td></tr>';

  try {
    const response = await fetch("get_newsletter_subscribers.php");
    const data = await response.json();

    if (!data.success) {
      tableBody.innerHTML = '<tr><td colspan="3">Σφάλμα φόρτωσης.</td></tr>';
      return;
    }

    const subscribers = data.subscribers || [];
    const stats = data.stats || {};

    // Ενημέρωση στατιστικών
    countEl.innerHTML = `
      Σύνολο: <strong>${stats.total ?? 0}</strong> &nbsp;|&nbsp;
      Ενεργοί: <strong style="color: #2d8a4e">${stats.active ?? 0}</strong> &nbsp;|&nbsp;
      Unsubscribed: <strong style="color: #c0392b">${stats.inactive ?? 0}</strong>
    `;

    if (subscribers.length === 0) {
      tableBody.innerHTML =
        '<tr><td colspan="3">Δεν υπάρχουν εγγεγραμμένοι.</td></tr>';
      return;
    }

    // Render rows
    tableBody.innerHTML = subscribers
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
          </tr>
        `;
      })
      .join("");
  } catch (error) {
    tableBody.innerHTML = '<tr><td colspan="3">Σφάλμα φόρτωσης.</td></tr>';
  }
}

// Filter table rows by email
function filterNewsletterTable() {
  const input = document.getElementById("newsletterSearch");
  const filter = input ? input.value.toLowerCase() : "";
  const rows = document.querySelectorAll("#newsletter-table-body tr");

  rows.forEach((row) => {
    const emailCell = row.querySelector("td");
    const email = emailCell ? emailCell.textContent.toLowerCase() : "";
    row.style.display = email.includes(filter) ? "" : "none";
  });
}

// Basic HTML escape
function escapeHtml(text) {
  const div = document.createElement("div");
  div.textContent = text ?? "";
  return div.innerHTML;
}

// Send newsletter to all subscribers
async function sendNewsletter(e) {
  e.preventDefault();

  const form = document.getElementById("newsletterSendForm");
  const statusEl = document.getElementById("newsletterSendStatus");
  const sendBtn = document.getElementById("newsletterSendBtn");

  if (!form || !statusEl || !sendBtn) {
    return;
  }

  // Reset status and disable button during send
  statusEl.style.display = "none";
  sendBtn.disabled = true;

  try {
    // Submit form data to the server
    const formData = new FormData(form);
    const response = await fetch("send_newsletter.php", {
      method: "POST",
      body: formData,
    });
    const data = await response.json();

    if (data.success) {
      // Success message
      statusEl.textContent = `Εστάλη σε ${data.sent} email. Αποτυχημένα: ${data.failed}.`;
      statusEl.className = "form-message success";
      form.reset();
    } else {
      // Error message from server
      statusEl.textContent = data.message || "Αποτυχία αποστολής.";
      statusEl.className = "form-message error";
    }
  } catch (error) {
    // Network or unexpected error
    statusEl.textContent = "Αποτυχία αποστολής.";
    statusEl.className = "form-message error";
  } finally {
    // Re-enable button and show status
    statusEl.style.display = "block";
    sendBtn.disabled = false;
  }
}

document.addEventListener("DOMContentLoaded", () => {
  // Wire up admin UI actions
  const refreshBtn = document.getElementById("refreshNewsletterBtn");
  const sendForm = document.getElementById("newsletterSendForm");
  const newsletterTabLink = document.getElementById("newsletter-tab-link");

  if (refreshBtn) {
    refreshBtn.addEventListener("click", loadNewsletterSubscribers);
  }

  if (sendForm) {
    sendForm.addEventListener("submit", sendNewsletter);
  }

  if (newsletterTabLink) {
    newsletterTabLink.addEventListener("click", loadNewsletterSubscribers);
  }

  // Initial load
  loadNewsletterSubscribers();
});
