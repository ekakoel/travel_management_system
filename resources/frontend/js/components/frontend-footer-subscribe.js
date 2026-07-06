(function () {
    var form = document.getElementById("subscribe-form");

    if (!form) {
        return;
    }

    var emailInput = document.getElementById("footer-email");
    var alertBox = document.getElementById("subscribe-alert");
    var submitButton = form.querySelector('button[type="submit"]');
    var subscribeUrl = form.dataset.subscribeUrl;
    var csrfToken = form.dataset.csrfToken;

    var showAlert = function (type, message) {
        alertBox.classList.remove("d-none", "alert-success", "alert-danger");
        alertBox.classList.add(type === "success" ? "alert-success" : "alert-danger");
        alertBox.textContent = message;

        window.clearTimeout(showAlert._timeoutId);
        showAlert._timeoutId = window.setTimeout(function () {
            alertBox.classList.add("d-none");
            alertBox.classList.remove("alert-success", "alert-danger");
        }, 5000);
    };

    form.addEventListener("submit", async function (event) {
        event.preventDefault();

        if (!subscribeUrl || !csrfToken || !emailInput) {
            return;
        }

        submitButton.disabled = true;

        try {
            var response = await fetch(subscribeUrl, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Content-Type": "application/json",
                    Accept: "application/json"
                },
                body: JSON.stringify({
                    email: emailInput.value
                })
            });

            var data = await response.json();

            if (!response.ok) {
                var errorMessage = (data && data.errors && data.errors.email && data.errors.email[0]) || data.message || "An error occurred.";
                throw new Error(errorMessage);
            }

            showAlert("success", data.message || "Subscribed successfully.");
            form.reset();
        } catch (error) {
            showAlert("error", error.message || "An error occurred.");
        } finally {
            submitButton.disabled = false;
        }
    });
})();
