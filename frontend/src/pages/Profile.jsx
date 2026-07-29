import { useState, useEffect } from "react";
import { useNavigate, Link } from "react-router-dom";
import { useAuth } from "../context/AuthContext";
import { fetchProfile, changePassword } from "../services/api";

/**
 * Profile Page Component
 * Allows authenticated users to view profile details, activity statistics,
 * and update their password securely.
 */
export default function Profile() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  const [profileData, setProfileData] = useState(null);
  const [loading, setLoading] = useState(true);

  /* Password Form State */
  const [currentPassword, setCurrentPassword] = useState("");
  const [newPassword, setNewPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");

  const [message, setMessage] = useState(null);
  const [error, setError] = useState(null);
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    async function load() {
      try {
        const data = await fetchProfile();
        setProfileData(data);
      } catch (err) {
        console.error("Impossible de charger le profil", err);
      } finally {
        setLoading(false);
      }
    }
    load();
  }, []);

  function handleLogout() {
    logout();
    navigate("/");
  }

  async function handlePasswordChange(e) {
    e.preventDefault();
    setMessage(null);
    setError(null);

    if (newPassword !== confirmPassword) {
      setError("La confirmation du mot de passe ne correspond pas.");
      return;
    }

    if (newPassword.length < 8) {
      setError("Le nouveau mot de passe doit contenir au moins 8 caractères.");
      return;
    }

    setSubmitting(true);

    try {
      const res = await changePassword(currentPassword, newPassword, confirmPassword);
      setMessage(res.message);
      setCurrentPassword("");
      setNewPassword("");
      setConfirmPassword("");
    } catch (err) {
      setError(err.message);
    } finally {
      setSubmitting(false);
    }
  }

  if (!user) return null;

  return (
    <section className="form-page container fade-in" id="profile-page">
      <h1>my-profile</h1>

      {/* User Overview */}
      <dl className="profile-card" id="profile-info">
        <dt className="profile-label">Utilisateur</dt>
        <dd className="profile-value" id="profile-username">
          @{user.username}
        </dd>

        <dt className="profile-label">Rôle</dt>
        <dd id="profile-role">
          <mark className="badge">{user.role}</mark>
        </dd>

        {profileData?.created_at && (
          <>
            <dt className="profile-label">Membre depuis</dt>
            <dd className="profile-value" id="profile-created-at">
              {new Date(profileData.created_at).toLocaleDateString()}
            </dd>
          </>
        )}
      </dl>

      {/* Activity Statistics */}
      {profileData?.stats && (
        <section className="profile-stats-grid" id="profile-stats">
          <div className="stat-card">
            <span className="stat-number">{profileData.stats.comments_count}</span>
            <span className="stat-label">Commentaires créés</span>
          </div>
          <div className="stat-card">
            <span className="stat-number">{profileData.stats.likes_count}</span>
            <span className="stat-label">Contenus aimés</span>
          </div>
        </section>
      )}

      {/* Security / Password Change Form */}
      <section className="profile-section" id="password-change-section">
        <h2>Changer le mot de passe</h2>

        {message && (
          <aside className="success-banner" role="status" id="pwd-success">
            <p>{message}</p>
          </aside>
        )}

        {error && (
          <aside className="error-banner" role="alert" id="pwd-error">
            <p>{error}</p>
          </aside>
        )}

        <form onSubmit={handlePasswordChange} className="form-card" id="change-password-form">
          <label htmlFor="current_password">Mot de passe actuel</label>
          <input
            type="password"
            id="current_password"
            value={currentPassword}
            onChange={(e) => setCurrentPassword(e.target.value)}
            required
          />

          <label htmlFor="new_password">Nouveau mot de passe (min 8 caractères)</label>
          <input
            type="password"
            id="new_password"
            value={newPassword}
            onChange={(e) => setNewPassword(e.target.value)}
            required
            minLength={8}
          />

          <label htmlFor="confirm_password">Confirmer le nouveau mot de passe</label>
          <input
            type="password"
            id="confirm_password"
            value={confirmPassword}
            onChange={(e) => setConfirmPassword(e.target.value)}
            required
          />

          <button type="submit" className="btn-primary" disabled={submitting} id="update-password-btn">
            {submitting ? "Mise à jour…" : "Mettre à jour le mot de passe"}
          </button>
        </form>
      </section>

      {/* Actions */}
      <nav className="profile-actions" style={{ marginTop: "2rem" }}>
        <button onClick={handleLogout} className="btn-danger" id="logout-btn">
          Déconnexion
        </button>
        <Link to="/" className="pill-link">
          Retour au Hub
        </Link>
        {(user.role === "admin" || user.role === "moderator") && (
          <Link to="/admin" className="pill-link">
            Panneau Admin
          </Link>
        )}
      </nav>
    </section>
  );
}
