document.addEventListener("DOMContentLoaded", async () => {
  const token = localStorage.getItem("token");
  if (!token) {
    window.location.href = "login.html";
    return;
  }

  try {
    const response = await fetch("http://localhost:8000/api/verify-token", {
      method: "GET",
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    if (!response.ok) {
      throw new Error("Invalid token");
    }

    const data = await response.json();
    if (!data.valid || (data.role !== "admin" && data.role !== "moderator")) {
      window.location.href = "profile.html";
      return;
    }

    loadUsers(token);
  } catch (error) {
    console.error("Session expired or invalid:", error);
    localStorage.removeItem("token");
    window.location.href = "login.html";
  }
});

function handleFormSubmit(formId, endpoint, messageDivId) {
  document
    .getElementById(formId)
    .addEventListener("submit", async function (e) {
      e.preventDefault();

      const messageDiv = document.getElementById(messageDivId);
      messageDiv.textContent = "Sending...";
      messageDiv.className = "";

      const formData = new FormData(this);
      const data = Object.fromEntries(formData.entries());

      try {
        const token = localStorage.getItem("token");
        const response = await fetch(
          `http://localhost:8000/api/${endpoint}`,
          {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              Authorization: `Bearer ${token}`,
            },
            body: JSON.stringify(data),
          }
        );

        const result = await response.json();

        if (response.ok) {
          messageDiv.textContent = "Success: " + result.message;
          messageDiv.className = "success";
          this.reset();
        } else {
          messageDiv.textContent =
            "Error: " + (result.error || "Request failed");
          messageDiv.className = "error";
        }
      } catch (error) {
        console.error("Error:", error);
        messageDiv.textContent = "Network Error: " + error.message;
        messageDiv.className = "error";
      }
    });
}

handleFormSubmit("add-video-form", "videos", "message-video");
handleFormSubmit("add-article-form", "articles", "message-article");

async function loadUsers(token) {
  try {
    const response = await fetch("http://localhost:8000/api/users", {
      headers: { Authorization: `Bearer ${token}` },
    });

    if (!response.ok) return;

    const data = await response.json();
    const tbody = document.getElementById("users-tbody");
    tbody.innerHTML = "";

    data.users.forEach((user) => {
      const tr = document.createElement("tr");

      const tdId = document.createElement("td");
      tdId.textContent = user.id;
      tr.appendChild(tdId);

      const tdUsername = document.createElement("td");
      tdUsername.textContent = user.username;
      tr.appendChild(tdUsername);

      const tdRole = document.createElement("td");
      const select = document.createElement("select");
      ["viewer", "sub", "moderator", "admin"].forEach((role) => {
        const option = document.createElement("option");
        option.value = role;
        option.textContent = role;
        if (role === user.role) option.selected = true;
        select.appendChild(option);
      });
      select.addEventListener("change", async () => {
        await updateUserRole(token, user.id, select.value);
      });
      tdRole.appendChild(select);
      tr.appendChild(tdRole);

      const tdCreated = document.createElement("td");
      tdCreated.textContent = user.created_at || "N/A";
      tr.appendChild(tdCreated);

      tbody.appendChild(tr);
    });
  } catch (e) {
    console.error("Failed to load users", e);
  }
}

async function updateUserRole(token, userId, newRole) {
  try {
    const response = await fetch(
      `http://localhost:8000/api/users/${userId}/role`,
      {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({ role: newRole }),
      }
    );

    const result = await response.json();

    if (!response.ok) {
      alert("Error: " + (result.error || "Failed to update role"));
    }
  } catch (e) {
    alert("Network error while updating role");
  }
}
