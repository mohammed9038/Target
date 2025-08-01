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
@endsection

@push('scripts')
<script>
    console.log("🎯 TARGET PAGE SCRIPT LOADED - V3.0 FINAL");

    let isPeriodOpen = false;

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
        if (!select) return;
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

    async function loadMasterData() {
        try {
            const [regions, channels, suppliers, categories, salesmen] = await Promise.all([
                fetch(`/api/deps/regions`).then(res => res.json()),
                fetch(`/api/deps/channels`).then(res => res.json()),
                fetch(`/api/deps/suppliers`).then(res => res.json()),
                fetch(`/api/deps/categories`).then(res => res.json()),
                fetch(`/api/deps/salesmen`).then(res => res.json())
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
        const filters = {};
        ['year', 'month', 'region_id', 'channel_id', 'supplier_id', 'category_id', 'salesman_id', 'classification'].forEach(id => {
            const key = id.includes('_') ? id : `filter_${id}`;
            const element = document.getElementById(key.replace('filter_year', 'target_year').replace('filter_month', 'target_month'));
            if (element && element.value) filters[id] = element.value;
        });
        return filters;
    }

    async function loadTargetMatrix() {
        const year = document.getElementById("target_year").value;
        const month = document.getElementById("target_month").value;

        if (!year || !month) {
            showAlert("Please select both Year and Month.", "warning");
            return;
        }

        const loadBtn = document.getElementById("loadMatrixBtn");
        loadBtn.disabled = true;
        loadBtn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Loading...`;
        
        document.getElementById("matrix-loading").style.display = "block";
        document.getElementById("matrix-container").style.display = "none";
        document.getElementById("matrix-empty").style.display = "none";

        const params = new URLSearchParams(getCurrentFilters());
        try {
            const response = await fetch(`/api/v1/targets/matrix?${params}`);
            const result = await response.json();
            
            if (!response.ok) throw new Error(result.message || 'Failed to load data.');

            isPeriodOpen = result.data.is_period_open;
            document.getElementById("saveAllBtn").style.display = isPeriodOpen ? 'block' : 'none';
            renderMatrix(result.data);
            showAlert("Matrix loaded successfully.", "success");

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

        salesmen.forEach(salesman => {
            suppliers.forEach(supplier => {
                if (isClassificationCompatible(salesman.salesman_classification, supplier.supplier_classification)) {
                    const tr = document.createElement("tr");
                    const targetKey = `${salesman.salesman_id}-${supplier.supplier_id}-${supplier.category_id}`;
                    const targetAmount = targetsMap[targetKey] || "";
                    tr.innerHTML = `
                        <td>${salesman.salesman_name}</td>
                        <td>${salesman.region_name}</td>
                        <td>${salesman.channel_name}</td>
                        <td>${supplier.supplier_name}</td>
                        <td>${supplier.category_name}</td>
                        <td><input type="number" class="form-control form-control-sm" value="${targetAmount}" 
                                   ${!isPeriodOpen ? 'disabled' : ''}
                                   data-salesman-id="${salesman.salesman_id}"
                                   data-supplier-id="${supplier.supplier_id}"
                                   data-category-id="${supplier.category_id}"></td>
                    `;
                    tbody.appendChild(tr);
                }
            });
        });
        document.getElementById("matrix-container").style.display = "block";
    }

    function isClassificationCompatible(salesmanClass, supplierClass) {
        return salesmanClass === 'both' || supplierClass === 'both' || salesmanClass === supplierClass;
    }

    async function saveAllTargets() {
        if (!isPeriodOpen) {
            showAlert("This period is closed for editing.", "warning");
            return;
        }

        const targetsToSave = [];
        document.querySelectorAll("#target-matrix input").forEach(input => {
            if (input.value.trim() !== '') {
                targetsToSave.push({
                    salesman_id: input.dataset.salesmanId,
                    supplier_id: input.dataset.supplierId,
                    category_id: input.dataset.categoryId,
                    target_amount: parseFloat(input.value) || 0
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
            if (!response.ok) throw new Error(result.message || 'Failed to save targets.');
            showAlert(`${result.saved_count} targets saved successfully.`, "success");
        } catch (error) {
            showAlert(error.message, "error");
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = `<i class="bi bi-check-circle me-2"></i>Save All Targets`;
        }
    }
    
    document.addEventListener("DOMContentLoaded", loadMasterData);

</script>
@endpush
