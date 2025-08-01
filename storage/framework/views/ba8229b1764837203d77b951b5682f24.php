

<?php $__env->startSection('title', __('Sales Targets')); ?>

<?php $__env->startSection('content'); ?>
<div id="alert-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;"></div>
<!-- Modern Page Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="h2 mb-2 fw-bold d-flex align-items-center">
            <div class="p-2 rounded-circle bg-primary bg-opacity-10 me-3">
                <i class="bi bi-bullseye text-primary"></i>
            </div>
            <?php echo e(__('Sales Targets')); ?>

        </h1>
        <p class="text-muted mb-0 ms-5 ps-2"><?php echo e(__('Set and manage sales targets for your team members')); ?></p>
    </div>
    <div class="d-flex gap-2 flex-wrap" style="margin-top: 0.5rem;">
        <button type="button" class="btn btn-success shadow-sm" onclick="saveAllTargets()" id="saveAllBtn" style="border-radius: 8px;">
            <i class="bi bi-check-circle me-2"></i><?php echo e(__('Save All Targets')); ?>

        </button>
        <div class="btn-group shadow-sm" role="group" style="border-radius: 8px;">
            <button type="button" class="btn btn-outline-primary" onclick="exportTargets()" style="border-radius: 8px 0 0 8px;">
                <i class="bi bi-file-earmark-spreadsheet me-2"></i><?php echo e(__('Export CSV')); ?>

            </button>
            <button type="button" class="btn btn-outline-primary" onclick="showUploadModal()" style="border-radius: 0;">
                <i class="bi bi-upload me-2"></i><?php echo e(__('Upload')); ?>

            </button>
            <button type="button" class="btn btn-outline-primary" onclick="downloadTemplate()" style="border-radius: 0 8px 8px 0;">
                <i class="bi bi-download me-2"></i><?php echo e(__('Template')); ?>

            </button>
        </div>
    </div>
</div>

<!-- Modern Compact Filters Panel -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
    <div class="card-body p-4">
        <!-- Period Selection Row -->
        <div class="row g-3 mb-4">
            <div class="col-auto">
                <h6 class="text-primary fw-semibold mb-3 d-flex align-items-center">
                    <i class="bi bi-calendar3 me-2"></i><?php echo e(__('Target Period')); ?>

                </h6>
            </div>
        </div>
        
        <div class="row g-3 mb-4">
            <div class="col-lg-2 col-md-3">
                <label for="target_year" class="form-label small fw-medium text-dark">
                    <i class="bi bi-calendar-year me-1 text-primary"></i><?php echo e(__('Year')); ?>

                </label>
                <select class="form-select form-select-sm border-0 shadow-sm" id="target_year" name="year" style="border-radius: 8px;">
                    <option value=""><?php echo e(__('Select Year')); ?></option>
                    <?php for($y = date('Y'); $y <= date('Y') + 2; $y++): ?>
                        <option value="<?php echo e($y); ?>" <?php echo e($y == date('Y') ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-lg-2 col-md-3">
                <label for="target_month" class="form-label small fw-medium text-dark">
                    <i class="bi bi-calendar-month me-1 text-primary"></i><?php echo e(__('Month')); ?>

                </label>
                <select class="form-select form-select-sm border-0 shadow-sm" id="target_month" name="month" style="border-radius: 8px;">
                    <option value=""><?php echo e(__('Select Month')); ?></option>
                    <?php for($m = 1; $m <= 12; $m++): ?>
                        <option value="<?php echo e($m); ?>" <?php echo e($m == date('n') ? 'selected' : ''); ?>>
                            <?php echo e(date('F', mktime(0, 0, 0, $m, 1))); ?>

                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-lg-2 col-md-3">
                <label class="form-label small fw-medium text-dark">
                    <i class="bi bi-play-circle me-1 text-success"></i><?php echo e(__('Action')); ?>

                </label>
                <button class="btn btn-primary btn-sm w-100 shadow-sm" id="loadMatrixBtn" onclick="loadTargetMatrix()" style="border-radius: 8px;">
                    <i class="bi bi-table me-2"></i><?php echo e(__('Load Matrix')); ?>

                </button>
            </div>
        </div>

        <!-- Divider -->
        <hr class="my-4" style="opacity: 0.1;">

        <!-- Filters Row -->
        <div class="row g-3 mb-3">
            <div class="col-auto">
                <h6 class="text-primary fw-semibold mb-3 d-flex align-items-center">
                    <i class="bi bi-funnel me-2"></i><?php echo e(__('Filters')); ?>

                    <small class="text-muted ms-2 fw-normal">(<?php echo e(__('Optional - Filter before loading')); ?>)</small>
                </h6>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-2 col-md-4 col-sm-6">
                <label for="filter_classification" class="form-label small fw-medium text-dark">
                    <i class="bi bi-collection me-1 text-info"></i><?php echo e(__('Classification')); ?>

                </label>
                <select class="form-select form-select-sm border-0 shadow-sm" id="filter_classification" style="border-radius: 8px;">
                    <option value=""><?php echo e(__('All Types')); ?></option>
                    <option value="food"><?php echo e(__('Food')); ?></option>
                    <option value="non_food"><?php echo e(__('Non Food')); ?></option>
                </select>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <label for="filter_region" class="form-label small fw-medium text-dark">
                    <i class="bi bi-geo-alt me-1 text-danger"></i><?php echo e(__('Region')); ?>

                </label>
                <select class="form-select form-select-sm border-0 shadow-sm" id="filter_region" style="border-radius: 8px;">
                    <option value=""><?php echo e(__('All Regions')); ?></option>
                </select>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <label for="filter_channel" class="form-label small fw-medium text-dark">
                    <i class="bi bi-diagram-3 me-1 text-warning"></i><?php echo e(__('Channel')); ?>

                </label>
                <select class="form-select form-select-sm border-0 shadow-sm" id="filter_channel" style="border-radius: 8px;">
                    <option value=""><?php echo e(__('All Channels')); ?></option>
                </select>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <label for="filter_supplier" class="form-label small fw-medium text-dark">
                    <i class="bi bi-building me-1 text-success"></i><?php echo e(__('Supplier')); ?>

                </label>
                <select class="form-select form-select-sm border-0 shadow-sm" id="filter_supplier" style="border-radius: 8px;">
                    <option value=""><?php echo e(__('All Suppliers')); ?></option>
                </select>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <label for="filter_category" class="form-label small fw-medium text-dark">
                    <i class="bi bi-tags me-1 text-primary"></i><?php echo e(__('Category')); ?>

                </label>
                <select class="form-select form-select-sm border-0 shadow-sm" id="filter_category" style="border-radius: 8px;">
                    <option value=""><?php echo e(__('All Categories')); ?></option>
                </select>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <label for="filter_salesman" class="form-label small fw-medium text-dark">
                    <i class="bi bi-person-badge me-1 text-secondary"></i><?php echo e(__('Salesman')); ?>

                </label>
                <select class="form-select form-select-sm border-0 shadow-sm" id="filter_salesman" style="border-radius: 8px;">
                    <option value=""><?php echo e(__('All Salesmen')); ?></option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Modern Target Matrix -->
<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-header border-0 bg-white d-flex justify-content-between align-items-center" style="border-radius: 12px 12px 0 0;">
        <h5 class="card-title mb-0 fw-semibold d-flex align-items-center">
            <div class="p-2 rounded-circle bg-success bg-opacity-10 me-3">
                <i class="bi bi-grid-3x3-gap text-success"></i>
            </div>
            <?php echo e(__('Target Matrix')); ?>

        </h5>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                <i class="bi bi-info-circle me-1"></i><?php echo e(__('Enter target amounts')); ?>

            </span>
        </div>
    </div>
    <div class="card-body p-0">
        <!-- Loading State -->
        <div id="matrix-loading" class="text-center py-5" style="display: none;">
            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden"><?php echo e(__('Loading...')); ?></span>
            </div>
            <h6 class="text-primary fw-semibold"><?php echo e(__('Loading target matrix...')); ?></h6>
            <p class="text-muted small mb-0"><?php echo e(__('Please wait while we fetch the data')); ?></p>
        </div>
        
        <!-- Matrix Container -->
        <div id="matrix-container" style="display: none;">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="target-matrix" style="font-size: 0.9rem;">
                    <thead style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); color: white;">
                        <tr>
                            <th class="border-0 py-3 px-4 fw-semibold">
                                <i class="bi bi-person-badge me-2"></i><?php echo e(__('Salesman')); ?>

                            </th>
                            <th class="border-0 py-3 px-4 fw-semibold">
                                <i class="bi bi-geo-alt me-2"></i><?php echo e(__('Region')); ?>

                            </th>
                            <th class="border-0 py-3 px-4 fw-semibold">
                                <i class="bi bi-diagram-3 me-2"></i><?php echo e(__('Channel')); ?>

                            </th>
                            <th class="border-0 py-3 px-4 fw-semibold">
                                <i class="bi bi-building me-2"></i><?php echo e(__('Supplier')); ?>

                            </th>
                            <th class="border-0 py-3 px-4 fw-semibold">
                                <i class="bi bi-tags me-2"></i><?php echo e(__('Category')); ?>

                            </th>
                            <th class="border-0 py-3 px-4 fw-semibold text-center">
                                <i class="bi bi-currency-dollar me-2"></i><?php echo e(__('Target Amount')); ?>

                            </th>
                        </tr>
                    </thead>
                    <tbody style="background-color: #fafbfc;"></tbody>
                </table>
            </div>
        </div>
        
        <!-- Empty State -->
        <div id="matrix-empty" class="text-center py-5" style="background-color: #fafbfc;">
            <div class="mb-4">
                <div class="d-inline-flex p-4 rounded-circle bg-light border">
                    <i class="bi bi-table text-muted" style="font-size: 2.5rem;"></i>
                </div>
            </div>
            <h6 class="text-dark fw-semibold mb-2"><?php echo e(__('No Data Available')); ?></h6>
            <p class="text-muted mb-4"><?php echo e(__('Please select year, month and click "Load Matrix" to view targets.')); ?></p>
            <div class="d-flex justify-content-center gap-2">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                    <i class="bi bi-1-circle me-1"></i><?php echo e(__('Select Period')); ?>

                </span>
                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                    <i class="bi bi-2-circle me-1"></i><?php echo e(__('Apply Filters')); ?>

                </span>
                <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                    <i class="bi bi-3-circle me-1"></i><?php echo e(__('Load Matrix')); ?>

                </span>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo e(__('Upload Targets')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="upload_file" class="form-label"><?php echo e(__('Select CSV File')); ?></label>
                        <input type="file" class="form-control" id="upload_file" name="csv_file" accept=".csv" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo e(__('Cancel')); ?></button>
                <button type="button" class="btn btn-primary" onclick="uploadTargets()"><?php echo e(__('Upload')); ?></button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    console.log("🎯 TARGET PAGE SCRIPT LOADED - V3.1 FINAL");

    let isPeriodOpen = false;
    let isMatrixLoaded = false;

    const apiOptions = {
        headers: {
            "Accept": "application/json",
            "Content-Type": "application/json",
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    };

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
        if (select) {
            const firstOption = select.options[0];
            select.innerHTML = "";
            select.appendChild(firstOption);
            if (data) data.forEach(item => {
                const option = document.createElement("option");
                option.value = item[valueField];
                option.textContent = item[textField];
                select.appendChild(option);
            });
        }
    }

    async function loadMasterData() {
        try {
            const [regions, channels, suppliers, categories, salesmen] = await Promise.all([
                fetch(`/api/v1/deps/regions`).then(res => res.json()),
                fetch(`/api/v1/deps/channels`).then(res => res.json()),
                fetch(`/api/v1/deps/suppliers`).then(res => res.json()),
                fetch(`/api/v1/deps/categories`).then(res => res.json()),
                fetch(`/api/v1/deps/salesmen`).then(res => res.json())
            ]);
            populateSelect("filter_region", regions.data, "id", "name");
            populateSelect("filter_channel", channels.data, "id", "name");
            populateSelect("filter_supplier", suppliers.data, "id", "name");
            populateSelect("filter_category", categories.data, "id", "name");
            populateSelect("filter_salesman", salesmen.data, "id", "name");
        } catch (error) {
            showAlert("Failed to load filter data.", "error");
        }
    }

    function getCurrentFilters() {
        const filters = {
            year: document.getElementById('target_year').value,
            month: document.getElementById('target_month').value,
            region_id: document.getElementById('filter_region').value,
            channel_id: document.getElementById('filter_channel').value,
            supplier_id: document.getElementById('filter_supplier').value,
            category_id: document.getElementById('filter_category').value,
            salesman_id: document.getElementById('filter_salesman').value,
            classification: document.getElementById('filter_classification').value
        };
        // Remove empty filters
        Object.keys(filters).forEach(key => (filters[key] === '') && delete filters[key]);
        return filters;
    }

    async function loadTargetMatrix() {
        const year = document.getElementById("target_year").value;
        const month = document.getElementById("target_month").value;
        if (!year || !month) {
            showAlert("Please select both Year and Month.", "warning");
            return;
        }

        // Reset matrix loaded state
        isMatrixLoaded = false;

        const loadBtn = document.getElementById("loadMatrixBtn");
        loadBtn.disabled = true;
        loadBtn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Loading...`;
        
        document.getElementById("matrix-loading").style.display = "block";
        document.getElementById("matrix-container").style.display = "none";

        const params = new URLSearchParams(getCurrentFilters());
        try {
            const response = await fetch(`/api/v1/targets/matrix?${params}`);
            if (!response.ok) throw new Error((await response.json()).message || 'Failed to load data.');
            
            const result = await response.json();
            isPeriodOpen = result.data.is_period_open;
            document.getElementById("saveAllBtn").style.display = isPeriodOpen ? 'flex' : 'none';
            renderMatrix(result.data);

        } catch (error) {
            showAlert(error.message, "error");
            document.getElementById("matrix-empty").style.display = "block";
        } finally {
            loadBtn.disabled = false;
            loadBtn.innerHTML = `<i class="bi bi-table me-2"></i>Load Matrix`;
            document.getElementById("matrix-loading").style.display = "none";
        }
    }

    function renderMatrix({ salesmen, suppliers, targets }) {
        const tbody = document.querySelector("#target-matrix tbody");
        tbody.innerHTML = "";
        
        if (salesmen.length === 0 || suppliers.length === 0) {
            document.getElementById("matrix-empty").style.display = "block";
            return;
        }

        const targetsMap = targets.reduce((map, t) => {
            map[`${t.salesman_id}-${t.supplier_id}-${t.category_id}`] = t.target_amount;
            return map;
        }, {});

        salesmen.forEach(s => {
            suppliers.forEach(sup => {
                if (isClassificationCompatible(s.salesman_classification, sup.supplier_classification)) {
                    const key = `${s.salesman_id}-${sup.supplier_id}-${sup.category_id}`;
                    tbody.innerHTML += `
                        <tr class="border-0" style="border-bottom: 1px solid #e9ecef !important;">
                            <td class="py-3 px-4 border-0">
                                <div class="d-flex align-items-center">
                                    <div class="p-1 rounded-circle bg-primary bg-opacity-10 me-2">
                                        <i class="bi bi-person text-primary" style="font-size: 0.8rem;"></i>
                                    </div>
                                    <span class="fw-medium">${s.salesman_name}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 border-0">
                                <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1">
                                    <i class="bi bi-geo-alt me-1"></i>${s.region_name || 'N/A'}
                                </span>
                            </td>
                            <td class="py-3 px-4 border-0">
                                <span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1">
                                    <i class="bi bi-diagram-3 me-1"></i>${s.channel_name || 'N/A'}
                                </span>
                            </td>
                            <td class="py-3 px-4 border-0">
                                <span class="text-dark fw-medium">${sup.supplier_name}</span>
                            </td>
                            <td class="py-3 px-4 border-0">
                                <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1">
                                    ${sup.category_name}
                                </span>
                            </td>
                            <td class="py-3 px-4 border-0 text-center">
                                <input type="number" 
                                       class="form-control form-control-sm border-0 shadow-sm text-center fw-bold" 
                                       style="border-radius: 8px; background-color: ${!isPeriodOpen ? '#f8f9fa' : '#fff'}; color: ${!isPeriodOpen ? '#6c757d' : '#28a745'};"
                                       value="${targetsMap[key] || ''}" 
                                       ${!isPeriodOpen ? 'disabled' : ''}
                                       placeholder="${!isPeriodOpen ? 'Closed' : 'Enter amount'}"
                                       data-salesman-id="${s.salesman_id}" 
                                       data-supplier-id="${sup.supplier_id}" 
                                       data-category-id="${sup.category_id}">
                            </td>
                        </tr>`;
                }
            });
        });
        document.getElementById("matrix-container").style.display = "block";
        isMatrixLoaded = true;
    }

    function isClassificationCompatible(salesmanClass, supplierClass) {
        return salesmanClass === 'both' || supplierClass === 'both' || salesmanClass === supplierClass;
    }

    async function saveAllTargets() {
        if (!isPeriodOpen) return;
        const targetsToSave = [];
        document.querySelectorAll("#target-matrix input").forEach(input => {
            if (input.value.trim() !== '') {
                targetsToSave.push({
                    salesman_id: input.dataset.salesmanId,
                    supplier_id: input.dataset.supplierId,
                    category_id: input.dataset.categoryId,
                    target_amount: parseFloat(input.value)
                });
            }
        });

        if (targetsToSave.length === 0) {
            showAlert("No targets to save.", "info");
            return;
        }

        const saveBtn = document.getElementById("saveAllBtn");
        saveBtn.disabled = true;
        saveBtn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Saving...`;

        try {
            const response = await fetch(`/api/targets/bulk-save`, {
                method: 'POST',
                headers: apiOptions.headers,
                body: JSON.stringify({
                    year: document.getElementById("target_year").value,
                    month: document.getElementById("target_month").value,
                    targets: targetsToSave
                })
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message);
            showAlert(`${result.saved_count} targets saved.`, "success");
        } catch (error) {
            showAlert(error.message, "error");
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = `<i class="bi bi-check-circle me-2"></i>Save All Targets`;
        }
    }
    
    async function exportTargets() {
        const year = document.getElementById("target_year").value;
        const month = document.getElementById("target_month").value;
        
        if (!year || !month) {
            showAlert("Please select Year and Month before exporting.", "warning");
            return;
        }

        if (!isMatrixLoaded) {
            showAlert("Please load the matrix first before exporting.", "warning");
            return;
        }

        const params = new URLSearchParams(getCurrentFilters());
        const exportBtn = document.querySelector('button[onclick="exportTargets()"]');
        const originalText = exportBtn.innerHTML;
        
        try {
            exportBtn.disabled = true;
            exportBtn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Exporting...`;
            
            const response = await fetch(`/api/v1/export/targets?${params}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const contentType = response.headers.get('content-type');
            
            if (!response.ok) {
                if (contentType && contentType.includes('application/json')) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Export failed');
                } else {
                    const textResponse = await response.text();
                    if (textResponse.includes('<')) {
                        throw new Error('Authentication required. Please refresh the page and try again.');
                    }
                    throw new Error('Export failed: ' + response.statusText);
                }
            }

            if (contentType && contentType.includes('text/html')) {
                throw new Error('Received HTML instead of CSV. Please try logging in again.');
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `targets_${year}_${month}.csv`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            
            showAlert("Export completed successfully", "success");
        } catch (error) {
            console.error('Export error:', error);
            showAlert("Failed to export targets: " + error.message, "error");
        } finally {
            exportBtn.disabled = false;
            exportBtn.innerHTML = originalText;
        }
    }

    function showUploadModal() {
        new bootstrap.Modal(document.getElementById('uploadModal')).show();
    }

    async function downloadTemplate() {
        const templateBtn = document.querySelector('button[onclick="downloadTemplate()"]');
        const originalText = templateBtn.innerHTML;
        
        try {
            templateBtn.disabled = true;
            templateBtn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Downloading...`;
            
            const response = await fetch('/api/v1/export/template', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Template download failed');
            }

            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('text/html')) {
                throw new Error('Received HTML instead of CSV. Please try logging in again.');
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `targets_template.csv`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            
            showAlert("Template downloaded successfully", "success");
        } catch (error) {
            console.error('Template download error:', error);
            showAlert("Failed to download template: " + error.message, "error");
        } finally {
            templateBtn.disabled = false;
            templateBtn.innerHTML = originalText;
        }
    }

    async function uploadTargets() {
        const form = document.getElementById('uploadForm');
        const fileInput = document.getElementById('upload_file');
        const year = document.getElementById("target_year").value;
        const month = document.getElementById("target_month").value;

        if (!year || !month) {
            showAlert("Please select Year and Month before uploading.", "warning");
            return;
        }

        if (!fileInput.files.length) {
            showAlert("Please select a file to upload.", "warning");
            return;
        }

        const formData = new FormData();
        formData.append('file', fileInput.files[0]);
        formData.append('year', year);
        formData.append('month', month);

        try {
            const response = await fetch('/api/v1/targets/upload', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });

            const result = await response.json();
            
            if (!response.ok) throw new Error(result.message || 'Upload failed');
            
            showAlert(`Upload completed: ${result.created} created, ${result.updated} updated`, "success");
            if (result.errors > 0) {
                console.error('Upload errors:', result.error_details);
                showAlert(`Warning: ${result.errors} errors occurred. Check console for details.`, "warning");
            }
            
            // Reset the file input
            fileInput.value = '';
            
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('uploadModal')).hide();
            
            // Wait a bit for the backend to finish processing
            await new Promise(resolve => setTimeout(resolve, 500));
            
            // Refresh the matrix with current filters
            await loadTargetMatrix();
            
            // Force a complete refresh of the data
            await loadMasterData();
            
        } catch (error) {
            showAlert(error.message, "error");
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        loadMasterData();
        
        // Add event listeners to reset matrix loaded state when filters change
        const filterElements = ['target_year', 'target_month', 'filter_region', 'filter_channel', 'filter_supplier', 'filter_category', 'filter_salesman', 'filter_classification'];
        
        filterElements.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('change', function() {
                    isMatrixLoaded = false;
                });
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\New target\target-system\resources\views/targets/index.blade.php ENDPATH**/ ?>