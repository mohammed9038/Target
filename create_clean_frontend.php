<?php
// Create a completely clean working frontend file

$file = 'resources/views/targets/index.blade.php';

// Read the original structure but create clean JavaScript
$content = file_get_contents($file);

// Find where the script starts and ends
$scriptStart = strpos($content, '<script>');
$scriptEnd = strpos($content, '</script>', $scriptStart);

if ($scriptStart === false || $scriptEnd === false) {
    // If we can't find script tags, add them before closing body
    $bodyEnd = strpos($content, '</body>');
    $beforeScript = substr($content, 0, $bodyEnd);
    $afterScript = substr($content, $bodyEnd);
} else {
    $beforeScript = substr($content, 0, $scriptStart);
    $afterScript = substr($content, $scriptEnd);
}

$cleanScript = '<script>
console.log("Loading Target Management JavaScript...");

// Global variables
let masterData = {
    regions: [],
    channels: [],
    suppliers: [],
    categories: [],
    salesmen: []
};

// Simple fetch configuration
const apiOptions = {
    method: "GET",
    headers: {
        "Accept": "application/json",
        "Content-Type": "application/json"
    }
};

// Load master data function
async function loadMasterData() {
    console.log("Starting loadMasterData...");
    
    try {
        // Load regions
        console.log("Fetching regions...");
        const regionsResponse = await fetch("/api/deps.php?type=regions", apiOptions);
        if (regionsResponse.ok) {
            masterData.regions = await regionsResponse.json();
            console.log("Regions loaded:", masterData.regions);
            populateSelect("filter_region", masterData.regions);
        }

        // Load channels
        console.log("Fetching channels...");
        const channelsResponse = await fetch("/api/deps.php?type=channels", apiOptions);
        if (channelsResponse.ok) {
            masterData.channels = await channelsResponse.json();
            console.log("Channels loaded:", masterData.channels);
            populateSelect("filter_channel", masterData.channels);
        }

        // Load suppliers
        console.log("Fetching suppliers...");
        const suppliersResponse = await fetch("/api/deps.php?type=suppliers", apiOptions);
        if (suppliersResponse.ok) {
            masterData.suppliers = await suppliersResponse.json();
            console.log("Suppliers loaded:", masterData.suppliers);
            populateSelect("filter_supplier", masterData.suppliers);
        }

        // Load categories
        console.log("Fetching categories...");
        const categoriesResponse = await fetch("/api/deps.php?type=categories", apiOptions);
        if (categoriesResponse.ok) {
            masterData.categories = await categoriesResponse.json();
            console.log("Categories loaded:", masterData.categories);
            populateSelect("filter_category", masterData.categories);
        }

        // Load salesmen
        console.log("Fetching salesmen...");
        const salesmenResponse = await fetch("/api/deps.php?type=salesmen", apiOptions);
        if (salesmenResponse.ok) {
            masterData.salesmen = await salesmenResponse.json();
            console.log("Salesmen loaded:", masterData.salesmen);
            populateSelect("filter_salesman", masterData.salesmen);
        }

        console.log("All master data loaded successfully!");
        
    } catch (error) {
        console.error("Error in loadMasterData:", error);
        alert("Error loading filter data: " + error.message);
    }
}

// Populate select dropdown
function populateSelect(selectId, data) {
    const select = document.getElementById(selectId);
    if (!select) {
        console.log("Select element not found:", selectId);
        return;
    }
    
    // Keep the first option (usually "All ...")
    while (select.children.length > 1) {
        select.removeChild(select.lastChild);
    }
    
    // Add data options
    if (Array.isArray(data)) {
        data.forEach(item => {
            const option = document.createElement("option");
            option.value = item.id;
            option.textContent = item.name;
            select.appendChild(option);
        });
        console.log(`Populated ${selectId} with ${data.length} items`);
    }
}

// Load target matrix function
async function loadTargetMatrix() {
    console.log("Loading target matrix...");
    
    try {
        // Get form values
        const year = document.getElementById("target_year")?.value;
        const month = document.getElementById("target_month")?.value;
        
        if (!year || !month) {
            alert("Please select both year and month.");
            return;
        }
        
        // Build query parameters
        const params = new URLSearchParams({
            year: year,
            month: month,
            classification: document.getElementById("filter_classification")?.value || "",
            region_id: document.getElementById("filter_region")?.value || "",
            channel_id: document.getElementById("filter_channel")?.value || "",
            supplier_id: document.getElementById("filter_supplier")?.value || "",
            category_id: document.getElementById("filter_category")?.value || "",
            salesman_id: document.getElementById("filter_salesman")?.value || ""
        });
        
        console.log("Matrix params:", params.toString());
        
        // Make API call
        const response = await fetch(`/api/matrix.php?${params}`, apiOptions);
        console.log("Matrix response status:", response.status);
        
        if (response.ok) {
            const result = await response.json();
            console.log("Matrix result:", result);
            
            if (result.success && result.data) {
                displayMatrixData(result.data);
                hideInstructions();
            } else {
                alert("Failed to load matrix: " + (result.error || "Unknown error"));
            }
        } else {
            const errorText = await response.text();
            console.error("Matrix API error:", errorText);
            alert("Matrix API error: " + response.status);
        }
        
    } catch (error) {
        console.error("Error in loadTargetMatrix:", error);
        alert("Error loading target matrix: " + error.message);
    }
}

// Display matrix data in table
function displayMatrixData(data) {
    console.log("Displaying matrix data:", data.length, "rows");
    
    const tbody = document.getElementById("target-matrix-tbody");
    if (!tbody) {
        console.error("Matrix tbody not found");
        return;
    }
    
    if (!data || data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-4">
                    <p>No data found for selected criteria.</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = "";
    
    data.forEach((row, index) => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td>${index + 1}</td>
            <td>${row.salesman_code || ""}</td>
            <td>${row.salesman_name || ""}</td>
            <td><span class="badge bg-secondary">${row.salesman_classification || ""}</span></td>
            <td>${row.region || ""}</td>
            <td>${row.channel || ""}</td>
            <td>${row.supplier || ""}</td>
            <td>${row.category || ""}</td>
            <td>
                <input type="number" class="form-control form-control-sm" 
                       value="${row.target_amount || 0}" min="0" step="0.01">
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// Hide instructions and show matrix
function hideInstructions() {
    const instructions = document.getElementById("instructions-card");
    const matrix = document.getElementById("target-matrix-card");
    
    if (instructions) instructions.style.display = "none";
    if (matrix) matrix.style.display = "block";
}

// Initialize when page loads
document.addEventListener("DOMContentLoaded", function() {
    console.log("DOM loaded, initializing...");
    
    // Load master data immediately
    loadMasterData();
    
    // Make loadTargetMatrix available globally
    window.loadTargetMatrix = loadTargetMatrix;
    
    console.log("Target Management System initialized!");
});

console.log("Target Management JavaScript loaded successfully!");
</script>';

// Combine everything
$newContent = $beforeScript . $cleanScript . $afterScript;

file_put_contents($file, $newContent);
echo "Clean frontend created successfully!\n";
?>
