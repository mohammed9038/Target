<?php
// Fix duplicate route names in api.php
$apiRoutes = file_get_contents('routes/api.php');

// Replace resource routes to use unique names for API
$apiRoutes = str_replace(
    "Route::apiResource('regions', RegionController::class);",
    "Route::apiResource('regions', RegionController::class)->names('api.regions');",
    $apiRoutes
);

$apiRoutes = str_replace(
    "Route::apiResource('channels', ChannelController::class);",
    "Route::apiResource('channels', ChannelController::class)->names('api.channels');", 
    $apiRoutes
);

$apiRoutes = str_replace(
    "Route::apiResource('suppliers', SupplierController::class);",
    "Route::apiResource('suppliers', SupplierController::class)->names('api.suppliers');",
    $apiRoutes
);

$apiRoutes = str_replace(
    "Route::apiResource('categories', CategoryController::class);",
    "Route::apiResource('categories', CategoryController::class)->names('api.categories');",
    $apiRoutes
);

$apiRoutes = str_replace(
    "Route::apiResource('salesmen', SalesmanController::class);",
    "Route::apiResource('salesmen', SalesmanController::class)->names('api.salesmen');",
    $apiRoutes
);

$apiRoutes = str_replace(
    "Route::apiResource('targets', TargetController::class);",
    "Route::apiResource('targets', TargetController::class)->names('api.targets');",
    $apiRoutes
);

file_put_contents('routes/api.php', $apiRoutes);
echo "✅ Fixed route name conflicts\n";
