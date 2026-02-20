# Arsitektur Sistem Manajemen Surat & Arsip Digital Instansi

## Gambaran Umum

Sistem ini dirancang sebagai aplikasi web berbasis REST API dengan arsitektur terpisah antara backend dan frontend. Backend dibangun dengan Laravel framework yang menyediakan API service, sedangkan frontend dibangun dengan React JS sebagai single page application (SPA).

## Komponen Utama

### 1. Backend (Laravel API)
- **Framework**: Laravel 9.x
- **Authentication**: Laravel Sanctum
- **Database**: MySQL
- **Struktur**: MVC pattern dengan API Resource

#### Subkomponen Backend:
- **Models**: Representasi data dan relasi antar tabel
- **Controllers**: Menangani logika bisnis dan API endpoints
- **Requests**: Validasi input dari client
- **Migrations**: Definisi skema database
- **Routes**: Definisi endpoint API

### 2. Frontend (React SPA)
- **Framework**: React 18.x
- **Routing**: React Router v6
- **State Management**: React hooks
- **Styling**: Bootstrap 5
- **HTTP Client**: Axios

#### Subkomponen Frontend:
- **Pages**: Komponen halaman utama
- **Components**: Komponen reusable
- **Services**: Layanan komunikasi dengan API
- **Routes**: Definisi routing aplikasi

## Struktur Database

### Tabel-tabel Utama:
1. **roles**: Menyimpan jenis role pengguna (admin, pimpinan, staff)
2. **users**: Data pengguna sistem dengan relasi ke role
3. **surat_masuk**: Data surat masuk beserta metadata
4. **surat_keluar**: Data surat keluar beserta metadata
5. **disposisi**: Data disposisi surat masuk ke pengguna lain
6. **laporan**: Data laporan yang telah digenerate

### Relasi Antar Tabel:
- `users` → `roles`: Many-to-One (user memiliki satu role)
- `surat_masuk` → `users`: Many-to-One (surat masuk dibuat oleh satu user)
- `surat_keluar` → `users`: Many-to-One (surat keluar dibuat oleh satu user)
- `disposisi` → `surat_masuk`: Many-to-One (banyak disposisi untuk satu surat masuk)
- `disposisi` → `users` (dari): Many-to-One (banyak disposisi dari satu user)
- `disposisi` → `users` (kepada): Many-to-One (banyak disposisi untuk satu user)

## Flow Penggunaan Sistem

### 1. Authentication Flow
1. User login dengan email/password
2. Server menghasilkan Sanctum token
3. Token disimpan di localStorage browser
4. Setiap request selanjutnya menyertakan token di header Authorization

### 2. Surat Masuk Flow
1. Staff menerima surat fisik
2. Staff input data surat ke sistem (nomor, tanggal, pengirim, dll)
3. File surat diupload (jika ada)
4. Pimpinan membuat disposisi ke staff tertentu
5. Staff menindaklanjuti sesuai disposisi

### 3. Surat Keluar Flow
1. Staff membuat draft surat keluar
2. Sistem otomatis menghasilkan nomor surat unik
3. Staff melengkapi data surat (tujuan, perihal, penandatangan)
4. File surat diupload (jika ada)
5. Surat siap dikirim

### 4. Disposisi Flow
1. Hanya pimpinan yang dapat membuat disposisi
2. Pimpinan memilih surat masuk
3. Pimpinan memilih staff tujuan disposisi
4. Pimpinan memberikan catatan disposisi
5. Staff menerima notifikasi dan menindaklanjuti

## Keamanan Sistem

### 1. Otentikasi
- Menggunakan Laravel Sanctum untuk token-based authentication
- Token disimpan di localStorage (untuk SPA)
- Token otomatis dihapus saat logout

### 2. Otorisasi
- Middleware role-based untuk membatasi akses endpoint
- Hanya user dengan role tertentu yang bisa mengakses fitur tertentu
- Validasi hak akses di level controller

### 3. Validasi Input
- Validasi di level request (Form Request)
- Validasi ekstensi dan ukuran file upload
- Sanitasi input sebelum disimpan ke database

### 4. Perlindungan CSRF
- Sanctum menyediakan perlindungan CSRF otomatis
- Semua request API dilindungi dari serangan CSRF

## Fitur-fitur Utama

### 1. Role Management
- **Admin**: Full akses ke semua fitur
- **Pimpinan**: Akses ke semua data, dapat membuat disposisi
- **Staff**: Akses terbatas, hanya bisa mengelola tugasnya

### 2. Manajemen Surat
- **Surat Masuk**: CRUD, pencarian, filter, upload file
- **Surat Keluar**: CRUD, nomor otomatis, upload file
- **Disposisi**: Pembuatan dan pelacakan disposisi

### 3. Pencarian & Filter
- Pencarian global berdasarkan keyword
- Filter berdasarkan tanggal, klasifikasi, status
- Pagination untuk efisiensi loading data

### 4. Laporan
- Generate laporan surat masuk/keluar
- Filter rentang tanggal
- Export data (akan diimplementasikan)

## Skema Integrasi

```
[Client Browser] 
       ↓ (HTTP Request + Token)
[React Frontend] 
       ↓ (Axios API Calls)
[Laravel API Backend] 
       ↓ (Eloquent/Query Builder)
[MySQL Database]
```

## Deployment Strategy

### Backend:
- Deploy di server dengan PHP 8+
- Konfigurasi environment (database, cache, etc)
- Jalankan migration dan seeding
- Setup queue worker jika diperlukan

### Frontend:
- Build static assets (npm run build)
- Serve melalui web server (nginx/Apache)
- Konfigurasi proxy API ke backend

## Teknologi Pendukung

### Backend:
- PHP 8+
- Laravel Framework
- MySQL Database
- Composer (dependency manager)
- Laravel Sanctum (authentication)

### Frontend:
- React JS
- React Router
- Axios
- Bootstrap 5
- npm/yarn (package manager)

### Development:
- Git (version control)
- Composer (PHP packages)
- NPM (JS packages)
- Postman (API testing)

## Scalability Considerations

1. **Database**: Optimasi query dengan indexing
2. **Caching**: Gunakan Redis untuk caching data
3. **Queue**: Proses background untuk operasi berat
4. **Load Balancing**: Multiple server instances
5. **CDN**: Untuk serving file statis dan upload

Arsitektur ini dirancang untuk memenuhi kebutuhan sistem manajemen surat pada instansi pemerintahan dengan fokus pada keamanan, skalabilitas, dan kemudahan penggunaan.