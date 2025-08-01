@extends('layouts.app')

@section('title', __('Sales Targets'))

@section('content')
<div id="alert-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;"></div>
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
                <select class="form-select" id="filter__category">
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
            </div>
        </div>
    </div>
</div>

<!-- Target Matrix -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">{{ __('Target Matrix') }}</h5>
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
@endsection

@push('scripts')
<script>
    console.log("🎯 TARGET PAGE SCRIPT LOADED - V2.4 FINAL & CORRECTED");

    // ==================== GLOBAL VARIABLES ====================
    let salesmenData = [];
    let suppliersData = [];
    let targetsData = {}; 
    let masterData = {
        regions: [],
        channels: [],
        suppliers: [],
        categories: [],
        salesmen: []
    };

    // ==================== API CONFIGURATION ====================
    const apiOptions = {
        headers: {
            "Accept": "application/json",
            "Content-Type": "application/json",
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    };

    // ==================== UTILITY FUNCTIONS ====================
    function showAlert(message, type = "info") {
        const alertContainer = document.getElementById('alert-container');
        const alertDiv = document.createElement("div");
        alertDiv.className = `alert alert-${type === "error" ? "danger" : type} alert-dismissible fade show`;
        alertDiv.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        alertContainer.appendChild(alertDiv);
        setTimeout(() => alertDiv.remove(), 5000);
    }

    function populateSelect(elementId, data, valueField, textField) {
        const select = document.getElementById(elementId);
        if (!select) return;
        
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
                fetch(`/api/deps/regions`, { headers: apiOptions.headers }),
                fetch(`/api/deps/channels`, { headers: apiOptions.headers }),
                fetch(`/api/deps/suppliers`, { headers: apiOptions.headers }),
                fetch(`/api/deps/categories`, { headers: apiOptions.headers }),
                fetch(`/api/deps/salesmen`, { headers: apiOptions.headers })
            ]);

            const [regions, channels, suppliers, categories, salesmen] = await Promise.all(responses.map(res => res.json()));

            masterData = {
                regions: regions.data || [],
                channels: channels.data || [],
                suppliers: suppliers.data || [],
                categories: categories.data || [],
                salesmen: salesmen.data || []
            };

            populateSelect("filter_region", masterData.regions, "id", "name");
            populateSelect("filter_channel", masterData.channels, "id", "name");
            populateSelect("filter_supplier", masterData.suppliers, "id", "name");
            populateSelect("filter_category", masterData.categories, "id", "name");
            populateSelect("filter_salesman", masterData.salesmen, "id", "name");
            
            console.log("✅ Master data loaded");
        } catch (error) {
            console.error("❌ Error loading master data:", error);
            showAlert("Failed to load filter data.", "error");
        }
    }

    // ==================== MATRIX LOADING & RENDERING ====================
    async function loadTargetMatrix() {
        console.log("📊 Loading target matrix...");
        const year = document.getElementById("target_year")?.value;
        const month = document.getElementById("target_month")?.value;

        if (!year || !month) {
            showAlert("Please select both year and month.", "warning");
            return;
        }

        const loadBtn = document.getElementById("loadMatrixBtn");
        loadBtn.disabled = true;
        loadBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Loading...`;

        document.getElementById("matrix-loading").style.display = "block";
        document.getElementById("matrix-container").style.display = "none";
        document.getElementById("matrix-empty").style.display = "none";

        try {
            const filters = getCurrentFilters();
            const params = new URLSearchParams({ year, month, ...filters });
            const response = await fetch(`/api/targets/matrix?${params}`, { headers: apiOptions.headers });

            if (!response.ok) throw new Error(`Server Error: ${response.statusText}`);
            
            const result = await response.json();
            
            if (result.data && typeof result.data === 'object') {
                salesmenData = result.data.salesmen || [];
                suppliersData = result.data.suppliers || [];
                targetsData = (result.data.targets || []).reduce((map, t) => {
                    map[`${t.salesman_id}-${t.supplier_id}-${t.category_id}`] = t.target_amount;
                    return map;
                }, {});

                renderMatrixPage();
                showAlert(`Matrix data loaded.`, "success");
            } else {
                 throw new Error("Invalid data format received from server.");
            }
        } catch (error) {
            console.error("❌ Error loading matrix:", error);
            showAlert(`Failed to load target matrix: ${error.message}`, "error");
            document.getElementById("matrix-empty").style.display = "block";
        } finally {
            loadBtn.disabled = false;
            loadBtn.innerHTML = `<i class="bi bi-table me-2"></i>Load Matrix`;
            document.getElementById("matrix-loading").style.display = "none";
        }
    }
    
    function renderMatrixPage() {
        const tbody = document.querySelector("#target-matrix tbody");
        if (!tbody) return;

        tbody.innerHTML = "";
        
        if (salesmenData.length === 0 || suppliersData.length === 0) {
            document.getElementById("matrix-container").style.display = "none";
            document.getElementById("matrix-empty").style.display = "block";
            return;
        }
        
        salesmenData.forEach(salesman => {
            suppliersData.forEach(supplier => {
                if (isClassificationCompatible(salesman.salesman_classification, supplier.supplier_classification)) {
                    const tr = document.createElement("tr");
                    const targetKey = `${salesman.salesman_id}-${supplier.supplier_id}-${supplier.category_id}`;
                    const targetAmount = targetsData[targetKey] || "";

                    tr.innerHTML = `
                        <td>${salesman.salesman_name} (${salesman.salesman_code})</td>
                        <td>${salesman.region_name || "N/A"}</td>
                        <td>${salesman.channel_name || "N/A"}</td>
                        <td>${supplier.supplier_name}</td>
                        <td>${supplier.category_name}</td>
                        <td>
                            <input type="number" class="form-control form-control-sm target-amount"
                                   value="${targetAmount}" min="0" step="0.01" style="width: 120px;"
                                   data-salesman-id="${salesman.salesman_id}"
                                   data-supplier-id="${supplier.supplier_id}"
                                   data-category-id="${supplier.category_id}">
                        </td>
                    `;
                    tbody.appendChild(tr);
                }
            });
        });

        document.getElementById("matrix-container").style.display = "block";
        document.getElementById("matrix-empty").style.display = "none";
    }

    function isClassificationCompatible(salesmanClass, supplierClass) {
        if (!salesmanClass || !supplierClass) return true;
        if (salesmanClass === 'both' || supplierClass === 'both') return true;
        return salesmanClass === supplierClass;
    }


    // ==================== FILTER & SAVE FUNCTIONS ====================
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
        loadTargetMatrix();
    }

    function resetFilters() {
        const filterIds = ["filter_classification", "filter_region", "filter_channel", "filter_supplier", "filter_category", "filter_salesman"];
        filterIds.forEach(id => document.getElementById(id).value = "");
        showAlert("Filters cleared.", "info");
        loadTargetMatrix();
    }

    async function saveAllTargets() {
        const year = document.getElementById("target_year")?.value;
        const month = document.getElementById("target_month")?.value;
        if (!year || !month) {
            showAlert("Please select year and month before saving.", "warning");
            return;
        }

        const targetsToSave = [];
        document.querySelectorAll(".target-amount").forEach(input => {
            const amount = parseFloat(input.value) || 0;
            if (!isNaN(amount) && amount >= 0) {
                targetsToSave.push({
                    salesman_id: parseInt(input.dataset.salesmanId),
                    supplier_id: parseInt(input.dataset.supplierId),
                    category_id: parseInt(input.dataset.categoryId),
                    target_amount: amount
                });
            }
        });

        if (targetsToSave.length === 0) {
            showAlert("No new targets to save.", "info");
            return;
        }

        const saveBtn = document.getElementById("saveAllBtn");
        saveBtn.disabled = true;
        saveBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Saving...`;

        try {
            const response = await fetch(`/api/targets/bulk-save`, {
                method: 'POST',
                headers: apiOptions.headers,
                body: JSON.stringify({ year, month, targets: targetsToSave })
            });
            if (!response.ok) {
                 const errorData = await response.json();
                 throw new Error(errorData.message || `Server error ${response.status}`);
            }
            const result = await response.json();
            showAlert(`${result.saved_count} targets saved successfully.`, "success");
            
            targetsToSave.forEach(t => {
                targetsData[`${t.salesman_id}-${t.supplier_id}-${t.category_id}`] = t.target_amount;
            });

        } catch (error) {
            console.error("❌ Error saving targets:", error);
            showAlert(`Failed to save targets: ${error.message}`, "error");
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = `<i class="bi bi-check-circle me-2"></i>Save All Targets`;
        }
    }
    
    // ==================== OTHER FUNCTIONS (Upload, Export, etc.) ====================
    function showUploadModal() {
        showAlert("Upload functionality is not yet implemented in this view.", "info");
    }
    
    function exportTargets() {
        showAlert("Export functionality is not yet implemented in this view.", "info");
    }

    function downloadTemplate() {
        showAlert("Template download is not yet implemented in this view.", "info");
    }


    // ==================== INITIALIZATION ====================
    document.addEventListener("DOMContentLoaded", function() {
        console.log("🚀 Target page initialized");
        loadMasterData();

        window.loadTargetMatrix = loadTargetMatrix;
        window.applyFilters = applyFilters;
        window.resetFilters = resetFilters;
        window.saveAllTargets = saveAllTargets;
        window.showUploadModal = showUploadModal;
        window.exportTargets = exportTargets;
        window.downloadTemplate = downloadTemplate;
    });

</script>
@endpush
