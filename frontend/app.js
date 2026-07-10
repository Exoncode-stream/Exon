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

    // Create video elements
    const videosContainer = document.getElementById("videos-container");
    if (data.videos && Array.isArray(data.videos)) {
      data.videos.forEach((video) => {
        const videoCard = document.createElement("div");
        videoCard.className = "video-card";

        // Extract YouTube ID from URL or use as is
        let videoId = video.youtube_id;
        if (videoId.includes("youtube.com") || videoId.includes("youtu.be")) {
          const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
          const match = videoId.match(regExp);
          if (match && match[2].length === 11) {
            videoId = match[2];
          }
        }

        // Create iframe element
        const iframe = document.createElement("iframe");
        iframe.src = `https://www.youtube.com/embed/${videoId}`;
        iframe.title = video.title;
        iframe.width = "100%";
        iframe.height = "315";
        iframe.style.border = "none";
        iframe.setAttribute("allow", "autoplay; encrypted-media; picture-in-picture");
        iframe.allowFullscreen = true;

        videoCard.appendChild(iframe);

        // Add video title
        if (video.title) {
          const titleEl = document.createElement("h3");
          titleEl.textContent = video.title;
          videoCard.appendChild(titleEl);
        }

        // Add video category
        if (video.category) {
          const categoryEl = document.createElement("span");
          categoryEl.textContent = video.category;
          categoryEl.className = "video-category";
          videoCard.appendChild(categoryEl);
        }

        videosContainer.appendChild(videoCard);
      });
    }
  } catch (error) {
    console.error("Impossible de récupérer les données :", error);
    document.getElementById("pseudo").textContent = "Erreur de chargement";
  }
}

document.addEventListener("DOMContentLoaded", fetchHubData);
