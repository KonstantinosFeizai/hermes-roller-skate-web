/* ── schedule.js ──────────────────────────────────────────────────────────────
   Seasonal schedule grid: data, card rendering, and season-toggle UI.
   Requires window.SCHED_LABELS to be defined (via inline <script>) before
   this deferred script runs.
   ────────────────────────────────────────────────────────────────────────── */

(function () {
  "use strict";

  const L = window.SCHED_LABELS || {};

  /* ── i18n helpers ────────────────────────────────────────────────────── */
  // Applied after data is defined – maps hardcoded English strings to L.*
  function localiseScheduleData(datasets) {
    const dayMap = {
      "Saturday & Sunday": L.satSun || "Saturday & Sunday",
      Saturday: L.saturday || "Saturday",
      Sunday: L.sunday || "Sunday",
      Tuesday: L.tuesday || "Tuesday",
      Wednesday: L.wednesday || "Wednesday",
    };
    const subMap = {
      Panepistimioupoli: L.subUniversity || "Panepistimioupoli",
      Polytexneioupoli: L.subPolytechnic || "Polytexneioupoli",
    };
    const locMap = {
      Zografou: L.locZografou || "Zografou",
      "OAKA / Marousi": L.locOaka || "OAKA / Marousi",
      Gerakas: L.locGerakas || "Gerakas",
      Egaleo: L.locEgaleo || "Egaleo",
      Vrilissia: L.locVrilissia || "Vrilissia",
      Megalopolis: L.locMegalopolis || "Megalopolis",
      Kalamata: L.locKalamata || "Kalamata",
      Ilioupoli: L.locIlioupoli || "Ilioupoli",
    };
    datasets.forEach(function (set) {
      set.forEach(function (loc) {
        if (locMap[loc.location]) {
          loc.location = locMap[loc.location];
        }
        if (subMap[loc.sub]) {
          loc.sub = subMap[loc.sub];
        }
        loc.days.forEach(function (d) {
          if (dayMap[d.label]) {
            d.label = dayMap[d.label];
          }
        });
      });
    });
  }

  /* ── Schedule data ────────────────────────────────────────────────────── */

  const WINTER = [
    {
      location: "Zografou",
      sub: "Panepistimioupoli",
      maps: "https://maps.app.goo.gl/4ifyZcivRadWFjKd6",
      days: [
        {
          label: "Saturday & Sunday",
          classes: [
            { time: "09:30–10:30", level: "l1", text: L.basic },
            { time: "10:30–11:30", level: "l2", text: L.advanced },
            { time: "11:30–12:30", level: "l3", text: L.beginners },
            { time: "16:00–17:00", level: "l1", text: L.basic },
            { time: "17:00–18:00", level: "l4", text: L.mixed },
          ],
        },
      ],
    },
    {
      location: "Zografou",
      sub: "Polytexneioupoli",
      maps: "https://maps.app.goo.gl/TSKAVESD4kXuv8ZU7",
      days: [
        {
          label: "Saturday",
          classes: [{ time: "18:30–19:30", level: "l4", text: L.mixed }],
        },
      ],
    },
    {
      location: "Gerakas",
      sub: "",
      maps: "https://maps.app.goo.gl/Hdjvv418PZGE3nQU8",
      days: [
        {
          label: "Saturday",
          classes: [{ time: "14:00–15:00", level: "l4", text: L.mixed }],
        },
      ],
    },
    {
      location: "Vrilissia",
      sub: "",
      maps: "https://maps.app.goo.gl/DTtnqGaxJqMfTDA28",
      days: [
        {
          label: "Sunday",
          classes: [{ time: "14:00–15:00", level: "l4", text: L.mixed }],
        },
      ],
    },
    {
      location: "Megalopolis",
      sub: "",
      maps: "https://maps.app.goo.gl/glgYHxkCwQp5NURGv78",
      days: [
        {
          label: "Tuesday",
          classes: [
            { time: "16:30–17:30", level: "l1", text: L.basic },
            { time: "17:30–18:30", level: "l2", text: L.advanced },
            { time: "18:30–19:30", level: "l4", text: L.mixed },
          ],
        },
      ],
    },
    {
      location: "Kalamata",
      sub: "",
      maps: "https://maps.app.goo.gl/AbqNkvtueDurwayW8",
      days: [
        {
          label: "Wednesday",
          classes: [
            { time: "16:30–17:30", level: "l1", text: L.basic },
            { time: "17:30–18:30", level: "l2", text: L.advanced },
            { time: "18:30–19:30", level: "l4", text: L.mixed },
          ],
        },
      ],
    },
    {
      location: "Egaleo",
      sub: "",
      maps: "https://maps.app.goo.gl/wqwgA6past6bn7137",
      days: [
        {
          label: "Wednesday",
          classes: [
            { time: "16:00–17:00", level: "l1", text: L.basic },
            { time: "17:00–18:00", level: "l2", text: L.advanced },
          ],
        },
      ],
    },
    {
      location: "OAKA / Marousi",
      sub: "",
      maps: "https://maps.app.goo.gl/BtULHH8qoyCsTo3v9",
      days: [
        {
          label: "Sunday",
          classes: [
            { time: "10:00–11:00", level: "l1", text: L.basic },
            { time: "11:00–12:00", level: "l2", text: L.advanced },
          ],
        },
      ],
    },
  ];

  const SUMMER = [
    {
      location: "Zografou",
      sub: "Panepistimioupoli",
      maps: "https://maps.app.goo.gl/4ifyZcivRadWFjKd6",
      days: [
        {
          label: "Saturday",
          classes: [
            { time: "09:45–10:45", level: "l2", text: L.advanced },
            { time: "10:45–11:45", level: "l3", text: L.beginners },
            { time: "11:45–12:45", level: "l1", text: L.basic },
            { time: "16:45–17:45", level: "l2", text: L.advanced },
            { time: "17:45–18:45", level: "l3", text: L.beginners },
          ],
        },
        {
          label: "Sunday",
          classes: [
            { time: "10:45–11:45", level: "l3", text: L.beginners },
            { time: "11:45–12:45", level: "l2", text: L.advanced },
            { time: "12:45–13:45", level: "l1", text: L.basic },
            { time: "16:45–17:45", level: "l2", text: L.advanced },
            { time: "17:45–18:45", level: "l3", text: L.beginners },
            { time: "18:45–19:45", level: "l4", text: L.mixed },
          ],
        },
      ],
    },
    {
      location: "Zografou",
      sub: "Polytexneioupoli",
      maps: "https://maps.app.goo.gl/TSKAVESD4kXuv8ZU7",
      days: [
        {
          label: "Saturday",
          classes: [{ time: "19:15–20:15", level: "l4", text: L.mixed }],
        },
      ],
    },
    {
      location: "Vrilissia",
      sub: "",
      maps: "https://maps.app.goo.gl/DTtnqGaxJqMfTDA28",
      days: [
        {
          label: "Sunday",
          classes: [{ time: "09:15–10:15", level: "l4", text: L.mixed }],
        },
      ],
    },
    {
      location: "Gerakas",
      sub: "",
      maps: "https://maps.app.goo.gl/Hdjvv418PZGE3nQU8",
      days: [
        {
          label: "Tuesday",
          classes: [{ time: "19:30–20:30", level: "l4", text: L.mixed }],
        },
      ],
    },
    {
      location: "Ilioupoli",
      sub: "",
      maps: "https://maps.app.goo.gl/R256iyDq5d4NWMsu9",
      days: [
        {
          label: "Tuesday",
          classes: [{ time: "18:00–19:00", level: "l4", text: L.mixed }],
        },
      ],
    },
    {
      location: "Egaleo",
      sub: "",
      maps: "https://maps.app.goo.gl/wqwgA6past6bn7137",
      days: [
        {
          label: "Wednesday",
          classes: [
            { time: "16:00–17:00", level: "l1", text: L.basic },
            { time: "17:00–18:00", level: "l2", text: L.advanced },
          ],
        },
      ],
    },
    {
      location: "OAKA / Marousi",
      sub: "",
      maps: "https://maps.app.goo.gl/BtULHH8qoyCsTo3v9",
      days: [
        {
          label: "Sunday",
          classes: [
            { time: "10:00–11:00", level: "l1", text: L.basic },
            { time: "11:00–12:00", level: "l2", text: L.advanced },
          ],
        },
      ],
    },
    {
      location: "Megalopolis",
      sub: "",
      maps: "https://maps.app.goo.gl/glgYHxkCwQp5NURGv78",
      days: [
        {
          label: "Tuesday",
          classes: [
            { time: "16:30–17:30", level: "l1", text: L.basic },
            { time: "17:30–18:30", level: "l2", text: L.advanced },
            { time: "18:30–19:30", level: "l4", text: L.mixed },
          ],
        },
      ],
    },
    {
      location: "Kalamata",
      sub: "",
      maps: "https://maps.app.goo.gl/AbqNkvtueDurwayW8",
      days: [
        {
          label: "Wednesday",
          classes: [
            { time: "16:30–17:30", level: "l1", text: L.basic },
            { time: "17:30–18:30", level: "l2", text: L.advanced },
            { time: "18:30–19:30", level: "l4", text: L.mixed },
          ],
        },
      ],
    },
  ];

  /* ── Apply i18n to data arrays ───────────────────────────────────────── */
  localiseScheduleData([WINTER, SUMMER]);

  /* ── Season detection ─────────────────────────────────────────────────── */
  /* Winter: 24 Oct → 12 May  |  Summer: 13 May → 23 Oct                    */
  function isSummerNow() {
    const d = new Date(),
      m = d.getMonth() + 1,
      day = d.getDate();
    if (m > 5 && m < 10) return true;
    if (m === 5 && day >= 13) return true;
    if (m === 10 && day <= 23) return true;
    return false;
  }

  /* ── SVG icons ────────────────────────────────────────────────────────── */
  const ICON_WINTER = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <line x1="12" y1="2" x2="12" y2="22"/>
        <path d="m2 12 10-5 10 5-10 5z"/>
        <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
        <line x1="4.93" y1="19.07" x2="19.07" y2="4.93"/>
    </svg>`;

  const ICON_SUMMER = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <circle cx="12" cy="12" r="5"/>
        <line x1="12" y1="1" x2="12" y2="3"/>
        <line x1="12" y1="21" x2="12" y2="23"/>
        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
        <line x1="1" y1="12" x2="3" y2="12"/>
        <line x1="21" y1="12" x2="23" y2="12"/>
        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
    </svg>`;

  const PIN_SVG = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
        <circle cx="12" cy="10" r="3"/>
    </svg>`;

  /* ── Card builder ─────────────────────────────────────────────────────── */
  function buildCard(loc) {
    const mapsEl = loc.maps
      ? `<a class="sched-maps-link" href="${loc.maps}" target="_blank" rel="noopener noreferrer">
                ${PIN_SVG} ${L.mapsLabel}
               </a>`
      : `<span class="sched-maps-link" style="opacity:.45">${L.locationTbc}</span>`;

    const dayBlocks = loc.days
      .map((day) => {
        const rows = day.classes
          .map(
            (c) =>
              `<div class="sched-class-row">
                    <span class="sched-time">${c.time}</span>
                    <span class="sched-badge sched-badge-${c.level}">${c.text}</span>
                </div>`,
          )
          .join("");

        return `<div class="sched-day-block">
                ${loc.days.length > 1 ? `<div class="sched-day-block-label">${day.label}</div>` : ""}
                ${rows}
            </div>`;
      })
      .join("");

    const headerDay =
      loc.days.length === 1
        ? loc.days[0].label
        : L.multipleDays || "Multiple Days";

    return `<article class="sched-card">
            <div class="sched-card-head">
                <span class="sched-card-day">${headerDay}</span>
                <a class="sched-card-location" href="${loc.maps || "#"}" target="_blank" rel="noopener noreferrer">
                    ${loc.location}
                </a>
                ${loc.sub ? `<span class="sched-card-sub">${loc.sub}</span>` : ""}
            </div>
            <hr class="sched-card-divider">
            <div class="sched-classes">${dayBlocks}</div>
            ${mapsEl}
        </article>`;
  }

  function renderGrid(data, panelEl) {
    panelEl.innerHTML = `<div class="schedule-grid">${data.map(buildCard).join("")}</div>`;
  }

  /* ── UI setup ─────────────────────────────────────────────────────────── */
  const panelWinter = document.getElementById("panel-winter");
  const panelSummer = document.getElementById("panel-summer");
  const badge = document.getElementById("schedule-season-badge");
  const subtitle = document.getElementById("schedule-season-subtitle");
  const toggleBtn = document.getElementById("sched-toggle-btn");
  const toggleLabel = document.getElementById("sched-toggle-label");
  const root = document.getElementById("schedule-root");

  if (!panelWinter || !panelSummer) return;

  renderGrid(WINTER, panelWinter);
  renderGrid(SUMMER, panelSummer);

  const currentSeason = isSummerNow() ? "summer" : "winter";
  let viewing = currentSeason;

  function applySeasonUI(season, animate) {
    const isSummer = season === "summer";
    const isCurrentSeason = season === currentSeason;

    badge.innerHTML =
      (isSummer ? ICON_SUMMER : ICON_WINTER) +
      `<span>${isSummer ? L.summerBadge : L.winterBadge}</span>`;
    badge.style.background = isSummer
      ? "var(--sched-accent-s)"
      : "var(--sched-accent-w)";

    subtitle.textContent = isSummer ? L.summerRange : L.winterRange;

    toggleLabel.textContent = isCurrentSeason
      ? isSummer
        ? L.previewWin
        : L.previewSum
      : L.returnCur;
    toggleBtn.setAttribute("aria-pressed", String(!isCurrentSeason));

    root.classList.toggle("summer-active", isSummer);

    const showPanel = isSummer ? panelSummer : panelWinter;
    const hidePanel = isSummer ? panelWinter : panelSummer;

    hidePanel.classList.add("hidden");
    hidePanel.classList.remove("fade-in");
    showPanel.classList.remove("hidden");

    if (animate) {
      showPanel.classList.remove("fade-in");
      void showPanel.offsetWidth; /* force reflow */
      showPanel.classList.add("fade-in");
    }
  }

  applySeasonUI(currentSeason, false);

  toggleBtn.addEventListener("click", () => {
    viewing = viewing === "winter" ? "summer" : "winter";
    toggleBtn.classList.toggle("spin");
    setTimeout(() => toggleBtn.classList.remove("spin"), 400);
    applySeasonUI(viewing, true);
  });
})();
