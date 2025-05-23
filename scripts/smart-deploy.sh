#!/bin/bash

# Script Smart Deploy untuk Prompt Store
# Skrip ini melakukan update selektif berdasarkan file yang berubah
# Penggunaan: ./smart-deploy.sh [branch] [environment]
# Contoh: ./smart-deploy.sh main production

# Warna untuk output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Fungsi logging
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Default values
BRANCH=${1:-main}
ENV=${2:-production}
GIT_REMOTE="origin"

# Directori aplikasi
APP_DIR=$(pwd)

# Cek apakah berada di direktori root aplikasi
if [ ! -f "artisan" ]; then
    log_error "Script harus dijalankan dari root directory aplikasi"
    exit 1
fi

# Simpan git hash sebelum update
log_info "Memeriksa status Git..."
BEFORE_HASH=$(git rev-parse HEAD)

# Aktifkan mode pemeliharaan jika production
if [ "$ENV" == "production" ]; then
    log_info "Mengaktifkan mode pemeliharaan..."
    php artisan down --message="Sedang dilakukan pembaruan sistem" --retry=60
fi

# Pull perubahan terbaru
log_info "Mengambil perubahan terbaru dari branch $BRANCH..."
git fetch $GIT_REMOTE $BRANCH
FETCH_STATUS=$?

if [ $FETCH_STATUS -ne 0 ]; then
    log_error "Gagal mengambil perubahan terbaru. Periksa koneksi dan repository Anda."
    if [ "$ENV" == "production" ]; then
        log_info "Menonaktifkan mode pemeliharaan..."
        php artisan up
    fi
    exit 1
fi

git merge $GIT_REMOTE/$BRANCH
MERGE_STATUS=$?

if [ $MERGE_STATUS -ne 0 ]; then
    log_error "Konflik saat merging. Silahkan selesaikan konflik secara manual."
    if [ "$ENV" == "production" ]; then
        log_info "Menonaktifkan mode pemeliharaan..."
        php artisan up
    fi
    exit 1
fi

# Simpan git hash setelah update
AFTER_HASH=$(git rev-parse HEAD)

# Jika tidak ada perubahan, keluar
if [ "$BEFORE_HASH" == "$AFTER_HASH" ]; then
    log_info "Tidak ada perubahan baru yang tersedia."
    if [ "$ENV" == "production" ]; then
        log_info "Menonaktifkan mode pemeliharaan..."
        php artisan up
    fi
    exit 0
fi

# Dapatkan file yang berubah
CHANGED_FILES=$(git diff --name-only $BEFORE_HASH $AFTER_HASH)
log_info "File yang berubah:"
echo "$CHANGED_FILES"

# Flag untuk berbagai jenis perubahan
COMPOSER_CHANGED=false
NPM_CHANGED=false
MIGRATION_CHANGED=false
PHP_CHANGED=false
JS_CHANGED=false
VUE_CHANGED=false
ENV_CHANGED=false
CONFIG_CHANGED=false
ROUTE_CHANGED=false
VIEW_CHANGED=false

# Periksa file yang berubah
echo "$CHANGED_FILES" | while read -r file; do
    if [[ "$file" == "composer.json" || "$file" == "composer.lock" ]]; then
        COMPOSER_CHANGED=true
    fi
    
    if [[ "$file" == "package.json" || "$file" == "package-lock.json" ]]; then
        NPM_CHANGED=true
    fi
    
    if [[ "$file" == database/migrations/* ]]; then
        MIGRATION_CHANGED=true
    fi
    
    if [[ "$file" == *.php ]]; then
        PHP_CHANGED=true
        
        # Cek apakah file config yang berubah
        if [[ "$file" == config/* ]]; then
            CONFIG_CHANGED=true
        fi
        
        # Cek apakah route yang berubah
        if [[ "$file" == routes/* ]]; then
            ROUTE_CHANGED=true
        fi
    fi
    
    if [[ "$file" == *.js || "$file" == *.ts ]]; then
        JS_CHANGED=true
    fi
    
    if [[ "$file" == *.vue ]]; then
        VUE_CHANGED=true
    fi
    
    if [[ "$file" == ".env.example" ]]; then
        ENV_CHANGED=true
    fi
    
    if [[ "$file" == resources/views/* ]]; then
        VIEW_CHANGED=true
    fi
done

# Jalankan perintah sesuai dengan file yang berubah

# Update Composer dependencies jika perlu
if [ "$COMPOSER_CHANGED" = true ]; then
    log_info "Memperbarui dependencies PHP..."
    composer install --no-interaction --no-dev --optimize-autoloader
fi

# Update NPM dependencies jika perlu
if [ "$NPM_CHANGED" = true ] || [ "$JS_CHANGED" = true ] || [ "$VUE_CHANGED" = true ]; then
    log_info "Memperbarui dependencies JavaScript..."
    npm ci
    
    log_info "Membangun assets..."
    npm run build
fi

# Jalankan migrasi jika ada perubahan migrasi
if [ "$MIGRATION_CHANGED" = true ]; then
    log_info "Menjalankan migrasi database..."
    php artisan migrate --force
fi

# Cache config jika ada perubahan config
if [ "$CONFIG_CHANGED" = true ]; then
    log_info "Mengoptimalkan konfigurasi..."
    php artisan config:cache
fi

# Cache route jika ada perubahan route
if [ "$ROUTE_CHANGED" = true ]; then
    log_info "Mengoptimalkan route..."
    php artisan route:cache
fi

# Cache view jika ada perubahan view
if [ "$VIEW_CHANGED" = true ]; then
    log_info "Mengoptimalkan view..."
    php artisan view:cache
fi

# Jika ada perubahan PHP, JavaScript, atau Vue, restart queue workers
if [ "$PHP_CHANGED" = true ] || [ "$JS_CHANGED" = true ] || [ "$VUE_CHANGED" = true ]; then
    log_info "Merestart queue workers..."
    if command -v supervisorctl &> /dev/null; then
        supervisorctl restart prompt-store-worker:*
    else
        log_warning "supervisorctl tidak ditemukan. Silakan restart queue workers secara manual."
    fi
fi

# Bersihkan cache jika ada perubahan environment
if [ "$ENV_CHANGED" = true ]; then
    log_info "Membersihkan cache aplikasi..."
    php artisan cache:clear
fi

# Ringkasan perubahan
log_info "Ringkasan update:"
echo "Commit sebelum: $BEFORE_HASH"
echo "Commit setelah: $AFTER_HASH"
echo "Total file berubah: $(echo "$CHANGED_FILES" | wc -l)"

# Matikan mode pemeliharaan jika production
if [ "$ENV" == "production" ]; then
    log_info "Menonaktifkan mode pemeliharaan..."
    php artisan up
fi

log_info "Deployment selesai!" 