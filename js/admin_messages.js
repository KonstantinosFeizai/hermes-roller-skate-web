// js/admin_messages.js
// Purpose: Admin dashboard — σύνταξη & αποστολή μηνυμάτων, user search, preview, pagination.

// Wrap in IIFE to scope variables and prevent conflicts with global scripts
(() => {
  // ── State ─────────────────────────────────────────────────────
  let previewTimeout = null;
  let selectedUsers = []; // { id, name } — manual recipient chips
  let allUsers = []; // populated from ADMIN_USERS global
  let sentMessagesCache = [];
  let msgCurrentPage = 1; // Renamed to prevent global collision
  const ITEMS_PER_PAGE = 5;

  // ── Init ──────────────────────────────────────────────────────
  document.addEventListener("DOMContentLoaded", () => {
    allUsers = window.ADMIN_USERS || [];

    window.loadSentMessages();

    // Preview on filter changes
    document
      .querySelectorAll(
        "#filter-all, #filter-locations, #filter-interests, #filter-roles",
      )
      .forEach((el) => el.addEventListener("change", debouncePreview));

    // User search input
    const userSearchEl = document.getElementById("user-search-input");
    if (userSearchEl) {
      userSearchEl.addEventListener("input", handleUserSearch);
      userSearchEl.addEventListener("focus", handleUserSearch);
      userSearchEl.addEventListener("keydown", handleUserSearchKeydown);

      const dropdown = document.getElementById("user-search-dropdown");
      if (dropdown) {
        dropdown.addEventListener("click", handleUserDropdownClick);
      }

      document.addEventListener("click", (e) => {
        if (!e.target.closest("#manual-users-wrapper")) closeUserDropdown();
      });
    }
  });

  // ── Debounce preview ──────────────────────────────────────────
  function debouncePreview() {
    clearTimeout(previewTimeout);
    previewTimeout = setTimeout(previewRecipients, 500);
  }

  // ── User Search Autocomplete ───────────────────────────────────
  function handleUserSearch() {
    const q = document
      .getElementById("user-search-input")
      .value.trim()
      .toLowerCase();
    const dropdown = document.getElementById("user-search-dropdown");

    const pool = allUsers.filter(
      (u) => !selectedUsers.find((s) => s.id === u.id),
    );

    const matches = !q
      ? pool.slice(0, 12)
      : pool
          .filter(
            (u) =>
              u.name.toLowerCase().includes(q) ||
              u.username.toLowerCase().includes(q) ||
              String(u.id) === q.replace(/^#/, ""),
          )
          .slice(0, 10);

    if (!matches.length) {
      dropdown.innerHTML =
        '<div class="user-search-no-results">Δεν βρέθηκαν χρήστες</div>';
      dropdown.style.display = "block";
      return;
    }

    dropdown.innerHTML = matches
      .map(
        (u) => `
      <div class="user-search-item" data-user-id="${u.id}" data-user-name="${escapeHtml(u.name || u.username)}">
        <span class="user-search-name">${escapeHtml(u.name || u.username)}</span>
        <span class="user-search-meta">@${escapeHtml(u.username)} · #${u.id}</span>
      </div>`,
      )
      .join("");
    dropdown.style.display = "block";
  }

  function handleUserDropdownClick(e) {
    const item = e.target.closest(".user-search-item");
    if (!item) return;
    selectUser(item.dataset.userId, item.dataset.userName);
  }

  function handleUserSearchKeydown(e) {
    if (e.key === "Escape") closeUserDropdown();
  }

  function selectUser(id, name) {
    const userId = parseInt(id, 10);
    if (!userId || !name) return;

    if (!selectedUsers.find((u) => u.id === userId)) {
      selectedUsers.push({ id: userId, name });
      renderSelectedUsers();
      debouncePreview();
    }
    document.getElementById("user-search-input").value = "";
    closeUserDropdown();
  }

  window.removeSelectedUser = function (id) {
    selectedUsers = selectedUsers.filter((u) => u.id !== id);
    renderSelectedUsers();
    debouncePreview();
  };

  function renderSelectedUsers() {
    const container = document.getElementById("selected-users-chips");
    if (!container) return;
    container.innerHTML = selectedUsers
      .map(
        (u) => `
      <span class="user-chip">
        ${escapeHtml(u.name)}
        <button type="button" class="user-chip-remove" onclick="removeSelectedUser(${u.id})" title="Αφαίρεση">×</button>
      </span>`,
      )
      .join("");
  }

  function closeUserDropdown() {
    const el = document.getElementById("user-search-dropdown");
    if (el) el.style.display = "none";
  }

  // ── Συλλογή φίλτρων από UI ────────────────────────────────────
  function collectFilters() {
    const filters = {};

    if (document.getElementById("filter-all")?.checked) {
      filters.all = true;
      return filters;
    }

    const locs = [
      ...document.querySelectorAll("#filter-locations input:checked"),
    ].map((el) => parseInt(el.value));
    if (locs.length) filters.locations = locs;

    const interests = [
      ...document.querySelectorAll("#filter-interests input:checked"),
    ].map((el) => el.value);
    if (interests.length) filters.interests = interests;

    const roles = [
      ...document.querySelectorAll("#filter-roles input:checked"),
    ].map((el) => el.value);
    if (roles.length) filters.roles = roles;

    if (selectedUsers.length)
      filters.manual_ids = selectedUsers.map((u) => u.id);

    return filters;
  }

  // ── Preview παραληπτών ────────────────────────────────────────
  async function previewRecipients() {
    const previewEl = document.getElementById("recipients-preview");
    if (!previewEl) return;

    const filters = collectFilters();
    previewEl.innerHTML = '<span style="color:#888">Φόρτωση...</span>';

    try {
      const res = await fetch(BASE_URL + "admin/get_filter_preview.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(filters),
      });
      const data = await res.json();

      if (data.status !== "success") {
        previewEl.innerHTML = `<span style="color:#e74c3c">${data.message}</span>`;
        return;
      }

      if (data.count === 0) {
        previewEl.innerHTML =
          '<span style="color:#f39c12">Κανένας παραλήπτης με αυτά τα φίλτρα.</span>';
        return;
      }

      const names = data.recipients
        .slice(0, 5)
        .map((r) => escapeHtml(r.name))
        .join(", ");
      const extra = data.count > 5 ? ` +${data.count - 5} ακόμα` : "";
      previewEl.innerHTML = `<strong style="color:#27ae60">${data.count} παραλήπτες:</strong> <span style="color:#555">${names}${extra}</span>`;
    } catch {
      previewEl.innerHTML =
        '<span style="color:#e74c3c">Σφάλμα σύνδεσης.</span>';
    }
  }

  // ── Αποστολή μηνύματος ────────────────────────────────────────
  window.sendAdminMessage = async function () {
    const subject = document.getElementById("msg-subject")?.value.trim();
    const body = document.getElementById("msg-body")?.value.trim();
    const send_email =
      document.getElementById("msg-send-email")?.checked ?? false;
    const statusEl = document.getElementById("msg-send-status");
    const btn = document.getElementById("msg-send-btn");

    if (!subject || !body) {
      showStatus(statusEl, "Παρακαλώ συμπλήρωσε θέμα και μήνυμα.", "error");
      return;
    }

    const filters = collectFilters();
    if (!filters.all && !Object.keys(filters).length) {
      showStatus(
        statusEl,
        'Παρακαλώ επίλεξε τουλάχιστον ένα φίλτρο ή "Όλοι".',
        "error",
      );
      return;
    }

    btn.disabled = true;
    btn.textContent = "Αποστολή...";
    statusEl.style.display = "none";

    try {
      const res = await fetch(BASE_URL + "api/send_admin_message.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ subject, body, filters, send_email }),
      });
      const data = await res.json();

      if (data.status === "success") {
        statusEl.innerHTML = `✅ Εστάλη σε <strong>${data.recipients}</strong> παραλήπτες.${
          send_email
            ? ` Email: ${data.email_sent} επιτυχή, ${data.email_failed} αποτυχημένα.`
            : ""
        }`;
        statusEl.className = "form-message form-message--success";
        statusEl.style.display = "block";

        document.getElementById("msg-subject").value = "";
        document.getElementById("msg-body").value = "";
        document.getElementById("recipients-preview").innerHTML = "";
        selectedUsers = [];
        renderSelectedUsers();
        document
          .querySelectorAll(
            "#filter-all, #filter-locations input, #filter-interests input, #filter-roles input",
          )
          .forEach((el) => (el.checked = false));

        msgCurrentPage = 1;
        window.loadSentMessages();
      } else {
        showStatus(statusEl, data.message || "Σφάλμα αποστολής.", "error");
      }
    } catch {
      showStatus(statusEl, "Αδυναμία σύνδεσης.", "error");
    } finally {
      btn.disabled = false;
      btn.textContent = "✉️ Αποστολή Μηνύματος";
    }
  };

  // ── Λίστα απεσταλμένων μηνυμάτων & Pagination ─────────────────
  window.loadSentMessages = async function () {
    const listEl = document.getElementById("sent-messages-list");
    if (!listEl) return;

    listEl.innerHTML = '<p class="msg-empty">Φόρτωση...</p>';

    try {
      const res = await fetch(BASE_URL + "admin/get_admin_messages.php");
      const data = await res.json();

      if (!data.messages?.length) {
        listEl.innerHTML =
          '<p class="msg-empty">Δεν έχουν σταλεί μηνύματα ακόμα.</p>';
        return;
      }

      sentMessagesCache = data.messages;
      renderPaginatedMessages();
    } catch {
      listEl.innerHTML = '<p style="color:#e74c3c">Σφάλμα φόρτωσης.</p>';
    }
  };

  function renderPaginatedMessages() {
    const listEl = document.getElementById("sent-messages-list");
    if (!listEl) return;

    const totalPages = Math.ceil(sentMessagesCache.length / ITEMS_PER_PAGE);
    if (msgCurrentPage > totalPages) msgCurrentPage = totalPages || 1;

    const start = (msgCurrentPage - 1) * ITEMS_PER_PAGE;
    const pageItems = sentMessagesCache.slice(start, start + ITEMS_PER_PAGE);

    const cardsHtml = pageItems
      .map((m, i) => {
        const index = start + i;
        const date = new Date(m.sent_at).toLocaleString("el-GR");
        const filterSummary = buildFilterSummary(m.filters);
        const previewId = `smsg-body-${index}`;
        return `
          <div class="sent-msg-card">
            <div class="sent-msg-header">
              <div class="sent-msg-subject">${escapeHtml(m.subject)}</div>
              <button class="sent-msg-toggle" onclick="toggleMsgBody('${previewId}', this)" title="Προβολή">
                <span>👁 Προβολή</span>
              </button>
            </div>
            <div class="sent-msg-meta">
              📅 ${date}
              <span class="sent-msg-badge">👥 ${m.recipient_count} παραλήπτες</span>
              ${m.send_email ? '<span class="sent-msg-badge sent-msg-badge--email">📧 Email</span>' : ""}
            </div>
            ${filterSummary ? `<div class="sent-msg-filters">${filterSummary}</div>` : ""}
            <div id="${previewId}" class="sent-msg-body" hidden>
              <div class="sent-msg-body-text">${escapeHtml(m.body)}</div>
            </div>
            <div class="sent-msg-actions" style="margin-top:10px;">
              <button class="action-btn btn-info" style="padding:5px 12px;font-size:.78rem;"
                  onclick="openThreadsModal(${m.id})">
                💬 Απαντήσεις
              </button>
            </div>
          </div>`;
      })
      .join("");

    const paginationHtml =
      totalPages > 1
        ? `
      <div class="history-pagination">
        <button class="action-btn" ${msgCurrentPage === 1 ? "disabled" : ""} onclick="changeSentPage(-1)">◀ Προηγούμενο</button>
        <span class="history-pagination-info">${msgCurrentPage} / ${totalPages}</span>
        <button class="action-btn" ${msgCurrentPage === totalPages ? "disabled" : ""} onclick="changeSentPage(1)">Επόμενο ▶</button>
      </div>`
        : "";

    listEl.innerHTML = `<div class="sent-msg-container">${cardsHtml}</div>${paginationHtml}`;
  }

  window.changeSentPage = function (delta) {
    msgCurrentPage += delta;
    renderPaginatedMessages();
    const listEl = document.getElementById("sent-messages-list");
    if (listEl) listEl.scrollTop = 0;
  };

  window.toggleMsgBody = function (id, btn) {
    const el = document.getElementById(id);
    if (!el) return;
    const open = !el.hidden;
    el.hidden = open;
    btn.querySelector("span").textContent = open ? "👁 Προβολή" : "🔼 Απόκρυψη";
  };

  // ── Helpers ───────────────────────────────────────────────────
  function buildFilterSummary(filters) {
    if (!filters || typeof filters !== "object") return "";
    const parts = [];
    if (filters.all) parts.push("Όλοι");
    if (filters.locations?.length)
      parts.push(`📍 ${filters.locations.length} περιοχές`);
    if (filters.interests?.length)
      parts.push(`🎯 ${filters.interests.join(", ")}`);
    if (filters.roles?.length) parts.push(`👤 ${filters.roles.join(", ")}`);
    if (filters.manual_ids?.length)
      parts.push(`✋ ${filters.manual_ids.length} χειροκίνητα`);
    return parts.join(" · ");
  }

  function showStatus(el, msg, type) {
    el.textContent = msg;
    el.className = `form-message form-message--${type}`;
    el.style.display = "block";
  }

  function escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text ?? "";
    return div.innerHTML;
  }

  // ── Threads Modal ─────────────────────────────────────────────
  let activeThreadMessageId = null;
  let activeThreadRecipientId = null;

  window.openThreadsModal = async function (messageId) {
    activeThreadMessageId = messageId;
    activeThreadRecipientId = null;

    const msg = sentMessagesCache.find((m) => m.id === messageId);
    document.getElementById("threadsModalSubject").textContent = msg
      ? `«${msg.subject}»`
      : "";

    document.getElementById("threadsModal").style.display = "flex";
    document.getElementById("threads-active-recipient-name").textContent =
      "Επίλεξε παραλήπτη";
    document.getElementById("threads-active-container").innerHTML =
      '<p class="thread-empty">Επίλεξε έναν παραλήπτη αριστερά για να δεις τη συζήτηση.</p>';
    document.getElementById("threads-reply-bar").style.display = "none";

    await loadThreadsRecipientsList(messageId);
  };

  window.closeThreadsModal = function () {
    document.getElementById("threadsModal").style.display = "none";
    activeThreadMessageId = null;
    activeThreadRecipientId = null;
  };

  document.addEventListener("keydown", (e) => {
    const input = document.getElementById("threads-reply-input");
    if (document.activeElement === input && e.key === "Enter" && !e.shiftKey) {
      e.preventDefault();
      window.sendAdminThreadReply();
    }
  });

  async function loadThreadsRecipientsList(messageId) {
    const listEl = document.getElementById("threads-recipients-list");
    listEl.innerHTML = '<p class="thread-empty">Φόρτωση...</p>';

    try {
      const res = await fetch(
        `${BASE_URL}api/get_message_threads_summary.php?message_id=${messageId}`,
      );
      const data = await res.json();

      if (data.status !== "success" || !data.recipients?.length) {
        listEl.innerHTML = '<p class="thread-empty">Κανένας παραλήπτης.</p>';
        return;
      }

      listEl.innerHTML = data.recipients
        .map((r) => {
          const hasReplies = r.user_reply_count > 0;
          return `
          <button class="threads-recipient-item ${hasReplies ? "has-replies" : ""}"
              onclick="selectThreadRecipient(${r.user_id}, '${escapeHtml(r.name)}')">
            <span class="threads-recipient-name">${escapeHtml(r.name)}</span>
            ${hasReplies ? `<span class="threads-recipient-badge">${r.user_reply_count}</span>` : ""}
          </button>`;
        })
        .join("");
    } catch {
      listEl.innerHTML =
        '<p class="thread-empty" style="color:#e74c3c;">Σφάλμα φόρτωσης.</p>';
    }
  }

  window.selectThreadRecipient = async function (userId, name) {
    document
      .querySelectorAll(".threads-recipient-item")
      .forEach((el) => el.classList.remove("is-active"));
    document.querySelectorAll(".threads-recipient-item").forEach((el) => {
      if (el.textContent.includes(name)) el.classList.add("is-active");
    });

    activeThreadRecipientId = userId;
    document.getElementById("threads-active-recipient-name").textContent = name;

    const container = document.getElementById("threads-active-container");
    container.innerHTML = '<p class="thread-empty">Φόρτωση...</p>';

    const html = await loadThreadHtml(activeThreadMessageId, userId, true);
    container.innerHTML = html;

    container.scrollTop = container.scrollHeight;

    const replyBar = document.getElementById("threads-reply-bar");
    const replyInput = document.getElementById("threads-reply-input");
    replyBar.style.display = "flex";
    replyInput.value = "";
    replyInput.focus();
  };

  async function loadThreadHtml(messageId, recipientId, isAdmin = false) {
    try {
      const res = await fetch(
        `${BASE_URL}api/get_message_thread.php?message_id=${messageId}&recipient_id=${recipientId}`,
      );
      const data = await res.json();

      if (data.status !== "success" || !data.thread?.length) {
        return '<p class="thread-empty">Δεν υπάρχουν μηνύματα στη συζήτηση.</p>';
      }

      return data.thread
        .map((msg) => {
          // Admin is "me" (right side), User is "them" (left side)
          const isMe = msg.sender_type === "admin";
          const time = new Date(msg.created_at).toLocaleString("el-GR");
          return `
          <div class="thread-bubble ${isMe ? "thread-bubble--me" : "thread-bubble--them"}">
            <div class="thread-bubble-body">${escapeHtml(msg.body)}</div>
            <div class="thread-bubble-time">${time}</div>
          </div>`;
        })
        .join("");
    } catch {
      return '<p class="thread-empty" style="color:#e74c3c;">Σφάλμα φόρτωσης συζήτησης.</p>';
    }
  }

  window.sendAdminThreadReply = async function () {
    const textarea = document.getElementById("threads-reply-input");
    const btn = document.querySelector(".threads-reply-send-btn");
    const body = textarea.value.trim();

    if (!body || !activeThreadMessageId || !activeThreadRecipientId) {
      textarea.classList.add("threads-reply-input--error");
      setTimeout(
        () => textarea.classList.remove("threads-reply-input--error"),
        1000,
      );
      return;
    }

    btn.disabled = true;
    try {
      const res = await fetch(`${BASE_URL}api/send_reply.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          message_id: activeThreadMessageId,
          recipient_id: activeThreadRecipientId,
          body,
        }),
      });
      const data = await res.json();
      if (data.status === "success") {
        textarea.value = "";
        const html = await loadThreadHtml(
          activeThreadMessageId,
          activeThreadRecipientId,
          true,
        );
        const container = document.getElementById("threads-active-container");
        container.innerHTML = html;
        container.scrollTop = container.scrollHeight;
        await loadThreadsRecipientsList(activeThreadMessageId);
      } else {
        console.error("sendAdminThreadReply failed:", data.message);
      }
    } catch (err) {
      console.error("sendAdminThreadReply error:", err);
    } finally {
      btn.disabled = false;
    }
  };
})();
