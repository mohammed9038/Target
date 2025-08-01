<?php
// Complete fix for frontend API calls

$file = 'resources/views/targets/index.blade.php';
$content = file_get_contents($file);

// Find and replace the loadMasterData function
$pattern = '/async function loadMasterData\(\) \{.*?\}\s*catch \(error\) \{.*?\}/s';

$replacement = 'async function loadMasterData() {
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

$content = preg_replace($pattern, $replacement, $content);

// Also fix the matrix API call
$content = str_replace(
    'const response = await fetch(`/api/matrix.php?${queryParams}`, fetchOptions);',
    'const simpleOptions = { method: \'GET\', headers: { \'Accept\': \'application/json\' } };
        const response = await fetch(`/api/matrix.php?${queryParams}`, simpleOptions);',
    $content
);

file_put_contents($file, $content);
echo "Frontend completely fixed!\n";
?>
        populateFilter('filter_supplier', masterData.suppliers);

        // Load categories
        const categoriesResponse = await fetch('/api/deps.php?type=categories', simpleOptions);
        masterData.categories = await categoriesResponse.json();
        populateFilter('filter_category', masterData.categories);

        // Load salesmen
        const salesmenResponse = await fetch('/api/deps.php?type=salesmen', simpleOptions);
        masterData.salesmen = await salesmenResponse.json();
        populateFilter('filter_salesman', masterData.salesmen);
    } catch (error) {
        console.error('Error loading master data:', error);
        showAlert('Error loading filter data. Please try again.', 'error');
    }
}';

$content = preg_replace($pattern, $replacement, $content);

// Also fix the matrix API call
$content = str_replace(
    'const response = await fetch(`/api/matrix.php?${queryParams}`, fetchOptions);',
    'const simpleOptions = { method: \'GET\', headers: { \'Accept\': \'application/json\' } };
        const response = await fetch(`/api/matrix.php?${queryParams}`, simpleOptions);',
    $content
);

file_put_contents($file, $content);
echo "Frontend completely fixed!\n";
?>
