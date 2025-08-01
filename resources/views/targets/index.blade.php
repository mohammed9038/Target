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
                    <option value="">{{ __('All') }}</option>
                    <option value="food">{{ __('Food') }}</option>
                    <option value="non_food">{{ __('Non Food') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="filter_region" class="form-label">{{ __('Region') }}</label>
                <select class="form-select" id="filter_region">
                    <option value="">{{ __('All') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="filter_channel" class="form-label">{{ __('Channel') }}</label>
                <select class="form-select" id="filter_channel">
                    <option value="">{{ __('All') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="filter_supplier" class="form-label">{{ __('Supplier') }}</label>
                <select class="form-select" id="filter_supplier">
                    <option value="">{{ __('All') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="filter_category" class="form-label">{{ __('Category') }}</label>
                <select class="form-select" id="filter_category">
                    <option value="">{{ __('All') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="filter_salesman" class="form-label">{{ __('Salesman') }}</label>
                <select class="form-select" id="filter_salesman">
                    <option value="">{{ __('All') }}</option>
                </select>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <button type="button" class="btn btn-primary w-100" onclick="loadTargetMatrix()" id="loadMatrixBtn">
                    <i class="bi bi-table me-2"></i>{{ __('Load Matrix') }}
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
            <p class="text-muted mt-2 mb-0">{{ __('No data available. Please select filters and click Load Matrix.') }}</p>
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
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="uploadTargets()">{{ __('Upload') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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
                        <tr>
                            <td>${s.salesman_name}</td>
                            <td>${s.region_name}</td>
                            <td>${s.channel_name}</td>
                            <td>${sup.supplier_name}</td>
                            <td>${sup.category_name}</td>
                            <td><input type="number" class="form-control form-control-sm" 
                                       value="${targetsMap[key] || ''}" ${!isPeriodOpen ? 'disabled' : ''}
                                       data-salesman-id="${s.salesman_id}" data-supplier-id="${sup.supplier_id}" 
                                       data-category-id="${sup.category_id}"></td>
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
@endpush
