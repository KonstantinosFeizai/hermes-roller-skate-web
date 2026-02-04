// contact-validation.js
// Purpose: Client-side validation + AJAX submission for the contact form.

// Determine current language from <html lang>
const htmlLang = document.documentElement.lang || "en";
const currentLang = htmlLang.toLowerCase().startsWith("el") ? "el" : "en";

// Field-specific error messages
const errorMessages = {
  el: {
    name: "Το όνομα είναι υποχρεωτικό.",
    surname: "Το επώνυμο είναι υποχρεωτικό.",
    email: "Το email είναι υποχρεωτικό.",
    email_invalid: "Παρακαλώ εισάγετε έγκυρο email.",
    name_length: "Το όνομα πρέπει να έχει 2–50 χαρακτήρες.",
    surname_length: "Το επώνυμο πρέπει να έχει 2–50 χαρακτήρες.",
    category: "Η κατηγορία είναι υποχρεωτική.",
    subject: "Το θέμα είναι υποχρεωτικό.",
    message: "Το μήνυμα είναι υποχρεωτικό.",
    phone: "Παρακαλώ εισάγετε έγκυρο αριθμό τηλεφώνου.",
  },
  en: {
    name: "Name is required.",
    surname: "Surname is required.",
    email: "Email is required.",
    email_invalid: "Please enter a valid email address.",
    name_length: "Name must be 2–50 characters.",
    surname_length: "Surname must be 2–50 characters.",
    category: "Category is required.",
    subject: "Subject is required.",
    message: "Message is required.",
    phone: "Please enter a valid phone number.",
  },
};

// Redirect messages based on language
const redirectMessages = {
  el: (sec) =>
    `Θα μεταφερθείτε αυτόματα στην αρχική σελίδα σε ${sec} δευτερόλεπτα...`,
  en: (sec) => `You will be redirected to the homepage in ${sec} seconds...`,
};

// Success messages based on language
const successMessages = {
  el: "✅ Ευχαριστούμε για το μήνυμά σας! Θα επικοινωνήσουμε μαζί σας σύντομα.",
  en: "✅ Thank you for your message! We will get back to you soon.",
};

// Validation and form handling
const requiredFields = [
  { id: "name-input", key: "name" },
  { id: "surname-input", key: "surname" },
  { id: "email-input", key: "email" },
  { id: "category-input", key: "category" },
  { id: "subject-input", key: "subject" },
  { id: "text-input", key: "message" },
];

const form = document.getElementById("form");
const responseContainer = document.getElementById("response-container");
const formResponse = document.getElementById("form-response");
const redirectMessage = document.getElementById("redirect-message");
const newMessageBtn = document.getElementById("new-message-btn");

let intervalId;
let activeRedirect = false;

// Ensure placeholder for category select is localized
const categoryInput = document.getElementById("category-input");
const disabledOption = categoryInput?.querySelector("option[disabled]");
if (disabledOption && disabledOption.textContent.trim() === "") {
  disabledOption.textContent =
    currentLang === "el" ? "Επιλέξτε Κατηγορία" : "Select Category";
}

// Handle form submit
form.addEventListener("submit", async (event) => {
  event.preventDefault();

  let isValid = true;
  let firstErrorElement = null;

  // Required fields validation
  requiredFields.forEach((field) => {
    const el = document.getElementById(field.id);
    const value = el.value.trim();

    el.classList.remove("error");
    const oldLabel = el.closest(".input-group").querySelector(".error-label");
    if (oldLabel) oldLabel.remove();

    if (value === "") {
      isValid = false;
      el.classList.add("error");
      const group = el.closest(".input-group");
      const errorLabel = document.createElement("span");
      errorLabel.className = "error-label";
      errorLabel.textContent = errorMessages[currentLang][field.key];

      group.insertBefore(errorLabel, group.firstChild);

      if (!firstErrorElement) {
        firstErrorElement = el;
      }
    }
  });

  if (!isValid) {
    if (firstErrorElement) {
      firstErrorElement.focus();
    }
    return;
  }

  // Name/surname length validation
  const nameEl = document.getElementById("name-input");
  const surnameEl = document.getElementById("surname-input");

  const validateLength = (el, min, max, messageKey) => {
    if (!el) return true;
    const value = el.value.trim();
    const length = value.length;
    const group = el.closest(".input-group");
    const existingError = group?.querySelector(".error-label");

    if (value !== "" && (length < min || length > max)) {
      el.classList.add("error");
      if (!existingError) {
        const errorLabel = document.createElement("span");
        errorLabel.className = "error-label";
        errorLabel.textContent = errorMessages[currentLang][messageKey];
        group.insertBefore(errorLabel, group.firstChild);
      }
      el.focus();
      return false;
    }
    return true;
  };

  if (!validateLength(nameEl, 2, 50, "name_length")) return;
  if (!validateLength(surnameEl, 2, 50, "surname_length")) return;

  // Email format validation
  const emailEl = document.getElementById("email-input");
  if (emailEl) {
    const emailValue = emailEl.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    const emailGroup = emailEl.closest(".input-group");
    const existingEmailError = emailGroup?.querySelector(".error-label");

    if (emailValue !== "" && !emailRegex.test(emailValue)) {
      emailEl.classList.add("error");
      if (!existingEmailError) {
        const emailError = document.createElement("span");
        emailError.className = "error-label";
        emailError.textContent = errorMessages[currentLang].email_invalid;
        emailGroup.insertBefore(emailError, emailGroup.firstChild);
      }
      emailEl.focus();
      return;
    }
  }

  // Phone format validation (optional field)
  const phoneEl = document.getElementById("phone-input");
  if (phoneEl) {
    const phoneValue = phoneEl.value.trim();
    const phoneRegex = /^[+()\d\s.-]{7,20}$/;
    const existingPhoneError = phoneEl
      .closest(".input-group")
      .querySelector(".error-label");

    if (phoneValue !== "" && !phoneRegex.test(phoneValue)) {
      phoneEl.classList.add("error");
      if (!existingPhoneError) {
        const phoneError = document.createElement("span");
        phoneError.className = "error-label";
        phoneError.textContent = errorMessages[currentLang].phone;
        phoneEl
          .closest(".input-group")
          .insertBefore(phoneError, phoneEl.closest(".input-group").firstChild);
      }
      phoneEl.focus();
      return;
    }
  }

  // Build payload
  const formData = new FormData(form);
  const responseData = {};
  formData.forEach((value, key) => {
    responseData[key] = value;
  });

  // Endpoint URL from global BASE_URL
  const baseUrl = window.BASE_URL || "/";
  const processContactUrl = `${baseUrl}admin/process_contact.php`;

  try {
    // Submit form to server
    const response = await fetch(processContactUrl, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(responseData),
    });

    if (!response.ok) {
      console.error(
        `Error: Server returned status ${response.status} - ${response.statusText}`,
      );
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();

    if (data.success) {
      // Success UI + redirect countdown
      form.style.display = "none";
      newMessageBtn.style.display = "block";
      formResponse.innerHTML = `<p>${successMessages[currentLang]}</p>`;
      redirectMessage.style.display = "block";

      responseContainer.style.display = "flex";

      let redirectSeconds = 5;
      activeRedirect = true;

      const updateRedirectMessage = () => {
        if (activeRedirect) {
          redirectMessage.textContent =
            redirectMessages[currentLang](redirectSeconds);
          redirectSeconds--;
          if (redirectSeconds < 0) {
            clearInterval(intervalId);
            window.location.href = baseUrl;
          }
        }
      };

      updateRedirectMessage();
      intervalId = setInterval(updateRedirectMessage, 1000);
    } else {
      // Server-side validation error
      formResponse.innerHTML = `<p>${data.message}</p>`;
      responseContainer.style.display = "flex";
      redirectMessage.style.display = "none";
      newMessageBtn.style.display = "block";
    }
  } catch (error) {
    // Network or unexpected error
    console.error("Error submitting form:", error);
    formResponse.innerHTML = `<p>${currentLang === "el" ? "Παρουσιάστηκε σφάλμα. Παρακαλώ δοκιμάστε ξανά αργότερα." : "An error occurred. Please try again later."}</p>`;
    responseContainer.style.display = "flex";
    redirectMessage.style.display = "none";
    newMessageBtn.style.display = "block";
  }
});

// Reset form to send a new message
newMessageBtn.addEventListener("click", () => {
  clearInterval(intervalId);
  activeRedirect = false;

  document.getElementById("form").style.display = "block";
  responseContainer.style.display = "none";

  form.reset();
  requiredFields.forEach((field) => {
    const el = document.getElementById(field.id);
    el.classList.remove("error");
    const group = el.closest(".input-group");
    const oldLabel = group.querySelector(".error-label");
    if (oldLabel) oldLabel.remove();
  });

  document.getElementById("form").scrollIntoView({ behavior: "smooth" });
});

// Remove error state on input
requiredFields.forEach((field) => {
  const el = document.getElementById(field.id);
  const eventType = el.tagName === "SELECT" ? "change" : "input";
  el.addEventListener(eventType, () => {
    if (el.classList.contains("error")) {
      el.classList.remove("error");
      const group = el.closest(".input-group");
      const oldLabel = group.querySelector(".error-label");
      if (oldLabel) oldLabel.remove();
    }
  });
});
