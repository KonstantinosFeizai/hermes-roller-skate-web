// login-modal.js

// —————————————————————————————
// Element references
// —————————————————————————————
const modal = document.getElementById("loginModal");
const loginForm = document.getElementById("loginForm");
const signupForm = document.getElementById("signupForm");
const signupSuccess = document.getElementById("signupSuccess");

// —————————————————————————————
// Open/close modal + scroll‑lock
// —————————————————————————————
function openModal() {
    loginForm.style.display = "flex";
    signupForm.style.display = "none";
    signupSuccess.style.display = "none";
    modal.style.display = "flex";
    document.body.classList.add("modal-open");
}

function closeModal() {
    modal.style.display = "none";
    document.body.classList.remove("modal-open");
}

// Close on backdrop click
window.addEventListener("click", e => {
    if (e.target === modal) closeModal();
});

// Close on Escape key
window.addEventListener("keydown", e => {
    if (e.key === "Escape" && modal.style.display === "flex") {
        closeModal();
    }
});

// —————————————————————————————
// Toggle between Login & Sign‑Up
// —————————————————————————————
function showSignup() {
    loginForm.style.display = "none";
    signupForm.style.display = "flex";
    signupSuccess.style.display = "none";
}

function showLogin() {
    signupForm.style.display = "none";
    signupSuccess.style.display = "none";
    loginForm.style.display = "flex";
}

// —————————————————————————————
// Validation helpers
// —————————————————————————————
function isValidEmail(email) {
    return /^[^\s@]+@([^\s@.\s]+\.)+[A-Za-z]{2,}$/.test(email);
}

function validateForm(formId) {
    const emailInput = document.getElementById(`${formId}Email`);
    const passInput = document.getElementById(`${formId}Password`);
    const emailError = document.getElementById(`${formId}EmailError`);
    const passError = document.getElementById(`${formId}PasswordError`);
    const submitBtn = document.getElementById(`${formId}Btn`);

    const email = emailInput.value.trim();
    const password = passInput.value.trim();
    const validEmail = isValidEmail(email);
    const validPass = password.length >= 6;

    // Email validation
    if (!validEmail && email !== "") {
        emailInput.classList.add("invalid");
        emailError.textContent = "Invalid email format (e.g. name@example.com)";
        emailError.style.display = "block";
    } else {
        emailInput.classList.remove("invalid");
        emailError.style.display = "none";
    }

    // Password validation
    if (!validPass && password !== "") {
        passInput.classList.add("invalid");
        passError.textContent = "Password must be at least 6 characters";
        passError.style.display = "block";
    } else {
        passInput.classList.remove("invalid");
        passError.style.display = "none";
    }

    submitBtn.disabled = !(validEmail && validPass);
}

// Attach live validation listeners
["login", "signup"].forEach(formId => {
    document.getElementById(`${formId}Email`)
        .addEventListener("input", () => validateForm(formId));
    document.getElementById(`${formId}Password`)
        .addEventListener("input", () => validateForm(formId));
});

// —————————————————————————————
// Form submission handlers
// —————————————————————————————
signupForm.addEventListener("submit", e => {
    e.preventDefault();
    validateForm("signup");
    const btn = document.getElementById("signupBtn");
    const emailInput = document.getElementById("signupEmail");
    const emailErr = document.getElementById("signupEmailError");

    // Καθαρίζουμε προηγούμενα errors
    emailErr.style.display = "none";
    emailInput.classList.remove("invalid");

    if (btn.disabled) return;

    fetch("signup.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
            email: emailInput.value.trim(),
            password: document.getElementById("signupPassword").value.trim()
        })
    })
        .then(res => {
            if (!res.ok && res.status === 409) {
                // Conflict: email already exists
                return res.json().then(err => { throw new Error(err.error); });
            }
            if (!res.ok) {
                throw new Error(`Server error ${res.status}`);
            }
            return res.json();
        })
        .then(data => {
            if (data.success) {
                signupForm.style.display = "none";
                signupSuccess.style.display = "flex";
            }
        })
        .catch(err => {
            // Αν έχουμε μήνυμα "Email already registered", το βγάζουμε inline
            if (err.message === "Email already registered") {
                emailErr.textContent = err.message;
                emailErr.style.display = "block";
                emailInput.classList.add("invalid");
            } else {
                alert(err.message);
            }
            console.error("Signup error:", err);
        });
});

// LOGIN
loginForm.addEventListener("submit", e => {
    e.preventDefault();
    validateForm("login");
    const btn = document.getElementById("loginBtn");
    if (btn.disabled) return;

    fetch("login.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
            email: document.getElementById("loginEmail").value.trim(),
            password: document.getElementById("loginPassword").value.trim()
        })
    })
        .then(res => {
            if (!res.ok) return res.json().then(err => { throw new Error(err.error); });
            return res.json();
        })
        .then(data => {
            closeModal();
            // Προαιρετικά redirect σε dashboard
            // window.location.href = "dashboard.php";
        })
        .catch(err => {
            // Εμφάνιση inline error κάτω από password
            const passInput = document.getElementById("loginPassword");
            const passError = document.getElementById("loginPasswordError");
            passError.textContent = err.message;
            passError.style.display = "block";
            passInput.classList.add("invalid");
        });
});