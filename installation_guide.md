# Panduan Instalasi Sistem Manajemen Surat & Arsip Digital Instansi

## Prasyarat Sistem

### Backend (Server Requirements)
- PHP >= 8.0
- Composer
- MySQL >= 5.7 atau MariaDB >= 10.3
- Web Server (Apache/Nginx)
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- Ctype PHP Extension
- JSON PHP Extension

### Frontend (Development Requirements)
- Node.js >= 14.0.0
- npm >= 6.0.0 atau yarn

## Instalasi Backend (Laravel API)

### 1. Clone atau Buat Proyek
```bash
# Jika menggunakan proyek yang sudah ada
cp -r /path/to/project/backend /var/www/surat-backend

# Atau buat proyek baru Laravel
composer create-project laravel/laravel surat-backend
```

### 2. Install Dependencies
```bash
cd /var/www/surat-backend
composer install
```

### 3. Konfigurasi Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurasi Database
Edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=surat_instansi
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password
```

### 5. Migrasi dan Seeder
```bash
# Jalankan migrasi database
php artisan migrate --seed

# Alternatif: jika ingin refresh migrasi (akan menghapus data lama)
php artisan migrate:refresh --seed
```

### 6. Konfigurasi Storage
```bash
# Buat symbolic link untuk akses file publik
php artisan storage:link
```

### 7. Jalankan Server
```bash
# Development
php artisan serve

# Production (konfigurasi web server diperlukan)
# Contoh dengan nginx/apache pointing ke /var/www/surat-backend/public
```

## Instalasi Frontend (React SPA)

### 1. Clone atau Buat Proyek
```bash
# Jika menggunakan proyek yang sudah ada
cp -r /path/to/project/frontend /var/www/surat-frontend

# Atau buat proyek baru React
npx create-react-app surat-frontend
```

### 2. Install Dependencies
```bash
cd /var/www/surat-frontend
npm install
```

### 3. Konfigurasi Environment
Buat file `.env` di root direktori frontend:
```env
REACT_APP_API_URL=http://localhost:8000/api
# Ganti dengan URL produksi saat deployment
# REACT_APP_API_URL=https://api.yourdomain.com/api
```

### 4. Build untuk Produksi
```bash
npm run build
```

### 5. Serving di Web Server
Web server harus diatur untuk serving file statis dari folder `build/`.

Contoh konfigurasi nginx:
```nginx
server {
    listen 80;
    server_name surat-instansi.yourdomain.com;
    root /var/www/surat-frontend/build;
    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }

    # Proxy API requests ke backend
    location /api {
        proxy_pass http://127.0.0.1:8000/api;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

## Konfigurasi Database

### Struktur Tabel (Telah Ditangani oleh Migration)

Tabel-tabel berikut akan dibuat otomatis saat migrasi:
- `roles`
- `users`
- `surat_masuk`
- `surat_keluar`
- `disposisi`
- `laporan`

### Data Awal (Seeder)

Data awal role akan dibuat:
- Admin (ID: 1)
- Pimpinan (ID: 2)
- Staff (ID: 3)

## Konfigurasi Web Server

### Apache (.htaccess)
Pastikan mod_rewrite aktif:
```apache
Options -MultiViews
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [QSA,L]
```

### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## Deployment Production

### 1. Optimasi Laravel
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimasi autoloader
composer dump-autoload --optimize
```

### 2. Security
```bash
# Set permission yang benar
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 3. Environment Variables Penting
```env
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=error
```

## Testing API

Setelah instalasi selesai, uji API dengan endpoint login:
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com", "password":"password"}'
```

## Troubleshooting

### Common Issues:

1. **Permission Error**
   ```bash
   sudo chown -R www-data:www-data storage bootstrap/cache
   ```

2. **Database Connection Error**
   - Periksa koneksi database di `.env`
   - Pastikan service MySQL/MariaDB aktif

3. **Migration Error**
   ```bash
   php artisan migrate:fresh --seed
   ```

4. **Storage Link Error**
   ```bash
   php artisan storage:link
   ```

## Backup dan Restore

### Backup Database
```bash
mysqldump -u username -p surat_instansi > backup_surat_$(date +%Y%m%d_%H%M%S).sql
```

### Restore Database
```bash
mysql -u username -p surat_instansi < backup_file.sql
```

## Update Sistem

### Update Backend
```bash
git pull origin main  # atau branch produksi
composer install
php artisan migrate
npm run build  # jika ada perubahan assets
```

### Update Frontend
```bash
git pull origin main  # atau branch produksi
npm install
npm run build
```

Proses instalasi selesai! Aplikasi siap digunakan. Pastikan untuk mengganti credential default setelah instalasi pertama kali.