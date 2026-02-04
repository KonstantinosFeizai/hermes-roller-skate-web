/* ================================================================
   DOM ELEMENT REFERENCES
   ================================================================
   Get references to all the DOM elements we'll be working with.
   This is done once at the start for better performance.
================================================================ */

// Main navbar element - used for hide/show on scroll
const navbar = document.getElementById("navbar");

// Hamburger menu button and mobile menu container
const hamburgerBtn = document.getElementById("hamburger-button");
const mobileMenu = document.getElementById("mobile-menu");

// Language selector dropdown menu
const languageDropdown = document.getElementById("language-dropdown");

// Body element - used to prevent scrolling when mobile menu is open
const body = document.body;

// User menu button and dropdown (for logged-in users)
// These might not exist if user is not logged in, hence the conditional checks later
const userMenuButton = document.getElementById("user-menu-button");
const userMenuDropdown = document.getElementById("user-menu-dropdown-content");

// Login modal (if present)
const loginModal = document.getElementById("loginModal");

// Helper to close mobile menu safely
const closeMobileMenu = () => {
  if (mobileMenu && mobileMenu.classList.contains("active")) {
    mobileMenu.classList.remove("active");
    if (hamburgerBtn) {
      hamburgerBtn.classList.remove("active");
    }
    body.classList.remove("no-scroll");
  }
};

/* ================================================================
   HAMBURGER MENU FUNCTIONALITY
   ================================================================
   Handles the mobile hamburger menu click event.
   - Toggles the mobile menu visibility
   - Animates the hamburger icon
   - Prevents body scrolling when menu is open
   - Ensures language dropdown closes when hamburger opens
================================================================ */
hamburgerBtn.addEventListener("click", () => {
  // Toggle 'active' class on mobile menu to show/hide it
  mobileMenu.classList.toggle("active");

  // Toggle 'active' class on hamburger button for animation (X shape)
  hamburgerBtn.classList.toggle("active");

  // Toggle 'no-scroll' class on body to prevent background scrolling
  body.classList.toggle("no-scroll");

  // Close language dropdown if it's open (mutual exclusivity)
  if (languageDropdown.classList.contains("show")) {
    languageDropdown.classList.remove("show");
  }

  // Close login modal if it's open
  if (loginModal && loginModal.style.display === "flex") {
    loginModal.style.display = "none";
    body.classList.remove("modal-open");
  }
});

/* ================================================================
   LANGUAGE DROPDOWN FUNCTIONALITY
   ================================================================
   Handles the language selector button click event.
   - Toggles the language dropdown visibility
   - Ensures other menus close when this opens (mutual exclusivity)
   - Prevents event bubbling to parent elements
================================================================ */
const languageButton = document.getElementById("language-button");

languageButton.addEventListener("click", (event) => {
  // Prevent the click event from bubbling up to parent elements
  // This ensures the "click outside" listener doesn't immediately close the dropdown
  event.stopPropagation();

  // Toggle the visibility of the language dropdown
  languageDropdown.classList.toggle("show");

  // If mobile menu is open, close it (mutual exclusivity)
  if (mobileMenu.classList.contains("active")) {
    closeMobileMenu();
  }

  // If user menu dropdown is open, close it (mutual exclusivity)
  // Check if elements exist first (they won't if user is not logged in)
  if (userMenuDropdown && userMenuDropdown.classList.contains("show")) {
    userMenuDropdown.classList.remove("show");
  }
});

/* ================================================================
   CLOSE LANGUAGE DROPDOWN ON OUTSIDE CLICK
   ================================================================
   Listens for clicks anywhere on the document.
   If the click is outside the language button and dropdown,
   the dropdown will close. This provides intuitive UX.
================================================================ */
document.addEventListener("click", (event) => {
  // Check if the click target is NOT inside the language button or dropdown
  if (
    !languageButton.contains(event.target) &&
    !languageDropdown.contains(event.target)
  ) {
    // Click was outside, so close the dropdown
    languageDropdown.classList.remove("show");
  }
});

/* ================================================================
   SCROLL-BASED NAVBAR HIDE/SHOW BEHAVIOR
   ================================================================
   Implements smart navbar behavior based on scroll direction:
   - Hides navbar when scrolling DOWN (past 70px)
   - Shows navbar when scrolling UP
   - Closes all dropdowns when scrolling (better UX)
   
   This creates a cleaner reading experience while keeping
   navigation easily accessible.
================================================================ */

// Store the last scroll position to compare with current position
let lastScrollY = window.scrollY;

window.addEventListener("scroll", () => {
  // Get current scroll position
  const currentScrollY = window.scrollY;

  // Close language dropdown if it's open (improves UX when scrolling)
  if (languageDropdown.classList.contains("show")) {
    languageDropdown.classList.remove("show");
  }

  // Close user menu dropdown if it's open and it exists
  if (userMenuDropdown && userMenuDropdown.classList.contains("show")) {
    userMenuDropdown.classList.remove("show");
  }

  // Check scroll direction and position
  if (currentScrollY > lastScrollY && currentScrollY > 70) {
    // Scrolling DOWN and past the navbar height (70px)
    // Hide the navbar by adding the 'navbar--hidden' class
    navbar.classList.add("navbar--hidden");
  } else {
    // Scrolling UP or at the very top of the page
    // Show the navbar by removing the 'navbar--hidden' class
    navbar.classList.remove("navbar--hidden");
  }

  // Update last scroll position for next comparison
  lastScrollY = currentScrollY;
});

/* ================================================================
   USER MENU DROPDOWN FUNCTIONALITY
   ================================================================
   Handles the user menu dropdown for logged-in users.
   These elements only exist when a user is logged in, so we
   check for their existence before adding event listeners.
   
   Features:
   - Toggles user dropdown on button click
   - Ensures language dropdown closes when user menu opens
   - Closes dropdown when clicking outside
================================================================ */

// Check if user menu elements exist (they won't if user is not logged in)
if (userMenuButton && userMenuDropdown) {
  // Handle user menu button click
  userMenuButton.addEventListener("click", (event) => {
    // Prevent the click event from bubbling up to parent elements
    // This prevents the "click outside" listener from immediately closing the dropdown
    event.stopPropagation();

    // Toggle the visibility of the user menu dropdown
    // (Ανοίγει/Κλείνει το dropdown με την κλάση 'show')
    userMenuDropdown.classList.toggle("show");

    // If mobile menu is open, close it (mutual exclusivity)
    closeMobileMenu();

    // If language dropdown is open, close it (mutual exclusivity)
    if (languageDropdown.classList.contains("show")) {
      languageDropdown.classList.remove("show");
    }
  });

  /* ================================================================
     CLOSE USER DROPDOWN ON OUTSIDE CLICK
     ================================================================
     Listens for clicks anywhere on the window.
     If the click is NOT on the user menu button, close the dropdown.
     This provides intuitive UX - clicking outside closes the menu.
  ================================================================ */
  window.addEventListener("click", (event) => {
    // Check if the clicked element is NOT the user menu button
    if (!event.target.matches("#user-menu-button")) {
      // If dropdown is currently open, close it
      if (userMenuDropdown.classList.contains("show")) {
        userMenuDropdown.classList.remove("show");
      }
    }
  });
}
