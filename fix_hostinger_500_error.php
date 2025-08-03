<?php
/**
 * Hostinger 500 Error Diagnostic & Fix Tool
 * For Target Management System Test Deployment
 */

// Prevent direct access in production
if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') === false) {
    // Add basic authentication for security
    if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
        $_SERVER['PHP_AUTH_USER'] !== 'admin' || $_SERVER['PHP_AUTH_PW'] !== 'DiagnosticTool2024') {
        header('WWW-Authenticate: Basic realm="Diagnostic Tool"');
        header('HTTP/1.0 401 Unauthorized');
        exit('Access denied. Use username: admin, password: DiagnosticTool2024');
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Target Management System - 500 Error Diagnostic</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #2563eb; }
        .header h1 { color: #2563eb; margin: 0; }
        .section { margin: 20px 0; padding: 20px; border-left: 4px solid #2563eb; background: #f8fafc; }
        .check { margin: 10px 0; padding: 10px; background: white; border-radius: 5px; }
        .success { color: #059669; font-weight: bold; }
        .error { color: #dc2626; font-weight: bold; }
        .warning { color: #d97706; font-weight: bold; }
        .info { color: #0891b2; font-weight: bold; }
        .code { background: #1f2937; color: #f9fafb; padding: 15px; border-radius: 5px; font-family: 'Courier New', monospace; overflow-x: auto; }
        .button { display: inline-block; padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
        .button:hover { background: #1d4ed8; }
        .fix-section { margin: 20px 0; padding: 20px; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 5px; }
        pre { background: #f3f4f6; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎯 Target Management System</h1>
            <h2>500 Error Diagnostic Tool</h2>
            <p>Comprehensive analysis and fixes for Hostinger deployment issues</p>
        </div>

        <?php
        $testAppPath = dirname($_SERVER['DOCUMENT_ROOT']) . '/newtarget';
        $currentPath = __DIR__;
        $issues = [];
        $fixes = [];

        echo "<div class='section'>";
        echo "<h3>📊 Environment Information</h3>";
        echo "<div class='check'>";
        echo "<strong>PHP Version:</strong> " . PHP_VERSION . "<br>";
        echo "<strong>Current Path:</strong> " . $currentPath . "<br>";
        echo "<strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
        echo "<strong>Test App Path:</strong> " . $testAppPath . "<br>";
        echo "<strong>Server Software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "<br>";
        echo "<strong>User Agent:</strong> " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown') . "<br>";
        echo "</div>";
        echo "</div>";

        // Check PHP Extensions
        echo "<div class='section'>";
        echo "<h3>🔧 PHP Extensions Check</h3>";
        $requiredExtensions = [
            'pdo' => 'PDO Database Support',
            'pdo_mysql' => 'MySQL PDO Driver',
            'mbstring' => 'Multibyte String Support',
            'tokenizer' => 'Tokenizer Support',
            'xml' => 'XML Support',
            'ctype' => 'Character Type Support',
            'json' => 'JSON Support',
            'bcmath' => 'BC Math Support',
            'curl' => 'cURL Support',
            'fileinfo' => 'File Information Support',
            'zip' => 'ZIP Archive Support'
        ];

        foreach ($requiredExtensions as $ext => $description) {
            echo "<div class='check'>";
            if (extension_loaded($ext)) {
                echo "<span class='success'>✅ {$description} ({$ext})</span>";
            } else {
                echo "<span class='error'>❌ {$description} ({$ext}) - MISSING</span>";
                $issues[] = "Missing PHP extension: {$ext}";
                $fixes[] = "Contact Hostinger support to enable PHP extension: {$ext}";
            }
            echo "</div>";
        }
        echo "</div>";

        // Check Directory Structure
        echo "<div class='section'>";
        echo "<h3>📁 Directory Structure Check</h3>";
        
        $checkPaths = [
            $testAppPath => 'Test Application Root',
            $testAppPath . '/app' => 'Laravel App Directory',
            $testAppPath . '/config' => 'Configuration Directory',
            $testAppPath . '/public' => 'Public Directory',
            $testAppPath . '/storage' => 'Storage Directory',
            $testAppPath . '/storage/logs' => 'Logs Directory',
            $testAppPath . '/storage/framework' => 'Framework Storage',
            $testAppPath . '/storage/framework/cache' => 'Cache Directory',
            $testAppPath . '/storage/framework/sessions' => 'Sessions Directory',
            $testAppPath . '/storage/framework/views' => 'Views Cache',
            $testAppPath . '/bootstrap/cache' => 'Bootstrap Cache',
            $testAppPath . '/vendor' => 'Vendor Directory',
            $testAppPath . '/.env' => 'Environment File'
        ];

        foreach ($checkPaths as $path => $description) {
            echo "<div class='check'>";
            if (file_exists($path)) {
                $perms = is_dir($path) ? substr(sprintf('%o', fileperms($path)), -4) : 'file';
                $writable = is_writable($path) ? 'writable' : 'not writable';
                echo "<span class='success'>✅ {$description}</span> - {$perms} ({$writable})";
            } else {
                echo "<span class='error'>❌ {$description} - NOT FOUND</span>";
                $issues[] = "Missing: {$description}";
                if (strpos($path, 'storage') !== false || strpos($path, 'cache') !== false) {
                    $fixes[] = "Create directory: {$path} with permissions 755";
                }
            }
            echo "</div>";
        }
        echo "</div>";

        // Check .env Configuration
        echo "<div class='section'>";
        echo "<h3>⚙️ Environment Configuration</h3>";
        
        $envFile = $testAppPath . '/.env';
        if (file_exists($envFile)) {
            echo "<div class='check'><span class='success'>✅ .env file exists</span></div>";
            
            $envContent = file_get_contents($envFile);
            $envLines = explode("\n", $envContent);
            $envVars = [];
            
            foreach ($envLines as $line) {
                if (strpos($line, '=') !== false && !str_starts_with(trim($line), '#')) {
                    list($key, $value) = explode('=', $line, 2);
                    $envVars[trim($key)] = trim($value);
                }
            }
            
            $requiredEnvVars = [
                'APP_NAME' => 'Application Name',
                'APP_ENV' => 'Environment',
                'APP_KEY' => 'Application Key',
                'APP_DEBUG' => 'Debug Mode',
                'APP_URL' => 'Application URL',
                'DB_CONNECTION' => 'Database Connection',
                'DB_HOST' => 'Database Host',
                'DB_DATABASE' => 'Database Name',
                'DB_USERNAME' => 'Database Username',
                'DB_PASSWORD' => 'Database Password'
            ];
            
            foreach ($requiredEnvVars as $var => $description) {
                echo "<div class='check'>";
                if (isset($envVars[$var]) && !empty($envVars[$var])) {
                    if ($var === 'APP_KEY') {
                        if (str_starts_with($envVars[$var], 'base64:')) {
                            echo "<span class='success'>✅ {$description}: Properly generated</span>";
                        } else {
                            echo "<span class='error'>❌ {$description}: Not properly generated</span>";
                            $issues[] = "APP_KEY not properly generated";
                            $fixes[] = "Run: php artisan key:generate";
                        }
                    } elseif ($var === 'DB_PASSWORD') {
                        echo "<span class='success'>✅ {$description}: Set</span>";
                    } else {
                        echo "<span class='success'>✅ {$description}: {$envVars[$var]}</span>";
                    }
                } else {
                    echo "<span class='error'>❌ {$description}: Not set</span>";
                    $issues[] = "Missing environment variable: {$var}";
                }
                echo "</div>";
            }
        } else {
            echo "<div class='check'><span class='error'>❌ .env file not found</span></div>";
            $issues[] = "Missing .env file";
            $fixes[] = "Create .env file based on .env.example";
        }
        echo "</div>";

        // Check Composer Dependencies
        echo "<div class='section'>";
        echo "<h3>📦 Composer Dependencies</h3>";
        
        $composerJson = $testAppPath . '/composer.json';
        $vendorDir = $testAppPath . '/vendor';
        $autoloadFile = $vendorDir . '/autoload.php';
        
        if (file_exists($composerJson)) {
            echo "<div class='check'><span class='success'>✅ composer.json found</span></div>";
            
            if (file_exists($vendorDir)) {
                echo "<div class='check'><span class='success'>✅ vendor directory exists</span></div>";
                
                if (file_exists($autoloadFile)) {
                    echo "<div class='check'><span class='success'>✅ Composer autoloader exists</span></div>";
                } else {
                    echo "<div class='check'><span class='error'>❌ Composer autoloader missing</span></div>";
                    $issues[] = "Composer autoloader not found";
                    $fixes[] = "Run: composer install --no-dev --optimize-autoloader";
                }
            } else {
                echo "<div class='check'><span class='error'>❌ vendor directory missing</span></div>";
                $issues[] = "Composer dependencies not installed";
                $fixes[] = "Run: composer install --no-dev --optimize-autoloader";
            }
        } else {
            echo "<div class='check'><span class='error'>❌ composer.json not found</span></div>";
            $issues[] = "Project files not properly uploaded";
        }
        echo "</div>";

        // Database Connection Test
        echo "<div class='section'>";
        echo "<h3>🗄️ Database Connection Test</h3>";
        
        if (file_exists($envFile)) {
            $dbHost = $envVars['DB_HOST'] ?? 'localhost';
            $dbName = $envVars['DB_DATABASE'] ?? '';
            $dbUser = $envVars['DB_USERNAME'] ?? '';
            $dbPass = $envVars['DB_PASSWORD'] ?? '';
            
            if (!empty($dbName) && !empty($dbUser)) {
                try {
                    $pdo = new PDO("mysql:host={$dbHost};dbname={$dbName}", $dbUser, $dbPass);
                    echo "<div class='check'><span class='success'>✅ Database connection successful</span></div>";
                    
                    // Check if tables exist
                    $stmt = $pdo->query("SHOW TABLES");
                    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    
                    if (count($tables) > 0) {
                        echo "<div class='check'><span class='success'>✅ Database has " . count($tables) . " tables</span></div>";
                    } else {
                        echo "<div class='check'><span class='warning'>⚠️ Database is empty - migrations needed</span></div>";
                        $fixes[] = "Run: php artisan migrate";
                        $fixes[] = "Run: php artisan db:seed";
                    }
                } catch (Exception $e) {
                    echo "<div class='check'><span class='error'>❌ Database connection failed: " . $e->getMessage() . "</span></div>";
                    $issues[] = "Database connection failed";
                    $fixes[] = "Check database credentials in .env file";
                    $fixes[] = "Ensure database exists in cPanel";
                    $fixes[] = "Verify database user has proper permissions";
                }
            } else {
                echo "<div class='check'><span class='warning'>⚠️ Database credentials incomplete</span></div>";
                $issues[] = "Database credentials not configured";
            }
        }
        echo "</div>";

        // Check Laravel Configuration
        echo "<div class='section'>";
        echo "<h3>🎯 Laravel Configuration</h3>";
        
        $configApp = $testAppPath . '/config/app.php';
        $routesWeb = $testAppPath . '/routes/web.php';
        $appBootstrap = $testAppPath . '/bootstrap/app.php';
        
        $laravelFiles = [
            $configApp => 'App Configuration',
            $routesWeb => 'Web Routes',
            $appBootstrap => 'Bootstrap File'
        ];
        
        foreach ($laravelFiles as $file => $description) {
            echo "<div class='check'>";
            if (file_exists($file)) {
                echo "<span class='success'>✅ {$description}</span>";
            } else {
                echo "<span class='error'>❌ {$description} missing</span>";
                $issues[] = "Missing Laravel file: {$description}";
            }
            echo "</div>";
        }
        
        // Check cache files
        $cacheConfig = $testAppPath . '/bootstrap/cache/config.php';
        $cacheRoutes = $testAppPath . '/bootstrap/cache/routes-v7.php';
        
        if (file_exists($cacheConfig) || file_exists($cacheRoutes)) {
            echo "<div class='check'><span class='warning'>⚠️ Cache files found - may need clearing</span></div>";
            $fixes[] = "Clear cache: php artisan cache:clear";
            $fixes[] = "Clear config: php artisan config:clear";
            $fixes[] = "Clear routes: php artisan route:clear";
        }
        echo "</div>";

        // Check .htaccess
        echo "<div class='section'>";
        echo "<h3>🌐 Web Server Configuration</h3>";
        
        $htaccessFile = $testAppPath . '/public/.htaccess';
        if (file_exists($htaccessFile)) {
            echo "<div class='check'><span class='success'>✅ .htaccess file exists</span></div>";
            
            $htaccessContent = file_get_contents($htaccessFile);
            if (strpos($htaccessContent, 'RewriteEngine On') !== false) {
                echo "<div class='check'><span class='success'>✅ URL rewriting enabled</span></div>";
            } else {
                echo "<div class='check'><span class='warning'>⚠️ URL rewriting may not be configured</span></div>";
            }
        } else {
            echo "<div class='check'><span class='error'>❌ .htaccess file missing</span></div>";
            $issues[] = "Missing .htaccess file";
            $fixes[] = "Create .htaccess file in public directory";
        }
        echo "</div>";

        // Display Issues and Fixes
        if (!empty($issues)) {
            echo "<div class='fix-section'>";
            echo "<h3>🚨 Issues Found</h3>";
            foreach ($issues as $issue) {
                echo "<div class='check'><span class='error'>❌ {$issue}</span></div>";
            }
            echo "</div>";
        }

        if (!empty($fixes)) {
            echo "<div class='fix-section'>";
            echo "<h3>🔧 Recommended Fixes</h3>";
            foreach ($fixes as $fix) {
                echo "<div class='check'><span class='info'>🔧 {$fix}</span></div>";
            }
            echo "</div>";
        }

        if (empty($issues)) {
            echo "<div class='section' style='background: #ecfdf5; border-left-color: #059669;'>";
            echo "<h3 style='color: #059669;'>🎉 All Checks Passed!</h3>";
            echo "<p>Your Target Management System test environment appears to be properly configured.</p>";
            echo "<p><strong>Test URL:</strong> <a href='https://mkalrawi.com/newtarget/public/' target='_blank'>https://mkalrawi.com/newtarget/public/</a></p>";
            echo "</div>";
        }
        ?>

        <div class="section">
            <h3>🛠️ Quick Actions</h3>
            <a href="?action=phpinfo" class="button">PHP Info</a>
            <a href="?action=permissions" class="button">Fix Permissions</a>
            <a href="?action=env" class="button">Show Environment</a>
            <a href="?action=logs" class="button">Show Logs</a>
        </div>

        <?php
        // Handle quick actions
        if (isset($_GET['action'])) {
            echo "<div class='section'>";
            
            switch ($_GET['action']) {
                case 'phpinfo':
                    echo "<h3>📋 PHP Information</h3>";
                    echo "<div class='code'>";
                    ob_start();
                    phpinfo();
                    $phpinfo = ob_get_clean();
                    echo strip_tags($phpinfo);
                    echo "</div>";
                    break;
                    
                case 'permissions':
                    echo "<h3>🔒 File Permissions</h3>";
                    $permDirs = [
                        $testAppPath . '/storage',
                        $testAppPath . '/storage/logs',
                        $testAppPath . '/storage/framework',
                        $testAppPath . '/storage/framework/cache',
                        $testAppPath . '/storage/framework/sessions',
                        $testAppPath . '/storage/framework/views',
                        $testAppPath . '/bootstrap/cache'
                    ];
                    
                    foreach ($permDirs as $dir) {
                        if (file_exists($dir)) {
                            $perms = substr(sprintf('%o', fileperms($dir)), -4);
                            echo "<div class='check'>Directory: {$dir} - Permissions: {$perms}</div>";
                        }
                    }
                    break;
                    
                case 'env':
                    echo "<h3>⚙️ Environment Variables</h3>";
                    if (file_exists($envFile)) {
                        echo "<pre>";
                        $envContent = file_get_contents($envFile);
                        // Hide sensitive information
                        $envContent = preg_replace('/DB_PASSWORD=.*/', 'DB_PASSWORD=***HIDDEN***', $envContent);
                        $envContent = preg_replace('/MAIL_PASSWORD=.*/', 'MAIL_PASSWORD=***HIDDEN***', $envContent);
                        echo htmlspecialchars($envContent);
                        echo "</pre>";
                    } else {
                        echo "<p class='error'>Environment file not found</p>";
                    }
                    break;
                    
                case 'logs':
                    echo "<h3>📋 Application Logs</h3>";
                    $logFile = $testAppPath . '/storage/logs/laravel.log';
                    if (file_exists($logFile)) {
                        echo "<pre>";
                        $logs = file_get_contents($logFile);
                        echo htmlspecialchars(substr($logs, -5000)); // Last 5KB
                        echo "</pre>";
                    } else {
                        echo "<p class='warning'>No log file found</p>";
                    }
                    break;
            }
            
            echo "</div>";
        }
        ?>

        <div class="section">
            <h3>📞 Support Information</h3>
            <p><strong>Diagnostic completed:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
            <p><strong>Server Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
            <p><strong>PHP Memory Limit:</strong> <?php echo ini_get('memory_limit'); ?></p>
            <p><strong>Max Execution Time:</strong> <?php echo ini_get('max_execution_time'); ?>s</p>
            <p><strong>Upload Max Filesize:</strong> <?php echo ini_get('upload_max_filesize'); ?></p>
        </div>
    </div>
</body>
</html>