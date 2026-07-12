import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { login as apiLogin } from "../services/api";
import { useAuth } from "../context/AuthContext";
import FormMessage from "../components/FormMessage";

export default function Login() {
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [message, setMessage] = useState("");
  const [messageType, setMessageType] = useState("");
  const [submitting, setSubmitting] = useState(false);

  const { loginUser } = useAuth();
  const navigate = useNavigate();

  async function handleSubmit(e) {
    e.preventDefault();
    setSubmitting(true);
    setMessage("Connexion en cours…");
    setMessageType("");

    try {
      const result = await apiLogin(username, password);
      setMessage("Connecté !");
      setMessageType("success");
      loginUser(result.token, result.username, result.role);
      setTimeout(() => navigate("/profile"), 800);
    } catch (err) {
      setMessage(err.message);
      setMessageType("error");
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <section className="form-page">
      <h1>login</h1>

      <form onSubmit={handleSubmit} className="form-card" id="login-form">
        <fieldset className="form-group">
          <label htmlFor="login-username">Identifiant</label>
          <input
            type="text"
            id="login-username"
            placeholder="admin"
            value={username}
            onChange={(e) => setUsername(e.target.value)}
            required
            autoComplete="username"
          />
        </fieldset>

        <fieldset className="form-group">
          <label htmlFor="login-password">Mot de passe</label>
          <input
            type="password"
            id="login-password"
            placeholder="••••••••"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            required
            autoComplete="current-password"
          />
        </fieldset>

        <button
          type="submit"
          className="btn-primary"
          disabled={submitting}
          id="login-submit"
        >
          {submitting ? "Connexion…" : "Se connecter"}
        </button>
      </form>

      <FormMessage message={message} type={messageType} />

      <p className="form-alt">
        <Link to="/register" className="text-link">
          Pas encore de compte ? S'inscrire
        </Link>
      </p>
    </section>
  );
}
