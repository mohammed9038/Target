<?php
$year = intval($_GET["year"] ?? date("Y"));
$month = intval($_GET["month"] ?? date("n"));
$classification = $_GET["classification"] ?? "";
$region_id = $_GET["region_id"] ?? "";
$channel_id = $_GET["channel_id"] ?? "";
$supplier_id = $_GET["supplier_id"] ?? "";
$category_id = $_GET["category_id"] ?? "";
$salesman_id = $_GET["salesman_id"] ?? "";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=u925629539_TSM", "u925629539_hesoka1", "HEsoka202090$");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT 
        s.salesman_code, 
        s.name as salesman_name, 
        s.classification as salesman_classification,
        r.name as region_name, 
        c.name as channel_name, 
        sup.name as supplier_name, 
        cat.name as category_name,
        COALESCE(t.target_amount, 0) as target_amount
        FROM salesmen s
        LEFT JOIN regions r ON s.region_id = r.id
        LEFT JOIN channels c ON s.channel_id = c.id
        CROSS JOIN suppliers sup
        CROSS JOIN categories cat
        LEFT JOIN sales_targets t ON t.salesman_id = s.id 
            AND t.supplier_id = sup.id 
            AND t.category_id = cat.id
            AND t.region_id = s.region_id 
            AND t.channel_id = s.channel_id 
            AND t.year = ? 
            AND t.month = ?
        WHERE cat.supplier_id = sup.id";
    
    $params = [$year, $month];
    
    // Add classification compatibility filter
    $query .= " AND ((s.classification = \"food\" AND sup.classification = \"food\") 
                OR (s.classification = \"non_food\" AND sup.classification = \"non_food\") 
                OR (s.classification = \"both\") 
                OR (sup.classification = \"both\"))";
    
    // Add filters
    if ($classification) {
        $query .= " AND (s.classification = ? OR s.classification = \"both\")";
        $params[] = $classification;
    }
    if ($region_id) {
        $query .= " AND s.region_id = ?";
        $params[] = $region_id;
    }
    if ($channel_id) {
        $query .= " AND s.channel_id = ?";
        $params[] = $channel_id;
    }
    if ($supplier_id) {
        $query .= " AND sup.id = ?";
        $params[] = $supplier_id;
    }
    if ($category_id) {
        $query .= " AND cat.id = ?";
        $params[] = $category_id;
    }
    if ($salesman_id) {
        $query .= " AND s.id = ?";
        $params[] = $salesman_id;
    }
    
    $query .= " ORDER BY s.name, sup.name, cat.name";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $filename = "targets_{$year}_{$month}";
    if ($classification) $filename .= "_{$classification}";
    $filename .= ".csv";

    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=" . $filename);
    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

    $output = fopen("php://output", "w");
    
    // CSV Headers
    fputcsv($output, [
        "Salesman Code", 
        "Salesman Name", 
        "Classification", 
        "Region", 
        "Channel", 
        "Supplier", 
        "Category", 
        "Target Amount"
    ]);
    
    // CSV Data
    foreach ($data as $row) {
        fputcsv($output, [
            $row["salesman_code"], 
            $row["salesman_name"], 
            $row["salesman_classification"], 
            $row["region_name"], 
            $row["channel_name"], 
            $row["supplier_name"], 
            $row["category_name"], 
            $row["target_amount"]
        ]);
    }
    
    fclose($output);
    
} catch (Exception $e) {
    header("Content-Type: application/json");
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>