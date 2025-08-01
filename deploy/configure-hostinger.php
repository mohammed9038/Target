<?php
/**
 * Hostinger Configuration Script
 * Creates production .env file with real credentials
 */

echo "=== Configuring for Hostinger Deployment ===\n\n";

// Your Hostinger credentials
$config = [
    'APP_URL' => 'https://mkalrawi.com/target-system',
    'DB_HOST' => 'localhost',
    'DB_DATABASE' => 'u925629539_TSM',
    'DB_USERNAME' => 'u925629539_hesoka1',
    'DB_PASSWORD' => 'HEsoka202090$',
    'MAIL_FROM_ADDRESS' => 'noreply@mkalrawi.com',
    'MAIL_USERNAME' => 'noreply@mkalrawi.com',
    'MAIL_PASSWORD' => 'HEsoka202090$',
];

// Production .env content
$envContent = 'APP_NAME="Target Management System"
APP_ENV=production
APP_KEY=base64:qc0C4fbpom+ux0Gu2ku8PsxHYvE+5BNDqXcpPzA9+hQ=
APP_DEBUG=false
APP_URL=' . $config['APP_URL'] . '
APP_LOCALE=en

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# HOSTINGER DATABASE CONFIGURATION
DB_CONNECTION=mysql
DB_HOST=' . $config['DB_HOST'] . '
DB_PORT=3306
DB_DATABASE=' . $config['DB_DATABASE'] . '
DB_USERNAME=' . $config['DB_USERNAME'] . '
DB_PASSWORD=' . $config['DB_PASSWORD'] . '

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=' . $config['MAIL_USERNAME'] . '
MAIL_PASSWORD=' . $config['MAIL_PASSWORD'] . '
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="' . $config['MAIL_FROM_ADDRESS'] . '"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"';

// Save production .env
file_put_contents('deploy/env.production', $envContent);
echo "✓ Created production .env: deploy/env.production\n";

// Create deployment instructions
$instructions = '
# HOSTINGER DEPLOYMENT INSTRUCTIONS

## Your Configuration:
- Domain: https://mkalrawi.com/target-system
- Database: u925629539_TSM
- FTP Host: 147.93.93.198
- SSH Host: 147.93.93.198:65002

## Step 1: Upload Files via FTP
1. Connect to FTP: 147.93.93.198
   Username: u925629539
   Password: HEsoka202090$
   Port: 21

2. Navigate to: public_html/target-system/
3. Upload ALL files EXCEPT:
   - .env (use deploy/env.production instead)
   - .git/
   - node_modules/
   - storage/logs/*.log

## Step 2: SSH Setup Commands
1. Connect via SSH: ssh u925629539@147.93.93.198 -p 65002
2. Navigate to your app: cd public_html/target-system/
3. Copy production env: cp deploy/env.production .env
4. Set permissions: chmod 644 .env
5. Set storage permissions: chmod -R 755 storage/ bootstrap/cache/

## Step 3: Laravel Setup (via SSH)
Run these commands in order:
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan db:seed --force
```

## Step 4: Web Server Setup
1. Copy .htaccess: cp deploy/.htaccess.hostinger public/.htaccess
2. Ensure public/ is your document root
3. Test: https://mkalrawi.com/target-system

## Step 5: Test Classification Fix
1. Login to the system
2. Go to Targets page
3. Select a period and verify:
   ✓ Food salesmen only see Food suppliers
   ✓ Non-Food salesmen only see Non-Food suppliers
   ✓ No incorrect combinations

## Troubleshooting
- Check error logs: tail -f storage/logs/laravel.log
- Verify database connection: php artisan tinker -> DB::connection()->getPdo();
- Test permissions: ls -la storage/ bootstrap/cache/
';

file_put_contents('deploy/HOSTINGER_INSTRUCTIONS.md', trim($instructions));
echo "✓ Created deployment instructions: deploy/HOSTINGER_INSTRUCTIONS.md\n";

// Create quick upload script
$uploadScript = '#!/bin/bash
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
';

file_put_contents('deploy/deploy.sh', trim($uploadScript));
chmod('deploy/deploy.sh', 0755);
echo "✓ Created deployment script: deploy/deploy.sh\n";

echo "\n=== Configuration Complete! ===\n";
echo "Your Hostinger settings have been configured:\n";
echo "- Domain: https://mkalrawi.com/target-system\n";
echo "- Database: u925629539_TSM\n";
echo "- All credentials configured\n";
echo "\nNext: Follow deploy/HOSTINGER_INSTRUCTIONS.md\n";
?>