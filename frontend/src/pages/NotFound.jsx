import { Link } from "react-router-dom";

export default function NotFound() {
  return (
    <section className="form-page" id="not-found-page">
      <h1>404</h1>
      <p className="not-found-text">
        La page que vous cherchez n'existe pas ou a été déplacée.
      </p>
      <Link to="/" className="btn-primary">
        Retour au Hub
      </Link>
    </section>
  );
}
