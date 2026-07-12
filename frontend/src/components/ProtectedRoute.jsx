import { Navigate } from "react-router-dom";
import { useAuth } from "../context/AuthContext";

export default function ProtectedRoute({ children, requiredRoles }) {
  const { user, loading } = useAuth();

  if (loading) {
    return (
      <section className="loading-screen" aria-busy="true">
        <progress className="spinner" />
        <p>Chargement…</p>
      </section>
    );
  }

  if (!user) {
    return <Navigate to="/login" replace />;
  }

  if (requiredRoles && !requiredRoles.includes(user.role)) {
    return <Navigate to="/profile" replace />;
  }

  return children;
}
