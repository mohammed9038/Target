<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $user = \App\Models\User::where('username', 'admin')->first();
    if (!$user) {
        $user = \App\Models\User::create([
            'username' => 'admin',
            'password' => bcrypt('admin123'),
            'role' => 'admin'
        ]);
        echo "✅ Admin user created successfully\n";
    } else {
        echo "✅ Admin user already exists\n";
    }
    echo "Username: admin\n";
    echo "Password: admin123\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
