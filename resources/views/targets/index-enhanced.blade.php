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

    <!-- Enhanced Filters Panel -->
    <div class="filter-panel animate-on-scroll">
        <div class="p-4">
            <h5 class="fw-semibold mb-3 d-flex align-items-center">
                <i class="bi bi-funnel me-2 text-primary"></i>
                {{ __('Filters & Period Selection') }}
            </h5>
            
            <div class="row g-3">
                <!-- Period Selection -->
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label for="target_year" class="form-label-enhanced">{{ __('Year') }}</label>
                    <select class="form-select form-control-enhanced" id="target_year" name="year">
                        <option value="">{{ __('Select Year') }}</option>
                        @for($y = date('Y'); $y <= date('Y') + 2; $y++)
                            <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label for="target_month" class="form-label-enhanced">{{ __('Month') }}</label>
                    <select class="form-select form-control-enhanced" id="target_month" name="month">
                        <option value="">{{ __('Select Month') }}</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label-enhanced">&nbsp;</label>
                    <button class="btn btn-primary btn-enhanced w-100" id="loadMatrixBtn" onclick="loadTargetMatrix()">
                        <i class="bi bi-search me-2"></i>{{ __('Load Data') }}
                    </button>
                </div>
                
                <!-- Filters -->
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label for="filter_region" class="form-label-enhanced">{{ __('Region') }}</label>
                    <select class="form-select form-control-enhanced" id="filter_region" data-filter="region">
                        <option value="">{{ __('All Regions') }}</option>
                    </select>
                </div>
                
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label for="filter_channel" class="form-label-enhanced">{{ __('Channel') }}</label>
                    <select class="form-select form-control-enhanced" id="filter_channel" data-filter="channel">
                        <option value="">{{ __('All Channels') }}</option>
                    </select>
                </div>
                
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label for="filter_supplier" class="form-label-enhanced">{{ __('Supplier') }}</label>
                    <select class="form-select form-control-enhanced" id="filter_supplier" data-filter="supplier">
                        <option value="">{{ __('All Suppliers') }}</option>
                    </select>
                </div>
            </div>
            
            <div class="row g-3 mt-2">
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label for="filter_category" class="form-label-enhanced">{{ __('Category') }}</label>
                    <select class="form-select form-control-enhanced" id="filter_category" data-filter="category">
                        <option value="">{{ __('All Categories') }}</option>
                    </select>
                </div>
                
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label for="filter_salesman" class="form-label-enhanced">{{ __('Salesman') }}</label>
                    <select class="form-select form-control-enhanced" id="filter_salesman" data-filter="salesman">
                        <option value="">{{ __('All Salesmen') }}</option>
                    </select>
                </div>
                
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label for="search_input" class="form-label-enhanced">{{ __('Search') }}</label>
                    <input type="text" class="form-control form-control-enhanced" id="search_input" 
                           placeholder="{{ __('Search targets...') }}" data-filter data-filter-target=".matrix-table">
                </div>
                
                <div class="col-lg-6 col-md-6 col-sm-12 d-flex align-items-end">
                    <div class="btn-group w-100" role="group">
                        <button type="button" class="btn btn-outline-secondary btn-enhanced" onclick="clearFilters()">
                            <i class="bi bi-x-circle me-2"></i>{{ __('Clear Filters') }}
                        </button>
                        <button type="button" class="btn btn-outline-info btn-enhanced" onclick="refreshData()">
                            <i class="bi bi-arrow-clockwise me-2"></i>{{ __('Refresh') }}
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-enhanced" onclick="toggleViewMode()">
                            <i class="bi bi-grid-3x3-gap me-2"></i>{{ __('Toggle View') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Matrix Container -->
    <div class="matrix-container animate-on-scroll" id="matrixContainer">
        <div class="loading-container" id="loadingContainer">
            <div class="text-center">
                <div class="loading-spinner mb-3"></div>
                <p class="text-muted">{{ __('Loading targets data...') }}</p>
            </div>
        </div>
        
        <div id="matrixContent" style="display: none;">
            <div class="table-responsive">
                <table class="matrix-table" id="targetsTable">
                    <thead>
                        <tr>
                            <th rowspan="2">{{ __('Region') }}</th>
                            <th rowspan="2">{{ __('Channel') }}</th>
                            <th rowspan="2">{{ __('Salesman') }}</th>
                            <th rowspan="2">{{ __('Supplier') }}</th>
                            <th rowspan="2">{{ __('Category') }}</th>
                            <th colspan="4">{{ __('Targets') }}</th>
                            <th rowspan="2">{{ __('Actions') }}</th>
                        </tr>
                        <tr>
                            <th>{{ __('Q1') }}</th>
                            <th>{{ __('Q2') }}</th>
                            <th>{{ __('Q3') }}</th>
                            <th>{{ __('Q4') }}</th>
                        </tr>
                    </thead>
                    <tbody id="matrixBody">
                        <!-- Data will be populated via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-upload me-2"></i>{{ __('Upload Targets') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="excelFile" class="form-label-enhanced">{{ __('Select Excel File') }}</label>
                        <input type="file" class="form-control form-control-enhanced" id="excelFile" 
                               name="file" accept=".xlsx,.xls" required>
                        <div class="form-text">{{ __('Supported formats: .xlsx, .xls') }}</div>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        {{ __('Make sure your Excel file matches the template format. Download the template first if needed.') }}
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary btn-enhanced" onclick="uploadTargets()">
                    <i class="bi bi-upload me-2"></i>{{ __('Upload') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Enhanced Target Management with Performance Optimizations
class EnhancedTargetMatrix {
    constructor() {
        this.currentData = [];
        this.filteredData = [];
        this.unsavedChanges = new Set();
        this.debounceTimers = new Map();
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.loadInitialData();
        this.setupAutoSave();
    }
    
    setupEventListeners() {
        // Filter change handlers with debouncing
        const filters = document.querySelectorAll('[data-filter]');
        filters.forEach(filter => {
            filter.addEventListener('change', () => this.debounceFilter(filter));
            filter.addEventListener('input', () => this.debounceFilter(filter));
        });
        
        // Save button handler
        document.getElementById('saveAllBtn')?.addEventListener('click', () => this.saveAllTargets());
        
        // Load button handler
        document.getElementById('loadMatrixBtn')?.addEventListener('click', () => this.loadTargetMatrix());
        
        // Window beforeunload handler for unsaved changes
        window.addEventListener('beforeunload', (e) => {
            if (this.unsavedChanges.size > 0) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    }
    
    debounceFilter(element) {
        const key = element.id || element.dataset.filter;
        clearTimeout(this.debounceTimers.get(key));
        
        this.debounceTimers.set(key, setTimeout(() => {
            this.applyFilters();
        }, 300));
    }
    
    async loadInitialData() {
        try {
            await this.loadDropdownData();
            // Auto-load current month data
            await this.loadTargetMatrix();
        } catch (error) {
            window.targetApp?.showToast('Failed to load initial data', 'danger');
        }
    }
    
    async loadDropdownData() {
        const dropdowns = ['regions', 'channels', 'suppliers', 'categories', 'salesmen'];
        
        for (const dropdown of dropdowns) {
            try {
                const response = await fetch(`/api/${dropdown}`);
                const data = await response.json();
                this.populateDropdown(`filter_${dropdown.slice(0, -1)}`, data);
            } catch (error) {
                console.error(`Failed to load ${dropdown}:`, error);
            }
        }
    }
    
    populateDropdown(selectId, data) {
        const select = document.getElementById(selectId);
        if (!select) return;
        
        // Clear existing options except the first one
        while (select.children.length > 1) {
            select.removeChild(select.lastChild);
        }
        
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.name;
            select.appendChild(option);
        });
    }
    
    async loadTargetMatrix() {
        const year = document.getElementById('target_year')?.value;
        const month = document.getElementById('target_month')?.value;
        
        if (!year || !month) {
            window.targetApp?.showToast('Please select year and month', 'warning');
            return;
        }
        
        const loadingContainer = document.getElementById('loadingContainer');
        const matrixContent = document.getElementById('matrixContent');
        
        // Show loading state
        loadingContainer.style.display = 'flex';
        matrixContent.style.display = 'none';
        
        try {
            const response = await fetch(`/api/targets?year=${year}&month=${month}`);
            const data = await response.json();
            
            this.currentData = data;
            this.filteredData = [...data];
            this.renderMatrix();
            this.updateStatistics();
            
            // Hide loading and show content
            loadingContainer.style.display = 'none';
            matrixContent.style.display = 'block';
            
            window.targetApp?.showToast('Data loaded successfully', 'success');
        } catch (error) {
            loadingContainer.style.display = 'none';
            window.targetApp?.showToast('Failed to load matrix data', 'danger');
        }
    }
    
    renderMatrix() {
        const tbody = document.getElementById('matrixBody');
        if (!tbody) return;
        
        tbody.innerHTML = '';
        
        this.filteredData.forEach((row, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td data-label="Region">${row.region_name || 'N/A'}</td>
                <td data-label="Channel">${row.channel_name || 'N/A'}</td>
                <td data-label="Salesman">${row.salesman_name || 'N/A'}</td>
                <td data-label="Supplier">${row.supplier_name || 'N/A'}</td>
                <td data-label="Category">${row.category_name || 'N/A'}</td>
                <td data-label="Q1">
                    <input type="number" class="target-input" 
                           data-row="${index}" data-quarter="q1"
                           value="${row.q1_target || ''}" 
                           placeholder="0">
                </td>
                <td data-label="Q2">
                    <input type="number" class="target-input" 
                           data-row="${index}" data-quarter="q2"
                           value="${row.q2_target || ''}" 
                           placeholder="0">
                </td>
                <td data-label="Q3">
                    <input type="number" class="target-input" 
                           data-row="${index}" data-quarter="q3"
                           value="${row.q3_target || ''}" 
                           placeholder="0">
                </td>
                <td data-label="Q4">
                    <input type="number" class="target-input" 
                           data-row="${index}" data-quarter="q4"
                           value="${row.q4_target || ''}" 
                           placeholder="0">
                </td>
                <td data-label="Actions">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary btn-sm" onclick="targetMatrix.saveRow(${index})">
                            <i class="bi bi-check"></i>
                        </button>
                        <button class="btn btn-outline-danger btn-sm" onclick="targetMatrix.resetRow(${index})">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });
        
        // Setup input event listeners
        this.setupInputListeners();
    }
    
    setupInputListeners() {
        const inputs = document.querySelectorAll('.target-input');
        inputs.forEach(input => {
            input.addEventListener('input', (e) => this.handleTargetChange(e));
            input.addEventListener('blur', (e) => this.handleTargetBlur(e));
        });
    }
    
    handleTargetChange(event) {
        const input = event.target;
        const row = input.closest('tr');
        const rowIndex = parseInt(input.dataset.row);
        
        // Mark as modified
        row.classList.add('modified');
        input.classList.add('modified');
        this.unsavedChanges.add(rowIndex);
        
        // Update the data
        const quarter = input.dataset.quarter;
        this.filteredData[rowIndex][`${quarter}_target`] = input.value;
        
        // Update statistics
        this.updateStatistics();
        
        // Show save indicator
        this.updateSaveButton();
    }
    
    handleTargetBlur(event) {
        const input = event.target;
        const rowIndex = parseInt(input.dataset.row);
        
        // Auto-save after delay
        setTimeout(() => {
            if (this.unsavedChanges.has(rowIndex)) {
                this.autoSaveRow(rowIndex);
            }
        }, 2000);
    }
    
    async autoSaveRow(rowIndex) {
        try {
            const data = this.filteredData[rowIndex];
            const response = await fetch('/api/targets/save-row', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });
            
            if (response.ok) {
                this.unsavedChanges.delete(rowIndex);
                const row = document.querySelector(`[data-row="${rowIndex}"]`)?.closest('tr');
                if (row) {
                    row.classList.remove('modified');
                    row.querySelectorAll('.target-input').forEach(input => {
                        input.classList.remove('modified');
                    });
                }
                this.updateSaveButton();
            }
        } catch (error) {
            console.error('Auto-save failed:', error);
        }
    }
    
    updateSaveButton() {
        const saveBtn = document.getElementById('saveAllBtn');
        if (!saveBtn) return;
        
        if (this.unsavedChanges.size > 0) {
            saveBtn.innerHTML = `<i class="bi bi-exclamation-circle me-2"></i>Save ${this.unsavedChanges.size} Changes`;
            saveBtn.classList.remove('btn-success');
            saveBtn.classList.add('btn-warning');
        } else {
            saveBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>All Saved';
            saveBtn.classList.remove('btn-warning');
            saveBtn.classList.add('btn-success');
        }
    }
    
    updateStatistics() {
        const stats = {
            total: this.filteredData.length,
            completed: 0,
            pending: 0,
            totalValue: 0
        };
        
        this.filteredData.forEach(row => {
            const hasTargets = row.q1_target || row.q2_target || row.q3_target || row.q4_target;
            if (hasTargets) {
                stats.completed++;
                stats.totalValue += (parseFloat(row.q1_target) || 0) +
                                   (parseFloat(row.q2_target) || 0) +
                                   (parseFloat(row.q3_target) || 0) +
                                   (parseFloat(row.q4_target) || 0);
            } else {
                stats.pending++;
            }
        });
        
        // Update DOM
        document.getElementById('totalTargets').textContent = stats.total.toLocaleString();
        document.getElementById('completedTargets').textContent = stats.completed.toLocaleString();
        document.getElementById('pendingTargets').textContent = stats.pending.toLocaleString();
        document.getElementById('totalValue').textContent = new Intl.NumberFormat().format(stats.totalValue);
    }
    
    applyFilters() {
        const filters = {
            region: document.getElementById('filter_region')?.value,
            channel: document.getElementById('filter_channel')?.value,
            supplier: document.getElementById('filter_supplier')?.value,
            category: document.getElementById('filter_category')?.value,
            salesman: document.getElementById('filter_salesman')?.value,
            search: document.getElementById('search_input')?.value?.toLowerCase()
        };
        
        this.filteredData = this.currentData.filter(row => {
            return Object.entries(filters).every(([key, value]) => {
                if (!value) return true;
                
                if (key === 'search') {
                    return Object.values(row).some(val => 
                        String(val).toLowerCase().includes(value)
                    );
                }
                
                return String(row[`${key}_id`]) === value;
            });
        });
        
        this.renderMatrix();
        this.updateStatistics();
    }
    
    async saveAllTargets() {
        if (this.unsavedChanges.size === 0) {
            window.targetApp?.showToast('No changes to save', 'info');
            return;
        }
        
        const saveBtn = document.getElementById('saveAllBtn');
        window.targetApp?.showButtonLoading(saveBtn);
        
        try {
            const response = await fetch('/api/targets/save-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    targets: this.filteredData.filter((_, index) => this.unsavedChanges.has(index))
                })
            });
            
            if (response.ok) {
                this.unsavedChanges.clear();
                document.querySelectorAll('.modified').forEach(el => {
                    el.classList.remove('modified');
                });
                this.updateSaveButton();
                window.targetApp?.showToast('All targets saved successfully', 'success');
            } else {
                throw new Error('Save failed');
            }
        } catch (error) {
            window.targetApp?.showToast('Failed to save targets', 'danger');
        } finally {
            window.targetApp?.hideButtonLoading(saveBtn);
        }
    }
}

// Global functions for backward compatibility
function loadTargetMatrix() {
    window.targetMatrix?.loadTargetMatrix();
}

function saveAllTargets() {
    window.targetMatrix?.saveAllTargets();
}

function exportTargets() {
    const year = document.getElementById('target_year')?.value;
    const month = document.getElementById('target_month')?.value;
    
    if (!year || !month) {
        window.targetApp?.showToast('Please select year and month first', 'warning');
        return;
    }
    
    window.location.href = `/api/targets/export?year=${year}&month=${month}`;
}

function showUploadModal() {
    const modal = new bootstrap.Modal(document.getElementById('uploadModal'));
    modal.show();
}

function downloadTemplate() {
    window.location.href = '/api/targets/template';
}

function clearFilters() {
    document.querySelectorAll('[data-filter]').forEach(element => {
        element.value = '';
    });
    window.targetMatrix?.applyFilters();
}

function refreshData() {
    window.targetMatrix?.loadTargetMatrix();
}

function toggleViewMode() {
    // Toggle between table and card view
    const table = document.querySelector('.matrix-table');
    table.classList.toggle('card-view');
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', () => {
    window.targetMatrix = new EnhancedTargetMatrix();
});
</script>
@endpush