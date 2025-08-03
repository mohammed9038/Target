<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🗑️  Clearing all sample data...\n\n";

try {
    // Clear sales targets first (has foreign keys)
    DB::table('sales_targets')->delete();
    echo "✅ Cleared sales targets\n";
    
    // Clear pivot tables
    DB::table('user_regions')->delete();
    DB::table('user_channels')->delete();
    DB::table('salesman_classifications')->delete();
    DB::table('user_classifications')->delete();
    echo "✅ Cleared user permissions and classifications\n";
    
    // Clear master data
    DB::table('categories')->delete();
    echo "✅ Cleared categories\n";
    
    DB::table('suppliers')->delete();
    echo "✅ Cleared suppliers\n";
    
    DB::table('salesmen')->delete();
    echo "✅ Cleared salesmen\n";
    
    DB::table('channels')->delete();
    echo "✅ Cleared channels\n";
    
    DB::table('regions')->delete();
    echo "✅ Cleared regions\n";
    
    // Clear active periods (you can set new ones)
    DB::table('active_months_years')->delete();
    echo "✅ Cleared active periods\n";
    
    // Keep admin user but clear other sample users
    DB::table('users')->where('username', '!=', 'admin')->delete();
    echo "✅ Cleared sample users (kept admin)\n";
    
    echo "\n🎉 Sample data cleared successfully!\n";
    echo "📋 What's preserved:\n";
    echo "   - Admin user (username: admin, password: admin123)\n";
    echo "   - Database structure\n";
    echo "   - Application settings\n\n";
    echo "📝 You can now add your real data:\n";
    echo "   1. Regions\n";
    echo "   2. Channels\n";
    echo "   3. Suppliers\n";
    echo "   4. Categories\n";
    echo "   5. Salesmen\n";
    echo "   6. Active Periods\n";
    echo "   7. Manager Users\n";
    echo "   8. Sales Targets\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
