<?php
/**
 * API Testing Script for Hostinger Deployment
 * Tests all API endpoints to ensure they work correctly in subdirectory deployment
 */

// Get the base URL from environment or use default
$baseUrl = env('APP_URL', 'https://yourdomain.com/target');
$apiUrl = $baseUrl . '/api/v1';

echo "🔍 Testing API endpoints for Hostinger deployment\n";
echo "Base URL: $baseUrl\n";
echo "API URL: $apiUrl\n";
echo "========================================================\n\n";

// Test endpoints
$endpoints = [
    'GET /test-auth' => '/test-auth',
    'GET /deps/regions' => '/deps/regions',
    'GET /deps/channels' => '/deps/channels', 
    'GET /deps/suppliers' => '/deps/suppliers',
    'GET /deps/categories' => '/deps/categories',
    'GET /deps/salesmen' => '/deps/salesmen',
    'GET /targets/matrix' => '/targets/matrix?year=2025&month=8'
];

foreach ($endpoints as $description => $endpoint) {
    echo "Testing $description...\n";
    
    $url = $apiUrl . $endpoint;
    echo "URL: $url\n";
    
    // Use curl to test the endpoint
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'User-Agent: API-Test-Script/1.0'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "❌ CURL Error: $error\n";
    } else {
        echo "📊 HTTP Status: $httpCode\n";
        
        if ($httpCode == 200) {
            echo "✅ Success\n";
            $json = json_decode($response, true);
            if ($json) {
                echo "📋 Response keys: " . implode(', ', array_keys($json)) . "\n";
            }
        } elseif ($httpCode == 401) {
            echo "🔐 Authentication required (expected for protected endpoints)\n";
        } elseif ($httpCode == 404) {
            echo "❌ Not Found - Route may not exist or .htaccess issue\n";
        } else {
            echo "⚠️  Unexpected status code\n";
            echo "Response: " . substr($response, 0, 200) . "...\n";
        }
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}

echo "🔧 If you see 404 errors, check:\n";
echo "1. .htaccess.subdirectory is copied to public_html/.htaccess\n";
echo "2. APP_URL in .env includes /target subdirectory\n";
echo "3. Laravel routes are properly cached\n";
echo "4. Web server is configured correctly for subdirectories\n";

echo "\n✅ API testing completed.\n";
?>