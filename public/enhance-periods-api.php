<?php
// Simple periods endpoint - fallback
if (!file_exists("api-handler.php")) {
    header("Content-Type: application/json");
    $currentYear = date("Y");
    echo json_encode([
        "success" => true,
        "data" => [
            "grouped" => [
                $currentYear => [1,2,3,4,5,6,7,8,9,10,11,12],
                ($currentYear + 1) => [1,2,3,4,5,6,7,8,9,10,11,12]
            ]
        ]
    ]);
    exit;
}

// If api-handler.php exists, enhance it minimally
$apiContent = file_get_contents("api-handler.php");

// Only add periods case if it doesn't exist
if (strpos($apiContent, 'case "periods"') === false) {
    $periodsCase = '
                case "periods":
                    // Simple fallback periods
                    $currentYear = date("Y");
                    $data = [
                        "grouped" => [
                            $currentYear => [1,2,3,4,5,6,7,8,9,10,11,12],
                            ($currentYear + 1) => [1,2,3,4,5,6,7,8,9,10,11,12]
                        ]
                    ];
                    break;';
    
    $apiContent = str_replace(
        'default:
            echo json_encode(["success" => false, "error" => "Invalid action: " . $action]);',
        $periodsCase . '
        default:
            echo json_encode(["success" => false, "error" => "Invalid action: " . $action]);',
        $apiContent
    );
    
    file_put_contents("api-handler.php", $apiContent);
    echo "✅ Enhanced existing api-handler.php with periods\n";
} else {
    echo "✅ Periods already exist in api-handler.php\n";
}
?>