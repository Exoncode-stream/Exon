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
import AdminCrudSection from "../components/AdminCrudSection";
import { formatDate } from "../utils/formatters";

export default function Admin() {
  const [links, setLinks] = useState([]);
  const [videos, setVideos] = useState([]);
  const [articles, setArticles] = useState([]);
  const [users, setUsers] = useState([]);

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
      <AdminCrudSection
        sectionId="links"
        title="Gestion des Liens"
        items={links}
        fields={[
          { name: "name", label: "Nom du lien", placeholder: "Ex: GitHub, Twitter, Discord..." },
          { name: "url", label: "URL", type: "url", placeholder: "https://github.com/mon-profil" },
        ]}
        columns={[
          { header: "ID", render: (item) => item.id },
          { header: "Nom", render: (item) => item.name },
          {
            header: "URL",
            render: (item) => (
              <a href={item.url} target="_blank" rel="noopener noreferrer" style={{ color: "var(--accent-color, #00ff66)" }}>
                {item.url}
              </a>
            ),
          },
        ]}
        addApiFn={addLink}
        updateApiFn={updateLink}
        deleteApiFn={deleteLink}
        onItemAdded={loadLinks}
        onItemUpdated={loadLinks}
        onItemDeleted={(id) => setLinks((prev) => prev.filter((l) => l.id !== id))}
        emptyText="Aucun lien enregistré"
      />

      {/* ─── Videos Management ─── */}
      <AdminCrudSection
        sectionId="videos"
        title="Gestion des Vidéos"
        items={videos}
        fields={[
          { name: "title", label: "Titre", placeholder: "Mon super tuto" },
          { name: "youtube_id", label: "URL ou ID YouTube", placeholder: "https://youtube.com/watch?v=..." },
          { name: "category", label: "Catégorie", placeholder: "Web development" },
        ]}
        columns={[
          { header: "ID", render: (item) => item.id },
          { header: "Titre", render: (item) => item.title },
          { header: "YouTube ID", render: (item) => <code>{item.youtube_id}</code> },
          { header: "Catégorie", render: (item) => <mark className="badge">{item.category}</mark> },
        ]}
        addApiFn={addVideo}
        updateApiFn={updateVideo}
        deleteApiFn={deleteVideo}
        onItemAdded={loadVideos}
        onItemUpdated={loadVideos}
        onItemDeleted={(id) => setVideos((prev) => prev.filter((v) => v.id !== id))}
        emptyText="Aucune vidéo enregistrée"
      />

      {/* ─── Articles Management ─── */}
      <AdminCrudSection
        sectionId="articles"
        title="Gestion des Articles"
        items={articles}
        fields={[
          { name: "title", label: "Titre", placeholder: "Introduction à React" },
          { name: "content", label: "Contenu", type: "textarea", rows: 6, placeholder: "Rédigez votre article ici…" },
        ]}
        columns={[
          { header: "ID", render: (item) => item.id },
          { header: "Titre", render: (item) => item.title },
          { header: "Aperçu", render: (item) => (item.content ? item.content.slice(0, 60) + "…" : "") },
        ]}
        addApiFn={addArticle}
        updateApiFn={updateArticle}
        deleteApiFn={deleteArticle}
        onItemAdded={loadArticles}
        onItemUpdated={loadArticles}
        onItemDeleted={(id) => setArticles((prev) => prev.filter((a) => a.id !== id))}
        emptyText="Aucun article enregistré"
      />

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
                    <time>{formatDate(u.created_at)}</time>
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
