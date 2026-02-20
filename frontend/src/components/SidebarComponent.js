import React, { useState, useEffect } from 'react';
import { Link, useLocation } from 'react-router-dom';

const SidebarComponent = () => {
  const [user, setUser] = useState(null);
  const location = useLocation();

  useEffect(() => {
    const userData = localStorage.getItem('user');
    if (userData) {
      setUser(JSON.parse(userData));
    }
  }, []);

  // Menu items based on user role
  const getMenuItems = () => {
    if (!user) return [];

    const baseMenu = [
      { path: '/', label: 'Dashboard', icon: 'bi bi-house' },
      { path: '/surat-masuk', label: 'Surat Masuk', icon: 'bi bi-envelope' },
      { path: '/surat-keluar', label: 'Surat Keluar', icon: 'bi bi-send' },
    ];

    // Pimpinan and admin can see disposisi
    if (user.role === 'pimpinan' || user.role === 'admin') {
      baseMenu.push({ path: '/disposisi', label: 'Disposisi', icon: 'bi bi-share' });
    }

    baseMenu.push({ path: '/laporan', label: 'Laporan', icon: 'bi bi-file-earmark-bar-graph' });

    return baseMenu;
  };

  const menuItems = getMenuItems();

  return (
    <nav id="sidebarMenu" className="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
      <div className="position-sticky pt-3">
        <ul className="nav flex-column">
          {menuItems.map((item, index) => (
            <li key={index} className="nav-item">
              <Link 
                to={item.path} 
                className={`nav-link ${location.pathname === item.path ? 'active' : ''}`}
              >
                <i className={`${item.icon} me-2`}></i>
                {item.label}
              </Link>
            </li>
          ))}
        </ul>
      </div>
    </nav>
  );
};

export default SidebarComponent;