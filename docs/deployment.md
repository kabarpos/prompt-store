# Panduan Deployment Prompt Store

Dokumen ini berisi panduan langkah demi langkah untuk men-deploy aplikasi Prompt Store ke lingkungan produksi.

## Persyaratan Server

- PHP 8.2 atau lebih tinggi
- MySQL 8.0 atau MariaDB 10.5+
- Nginx atau Apache
- Redis (untuk caching dan antrian)
- Composer
- Node.js 16+ dan NPM
- SSL Certificate

## Persiapan Deployment

### 1. Clone Repositori

```bash
git clone https://github.com/username/prompt-store.git
cd prompt-store
```

### 2. Instalasi Dependensi

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

### 3. Konfigurasi Lingkungan

Salin file `.env.example` ke `.env` dan sesuaikan dengan pengaturan lingkungan produksi Anda:

```bash
cp .env.example .env
php artisan key:generate
```

Pastikan untuk mengatur nilai-nilai berikut di file `.env`:

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=your-database-host
DB_PORT=3306
DB_DATABASE=your-database-name
DB_USERNAME=your-database-username
DB_PASSWORD=your-database-password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=your-redis-host
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=your-mail-host
MAIL_PORT=587
MAIL_USERNAME=your-mail-username
MAIL_PASSWORD=your-mail-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. Migrasi Database

```bash
php artisan migrate --force
```

Jika Anda ingin mengisi data awal:

```bash
php artisan db:seed --class=ProductionSeeder
```

### 5. Mengoptimalkan Laravel

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6. Konfigurasi Webserver

#### Nginx

Buat file konfigurasi Nginx di `/etc/nginx/sites-available/prompt-store`:

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com www.your-domain.com;
    
    ssl_certificate /path/to/ssl/certificate.crt;
    ssl_certificate_key /path/to/ssl/private.key;
    
    root /path/to/prompt-store/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
    
    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
    }
    
    # Gzip compression
    gzip on;
    gzip_comp_level 5;
    gzip_min_length 256;
    gzip_proxied any;
    gzip_vary on;
    gzip_types
        application/javascript
        application/json
        application/xml
        application/xml+rss
        text/css
        text/javascript
        text/plain
        text/xml;
}
```

Aktifkan konfigurasi:

```bash
ln -s /etc/nginx/sites-available/prompt-store /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx
```

#### Apache

Buat file `.htaccess` di direktori publik (biasanya sudah ada):

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### 7. Konfigurasi Supervisor untuk Antrian

Buat file konfigurasi supervisor di `/etc/supervisor/conf.d/prompt-store-worker.conf`:

```ini
[program:prompt-store-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/prompt-store/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/prompt-store/storage/logs/worker.log
stopwaitsecs=3600
```

Muat ulang supervisor:

```bash
supervisorctl reread
supervisorctl update
supervisorctl start prompt-store-worker:*
```

### 8. Konfigurasi Cron untuk Tugas Terjadwal

Tambahkan entri cron berikut untuk menjalankan tugas terjadwal Laravel:

```bash
* * * * * cd /path/to/prompt-store && php artisan schedule:run >> /dev/null 2>&1
```

## Pemeliharaan

### Update Aplikasi

Untuk memperbarui aplikasi ke versi terbaru:

```bash
cd /path/to/prompt-store
git pull origin main
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
supervisorctl restart prompt-store-worker:*
```

### Backup

Lakukan backup database dan file secara teratur:

```bash
# Backup database
mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql

# Backup file
tar -czf prompt_store_files_$(date +%Y%m%d).tar.gz /path/to/prompt-store
```

### Mode Pemeliharaan

Untuk mengaktifkan mode pemeliharaan:

```bash
php artisan down --message="Sedang dalam pemeliharaan" --retry=60
```

Untuk menonaktifkan mode pemeliharaan:

```bash
php artisan up
```

## Pemantauan

### Log

Log aplikasi dapat ditemukan di direktori `storage/logs/`. Pastikan untuk memantau file-file ini secara teratur untuk mendeteksi masalah potensial.

### Performa

Gunakan alat seperti New Relic, Datadog, atau Laravel Telescope untuk memantau performa aplikasi.

## Keamanan

- Pastikan semua dependensi tetap diperbarui
- Jalankan pemindaian keamanan secara teratur
- Terapkan pembatasan akses IP untuk panel admin jika memungkinkan
- Aktifkan otentikasi dua faktor untuk akun admin
- Gunakan header keamanan seperti Content-Security-Policy, X-XSS-Protection, dll.

## Troubleshooting

### Masalah Umum

1. **500 Internal Server Error**
   - Periksa log di `storage/logs/laravel.log`
   - Pastikan izin file dan direktori sudah benar
   - Verifikasi pengaturan `.env`

2. **Masalah Koneksi Database**
   - Periksa kredensial database di `.env`
   - Pastikan server database berjalan
   - Periksa firewall dan izin pengguna database

3. **Masalah Antrian**
   - Periksa log supervisor
   - Pastikan Redis berjalan
   - Restart worker antrian: `supervisorctl restart prompt-store-worker:*`

## Kontak Dukungan

Jika Anda mengalami masalah yang tidak dapat diselesaikan, hubungi tim dukungan:

- Email: support@example.com
- Telepon: +62 123 4567 890 