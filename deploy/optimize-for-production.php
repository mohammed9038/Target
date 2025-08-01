<?php
/**
 * Production Optimization Script
 * Prepares the Laravel application for production deployment
 */

echo "=== Production Optimization ===\n\n";

// 1. Clear any existing caches
$commands = [
    'php artisan config:clear',
    'php artisan route:clear', 
    'php artisan view:clear',
    'php artisan cache:clear'
];

foreach ($commands as $command) {
    echo "Running: $command\n";
    $output = shell_exec($command . ' 2>&1');
    if ($output) {
        echo "  Output: " . trim($output) . "\n";
    }
}

echo "\n✓ Cleared all caches\n";

// 2. Optimize composer autoloader
echo "\nOptimizing Composer autoloader...\n";
$output = shell_exec('composer install --optimize-autoloader --no-dev 2>&1');
if ($output) {
    echo "Composer output:\n$output\n";
}

// 3. Create production caches (these will be done on server)
echo "\n=== Commands to run ON THE SERVER ===\n";
echo "php artisan config:cache\n";
echo "php artisan route:cache\n";
echo "php artisan view:cache\n";
echo "php artisan migrate --force\n";
echo "php artisan db:seed --force\n";

// 4. Check for production readiness
echo "\n=== Production Readiness Check ===\n";

// Check .env template
if (file_exists('deploy/.env.hostinger')) {
    echo "✓ Production .env template created\n";
} else {
    echo "✗ Missing production .env template\n";
}

// Check .htaccess
if (file_exists('deploy/.htaccess.hostinger')) {
    echo "✓ Production .htaccess created\n";
} else {
    echo "✗ Missing production .htaccess\n";
}

// Check critical files
$criticalFiles = [
    'composer.json',
    'composer.lock', 
    'artisan',
    'public/index.php',
    'app/Http/Kernel.php'
];

foreach ($criticalFiles as $file) {
    if (file_exists($file)) {
        echo "✓ $file exists\n";
    } else {
        echo "✗ Missing critical file: $file\n";
    }
}

// Check storage directories
$storageDirectories = [
    'storage/app',
    'storage/framework/cache', 
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache'
];

foreach ($storageDirectories as $dir) {
    if (is_dir($dir) && is_writable($dir)) {
        echo "✓ $dir is writable\n";
    } else {
        echo "⚠ $dir needs write permissions\n";
    }
}

echo "\n=== Files to EXCLUDE from upload ===\n";
echo "- .env (use deploy/.env.hostinger instead)\n";
echo "- .git/\n";
echo "- node_modules/\n";
echo "- storage/logs/*.log\n";
echo "- tests/\n";
echo "- .phpunit.result.cache\n";

echo "\n=== Ready for Deployment! ===\n";
echo "Follow deploy/DEPLOYMENT_CHECKLIST.md for next steps\n";
?>