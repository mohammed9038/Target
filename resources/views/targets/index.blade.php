@extends('layouts.app')

@section('title', __('Sales Targets'))

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="h2 mb-1">{{ __('Sales Targets') }}</h1>
        <p class="text-muted mb-0">{{ __('Set sales targets for your team members') }}</p>
    </div>
    <div class="d-flex gap-2" style="margin-top: 0.5rem;">
        <button type="button" class="btn btn-success" onclick="saveAllTargets()" id="saveAllBtn">
            <i class="bi bi-check-circle me-2"></i>{{ __('Save All Targets') }}
        </button>
        <button type="button" class="btn btn-outline-secondary" onclick="exportTargets()">
            <i class="bi bi-file-earmark-spreadsheet me-2"></i>{{ __('Export CSV') }}
        </button>
        <button type="button" class="btn btn-outline-primary" onclick="showUploadModal()">
            <i class="bi bi-upload me-2"></i>{{ __('Upload Targets') }}
        </button>
        <button type="button" class="btn btn-outline-info" onclick="downloadTemplate()">
            <i class="bi bi-download me-2"></i>{{ __('Download Template') }}
        </button>
    </div>
</div>

<!-- Period Selection -->
<div class="row mb-3">
    <div class="col-md-3">
        <label for="target_year" class="form-label">{{ __('Year') }}</label>
        <select class="form-select" id="target_year" name="year">
            <option value="">{{ __('Select Year') }}</option>
            @for($y = date('Y'); $y <= date('Y') + 2; $y++)
                <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </div>
    <div class="col-md-3">
        <label for="target_month" class="form-label">{{ __('Month') }}</label>
        <select class="form-select" id="target_month" name="month">
            <option value="">{{ __('Select Month') }}</option>
            @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                </option>
            @endfor
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">&nbsp;</label>
        <button type="button" class="btn btn-primary w-100" onclick="loadTargetMatrix()" id="loadMatrixBtn">
            <i class="bi bi-table me-2"></i>{{ __('Load Matrix') }}
        </button>
    </div>
</div>

<!-- Filters Section -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">{{ __('Filters') }}</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-2">
                <label for="filter_classification" class="form-label">{{ __('Classification') }}</label>
                <select class="form-select" id="filter_classification">
                    <option value="">{{ __('All Classifications') }}</option>
                    <option value="food">{{ __('Food') }}</option>
                    <option value="non_food">{{ __('Non Food') }}</option>
                    <option value="both">{{ __('Both') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="filter_region" class="form-label">{{ __('Region') }}</label>
                <select class="form-select" id="filter_region">
                    <option value="">{{ __('All Regions') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="filter_channel" class="form-label">{{ __('Channel') }}</label>
                <select class="form-select" id="filter_channel">
                    <option value="">{{ __('All Channels') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="filter_supplier" class="form-label">{{ __('Supplier') }}</label>
                <select class="form-select" id="filter_supplier">
                    <option value="">{{ __('All Suppliers') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="filter_category" class="form-label">{{ __('Category') }}</label>
                <select class="form-select" id="filter_category">
                    <option value="">{{ __('All Categories') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="filter_salesman" class="form-label">{{ __('Salesman') }}</label>
                <select class="form-select" id="filter_salesman">
                    <option value="">{{ __('All Salesmen') }}</option>
                </select>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <button type="button" class="btn btn-primary me-2" onclick="applyFilters()">
                    <i class="bi bi-funnel me-1"></i>{{ __('Apply Filters') }}
                </button>
                <button type="button" class="btn btn-outline-secondary me-2" onclick="resetFilters()">
                    <i class="bi bi-x-circle me-1"></i>{{ __('Clear Filters') }}
                </button>
                <button type="button" class="btn btn-outline-success me-2" onclick="fillAllAmounts()">
                    <i class="bi bi-plus-circle me-1"></i>{{ __('Fill All') }}
                </button>
                <button type="button" class="btn btn-outline-danger" onclick="clearAllAmounts()">
                    <i class="bi bi-dash-circle me-1"></i>{{ __('Clear All') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Target Matrix -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">{{ __('Target Matrix') }}</h5>
        <button type="button" class="btn btn-success btn-sm" onclick="saveAllTargets()" id="saveMatrixBtn">
            <i class="bi bi-floppy me-1"></i>{{ __('Save All') }}
        </button>
    </div>
    <div class="card-body">
        <div id="matrix-loading" class="text-center py-4" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">{{ __('Loading...') }}</span>
            </div>
            <p class="mt-2 mb-0">{{ __('Loading target matrix...') }}</p>
        </div>
        
        <div id="matrix-container" style="display: none;">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="target-matrix">
                    <thead class="table-dark">
                        <tr>
                            <th>{{ __('Salesman') }}</th>
                            <th>{{ __('Region') }}</th>
                            <th>{{ __('Channel') }}</th>
                            <th>{{ __('Supplier') }}</th>
                            <th>{{ __('Category') }}</th>
                            <th>{{ __('Target Amount') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        
        <div id="matrix-empty" class="text-center py-4">
            <i class="bi bi-table text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-2 mb-0">{{ __('No data available. Please select year and month, then click Load Matrix.') }}</p>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Upload Targets') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="upload_file" class="form-label">{{ __('Select CSV File') }}</label>
                        <input type="file" class="form-control" id="upload_file" name="csv_file" accept=".csv" required>
                        <div class="form-text">{{ __('Please upload a CSV file with the correct format.') }}</div>
                    </div>
                    <input type="hidden" id="upload_year" name="year">
                    <input type="hidden" id="upload_month" name="month">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="uploadTargets()">
                    <i class="bi bi-upload me-1"></i>{{ __('Upload') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
console.log("🎯 TARGET PAGE SCRIPT LOADED");

// ==================== GLOBAL VARIABLES ====================
let matrixData = [];
let masterData = {
    regions: [],
    channels: [],
    suppliers: [],
    categories: [],
    salesmen: []
};

// ==================== API CONFIGURATION ====================
const API_BASE = "/target-system/public";
const apiOptions = {
    method: "GET",
    headers: {
        "Accept": "application/json",
        "Content-Type": "application/json"
    }
};

// ==================== UTILITY FUNCTIONS ====================
function showAlert(message, type = "info") {
    console.log(`Alert (${type}): ${message}`);
    
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll(".alert.alert-custom");
    existingAlerts.forEach(alert => alert.remove());
    
    // Create new alert
    const alertDiv = document.createElement("div");
    alertDiv.className = `alert alert-${type === "error" ? "danger" : type} alert-dismissible fade show alert-custom`;
    alertDiv.style.position = "fixed";
    alertDiv.style.top = "20px";
    alertDiv.style.right = "20px";
    alertDiv.style.zIndex = "9999";
    alertDiv.style.minWidth = "300px";
    
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv && alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

function populateSelect(elementId, data, valueField, textField) {
    const select = document.getElementById(elementId);
    if (!select) {
        console.warn(`Select element ${elementId} not found`);
        return;
    }
    
    // Keep the first option (usually "All" or "Select")
    const firstOption = select.options[0];
    select.innerHTML = "";
    select.appendChild(firstOption);
    
    if (data && Array.isArray(data)) {
        data.forEach(item => {
            const option = document.createElement("option");
            option.value = item[valueField];
            option.textContent = item[textField];
            select.appendChild(option);
        });
    }
}

// ==================== MASTER DATA LOADING ====================
async function loadMasterData() {
    console.log("📥 Loading master data...");
    
    try {
        const responses = await Promise.all([
            fetch(`/api/deps/regions`, apiOptions),
            fetch(`/api/deps/channels`, apiOptions),
            fetch(`/api/deps/suppliers`, apiOptions),
            fetch(`/api/deps/categories`, apiOptions),
            fetch(`/api/deps/salesmen`, apiOptions)
        ]);
        
        const [regionsData, channelsData, suppliersData, categoriesData, salesmenData] = await Promise.all(
            responses.map(response => response.json())
        );
        
        // Store master data (Laravel API returns { "data": [...] } format)
        masterData.regions = regionsData.data || [];
        masterData.channels = channelsData.data || [];
        masterData.suppliers = suppliersData.data || [];
        masterData.categories = categoriesData.data || [];
        masterData.salesmen = salesmenData.data || [];
        
        // Populate filters
        populateSelect("filter_region", masterData.regions, "id", "name");
        populateSelect("filter_channel", masterData.channels, "id", "name");
        populateSelect("filter_supplier", masterData.suppliers, "id", "name");
        populateSelect("filter_category", masterData.categories, "id", "name");
        populateSelect("filter_salesman", masterData.salesmen, "id", "name");
        
        console.log("✅ Master data loaded successfully");
        
    } catch (error) {
        console.error("❌ Error loading master data:", error);
        showAlert("Failed to load filter data", "error");
    }
}

// ==================== MATRIX LOADING ====================
async function loadTargetMatrix() {
    console.log("📊 Loading target matrix...");
    
    const year = document.getElementById("target_year")?.value;
    const month = document.getElementById("target_month")?.value;
    
    if (!year || !month) {
        showAlert("Please select both year and month", "warning");
        return;
    }
    
    // Show loading
    document.getElementById("matrix-loading").style.display = "block";
    document.getElementById("matrix-container").style.display = "none";
    document.getElementById("matrix-empty").style.display = "none";
    
    const loadBtn = document.getElementById("loadMatrixBtn");
    if (loadBtn) {
        loadBtn.disabled = true;
        loadBtn.innerHTML = `<i class="bi bi-hourglass-split me-2"></i>Loading...`;
    }
    
    try {
        const filters = getCurrentFilters();
        const params = new URLSearchParams({
            year: year,
            month: month,
            ...filters
        });
        
        const response = await fetch(`/api/targets/matrix?${params}`, apiOptions);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        
        // Laravel API returns { "data": [...] } format
        if (result.data !== undefined) {
            matrixData = result.data;
            displayMatrixData(matrixData);
            showAlert(`Matrix loaded with ${matrixData.length} records`, "success");
        } else {
            throw new Error(result.message || result.error || "No data received from server");
        }
        
    } catch (error) {
        console.error("❌ Error loading matrix:", error);
        showAlert("Failed to load target matrix: " + error.message, "error");
        
        // Show empty state
        document.getElementById("matrix-empty").style.display = "block";
        
    } finally {
        // Hide loading
        document.getElementById("matrix-loading").style.display = "none";
        
        // Reset button
        if (loadBtn) {
            loadBtn.disabled = false;
            loadBtn.innerHTML = `<i class="bi bi-table me-2"></i>Load Matrix`;
        }
    }
}

function displayMatrixData(data) {
    const tbody = document.querySelector("#target-matrix tbody");
    if (!tbody) return;
    
    tbody.innerHTML = "";
    
    if (!data || data.length === 0) {
        document.getElementById("matrix-container").style.display = "none";
        document.getElementById("matrix-empty").style.display = "block";
        return;
    }
    
    data.forEach(row => {
        const tr = document.createElement("tr");
        tr.setAttribute("data-salesman-id", row.salesman_id);
        tr.setAttribute("data-supplier-id", row.supplier_id);
        tr.setAttribute("data-category-id", row.category_id);
        tr.setAttribute("data-region-id", row.region_id);
        tr.setAttribute("data-channel-id", row.channel_id);
        
        tr.innerHTML = `
            <td>${row.salesman_name} (${row.salesman_code})</td>
            <td>${row.region_name || "N/A"}</td>
            <td>${row.channel_name || "N/A"}</td>
            <td>${row.supplier_name}</td>
            <td>${row.category_name}</td>
            <td>
                <input type="number" 
                       class="form-control form-control-sm target-amount" 
                       value="${row.target_amount}" 
                       min="0" 
                       step="0.01"
                       style="width: 120px;">
            </td>
            <td>
                <button type="button" 
                        class="btn btn-sm btn-outline-primary" 
                        onclick="saveIndividualTarget(this)">
                    <i class="bi bi-check"></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(tr);
    });
    
    document.getElementById("matrix-container").style.display = "block";
    document.getElementById("matrix-empty").style.display = "none";
}

// ==================== FILTER FUNCTIONS ====================
function getCurrentFilters() {
    return {
        classification: document.getElementById("filter_classification")?.value || "",
        region_id: document.getElementById("filter_region")?.value || "",
        channel_id: document.getElementById("filter_channel")?.value || "",
        supplier_id: document.getElementById("filter_supplier")?.value || "",
        category_id: document.getElementById("filter_category")?.value || "",
        salesman_id: document.getElementById("filter_salesman")?.value || ""
    };
}

function applyFilters() {
    console.log("🔍 Applying filters...");
    const filters = getCurrentFilters();
    console.log("Filters:", filters);
    
    // Reload matrix with filters
    loadTargetMatrix();
}

function resetFilters() {
    console.log("🧹 Resetting filters...");
    
    document.getElementById("filter_classification").value = "";
    document.getElementById("filter_region").value = "";
    document.getElementById("filter_channel").value = "";
    document.getElementById("filter_supplier").value = "";
    document.getElementById("filter_category").value = "";
    document.getElementById("filter_salesman").value = "";
    
    showAlert("Filters cleared", "info");
}

// ==================== SAVE FUNCTIONS ====================
async function saveAllTargets() {
    console.log("💾 Saving all targets...");
    
    const year = document.getElementById("target_year")?.value;
    const month = document.getElementById("target_month")?.value;
    
    if (!year || !month) {
        showAlert("Please select year and month first", "warning");
        return;
    }
    
    const targets = [];
    const rows = document.querySelectorAll("#target-matrix tbody tr[data-salesman-id]");
    
    rows.forEach(row => {
        const input = row.querySelector(".target-amount");
        if (input) {
            const targetAmount = parseFloat(input.value) || 0;
            targets.push({
                salesman_id: parseInt(row.getAttribute("data-salesman-id")),
                supplier_id: parseInt(row.getAttribute("data-supplier-id")),
                category_id: parseInt(row.getAttribute("data-category-id")),
                region_id: parseInt(row.getAttribute("data-region-id")),
                channel_id: parseInt(row.getAttribute("data-channel-id")),
                target_amount: targetAmount
            });
        }
    });
    
    if (targets.length === 0) {
        showAlert("No targets to save", "warning");
        return;
    }
    
    // Disable save buttons
    const saveAllBtn = document.getElementById("saveAllBtn");
    const saveMatrixBtn = document.getElementById("saveMatrixBtn");
    
    if (saveAllBtn) {
        saveAllBtn.disabled = true;
        saveAllBtn.innerHTML = `<i class="bi bi-hourglass-split me-2"></i>Saving...`;
    }
    if (saveMatrixBtn) {
        saveMatrixBtn.disabled = true;
        saveMatrixBtn.innerHTML = `<i class="bi bi-hourglass-split me-1"></i>Saving...`;
    }
    
    try {
        const response = await fetch(`${API_BASE}/save-targets.php`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify({
                year: parseInt(year),
                month: parseInt(month),
                targets: targets
            })
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            showAlert(`Successfully saved ${result.saved} targets`, "success");
        } else {
            throw new Error(result.error || "Save failed");
        }
        
    } catch (error) {
        console.error("❌ Error saving targets:", error);
        showAlert("Failed to save targets: " + error.message, "error");
        
    } finally {
        // Re-enable save buttons
        if (saveAllBtn) {
            saveAllBtn.disabled = false;
            saveAllBtn.innerHTML = `<i class="bi bi-check-circle me-2"></i>Save All Targets`;
        }
        if (saveMatrixBtn) {
            saveMatrixBtn.disabled = false;
            saveMatrixBtn.innerHTML = `<i class="bi bi-floppy me-1"></i>Save All`;
        }
    }
}

async function saveIndividualTarget(button) {
    const row = button.closest("tr");
    const input = row.querySelector(".target-amount");
    const targetAmount = parseFloat(input.value) || 0;
    
    const year = document.getElementById("target_year")?.value;
    const month = document.getElementById("target_month")?.value;
    
    if (!year || !month) {
        showAlert("Please select year and month first", "warning");
        return;
    }
    
    button.disabled = true;
    button.innerHTML = `<i class="bi bi-hourglass-split"></i>`;
    
    try {
        const response = await fetch(`${API_BASE}/save-targets.php`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify({
                year: parseInt(year),
                month: parseInt(month),
                targets: [{
                    salesman_id: parseInt(row.getAttribute("data-salesman-id")),
                    supplier_id: parseInt(row.getAttribute("data-supplier-id")),
                    category_id: parseInt(row.getAttribute("data-category-id")),
                    region_id: parseInt(row.getAttribute("data-region-id")),
                    channel_id: parseInt(row.getAttribute("data-channel-id")),
                    target_amount: targetAmount
                }]
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert("Target saved successfully", "success");
        } else {
            throw new Error(result.error || "Save failed");
        }
        
    } catch (error) {
        console.error("❌ Error saving target:", error);
        showAlert("Failed to save target: " + error.message, "error");
        
    } finally {
        button.disabled = false;
        button.innerHTML = `<i class="bi bi-check"></i>`;
    }
}

// ==================== EXPORT FUNCTION ====================
function exportTargets() {
    console.log("📤 Exporting targets...");
    
    const year = document.getElementById("target_year")?.value;
    const month = document.getElementById("target_month")?.value;
    
    if (!year || !month) {
        showAlert("Please select year and month first", "warning");
        return;
    }
    
    const filters = getCurrentFilters();
    const params = new URLSearchParams({
        year: year,
        month: month,
        ...filters
    });
    
    // Create download link
    const downloadUrl = `${API_BASE}/export-targets.php?${params}`;
    
    // Create temporary link and click it
    const link = document.createElement("a");
    link.href = downloadUrl;
    link.download = `targets_${year}_${month}.csv`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showAlert("Export started. File will download shortly.", "info");
}

// ==================== UPLOAD FUNCTIONS ====================
function showUploadModal() {
    const year = document.getElementById("target_year")?.value;
    const month = document.getElementById("target_month")?.value;
    
    if (!year || !month) {
        showAlert("Please select year and month first", "warning");
        return;
    }
    
    document.getElementById("upload_year").value = year;
    document.getElementById("upload_month").value = month;
    
    const modal = new bootstrap.Modal(document.getElementById("uploadModal"));
    modal.show();
}

async function uploadTargets() {
    console.log("📤 Uploading targets...");
    
    const fileInput = document.getElementById("upload_file");
    const file = fileInput.files[0];
    
    if (!file) {
        showAlert("Please select a CSV file", "warning");
        return;
    }
    
    const year = document.getElementById("upload_year").value;
    const month = document.getElementById("upload_month").value;
    
    const formData = new FormData();
    formData.append("csv_file", file);
    formData.append("year", year);
    formData.append("month", month);
    
    try {
        const response = await fetch(`${API_BASE}/upload-targets.php`, {
            method: "POST",
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert(`Upload completed. Processed: ${result.processed} records`, "success");
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById("uploadModal"));
            modal.hide();
            
            // Reload matrix
            loadTargetMatrix();
            
        } else {
            throw new Error(result.error || "Upload failed");
        }
        
    } catch (error) {
        console.error("❌ Error uploading targets:", error);
        showAlert("Failed to upload targets: " + error.message, "error");
    }
}

// ==================== TEMPLATE DOWNLOAD ====================
function downloadTemplate() {
    console.log("📥 Downloading template...");
    
    // Create download link
    const downloadUrl = `${API_BASE}/download-template.php`;
    
    // Create temporary link and click it
    const link = document.createElement("a");
    link.href = downloadUrl;
    link.download = `targets_template_${new Date().toISOString().split("T")[0]}.csv`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showAlert("Template download started", "info");
}

// ==================== UTILITY FUNCTIONS ====================
function fillAllAmounts() {
    const amount = prompt("Enter amount to fill for all visible targets:", "1000");
    if (amount === null) return;
    
    const parsedAmount = parseFloat(amount);
    if (isNaN(parsedAmount) || parsedAmount < 0) {
        showAlert("Please enter a valid positive number", "warning");
        return;
    }
    
    const inputs = document.querySelectorAll("#target-matrix .target-amount");
    inputs.forEach(input => {
        input.value = parsedAmount;
    });
    
    showAlert(`All amounts filled with ${parsedAmount}`, "success");
}

function clearAllAmounts() {
    if (!confirm("Are you sure you want to clear all target amounts?")) {
        return;
    }
    
    const inputs = document.querySelectorAll("#target-matrix .target-amount");
    inputs.forEach(input => {
        input.value = 0;
    });
    
    showAlert("All amounts cleared", "info");
}

// ==================== INITIALIZATION ====================
document.addEventListener("DOMContentLoaded", function() {
    console.log("🚀 Target page initialized");
    
    // Load master data for filters
    loadMasterData();
    
    // Set up global functions
    window.loadTargetMatrix = loadTargetMatrix;
    window.saveAllTargets = saveAllTargets;
    window.saveIndividualTarget = saveIndividualTarget;
    window.applyFilters = applyFilters;
    window.resetFilters = resetFilters;
    window.exportTargets = exportTargets;
    window.showUploadModal = showUploadModal;
    window.uploadTargets = uploadTargets;
    window.downloadTemplate = downloadTemplate;
    window.fillAllAmounts = fillAllAmounts;
    window.clearAllAmounts = clearAllAmounts;
    
    console.log("✅ All functions loaded and ready");
});

</script>
@endsection