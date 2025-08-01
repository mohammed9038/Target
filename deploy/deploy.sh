#!/bin/bash
# Quick deployment script for Hostinger

echo "=== Hostinger Deployment ==="
echo "Connecting to SSH..."

# SSH connection details
SSH_HOST="147.93.93.198"
SSH_PORT="65002"
SSH_USER="u925629539"
APP_PATH="public_html/target-system"

echo "Run these commands on the server:"
echo "cd $APP_PATH"
echo "cp deploy/env.production .env"
echo "chmod 644 .env"
echo "chmod -R 755 storage/ bootstrap/cache/"
echo "composer install --optimize-autoloader --no-dev"
echo "php artisan config:cache"
echo "php artisan route:cache"
echo "php artisan view:cache"
echo "php artisan migrate --force"
echo "php artisan db:seed --force"
echo "cp deploy/.htaccess.hostinger public/.htaccess"
echo ""
echo "Then test: https://mkalrawi.com/target-system"