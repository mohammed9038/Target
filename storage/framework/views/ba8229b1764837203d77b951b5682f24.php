

<?php $__env->startSection('title', __('Sales Targets')); ?>

<?php $__env->startSection('content'); ?>
<div id="alert-container"></div>
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="h2 mb-1"><?php echo e(__('Sales Targets')); ?></h1>
        <p class="text-muted mb-0"><?php echo e(__('Set sales targets for your team members')); ?></p>
    </div>
    <div class="d-flex gap-2" style="margin-top: 0.5rem;">
        <button type="button" class="btn btn-success" onclick="saveAllTargets()" id="saveAllBtn">
            <i class="bi bi-check-circle me-2"></i><?php echo e(__('Save All Targets')); ?>

        </button>
        <button type="button" class="btn btn-outline-secondary" onclick="exportTargets()">
            <i class="bi bi-file-earmark-spreadsheet me-2"></i><?php echo e(__('Export CSV')); ?>

        </button>
        <button type="button" class="btn btn-outline-primary" onclick="showUploadModal()">
            <i class="bi bi-upload me-2"></i><?php echo e(__('Upload Targets')); ?>

        </button>
        <button type="button" class="btn btn-outline-info" onclick="downloadTemplate()">
            <i class="bi bi-download me-2"></i><?php echo e(__('Download Template')); ?>

        </button>
    </div>
</div>

<!-- Period Selection -->
<div class="row mb-3">
    <div class="col-md-3">
        <label for="target_year" class="form-label"><?php echo e(__('Year')); ?></label>
        <select class="form-select" id="target_year" name="year">
            <option value=""><?php echo e(__('Select Year')); ?></option>
            <?php for($y = date('Y'); $y <= date('Y') + 2; $y++): ?>
                <option value="<?php echo e($y); ?>" <?php echo e($y == date('Y') ? 'selected' : ''); ?>><?php echo e($y); ?></option>
            <?php endfor; ?>
        </select>
    </div>
    <div class="col-md-3">
        <label for="target_month" class="form-label"><?php echo e(__('Month')); ?></label>
        <select class="form-select" id="target_month" name="month">
            <option value=""><?php echo e(__('Select Month')); ?></option>
            <?php for($m = 1; $m <= 12; $m++): ?>
                <option value="<?php echo e($m); ?>" <?php echo e($m == date('n') ? 'selected' : ''); ?>>
                    <?php echo e(date('F', mktime(0, 0, 0, $m, 1))); ?>

                </option>
            <?php endfor; ?>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">&nbsp;</label>
        <button type="button" class="btn btn-primary w-100" onclick="loadTargetMatrix()" id="loadMatrixBtn">
            <i class="bi bi-table me-2"></i><?php echo e(__('Load Matrix')); ?>

        </button>
    </div>
</div>

<!-- Filters Section -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0"><?php echo e(__('Filters')); ?></h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-2">
                <label for="filter_classification" class="form-label"><?php echo e(__('Classification')); ?></label>
                <select class="form-select" id="filter_classification">
                    <option value=""><?php echo e(__('All Classifications')); ?></option>
                    <option value="food"><?php echo e(__('Food')); ?></option>
                    <option value="non_food"><?php echo e(__('Non Food')); ?></option>
                    <option value="both"><?php echo e(__('Both')); ?></option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="filter_region" class="form-label"><?php echo e(__('Region')); ?></label>
                <select class="form-select" id="filter_region">
                    <option value=""><?php echo e(__('All Regions')); ?></option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="filter_channel" class="form-label"><?php echo e(__('Channel')); ?></label>
                <select class="form-select" id="filter_channel">
                    <option value=""><?php echo e(__('All Channels')); ?></option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="filter_supplier" class="form-label"><?php echo e(__('Supplier')); ?></label>
                <select class="form-select" id="filter_supplier">
                    <option value=""><?php echo e(__('All Suppliers')); ?></option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="filter_category" class="form-label"><?php echo e(__('Category')); ?></label>
                <select class="form-select" id="filter_category">
                    <option value=""><?php echo e(__('All Categories')); ?></option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="filter_salesman" class="form-label"><?php echo e(__('Salesman')); ?></label>
                <select class="form-select" id="filter_salesman">
                    <option value=""><?php echo e(__('All Salesmen')); ?></option>
                </select>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <button type="button" class="btn btn-primary me-2" onclick="applyFilters()">
                    <i class="bi bi-funnel me-1"></i><?php echo e(__('Apply Filters')); ?>

                </button>
                <button type="button" class="btn btn-outline-secondary me-2" onclick="resetFilters()">
                    <i class="bi bi-x-circle me-1"></i><?php echo e(__('Clear Filters')); ?>

                </button>
            </div>
        </div>
    </div>
</div>

<!-- Target Matrix -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0"><?php echo e(__('Target Matrix')); ?></h5>
    </div>
    <div class="card-body">
        <div id="matrix-loading" class="text-center py-4" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden"><?php echo e(__('Loading...')); ?></span>
            </div>
            <p class="mt-2 mb-0"><?php echo e(__('Loading target matrix...')); ?></p>
        </div>
        
        <div id="matrix-container" style="display: none;">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="target-matrix">
                    <thead class="table-dark">
                        <tr>
                            <th><?php echo e(__('Salesman')); ?></th>
                            <th><?php echo e(__('Region')); ?></th>
                            <th><?php echo e(__('Channel')); ?></th>
                            <th><?php echo e(__('Supplier')); ?></th>
                            <th><?php echo e(__('Category')); ?></th>
                            <th><?php echo e(__('Target Amount')); ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
             <div id="pagination-container" class="mt-3"></div>
        </div>
        
        <div id="matrix-empty" class="text-center py-4">
            <i class="bi bi-table text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-2 mb-0"><?php echo e(__('No data available. Please select year and month, then click Load Matrix.')); ?></p>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    console.log("🎯 TARGET PAGE SCRIPT LOADED - V2.2 FINAL");

    // ==================== GLOBAL VARIABLES ====================
    let salesmenData = [];
    let suppliersData = [];
    let targetsData = {}; // Use a map for quick lookups: 'salesman-supplier-category' -> amount
    let masterData = {
        regions: [],
        channels: [],
        suppliers: [],
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
        if (!alertContainer) {
            const container = document.createElement('div');
            container.id = 'alert-container';
            container.style.position = 'fixed';
            container.style.top = '20px';
            container.style.right = '20px';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }
        
        const alertDiv = document.createElement("div");
        alertDiv.className = `alert alert-${type === "error" ? "danger" : type} alert-dismissible fade show`;
        alertDiv.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        document.getElementById('alert-container').appendChild(alertDiv);
        
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
            const [regionsRes, channelsRes, salesmenRes, suppliersRes, categoriesRes] = await Promise.all([
                fetch(`/api/deps/regions`, { headers: apiOptions.headers }),
                fetch(`/api/deps/channels`, { headers: apiOptions.headers }),
                fetch(`/api/deps/salesmen`, { headers: apiOptions.headers }),
                fetch(`/api/deps/suppliers`, { headers: apiOptions.headers }),
                fetch(`/api/deps/categories`, { headers: apiOptions.headers })
            ]);

            masterData.regions = (await regionsRes.json()).data || [];
            masterData.channels = (await channelsRes.json()).data || [];
            masterData.salesmen = (await salesmenRes.json()).data || [];
            masterData.suppliers = (await suppliersRes.json()).data || [];
            const categories = (await categoriesRes.json()).data || [];

            populateSelect("filter_region", masterData.regions, "id", "name");
            populateSelect("filter_channel", masterData.channels, "id", "name");
            populateSelect("filter_salesman", masterData.salesmen, "id", "name");
            populateSelect("filter_supplier", masterData.suppliers, "id", "name");
            populateSelect("filter_category", categories, "id", "name");
            
            console.log("✅ Master data loaded");
        } catch (error) {
            console.error("❌ Error loading master data:", error);
            showAlert("Failed to load filter data. Please refresh the page.", "error");
        }
    }

    // ==================== MATRIX LOADING & RENDERING ====================
    async function loadTargetMatrix() {
        console.log("📊 Loading target matrix...");
        const year = document.getElementById("target_year")?.value;
        const month = document.getElementById("target_month")?.value;

        if (!year || !month) {
            showAlert("Please select both year and month", "warning");
            return;
        }

        const loadBtn = document.getElementById("loadMatrixBtn");
        loadBtn.disabled = true;
        loadBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...`;

        document.getElementById("matrix-loading").style.display = "block";
        document.getElementById("matrix-container").style.display = "none";
        document.getElementById("matrix-empty").style.display = "none";
        document.getElementById('pagination-container').innerHTML = '';


        try {
            const filters = getCurrentFilters();
            const params = new URLSearchParams({ year, month, ...filters });
            const response = await fetch(`/api/targets/matrix?${params}`, { headers: apiOptions.headers });

            if (!response.ok) throw new Error(`Server returned ${response.status}: ${response.statusText}`);
            
            const result = await response.json();
            
            if (result.data && typeof result.data === 'object') {
                salesmenData = result.data.salesmen || [];
                suppliersData = result.data.suppliers || [];
                targetsData = (result.data.targets || []).reduce((map, t) => {
                    map[`${t.salesman_id}-${t.supplier_id}-${t.category_id}`] = t.target_amount;
                    return map;
                }, {});

                renderMatrixPage();
                showAlert(`Matrix data loaded successfully.`, "success");
            } else {
                 throw new Error("Invalid data format from server. Expected an object with salesmen, suppliers, and targets.");
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
            const amount = parseFloat(input.value);
            if (!isNaN(amount) && amount >= 0 && input.value.trim() !== '') {
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
            showAlert(`${result.saved} targets saved successfully.`, "success");
            
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

    // ==================== INITIALIZATION ====================
    document.addEventListener("DOMContentLoaded", function() {
        console.log("🚀 Target page initialized");
        loadMasterData();

        window.loadTargetMatrix = loadTargetMatrix;
        window.applyFilters = applyFilters;
        window.resetFilters = resetFilters;
        window.saveAllTargets = saveAllTargets;
    });

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\New target\target-system\resources\views/targets/index.blade.php ENDPATH**/ ?>