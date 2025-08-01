<?php
/**
 * Target Management System - Hostinger Deployment Script
 * 
 * This script automates the deployment process for Hostinger hosting.
 * Run this script AFTER uploading files to your Hostinger account.
 * 
 * IMPORTANT: Make sure you have:
 * 1. Uploaded all files to your Hostinger public_html directory
 * 2. Created a MySQL database in Hostinger control panel
 * 3. Updated the .env file with your database credentials
 */

echo "🚀 Target Management System - Hostinger Deployment Script\n";
echo "========================================================\n\n";

// Check if we're in the correct directory
if (!file_exists('artisan')) {
    die("❌ Error: This script must be run from the Laravel root directory.\n");
}

// Check if .env file exists
if (!file_exists('.env')) {
    echo "⚠️  Warning: .env file not found!\n";
    echo "📋 Please copy env.production.example to .env and update database credentials.\n";
    
    if (file_exists('env.production.example')) {
        echo "💡 Copying env.production.example to .env...\n";
        copy('env.production.example', '.env');
        echo "✅ .env file created. Please edit it with your database credentials.\n\n";
    } else {
        die("❌ Error: env.production.example file not found.\n");
    }
}

echo "🔧 Step 1: Setting up application...\n";

// Generate application key if not set
echo "🔑 Generating application key...\n";
exec('php artisan key:generate --force 2>&1', $output, $returnCode);
if ($returnCode === 0) {
    echo "✅ Application key generated successfully.\n";
} else {
    echo "⚠️  Warning: Could not generate application key. Output: " . implode("\n", $output) . "\n";
}

echo "\n🔧 Step 2: Optimizing application...\n";

// Clear and cache configuration
echo "🧹 Clearing caches...\n";
$commands = [
    'config:clear' => 'Configuration cache cleared',
    'route:clear' => 'Route cache cleared',
    'view:clear' => 'View cache cleared',
    'cache:clear' => 'Application cache cleared'
];

foreach ($commands as $command => $message) {
    exec("php artisan $command 2>&1", $output, $returnCode);
    if ($returnCode === 0) {
        echo "✅ $message.\n";
    } else {
        echo "⚠️  Warning: Could not run $command.\n";
    }
}

echo "\n🔧 Step 3: Setting up database...\n";

// Test database connection
echo "🔍 Testing database connection...\n";
exec('php artisan tinker --execute="DB::connection()->getPdo(); echo \"Database connected successfully\";" 2>&1', $dbOutput, $dbReturn);

if ($dbReturn === 0 && strpos(implode("\n", $dbOutput), 'Database connected successfully') !== false) {
    echo "✅ Database connection successful.\n";
    
    // Run migrations
    echo "🗄️  Running database migrations...\n";
    exec('php artisan migrate --force 2>&1', $migrateOutput, $migrateReturn);
    if ($migrateReturn === 0) {
        echo "✅ Database migrations completed successfully.\n";
        
        // Seed database
        echo "🌱 Seeding database with initial data...\n";
        exec('php artisan db:seed --force 2>&1', $seedOutput, $seedReturn);
        if ($seedReturn === 0) {
            echo "✅ Database seeded successfully.\n";
        } else {
            echo "⚠️  Warning: Database seeding failed. You may need to seed manually.\n";
            echo "Output: " . implode("\n", $seedOutput) . "\n";
        }
    } else {
        echo "❌ Error: Database migrations failed.\n";
        echo "Output: " . implode("\n", $migrateOutput) . "\n";
    }
} else {
    echo "❌ Error: Cannot connect to database.\n";
    echo "Please check your .env database configuration.\n";
    echo "Output: " . implode("\n", $dbOutput) . "\n";
}

echo "\n🔧 Step 4: Setting file permissions...\n";

// Set proper permissions for storage and bootstrap/cache
$directories = [
    'storage',
    'storage/app',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache'
];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        chmod($dir, 0755);
        echo "✅ Set permissions for $dir\n";
    } else {
        echo "⚠️  Directory $dir does not exist, creating...\n";
        mkdir($dir, 0755, true);
        echo "✅ Created and set permissions for $dir\n";
    }
}

echo "\n🔧 Step 5: Optimizing for production...\n";

// Cache configurations for production
$productionCommands = [
    'config:cache' => 'Configuration cached',
    'route:cache' => 'Routes cached',
    'view:cache' => 'Views cached'
];

foreach ($productionCommands as $command => $message) {
    exec("php artisan $command 2>&1", $output, $returnCode);
    if ($returnCode === 0) {
        echo "✅ $message.\n";
    } else {
        echo "⚠️  Warning: Could not run $command.\n";
    }
}

echo "\n🎉 Deployment Summary\n";
echo "====================\n";
echo "✅ Application key generated\n";
echo "✅ Caches cleared and optimized\n";
echo "✅ File permissions set\n";
echo "✅ Production optimizations applied\n";

if (isset($migrateReturn) && $migrateReturn === 0) {
    echo "✅ Database migrations completed\n";
}
if (isset($seedReturn) && $seedReturn === 0) {
    echo "✅ Database seeded with initial data\n";
}

echo "\n🔧 Step 6: Setting up subdirectory routing...\n";

// Setup .htaccess for subdirectory deployment
if (file_exists('.htaccess.subdirectory')) {
    echo "📁 Setting up main directory .htaccess for subdirectory routing...\n";
    echo "💡 IMPORTANT: Copy .htaccess.subdirectory to your main public_html/.htaccess\n";
    echo "   Command: cp target/.htaccess.subdirectory .htaccess\n";
    echo "   (Run this from your public_html directory)\n";
} else {
    echo "⚠️  Warning: .htaccess.subdirectory file not found!\n";
    echo "   This file is needed for proper API routing in subdirectories.\n";
}

echo "\n📋 Next Steps:\n";
echo "1. Verify your .env file has correct database credentials\n";
echo "2. Copy .htaccess.subdirectory to public_html/.htaccess (CRITICAL for API routing)\n";
echo "3. Ensure your APP_URL in .env includes /target subdirectory\n";
echo "4. Test the application by visiting https://yourdomain.com/target\n";
echo "5. Login with admin credentials\n";

echo "\n🔐 Default Admin Credentials:\n";
echo "Username: admin\n";
echo "Password: admin123\n";
echo "(Change these immediately after first login!)\n";

echo "\n🌐 Application URLs:\n";
echo "Main App: https://yourdomain.com/target\n";
echo "API Base: https://yourdomain.com/target/api/v1/\n";

echo "\n✅ Deployment completed successfully!\n";
echo "Your Target Management System is ready for Hostinger subdirectory deployment.\n";
?>