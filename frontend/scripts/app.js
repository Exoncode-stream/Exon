const API_URL = "http://localhost:8000/index.php";

async function fetchHubData() {
  try {
    const response = await fetch(API_URL);

    if (!response.ok) {
      throw new Error(`Erreur serveur : ${response.status}`);
    }

    const data = await response.json();

    document.getElementById("pseudo").textContent = data.pseudo;
    document.getElementById("description").textContent = data.description;

    document.getElementById("links-container").innerHTML = data.linksHtml;
    document.getElementById("videos-container").innerHTML = data.videosHtml;

    const articlesContainer = document.getElementById("articles-container");
    articlesContainer.innerHTML = '';
    if (data.articles && data.articles.length > 0) {
      data.articles.forEach(article => {
        const articleEl = document.createElement("article");
        articleEl.className = "article-card";

        const titleH3 = document.createElement("h3");
        titleH3.textContent = article.title;
        articleEl.appendChild(titleH3);

        articleEl.addEventListener("click", () => {
          document.getElementById("modal-title").textContent = article.title;
          document.getElementById("modal-content").textContent = article.content;
          document.getElementById("article-modal").showModal();
        });

        articlesContainer.appendChild(articleEl);
      });
    }

    document.getElementById("close-modal").addEventListener("click", () => {
      document.getElementById("article-modal").close();
    });

  } catch (error) {
    console.error("Impossible de récupérer les données :", error);
    document.getElementById("pseudo").textContent = "Erreur de chargement";
  }
}

document.addEventListener("DOMContentLoaded", fetchHubData);
