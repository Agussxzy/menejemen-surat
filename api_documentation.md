# Dokumentasi API Sistem Manajemen Surat & Arsip Digital Instansi

## Base URL
`http://localhost:8000/api`

## Authentication
API ini menggunakan Laravel Sanctum untuk otentikasi. Header yang diperlukan:
```
Authorization: Bearer {access_token}
Content-Type: application/json
```

---

## 1. Authentication

### Login
**Endpoint:** `POST /api/login`

**Deskripsi:** Mengautentikasi pengguna dan mengembalikan token akses.

**Headers:**
- Content-Type: application/json

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response Sukses (200):**
```json
{
  "message": "Login berhasil.",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com",
    "role": "admin",
    "role_id": 1
  },
  "access_token": "1|abc123def456...",
  "token_type": "Bearer"
}
```

**Response Error (401):**
```json
{
  "message": "Email atau password salah."
}
```

**HTTP Status Code:** 200 (Success), 401 (Unauthorized)

### Logout
**Endpoint:** `POST /api/logout`

**Deskripsi:** Menghapus token akses pengguna saat ini.

**Headers:**
- Authorization: Bearer {access_token}

**Response Sukses (200):**
```json
{
  "message": "Logout berhasil."
}
```

**HTTP Status Code:** 200 (Success)

### Get User Info
**Endpoint:** `GET /api/me`

**Deskripsi:** Mendapatkan informasi pengguna yang sedang login.

**Headers:**
- Authorization: Bearer {access_token}

**Response Sukses (200):**
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com",
    "email_verified_at": null,
    "role_id": 1,
    "phone": null,
    "address": null,
    "avatar": null,
    "is_active": true,
    "created_at": "2023-01-01T00:00:00.000000Z",
    "updated_at": "2023-01-01T00:00:00.000000Z"
  }
}
```

**HTTP Status Code:** 200 (Success), 401 (Unauthorized)

---

## 2. Surat Masuk

### Get All Surat Masuk
**Endpoint:** `GET /api/surat-masuk`

**Deskripsi:** Mendapatkan daftar surat masuk dengan fitur pencarian dan filter.

**Headers:**
- Authorization: Bearer {access_token}

**Query Parameters (Opsional):**
- search: kata kunci pencarian
- tanggal_mulai: tanggal awal (format: YYYY-MM-DD)
- tanggal_selesai: tanggal akhir (format: YYYY-MM-DD)
- klasifikasi: rahasia/penting/umum
- status: baru/diproses/selesai
- per_page: jumlah item per halaman (default: 10)

**Response Sukses (200):**
```json
{
  "message": "Data surat masuk berhasil diambil.",
  "data": {
    "data": [
      {
        "id": 1,
        "nomor_surat": "001/INSTANSI/I/2023",
        "tanggal_surat": "2023-01-15",
        "tanggal_diterima": "2023-01-16",
        "pengirim": "PT ABC",
        "perihal": "Undangan Rapat",
        "klasifikasi": "umum",
        "status": "baru",
        "file_surat": "1673760000_sample.pdf",
        "catatan": "Perlu ditindaklanjuti",
        "user_id": 1,
        "created_at": "2023-01-16T08:00:00.000000Z",
        "updated_at": "2023-01-16T08:00:00.000000Z",
        "user": {
          "id": 1,
          "name": "John Doe",
          "email": "user@example.com"
        }
      }
    ],
    "links": {
      "first": "http://localhost:8000/api/surat-masuk?page=1",
      "last": "http://localhost:8000/api/surat-masuk?page=1",
      "prev": null,
      "next": null
    },
    "meta": {
      "current_page": 1,
      "from": 1,
      "last_page": 1,
      "links": [...],
      "path": "http://localhost:8000/api/surat-masuk",
      "per_page": 10,
      "to": 1,
      "total": 1
    }
  }
}
```

**HTTP Status Code:** 200 (Success), 401 (Unauthorized)

### Create Surat Masuk
**Endpoint:** `POST /api/surat-masuk`

**Deskripsi:** Membuat surat masuk baru.

**Headers:**
- Authorization: Bearer {access_token}

**Request Body:**
```json
{
  "nomor_surat": "002/INSTANSI/I/2023",
  "tanggal_surat": "2023-01-20",
  "tanggal_diterima": "2023-01-21",
  "pengirim": "PT XYZ",
  "perihal": "Proposal Kerjasama",
  "klasifikasi": "penting",
  "file_surat": "binary_file_data",
  "catatan": "Harap dicek segera"
}
```

**Response Sukses (201):**
```json
{
  "message": "Surat masuk berhasil ditambahkan.",
  "data": {
    "id": 2,
    "nomor_surat": "002/INSTANSI/I/2023",
    "tanggal_surat": "2023-01-20",
    "tanggal_diterima": "2023-01-21",
    "pengirim": "PT XYZ",
    "perihal": "Proposal Kerjasama",
    "klasifikasi": "penting",
    "status": "baru",
    "file_surat": "1674278400_sample.pdf",
    "catatan": "Harap dicek segera",
    "user_id": 1,
    "created_at": "2023-01-21T09:00:00.000000Z",
    "updated_at": "2023-01-21T09:00:00.000000Z",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "user@example.com"
    }
  }
}
```

**Response Error (422):**
```json
{
  "message": "Validasi gagal.",
  "errors": {
    "nomor_surat": [
      "The nomor surat has already been taken."
    ]
  }
}
```

**HTTP Status Code:** 201 (Created), 401 (Unauthorized), 422 (Unprocessable Entity)

### Get Single Surat Masuk
**Endpoint:** `GET /api/surat-masuk/{id}`

**Deskripsi:** Mendapatkan detail surat masuk tertentu.

**Headers:**
- Authorization: Bearer {access_token}

**Response Sukses (200):**
```json
{
  "message": "Data surat masuk berhasil diambil.",
  "data": {
    "id": 1,
    "nomor_surat": "001/INSTANSI/I/2023",
    "tanggal_surat": "2023-01-15",
    "tanggal_diterima": "2023-01-16",
    "pengirim": "PT ABC",
    "perihal": "Undangan Rapat",
    "klasifikasi": "umum",
    "status": "baru",
    "file_surat": "1673760000_sample.pdf",
    "catatan": "Perlu ditindaklanjuti",
    "user_id": 1,
    "created_at": "2023-01-16T08:00:00.000000Z",
    "updated_at": "2023-01-16T08:00:00.000000Z",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "user@example.com"
    },
    "disposisi": []
  }
}
```

**HTTP Status Code:** 200 (Success), 401 (Unauthorized), 404 (Not Found)

### Update Surat Masuk
**Endpoint:** `PUT /api/surat-masuk/{id}`

**Deskripsi:** Memperbarui data surat masuk.

**Headers:**
- Authorization: Bearer {access_token}

**Request Body:**
```json
{
  "nomor_surat": "001/INSTANSI/I/2023",
  "tanggal_surat": "2023-01-15",
  "tanggal_diterima": "2023-01-16",
  "pengirim": "PT ABC Updated",
  "perihal": "Undangan Rapat Baru",
  "klasifikasi": "penting",
  "status": "diproses",
  "catatan": "Sedang dalam proses"
}
```

**Response Sukses (200):**
```json
{
  "message": "Surat masuk berhasil diperbarui.",
  "data": {
    "id": 1,
    "nomor_surat": "001/INSTANSI/I/2023",
    "tanggal_surat": "2023-01-15",
    "tanggal_diterima": "2023-01-16",
    "pengirim": "PT ABC Updated",
    "perihal": "Undangan Rapat Baru",
    "klasifikasi": "penting",
    "status": "diproses",
    "file_surat": "1673760000_sample.pdf",
    "catatan": "Sedang dalam proses",
    "user_id": 1,
    "created_at": "2023-01-16T08:00:00.000000Z",
    "updated_at": "2023-01-21T10:00:00.000000Z",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "user@example.com"
    }
  }
}
```

**HTTP Status Code:** 200 (Success), 401 (Unauthorized), 404 (Not Found), 422 (Unprocessable Entity)

### Delete Surat Masuk
**Endpoint:** `DELETE /api/surat-masuk/{id}`

**Deskripsi:** Menghapus surat masuk.

**Headers:**
- Authorization: Bearer {access_token}

**Response Sukses (200):**
```json
{
  "message": "Surat masuk berhasil dihapus."
}
```

**HTTP Status Code:** 200 (Success), 401 (Unauthorized), 404 (Not Found)

### Download File Surat Masuk
**Endpoint:** `GET /api/surat-masuk/{id}/download`

**Deskripsi:** Mendownload file surat masuk.

**Headers:**
- Authorization: Bearer {access_token}

**HTTP Status Code:** 200 (Success), 401 (Unauthorized), 404 (Not Found)

---

## 3. Surat Keluar

### Get All Surat Keluar
**Endpoint:** `GET /api/surat-keluar`

**Deskripsi:** Mendapatkan daftar surat keluar dengan fitur pencarian dan filter.

**Headers:**
- Authorization: Bearer {access_token}

**Query Parameters (Opsional):**
- search: kata kunci pencarian
- tanggal_mulai: tanggal awal (format: YYYY-MM-DD)
- tanggal_selesai: tanggal akhir (format: YYYY-MM-DD)
- klasifikasi: rahasia/penting/umum
- per_page: jumlah item per halaman (default: 10)

**Response Sukses (200):**
```json
{
  "message": "Data surat keluar berhasil diambil.",
  "data": {
    "data": [
      {
        "id": 1,
        "nomor_surat": "001/INSTANSI/I/2023",
        "tanggal_surat": "2023-01-15",
        "tujuan": "Instansi X",
        "perihal": "Pemberitahuan",
        "klasifikasi": "umum",
        "tembusan": "Instansi Y, Instansi Z",
        "penandatangan": "Kepala Instansi",
        "file_surat": "1673760000_keluar.pdf",
        "user_id": 1,
        "created_at": "2023-01-15T08:00:00.000000Z",
        "updated_at": "2023-01-15T08:00:00.000000Z",
        "user": {
          "id": 1,
          "name": "John Doe",
          "email": "user@example.com"
        }
      }
    ],
    "links": {...},
    "meta": {...}
  }
}
```

**HTTP Status Code:** 200 (Success), 401 (Unauthorized)

### Create Surat Keluar
**Endpoint:** `POST /api/surat-keluar`

**Deskripsi:** Membuat surat keluar baru (nomor surat otomatis).

**Headers:**
- Authorization: Bearer {access_token}

**Request Body:**
```json
{
  "tanggal_surat": "2023-01-20",
  "tujuan": "Instansi ABC",
  "perihal": "Surat Edaran",
  "klasifikasi": "penting",
  "tembusan": "Instansi XYZ",
  "penandatangan": "Kepala Bagian",
  "file_surat": "binary_file_data"
}
```

**Response Sukses (201):**
```json
{
  "message": "Surat keluar berhasil ditambahkan.",
  "data": {
    "id": 2,
    "nomor_surat": "002/INSTANSI/I/2023",
    "tanggal_surat": "2023-01-20",
    "tujuan": "Instansi ABC",
    "perihal": "Surat Edaran",
    "klasifikasi": "penting",
    "tembusan": "Instansi XYZ",
    "penandatangan": "Kepala Bagian",
    "file_surat": "1674278400_keluar.pdf",
    "user_id": 1,
    "created_at": "2023-01-20T09:00:00.000000Z",
    "updated_at": "2023-01-20T09:00:00.000000Z",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "user@example.com"
    }
  }
}
```

**HTTP Status Code:** 201 (Created), 401 (Unauthorized), 422 (Unprocessable Entity)

### Get Single Surat Keluar
**Endpoint:** `GET /api/surat-keluar/{id}`

**Deskripsi:** Mendapatkan detail surat keluar tertentu.

**Headers:**
- Authorization: Bearer {access_token}

**Response Sukses (200):**
```json
{
  "message": "Data surat keluar berhasil diambil.",
  "data": {
    "id": 1,
    "nomor_surat": "001/INSTANSI/I/2023",
    "tanggal_surat": "2023-01-15",
    "tujuan": "Instansi X",
    "perihal": "Pemberitahuan",
    "klasifikasi": "umum",
    "tembusan": "Instansi Y, Instansi Z",
    "penandatangan": "Kepala Instansi",
    "file_surat": "1673760000_keluar.pdf",
    "user_id": 1,
    "created_at": "2023-01-15T08:00:00.000000Z",
    "updated_at": "2023-01-15T08:00:00.000000Z",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "user@example.com"
    }
  }
}
```

**HTTP Status Code:** 200 (Success), 401 (Unauthorized), 404 (Not Found)

### Update Surat Keluar
**Endpoint:** `PUT /api/surat-keluar/{id}`

**Deskripsi:** Memperbarui data surat keluar.

**Headers:**
- Authorization: Bearer {access_token}

**Request Body:**
```json
{
  "tanggal_surat": "2023-01-15",
  "tujuan": "Instansi X Updated",
  "perihal": "Pemberitahuan Baru",
  "klasifikasi": "penting",
  "tembusan": "Instansi Y",
  "penandatangan": "Wakil Kepala"
}
```

**Response Sukses (200):**
```json
{
  "message": "Surat keluar berhasil diperbarui.",
  "data": {
    "id": 1,
    "nomor_surat": "001/INSTANSI/I/2023",
    "tanggal_surat": "2023-01-15",
    "tujuan": "Instansi X Updated",
    "perihal": "Pemberitahuan Baru",
    "klasifikasi": "penting",
    "tembusan": "Instansi Y",
    "penandatangan": "Wakil Kepala",
    "file_surat": "1673760000_keluar.pdf",
    "user_id": 1,
    "created_at": "2023-01-15T08:00:00.000000Z",
    "updated_at": "2023-01-21T10:00:00.000000Z",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "user@example.com"
    }
  }
}
```

**HTTP Status Code:** 200 (Success), 401 (Unauthorized), 404 (Not Found), 422 (Unprocessable Entity)

### Delete Surat Keluar
**Endpoint:** `DELETE /api/surat-keluar/{id}`

**Deskripsi:** Menghapus surat keluar.

**Headers:**
- Authorization: Bearer {access_token}

**Response Sukses (200):**
```json
{
  "message": "Surat keluar berhasil dihapus."
}
```

**HTTP Status Code:** 200 (Success), 401 (Unauthorized), 404 (Not Found)

### Download File Surat Keluar
**Endpoint:** `GET /api/surat-keluar/{id}/download`

**Deskripsi:** Mendownload file surat keluar.

**Headers:**
- Authorization: Bearer {access_token}

**HTTP Status Code:** 200 (Success), 401 (Unauthorized), 404 (Not Found)

---

## 4. Disposisi

### Get All Disposisi
**Endpoint:** `GET /api/disposisi`

**Deskripsi:** Mendapatkan daftar disposisi surat masuk.

**Headers:**
- Authorization: Bearer {access_token}

**Query Parameters (Opsional):**
- kepada_user_id: ID penerima disposisi
- surat_masuk_id: ID surat masuk
- dibaca: status baca (true/false)
- per_page: jumlah item per halaman (default: 10)

**Response Sukses (200):**
```json
{
  "message": "Data disposisi berhasil diambil.",
  "data": {
    "data": [
      {
        "id": 1,
        "surat_masuk_id": 1,
        "dari_user_id": 2,
        "kepada_user_id": 3,
        "catatan": "Silakan ditindaklanjuti",
        "dibaca": false,
        "tanggal_disposisi": "2023-01-20T08:00:00.000000Z",
        "created_at": "2023-01-20T08:00:00.000000Z",
        "updated_at": "2023-01-20T08:00:00.000000Z",
        "surat_masuk": {
          "id": 1,
          "nomor_surat": "001/INSTANSI/I/2023",
          "pengirim": "PT ABC",
          "perihal": "Undangan Rapat"
        },
        "dari_user": {
          "id": 2,
          "name": "Pimpinan",
          "email": "pimpinan@example.com"
        },
        "kepada_user": {
          "id": 3,
          "name": "Staff",
          "email": "staff@example.com"
        }
      }
    ],
    "links": {...},
    "meta": {...}
  }
}
```

**HTTP Status Code:** 200 (Success), 401 (Unauthorized)

### Create Disposisi
**Endpoint:** `POST /api/disposisi`

**Deskripsi:** Membuat disposisi surat masuk (hanya untuk pimpinan).

**Headers:**
- Authorization: Bearer {access_token}

**Request Body:**
```json
{
  "surat_masuk_id": 1,
  "kepada_user_id": 3,
  "catatan": "Mohon ditindaklanjuti segera"
}
```

**Response Sukses (201):**
```json
{
  "message": "Disposisi berhasil ditambahkan.",
  "data": {
    "id": 2,
    "surat_masuk_id": 1,
    "dari_user_id": 2,
    "kepada_user_id": 3,
    "catatan": "Mohon ditindaklanjuti segera",
    "dibaca": false,
    "tanggal_disposisi": "2023-01-21T09:00:00.000000Z",
    "created_at": "2023-01-21T09:00:00.000000Z",
    "updated_at": "2023-01-21T09:00:00.000000Z",
    "surat_masuk": {
      "id": 1,
      "nomor_surat": "001/INSTANSI/I/2023",
      "pengirim": "PT ABC",
      "perihal": "Undangan Rapat"
    },
    "dari_user": {
      "id": 2,
      "name": "Pimpinan",
      "email": "pimpinan@example.com"
    },
    "kepada_user": {
      "id": 3,
      "name": "Staff",
      "email": "staff@example.com"
    }
  }
}
```

**Response Error (403):**
```json
{
  "message": "Hanya pimpinan yang dapat membuat disposisi."
}
```

**HTTP Status Code:** 201 (Created), 401 (Unauthorized), 403 (Forbidden), 422 (Unprocessable Entity)

### Get Single Disposisi
**Endpoint:** `GET /api/disposisi/{id}`

**Deskripsi:** Mendapatkan detail disposisi dan menandai sebagai sudah dibaca jika penerima.

**Headers:**
- Authorization: Bearer {access_token}

**Response Sukses (200):**
```json
{
  "message": "Data disposisi berhasil diambil.",
  "data": {
    "id": 1,
    "surat_masuk_id": 1,
    "dari_user_id": 2,
    "kepada_user_id": 3,
    "catatan": "Silakan ditindaklanjuti",
    "dibaca": true,
    "tanggal_disposisi": "2023-01-20T08:00:00.000000Z",
    "created_at": "2023-01-20T08:00:00.000000Z",
    "updated_at": "2023-01-21T10:00:00.000000Z",
    "surat_masuk": {
      "id": 1,
      "nomor_surat": "001/INSTANSI/I/2023",
      "pengirim": "PT ABC",
      "perihal": "Undangan Rapat"
    },
    "dari_user": {
      "id": 2,
      "name": "Pimpinan",
      "email": "pimpinan@example.com"
    },
    "kepada_user": {
      "id": 3,
      "name": "Staff",
      "email": "staff@example.com"
    }
  }
}
```

**HTTP Status Code:** 200 (Success), 401 (Unauthorized), 404 (Not Found)

### Update Disposisi
**Endpoint:** `PUT /api/disposisi/{id}`

**Deskripsi:** Memperbarui status disposisi (hanya untuk penerima).

**Headers:**
- Authorization: Bearer {access_token}

**Request Body:**
```json
{
  "dibaca": true
}
```

**Response Sukses (200):**
```json
{
  "message": "Status disposisi berhasil diperbarui.",
  "data": {
    "id": 1,
    "surat_masuk_id": 1,
    "dari_user_id": 2,
    "kepada_user_id": 3,
    "catatan": "Silakan ditindaklanjuti",
    "dibaca": true,
    "tanggal_disposisi": "2023-01-20T08:00:00.000000Z",
    "created_at": "2023-01-20T08:00:00.000000Z",
    "updated_at": "2023-01-21T10:00:00.000000Z",
    "surat_masuk": {
      "id": 1,
      "nomor_surat": "001/INSTANSI/I/2023",
      "pengirim": "PT ABC",
      "perihal": "Undangan Rapat"
    },
    "dari_user": {
      "id": 2,
      "name": "Pimpinan",
      "email": "pimpinan@example.com"
    },
    "kepada_user": {
      "id": 3,
      "name": "Staff",
      "email": "staff@example.com"
    }
  }
}
```

**HTTP Status Code:** 200 (Success), 401 (Unauthorized), 403 (Forbidden), 404 (Not Found), 422 (Unprocessable Entity)

### Delete Disposisi
**Endpoint:** `DELETE /api/disposisi/{id}`

**Deskripsi:** Menghapus disposisi (hanya untuk admin atau pembuat disposisi).

**Headers:**
- Authorization: Bearer {access_token}

**Response Sukses (200):**
```json
{
  "message": "Disposisi berhasil dihapus."
}
```

**Response Error (403):**
```json
{
  "message": "Anda tidak memiliki izin untuk menghapus data ini."
}
```

**HTTP Status Code:** 200 (Success), 401 (Unauthorized), 403 (Forbidden), 404 (Not Found)

---

## 5. Laporan

### Generate Laporan Surat Masuk
**Endpoint:** `POST /api/laporan/surat-masuk/generate`

**Deskripsi:** Membuat laporan surat masuk berdasarkan rentang tanggal.

**Headers:**
- Authorization: Bearer {access_token}

**Request Body:**
```json
{
  "tanggal_mulai": "2023-01-01",
  "tanggal_selesai": "2023-01-31"
}
```

**Response Sukses (200):**
```json
{
  "message": "Laporan surat masuk berhasil dibuat.",
  "data": {
    "id": 1,
    "judul": "Laporan Surat Masuk 2023-01-01 s.d 2023-01-31",
    "jenis": "surat_masuk",
    "tanggal_mulai": "2023-01-01",
    "tanggal_selesai": "2023-01-31",
    "data_laporan": {
      "tanggal_mulai": "2023-01-01",
      "tanggal_selesai": "2023-01-31",
      "total_surat": 5,
      "data": [
        {
          "nomor_surat": "001/INSTANSI/I/2023",
          "tanggal_surat": "2023-01-15",
          "pengirim": "PT ABC",
          "perihal": "Undangan Rapat",
          "klasifikasi": "umum",
          "status": "baru",
          "user_pembuat": "John Doe"
        }
      ]
    },
    "user_id": 1,
    "created_at": "2023-02-01T08:00:00.000000Z",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "user@example.com"
    }
  }
}
```

**HTTP Status Code:** 200 (Success), 401 (Unauthorized), 422 (Unprocessable Entity)

### Generate Laporan Surat Keluar
**Endpoint:** `POST /api/laporan/surat-keluar/generate`

**Deskripsi:** Membuat laporan surat keluar berdasarkan rentang tanggal.

**Headers:**
- Authorization: Bearer {access_token}

**Request Body:**
```json
{
  "tanggal_mulai": "2023-01-01",
  "tanggal_selesai": "2023-01-31"
}
```

**Response Sukses (200):**
```json
{
  "message": "Laporan surat keluar berhasil dibuat.",
  "data": {
    "id": 2,
    "judul": "Laporan Surat Keluar 2023-01-01 s.d 2023-01-31",
    "jenis": "surat_keluar",
    "tanggal_mulai": "2023-01-01",
    "tanggal_selesai": "2023-01-31",
    "data_laporan": {
      "tanggal_mulai": "2023-01-01",
      "tanggal_selesai": "2023-01-31",
      "total_surat": 3,
      "data": [
        {
          "nomor_surat": "001/INSTANSI/I/2023",
          "tanggal_surat": "2023-01-15",
          "tujuan": "Instansi X",
          "perihal": "Pemberitahuan",
          "klasifikasi": "umum",
          "penandatangan": "Kepala Instansi",
          "user_pembuat": "John Doe"
        }
      ]
    },
    "user_id": 1,
    "created_at": "2023-02-01T08:05:00.000000Z",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "user@example.com"
    }
  }
}
```

**HTTP Status Code:** 200 (Success), 401 (Unauthorized), 422 (Unprocessable Entity)

### Get All Laporan
**Endpoint:** `GET /api/laporan`

**Deskripsi:** Mendapatkan daftar laporan.

**Headers:**
- Authorization: Bearer {access_token}

**Query Parameters (Opsional):**
- jenis: surat_masuk/surat_keluar
- tanggal_mulai: tanggal awal filter
- tanggal_selesai: tanggal akhir filter
- per_page: jumlah item per halaman (default: 10)

**Response Sukses (200):**
```json
{
  "message": "Data laporan berhasil diambil.",
  "data": {
    "data": [
      {
        "id": 1,
        "judul": "Laporan Surat Masuk 2023-01-01 s.d 2023-01-31",
        "jenis": "surat_masuk",
        "tanggal_mulai": "2023-01-01",
        "tanggal_selesai": "2023-01-31",
        "user_id": 1,
        "created_at": "2023-02-01T08:00:00.000000Z",
        "user": {
          "id": 1,
          "name": "John Doe",
          "email": "user@example.com"
        }
      }
    ],
    "links": {...},
    "meta": {...}
  }
}
```

**HTTP Status Code:** 200 (Success), 401 (Unauthorized)

### Get Single Laporan
**Endpoint:** `GET /api/laporan/{id}`

**Deskripsi:** Mendapatkan detail laporan tertentu.

**Headers:**
- Authorization: Bearer {access_token}

**Response Sukses (200):**
```json
{
  "message": "Data laporan berhasil diambil.",
  "data": {
    "id": 1,
    "judul": "Laporan Surat Masuk 2023-01-01 s.d 2023-01-31",
    "jenis": "surat_masuk",
    "tanggal_mulai": "2023-01-01",
    "tanggal_selesai": "2023-01-31",
    "data_laporan": {
      "tanggal_mulai": "2023-01-01",
      "tanggal_selesai": "2023-01-31",
      "total_surat": 5,
      "data": [...]
    },
    "user_id": 1,
    "created_at": "2023-02-01T08:00:00.000000Z",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "user@example.com"
    }
  }
}
```

**HTTP Status Code:** 200 (Success), 401 (Unauthorized), 404 (Not Found)

### Delete Laporan
**Endpoint:** `DELETE /api/laporan/{id}`

**Deskripsi:** Menghapus laporan.

**Headers:**
- Authorization: Bearer {access_token}

**Response Sukses (200):**
```json
{
  "message": "Laporan berhasil dihapus."
}
```

**HTTP Status Code:** 200 (Success), 401 (Unauthorized), 404 (Not Found)