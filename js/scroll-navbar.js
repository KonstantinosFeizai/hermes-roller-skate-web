// Get a reference to the navbar element
const navbar = document.getElementById('navbar');
// Get a reference to the hamburger and mobile menu elements
const hamburgerBtn = document.getElementById('hamburger-button');
const mobileMenu = document.getElementById('mobile-menu');
const languageDropdown = document.getElementById('language-dropdown');
const body = document.body;

// Functionality for the hamburger menu
hamburgerBtn.addEventListener('click', () => {
    mobileMenu.classList.toggle('active')
    hamburgerBtn.classList.toggle('active');
    body.classList.toggle('no-scroll');

    // Close language dropdown if open
    if (languageDropdown.classList.contains('show')) {
        languageDropdown.classList.remove('show');
    }
});

// Functionality for the language dropdown
const languageButton = document.getElementById('language-button');

languageButton.addEventListener('click', (event) => {
    event.stopPropagation();
    languageDropdown.classList.toggle('show');

    // Close mobile menu if open
    if (mobileMenu.classList.contains('active')) {
        mobileMenu.classList.remove('active');
        hamburgerBtn.classList.remove('active');
        body.classList.remove('no-scroll');
    }
});

// Functionality for closing the language dropdown when clicking outside
document.addEventListener('click', (event) => {
    if (!languageButton.contains(event.target) && !languageDropdown.contains(event.target)) {
        languageDropdown.classList.remove('show');
    }
});

// Functionality for the scroll-based navbar behavior
let lastScrollY = window.scrollY;

window.addEventListener('scroll', () => {

    const currentScrollY = window.scrollY;
    // Close language dropdown on scroll
    if (languageDropdown.classList.contains('show')) {
        languageDropdown.classList.remove('show');
    }
    if (currentScrollY > lastScrollY && currentScrollY > 70) {
        // Scrolling DOWN
        navbar.classList.add('navbar--hidden');
    } else {
        // Scrolling UP or at the very top
        navbar.classList.remove('navbar--hidden');
    }

    lastScrollY = currentScrollY;
});
