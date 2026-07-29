import { useState, useEffect, useCallback, useMemo } from "react";
import { fetchHub } from "../services/api";
import Terminal from "../components/Terminal";
import VideoCard from "../components/VideoCard";
import ArticleCard from "../components/ArticleCard";
import ArticleModal from "../components/ArticleModal";

/**
 * Home Component
 * Main landing page for the Exon hub.
 * Displays terminal profile, links, search/filter toolbar, videos, and articles.
 *
 * @returns {JSX.Element}
 */
export default function Home() {
  const [hub, setHub] = useState(null);
  const [error, setError] = useState(null);
  const [selectedArticle, setSelectedArticle] = useState(null);

  /* ─── Search & Category Filter State ─── */
  const [searchQuery, setSearchQuery] = useState("");
  const [selectedCategory, setSelectedCategory] = useState("all");

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

  /* ─── Extract Unique Categories ─── */
  const categories = useMemo(() => {
    if (!hub?.videos) return ["all"];
    const cats = new Set();
    hub.videos.forEach((v) => {
      if (v.category) cats.add(v.category.trim());
    });
    return ["all", ...Array.from(cats)];
  }, [hub]);

  /* ─── Filter Logic ─── */
  const filteredVideos = useMemo(() => {
    if (!hub?.videos) return [];
    const query = searchQuery.toLowerCase().trim();

    return hub.videos.filter((video) => {
      const matchesCategory =
        selectedCategory === "all" ||
        video.category?.toLowerCase() === selectedCategory.toLowerCase();
      const matchesQuery =
        !query ||
        video.title?.toLowerCase().includes(query) ||
        video.category?.toLowerCase().includes(query);

      return matchesCategory && matchesQuery;
    });
  }, [hub, searchQuery, selectedCategory]);

  const filteredArticles = useMemo(() => {
    if (!hub?.articles) return [];
    const query = searchQuery.toLowerCase().trim();

    return hub.articles.filter((article) => {
      const matchesQuery =
        !query ||
        article.title?.toLowerCase().includes(query) ||
        article.content?.toLowerCase().includes(query);

      return matchesQuery;
    });
  }, [hub, searchQuery]);

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

  const links = hub.links || [];
  const hasNoResults =
    (hub.videos?.length > 0 || hub.articles?.length > 0) &&
    filteredVideos.length === 0 &&
    filteredArticles.length === 0;

  return (
    <article className="container">
      <Terminal pseudo={hub.pseudo} description={hub.description} />

      {/* External Links Navigation */}
      {links.length > 0 && (
        <nav className="links-grid fade-in delay-1" id="links-section">
          {links.map((link, i) => (
            <a
              key={link.id || i}
              href={link.url}
              className="pill-link"
              target="_blank"
              rel="noopener noreferrer"
            >
              {link.label || link.name}
            </a>
          ))}
        </nav>
      )}

      {/* ─── Search & Filter Toolbar ─── */}
      <section className="hub-toolbar fade-in delay-1" id="hub-filter-toolbar">
        <div className="search-box">
          <span className="search-prompt">&gt;</span>
          <input
            type="text"
            className="search-input"
            id="hub-search-input"
            placeholder="grep search videos, articles..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
          />
          {searchQuery && (
            <button
              type="button"
              className="search-clear"
              onClick={() => setSearchQuery("")}
              aria-label="Effacer la recherche"
            >
              ✕
            </button>
          )}
        </div>

        {categories.length > 1 && (
          <div className="category-filter" id="category-filter-pills">
            <span className="category-filter-label">Catégories :</span>
            {categories.map((cat) => (
              <button
                key={cat}
                type="button"
                className={`category-pill ${selectedCategory === cat ? "active" : ""}`}
                onClick={() => setSelectedCategory(cat)}
              >
                {cat === "all" ? "[ Tous ]" : cat}
              </button>
            ))}
          </div>
        )}
      </section>

      {/* No Results Feedback */}
      {hasNoResults && (
        <div className="no-results fade-in" id="no-results-alert">
          <p>&gt; aucun résultat correspondant à la recherche "{searchQuery}"</p>
          <button
            type="button"
            className="pill-link"
            onClick={() => {
              setSearchQuery("");
              setSelectedCategory("all");
            }}
          >
            Réinitialiser les filtres
          </button>
        </div>
      )}

      {/* Videos */}
      {filteredVideos.length > 0 && (
        <section className="fade-in delay-2" id="videos-section">
          <h2 className="section-title">
            Latest Videos ({filteredVideos.length})
          </h2>
          <ul className="card-grid" role="list">
            {filteredVideos.map((video) => (
              <li key={video.id}>
                <VideoCard video={video} onDeleted={handleVideoDeleted} />
              </li>
            ))}
          </ul>
        </section>
      )}

      {/* Articles */}
      {filteredArticles.length > 0 && (
        <section className="fade-in delay-3" id="articles-section">
          <h2 className="section-title">
            Articles ({filteredArticles.length})
          </h2>
          <ul className="card-grid card-grid--articles" role="list">
            {filteredArticles.map((article) => (
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
