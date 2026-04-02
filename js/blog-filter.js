document.addEventListener("DOMContentLoaded", function () {
  const catButtons = document.querySelectorAll(".cat-filter");
  const searchInput = document.getElementById("blogSearch");
  const cards = Array.from(document.querySelectorAll(".card"));
  const cardsPerPage = 9;
  let currentPage = 1;
  let activeCategory = "all";

  /**
   * Κύρια συνάρτηση φιλτραρίσματος
   */
  function applyFilters() {
    const q = (searchInput?.value || "").trim().toLowerCase();

    cards.forEach((card) => {
      const cats = (card.dataset.cats || "").split("|").filter(Boolean);
      const title = (card.dataset.title || "").toLowerCase();

      const matchesCategory =
        activeCategory === "all" || cats.includes(activeCategory);
      const matchesSearch = q === "" || title.includes(q);

      // Εδώ απλά σημειώνουμε ποιες κάρτες "πληρούν" τα κριτήρια
      // πριν το pagination τις κρύψει/εμφανίσει ανά σελίδα
      if (matchesCategory && matchesSearch) {
        card.setAttribute("data-visible", "true");
      } else {
        card.setAttribute("data-visible", "false");
        card.style.display = "none";
      }
    });

    // Κάθε φορά που φιλτράρουμε, επιστρέφουμε στην 1η σελίδα
    currentPage = 1;
    updatePagination();
  }

  /**
   * Δημιουργία των κουμπιών της σελιδοποίησης
   */
  function updatePagination() {
    // Παίρνουμε μόνο όσες κάρτες πέρασαν το φίλτρο
    const visibleCards = cards.filter(
      (card) => card.getAttribute("data-visible") === "true",
    );
    const totalPages = Math.ceil(visibleCards.length / cardsPerPage);
    const paginationContainer = document.getElementById("blogPagination");

    if (!paginationContainer) return;
    paginationContainer.innerHTML = "";

    // Αν δεν υπάρχουν κάρτες ή είναι μόνο 1 σελίδα, σταματάμε
    if (totalPages <= 1) {
      // Αν είναι 1 σελίδα, απλά δείχνουμε όλες τις visible κάρτες
      visibleCards.forEach((card) => (card.style.display = ""));
      return;
    }

    for (let i = 1; i <= totalPages; i++) {
      const btn = document.createElement("button");
      btn.innerText = i;
      if (i === currentPage) btn.classList.add("active");

      btn.addEventListener("click", () => {
        currentPage = i;
        showPage(i, visibleCards);
        // Scroll up στην αρχή της λίστας για καλύτερο UX
        document
          .querySelector(".blog-list")
          .scrollIntoView({ behavior: "smooth" });
      });

      paginationContainer.appendChild(btn);
    }

    showPage(currentPage, visibleCards);
  }

  /**
   * Εμφάνιση συγκεκριμένης σελίδας
   */
  function showPage(page, visibleCards) {
    const start = (page - 1) * cardsPerPage;
    const end = start + cardsPerPage;

    // Κρύβουμε τα πάντα και δείχνουμε μόνο το εύρος της σελίδας
    cards.forEach((card) => (card.style.display = "none"));

    visibleCards.forEach((card, index) => {
      if (index >= start && index < end) {
        card.style.display = ""; // Εμφάνιση (χρησιμοποιεί το default του CSS π.χ. block/flex)
      }
    });

    // Ενημέρωση του active κλάσης στα κουμπιά
    const buttons = document.querySelectorAll(".pagination-controls button");
    buttons.forEach((btn, idx) => {
      btn.classList.toggle("active", idx + 1 === page);
    });
  }

  // --- Event Listeners ---

  catButtons.forEach((btn) => {
    btn.addEventListener("click", function () {
      catButtons.forEach((b) => b.classList.remove("active"));
      this.classList.add("active");
      activeCategory = this.dataset.slug || "all";
      applyFilters();
    });
  });

  if (searchInput) {
    searchInput.addEventListener("input", function () {
      applyFilters();
    });
  }

  // Αρχική εκτέλεση
  applyFilters();
});
