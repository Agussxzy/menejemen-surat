import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import 'bootstrap/dist/css/bootstrap.min.css';

// Import pages
import LoginPage from './pages/LoginPage';
import DashboardPage from './pages/DashboardPage';
import SuratMasukPage from './pages/SuratMasukPage';
import SuratKeluarPage from './pages/SuratKeluarPage';
import DisposisiPage from './pages/DisposisiPage';
import LaporanPage from './pages/LaporanPage';

// Import components
import NavbarComponent from './components/NavbarComponent';
import SidebarComponent from './components/SidebarComponent';

function App() {
  return (
    <Router>
      <div className="App">
        <Routes>
          {/* Public routes */}
          <Route path="/login" element={<LoginPage />} />
          
          {/* Protected routes */}
          <Route path="/" element={
            <>
              <NavbarComponent />
              <div className="container-fluid">
                <div className="row">
                  <SidebarComponent />
                  <main className="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                    <DashboardPage />
                  </main>
                </div>
              </div>
            </>
          } />
          
          <Route path="/surat-masuk" element={
            <>
              <NavbarComponent />
              <div className="container-fluid">
                <div className="row">
                  <SidebarComponent />
                  <main className="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                    <SuratMasukPage />
                  </main>
                </div>
              </div>
            </>
          } />
          
          <Route path="/surat-keluar" element={
            <>
              <NavbarComponent />
              <div className="container-fluid">
                <div className="row">
                  <SidebarComponent />
                  <main className="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                    <SuratKeluarPage />
                  </main>
                </div>
              </div>
            </>
          } />
          
          <Route path="/disposisi" element={
            <>
              <NavbarComponent />
              <div className="container-fluid">
                <div className="row">
                  <SidebarComponent />
                  <main className="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                    <DisposisiPage />
                  </main>
                </div>
              </div>
            </>
          } />
          
          <Route path="/laporan" element={
            <>
              <NavbarComponent />
              <div className="container-fluid">
                <div className="row">
                  <SidebarComponent />
                  <main className="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                    <LaporanPage />
                  </main>
                </div>
              </div>
            </>
          } />
        </Routes>
      </div>
    </Router>
  );
}

export default App;