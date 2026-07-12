import { useNavigate, Link } from "react-router-dom";
import { useAuth } from "../context/AuthContext";

export default function Profile() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  function handleLogout() {
    logout();
    navigate("/");
  }

  if (!user) return null;

  return (
    <section className="form-page">
      <h1>my-profile</h1>

      <dl className="profile-card" id="profile-info">
        <dt className="profile-label">Utilisateur</dt>
        <dd className="profile-value" id="profile-username">
          {user.username}
        </dd>

        <dt className="profile-label">Rôle</dt>
        <dd id="profile-role">
          <mark className="badge">{user.role}</mark>
        </dd>
      </dl>

      <nav className="profile-actions">
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
