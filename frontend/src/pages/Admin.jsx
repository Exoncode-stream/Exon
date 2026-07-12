import { useState, useEffect, useCallback } from "react";
import {
  addVideo,
  addArticle,
  fetchUsers,
  updateUserRole,
} from "../services/api";
import FormMessage from "../components/FormMessage";

export default function Admin() {
  /* ─── Video Form ─── */
  const [videoTitle, setVideoTitle] = useState("");
  const [youtubeId, setYoutubeId] = useState("");
  const [category, setCategory] = useState("");
  const [videoMsg, setVideoMsg] = useState("");
  const [videoMsgType, setVideoMsgType] = useState("");

  /* ─── Article Form ─── */
  const [articleTitle, setArticleTitle] = useState("");
  const [articleContent, setArticleContent] = useState("");
  const [articleMsg, setArticleMsg] = useState("");
  const [articleMsgType, setArticleMsgType] = useState("");

  /* ─── Users ─── */
  const [users, setUsers] = useState([]);

  const loadUsers = useCallback(async () => {
    try {
      const data = await fetchUsers();
      setUsers(data.users || []);
    } catch (err) {
      console.error("Failed to load users", err);
    }
  }, []);

  useEffect(() => {
    loadUsers();
  }, [loadUsers]);

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
    } catch (err) {
      setVideoMsg(err.message);
      setVideoMsgType("error");
    }
  }

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
    } catch (err) {
      setArticleMsg(err.message);
      setArticleMsgType("error");
    }
  }

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

      {/* ─── Add Video ─── */}
      <section className="admin-section" id="add-video-section">
        <h2 className="section-title">Ajouter une Vidéo</h2>
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
            Ajouter
          </button>
        </form>
        <FormMessage message={videoMsg} type={videoMsgType} />
      </section>

      {/* ─── Add Article ─── */}
      <section className="admin-section" id="add-article-section">
        <h2 className="section-title">Ajouter un Article</h2>
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
            Ajouter
          </button>
        </form>
        <FormMessage message={articleMsg} type={articleMsgType} />
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
