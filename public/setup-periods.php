<?php
// Script to create periods table and add some open periods
try {
    $pdo = new PDO("mysql:host=localhost;dbname=u925629539_TSM", "u925629539_hesoka1", "HEsoka202090$");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if periods table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'periods'");
    if ($stmt->rowCount() == 0) {
        // Create periods table
        $createTableSQL = "
        CREATE TABLE periods (
            id INT AUTO_INCREMENT PRIMARY KEY,
            year INT NOT NULL,
            month INT NOT NULL,
            name VARCHAR(50) NOT NULL,
            is_open BOOLEAN DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_period (year, month)
        )";
        
        $pdo->exec($createTableSQL);
        echo "✅ Created periods table\n";
        
        // Add current and next few months as open periods
        $currentYear = date("Y");
        $currentMonth = date("n");
        
        $monthNames = [
            1 => "January", 2 => "February", 3 => "March", 4 => "April",
            5 => "May", 6 => "June", 7 => "July", 8 => "August",
            9 => "September", 10 => "October", 11 => "November", 12 => "December"
        ];
        
        // Add current month and next 11 months as open
        for ($i = 0; $i < 12; $i++) {
            $targetMonth = ($currentMonth + $i - 1) % 12 + 1;
            $targetYear = $currentYear + intval(($currentMonth + $i - 1) / 12);
            
            $monthName = $monthNames[$targetMonth] . " " . $targetYear;
            
            $stmt = $pdo->prepare("INSERT IGNORE INTO periods (year, month, name, is_open) VALUES (?, ?, ?, 1)");
            $stmt->execute([$targetYear, $targetMonth, $monthName]);
        }
        
        echo "✅ Added open periods for current and next 11 months\n";
    } else {
        echo "✅ Periods table already exists\n";
        
        // Ensure we have some open periods
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM periods WHERE is_open = 1");
        $result = $stmt->fetch();
        
        if ($result["count"] == 0) {
            echo "⚠️ No open periods found, adding current month...\n";
            
            $currentYear = date("Y");
            $currentMonth = date("n");
            $monthNames = [
                1 => "January", 2 => "February", 3 => "March", 4 => "April",
                5 => "May", 6 => "June", 7 => "July", 8 => "August",
                9 => "September", 10 => "October", 11 => "November", 12 => "December"
            ];
            
            $monthName = $monthNames[$currentMonth] . " " . $currentYear;
            
            $stmt = $pdo->prepare("INSERT IGNORE INTO periods (year, month, name, is_open) VALUES (?, ?, ?, 1)");
            $stmt->execute([$currentYear, $currentMonth, $monthName]);
            
            echo "✅ Added current month as open period\n";
        }
    }
    
    // Show current open periods
    $stmt = $pdo->query("SELECT year, month, name FROM periods WHERE is_open = 1 ORDER BY year, month");
    $openPeriods = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "📅 Current open periods:\n";
    foreach ($openPeriods as $period) {
        echo "   - {$period['name']} ({$period['year']}-{$period['month']})\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}
?>