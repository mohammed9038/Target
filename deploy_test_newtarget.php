<?php
/**
 * Deploy Test Environment: NewTarget
 * Hostinger Deployment Script for Test Version
 */

echo "🚀 Target Management System - Test Deployment (NewTarget)\n";
echo "========================================================\n\n";

// Configuration
$testAppName = 'newtarget';
$prodAppName = 'targetsystem';

// Step 1: Environment Detection
echo "📊 Step 1: Environment Detection\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Current Directory: " . getcwd() . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] ?? 'N/A' . "\n";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' . "\n";

// Check PHP Extensions
$requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath'];
echo "\n🔧 Required PHP Extensions:\n";
foreach ($requiredExtensions as $ext) {
    $status = extension_loaded($ext) ? '✅' : '❌';
    echo "  {$status} {$ext}\n";
}

// Step 2: Directory Structure Setup
echo "\n📁 Step 2: Setting up Test Directory Structure\n";

$baseDir = dirname(__DIR__);
$testDir = $baseDir . '/' . $testAppName;
$publicDir = $testDir . '/public';

echo "Base Directory: {$baseDir}\n";
echo "Test Directory: {$testDir}\n";
echo "Public Directory: {$publicDir}\n";

// Create directories if they don't exist
if (!file_exists($testDir)) {
    mkdir($testDir, 0755, true);
    echo "✅ Created test directory\n";
} else {
    echo "ℹ️  Test directory already exists\n";
}

if (!file_exists($publicDir)) {
    mkdir($publicDir, 0755, true);
    echo "✅ Created public directory\n";
} else {
    echo "ℹ️  Public directory already exists\n";
}

// Step 3: File Permissions Check
echo "\n🔒 Step 3: File Permissions Check\n";
$checkDirs = [
    $testDir . '/storage',
    $testDir . '/storage/logs',
    $testDir . '/storage/framework',
    $testDir . '/storage/framework/cache',
    $testDir . '/storage/framework/sessions',
    $testDir . '/storage/framework/views',
    $testDir . '/bootstrap/cache'
];

foreach ($checkDirs as $dir) {
    if (file_exists($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        $writable = is_writable($dir) ? '✅' : '❌';
        echo "  {$writable} {$dir} (permissions: {$perms})\n";
        
        if (!is_writable($dir)) {
            chmod($dir, 0755);
            echo "    🔧 Fixed permissions to 755\n";
        }
    } else {
        echo "  ⚠️  {$dir} does not exist\n";
    }
}

// Step 4: Environment Configuration
echo "\n⚙️  Step 4: Environment Configuration\n";

$envFile = $testDir . '/.env';
if (!file_exists($envFile)) {
    $envContent = "APP_NAME=\"Target Management System (Test)\"
APP_ENV=testing
APP_KEY=
APP_DEBUG=true
APP_URL=https://mkalrawi.com/{$testAppName}

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE={$testAppName}_db
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DRIVER=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME=\"Target Management System\"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY=\"\${PUSHER_APP_KEY}\"
MIX_PUSHER_APP_CLUSTER=\"\${PUSHER_APP_CLUSTER}\"
";

    file_put_contents($envFile, $envContent);
    echo "✅ Created .env file for test environment\n";
} else {
    echo "ℹ️  .env file already exists\n";
}

// Step 5: Composer Dependencies Check
echo "\n📦 Step 5: Composer Dependencies Check\n";

$composerJson = $testDir . '/composer.json';
$vendorDir = $testDir . '/vendor';

if (file_exists($composerJson)) {
    echo "✅ composer.json found\n";
    
    if (!file_exists($vendorDir)) {
        echo "❌ Vendor directory not found - Dependencies need to be installed\n";
        echo "📝 Run: cd {$testDir} && composer install --no-dev --optimize-autoloader\n";
    } else {
        echo "✅ Vendor directory exists\n";
    }
} else {
    echo "❌ composer.json not found\n";
}

// Step 6: Laravel Configuration
echo "\n🎯 Step 6: Laravel Configuration Check\n";

$configApp = $testDir . '/config/app.php';
if (file_exists($configApp)) {
    echo "✅ Laravel config files found\n";
    
    // Check if app key is set
    $envContent = file_exists($envFile) ? file_get_contents($envFile) : '';
    if (strpos($envContent, 'APP_KEY=base64:') === false) {
        echo "❌ APP_KEY not generated\n";
        echo "📝 Run: php artisan key:generate\n";
    } else {
        echo "✅ APP_KEY is set\n";
    }
} else {
    echo "❌ Laravel config files not found\n";
}

// Step 7: Database Connection Test
echo "\n🗄️  Step 7: Database Connection Test\n";

try {
    if (file_exists($envFile)) {
        $envVars = parse_ini_file($envFile);
        $dbHost = $envVars['DB_HOST'] ?? 'localhost';
        $dbName = $envVars['DB_DATABASE'] ?? '';
        $dbUser = $envVars['DB_USERNAME'] ?? '';
        $dbPass = $envVars['DB_PASSWORD'] ?? '';
        
        if (!empty($dbName) && !empty($dbUser)) {
            $pdo = new PDO("mysql:host={$dbHost};dbname={$dbName}", $dbUser, $dbPass);
            echo "✅ Database connection successful\n";
        } else {
            echo "⚠️  Database credentials not fully configured\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}

// Step 8: Web Server Configuration
echo "\n🌐 Step 8: Web Server Configuration\n";

$htaccessFile = $publicDir . '/.htaccess';
$htaccessContent = "<IfModule mod_rewrite.c>
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

# Deny access to sensitive files
<Files \".env\">
    Order allow,deny
    Deny from all
</Files>

<Files \"composer.json\">
    Order allow,deny
    Deny from all
</Files>

<Files \"composer.lock\">
    Order allow,deny
    Deny from all
</Files>
";

if (!file_exists($htaccessFile)) {
    file_put_contents($htaccessFile, $htaccessContent);
    echo "✅ Created .htaccess file\n";
} else {
    echo "ℹ️  .htaccess file already exists\n";
}

// Step 9: Index.php for public directory
echo "\n🎯 Step 9: Public Index Configuration\n";

$indexFile = $publicDir . '/index.php';
$indexContent = "<?php

use Illuminate\\Contracts\\Http\\Kernel;
use Illuminate\\Http\\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the \"down\" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists(__DIR__.'/../storage/framework/maintenance.php')) {
    require __DIR__.'/../storage/framework/maintenance.php';
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

\$app = require_once __DIR__.'/../bootstrap/app.php';

\$kernel = \$app->make(Kernel::class);

\$response = \$kernel->handle(
    \$request = Request::capture()
)->send();

\$kernel->terminate(\$request, \$response);
";

if (!file_exists($indexFile)) {
    file_put_contents($indexFile, $indexContent);
    echo "✅ Created public/index.php\n";
} else {
    echo "ℹ️  public/index.php already exists\n";
}

// Step 10: Generate Deployment Summary
echo "\n📋 Step 10: Deployment Summary\n";
echo "=============================\n";

$issues = [];

// Check for common issues
if (!extension_loaded('pdo_mysql')) {
    $issues[] = "❌ PDO MySQL extension missing";
}

if (!file_exists($vendorDir)) {
    $issues[] = "❌ Composer dependencies not installed";
}

if (!file_exists($configApp)) {
    $issues[] = "❌ Laravel application files missing";
}

$envContent = file_exists($envFile) ? file_get_contents($envFile) : '';
if (strpos($envContent, 'APP_KEY=base64:') === false) {
    $issues[] = "❌ Laravel APP_KEY not generated";
}

if (empty($issues)) {
    echo "🎉 All checks passed! The test environment should be ready.\n";
} else {
    echo "⚠️  Issues found that need to be resolved:\n";
    foreach ($issues as $issue) {
        echo "  {$issue}\n";
    }
}

echo "\n📝 Next Steps:\n";
echo "1. Upload Laravel application files to: {$testDir}\n";
echo "2. Run: composer install --no-dev --optimize-autoloader\n";
echo "3. Run: php artisan key:generate\n";
echo "4. Configure database credentials in .env\n";
echo "5. Run: php artisan migrate\n";
echo "6. Run: php artisan db:seed\n";
echo "7. Set file permissions: chmod -R 755 storage bootstrap/cache\n";
echo "8. Test access: https://mkalrawi.com/{$testAppName}/public/\n";

echo "\n🆘 Troubleshooting 500 Errors:\n";
echo "- Check error logs: {$testDir}/storage/logs/laravel.log\n";
echo "- Enable debug mode: APP_DEBUG=true in .env\n";
echo "- Check PHP error logs in cPanel\n";
echo "- Verify file permissions (755 for directories, 644 for files)\n";
echo "- Ensure all PHP extensions are installed\n";

echo "\n✅ Deployment script completed!\n";
?>