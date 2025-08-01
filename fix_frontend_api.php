<?php
// Script to fix frontend API calls to remove authentication

$file = 'resources/views/targets/index.blade.php';
$content = file_get_contents($file);

// Replace loadMasterData function
$oldFunction = '/\/\/ Load master data for filters\s*async function loadMasterData\(\) \{.*?\}\s*catch \(error\) \{.*?\}/s';

$newFunction = '// Load master data for filters
async function loadMasterData() {
    try {
        // Simple fetch options without authentication for direct API endpoints
        const simpleOptions = {
            method: \'GET\',
            headers: {
                \'Accept\': \'application/json\'
            }
        };

        // Load regions
        const regionsResponse = await fetch(\'/api/deps.php?type=regions\', simpleOptions);
        masterData.regions = await regionsResponse.json();
        populateFilter(\'filter_region\', masterData.regions);

        // Load channels
        const channelsResponse = await fetch(\'/api/deps.php?type=channels\', simpleOptions);
        masterData.channels = await channelsResponse.json();
        populateFilter(\'filter_channel\', masterData.channels);

        // Load suppliers
        const suppliersResponse = await fetch(\'/api/deps.php?type=suppliers\', simpleOptions);
        masterData.suppliers = await suppliersResponse.json();
        populateFilter(\'filter_supplier\', masterData.suppliers);

        // Load categories
        const categoriesResponse = await fetch(\'/api/deps.php?type=categories\', simpleOptions);
        masterData.categories = await categoriesResponse.json();
        populateFilter(\'filter_category\', masterData.categories);

        // Load salesmen
        const salesmenResponse = await fetch(\'/api/deps.php?type=salesmen\', simpleOptions);
        masterData.salesmen = await salesmenResponse.json();
        populateFilter(\'filter_salesman\', masterData.salesmen);
    } catch (error) {
        console.error(\'Error loading master data:\', error);
        showAlert(\'Error loading filter data. Please try again.\', \'error\');
    }
}';

// Replace the matrix API call
$content = str_replace(
    'const response = await fetch(`/api/matrix.php?${queryParams}`, fetchOptions);',
    'const simpleOptions = { method: \'GET\', headers: { \'Accept\': \'application/json\' } };
        const response = await fetch(`/api/matrix.php?${queryParams}`, simpleOptions);',
    $content
);

// Simple replacements for individual API calls
$replacements = [
    "await fetch('/api/deps.php?type=regions', fetchOptions)" => "await fetch('/api/deps.php?type=regions', simpleOptions)",
    "await fetch('/api/deps.php?type=channels', fetchOptions)" => "await fetch('/api/deps.php?type=channels', simpleOptions)",
    "await fetch('/api/deps.php?type=suppliers', fetchOptions)" => "await fetch('/api/deps.php?type=suppliers', simpleOptions)",
    "await fetch('/api/deps.php?type=categories', fetchOptions)" => "await fetch('/api/deps.php?type=categories', simpleOptions)",
    "await fetch('/api/deps.php?type=salesmen', fetchOptions)" => "await fetch('/api/deps.php?type=salesmen', simpleOptions)"
];

foreach ($replacements as $old => $new) {
    $content = str_replace($old, $new, $content);
}

// Add simpleOptions definition at the beginning of loadMasterData
$content = str_replace(
    '// Load regions',
    '// Simple fetch options without authentication for direct API endpoints
        const simpleOptions = {
            method: \'GET\',
            headers: {
                \'Accept\': \'application/json\'
            }
        };

        // Load regions',
    $content
);

file_put_contents($file, $content);
echo "Frontend API calls fixed successfully!\n";
?>
