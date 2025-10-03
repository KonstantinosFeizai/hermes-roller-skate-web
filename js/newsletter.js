document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector(".newsletter-form");
    const messageContainer = document.getElementById("newsletter-message");

    // Ensure form and message container exist before adding listeners
    if (!form || !messageContainer) {
        return;
    }

    form.addEventListener("submit", function (event) {
        event.preventDefault(); // Stop the default form submission

        // Clear previous messages
        messageContainer.textContent = "";
        messageContainer.className = "newsletter-message";

        const formData = new FormData(form);

        // CORRECTED: Use the absolute path from the base URL to ensure the script is found
        // The base URL is a global variable set in the header file.
        fetch(window.BASE_URL + "newsletter-subscribe.php", {
            method: "POST",
            body: formData
        })
            .then(response => {
                // Check if the response is ok (status code 200-299)
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                messageContainer.textContent = data.message;
                messageContainer.classList.add(data.class);
            })
            .catch(error => {
                console.error('Error:', error);
                messageContainer.textContent = "An error occurred. Please try again later.";
                messageContainer.classList.add("error");
            });
    });
});
