// js/finance.js
// Finance tab: athlete balance cards, add payment, history modal, receipts.

// ── State ─────────────────────────────────────────────────────
let _allFinanceData = [];
let _currentAthleteId = null;
let _locationFilter = "";
let _financeCurrentPage = 1;
let _financeSearchTimeout = null;
const _financeItemsPerPage = 10;

// ── Load / Refresh ────────────────────────────────────────────

async function refreshFinanceTab() {
  if (!_allFinanceData.length) {
    document.getElementById("financeCardsGrid").innerHTML =
      '<p class="loading-msg">Φόρτωση...</p>';
  }

  await refreshFinanceSummary();
}

async function refreshFinanceSummary() {
  const period =
    document.getElementById("financePeriodFilter")?.value || "current_month";
  const start = document.getElementById("finStartDate")?.value || "";
  const end = document.getElementById("finEndDate")?.value || "";
  const loc = document.getElementById("financeLocationFilter")?.value || "";

  try {
    const url = `get_finance_overview.php?period=${period}&start_date=${start}&end_date=${end}&location=${encodeURIComponent(loc)}`;
    const res = await fetch(url);
    const result = await res.json();
    if (result.status !== "success") {
      alert(result.message);
      return;
    }

    _allFinanceData = result.athletes;
    _populateLocationFilter(result.athletes);

    // Εφαρμογή φίλτρων στις κάρτες ΚΑΙ ενημέρωση του summary bar
    filterFinanceCards(result.summary);
  } catch (err) {
    console.error(err);
  }
}

function onFinancePeriodChange() {
  const period = document.getElementById("financePeriodFilter").value;
  const customRange = document.getElementById("finCustomDateRange");

  if (period === "custom") {
    customRange.style.display = "flex";
  } else {
    customRange.style.display = "none";
    refreshFinanceSummary();
  }
}

function _renderSummary(s, filteredAthletes) {
  // Οι οφειλέτες και τα θετικά υπόλοιπα υπολογίζονται ΔΥΝΑΜΙΚΑ από τους φιλτραρισμένους αθλητές!
  const debt = filteredAthletes.filter(
    (a) => parseInt(a.lessons_remaining) < 0,
  ).length;
  const credit = filteredAthletes.filter(
    (a) => parseInt(a.lessons_remaining) > 0,
  ).length;

  // Εμφάνιση εισπράξεων & τάσης %
  let revHtml =
    Number(s.revenue || 0).toLocaleString("el-GR", {
      minimumFractionDigits: 2,
    }) + " €";
  if (s.trend_percent !== null && s.trend_percent !== undefined) {
    const isUp = s.trend_percent >= 0;
    const sign = isUp ? "+" : "";
    const cls = isUp ? "fin-trend-up" : "fin-trend-down";
    revHtml += ` <span class="fin-trend-badge ${cls}">${sign}${s.trend_percent}%</span>`;
  }

  document.getElementById("finMonthRevenue").innerHTML = revHtml;
  document.getElementById("finMonthLessons").textContent =
    (s.lessons_sold || 0) + " μαθήματα";
  document.getElementById("finDebtCount").textContent = debt;
  document.getElementById("finCreditCount").textContent = credit;
}

function _renderCards(athletes) {
  const grid = document.getElementById("financeCardsGrid");

  if (!athletes.length) {
    grid.innerHTML =
      '<p class="empty-state">Δεν υπάρχουν αθλητές με ιστορικό.</p>';
    _renderPagination([], _financeCurrentPage, _financeItemsPerPage);
    return;
  }

  // Pagination
  const totalPages = Math.ceil(athletes.length / _financeItemsPerPage);
  if (_financeCurrentPage > totalPages && totalPages > 0) {
    _financeCurrentPage = 1;
  }
  const start = (_financeCurrentPage - 1) * _financeItemsPerPage;
  const end = start + _financeItemsPerPage;
  const pageAthletes = athletes.slice(start, end);

  grid.innerHTML = pageAthletes
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
      <div class="fin-card${a.is_active == 0 ? " fin-card--inactive" : ""}" data-athlete-id="${a.athlete_id}">
        <div class="fin-card-top">
          <div class="fin-card-header">
            <div class="fin-card-name">${escHtml(a.athlete_name)}</div>
            <div style="display:flex;gap:6px;align-items:center;">
              ${a.is_active == 0 ? '<span class="fin-card-inactive-badge">Ανενεργός</span>' : ""}
              <span class="fin-card-id">ID: ${a.athlete_id}</span>
            </div>
          </div>
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
          <button class="action-btn btn-success" ${a.is_active == 0 ? 'disabled title="Ανενεργός αθλητής"' : ""}
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

  // Render pagination controls
  _renderPagination(athletes, _financeCurrentPage, _financeItemsPerPage);
}

function _renderPagination(athletes, currentPage, itemsPerPage) {
  const paginationEl = document.getElementById("financePagination");
  if (!paginationEl) return;

  if (!athletes.length) {
    paginationEl.innerHTML = "";
    return;
  }

  const totalPages = Math.ceil(athletes.length / itemsPerPage);
  if (totalPages <= 1) {
    paginationEl.innerHTML = "";
    return;
  }

  let html =
    '<div style="display:flex; gap:8px; justify-content:center; flex-wrap:wrap; margin-top:20px;">';

  // Previous button
  if (currentPage > 1) {
    html += `<button class="action-btn btn-secondary" onclick="_financeGoToPage(${currentPage - 1})">← Προηγ.</button>`;
  }

  // Page numbers
  const startPage = Math.max(1, currentPage - 2);
  const endPage = Math.min(totalPages, currentPage + 2);

  if (startPage > 1) {
    html += `<button class="action-btn" onclick="_financeGoToPage(1)">1</button>`;
    if (startPage > 2) html += '<span style="padding: 8px;">...</span>';
  }

  for (let i = startPage; i <= endPage; i++) {
    if (i === currentPage) {
      html += `<button class="action-btn btn-primary" style="background:#f39c12;color:white;">${i}</button>`;
    } else {
      html += `<button class="action-btn" onclick="_financeGoToPage(${i})">${i}</button>`;
    }
  }

  if (endPage < totalPages) {
    if (endPage < totalPages - 1)
      html += '<span style="padding: 8px;">...</span>';
    html += `<button class="action-btn" onclick="_financeGoToPage(${totalPages})">${totalPages}</button>`;
  }

  // Next button
  if (currentPage < totalPages) {
    html += `<button class="action-btn btn-secondary" onclick="_financeGoToPage(${currentPage + 1})">Επόμ. →</button>`;
  }

  html += "</div>";
  paginationEl.innerHTML = html;
}

function _financeGoToPage(page) {
  _financeCurrentPage = page;
  const filtered = _locationFilter
    ? _allFinanceData.filter((a) => (a.location_name || "") === _locationFilter)
    : _allFinanceData;
  _renderCards(filtered);
}

// ── Search / Filter ───────────────────────────────────────────

// 1. Debounce για την αναζήτηση ονόματος (250ms)
function debouncedFinanceSearch() {
  const input = document.getElementById("financeNameSearch");
  const clearBtn = document.getElementById("clearFinanceSearch");

  // Εμφάνιση / Απόκρυψη του "X"
  if (clearBtn) {
    clearBtn.style.display = input.value.length > 0 ? "block" : "none";
  }

  // Ακύρωση του προηγούμενου timer
  clearTimeout(_financeSearchTimeout);

  // Έναρξη νέου timer 250ms
  _financeSearchTimeout = setTimeout(() => {
    filterFinanceCards();
  }, 250);
}

// 2. Συνάρτηση για καθαρισμό του πεδίου με το "X"
function clearFinanceSearchInput() {
  const input = document.getElementById("financeNameSearch");
  const clearBtn = document.getElementById("clearFinanceSearch");

  if (input) {
    input.value = "";
    if (clearBtn) clearBtn.style.display = "none";
    filterFinanceCards(); // Επαναφορά της πλήρους λίστας
  }
}

// 3. Η βασική συνάρτηση φιλτραρίσματος
function filterFinanceCards(latestSummary = null) {
  _locationFilter = document.getElementById("financeLocationFilter").value;
  const nameSearch = document
    .getElementById("financeNameSearch")
    .value.toLowerCase()
    .trim();

  let filtered = _allFinanceData;

  // Apply location filter
  if (_locationFilter) {
    filtered = filtered.filter(
      (a) => (a.location_name || "") === _locationFilter,
    );
  }

  // Apply name OR ID filter
  if (nameSearch) {
    filtered = filtered.filter((a) => {
      const athleteName = (a.athlete_name || "").toLowerCase();
      const athleteId = String(a.athlete_id || "");

      // Ψάχνει είτε στο όνομα είτε αν ταιριάζει με το ID!
      return athleteName.includes(nameSearch) || athleteId.includes(nameSearch);
    });
  }

  _renderCards(filtered);

  // Ενημέρωση του summary bar δυναμικά
  if (latestSummary) {
    _lastSummary = latestSummary;
  }
  if (_lastSummary) {
    _renderSummary(_lastSummary, filtered);
  }
}

// Κρατάμε το τελευταίο summary στη μνήμη
let _lastSummary = null;

function _populateLocationFilter(athletes) {
  const sel = document.getElementById("financeLocationFilter");
  if (!sel) return;

  const current = sel.value;
  const locations = [
    ...new Set(athletes.map((a) => a.location_name).filter(Boolean)),
  ].sort();

  sel.innerHTML =
    '<option value="">Όλες οι τοποθεσίες</option>' +
    locations
      .map((l) => `<option value="${escAttr(l)}">${escHtml(l)}</option>`)
      .join("");

  if (current && locations.includes(current)) {
    sel.value = current;
  }

  // Όταν αλλάζει η τοποθεσία, ξανακαλούμε το backend για να φέρει τα σωστά ποσά εισπράξεων
  sel.onchange = () => {
    refreshFinanceSummary();
  };
}
// ── Add Payment Modal ─────────────────────────────────────────

function openPaymentModal(athleteId, name) {
  _currentAthleteId = athleteId;
  document.getElementById("pf_athlete_id").value = athleteId;
  document.getElementById("paymentAthleteLabel").textContent = name;
  document.getElementById("pf_type").value = "prepaid";
  document.getElementById("pf_method").value = "cash";
  document.getElementById("pf_lessons").value = "4";
  document.getElementById("pf_amount").value = "32";
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

  const idBadge = document.getElementById("historyAthleteId");
  if (idBadge) {
    idBadge.textContent = `ID: ${athleteId}`;
  }

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
      const hasReceipt =
        p.receipt_file_path && p.receipt_file_path.trim() !== "";
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
            📄 Προεπισκόπηση
          </button>
          ${
            hasReceipt
              ? `
            <button class="hpr-btn hpr-receipt-official"
                    onclick="viewOrReplaceReceipt(${p.id})">
              📑 Προβολή/Αντικατάσταση <span class="receipt-checkmark">✅</span>
            </button>
          `
              : `
            <button class="hpr-btn hpr-receipt-upload"
                    onclick="uploadReceipt(${p.id})">
              📤 Ανέβασμα Επίσημης
            </button>
          `
          }
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
  refreshFinanceTab();
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

// ── Upload Receipt ────────────────────────────────────────────

async function uploadReceipt(paymentId) {
  // Create file input element
  const input = document.createElement("input");
  input.type = "file";
  input.accept = "application/pdf";

  input.onchange = async (e) => {
    const file = e.target.files[0];
    if (!file) return;

    // Validate file type
    if (file.type !== "application/pdf") {
      alert("Επιτρέπονται μόνο αρχεία PDF.");
      return;
    }

    // Validate file size (10MB)
    const maxSize = 10 * 1024 * 1024;
    if (file.size > maxSize) {
      alert("Το αρχείο υπερβαίνει τα 10MB.");
      return;
    }

    // Upload the file
    const formData = new FormData();
    formData.append("receipt", file);
    formData.append("payment_id", paymentId);

    try {
      const res = await fetch("../api/upload_receipt.php", {
        method: "POST",
        body: formData,
      });
      const result = await res.json();

      if (result.status === "success") {
        alert("Η απόδειξη ανέβηκε επιτυχώς!");
        // Reload the modal to show updated button
        const modal = document.getElementById("athleteHistoryModal");
        const athleteId = _currentAthleteId;
        const athleteName =
          modal.querySelector("h2")?.textContent?.replace("Ιστορικό: ", "") ||
          "";
        openHistoryModal(athleteId, athleteName);
      } else {
        alert(result.message || "Σφάλμα κατά το ανέβασμα.");
      }
    } catch (err) {
      console.error("Upload error:", err);
      alert("Σφάλμα επικοινωνίας.");
    }
  };

  input.click();
}

// ── View or Replace Receipt ───────────────────────────────────

async function viewOrReplaceReceipt(paymentId) {
  const actions = [
    { label: "Προβολή Απόδειξης", action: "view" },
    { label: "Αντικατάσταση Απόδειξης", action: "replace" },
    { label: "Ακύρωση", action: "cancel" },
  ];

  const choice = prompt(
    "Επιλέξτε ενέργεια:\n" +
      actions.map((a, i) => `${i + 1}. ${a.label}`).join("\n"),
  );

  const index = parseInt(choice) - 1;
  if (index < 0 || index >= actions.length) return;

  const selectedAction = actions[index].action;

  if (selectedAction === "view") {
    window.open(
      `../api/download_receipt.php?payment_id=${paymentId}`,
      "_blank",
    );
  } else if (selectedAction === "replace") {
    uploadReceipt(paymentId);
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
