<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    exit(0);
}

try {
    $action = $_GET["action"] ?? "";
    $pdo = new PDO("mysql:host=localhost;dbname=u925629539_TSM", "u925629539_hesoka1", "HEsoka202090$");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    switch ($action) {
        case "deps":
            $type = $_GET["type"] ?? "";
            $data = [];
            
            switch ($type) {
                case "regions":
                    $stmt = $pdo->query("SELECT id, name FROM regions ORDER BY name");
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    break;
                case "channels":
                    $stmt = $pdo->query("SELECT id, name FROM channels ORDER BY name");
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    break;
                case "suppliers":
                    $stmt = $pdo->query("SELECT id, name, classification FROM suppliers ORDER BY name");
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    break;
                case "categories":
                    $stmt = $pdo->query("SELECT id, name, supplier_id FROM categories ORDER BY name");
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    break;
                case "salesmen":
                    $stmt = $pdo->query("SELECT id, name, salesman_code, classification FROM salesmen ORDER BY name");
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    break;
                default:
                    echo json_encode(["success" => false, "error" => "Invalid type"]);
                    exit;
            }
            
            echo json_encode(["success" => true, "data" => $data]);
            exit;
            
        case "matrix":
            $year = intval($_GET["year"] ?? date("Y"));
            $month = intval($_GET["month"] ?? date("n"));
            $classification = $_GET["classification"] ?? "";
            $region_id = $_GET["region_id"] ?? "";
            $channel_id = $_GET["channel_id"] ?? "";
            $supplier_id = $_GET["supplier_id"] ?? "";
            $category_id = $_GET["category_id"] ?? "";
            $salesman_id = $_GET["salesman_id"] ?? "";
            
            $query = "SELECT 
                s.id as salesman_id, 
                s.salesman_code, 
                s.name as salesman_name,
                s.classification as salesman_classification,
                r.id as region_id, 
                r.name as region_name,
                c.id as channel_id, 
                c.name as channel_name,
                sup.id as supplier_id, 
                sup.name as supplier_name,
                sup.classification as supplier_classification,
                cat.id as category_id, 
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
            $matrixData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(["success" => true, "data" => $matrixData]);
            exit;
            
        default:
            echo json_encode(["success" => false, "error" => "Invalid action"]);
            exit;
    }
    
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>