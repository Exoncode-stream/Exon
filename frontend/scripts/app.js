const API_URL = "http://localhost:8000/index.php";

function typewriter(element, text, speed = 60) {
  return new Promise((resolve) => {
    let i = 0;
    function tick() {
      if (i < text.length) {
        element.textContent += text.charAt(i);
        i++;
        setTimeout(tick, speed);
      } else {
        resolve();
      }
    }
    tick();
  });
}

async function fetchHubData() {
  try {
    const response = await fetch(API_URL);

    if (!response.ok) {
      throw new Error(`Server error: ${response.status}`);
    }

    const data = await response.json();

    const target = document.getElementById("typewriter-target");
    const cursor = document.getElementById("cursor");

    await typewriter(target, "whoami");
    await new Promise((r) => setTimeout(r, 400));
    cursor.classList.add("hidden");

    document.getElementById("pseudo").textContent = data.pseudo;
    document.getElementById("description").textContent = data.description;

    document.getElementById("links-container").innerHTML = data.linksHtml;
    document.getElementById("videos-container").innerHTML = data.videosHtml;

    const authContainer = document.getElementById("auth-container");
    const token = localStorage.getItem("token");
    let userRole = "viewer";

    if (token) {
      try {
        const roleResp = await fetch("http://localhost:8000/verify-token.php", {
          headers: { Authorization: `Bearer ${token}` },
        });
        if (roleResp.ok) {
          const roleData = await roleResp.json();
          if (roleData.valid) {
            userRole = roleData.role;
            localStorage.setItem("role", userRole);
            localStorage.setItem("username", roleData.username);
          }
        }
      } catch (e) {
        console.error("Token verification failed", e);
      }
    }

    if (authContainer) {
      if (token) {
        const username = localStorage.getItem("username") || "Profile";
        const profileLink = document.createElement("a");
        profileLink.href = "profile.html";
        profileLink.className = "btn-primary";
        profileLink.textContent = `${username} (${userRole})`;
        authContainer.appendChild(profileLink);
      } else {
        const loginLink = document.createElement("a");
        loginLink.href = "login.html";
        loginLink.className = "btn-primary";
        loginLink.textContent = "Login";

        const registerLink = document.createElement("a");
        registerLink.href = "register.html";
        registerLink.className = "btn-secondary";
        registerLink.textContent = "Register";

        authContainer.appendChild(loginLink);
        authContainer.appendChild(registerLink);
      }
    }

    if (userRole === "admin" || userRole === "moderator") {
      const videoCards = document.querySelectorAll(
        "#videos-container .video-card"
      );
      videoCards.forEach((card) => {
        const videoId = card.getAttribute("data-id");
        if (videoId) {
          const deleteBtn = document.createElement("button");
          deleteBtn.textContent = "Delete";
          deleteBtn.className = "btn-delete-video";
          deleteBtn.addEventListener("click", async () => {
            if (confirm("Are you sure you want to delete this video?")) {
              try {
                const delResp = await fetch(
                  "http://localhost:8000/delete-video.php",
                  {
                    method: "POST",
                    headers: {
                      "Content-Type": "application/json",
                      Authorization: `Bearer ${token}`,
                    },
                    body: JSON.stringify({ id: videoId }),
                  }
                );
                if (delResp.ok) {
                  card.remove();
                } else {
                  alert("Failed to delete video");
                }
              } catch (e) {
                alert("Network error while deleting video");
              }
            }
          });
          card.appendChild(deleteBtn);
        }
      });
    }

    const articlesSection = document.getElementById("articles-section");
    const articlesContainer = document.getElementById("articles-container");
    articlesContainer.innerHTML = "";

    if (data.articles && data.articles.length > 0) {
      data.articles.forEach((article) => {
        const articleEl = document.createElement("article");
        articleEl.className = "article-card";

        const titleH3 = document.createElement("h3");
        titleH3.textContent = article.title;
        articleEl.appendChild(titleH3);

        articleEl.addEventListener("click", () => {
          document.getElementById("modal-title").textContent = article.title;
          document.getElementById("modal-content").textContent =
            article.content;
          document.getElementById("article-modal").showModal();
        });

        articlesContainer.appendChild(articleEl);
      });
    } else {
      articlesSection.classList.add("hidden");
    }

    document.getElementById("close-modal").addEventListener("click", () => {
      document.getElementById("article-modal").close();
    });
  } catch (error) {
    console.error("Failed to fetch hub data:", error);
    document.getElementById("pseudo").textContent = "Connection failed";
    const cursor = document.getElementById("cursor");
    if (cursor) cursor.classList.add("hidden");
  }
}

document.addEventListener("DOMContentLoaded", fetchHubData);
