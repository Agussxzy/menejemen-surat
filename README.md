# Sistem Manajemen Surat & Arsip Digital Instansi

Aplikasi web untuk mengelola surat masuk, surat keluar, dan arsip digital bagi instansi pemerintahan seperti kantor desa, sekolah, kecamatan, dinas, dan puskesmas.

## Teknologi yang Digunakan

**Backend:**
- Laravel (REST API)
- PHP 8+
- Laravel Sanctum (Authentication)
- MySQL

**Frontend:**
- React JS
- Bootstrap 5
- Axios

## Fitur Utama

1. Authentication & Role Management
2. Manajemen Surat Masuk
3. Manajemen Surat Keluar
4. Disposisi Surat
5. Arsip & Pencarian
6. Laporan

## Instalasi

### Backend (Laravel)

```bash
# Clone repository
composer install
cp .env.example .env
php artisan key:generate
# Konfigurasi database di .env
php artisan migrate --seed
php artisan serve
```

### Frontend (React)

```bash
npm install
npm start
```