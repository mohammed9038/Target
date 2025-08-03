# 🚀 Hostinger Test Deployment Guide: NewTarget

## 🎯 Overview
This guide will help you set up a test version of your Target Management System called "newtarget" on Hostinger, separate from your production "targetsystem".

## 🔍 Diagnosing the 500 Error

The 500 Internal Server Error you're seeing is typically caused by one of these issues:

### 1. **Missing Dependencies**
```bash
# Laravel needs Composer dependencies installed
composer install --no-dev --optimize-autoloader
```

### 2. **Missing APP_KEY**
```bash
# Generate Laravel application key
php artisan key:generate
```

### 3. **Wrong File Permissions**
```bash
# Set correct permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### 4. **Missing PHP Extensions**
Required extensions: `pdo_mysql`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`

### 5. **Environment Configuration**
Missing or incorrect `.env` file configuration.

## 📁 Recommended Directory Structure on Hostinger

```
public_html/
├── targetsystem/           # Your production app
│   ├── public/
│   ├── app/
│   ├── config/
│   └── ...
├── newtarget/             # Your test app
│   ├── public/
│   ├── app/
│   ├── config/
│   ├── .env
│   └── ...
└── index.html             # Default Hostinger page
```

## 🛠️ Step-by-Step Deployment

### Step 1: Create Test Environment Structure

1. **Access cPanel File Manager** or FTP
2. **Navigate to public_html**
3. **Create folder**: `newtarget`
4. **Upload all Laravel files** to `/public_html/newtarget/`

### Step 2: Configure Environment File

Create `/public_html/newtarget/.env`:

```env
APP_NAME="Target Management System (Test)"
APP_ENV=testing
APP_KEY=
APP_DEBUG=true
APP_URL=https://mkalrawi.com/newtarget

LOG_CHANNEL=stack
LOG_LEVEL=debug

# Database Configuration (Create separate test database)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=username_newtarget  # Replace 'username' with your cPanel username
DB_USERNAME=username_dbuser     # Your database username
DB_PASSWORD=your_db_password    # Your database password

CACHE_DRIVER=file
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Add your other configuration as needed
```

### Step 3: Database Setup

1. **Create Test Database** in cPanel:
   - Name: `username_newtarget` (replace username with your cPanel username)
   - Create database user and assign privileges

2. **Update .env** with correct database credentials

### Step 4: Install Dependencies and Configure Laravel

```bash
# SSH into your account or use cPanel Terminal
cd /home/username/public_html/newtarget

# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Clear and cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 5: Set File Permissions

```bash
# Set correct permissions
find /home/username/public_html/newtarget -type f -exec chmod 644 {} \;
find /home/username/public_html/newtarget -type d -exec chmod 755 {} \;
chmod -R 755 /home/username/public_html/newtarget/storage
chmod -R 755 /home/username/public_html/newtarget/bootstrap/cache
```

### Step 6: Configure Web Access

Create `/public_html/newtarget/public/.htaccess`:

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

# Security headers
<Files ".env">
    Order allow,deny
    Deny from all
</Files>
```

## 🌐 Access URLs

- **Test Version**: `https://mkalrawi.com/newtarget/public/`
- **Production Version**: `https://mkalrawi.com/targetsystem/public/`

## 🆘 Troubleshooting 500 Errors

### 1. Check Error Logs
```bash
# Laravel logs
tail -f /home/username/public_html/newtarget/storage/logs/laravel.log

# PHP error logs (check cPanel Error Logs)
```

### 2. Enable Debug Mode
In `.env`:
```env
APP_DEBUG=true
APP_ENV=local
LOG_LEVEL=debug
```

### 3. Common Fixes

#### Fix 1: Composer Autoload
```bash
composer dump-autoload --optimize
```

#### Fix 2: Clear All Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

#### Fix 3: Storage Link
```bash
php artisan storage:link
```

#### Fix 4: Check PHP Version
Ensure PHP 8.0+ is selected in cPanel

## 📋 Pre-Deployment Checklist

- [ ] Create separate test database
- [ ] Upload all Laravel files to `/newtarget/` folder
- [ ] Configure `.env` file with test database credentials
- [ ] Run `composer install --no-dev --optimize-autoloader`
- [ ] Run `php artisan key:generate`
- [ ] Set correct file permissions (755 for directories, 644 for files)
- [ ] Run database migrations and seeders
- [ ] Test access to `/newtarget/public/`

## 🔧 Quick Deployment Script

Upload the `deploy_test_newtarget.php` script to your public_html and run it via browser to automatically check and configure your test environment:

`https://mkalrawi.com/deploy_test_newtarget.php`

## 🎯 Login Credentials for Test

- **Admin**: username: `admin`, password: `password`
- **Manager**: username: `manager`, password: `password`

## 📞 Support

If you continue to experience issues:

1. **Check the deployment script output** for specific errors
2. **Review Laravel logs** in `storage/logs/laravel.log`
3. **Verify PHP extensions** are installed
4. **Ensure database connection** is working
5. **Check file permissions** are correct

The test environment will allow you to safely test all changes before deploying to production!