<?php
/**
 * Creates a deployment package for Hostinger upload
 */

echo "=== Creating Deployment Package ===\n\n";

// Files and directories to exclude from deployment
$excludePatterns = [
    '.git',
    '.env',
    'node_modules',
    'storage/logs/*.log',
    'tests',
    '.phpunit.result.cache',
    'vendor/composer/platform_check.php.bak',
    '*.zip'
];

// Get all files in current directory
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator('.', RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

$filesToInclude = [];
foreach ($iterator as $file) {
    $relativePath = str_replace('\\', '/', $file->getPathname());
    $relativePath = ltrim($relativePath, './');
    
    // Check if file should be excluded
    $exclude = false;
    foreach ($excludePatterns as $pattern) {
        if (fnmatch($pattern, $relativePath) || 
            strpos($relativePath, str_replace('*', '', $pattern)) !== false) {
            $exclude = true;
            break;
        }
    }
    
    if (!$exclude && $file->isFile()) {
        $filesToInclude[] = $relativePath;
    }
}

echo "Found " . count($filesToInclude) . " files to include in deployment\n";

// Create deployment summary
$summary = "# HOSTINGER DEPLOYMENT PACKAGE\n\n";
$summary .= "Generated: " . date('Y-m-d H:i:s') . "\n";
$summary .= "Domain: https://mkalrawi.com/target-system\n";
$summary .= "Database: u925629539_TSM\n\n";
$summary .= "## Files Included: " . count($filesToInclude) . "\n\n";
$summary .= "## Key Configuration Files:\n";
$summary .= "- deploy/env.production (rename to .env on server)\n";
$summary .= "- deploy/.htaccess.hostinger (copy to public/.htaccess)\n";
$summary .= "- deploy/HOSTINGER_INSTRUCTIONS.md (deployment guide)\n\n";
$summary .= "## Classification Fix Included:\n";
$summary .= "✓ Target matrix now shows only compatible combinations\n";
$summary .= "✓ Food salesmen with Food suppliers only\n";
$summary .= "✓ Non-Food salesmen with Non-Food suppliers only\n\n";
$summary .= "## Next Steps:\n";
$summary .= "1. Upload all files to public_html/target-system/\n";
$summary .= "2. Follow deploy/HOSTINGER_INSTRUCTIONS.md\n";
$summary .= "3. Test the classification fix\n";

file_put_contents('deploy/DEPLOYMENT_SUMMARY.md', $summary);
echo "✓ Created deployment summary: deploy/DEPLOYMENT_SUMMARY.md\n";

// Create file list for reference
$fileList = "# DEPLOYMENT FILE LIST\n\n";
$fileList .= "Total files: " . count($filesToInclude) . "\n\n";
foreach ($filesToInclude as $file) {
    $fileList .= "- $file\n";
}

file_put_contents('deploy/FILE_LIST.md', $fileList);
echo "✓ Created file list: deploy/FILE_LIST.md\n";

echo "\n=== Deployment Package Ready! ===\n";
echo "Upload these files to Hostinger:\n";
echo "- All application files (see deploy/FILE_LIST.md)\n";
echo "- Use deploy/env.production as .env\n";
echo "- Use deploy/.htaccess.hostinger as public/.htaccess\n";
echo "\nFollow deploy/HOSTINGER_INSTRUCTIONS.md for complete setup\n";
?>