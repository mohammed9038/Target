<?php
// Fix API endpoints in the targets index file
$file = 'resources/views/targets/index.blade.php';
$content = file_get_contents($file);

// Fix bulk-save endpoint
$content = str_replace(
    '/api/targets/bulk-save',
    '/api/v1/targets/bulk-save', 
    $content
);

// Fix any other missing /v1/ prefixes
$content = str_replace(
    "'/api/targets'",
    "'/api/v1/targets'",
    $content
);

$content = str_replace(
    '"/api/targets"',
    '"/api/v1/targets"',
    $content
);

$content = str_replace(
    '/api/periods/check',
    '/api/v1/periods/check',
    $content
);

file_put_contents($file, $content);
echo "✅ Fixed API endpoints in frontend\n";
