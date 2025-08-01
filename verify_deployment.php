<?php
/**
 * Target Management System - Deployment Verification Script
 * 
 * This script verifies that the deployment was successful and all
 * components are working correctly.
 */

echo "🔍 Target Management System - Deployment Verification\n";
echo "===================================================\n\n";

$checks = [];
$errors = [];

// Check 1: Laravel Installation
echo "1. ✅ Checking Laravel installation...\n";
if (file_exists('artisan')) {
    $checks['laravel'] = true;
    echo "   ✅ Laravel artisan found\n";
} else {
    $checks['laravel'] = false;
    $errors[] = "Laravel artisan file not found";
    echo "   ❌ Laravel artisan file not found\n";
}

// Check 2: Environment Configuration
echo "\n2. 🔧 Checking environment configuration...\n";
if (file_exists('.env')) {
    $checks['env'] = true;
    echo "   ✅ .env file found\n";
    
    // Check if APP_KEY is set
    $envContent = file_get_contents('.env');
    if (strpos($envContent, 'APP_KEY=base64:') !== false) {
        echo "   ✅ Application key is set\n";
    } else {
        $errors[] = "Application key not generated";
        echo "   ⚠️  Application key not set\n";
    }
} else {
    $checks['env'] = false;
    $errors[] = ".env file not found";
    echo "   ❌ .env file not found\n";
}

// Check 3: Database Connection
echo "\n3. 🗄️  Checking database connection...\n";
try {
    exec('php artisan tinker --execute="DB::connection()->getPdo(); echo \"connected\";" 2>&1', $dbOutput, $dbReturn);
    if ($dbReturn === 0 && strpos(implode("\n", $dbOutput), 'connected') !== false) {
        $checks['database'] = true;
        echo "   ✅ Database connection successful\n";
    } else {
        $checks['database'] = false;
        $errors[] = "Database connection failed";
        echo "   ❌ Database connection failed\n";
    }
} catch (Exception $e) {
    $checks['database'] = false;
    $errors[] = "Database connection error: " . $e->getMessage();
    echo "   ❌ Database connection error\n";
}

// Check 4: Required Directories and Permissions
echo "\n4. 📁 Checking directories and permissions...\n";
$requiredDirs = ['storage', 'storage/logs', 'storage/framework', 'bootstrap/cache'];
$dirChecks = true;

foreach ($requiredDirs as $dir) {
    if (is_dir($dir) && is_writable($dir)) {
        echo "   ✅ $dir exists and is writable\n";
    } else {
        $dirChecks = false;
        $errors[] = "Directory $dir is not writable or doesn't exist";
        echo "   ❌ $dir is not writable or doesn't exist\n";
    }
}
$checks['directories'] = $dirChecks;

// Check 5: Database Tables
echo "\n5. 📊 Checking database tables...\n";
if ($checks['database']) {
    try {
        exec('php artisan tinker --execute="echo \'Tables: \' . count(DB::select(\'SHOW TABLES\'));" 2>&1', $tableOutput, $tableReturn);
        if ($tableReturn === 0 && strpos(implode("\n", $tableOutput), 'Tables:') !== false) {
            $checks['tables'] = true;
            echo "   ✅ Database tables found\n";
        } else {
            $checks['tables'] = false;
            $errors[] = "Database tables not found - run migrations";
            echo "   ❌ Database tables not found\n";
        }
    } catch (Exception $e) {
        $checks['tables'] = false;
        $errors[] = "Error checking database tables";
        echo "   ❌ Error checking database tables\n";
    }
} else {
    $checks['tables'] = false;
    echo "   ⏭️  Skipped (database not connected)\n";
}

// Check 6: Web Server Configuration
echo "\n6. 🌐 Checking web server configuration...\n";
if (file_exists('public/.htaccess')) {
    $checks['htaccess'] = true;
    echo "   ✅ .htaccess file found in public directory\n";
} else {
    $checks['htaccess'] = false;
    $errors[] = "public/.htaccess file not found";
    echo "   ❌ public/.htaccess file not found\n";
}

// Check 7: Composer Dependencies
echo "\n7. 📦 Checking Composer dependencies...\n";
if (is_dir('vendor') && file_exists('vendor/autoload.php')) {
    $checks['composer'] = true;
    echo "   ✅ Composer dependencies installed\n";
} else {
    $checks['composer'] = false;
    $errors[] = "Composer dependencies not installed";
    echo "   ❌ Composer dependencies not installed\n";
}

// Summary
echo "\n📋 Verification Summary\n";
echo "======================\n";

$totalChecks = count($checks);
$passedChecks = count(array_filter($checks));

foreach ($checks as $check => $status) {
    $icon = $status ? "✅" : "❌";
    echo "$icon " . ucfirst($check) . "\n";
}

echo "\nResult: $passedChecks/$totalChecks checks passed\n";

if (empty($errors)) {
    echo "\n🎉 All checks passed! Your deployment is successful.\n";
    echo "\n📋 Next steps:\n";
    echo "1. Visit your domain to test the application\n";
    echo "2. Login with admin credentials\n";
    echo "3. Change default password immediately\n";
    echo "4. Test all functionality\n";
} else {
    echo "\n⚠️  Issues found that need attention:\n";
    foreach ($errors as $error) {
        echo "   • $error\n";
    }
    echo "\n💡 Please fix these issues and run the verification again.\n";
}

echo "\n🔍 Verification completed.\n";
?>