# Hostinger Deployment Checklist

## Before Deployment

### 1. Database Setup
- [ ] Create MySQL database in Hostinger control panel
- [ ] Note database name: u123456789_target_db
- [ ] Note database user: u123456789_target_user  
- [ ] Note database password: [SECURE_PASSWORD]
- [ ] Note database host: localhost

### 2. Domain Setup
- [ ] Point domain to Hostinger
- [ ] Enable SSL certificate
- [ ] Verify domain is accessible

### 3. File Preparation
- [ ] Update deploy/.env.hostinger with real credentials
- [ ] Copy deploy/.env.hostinger to .env on server
- [ ] Copy deploy/.htaccess.hostinger to public/.htaccess on server

## Deployment Steps

### Step 1: Upload Files
Upload all files except:
- .env (use deploy/.env.hostinger instead)
- node_modules/
- .git/
- storage/logs/*.log

### Step 2: Set Permissions
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod 644 .env
```

### Step 3: Install Dependencies
```bash
composer install --optimize-autoloader --no-dev
```

### Step 4: Laravel Setup
```bash
php artisan config:cache
php artisan route:cache  
php artisan view:cache
php artisan migrate --force
php artisan db:seed --force
```

### Step 5: Final Checks
- [ ] Test login functionality
- [ ] Test target matrix (classification fix)
- [ ] Test reports generation
- [ ] Test file uploads
- [ ] Verify all pages load correctly

## Post-Deployment

### Security
- [ ] Change default admin password
- [ ] Review user permissions
- [ ] Test backup procedures
- [ ] Monitor error logs

### Performance
- [ ] Enable OPcache if available
- [ ] Test page load speeds
- [ ] Verify database performance
- [ ] Check memory usage