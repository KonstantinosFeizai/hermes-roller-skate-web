// js/finance.js
// Finance tab: athlete balance cards, add payment, history modal, receipts.

// ── State ─────────────────────────────────────────────────────
let _allFinanceData = [];
let _currentAthleteId = null;
let _locationFilter = "";

// ── Load / Refresh ────────────────────────────────────────────

async function refreshFinanceTab() {
  document.getElementById("financeCardsGrid").innerHTML =
    '<p class="loading-msg">Φόρτωση...</p>';

  try {
    const res = await fetch("get_finance_overview.php");
    const result = await res.json();
    if (result.status !== "success") {
      alert(result.message);
      return;
    }

    _allFinanceData = result.athletes;
    _renderSummary(result.summary, result.athletes);
    _populateLocationFilter(result.athletes);
    _renderCards(_allFinanceData);
  } catch {
    document.getElementById("financeCardsGrid").innerHTML =
      '<p style="color:red">Σφάλμα φόρτωσης.</p>';
  }
}

function _renderSummary(s, athletes) {
  const debt = athletes.filter((a) => parseInt(a.lessons_remaining) < 0).length;
  const credit = athletes.filter(
    (a) => parseInt(a.lessons_remaining) > 0,
  ).length;

  document.getElementById("finMonthRevenue").textContent =
    Number(s.month_revenue).toLocaleString("el-GR", {
      minimumFractionDigits: 2,
    }) + " €";
  document.getElementById("finMonthLessons").textContent =
    s.month_lessons_sold + " μαθήματα";
  document.getElementById("finDebtCount").textContent = debt;
  document.getElementById("finCreditCount").textContent = credit;
}

function _renderCards(athletes) {
  const grid = document.getElementById("financeCardsGrid");

  if (!athletes.length) {
    grid.innerHTML =
      '<p class="empty-state">Δεν υπάρχουν αθλητές με ιστορικό.</p>';
    return;
  }

  grid.innerHTML = athletes
    .map((a) => {
      const rem = parseInt(a.lessons_remaining);
      const cls =
        rem > 0
          ? "balance-positive"
          : rem < 0
            ? "balance-negative"
            : "balance-zero";
      const sign = rem > 0 ? "+" : "";
      const lastPay = a.last_payment_date
        ? new Date(a.last_payment_date).toLocaleDateString("el-GR", {
            day: "2-digit",
            month: "2-digit",
            year: "numeric",
          })
        : "—";
      const totalPaid = Number(a.total_paid || 0).toLocaleString("el-GR", {
        minimumFractionDigits: 2,
      });

      return `
      <div class="fin-card" data-athlete-id="${a.athlete_id}">
        <div class="fin-card-top">
          <div class="fin-card-name">${escHtml(a.athlete_name)}</div>
          ${a.location_name ? `<div class="fin-card-location">📍 ${escHtml(a.location_name)}</div>` : ""}
        </div>

        <div class="fin-balance-row">
          <span class="fin-balance-badge ${cls}">${sign}${rem} μαθήματα</span>
        </div>

        <div class="fin-card-meta">
          <span>Χρησιμοποιήθηκαν: <strong>${a.lessons_used}</strong></span>
          <span>Αγοράστηκαν: <strong>${a.lessons_purchased}</strong></span>
        </div>

        <div class="fin-card-meta">
          <span>Τελ. πληρωμή: <strong>${lastPay}</strong></span>
          <span>Σύνολο: <strong>${totalPaid} €</strong></span>
        </div>

        <div class="fin-card-actions">
          <button class="action-btn btn-success"
                  onclick="openPaymentModal(${a.athlete_id}, '${escAttr(a.athlete_name)}')">
            + Πληρωμή
          </button>
          <button class="action-btn btn-info"
                  onclick="openHistoryModal(${a.athlete_id}, '${escAttr(a.athlete_name)}')">
            📋 Καρτέλα
          </button>
        </div>
      </div>
    `;
    })
    .join("");
}

// ── Search / Filter ───────────────────────────────────────────

function filterFinanceCards() {
  _locationFilter = document.getElementById("financeLocationFilter").value;
  const filtered = _locationFilter
    ? _allFinanceData.filter((a) => (a.location_name || "") === _locationFilter)
    : _allFinanceData;
  _renderCards(filtered);
}

function _populateLocationFilter(athletes) {
  const sel = document.getElementById("financeLocationFilter");
  const current = sel.value;
  const locations = [
    ...new Set(athletes.map((a) => a.location_name).filter(Boolean)),
  ].sort();

  sel.innerHTML =
    '<option value="">Όλες οι τοποθεσίες</option>' +
    locations
      .map((l) => `<option value="${escAttr(l)}">${escHtml(l)}</option>`)
      .join("");

  if (locations.includes(current)) sel.value = current;
}

// ── Add Payment Modal ─────────────────────────────────────────

function openPaymentModal(athleteId, name) {
  _currentAthleteId = athleteId;
  document.getElementById("pf_athlete_id").value = athleteId;
  document.getElementById("paymentAthleteLabel").textContent = name;
  document.getElementById("pf_type").value = "prepaid";
  document.getElementById("pf_method").value = "cash";
  document.getElementById("pf_lessons").value = "4";
  document.getElementById("pf_amount").value = "100";
  document.getElementById("pf_notes").value = "";
  document.getElementById("pf_date").value = new Date()
    .toISOString()
    .slice(0, 10);
  document.getElementById("paymentFormMessage").style.display = "none";
  onPayTypeChange();
  document.getElementById("paymentModal").style.display = "flex";
}

function closePaymentModal() {
  document.getElementById("paymentModal").style.display = "none";
}

function onPayTypeChange() {
  const type = document.getElementById("pf_type").value;
  const group = document.getElementById("pf_amount_group");
  const isFree = type === "free" || type === "gift";
  group.style.opacity = isFree ? "0.4" : "1";
  group.style.pointerEvents = isFree ? "none" : "";
  if (isFree) document.getElementById("pf_amount").value = "0";
  calcPricePerLesson();
}

function calcPricePerLesson() {
  const lessons = parseFloat(document.getElementById("pf_lessons").value) || 0;
  const amount = parseFloat(document.getElementById("pf_amount").value) || 0;
  const hint = document.getElementById("pf_price_hint");
  hint.textContent =
    lessons > 0 && amount > 0
      ? "→ " + (amount / lessons).toFixed(2) + " €/μάθημα"
      : "";
}

document.getElementById("paymentForm").addEventListener("submit", async (e) => {
  e.preventDefault();
  const msgEl = document.getElementById("paymentFormMessage");
  const btn = e.target.querySelector('[type="submit"]');
  btn.disabled = true;

  const payload = {
    athlete_id: parseInt(document.getElementById("pf_athlete_id").value),
    lessons_purchased: parseInt(document.getElementById("pf_lessons").value),
    amount: parseFloat(document.getElementById("pf_amount").value) || 0,
    payment_type: document.getElementById("pf_type").value,
    payment_method: document.getElementById("pf_method").value,
    payment_date: document.getElementById("pf_date").value,
    notes: document.getElementById("pf_notes").value.trim(),
  };

  try {
    const res = await fetch("add_payment.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });
    const result = await res.json();

    if (result.status === "success") {
      msgEl.textContent = result.message + " (" + result.receipt_number + ")";
      msgEl.style.color = "#27ae60";
      msgEl.style.display = "";
      setTimeout(() => {
        closePaymentModal();
        refreshFinanceTab();
      }, 1200);
    } else {
      msgEl.textContent = result.message;
      msgEl.style.color = "#e74c3c";
      msgEl.style.display = "";
    }
  } catch {
    msgEl.textContent = "Σφάλμα επικοινωνίας.";
    msgEl.style.color = "#e74c3c";
    msgEl.style.display = "";
  } finally {
    btn.disabled = false;
  }
});

// ── History Modal ─────────────────────────────────────────────

async function openHistoryModal(athleteId, name) {
  _currentAthleteId = athleteId;
  document.getElementById("historyAthleteName").textContent = name;
  document.getElementById("historySummaryLine").textContent = "Φόρτωση...";
  document.getElementById("historyBalanceStrip").innerHTML = "";
  document.getElementById("historyPaymentsList").innerHTML =
    '<p class="loading-msg">Φόρτωση...</p>';
  document.getElementById("historyAttendanceList").innerHTML =
    '<p class="loading-msg">Φόρτωση...</p>';
  document.getElementById("athleteHistoryModal").style.display = "flex";

  try {
    const res = await fetch("get_athlete_history.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ athlete_id: athleteId }),
    });
    const result = await res.json();
    if (result.status !== "success") {
      alert(result.message);
      return;
    }

    const b = result.balance || {};
    const rem = parseInt(b.lessons_remaining || 0);
    const cls =
      rem > 0
        ? "balance-positive"
        : rem < 0
          ? "balance-negative"
          : "balance-zero";

    document.getElementById("historySummaryLine").textContent =
      (b.location_name || "") +
      (b.location_name ? "  ·  " : "") +
      "Υπόλοιπο: " +
      (rem > 0 ? "+" : "") +
      rem +
      " μαθήματα";

    document.getElementById("historyBalanceStrip").innerHTML = `
      <div class="hbs-item"><span class="hbs-label">Αγοράστηκαν</span><strong>${b.lessons_purchased || 0}</strong></div>
      <div class="hbs-item"><span class="hbs-label">Χρησιμοποιήθηκαν</span><strong>${b.lessons_used || 0}</strong></div>
      <div class="hbs-item"><span class="hbs-label">Υπόλοιπο</span><strong class="${cls}">${rem > 0 ? "+" : ""}${rem}</strong></div>
      <div class="hbs-item"><span class="hbs-label">Σύνολο πληρωμών</span><strong>${Number(b.total_paid || 0).toLocaleString("el-GR", { minimumFractionDigits: 2 })} €</strong></div>
    `;

    _renderPaymentsList(result.payments, athleteId, name);
    _renderAttendanceList(result.attendance);
  } catch {
    document.getElementById("historyPaymentsList").innerHTML =
      '<p style="color:red">Σφάλμα.</p>';
  }
}

function _renderPaymentsList(payments, athleteId, name) {
  const el = document.getElementById("historyPaymentsList");
  if (!payments.length) {
    el.innerHTML = '<p class="empty-history">Καμία πληρωμή ακόμα.</p>';
    return;
  }

  const typeLabels = { prepaid: "Προπληρωμή", free: "Δωρεάν", gift: "Δώρο" };
  const methodLabels = {
    cash: "Μετρητά",
    card: "Κάρτα",
    transfer: "Τράπεζα",
    other: "Άλλο",
  };

  el.innerHTML = payments
    .map((p) => {
      const dt = new Date(p.payment_date).toLocaleDateString("el-GR", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
      });
      const typeL = typeLabels[p.payment_type] || p.payment_type;
      const methodL = methodLabels[p.payment_method] || p.payment_method;
      const isFree = p.payment_type !== "prepaid";
      return `
      <div class="history-pay-row" id="hpr-${p.id}">
        <div class="hpr-top">
          <span class="hpr-lessons">+${p.lessons_purchased} μαθήματα</span>
          <span class="hpr-amount">${isFree ? "Δωρεάν" : Number(p.amount).toFixed(2) + " €"}</span>
        </div>
        <div class="hpr-meta">
          <span>📅 ${dt}</span>
          <span>${escHtml(typeL)} · ${escHtml(methodL)}</span>
          ${p.notes ? `<span>💬 ${escHtml(p.notes)}</span>` : ""}
        </div>
        <div class="hpr-actions">
          <button class="hpr-btn hpr-receipt"
                  onclick="window.open('generate_receipt.php?id=${p.id}','_blank')">
            🧾 Απόδειξη
          </button>
          <button class="hpr-btn hpr-delete"
                  onclick="deletePayment(${p.id}, ${athleteId}, '${escAttr(name)}')">
            🗑️
          </button>
        </div>
      </div>
    `;
    })
    .join("");
}

function _renderAttendanceList(attendance) {
  const el = document.getElementById("historyAttendanceList");
  if (!attendance.length) {
    el.innerHTML = '<p class="empty-history">Καμία παρουσία ακόμα.</p>';
    return;
  }

  const typeIcons = {
    rollers: "🛼",
    iceskate: "⛸️",
    hockey: "🏒",
    ski: "⛷️",
    fitness: "🏋️",
  };

  el.innerHTML = attendance
    .map((a) => {
      const dt = new Date(a.lesson_datetime).toLocaleDateString("el-GR", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
      });
      const time = new Date(a.lesson_datetime).toLocaleTimeString("el-GR", {
        hour: "2-digit",
        minute: "2-digit",
      });
      const icon = typeIcons[a.lesson_type] || "📋";
      return `
      <div class="history-att-row">
        <span class="hat-icon">${icon}</span>
        <div class="hat-info">
          <span class="hat-date">${dt}  ${time}</span>
          ${a.location_name ? `<span class="hat-loc">📍 ${escHtml(a.location_name)}</span>` : ""}
        </div>
      </div>
    `;
    })
    .join("");
}

function closeHistoryModal() {
  document.getElementById("athleteHistoryModal").style.display = "none";
  _currentAthleteId = null;
}

// ── Delete Payment ────────────────────────────────────────────

async function deletePayment(paymentId, athleteId, name) {
  if (!confirm("Διαγραφή πληρωμής; Το υπόλοιπο θα ενημερωθεί αυτόματα."))
    return;

  const row = document.getElementById("hpr-" + paymentId);
  if (row) row.style.opacity = "0.4";

  try {
    const res = await fetch("delete_payment.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ payment_id: paymentId }),
    });
    const result = await res.json();
    if (result.status === "success") {
      openHistoryModal(athleteId, name);
      refreshFinanceTab();
    } else {
      if (row) row.style.opacity = "1";
      alert(result.message);
    }
  } catch {
    if (row) row.style.opacity = "1";
    alert("Σφάλμα επικοινωνίας.");
  }
}

// ── Monthly Report Modal ──────────────────────────────────────

async function openMonthlyReportModal() {
  document.getElementById("monthlyReportContent").innerHTML =
    '<p class="loading-msg">Φόρτωση...</p>';
  document.getElementById("monthlyReportModal").style.display = "flex";

  try {
    const res = await fetch("get_monthly_report.php");
    const result = await res.json();
    if (result.status !== "success") {
      alert(result.message);
      return;
    }
    _renderMonthlyReport(result.months, result.total);
  } catch {
    document.getElementById("monthlyReportContent").innerHTML =
      '<p style="color:red">Σφάλμα φόρτωσης.</p>';
  }
}

function closeMonthlyReportModal() {
  document.getElementById("monthlyReportModal").style.display = "none";
}

function _renderMonthlyReport(months, total) {
  const el = document.getElementById("monthlyReportContent");
  if (!months.length) {
    el.innerHTML = '<p class="empty-history">Δεν υπάρχουν δεδομένα.</p>';
    return;
  }

  const rows = months
    .map(
      (m) => `
    <tr>
      <td>${escHtml(m.month_label)}</td>
      <td class="report-num">${Number(m.revenue).toLocaleString("el-GR", { minimumFractionDigits: 2 })} €</td>
      <td class="report-num">${m.lessons_sold}</td>
      <td class="report-num">${m.athletes_paying}</td>
      <td class="report-num">${m.payments_count}</td>
    </tr>
  `,
    )
    .join("");

  const totalRev = Number(total.total_revenue).toLocaleString("el-GR", {
    minimumFractionDigits: 2,
  });

  el.innerHTML = `
    <table class="report-table">
      <thead>
        <tr>
          <th>Μήνας</th>
          <th>Εισπράξεις</th>
          <th>Μαθήματα</th>
          <th>Αθλητές</th>
          <th>Πληρωμές</th>
        </tr>
      </thead>
      <tbody>${rows}</tbody>
      <tfoot>
        <tr>
          <td><strong>Σύνολο 12μ.</strong></td>
          <td class="report-num"><strong>${totalRev} €</strong></td>
          <td class="report-num"><strong>${total.total_lessons}</strong></td>
          <td></td><td></td>
        </tr>
      </tfoot>
    </table>
  `;
}

// ── Helpers ───────────────────────────────────────────────────

function escHtml(str) {
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}
function escAttr(str) {
  return String(str).replace(/'/g, "\\'");
}

// ── Init ──────────────────────────────────────────────────────

document.addEventListener("DOMContentLoaded", () => {
  refreshFinanceTab();

  const pmModal = document.getElementById("paymentModal");
  if (pmModal)
    pmModal.addEventListener("click", (e) => {
      if (e.target === pmModal) closePaymentModal();
    });

  const hModal = document.getElementById("athleteHistoryModal");
  if (hModal)
    hModal.addEventListener("click", (e) => {
      if (e.target === hModal) closeHistoryModal();
    });

  const rModal = document.getElementById("monthlyReportModal");
  if (rModal)
    rModal.addEventListener("click", (e) => {
      if (e.target === rModal) closeMonthlyReportModal();
    });
});
