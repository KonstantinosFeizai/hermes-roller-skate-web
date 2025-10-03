// determine current language based on URL path
const currentLang = window.location.pathname.includes('/gr/') ? 'gr' : 'en';

// field-specific error messages
const errorMessages = {
    gr: {
        name: "Το όνομα είναι υποχρεωτικό.",
        surname: "Το επώνυμο είναι υποχρεωτικό.",
        email: "Το email είναι υποχρεωτικό.",
        category: "Η κατηγορία είναι υποχρεωτική.",
        subject: "Το θέμα είναι υποχρεωτικό.",
        message: "Το μήνυμα είναι υποχρεωτικό."
    },
    en: {
        name: "Name is required.",
        surname: "Surname is required.",
        email: "Email is required.",
        category: "Category is required.",
        subject: "Subject is required.",
        message: "Message is required."
    }
};

// redirect messages based on language
const redirectMessages = {
    gr: (sec) => `Θα μεταφερθείτε αυτόματα στην αρχική σελίδα σε ${sec} δευτερόλεπτα...`,
    en: (sec) => `You will be redirected to the homepage in ${sec} seconds...`
};

// Success messages based on language
const successMessages = {
    gr: "✅ Ευχαριστούμε για το μήνυμά σας! Θα επικοινωνήσουμε μαζί σας σύντομα.",
    en: "✅ Thank you for your message! We will get back to you soon."
};

// validation and form handling
const requiredFields = [
    { id: "name-input", key: "name" },
    { id: "surname-input", key: "surname" },
    { id: "email-input", key: "email" },
    { id: "category-input", key: "category" },
    { id: "subject-input", key: "subject" },
    { id: "text-input", key: "message" }
];

const form = document.getElementById("form");
const responseContainer = document.getElementById('response-container');
const formResponse = document.getElementById('form-response');
const redirectMessage = document.getElementById('redirect-message');
const newMessageBtn = document.getElementById('new-message-btn');

let intervalId;
let activeRedirect = false;

const categoryInput = document.getElementById("category-input");
if (currentLang === 'gr') {
    categoryInput.querySelector('option[disabled]').textContent = 'Επιλέξτε Κατηγορία';
} else {
    categoryInput.querySelector('option[disabled]').textContent = 'Select Category';
}

form.addEventListener("submit", async (event) => {
    event.preventDefault();

    let isValid = true;
    let firstErrorElement = null;

    // Validation
    requiredFields.forEach(field => {
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

    const formData = new FormData(form);
    const responseData = {};
    formData.forEach((value, key) => {
        responseData[key] = value;
    });

    const processContactUrl = currentLang === 'gr' ? '../process_contact.php' : 'process_contact.php';

    try {
        const response = await fetch(processContactUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(responseData)
        });

        if (!response.ok) {
            console.error(`Error: Server returned status ${response.status} - ${response.statusText}`);
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.success) {
            form.style.display = "none";
            newMessageBtn.style.display = "block";
            formResponse.innerHTML = `<p>${successMessages[currentLang]}</p>`;
            redirectMessage.style.display = 'block';


            responseContainer.style.display = 'flex';


            let redirectSeconds = 5;
            activeRedirect = true;

            const updateRedirectMessage = () => {
                if (activeRedirect) {
                    redirectMessage.textContent = redirectMessages[currentLang](redirectSeconds);
                    redirectSeconds--;
                    if (redirectSeconds < 0) {
                        clearInterval(intervalId);
                        const redirectUrl = currentLang === 'gr' ? 'index.php' : 'index.php';
                        window.location.href = redirectUrl;
                    }
                }
            };

            updateRedirectMessage();
            intervalId = setInterval(updateRedirectMessage, 1000);

        } else {
            formResponse.innerHTML = `<p>${data.message}</p>`;
            responseContainer.style.display = "flex";
            redirectMessage.style.display = "none";
            newMessageBtn.style.display = "block";
        }
    } catch (error) {
        console.error("Error submitting form:", error);
        formResponse.innerHTML = `<p>${currentLang === 'gr' ? 'Παρουσιάστηκε σφάλμα. Παρακαλώ δοκιμάστε ξανά αργότερα.' : 'An error occurred. Please try again later.'}</p>`;
        responseContainer.style.display = "flex";
        redirectMessage.style.display = "none";
        newMessageBtn.style.display = "block";
    }
});

newMessageBtn.addEventListener("click", () => {
    clearInterval(intervalId);
    activeRedirect = false;

    document.getElementById("form").style.display = "block";
    responseContainer.style.display = "none";

    form.reset();
    requiredFields.forEach(field => {
        const el = document.getElementById(field.id);
        el.classList.remove("error");
        const group = el.closest(".input-group");
        const oldLabel = group.querySelector(".error-label");
        if (oldLabel) oldLabel.remove();
    });

    document.getElementById("form").scrollIntoView({ behavior: "smooth" });
});

requiredFields.forEach(field => {
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
