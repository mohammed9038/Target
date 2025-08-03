@extends('layouts.app')

@section('title', __('Sales Targets'))

@push('styles')
<style>
    /* Page-specific optimizations */
    .targets-page {
        animation: fadeIn 0.5s ease-out;
    }
    
    .filter-panel {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        margin-bottom: 2rem;
        overflow: hidden;
    }
    
    .matrix-container {
        position: relative;
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    }
    
    .matrix-table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    
    .matrix-table thead th {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        color: white;
        font-weight: 600;
        text-align: center;
        padding: 1rem 0.5rem;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border: none;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .matrix-table tbody td {
        padding: 0.75rem 0.5rem;
        border-bottom: 1px solid #f1f5f9;
        border-right: 1px solid #f1f5f9;
        text-align: center;
        font-size: 0.875rem;
        transition: background-color 0.2s ease;
    }
    
    .matrix-table tbody tr:hover {
        background-color: #f8fafc;
    }
    
    .matrix-table tbody tr.modified {
        background-color: #fef3c7;
        border-left: 4px solid #f59e0b;
    }
    
    .target-input {
        width: 80px;
        padding: 0.375rem 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        text-align: center;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
        background: white;
    }
    
    .target-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgb(59 130 246 / 0.1);
        outline: none;
        transform: scale(1.05);
    }
    
    .target-input.modified {
        border-color: #f59e0b;
        background-color: #fefbf2;
    }
    
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        align-items: center;
    }
    
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px 0 rgb(0 0 0 / 0.15);
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .stat-label {
        color: #6b7280;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    /* Loading states */
    .loading-container {
        position: relative;
        min-height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .loading-spinner {
        display: inline-block;
        width: 2rem;
        height: 2rem;
        border: 3px solid #e2e8f0;
        border-top: 3px solid #3b82f6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    /* Responsive design */
    @media (max-width: 768px) {
        .action-buttons {
            flex-direction: column;
            width: 100%;
        }
        
        .action-buttons .btn {
            width: 100%;
            justify-content: center;
        }
        
        .matrix-table {
            font-size: 0.75rem;
        }
        
        .target-input {
            width: 60px;
            font-size: 0.75rem;
        }
        
        .stat-card {
            text-align: center;
        }
    }
    
    @media (max-width: 576px) {
        .matrix-container {
            margin: 0 -1rem;
            border-radius: 0;
        }
        
        .filter-panel {
            margin: 0 -1rem 1rem;
            border-radius: 0;
        }
    }
</style>
@endpush

@section('content')
<div class="targets-page">
    <!-- Toast Container -->
    <div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1200;"></div>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div class="animate-on-scroll">
            <h1 class="h3 fw-bold text-gradient mb-2 d-flex align-items-center">
                <div class="p-2 rounded-circle bg-primary bg-opacity-10 me-3">
                    <i class="bi bi-bullseye text-primary fs-4"></i>
                </div>
                {{ __('Sales Targets Management') }}
            </h1>
            <p class="text-muted mb-0 ms-5">
                {{ __('Set and manage sales targets for your team members with real-time updates') }}
            </p>
        </div>
        
        <div class="action-buttons animate-on-scroll">
            <button type="button" class="btn btn-success btn-enhanced" onclick="saveAllTargets()" id="saveAllBtn">
                <i class="bi bi-check-circle me-2"></i>{{ __('Save All Changes') }}
            </button>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary btn-enhanced" onclick="exportTargets()">
                    <i class="bi bi-file-earmark-spreadsheet me-2"></i>{{ __('Export') }}
                </button>
                <button type="button" class="btn btn-outline-primary btn-enhanced" onclick="showUploadModal()">
                    <i class="bi bi-upload me-2"></i>{{ __('Import') }}
                </button>
                <button type="button" class="btn btn-outline-secondary btn-enhanced" onclick="downloadTemplate()">
                    <i class="bi bi-download me-2"></i>{{ __('Template') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4 animate-on-scroll">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-value text-primary" id="totalTargets">-</div>
                <div class="stat-label">{{ __('Total Targets') }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-value text-success" id="completedTargets">-</div>
                <div class="stat-label">{{ __('Completed') }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-value text-warning" id="pendingTargets">-</div>
                <div class="stat-label">{{ __('Pending') }}</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-value text-info" id="totalValue">-</div>
                <div class="stat-label">{{ __('Total Value') }}</div>
            </div>
        </div>
    </div>

<!-- Compact Filters Panel -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body p-3">
        <!-- Period and Filters in One Row -->
        <div class="row g-2 align-items-end">
            <div class="col-lg-1 col-md-2">
                <label for="target_year" class="form-label small text-muted mb-1">{{ __('Year') }}</label>
                <select class="form-select form-select-sm" id="target_year" name="year">
                    <option value="">{{ __('Year') }}</option>
                    @for($y = date('Y'); $y <= date('Y') + 2; $y++)
                        <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-lg-1 col-md-2">
                <label for="target_month" class="form-label small text-muted mb-1">{{ __('Month') }}</label>
                <select class="form-select form-select-sm" id="target_month" name="month">
                    <option value="">{{ __('Month') }}</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                            {{ date('M', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-lg-1 col-md-2">
                <button class="btn btn-primary btn-sm w-100" id="loadMatrixBtn" onclick="loadTargetMatrix()">
                    <i class="bi bi-table me-1"></i>{{ __('Load') }}
                </button>
            </div>
            <div class="col-lg-1 col-md-2">
                <label for="filter_classification" class="form-label small text-muted mb-1">{{ __('Type') }}</label>
                <select class="form-select form-select-sm" id="filter_classification">
                    <option value="">{{ __('All') }}</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-2">
                <label for="filter_region" class="form-label small text-muted mb-1">{{ __('Region') }}</label>
                <select class="form-select form-select-sm" id="filter_region">
                    <option value="">{{ __('All Regions') }}</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-2">
                <label for="filter_channel" class="form-label small text-muted mb-1">{{ __('Channel') }}</label>
                <select class="form-select form-select-sm" id="filter_channel">
                    <option value="">{{ __('All Channels') }}</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-2">
                <label for="filter_supplier" class="form-label small text-muted mb-1">{{ __('Supplier') }}</label>
                <select class="form-select form-select-sm" id="filter_supplier">
                    <option value="">{{ __('All Suppliers') }}</option>
                </select>
            </div>
            <div class="col-lg-1 col-md-2">
                <label for="filter_category" class="form-label small text-muted mb-1">{{ __('Category') }}</label>
                <select class="form-select form-select-sm" id="filter_category">
                    <option value="">{{ __('All') }}</option>
                </select>
            </div>
            <div class="col-lg-1 col-md-2">
                <label for="filter_salesman" class="form-label small text-muted mb-1">{{ __('Salesman') }}</label>
                <select class="form-select form-select-sm" id="filter_salesman">
                    <option value="">{{ __('All') }}</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Compact Target Matrix -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
        <h6 class="card-title mb-0 fw-semibold d-flex align-items-center">
            <i class="bi bi-grid-3x3-gap text-success me-2"></i>
            {{ __('Target Matrix') }}
        </h6>
        <small class="text-muted">
            <i class="bi bi-info-circle me-1"></i>{{ __('Enter target amounts') }}
        </small>
    </div>
    <div class="card-body p-0">
        <!-- Loading State -->
        <div id="matrix-loading" class="text-center py-3" style="display: none;">
            <div class="spinner-border text-primary mb-2" role="status">
                <span class="visually-hidden">{{ __('Loading...') }}</span>
            </div>
            <small class="text-muted">{{ __('Loading target matrix...') }}</small>
        </div>
        
        <!-- Matrix Container -->
        <div id="matrix-container" style="display: none;">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0" id="target-matrix" style="font-size: 0.90rem;">
                    <thead class="table-dark">
                        <tr>
                            <th class="py-2 px-3 small">
                                <i class="bi bi-person-badge me-1"></i>{{ __('Salesman') }}
                            </th>
                            <th class="py-2 px-3 small">
                                <i class="bi bi-geo-alt me-1"></i>{{ __('Region') }}
                            </th>
                            <th class="py-2 px-3 small">
                                <i class="bi bi-diagram-3 me-1"></i>{{ __('Channel') }}
                            </th>
                            <th class="py-2 px-3 small">
                                <i class="bi bi-building me-1"></i>{{ __('Supplier') }}
                            </th>
                            <th class="py-2 px-3 small">
                                <i class="bi bi-tags me-1"></i>{{ __('Category') }}
                            </th>
                            <th class="py-2 px-3 small">
                                <i class="bi bi-diagram-2 me-1"></i>{{ __('Type') }}
                            </th>
                            <th class="py-2 px-3 small text-center">
                                <i class="bi bi-currency-dollar me-1"></i>{{ __('Amount') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        
        <!-- Empty State -->
        <div id="matrix-empty" class="text-center py-4">
            <div class="mb-3">
                <i class="bi bi-table text-muted" style="font-size: 2rem;"></i>
            </div>
            <h6 class="text-dark mb-2">{{ __('No Data Available') }}</h6>
            <p class="text-muted small mb-3">{{ __('Please select year, month and click "Load Matrix" to view targets.') }}</p>
            <div class="d-flex justify-content-center gap-1 flex-wrap">
                <small class="badge bg-primary bg-opacity-10 text-primary px-2 py-1">
                    <i class="bi bi-1-circle me-1"></i>{{ __('Select Period') }}
                </small>
                <small class="badge bg-success bg-opacity-10 text-success px-2 py-1">
                    <i class="bi bi-2-circle me-1"></i>{{ __('Apply Filters') }}
                </small>
                <small class="badge bg-info bg-opacity-10 text-info px-2 py-1">
                    <i class="bi bi-3-circle me-1"></i>{{ __('Load Matrix') }}
                </small>
            </div>
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

    // Helper functions for classification display
    function getClassificationLabel(classification) {
        switch(classification) {
            case 'food': return 'Food';
            case 'non_food': return 'Non-Food';
            case 'both': return 'Both';
            default: return classification || 'N/A';
        }
    }

    function getClassificationBadgeClass(classification) {
        switch(classification) {
            case 'food': return 'bg-success bg-opacity-10 text-success';
            case 'non_food': return 'bg-info bg-opacity-10 text-info';
            case 'both': return 'bg-secondary bg-opacity-10 text-secondary';
            default: return 'bg-light text-muted';
        }
    }

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
            
            // Auto-set user's classification filter based on their permissions
            await setUserClassificationFilter(regions.data, channels.data, suppliers.data);
            
        } catch (error) {
            showAlert("Failed to load filter data.", "error");
        }
    }

    async function setUserClassificationFilter(regions, channels, suppliers) {
        try {
            // Get user info from API
            const userResponse = await fetch('/api/v1/user/info');
            if (!userResponse.ok) return;
            
            const userData = await userResponse.json();
            const user = userData.data;
            
            // Populate classification dropdown based on user permissions
            const classificationSelect = document.getElementById('filter_classification');
            
            if (user.is_admin) {
                // Admin can see all classifications
                classificationSelect.innerHTML = `
                    <option value="">All Types</option>
                    <option value="food">Food</option>
                    <option value="non_food">Non-Food</option>
                `;
            } else if (user.classifications && user.classifications.length > 0) {
                // Manager can only see their assigned classifications
                let optionsHtml = '<option value="">All Types</option>';
                
                user.classifications.forEach(classification => {
                    const label = classification === 'food' ? 'Food' : 'Non-Food';
                    optionsHtml += `<option value="${classification}">${label}</option>`;
                });
                
                classificationSelect.innerHTML = optionsHtml;
                
                // If user has only one classification, auto-select it
                if (user.classifications.length === 1) {
                    classificationSelect.value = user.classifications[0];
                }
            }
            
            // Auto-select region and channel if user has only one option
            if (!user.is_admin) {
                if (regions.length === 1) {
                    document.getElementById('filter_region').value = regions[0].id;
                }
                if (channels.length === 1) {
                    document.getElementById('filter_channel').value = channels[0].id;
                }
                
                console.log(`Auto-applied user filters - Classifications: ${user.classifications?.join(', ')}, Regions: ${regions.length}, Channels: ${channels.length}`);
            }
        } catch (error) {
            console.log('Could not auto-set user filters:', error);
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
        document.getElementById("matrix-empty").style.display = "none";

        const params = new URLSearchParams(getCurrentFilters());
        try {
            console.log('Loading matrix with params:', params.toString());
            const response = await fetch(`/api/v1/targets/matrix?${params}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Error response:', errorText);
                throw new Error('Failed to load data: ' + response.status);
            }
            
            const result = await response.json();
            console.log('Matrix data received:', result);
            
            if (!result.data) {
                throw new Error('Invalid response format - missing data field');
            }
            
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
        console.log('Rendering matrix with:', { 
            salesmenCount: salesmen?.length || 0, 
            suppliersCount: suppliers?.length || 0, 
            targetsCount: targets?.length || 0 
        });
        

        
        const tbody = document.querySelector("#target-matrix tbody");
        tbody.innerHTML = "";
        
        if (!salesmen || !suppliers || salesmen.length === 0 || suppliers.length === 0) {
            console.log('No data to display - showing empty state');
            document.getElementById("matrix-empty").style.display = "block";
            return;
        }



        const targetsMap = targets.reduce((map, t) => {
            map[`${t.salesman_id}-${t.supplier_id}-${t.category_id}`] = t.target_amount;
            return map;
        }, {});

        let rowCount = 0;
        salesmen.forEach(s => {
            suppliers.forEach(sup => {
                const compatible = isClassificationCompatible(s.salesman_classifications, sup.supplier_classification);
                if (compatible) {
                    rowCount++;
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
                                <span class="text-dark fw-medium">
                                    <i class="bi bi-geo-alt me-1"></i>${s.region_name || 'N/A'}
                                </span>
                            </td>
                            <td class="py-3 px-4 border-0">
                                <span class="text-dark fw-medium">
                                    <i class="bi bi-diagram-3 me-1"></i>${s.channel_name || 'N/A'}
                                </span>
                            </td>
                            <td class="py-3 px-4 border-0">
                                <span class="text-dark fw-medium">${sup.supplier_name}</span>
                            </td>
                            <td class="py-3 px-4 border-0">
                                <span class="text-dark fw-medium">
                                    ${sup.category_name}
                                </span>
                            </td>
                            <td class="py-3 px-4 border-0">
                                <span class="badge ${getClassificationBadgeClass(sup.supplier_classification)} px-2 py-1">
                                    <i class="bi bi-diagram-2 me-1"></i>${getClassificationLabel(sup.supplier_classification)}
                                </span>
                            </td>
                            <td class="py-3 px-4 border-0 text-center">
                                <input type="number" 
                                       class="form-control form-control-sm border shadow-sm text-center fw-bold" 
                                       style="border-radius: 8px; background-color: ${!isPeriodOpen ? '#f3f4f6' : '#ffffff'}; color: ${!isPeriodOpen ? '#6b7280' : '#111827'}; border-color: ${!isPeriodOpen ? '#d1d5db' : '#059669'};"
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
        

        
        // Hide empty state and show matrix
        document.getElementById("matrix-empty").style.display = "none";
        document.getElementById("matrix-container").style.display = "block";
        isMatrixLoaded = true;
    }

    function isClassificationCompatible(salesmanClassifications, supplierClass) {
        // salesmanClassifications is now an array, supplierClass is a single value
        if (!Array.isArray(salesmanClassifications)) {
            return false;
        }
        // Check if any of the salesman's classifications match the supplier's classification
        return salesmanClassifications.includes(supplierClass);
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
            const response = await fetch(`/api/v1/targets/bulk-save`, {
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
        // Test authentication on page load
        console.log('Page loaded. Testing authentication...');
        fetch('/api/v1/test-auth', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            console.log('Auth test result:', data);
        })
        .catch(error => {
            console.error('Auth test failed:', error);
        });
        
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
