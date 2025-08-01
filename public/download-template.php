<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=u925629539_TSM", "u925629539_hesoka1", "HEsoka202090$");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get template data
    $query = "SELECT 
        s.salesman_code, 
        s.name as salesman_name, 
        s.classification as salesman_classification,
        r.name as region_name, 
        c.name as channel_name, 
        sup.name as supplier_name, 
        cat.name as category_name
        FROM salesmen s
        LEFT JOIN regions r ON s.region_id = r.id
        LEFT JOIN channels c ON s.channel_id = c.id
        CROSS JOIN suppliers sup
        CROSS JOIN categories cat
        WHERE cat.supplier_id = sup.id
        AND ((s.classification = \"food\" AND sup.classification = \"food\") 
            OR (s.classification = \"non_food\" AND sup.classification = \"non_food\") 
            OR (s.classification = \"both\") 
            OR (sup.classification = \"both\"))
        ORDER BY s.name, sup.name, cat.name";
    
    $stmt = $pdo->query($query);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $filename = "targets_template_" . date("Y_m_d") . ".csv";
    
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
    
    // CSV Data with zero amounts
    foreach ($data as $row) {
        fputcsv($output, [
            $row["salesman_code"], 
            $row["salesman_name"], 
            $row["salesman_classification"], 
            $row["region_name"], 
            $row["channel_name"], 
            $row["supplier_name"], 
            $row["category_name"], 
            "0" // Default target amount
        ]);
    }
    
    fclose($output);
    
} catch (Exception $e) {
    header("Content-Type: application/json");
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>