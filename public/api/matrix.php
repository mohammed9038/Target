<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

try {
    $pdo = new PDO("mysql:host=localhost;dbname=u925629539_TSM", "u925629539_hesoka1", "HEsoka202090$");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get parameters
    $year = $_GET["year"] ?? date("Y");
    $month = $_GET["month"] ?? date("n");
    
    // Simple query to get salesmen with their regions and channels
    $query = "SELECT 
        s.id as salesman_id,
        s.salesman_code,
        s.employee_code,
        s.name as salesman_name,
        s.classification as salesman_classification,
        r.name as region,
        r.id as region_id,
        c.name as channel,
        c.id as channel_id,
        'Sample Supplier' as supplier,
        1 as supplier_id,
        'food' as supplier_classification,
        'Sample Category' as category,
        1 as category_id,
        0 as target_amount
    FROM salesmen s
    LEFT JOIN regions r ON s.region_id = r.id
    LEFT JOIN channels c ON s.channel_id = c.id
    ORDER BY s.name
    LIMIT 10";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $matrixData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        "success" => true,
        "data" => $matrixData,
        "filters" => [
            "year" => $year,
            "month" => $month
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>
