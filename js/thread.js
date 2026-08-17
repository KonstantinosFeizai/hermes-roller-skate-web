// js/thread.js
// Purpose: Κοινή λογική για render & αποστολή replies σε ένα private thread
//          (message_id, recipient_id). Χρησιμοποιείται από:
//          - profile.php (χρήστης βλέπει/απαντά στο δικό του thread)
//          - admin_dashboard.php (admin βλέπει/απαντά σε thread με συγκεκριμένο recipient)

// Ensure BASE_URL is defined
const BASE_URL = window.BASE_URL || "../";

// ── Helpers ──────────────────────────────────────────────────────

function threadEscapeHtml(text) {
  const div = document.createElement("div");
  div.textContent = text ?? "";
  return div.innerHTML;
}

function threadFormatDate(dateStr) {
  if (!dateStr) return "";
  return new Date(dateStr).toLocaleString("el-GR", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}

/**
 * Render ένα HTML block λίστας από replies (bubbles).
 * @param {Object|null} originalMessage - The original admin message
 * @param {Array} replies
 * @param {boolean} viewerIsAdmin - true αν ο viewer είναι ο admin (καθορίζει ποια bubble είναι "δικά μου")
 */
function renderThreadBubbles(originalMessage, replies, viewerIsAdmin) {
  const allMessages = [];

  // Add original message as the first bubble
  if (originalMessage) {
    allMessages.push({
      id: originalMessage.id,
      body: originalMessage.body,
      is_from_admin: 1, // Original message is always from admin
      created_at: originalMessage.created_at,
      sender_name: originalMessage.sender_name || "Admin",
      is_original: true,
    });
  }

  // Add all replies
  if (replies && replies.length > 0) {
    allMessages.push(...replies);
  }

  if (allMessages.length === 0) {
    return '<p class="thread-empty">Δεν υπάρχουν μηνύματα.</p>';
  }

  return allMessages
    .map((r) => {
      // "mine" bubble: admin βλέπει τα δικά admin replies ως mine,
      // χρήστης βλέπει τα δικά του (is_from_admin=0) ως mine.
      const isMine = viewerIsAdmin ? !!r.is_from_admin : !r.is_from_admin;
      const cls = isMine
        ? "thread-bubble thread-bubble--mine"
        : "thread-bubble thread-bubble--other";
      const label = r.is_from_admin
        ? "Admin"
        : threadEscapeHtml(r.sender_name || "Χρήστης");

      return `
        <div class="${cls}">
            <div class="thread-bubble-meta">
                <span class="thread-bubble-sender">${label}</span>
                <span class="thread-bubble-date">${threadFormatDate(r.created_at)}</span>
            </div>
            <div class="thread-bubble-body">${threadEscapeHtml(r.body).replace(/\n/g, "<br>")}</div>
        </div>`;
    })
    .join("");
}

/**
 * Φορτώνει το thread από τον server και επιστρέφει το HTML (bubbles + reply form).
 * @param {number} messageId
 * @param {number} recipientId
 * @param {boolean} viewerIsAdmin
 * @returns {Promise<string>} HTML string έτοιμο για innerHTML
 */
async function loadThreadHtml(messageId, recipientId, viewerIsAdmin) {
  try {
    const res = await fetch(
      `${BASE_URL}api/get_thread.php?message_id=${messageId}&recipient_id=${recipientId}`,
    );

    if (!res.ok) {
      console.error(`get_thread.php returned ${res.status}`);
      return `<p class="thread-empty" style="color:#e74c3c;">Σφάλμα σύνδεσης (${res.status}).</p>`;
    }

    const data = await res.json();

    if (data.status !== "success") {
      return `<p class="thread-empty" style="color:#e74c3c;">${threadEscapeHtml(data.message || "Σφάλμα φόρτωσης.")}</p>`;
    }

    const bubblesHtml = renderThreadBubbles(
      data.original_message,
      data.replies,
      viewerIsAdmin,
    );

    return `
        <div class="thread-wrapper" data-message-id="${messageId}" data-recipient-id="${recipientId}">
            <div class="thread-bubbles">${bubblesHtml}</div>
            <div class="thread-reply-status" style="display:none;"></div>
        </div>`;
  } catch (err) {
    console.error("loadThreadHtml error:", err);
    return '<p class="thread-empty" style="color:#e74c3c;">Αδυναμία σύνδεσης.</p>';
  }
}

/**
 * Στέλνει ένα reply και ανανεώνει το thread container όπου βρίσκεται το textarea.
 */
async function sendThreadReply(messageId, recipientId, viewerIsAdmin) {
  const wrapper = document.querySelector(
    `.thread-wrapper[data-message-id="${messageId}"][data-recipient-id="${recipientId}"]`,
  );
  if (!wrapper) return;

  const textarea = wrapper.querySelector(".thread-reply-input");
  const statusEl = wrapper.querySelector(".thread-reply-status");
  const btn = wrapper.querySelector(".thread-reply-send-btn");
  const body = textarea.value.trim();

  if (!body) {
    statusEl.textContent = "Γράψε κάτι πριν στείλεις.";
    statusEl.style.color = "#e74c3c";
    statusEl.style.display = "block";
    return;
  }

  btn.disabled = true;
  try {
    const res = await fetch(BASE_URL + "api/send_reply.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        message_id: messageId,
        recipient_id: recipientId,
        body,
      }),
    });

    console.log("send_reply response status:", res.status);
    const data = await res.json();
    console.log("send_reply data:", data);

    if (data.status === "success") {
      textarea.value = "";
      statusEl.style.display = "none";

      console.log("Reloading thread...");
      // Ανανέωση μόνο των bubbles (όχι ολόκληρου του reply box)
      const html = await loadThreadHtml(messageId, recipientId, viewerIsAdmin);
      console.log("Received HTML length:", html.length);
      wrapper.outerHTML = html;
    } else {
      console.error("send_reply failed:", data.message);
      statusEl.textContent = data.message || "Σφάλμα αποστολής.";
      statusEl.style.color = "#e74c3c";
      statusEl.style.display = "block";
    }
  } catch (err) {
    console.error("sendThreadReply error:", err);
    if (statusEl) {
      statusEl.textContent = "Αδυναμία σύνδεσης.";
      statusEl.style.color = "#e74c3c";
      statusEl.style.display = "block";
    }
  } finally {
    if (btn) btn.disabled = false;
  }
}

/**
 * Toggles the reply box visibility
 */
function toggleThreadReplyBox(button) {
  const wrapper = button.closest(".thread-wrapper");
  if (!wrapper) return;

  const replyBox = wrapper.querySelector(".thread-reply-box");
  const toggleBtn = wrapper.querySelector(".thread-reply-toggle-btn");

  if (!replyBox) return;

  if (replyBox.style.display === "none") {
    // Show reply box
    replyBox.style.display = "flex";
    toggleBtn.style.display = "none";

    // Focus textarea
    const textarea = replyBox.querySelector(".thread-reply-input");
    if (textarea) {
      setTimeout(() => textarea.focus(), 100);
    }
  } else {
    // Hide reply box
    replyBox.style.display = "none";
    toggleBtn.style.display = "block";
  }
}

/**
 * Cancels reply and hides the reply box
 */
function cancelThreadReply(button) {
  const wrapper = button.closest(".thread-wrapper");
  if (!wrapper) return;

  const replyBox = wrapper.querySelector(".thread-reply-box");
  const toggleBtn = wrapper.querySelector(".thread-reply-toggle-btn");
  const textarea = wrapper.querySelector(".thread-reply-input");

  if (replyBox) replyBox.style.display = "none";
  if (toggleBtn) toggleBtn.style.display = "block";
  if (textarea) textarea.value = ""; // Clear textarea
}
