<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    exit(0);
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
    exit;
}

try {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!$input) {
        echo json_encode(["success" => false, "error" => "Invalid JSON data"]);
        exit;
    }
    
    $year = intval($input["year"] ?? date("Y"));
    $month = intval($input["month"] ?? date("n"));
    $targets = $input["targets"] ?? [];
    
    if (empty($targets)) {
        echo json_encode(["success" => false, "error" => "No targets provided"]);
        exit;
    }
    
    $pdo = new PDO("mysql:host=localhost;dbname=u925629539_TSM", "u925629539_hesoka1", "HEsoka202090$");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $pdo->beginTransaction();
    $saved = 0;
    
    foreach ($targets as $target) {
        $salesman_id = intval($target["salesman_id"] ?? 0);
        $supplier_id = intval($target["supplier_id"] ?? 0);
        $category_id = intval($target["category_id"] ?? 0);
        $region_id = intval($target["region_id"] ?? 0);
        $channel_id = intval($target["channel_id"] ?? 0);
        $target_amount = floatval($target["target_amount"] ?? 0);
        
        if ($salesman_id && $supplier_id && $category_id && $region_id && $channel_id) {
            // Check if record exists
            $checkStmt = $pdo->prepare("SELECT id FROM sales_targets WHERE salesman_id = ? AND supplier_id = ? AND category_id = ? AND region_id = ? AND channel_id = ? AND year = ? AND month = ?");
            $checkStmt->execute([$salesman_id, $supplier_id, $category_id, $region_id, $channel_id, $year, $month]);
            
            if ($existing = $checkStmt->fetch()) {
                // Update existing
                $updateStmt = $pdo->prepare("UPDATE sales_targets SET target_amount = ?, updated_at = NOW() WHERE id = ?");
                $updateStmt->execute([$target_amount, $existing["id"]]);
            } else {
                // Insert new
                $insertStmt = $pdo->prepare("INSERT INTO sales_targets (year, month, region_id, channel_id, salesman_id, supplier_id, category_id, target_amount, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
                $insertStmt->execute([$year, $month, $region_id, $channel_id, $salesman_id, $supplier_id, $category_id, $target_amount]);
            }
            $saved++;
        }
    }
    
    $pdo->commit();
    echo json_encode(["success" => true, "saved" => $saved, "message" => "Targets saved successfully"]);
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>