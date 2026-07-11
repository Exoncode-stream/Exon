document
  .getElementById("register-form")
  .addEventListener("submit", async function (e) {
    e.preventDefault();

    const messageDiv = document.getElementById("message");
    messageDiv.textContent = "Registering...";
    messageDiv.className = "";

    const data = {
      username: this.username.value,
      password: this.password.value,
    };

    try {
      const response = await fetch("http://localhost:8000/api/register", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      });

      const result = await response.json();

      if (response.ok) {
        messageDiv.textContent = "Success: " + result.message;
        messageDiv.className = "success";
        setTimeout(() => {
          window.location.href = "login.html";
        }, 2000);
      } else {
        messageDiv.textContent =
          "Error: " + (result.error || "Failed to register");
        messageDiv.className = "error";
      }
    } catch (error) {
      messageDiv.textContent = "Network Error: " + error.message;
      messageDiv.className = "error";
    }
  });
