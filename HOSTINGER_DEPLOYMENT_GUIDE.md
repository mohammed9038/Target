# Target Management System - Hostinger Deployment Guide

## 📋 Prerequisites

Before deploying to Hostinger, ensure you have:
- A Hostinger hosting account with PHP 8.1+ support
- MySQL database access
- SSH access (optional but recommended)
- Your GitHub repository ready

## 🚀 Deployment Steps

### Step 1: Prepare Hostinger Environment

1. **Create MySQL Database**
   - Login to Hostinger control panel
   - Go to "Databases" → "MySQL Databases"
   - Create a new database (e.g., `your_username_targets`)
   - Create a database user with full privileges
   - Note down: database name, username, password

2. **Domain Configuration**
   - **IMPORTANT**: For subdirectory deployment, keep domain pointing to `public_html`
   - The app will be accessible at `yourdomain.com/target`
   - DO NOT point domain to `public_html/target/public` - this breaks API routing

### Step 2: Upload Files from GitHub

**Option A: Using Git (Recommended)**
```bash
# SSH into your Hostinger account
cd public_html
git clone https://github.com/mohammed9038/Target-system.git .
```

**Option B: Download and Upload**
1. Download ZIP from GitHub
2. Extract and upload all files to `public_html`
3. Ensure folder structure is correct

### Step 3: Configure Environment

1. **Setup Environment File**
   ```bash
   cp env.production.example .env
   nano .env  # Edit with your database credentials
   ```

2. **Update .env with your Hostinger details:**
   ```env
   APP_NAME="Target Management System"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com
   
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_username
   DB_PASSWORD=your_database_password
   ```

### Step 4: Setup Domain Routing

**If domain points to public_html/public (Recommended):**
- No additional setup needed
- The existing `public/.htaccess` will handle routing

**If domain points to public_html root:**
```bash
cp .htaccess.root .htaccess
```

### Step 5: Run Deployment Script

```bash
php deploy_to_hostinger.php
```

This script will automatically:
- Generate application key
- Clear and optimize caches
- Test database connection
- Run migrations
- Seed initial data
- Set proper file permissions
- Cache configurations for production

### Step 6: Verify Installation

1. **Visit your domain**
   - You should see the login page
   - No errors should be displayed

2. **Test login with default credentials:**
   - Username: `admin`
   - Password: `admin123`
   - **Change these immediately after first login!**

3. **Test key functionality:**
   - Dashboard loads correctly
   - Target matrix works
   - Filters function properly
   - Export/import features work

## 🔧 Manual Steps (if deployment script fails)

### Generate Application Key
```bash
php artisan key:generate --force
```

### Run Database Setup
```bash
php artisan migrate --force
php artisan db:seed --force
```

### Clear and Cache
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Set File Permissions
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

## 🛡️ Security Considerations

1. **Change Default Credentials**
   - Login immediately and change admin password
   - Update admin username if desired

2. **Environment Security**
   - Ensure .env file is not web-accessible
   - The .htaccess files protect sensitive files

3. **Database Security**
   - Use strong database passwords
   - Limit database user privileges to your database only

## 🐛 Troubleshooting

### Common Issues and Solutions

**1. "500 Internal Server Error"**
- Check error logs in Hostinger control panel
- Ensure PHP version is 8.1+
- Verify file permissions (755 for directories, 644 for files)

**2. "Database Connection Error"**
- Verify database credentials in .env
- Ensure database exists and user has privileges
- Check if database server is accessible

**3. "Route Not Found"**
- Ensure .htaccess files are properly configured
- Check if mod_rewrite is enabled (usually enabled on Hostinger)

**4. "Storage Permission Error"**
- Run: `chmod -R 755 storage bootstrap/cache`
- Ensure web server can write to these directories

**5. "Class Not Found"**
- Run: `composer install --no-dev --optimize-autoloader`
- Clear and cache configurations

### Performance Optimization

**1. Enable Opcache (if available)**
- Check Hostinger control panel for PHP extensions
- Enable Opcache for better performance

**2. Database Optimization**
- Regularly check database performance
- Consider indexing for large datasets

**3. Caching**
- The deployment script enables configuration caching
- Consider Redis if available for session/cache storage

## 📞 Support

If you encounter issues:
1. Check Hostinger error logs
2. Verify all deployment steps were completed
3. Ensure your hosting plan supports Laravel requirements
4. Contact Hostinger support for server-specific issues

## ✅ Post-Deployment Checklist

- [ ] Application loads without errors
- [ ] Login works with default credentials
- [ ] Admin password changed
- [ ] Database tables created and seeded
- [ ] Dashboard displays correctly
- [ ] Target matrix functionality works
- [ ] CSV export/import works
- [ ] User permissions function correctly
- [ ] All master data (regions, channels, etc.) is accessible
- [ ] SSL certificate is active (if applicable)

## 🔄 Updates and Maintenance

**To update the application:**
1. Pull latest changes from GitHub
2. Run migrations if needed: `php artisan migrate --force`
3. Clear caches: `php artisan config:clear && php artisan config:cache`
4. Test functionality

**Regular maintenance:**
- Monitor error logs
- Backup database regularly
- Keep PHP version updated
- Monitor storage space usage

---

**🎉 Congratulations! Your Target Management System is now live on Hostinger!**