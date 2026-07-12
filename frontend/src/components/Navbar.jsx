import { Link, useLocation } from "react-router-dom";
import { useAuth } from "../context/AuthContext";

export default function Navbar() {
  const { user } = useAuth();
  const location = useLocation();

  function isActive(path) {
    return location.pathname === path ? "nav-link active" : "nav-link";
  }

  return (
    <nav className="navbar" id="main-navbar">
      <header className="navbar-inner">
        <Link to="/" className="navbar-brand">
          <strong className="brand-accent">&gt;</strong> exon
        </Link>

        <ul className="navbar-links">
          <li>
            <Link to="/" className={isActive("/")}>
              Hub
            </Link>
          </li>

          {user ? (
            <>
              <li>
                <Link to="/profile" className={isActive("/profile")}>
                  {user.username}
                </Link>
              </li>
              {(user.role === "admin" || user.role === "moderator") && (
                <li>
                  <Link to="/admin" className={isActive("/admin")}>
                    Admin
                  </Link>
                </li>
              )}
            </>
          ) : (
            <>
              <li>
                <Link to="/login" className={isActive("/login")}>
                  Login
                </Link>
              </li>
              <li>
                <Link to="/register" className={isActive("/register")}>
                  Register
                </Link>
              </li>
            </>
          )}
        </ul>
      </header>
    </nav>
  );
}
