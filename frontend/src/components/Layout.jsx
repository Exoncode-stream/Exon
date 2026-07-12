import { Outlet } from 'react-router-dom';
import Navbar from './Navbar';

export default function Layout() {
  return (
    <>
      <Navbar />
      <main className="page-content">
        <Outlet />
      </main>
      <footer className="site-footer">
        <p>Built with React, Laravel &amp; SQLite</p>
      </footer>
    </>
  );
}
