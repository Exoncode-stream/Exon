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
    cursor.style.display = "none";

    document.getElementById("pseudo").textContent = data.pseudo;
    document.getElementById("description").textContent = data.description;

    document.getElementById("links-container").innerHTML = data.linksHtml;

    document.getElementById("videos-container").innerHTML = data.videosHtml;

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
      articlesSection.style.display = "none";
    }

    document.getElementById("close-modal").addEventListener("click", () => {
      document.getElementById("article-modal").close();
    });
  } catch (error) {
    console.error("Failed to fetch hub data:", error);
    document.getElementById("pseudo").textContent = "Connection failed";
    const cursor = document.getElementById("cursor");
    if (cursor) cursor.style.display = "none";
  }
}

document.addEventListener("DOMContentLoaded", fetchHubData);
