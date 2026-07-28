import { useState, useEffect, useCallback } from "react";
import {
  fetchVideos,
  addVideo,
  updateVideo,
  deleteVideo,
  fetchArticles,
  addArticle,
  updateArticle,
  deleteArticle,
  fetchUsers,
  updateUserRole,
  fetchLinks,
  addLink,
  updateLink,
  deleteLink,
} from "../services/api";
import FormMessage from "../components/FormMessage";

export default function Admin() {
  /* ─── Links Form & CRUD ─── */
  const [links, setLinks] = useState([]);
  const [linkName, setLinkName] = useState("");
  const [linkUrl, setLinkUrl] = useState("");
  const [linkMsg, setLinkMsg] = useState("");
  const [linkMsgType, setLinkMsgType] = useState("");
  const [editingLink, setEditingLink] = useState(null);

  /* ─── Video Form & CRUD ─── */
  const [videos, setVideos] = useState([]);
  const [videoTitle, setVideoTitle] = useState("");
  const [youtubeId, setYoutubeId] = useState("");
  const [category, setCategory] = useState("");
  const [videoMsg, setVideoMsg] = useState("");
  const [videoMsgType, setVideoMsgType] = useState("");
  const [editingVideo, setEditingVideo] = useState(null);

  /* ─── Article Form & CRUD ─── */
  const [articles, setArticles] = useState([]);
  const [articleTitle, setArticleTitle] = useState("");
  const [articleContent, setArticleContent] = useState("");
  const [articleMsg, setArticleMsg] = useState("");
  const [articleMsgType, setArticleMsgType] = useState("");
  const [editingArticle, setEditingArticle] = useState(null);

  /* ─── Users ─── */
  const [users, setUsers] = useState([]);

  /* ─── Data Loaders ─── */
  const loadUsers = useCallback(async () => {
    try {
      const data = await fetchUsers();
      setUsers(data.users || []);
    } catch (err) {
      console.error("Failed to load users", err);
    }
  }, []);

  const loadLinks = useCallback(async () => {
    try {
      const data = await fetchLinks();
      setLinks(data || []);
    } catch (err) {
      console.error("Failed to load links", err);
    }
  }, []);

  const loadVideos = useCallback(async () => {
    try {
      const data = await fetchVideos();
      setVideos(data || []);
    } catch (err) {
      console.error("Failed to load videos", err);
    }
  }, []);

  const loadArticles = useCallback(async () => {
    try {
      const data = await fetchArticles();
      setArticles(data || []);
    } catch (err) {
      console.error("Failed to load articles", err);
    }
  }, []);

  useEffect(() => {
    loadUsers();
    loadLinks();
    loadVideos();
    loadArticles();
  }, [loadUsers, loadLinks, loadVideos, loadArticles]);

  /* ─── Links Handlers ─── */
  async function handleAddLink(e) {
    e.preventDefault();
    setLinkMsg("Envoi…");
    setLinkMsgType("");

    try {
      const res = await addLink(linkName, linkUrl);
      setLinkMsg(res.message || "Lien ajouté !");
      setLinkMsgType("success");
      setLinkName("");
      setLinkUrl("");
      loadLinks();
    } catch (err) {
      setLinkMsg(err.message);
      setLinkMsgType("error");
    }
  }

  async function handleUpdateLink(e) {
    e.preventDefault();
    if (!editingLink) return;

    try {
      await updateLink(editingLink.id, editingLink.name, editingLink.url);
      setEditingLink(null);
      loadLinks();
    } catch (err) {
      alert(err.message);
    }
  }

  async function handleDeleteLink(id) {
    if (!window.confirm("Voulez-vous vraiment supprimer ce lien ?")) return;

    try {
      await deleteLink(id);
      setLinks((prev) => prev.filter((l) => l.id !== id));
    } catch (err) {
      alert(err.message);
    }
  }

  /* ─── Video Handlers ─── */
  async function handleAddVideo(e) {
    e.preventDefault();
    setVideoMsg("Envoi…");
    setVideoMsgType("");

    try {
      const res = await addVideo(videoTitle, youtubeId, category);
      setVideoMsg(res.message || "Vidéo ajoutée !");
      setVideoMsgType("success");
      setVideoTitle("");
      setYoutubeId("");
      setCategory("");
      loadVideos();
    } catch (err) {
      setVideoMsg(err.message);
      setVideoMsgType("error");
    }
  }

  async function handleUpdateVideo(e) {
    e.preventDefault();
    if (!editingVideo) return;

    try {
      await updateVideo(editingVideo.id, editingVideo.title, editingVideo.youtube_id, editingVideo.category);
      setEditingVideo(null);
      loadVideos();
    } catch (err) {
      alert(err.message);
    }
  }

  async function handleDeleteVideo(id) {
    if (!window.confirm("Voulez-vous vraiment supprimer cette vidéo ?")) return;

    try {
      await deleteVideo(id);
      setVideos((prev) => prev.filter((v) => v.id !== id));
    } catch (err) {
      alert(err.message);
    }
  }

  /* ─── Article Handlers ─── */
  async function handleAddArticle(e) {
    e.preventDefault();
    setArticleMsg("Envoi…");
    setArticleMsgType("");

    try {
      const res = await addArticle(articleTitle, articleContent);
      setArticleMsg(res.message || "Article ajouté !");
      setArticleMsgType("success");
      setArticleTitle("");
      setArticleContent("");
      loadArticles();
    } catch (err) {
      setArticleMsg(err.message);
      setArticleMsgType("error");
    }
  }

  async function handleUpdateArticle(e) {
    e.preventDefault();
    if (!editingArticle) return;

    try {
      await updateArticle(editingArticle.id, editingArticle.title, editingArticle.content);
      setEditingArticle(null);
      loadArticles();
    } catch (err) {
      alert(err.message);
    }
  }

  async function handleDeleteArticle(id) {
    if (!window.confirm("Voulez-vous vraiment supprimer cet article ?")) return;

    try {
      await deleteArticle(id);
      setArticles((prev) => prev.filter((a) => a.id !== id));
    } catch (err) {
      alert(err.message);
    }
  }

  /* ─── User Role Handler ─── */
  async function handleRoleChange(userId, newRole) {
    try {
      await updateUserRole(userId, newRole);
    } catch (err) {
      alert(err.message);
    }
  }

  return (
    <article className="admin-page">
      <h1>admin-panel</h1>

      {/* ─── Links Management ─── */}
      <section className="admin-section" id="links-section">
        <h2 className="section-title">Gestion des Liens</h2>
        <form onSubmit={handleAddLink} className="form-card" id="add-link-form">
          <fieldset className="form-group">
            <label htmlFor="link-name">Nom du lien</label>
            <input
              type="text"
              id="link-name"
              placeholder="Ex: GitHub, Twitter, Discord..."
              value={linkName}
              onChange={(e) => setLinkName(e.target.value)}
              required
            />
          </fieldset>
          <fieldset className="form-group">
            <label htmlFor="link-url">URL</label>
            <input
              type="url"
              id="link-url"
              placeholder="https://github.com/mon-profil"
              value={linkUrl}
              onChange={(e) => setLinkUrl(e.target.value)}
              required
            />
          </fieldset>
          <button type="submit" className="btn-primary" id="submit-link">
            Ajouter le lien
          </button>
        </form>
        <FormMessage message={linkMsg} type={linkMsgType} />

        {editingLink && (
          <form onSubmit={handleUpdateLink} className="form-card" id="edit-link-form" style={{ marginTop: "1rem" }}>
            <h3>Modifier le lien #{editingLink.id}</h3>
            <fieldset className="form-group">
              <label htmlFor="edit-link-name">Nom</label>
              <input
                type="text"
                id="edit-link-name"
                value={editingLink.name}
                onChange={(e) => setEditingLink({ ...editingLink, name: e.target.value })}
                required
              />
            </fieldset>
            <fieldset className="form-group">
              <label htmlFor="edit-link-url">URL</label>
              <input
                type="url"
                id="edit-link-url"
                value={editingLink.url}
                onChange={(e) => setEditingLink({ ...editingLink, url: e.target.value })}
                required
              />
            </fieldset>
            <div style={{ display: "flex", gap: "0.5rem" }}>
              <button type="submit" className="btn-primary" id="save-link-edit">
                Enregistrer
              </button>
              <button
                type="button"
                className="btn-danger"
                onClick={() => setEditingLink(null)}
                id="cancel-link-edit"
              >
                Annuler
              </button>
            </div>
          </form>
        )}

        <figure className="table-wrap" style={{ marginTop: "1.5rem" }}>
          <table className="users-table" id="links-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>URL</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              {links.map((link) => (
                <tr key={link.id}>
                  <td>{link.id}</td>
                  <td>{link.name}</td>
                  <td>
                    <a href={link.url} target="_blank" rel="noopener noreferrer" style={{ color: "var(--accent-color, #00ff66)" }}>
                      {link.url}
                    </a>
                  </td>
                  <td>
                    <div style={{ display: "flex", gap: "0.5rem" }}>
                      <button
                        type="button"
                        className="pill-link"
                        onClick={() => setEditingLink(link)}
                        id={`edit-link-btn-${link.id}`}
                      >
                        Éditer
                      </button>
                      <button
                        type="button"
                        className="btn-danger"
                        onClick={() => handleDeleteLink(link.id)}
                        id={`delete-link-btn-${link.id}`}
                      >
                        Supprimer
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
              {links.length === 0 && (
                <tr>
                  <td colSpan="4" className="text-center">
                    Aucun lien enregistré
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </figure>
      </section>

      {/* ─── Videos Management ─── */}
      <section className="admin-section" id="videos-section">
        <h2 className="section-title">Gestion des Vidéos</h2>
        <form onSubmit={handleAddVideo} className="form-card" id="add-video-form">
          <fieldset className="form-group">
            <label htmlFor="video-title">Titre</label>
            <input
              type="text"
              id="video-title"
              placeholder="Mon super tuto"
              value={videoTitle}
              onChange={(e) => setVideoTitle(e.target.value)}
              required
            />
          </fieldset>
          <fieldset className="form-group">
            <label htmlFor="video-youtube-id">URL ou ID YouTube</label>
            <input
              type="text"
              id="video-youtube-id"
              placeholder="https://youtube.com/watch?v=..."
              value={youtubeId}
              onChange={(e) => setYoutubeId(e.target.value)}
              required
            />
          </fieldset>
          <fieldset className="form-group">
            <label htmlFor="video-category">Catégorie</label>
            <input
              type="text"
              id="video-category"
              placeholder="Web development"
              value={category}
              onChange={(e) => setCategory(e.target.value)}
              required
            />
          </fieldset>
          <button type="submit" className="btn-primary" id="submit-video">
            Ajouter la vidéo
          </button>
        </form>
        <FormMessage message={videoMsg} type={videoMsgType} />

        {editingVideo && (
          <form onSubmit={handleUpdateVideo} className="form-card" id="edit-video-form" style={{ marginTop: "1rem" }}>
            <h3>Modifier la vidéo #{editingVideo.id}</h3>
            <fieldset className="form-group">
              <label htmlFor="edit-video-title">Titre</label>
              <input
                type="text"
                id="edit-video-title"
                value={editingVideo.title}
                onChange={(e) => setEditingVideo({ ...editingVideo, title: e.target.value })}
                required
              />
            </fieldset>
            <fieldset className="form-group">
              <label htmlFor="edit-video-youtube-id">ID YouTube</label>
              <input
                type="text"
                id="edit-video-youtube-id"
                value={editingVideo.youtube_id}
                onChange={(e) => setEditingVideo({ ...editingVideo, youtube_id: e.target.value })}
                required
              />
            </fieldset>
            <fieldset className="form-group">
              <label htmlFor="edit-video-category">Catégorie</label>
              <input
                type="text"
                id="edit-video-category"
                value={editingVideo.category}
                onChange={(e) => setEditingVideo({ ...editingVideo, category: e.target.value })}
                required
              />
            </fieldset>
            <div style={{ display: "flex", gap: "0.5rem" }}>
              <button type="submit" className="btn-primary" id="save-video-edit">
                Enregistrer
              </button>
              <button
                type="button"
                className="btn-danger"
                onClick={() => setEditingVideo(null)}
                id="cancel-video-edit"
              >
                Annuler
              </button>
            </div>
          </form>
        )}

        <figure className="table-wrap" style={{ marginTop: "1.5rem" }}>
          <table className="users-table" id="videos-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>YouTube ID</th>
                <th>Catégorie</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              {videos.map((vid) => (
                <tr key={vid.id}>
                  <td>{vid.id}</td>
                  <td>{vid.title}</td>
                  <td><code>{vid.youtube_id}</code></td>
                  <td><mark className="badge">{vid.category}</mark></td>
                  <td>
                    <div style={{ display: "flex", gap: "0.5rem" }}>
                      <button
                        type="button"
                        className="pill-link"
                        onClick={() => setEditingVideo(vid)}
                        id={`edit-video-btn-${vid.id}`}
                      >
                        Éditer
                      </button>
                      <button
                        type="button"
                        className="btn-danger"
                        onClick={() => handleDeleteVideo(vid.id)}
                        id={`delete-video-btn-${vid.id}`}
                      >
                        Supprimer
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
              {videos.length === 0 && (
                <tr>
                  <td colSpan="5" className="text-center">
                    Aucune vidéo enregistrée
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </figure>
      </section>

      {/* ─── Articles Management ─── */}
      <section className="admin-section" id="articles-section">
        <h2 className="section-title">Gestion des Articles</h2>
        <form onSubmit={handleAddArticle} className="form-card" id="add-article-form">
          <fieldset className="form-group">
            <label htmlFor="article-title">Titre</label>
            <input
              type="text"
              id="article-title"
              placeholder="Introduction à React"
              value={articleTitle}
              onChange={(e) => setArticleTitle(e.target.value)}
              required
            />
          </fieldset>
          <fieldset className="form-group">
            <label htmlFor="article-content">Contenu</label>
            <textarea
              id="article-content"
              rows="6"
              placeholder="Rédigez votre article ici…"
              value={articleContent}
              onChange={(e) => setArticleContent(e.target.value)}
              required
            />
          </fieldset>
          <button type="submit" className="btn-primary" id="submit-article">
            Ajouter l'article
          </button>
        </form>
        <FormMessage message={articleMsg} type={articleMsgType} />

        {editingArticle && (
          <form onSubmit={handleUpdateArticle} className="form-card" id="edit-article-form" style={{ marginTop: "1rem" }}>
            <h3>Modifier l'article #{editingArticle.id}</h3>
            <fieldset className="form-group">
              <label htmlFor="edit-article-title">Titre</label>
              <input
                type="text"
                id="edit-article-title"
                value={editingArticle.title}
                onChange={(e) => setEditingArticle({ ...editingArticle, title: e.target.value })}
                required
              />
            </fieldset>
            <fieldset className="form-group">
              <label htmlFor="edit-article-content">Contenu</label>
              <textarea
                id="edit-article-content"
                rows="6"
                value={editingArticle.content}
                onChange={(e) => setEditingArticle({ ...editingArticle, content: e.target.value })}
                required
              />
            </fieldset>
            <div style={{ display: "flex", gap: "0.5rem" }}>
              <button type="submit" className="btn-primary" id="save-article-edit">
                Enregistrer
              </button>
              <button
                type="button"
                className="btn-danger"
                onClick={() => setEditingArticle(null)}
                id="cancel-article-edit"
              >
                Annuler
              </button>
            </div>
          </form>
        )}

        <figure className="table-wrap" style={{ marginTop: "1.5rem" }}>
          <table className="users-table" id="articles-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Aperçu</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              {articles.map((art) => (
                <tr key={art.id}>
                  <td>{art.id}</td>
                  <td>{art.title}</td>
                  <td>{art.content ? art.content.slice(0, 60) + "…" : ""}</td>
                  <td>
                    <div style={{ display: "flex", gap: "0.5rem" }}>
                      <button
                        type="button"
                        className="pill-link"
                        onClick={() => setEditingArticle(art)}
                        id={`edit-article-btn-${art.id}`}
                      >
                        Éditer
                      </button>
                      <button
                        type="button"
                        className="btn-danger"
                        onClick={() => handleDeleteArticle(art.id)}
                        id={`delete-article-btn-${art.id}`}
                      >
                        Supprimer
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
              {articles.length === 0 && (
                <tr>
                  <td colSpan="4" className="text-center">
                    Aucun article enregistré
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </figure>
      </section>

      {/* ─── Users Table ─── */}
      <section className="admin-section" id="users-section">
        <h2 className="section-title">Utilisateurs</h2>
        <figure className="table-wrap">
          <table className="users-table" id="users-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Utilisateur</th>
                <th>Rôle</th>
                <th>Créé le</th>
              </tr>
            </thead>
            <tbody>
              {users.map((u) => (
                <tr key={u.id}>
                  <td>{u.id}</td>
                  <td>{u.username}</td>
                  <td>
                    <select
                      value={u.role}
                      onChange={(e) => {
                        handleRoleChange(u.id, e.target.value);
                        setUsers((prev) =>
                          prev.map((usr) =>
                            usr.id === u.id
                              ? { ...usr, role: e.target.value }
                              : usr
                          )
                        );
                      }}
                      id={`role-select-${u.id}`}
                    >
                      {["viewer", "sub", "moderator", "admin"].map((role) => (
                        <option key={role} value={role}>
                          {role}
                        </option>
                      ))}
                    </select>
                  </td>
                  <td>
                    <time>{u.created_at || "N/A"}</time>
                  </td>
                </tr>
              ))}
              {users.length === 0 && (
                <tr>
                  <td colSpan="4" className="text-center">
                    Aucun utilisateur
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </figure>
      </section>
    </article>
  );
}
