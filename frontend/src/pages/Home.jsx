import { useState, useEffect, useCallback } from "react";
import { fetchHub } from "../services/api";
import Terminal from "../components/Terminal";
import VideoCard from "../components/VideoCard";
import ArticleCard from "../components/ArticleCard";
import ArticleModal from "../components/ArticleModal";

export default function Home() {
  const [hub, setHub] = useState(null);
  const [error, setError] = useState(null);
  const [selectedArticle, setSelectedArticle] = useState(null);

  const load = useCallback(async () => {
    try {
      const data = await fetchHub();
      setHub(data);
    } catch (err) {
      setError(err.message);
    }
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  function handleVideoDeleted(id) {
    setHub((prev) => ({
      ...prev,
      videos: prev.videos.filter((v) => v.id !== id),
    }));
  }

  if (error) {
    return (
      <section className="container">
        <aside className="error-banner" role="alert" id="hub-error">
          <p>Impossible de charger les données : {error}</p>
        </aside>
      </section>
    );
  }

  if (!hub) {
    return (
      <section className="loading-screen" aria-busy="true">
        <progress className="spinner" />
        <p>Chargement…</p>
      </section>
    );
  }

  /* Parse links from HTML string returned by the API */
  const links = hub.links || [];

  return (
    <article className="container">
      <Terminal pseudo={hub.pseudo} description={hub.description} />

      {/* External Links */}
      {links.length > 0 && (
        <nav className="links-grid fade-in delay-1" id="links-section">
          {links.map((link, i) => (
            <a
              key={i}
              href={link.url}
              className="pill-link"
              target="_blank"
              rel="noopener noreferrer"
            >
              {link.label}
            </a>
          ))}
        </nav>
      )}

      {/* If linksHtml is returned instead of structured links */}
      {!links.length && hub.linksHtml && (
        <nav
          className="links-grid fade-in delay-1"
          id="links-section"
          dangerouslySetInnerHTML={{ __html: hub.linksHtml }}
        />
      )}

      {/* Videos */}
      {hub.videos && hub.videos.length > 0 && (
        <section className="fade-in delay-2" id="videos-section">
          <h2 className="section-title">Latest Videos</h2>
          <ul className="card-grid" role="list">
            {hub.videos.map((video) => (
              <li key={video.id}>
                <VideoCard video={video} onDeleted={handleVideoDeleted} />
              </li>
            ))}
          </ul>
        </section>
      )}

      {/* Articles */}
      {hub.articles && hub.articles.length > 0 && (
        <section className="fade-in delay-3" id="articles-section">
          <h2 className="section-title">Articles</h2>
          <ul className="card-grid card-grid--articles" role="list">
            {hub.articles.map((article) => (
              <li key={article.id}>
                <ArticleCard
                  article={article}
                  onClick={() => setSelectedArticle(article)}
                />
              </li>
            ))}
          </ul>
        </section>
      )}

      <ArticleModal
        article={selectedArticle}
        onClose={() => setSelectedArticle(null)}
      />
    </article>
  );
}
