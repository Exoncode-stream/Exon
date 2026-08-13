import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { login as apiLogin, register as apiRegister } from "../services/api";
import { useAuth } from "../context/AuthContext";
import FormMessage from "./FormMessage";

export default function AuthForm({ mode = "login" }) {
  const isLogin = mode === "login";
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
    setMessage(isLogin ? "Connexion en cours…" : "Inscription en cours…");
    setMessageType("");

    try {
      if (isLogin) {
        const result = await apiLogin(username, password);
        setMessage("Connecté !");
        setMessageType("success");
        loginUser(result.token, result.username, result.role);
        setTimeout(() => navigate("/profile"), 800);
      } else {
        const result = await apiRegister(username, password);
        setMessage(result.message || "Compte créé !");
        setMessageType("success");
        setTimeout(() => navigate("/login"), 1500);
      }
    } catch (err) {
      setMessage(err.message);
      setMessageType("error");
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <section className="form-page">
      <h1>{isLogin ? "login" : "register"}</h1>

      <form
        onSubmit={handleSubmit}
        className="form-card"
        id={isLogin ? "login-form" : "register-form"}
      >
        <fieldset className="form-group">
          <label htmlFor={isLogin ? "login-username" : "reg-username"}>
            {isLogin ? "Identifiant" : "Nom d'utilisateur (min 3 car.)"}
          </label>
          <input
            type="text"
            id={isLogin ? "login-username" : "reg-username"}
            placeholder={isLogin ? "admin" : "john_doe"}
            value={username}
            onChange={(e) => setUsername(e.target.value)}
            required
            minLength={isLogin ? undefined : 3}
            autoComplete="username"
          />
        </fieldset>

        <fieldset className="form-group">
          <label htmlFor={isLogin ? "login-password" : "reg-password"}>
            {isLogin ? "Mot de passe" : "Mot de passe (min 5 car.)"}
          </label>
          <input
            type="password"
            id={isLogin ? "login-password" : "reg-password"}
            placeholder="••••••••"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            required
            minLength={isLogin ? undefined : 5}
            autoComplete={isLogin ? "current-password" : "new-password"}
          />
        </fieldset>

        <button
          type="submit"
          className="btn-primary"
          disabled={submitting}
          id={isLogin ? "login-submit" : "register-submit"}
        >
          {submitting
            ? isLogin ? "Connexion…" : "Inscription…"
            : isLogin ? "Se connecter" : "S'inscrire"}
        </button>
      </form>

      <FormMessage message={message} type={messageType} />

      <p className="form-alt">
        <Link to={isLogin ? "/register" : "/login"} className="text-link">
          {isLogin ? "Pas encore de compte ? S'inscrire" : "Déjà un compte ? Se connecter"}
        </Link>
      </p>
    </section>
  );
}
