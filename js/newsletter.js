// newsletter.js
// Purpose: Handle newsletter form submission via AJAX and show feedback.
document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector(".newsletter-form");
  const messageContainer = document.getElementById("newsletter-message");

  // Ensure form and message container exist before adding listeners
  if (!form || !messageContainer) {
    return;
  }

  form.addEventListener("submit", function (event) {
    // Stop the default form submission
    event.preventDefault();

    // Clear previous messages
    messageContainer.textContent = "";
    messageContainer.className = "newsletter-message";

    const formData = new FormData(form);

    // Use the base URL from the header to build the endpoint path
    fetch(window.BASE_URL + "admin/newsletter-subscribe.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        // Check if the response is OK (status code 200-299)
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then((data) => {
        // Show server message
        messageContainer.textContent = data.message;
        messageContainer.classList.add(data.class);
      })
      .catch((error) => {
        console.error("Error:", error);
        // Fallback error message
        messageContainer.textContent =
          "An error occurred. Please try again later.";
        messageContainer.classList.add("error");
      });
  });
});
