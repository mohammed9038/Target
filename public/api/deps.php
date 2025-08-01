<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$request = $_GET["type"] ?? "";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=u925629539_TSM", "u925629539_hesoka1", "HEsoka202090$");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    switch ($request) {
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
            $stmt = $pdo->query("SELECT id, name, salesman_code, employee_code, region_id, channel_id, classification FROM salesmen ORDER BY name");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        default:
            $data = ["error" => "Invalid type parameter"];
    }
    
    echo json_encode($data);
    
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
