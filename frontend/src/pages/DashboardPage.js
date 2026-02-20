import React, { useState, useEffect } from 'react';

const DashboardPage = () => {
  const [user, setUser] = useState(null);

  useEffect(() => {
    const userData = localStorage.getItem('user');
    if (userData) {
      setUser(JSON.parse(userData));
    }
  }, []);

  return (
    <div className="container-fluid">
      <div className="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 className="h2">Dashboard</h1>
      </div>

      <div className="row">
        <div className="col-md-12">
          <div className="card">
            <div className="card-header">
              <h5>Selamat Datang di Sistem Manajemen Surat</h5>
            </div>
            <div className="card-body">
              {user && (
                <p>Halo <strong>{user.name}</strong>, Anda login sebagai <strong>{user.role}</strong>.</p>
              )}
              <p>Pilih menu di sebelah kiri untuk mengelola surat masuk, surat keluar, disposisi, dan laporan.</p>
              
              <div className="row">
                <div className="col-md-3">
                  <div className="card bg-primary text-white">
                    <div className="card-body">
                      <h5 className="card-title">Surat Masuk</h5>
                      <p className="card-text">Kelola surat masuk</p>
                    </div>
                  </div>
                </div>
                <div className="col-md-3">
                  <div className="card bg-success text-white">
                    <div className="card-body">
                      <h5 className="card-title">Surat Keluar</h5>
                      <p className="card-text">Kelola surat keluar</p>
                    </div>
                  </div>
                </div>
                <div className="col-md-3">
                  <div className="card bg-info text-white">
                    <div className="card-body">
                      <h5 className="card-title">Disposisi</h5>
                      <p className="card-text">Atur disposisi surat</p>
                    </div>
                  </div>
                </div>
                <div className="col-md-3">
                  <div className="card bg-warning text-white">
                    <div className="card-body">
                      <h5 className="card-title">Laporan</h5>
                      <p className="card-text">Generate laporan</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default DashboardPage;