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