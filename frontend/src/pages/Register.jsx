import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { register as apiRegister } from "../services/api";
import FormMessage from "../components/FormMessage";

export default function Register() {
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [message, setMessage] = useState("");
  const [messageType, setMessageType] = useState("");
  const [submitting, setSubmitting] = useState(false);

  const navigate = useNavigate();

  async function handleSubmit(e) {
    e.preventDefault();
    setSubmitting(true);
    setMessage("Inscription en cours…");
    setMessageType("");

    try {
      const result = await apiRegister(username, password);
      setMessage(result.message || "Compte créé !");
      setMessageType("success");
      setTimeout(() => navigate("/login"), 1500);
    } catch (err) {
      setMessage(err.message);
      setMessageType("error");
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <section className="form-page">
      <h1>register</h1>

      <form onSubmit={handleSubmit} className="form-card" id="register-form">
        <fieldset className="form-group">
          <label htmlFor="reg-username">Nom d'utilisateur (min 3 car.)</label>
          <input
            type="text"
            id="reg-username"
            placeholder="john_doe"
            value={username}
            onChange={(e) => setUsername(e.target.value)}
            required
            minLength={3}
            autoComplete="username"
          />
        </fieldset>

        <fieldset className="form-group">
          <label htmlFor="reg-password">Mot de passe (min 5 car.)</label>
          <input
            type="password"
            id="reg-password"
            placeholder="••••••••"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            required
            minLength={5}
            autoComplete="new-password"
          />
        </fieldset>

        <button
          type="submit"
          className="btn-primary"
          disabled={submitting}
          id="register-submit"
        >
          {submitting ? "Inscription…" : "S'inscrire"}
        </button>
      </form>

      <FormMessage message={message} type={messageType} />

      <p className="form-alt">
        <Link to="/login" className="text-link">
          Déjà un compte ? Se connecter
        </Link>
      </p>
    </section>
  );
}
