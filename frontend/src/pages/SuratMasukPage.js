import React, { useState, useEffect } from 'react';
import api from '../services/api';

const SuratMasukPage = () => {
  const [suratMasukList, setSuratMasukList] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [search, setSearch] = useState('');
  const [showModal, setShowModal] = useState(false);
  const [formData, setFormData] = useState({
    nomor_surat: '',
    tanggal_surat: '',
    tanggal_diterima: '',
    pengirim: '',
    perihal: '',
    klasifikasi: 'umum',
    status: 'baru',
    catatan: '',
    file_surat: null
  });
  const [isEditing, setIsEditing] = useState(false);
  const [editId, setEditId] = useState(null);

  const fetchSuratMasuk = async (page = 1) => {
    try {
      setLoading(true);
      const params = {
        page: page,
        per_page: 10
      };
      
      if (search) {
        params.search = search;
      }
      
      const response = await api.get('/surat-masuk', { params });
      setSuratMasukList(response.data.data.data);
      setCurrentPage(response.data.data.current_page);
      setTotalPages(response.data.data.last_page);
    } catch (err) {
      setError(err.response?.data?.message || 'Gagal mengambil data surat masuk');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchSuratMasuk(currentPage);
  }, [currentPage]);

  const handleSearch = (e) => {
    e.preventDefault();
    setCurrentPage(1);
    fetchSuratMasuk(1);
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData({
      ...formData,
      [name]: value
    });
  };

  const handleFileChange = (e) => {
    setFormData({
      ...formData,
      file_surat: e.target.files[0]
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    
    try {
      const formPayload = new FormData();
      Object.keys(formData).forEach(key => {
        if (key !== 'file_surat' || formData[key]) {
          formPayload.append(key, formData[key]);
        }
      });

      if (isEditing) {
        await api.put(`/surat-masuk/${editId}`, formPayload, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });
      } else {
        await api.post('/surat-masuk', formPayload, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });
      }
      
      setShowModal(false);
      resetForm();
      fetchSuratMasuk(currentPage);
    } catch (err) {
      setError(err.response?.data?.message || 'Gagal menyimpan data surat masuk');
    }
  };

  const resetForm = () => {
    setFormData({
      nomor_surat: '',
      tanggal_surat: '',
      tanggal_diterima: '',
      pengirim: '',
      perihal: '',
      klasifikasi: 'umum',
      status: 'baru',
      catatan: '',
      file_surat: null
    });
    setIsEditing(false);
    setEditId(null);
  };

  const handleEdit = (surat) => {
    setFormData({
      nomor_surat: surat.nomor_surat,
      tanggal_surat: surat.tanggal_surat,
      tanggal_diterima: surat.tanggal_diterima,
      pengirim: surat.pengirim,
      perihal: surat.perihal,
      klasifikasi: surat.klasifikasi,
      status: surat.status,
      catatan: surat.catatan || '',
      file_surat: null
    });
    setIsEditing(true);
    setEditId(surat.id);
    setShowModal(true);
  };

  const handleDelete = async (id) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus surat ini?')) {
      try {
        await api.delete(`/surat-masuk/${id}`);
        fetchSuratMasuk(currentPage);
      } catch (err) {
        setError(err.response?.data?.message || 'Gagal menghapus surat masuk');
      }
    }
  };

  const handleDownload = async (id, fileName) => {
    try {
      const response = await api.get(`/surat-masuk/${id}/download`, {
        responseType: 'blob'
      });
      
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', fileName);
      document.body.appendChild(link);
      link.click();
      link.remove();
    } catch (err) {
      setError('Gagal mengunduh file');
    }
  };

  if (loading) return <div className="text-center mt-5">Memuat data...</div>;

  return (
    <div className="container-fluid">
      <div className="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 className="h2">Surat Masuk</h1>
        <button 
          className="btn btn-primary" 
          onClick={() => {
            resetForm();
            setShowModal(true);
          }}
        >
          Tambah Surat Masuk
        </button>
      </div>

      {error && (
        <div className="alert alert-danger" role="alert">
          {error}
        </div>
      )}

      <form onSubmit={handleSearch} className="row g-3 mb-4">
        <div className="col-md-8">
          <input
            type="text"
            className="form-control"
            placeholder="Cari surat masuk..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
          />
        </div>
        <div className="col-md-4">
          <button type="submit" className="btn btn-primary w-100">Cari</button>
        </div>
      </form>

      <div className="table-responsive">
        <table className="table table-striped table-hover">
          <thead>
            <tr>
              <th>Nomor Surat</th>
              <th>Tanggal Surat</th>
              <th>Pengirim</th>
              <th>Perihal</th>
              <th>Klasifikasi</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            {suratMasukList.length > 0 ? (
              suratMasukList.map((surat) => (
                <tr key={surat.id}>
                  <td>{surat.nomor_surat}</td>
                  <td>{new Date(surat.tanggal_surat).toLocaleDateString()}</td>
                  <td>{surat.pengirim}</td>
                  <td>{surat.perihal}</td>
                  <td>
                    <span className={`badge ${
                      surat.klasifikasi === 'rahasia' ? 'bg-danger' :
                      surat.klasifikasi === 'penting' ? 'bg-warning' : 'bg-secondary'
                    }`}>
                      {surat.klasifikasi}
                    </span>
                  </td>
                  <td>
                    <span className={`badge ${
                      surat.status === 'baru' ? 'bg-primary' :
                      surat.status === 'diproses' ? 'bg-info' : 'bg-success'
                    }`}>
                      {surat.status}
                    </span>
                  </td>
                  <td>
                    <div className="btn-group" role="group">
                      {surat.file_surat && (
                        <button
                          type="button"
                          className="btn btn-sm btn-outline-primary"
                          onClick={() => handleDownload(surat.id, surat.file_surat)}
                        >
                          Unduh
                        </button>
                      )}
                      <button
                        type="button"
                        className="btn btn-sm btn-outline-warning"
                        onClick={() => handleEdit(surat)}
                      >
                        Edit
                      </button>
                      <button
                        type="button"
                        className="btn btn-sm btn-outline-danger"
                        onClick={() => handleDelete(surat.id)}
                      >
                        Hapus
                      </button>
                    </div>
                  </td>
                </tr>
              ))
            ) : (
              <tr>
                <td colSpan="7" className="text-center">Tidak ada data surat masuk</td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      {/* Pagination */}
      {totalPages > 1 && (
        <nav aria-label="Surat Masuk pagination">
          <ul className="pagination justify-content-center">
            <li className={`page-item ${currentPage === 1 ? 'disabled' : ''}`}>
              <button 
                className="page-link" 
                onClick={() => setCurrentPage(prev => Math.max(prev - 1, 1))}
              >
                Previous
              </button>
            </li>
            
            {[...Array(totalPages)].map((_, i) => (
              <li key={i} className={`page-item ${currentPage === i + 1 ? 'active' : ''}`}>
                <button 
                  className="page-link" 
                  onClick={() => setCurrentPage(i + 1)}
                >
                  {i + 1}
                </button>
              </li>
            ))}
            
            <li className={`page-item ${currentPage === totalPages ? 'disabled' : ''}`}>
              <button 
                className="page-link" 
                onClick={() => setCurrentPage(prev => Math.min(prev + 1, totalPages))}
              >
                Next
              </button>
            </li>
          </ul>
        </nav>
      )}

      {/* Modal Form */}
      {showModal && (
        <div className="modal fade show" style={{ display: 'block' }} tabIndex="-1">
          <div className="modal-dialog modal-lg">
            <div className="modal-content">
              <div className="modal-header">
                <h5 className="modal-title">{isEditing ? 'Edit Surat Masuk' : 'Tambah Surat Masuk'}</h5>
                <button type="button" className="btn-close" onClick={() => setShowModal(false)}></button>
              </div>
              <form onSubmit={handleSubmit}>
                <div className="modal-body">
                  <div className="row">
                    <div className="col-md-6">
                      <div className="mb-3">
                        <label className="form-label">Nomor Surat *</label>
                        <input
                          type="text"
                          className="form-control"
                          name="nomor_surat"
                          value={formData.nomor_surat}
                          onChange={handleInputChange}
                          required
                        />
                      </div>
                    </div>
                    <div className="col-md-6">
                      <div className="mb-3">
                        <label className="form-label">Tanggal Surat *</label>
                        <input
                          type="date"
                          className="form-control"
                          name="tanggal_surat"
                          value={formData.tanggal_surat}
                          onChange={handleInputChange}
                          required
                        />
                      </div>
                    </div>
                  </div>
                  
                  <div className="row">
                    <div className="col-md-6">
                      <div className="mb-3">
                        <label className="form-label">Tanggal Diterima *</label>
                        <input
                          type="date"
                          className="form-control"
                          name="tanggal_diterima"
                          value={formData.tanggal_diterima}
                          onChange={handleInputChange}
                          required
                        />
                      </div>
                    </div>
                    <div className="col-md-6">
                      <div className="mb-3">
                        <label className="form-label">Pengirim *</label>
                        <input
                          type="text"
                          className="form-control"
                          name="pengirim"
                          value={formData.pengirim}
                          onChange={handleInputChange}
                          required
                        />
                      </div>
                    </div>
                  </div>
                  
                  <div className="mb-3">
                    <label className="form-label">Perihal *</label>
                    <input
                      type="text"
                      className="form-control"
                      name="perihal"
                      value={formData.perihal}
                      onChange={handleInputChange}
                      required
                    />
                  </div>
                  
                  <div className="row">
                    <div className="col-md-6">
                      <div className="mb-3">
                        <label className="form-label">Klasifikasi</label>
                        <select
                          className="form-select"
                          name="klasifikasi"
                          value={formData.klasifikasi}
                          onChange={handleInputChange}
                        >
                          <option value="rahasia">Rahasia</option>
                          <option value="penting">Penting</option>
                          <option value="umum">Umum</option>
                        </select>
                      </div>
                    </div>
                    <div className="col-md-6">
                      <div className="mb-3">
                        <label className="form-label">Status</label>
                        <select
                          className="form-select"
                          name="status"
                          value={formData.status}
                          onChange={handleInputChange}
                        >
                          <option value="baru">Baru</option>
                          <option value="diproses">Diproses</option>
                          <option value="selesai">Selesai</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  
                  <div className="mb-3">
                    <label className="form-label">Catatan</label>
                    <textarea
                      className="form-control"
                      name="catatan"
                      value={formData.catatan}
                      onChange={handleInputChange}
                      rows="3"
                    ></textarea>
                  </div>
                  
                  <div className="mb-3">
                    <label className="form-label">File Surat</label>
                    <input
                      type="file"
                      className="form-control"
                      name="file_surat"
                      onChange={handleFileChange}
                      accept=".pdf,.jpg,.jpeg,.png"
                    />
                    {isEditing && suratMasukList.find(s => s.id === editId)?.file_surat && (
                      <small className="form-text text-muted">
                        File saat ini: {suratMasukList.find(s => s.id === editId)?.file_surat}
                      </small>
                    )}
                  </div>
                </div>
                <div className="modal-footer">
                  <button type="button" className="btn btn-secondary" onClick={() => setShowModal(false)}>Batal</button>
                  <button type="submit" className="btn btn-primary">{isEditing ? 'Update' : 'Simpan'}</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      )}
      
      {showModal && (
        <div className="modal-backdrop fade show"></div>
      )}
    </div>
  );
};

export default SuratMasukPage;