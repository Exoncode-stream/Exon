const API_URL = "http://localhost:8000/index.php";

// Async function to fetch data from the backend API
async function fetchHubData() {
  try {
    const response = await fetch(API_URL);

    if (!response.ok) {
      throw new Error(`Erreur serveur : ${response.status}`);
    }

    const data = await response.json();

    // Get the pseudo, description, and links container elements
    const pseudoElement = document.getElementById("pseudo");
    const descElement = document.getElementById("description");
    const linksContainer = document.getElementById("links-container");

    // Set the pseudo and description
    pseudoElement.textContent = data.pseudo;
    descElement.textContent = data.description;

    // Create link elements
    data.links.forEach((link) => {
      const anchor = document.createElement("a");
      anchor.href = link.url;
      anchor.textContent = link.name;
      anchor.className = "btn-link";
      anchor.target = "_blank";

      linksContainer.appendChild(anchor);
    });
  } catch (error) {
    console.error("Impossible de récupérer les données :", error);
    document.getElementById("pseudo").textContent = "Erreur de chargement";
  }
}

document.addEventListener("DOMContentLoaded", fetchHubData);
