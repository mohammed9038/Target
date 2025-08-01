<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
    exit;
}

try {
    if (!isset($_FILES["csv_file"]) || $_FILES["csv_file"]["error"] !== UPLOAD_ERR_OK) {
        echo json_encode(["success" => false, "error" => "No file uploaded or upload error"]);
        exit;
    }
    
    $year = intval($_POST["year"] ?? date("Y"));
    $month = intval($_POST["month"] ?? date("n"));
    
    $pdo = new PDO("mysql:host=localhost;dbname=u925629539_TSM", "u925629539_hesoka1", "HEsoka202090$");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $csvFile = $_FILES["csv_file"]["tmp_name"];
    $handle = fopen($csvFile, "r");
    
    if (!$handle) {
        echo json_encode(["success" => false, "error" => "Could not read CSV file"]);
        exit;
    }
    
    // Skip header row
    fgetcsv($handle);
    
    $pdo->beginTransaction();
    $processed = 0;
    $errors = [];
    
    while (($data = fgetcsv($handle)) !== FALSE) {
        if (count($data) < 8) continue;
        
        $salesman_code = trim($data[0]);
        $supplier_name = trim($data[5]);
        $category_name = trim($data[6]);
        $target_amount = floatval($data[7]);
        
        // Get salesman ID
        $salesmanStmt = $pdo->prepare("SELECT id, region_id, channel_id FROM salesmen WHERE salesman_code = ?");
        $salesmanStmt->execute([$salesman_code]);
        $salesman = $salesmanStmt->fetch();
        
        if (!$salesman) {
            $errors[] = "Salesman code not found: " . $salesman_code;
            continue;
        }
        
        // Get supplier ID
        $supplierStmt = $pdo->prepare("SELECT id FROM suppliers WHERE name = ?");
        $supplierStmt->execute([$supplier_name]);
        $supplier = $supplierStmt->fetch();
        
        if (!$supplier) {
            $errors[] = "Supplier not found: " . $supplier_name;
            continue;
        }
        
        // Get category ID
        $categoryStmt = $pdo->prepare("SELECT id FROM categories WHERE name = ? AND supplier_id = ?");
        $categoryStmt->execute([$category_name, $supplier["id"]]);
        $category = $categoryStmt->fetch();
        
        if (!$category) {
            $errors[] = "Category not found: " . $category_name . " for supplier " . $supplier_name;
            continue;
        }
        
        // Check if target exists
        $checkStmt = $pdo->prepare("SELECT id FROM sales_targets WHERE salesman_id = ? AND supplier_id = ? AND category_id = ? AND region_id = ? AND channel_id = ? AND year = ? AND month = ?");
        $checkStmt->execute([$salesman["id"], $supplier["id"], $category["id"], $salesman["region_id"], $salesman["channel_id"], $year, $month]);
        
        if ($existing = $checkStmt->fetch()) {
            // Update existing
            $updateStmt = $pdo->prepare("UPDATE sales_targets SET target_amount = ?, updated_at = NOW() WHERE id = ?");
            $updateStmt->execute([$target_amount, $existing["id"]]);
        } else {
            // Insert new
            $insertStmt = $pdo->prepare("INSERT INTO sales_targets (year, month, region_id, channel_id, salesman_id, supplier_id, category_id, target_amount, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $insertStmt->execute([$year, $month, $salesman["region_id"], $salesman["channel_id"], $salesman["id"], $supplier["id"], $category["id"], $target_amount]);
        }
        
        $processed++;
    }
    
    fclose($handle);
    $pdo->commit();
    
    echo json_encode([
        "success" => true, 
        "processed" => $processed, 
        "errors" => $errors,
        "message" => "Upload completed. Processed: $processed records"
    ]);
    
} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>