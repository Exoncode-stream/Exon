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
    if (data.valid) {
      document.getElementById("profile-username").textContent = data.username;
      document.getElementById("profile-role").textContent = data.role;

      if (data.role === "admin" || data.role === "moderator") {
        document.getElementById("admin-link").classList.remove("hidden");
      }

      localStorage.setItem("role", data.role);
      localStorage.setItem("username", data.username);
    } else {
      throw new Error("Invalid token");
    }
  } catch (error) {
    localStorage.removeItem("token");
    localStorage.removeItem("role");
    localStorage.removeItem("username");
    window.location.href = "login.html";
  }
});

document.getElementById("logout-btn").addEventListener("click", () => {
  localStorage.removeItem("token");
  localStorage.removeItem("role");
  localStorage.removeItem("username");
  window.location.href = "index.html";
});
