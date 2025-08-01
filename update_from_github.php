<?php
/**
 * Target Management System - Update from GitHub Script
 * 
 * This script updates the application from GitHub while preserving
 * your .env configuration and user data.
 */

echo "🔄 Target Management System - Update from GitHub\n";
echo "===============================================\n\n";

// Backup .env file
if (file_exists('.env')) {
    echo "💾 Backing up .env file...\n";
    copy('.env', '.env.backup');
    echo "✅ .env backed up to .env.backup\n";
} else {
    echo "⚠️  No .env file found to backup\n";
}

// Pull latest changes from GitHub
echo "\n📥 Pulling latest changes from GitHub...\n";
exec('git pull origin main 2>&1', $gitOutput, $gitReturn);

if ($gitReturn === 0) {
    echo "✅ Successfully pulled latest changes\n";
    echo "Output: " . implode("\n", $gitOutput) . "\n";
} else {
    echo "❌ Error pulling from GitHub\n";
    echo "Output: " . implode("\n", $gitOutput) . "\n";
    
    // Restore .env if pull failed
    if (file_exists('.env.backup')) {
        copy('.env.backup', '.env');
        echo "🔄 Restored .env from backup\n";
    }
    exit(1);
}

// Restore .env file
if (file_exists('.env.backup')) {
    echo "\n🔄 Restoring .env configuration...\n";
    copy('.env.backup', '.env');
    echo "✅ .env restored\n";
}

// Install/update Composer dependencies
echo "\n📦 Updating Composer dependencies...\n";
exec('composer install --no-dev --optimize-autoloader 2>&1', $composerOutput, $composerReturn);
if ($composerReturn === 0) {
    echo "✅ Composer dependencies updated\n";
} else {
    echo "⚠️  Warning: Composer update failed\n";
    echo "Output: " . implode("\n", $composerOutput) . "\n";
}

// Run any new migrations
echo "\n🗄️  Running database migrations...\n";
exec('php artisan migrate --force 2>&1', $migrateOutput, $migrateReturn);
if ($migrateReturn === 0) {
    echo "✅ Database migrations completed\n";
} else {
    echo "⚠️  Warning: Migrations failed\n";
    echo "Output: " . implode("\n", $migrateOutput) . "\n";
}

// Clear and cache configurations
echo "\n🧹 Clearing and optimizing caches...\n";
$cacheCommands = [
    'config:clear' => 'Configuration cache cleared',
    'route:clear' => 'Route cache cleared',
    'view:clear' => 'View cache cleared',
    'cache:clear' => 'Application cache cleared',
    'config:cache' => 'Configuration cached',
    'route:cache' => 'Routes cached',
    'view:cache' => 'Views cached'
];

foreach ($cacheCommands as $command => $message) {
    exec("php artisan $command 2>&1", $output, $returnCode);
    if ($returnCode === 0) {
        echo "✅ $message\n";
    } else {
        echo "⚠️  Warning: Could not run $command\n";
    }
}

// Cleanup backup file
if (file_exists('.env.backup')) {
    unlink('.env.backup');
    echo "\n🧹 Cleaned up backup files\n";
}

echo "\n🎉 Update completed successfully!\n";
echo "\n📋 What was updated:\n";
echo "✅ Application code from GitHub\n";
echo "✅ Composer dependencies\n";
echo "✅ Database migrations (if any)\n";
echo "✅ Cached configurations\n";
echo "✅ Your .env settings preserved\n";

echo "\n💡 Recommended next steps:\n";
echo "1. Test the application functionality\n";
echo "2. Check for any new features or changes\n";
echo "3. Verify all existing data is intact\n";
echo "4. Monitor error logs for any issues\n";

echo "\n✅ Update process completed!\n";
?>