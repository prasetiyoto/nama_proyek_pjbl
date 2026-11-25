#!/bin/sh
# Dijalankan setiap kali kontainer mulai di Render.
# Paket gratis Render tidak punya "pre-deploy command", jadi migrasi dijalankan di sini.
set -e
cd /var/www/html

# Cache konfigurasi, route, view, dan event dari Environment Variables Render
php artisan optimize

# Migrasi skema basis data (tanpa konfirmasi interaktif)
php artisan migrate --force

chown -R www-data:www-data storage bootstrap/cache

exec apache2-foreground
