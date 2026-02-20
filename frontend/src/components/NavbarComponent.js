import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';

const NavbarComponent = () => {
  const [user, setUser] = useState(null);
  const navigate = useNavigate();

  useEffect(() => {
    const userData = localStorage.getItem('user');
    if (userData) {
      setUser(JSON.parse(userData));
    }
  }, []);

  const handleLogout = () => {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    navigate('/login');
  };

  return (
    <nav className="navbar navbar-expand-lg navbar-dark bg-primary">
      <div className="container-fluid">
        <a className="navbar-brand" href="/">Sistem Surat</a>
        
        <div className="navbar-nav ms-auto">
          {user ? (
            <>
              <span className="navbar-text me-3">
                Halo, {user.name} ({user.role})
              </span>
              <button 
                className="btn btn-outline-light" 
                onClick={handleLogout}
              >
                Logout
              </button>
            </>
          ) : (
            <a className="btn btn-outline-light" href="/login">Login</a>
          )}
        </div>
      </div>
    </nav>
  );
};

export default NavbarComponent;