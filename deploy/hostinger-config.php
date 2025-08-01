<?php
/**
 * Hostinger Deployment Configuration Script
 * Run this script to prepare the application for Hostinger deployment
 */

echo "=== Hostinger Deployment Configuration ===\n\n";

// Create directories if they don't exist
$directories = [
    'deploy',
    'storage/logs',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'bootstrap/cache'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "✓ Created directory: $dir\n";
    }
}

// Production .env template
$envProduction = '
APP_NAME="Target Management System"
APP_ENV=production
APP_KEY=base64:qc0C4fbpom+ux0Gu2ku8PsxHYvE+5BNDqXcpPzA9+hQ=
APP_DEBUG=false
APP_URL=https://your-domain.com
APP_LOCALE=en

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# HOSTINGER DATABASE CONFIGURATION
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u123456789_target_db
DB_USERNAME=u123456789_target_user
DB_PASSWORD=YOUR_DATABASE_PASSWORD

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=your-email@your-domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"
';

file_put_contents('deploy/.env.hostinger', trim($envProduction));
echo "✓ Created production .env template: deploy/.env.hostinger\n";

// Hostinger .htaccess for public folder
$htaccess = '
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

# Security Headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    Header always set Permissions-Policy "camera=(), microphone=(), geolocation=()"
</IfModule>

# File Protection
<Files .env>
    Order Allow,Deny
    Deny from all
</Files>

<Files composer.json>
    Order Allow,Deny
    Deny from all
</Files>

<Files composer.lock>
    Order Allow,Deny
    Deny from all
</Files>

<Files package.json>
    Order Allow,Deny
    Deny from all
</Files>
';

file_put_contents('deploy/.htaccess.hostinger', trim($htaccess));
echo "✓ Created Hostinger .htaccess: deploy/.htaccess.hostinger\n";

// Deployment checklist
$checklist = '
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
';

file_put_contents('deploy/DEPLOYMENT_CHECKLIST.md', trim($checklist));
echo "✓ Created deployment checklist: deploy/DEPLOYMENT_CHECKLIST.md\n";

echo "\n=== Next Steps ===\n";
echo "1. Provide your Hostinger database credentials\n";
echo "2. Update deploy/.env.hostinger with real values\n";
echo "3. Upload files to Hostinger\n";
echo "4. Follow deploy/DEPLOYMENT_CHECKLIST.md\n";
echo "\n=== Configuration Complete! ===\n";
?>