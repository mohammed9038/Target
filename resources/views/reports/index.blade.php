@extends('layouts.app')

@section('title', __('Reports'))

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="h2 mb-1">{{ __('Reports') }}</h1>
        <p class="text-muted mb-0">{{ __('View and analyze sales target reports') }}</p>
    </div>
    <div class="d-flex gap-2" style="margin-top: 0.5rem;">
        <span class="badge bg-info-subtle text-info px-3 py-2">
            <i class="bi bi-graph-up me-1"></i>{{ __('Analytics Dashboard') }}
        </span>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-funnel me-2"></i>{{ __('Report Filters') }}
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <!-- Period Filters -->
            <div class="col-md-2">
                <label for="yearFilter" class="form-label small fw-medium">
                    <i class="bi bi-calendar-date me-1"></i>{{ __('Year') }}
                </label>
                <select class="form-select form-select-sm" id="yearFilter">
                    <option value="">{{ __('All Years') }}</option>
                    @for($y = date('Y'); $y >= date('Y') - 2; $y--)
                        <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label for="monthFilter" class="form-label small fw-medium">
                    <i class="bi bi-calendar-month me-1"></i>{{ __('Month') }}
                </label>
                <select class="form-select form-select-sm" id="monthFilter">
                    <option value="">{{ __('All Months') }}</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                            {{ date('M', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endfor
                </select>
            </div>
            
            <!-- Location Filters -->
            @if(auth()->user()->isAdmin())
            <div class="col-md-2">
                <label for="regionFilter" class="form-label small fw-medium">
                    <i class="bi bi-geo-alt me-1"></i>{{ __('Region') }}
                </label>
                <select class="form-select form-select-sm" id="regionFilter">
                    <option value="">{{ __('All Regions') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="channelFilter" class="form-label small fw-medium">
                    <i class="bi bi-diagram-3 me-1"></i>{{ __('Channel') }}
                </label>
                <select class="form-select form-select-sm" id="channelFilter">
                    <option value="">{{ __('All Channels') }}</option>
                </select>
            </div>
            @else
            <!-- Manager sees their assigned region/channel only -->
            <div class="col-md-2">
                <label for="regionFilter" class="form-label small fw-medium">
                    <i class="bi bi-geo-alt me-1"></i>{{ __('Region') }}
                </label>
                <select class="form-select form-select-sm" id="regionFilter" 
                        @if(auth()->user()->isManager() && auth()->user()->regions->count() <= 1) disabled @endif>
                    @if(auth()->user()->isAdmin())
                        <option value="">{{ __('All Regions') }}</option>
                    @elseif(auth()->user()->regions->count() == 1)
                        <option value="{{ auth()->user()->regions->first()->id }}" selected>
                            {{ auth()->user()->regions->first()->name }}
                        </option>
                    @else
                        <option value="">{{ __('Select Region') }}</option>
                        @foreach(auth()->user()->regions as $region)
                            <option value="{{ $region->id }}">{{ $region->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-2">
                <label for="channelFilter" class="form-label small fw-medium">
                    <i class="bi bi-diagram-3 me-1"></i>{{ __('Channel') }}
                </label>
                <select class="form-select form-select-sm" id="channelFilter" 
                        @if(auth()->user()->isManager() && auth()->user()->channels->count() <= 1) disabled @endif>
                    @if(auth()->user()->isAdmin())
                        <option value="">{{ __('All Channels') }}</option>
                    @elseif(auth()->user()->channels->count() == 1)
                        <option value="{{ auth()->user()->channels->first()->id }}" selected>
                            {{ auth()->user()->channels->first()->name }}
                        </option>
                    @else
                        <option value="">{{ __('Select Channel') }}</option>
                        @foreach(auth()->user()->channels as $channel)
                            <option value="{{ $channel->id }}">{{ $channel->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            @endif
            
            <!-- Product Filters -->
            <div class="col-md-2">
                <label for="supplierFilter" class="form-label small fw-medium">
                    <i class="bi bi-building me-1"></i>{{ __('Supplier') }}
                </label>
                <select class="form-select form-select-sm" id="supplierFilter">
                    <option value="">{{ __('All Suppliers') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="categoryFilter" class="form-label small fw-medium">
                    <i class="bi bi-tags me-1"></i>{{ __('Category') }}
                </label>
                <select class="form-select form-select-sm" id="categoryFilter">
                    <option value="">{{ __('All Categories') }}</option>
                </select>
            </div>
        </div>
        
        <div class="row g-3 mt-2">
            <!-- Classification Filter -->
            <div class="col-md-2">
                <label for="classificationFilter" class="form-label small fw-medium">
                    <i class="bi bi-collection me-1"></i>{{ __('Classification') }}
                </label>
                <select class="form-select form-select-sm" id="classificationFilter">
                    <!-- Will be populated dynamically based on user permissions -->
                </select>
            </div>
            
            <!-- Salesman Filter -->
            <div class="col-md-3">
                <label for="salesmanFilter" class="form-label small fw-medium">
                    <i class="bi bi-people me-1"></i>{{ __('Salesman') }}
                </label>
                <select class="form-select form-select-sm" id="salesmanFilter">
                    <option value="">{{ __('All Salesmen') }}</option>
                </select>
            </div>
            
            <!-- Action Buttons -->
            <div class="col-md-7 d-flex align-items-end gap-2">
                <button class="btn btn-primary btn-sm" onclick="loadReports()">
                    <i class="bi bi-search me-1"></i>{{ __('Generate Report') }}
                </button>
                <button class="btn btn-outline-secondary btn-sm" onclick="clearFilters()">
                    <i class="bi bi-x-circle me-1"></i>{{ __('Clear Filters') }}
                </button>
                <button class="btn btn-success btn-sm" onclick="exportReport()">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i>{{ __('Export Excel') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-currency-dollar text-primary fs-2"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="card-title text-muted mb-1">{{ __('Total Targets') }}</h6>
                        <h3 class="mb-0" id="totalTargets">$0</h3>
                        <small class="text-muted">{{ __('All Periods') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-check-circle text-success fs-2"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="card-title text-muted mb-1">{{ __('Achieved') }}</h6>
                        <h3 class="mb-0" id="achievedTargets">$0</h3>
                        <small class="text-success">{{ __('100%') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-hourglass-split text-warning fs-2"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="card-title text-muted mb-1">{{ __('Pending') }}</h6>
                        <h3 class="mb-0" id="pendingTargets">$0</h3>
                        <small class="text-warning">{{ __('0%') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-people text-info fs-2"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="card-title text-muted mb-1">{{ __('Active Salesmen') }}</h6>
                        <h3 class="mb-0" id="activeSalesmen">0</h3>
                        <small class="text-muted">{{ __('This Period') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reports Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-table me-2"></i>{{ __('Detailed Report') }}
        </h5>
    </div>
    <div class="card-body">
        <div id="reportsContent">
            <div class="text-center py-5">
                <i class="bi bi-graph-up text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3">{{ __('Generate Report') }}</h5>
                <p class="text-muted">{{ __('Select filters and click "Generate Report" to view data') }}</p>
            </div>
        </div>
    </div>
</div>

<script>
let userInfo = null;

document.addEventListener('DOMContentLoaded', function() {
    console.log('🎯 REPORTS PAGE LOADED');
    loadUserInfo();
    loadFilters();
    loadSummary();
});

async function loadUserInfo() {
    const fetchOptions = {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    };

    try {
        console.log('📡 Fetching user info from /api/v1/user/info');
        const response = await fetch('/api/v1/user/info', fetchOptions);
        console.log('📡 User info response status:', response.status);
        
        if (response.ok) {
            const result = await response.json();
            console.log('👤 User info received:', result);
            userInfo = result.data;
            setUserClassificationFilter();
        } else {
            console.error('❌ User info failed:', response.status, await response.text());
        }
    } catch (error) {
        console.error('❌ Error loading user info:', error);
    }
}

function setUserClassificationFilter() {
    console.log('🎯 Setting classification filter, userInfo:', userInfo);
    const classificationSelect = document.getElementById('classificationFilter');
    classificationSelect.innerHTML = ''; // Clear existing options
    
    if (userInfo.is_admin) {
        // Admin sees all options
        const allOption = document.createElement('option');
        allOption.value = '';
        allOption.textContent = 'All Classifications';
        classificationSelect.appendChild(allOption);
        
        const foodOption = document.createElement('option');
        foodOption.value = 'food';
        foodOption.textContent = 'Food';
        classificationSelect.appendChild(foodOption);
        
        const nonFoodOption = document.createElement('option');
        nonFoodOption.value = 'non_food';
        nonFoodOption.textContent = 'Non-Food';
        classificationSelect.appendChild(nonFoodOption);
    } else {
        // Manager sees based on their assigned classifications
        if (userInfo.classifications && userInfo.classifications.length > 0) {
            if (userInfo.classifications.length > 1) {
                // Multiple classifications - show "All" option
                const allOption = document.createElement('option');
                allOption.value = '';
                allOption.textContent = 'All Classifications';
                classificationSelect.appendChild(allOption);
            }
            
            userInfo.classifications.forEach(classification => {
                const option = document.createElement('option');
                option.value = classification;
                option.textContent = classification === 'food' ? 'Food' : 'Non-Food';
                if (userInfo.classifications.length === 1) {
                    option.selected = true;
                    classificationSelect.disabled = true;
                }
                classificationSelect.appendChild(option);
            });
        } else {
            // No classifications assigned
            const noOption = document.createElement('option');
            noOption.value = '';
            noOption.textContent = 'No Classifications';
            noOption.disabled = true;
            classificationSelect.appendChild(noOption);
            classificationSelect.disabled = true;
        }
    }
}

async function loadFilters() {
    const fetchOptions = {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    };

    try {
        // Load regions
        console.log('📡 Loading regions from /api/v1/deps/regions');
        const regionsResponse = await fetch('/api/v1/deps/regions', fetchOptions);
        console.log('📡 Regions response status:', regionsResponse.status);
        
        if (regionsResponse.ok) {
            const regions = await regionsResponse.json();
            console.log('🏘️ Regions loaded:', regions);
            const regionSelect = document.getElementById('regionFilter');
            regions.data?.forEach(region => {
                const option = document.createElement('option');
                option.value = region.id;
                option.textContent = region.name;
                regionSelect.appendChild(option);
            });
        } else {
            console.error('❌ Regions failed:', regionsResponse.status, await regionsResponse.text());
        }

        // Load channels
        console.log('📡 Loading channels from /api/v1/deps/channels');
        const channelsResponse = await fetch('/api/v1/deps/channels', fetchOptions);
        console.log('📡 Channels response status:', channelsResponse.status);
        
        if (channelsResponse.ok) {
            const channels = await channelsResponse.json();
            console.log('🏢 Channels loaded:', channels);
            const channelSelect = document.getElementById('channelFilter');
            channels.data?.forEach(channel => {
                const option = document.createElement('option');
                option.value = channel.id;
                option.textContent = channel.name;
                channelSelect.appendChild(option);
            });
        } else {
            console.error('❌ Channels failed:', channelsResponse.status, await channelsResponse.text());
        }

        // Load suppliers
        console.log('📡 Loading suppliers from /api/v1/deps/suppliers');
        const suppliersResponse = await fetch('/api/v1/deps/suppliers', fetchOptions);
        console.log('📡 Suppliers response status:', suppliersResponse.status);
        
        if (suppliersResponse.ok) {
            const suppliers = await suppliersResponse.json();
            console.log('🏪 Suppliers loaded:', suppliers);
            const supplierSelect = document.getElementById('supplierFilter');
            suppliers.data?.forEach(supplier => {
                const option = document.createElement('option');
                option.value = supplier.id;
                option.textContent = supplier.name;
                supplierSelect.appendChild(option);
            });
        } else {
            console.error('❌ Suppliers failed:', suppliersResponse.status, await suppliersResponse.text());
        }

        // Load categories
        console.log('📡 Loading categories from /api/v1/deps/categories');
        const categoriesResponse = await fetch('/api/v1/deps/categories', fetchOptions);
        console.log('📡 Categories response status:', categoriesResponse.status);
        
        if (categoriesResponse.ok) {
            const categories = await categoriesResponse.json();
            console.log('📦 Categories loaded:', categories);
            const categorySelect = document.getElementById('categoryFilter');
            categories.data?.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                categorySelect.appendChild(option);
            });
        } else {
            console.error('❌ Categories failed:', categoriesResponse.status, await categoriesResponse.text());
        }

        // Load salesmen
        console.log('📡 Loading salesmen from /api/v1/deps/salesmen');
        const salesmenResponse = await fetch('/api/v1/deps/salesmen', fetchOptions);
        console.log('📡 Salesmen response status:', salesmenResponse.status);
        
        if (salesmenResponse.ok) {
            const salesmen = await salesmenResponse.json();
            console.log('👥 Salesmen loaded:', salesmen);
            const salesmanSelect = document.getElementById('salesmanFilter');
            salesmen.data?.forEach(salesman => {
                const option = document.createElement('option');
                option.value = salesman.id;
                option.textContent = `${salesman.name} (${salesman.salesman_code})`;
                salesmanSelect.appendChild(option);
            });
        } else {
            console.error('❌ Salesmen failed:', salesmenResponse.status, await salesmenResponse.text());
        }
    } catch (error) {
        console.error('Error loading filters:', error);
    }
}

async function loadSummary() {
    const fetchOptions = {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    };

    try {
        const response = await fetch('/api/v1/reports/summary', fetchOptions);
        if (response.ok) {
            const data = await response.json();
            document.getElementById('totalTargets').textContent = `$${(data.total_amount || 0).toLocaleString()}`;
            document.getElementById('achievedTargets').textContent = `$${(data.total_amount || 0).toLocaleString()}`;
            document.getElementById('pendingTargets').textContent = '$0';
            document.getElementById('activeSalesmen').textContent = data.total_targets || '0';
        } else {
            // Fallback to default values
            document.getElementById('totalTargets').textContent = '$0';
            document.getElementById('achievedTargets').textContent = '$0';
            document.getElementById('pendingTargets').textContent = '$0';
            document.getElementById('activeSalesmen').textContent = '0';
        }
    } catch (error) {
        console.error('Error loading summary:', error);
        // Fallback to default values
        document.getElementById('totalTargets').textContent = '$0';
        document.getElementById('achievedTargets').textContent = '$0';
        document.getElementById('pendingTargets').textContent = '$0';
        document.getElementById('activeSalesmen').textContent = '0';
    }
}

async function loadReports() {
    console.log('📊 Loading reports...');
    const reportsContent = document.getElementById('reportsContent');
    
    // Get all filter values
    const filters = {
        year: document.getElementById('yearFilter').value,
        month: document.getElementById('monthFilter').value,
        region_id: document.getElementById('regionFilter').value,
        channel_id: document.getElementById('channelFilter').value,
        supplier_id: document.getElementById('supplierFilter').value,
        category_id: document.getElementById('categoryFilter').value,
        classification: document.getElementById('classificationFilter').value,
        salesman_id: document.getElementById('salesmanFilter').value,
    };
    
    console.log('🎯 Raw filters:', filters);
    
    // Remove empty filters
    Object.keys(filters).forEach(key => {
        if (!filters[key]) {
            delete filters[key];
        }
    });
    
    console.log('🎯 Clean filters:', filters);
    
    // Show loading state
    reportsContent.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">{{ __('Loading...') }}</span>
            </div>
            <p class="text-muted mt-2">{{ __('Generating report with selected filters...') }}</p>
        </div>
    `;
    
    try {
        // Build query string
        const queryParams = new URLSearchParams(filters);
        
        // Fetch targets data from API
        const apiUrl = `/api/v1/targets?${queryParams}`;
        console.log('📡 Fetching targets from:', apiUrl);
        
        const response = await fetch(apiUrl, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        });
        
        console.log('📡 Targets response status:', response.status);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('❌ Targets API error:', response.status, errorText);
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        console.log('📊 Targets result:', result);
        const targets = result.data.data || []; // Handle pagination
        console.log('📊 Targets array:', targets);
        
        // Build active filters display
        const activeFilters = Object.entries(filters)
            .map(([key, value]) => {
                const filterNames = {
                    year: 'Year',
                    month: 'Month',
                    region_id: 'Region',
                    channel_id: 'Channel',
                    supplier_id: 'Supplier',
                    category_id: 'Category',
                    classification: 'Classification',
                    salesman_id: 'Salesman'
                };
                return `${filterNames[key]}: ${value}`;
            });
        
        if (targets.length === 0) {
            // No data found
            reportsContent.innerHTML = `
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">
                                    <i class="bi bi-person me-1 text-muted"></i>{{ __('Salesman') }}
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-geo-alt me-1 text-muted"></i>{{ __('Region') }}
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-diagram-3 me-1 text-muted"></i>{{ __('Channel') }}
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-building me-1 text-muted"></i>{{ __('Supplier') }}
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-tags me-1 text-muted"></i>{{ __('Category') }}
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-calendar me-1 text-muted"></i>{{ __('Period') }}
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-currency-dollar me-1 text-muted"></i>{{ __('Target Amount') }}
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-activity me-1 text-muted"></i>{{ __('Status') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-graph-up" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-1">{{ __('No data available for the selected filters') }}</p>
                                        ${activeFilters.length > 0 ? 
                                            `<small class="text-muted">{{ __('Active filters') }}: ${activeFilters.join(', ')}</small>` : 
                                            `<small class="text-muted">{{ __('Try selecting specific filters to narrow down the results') }}</small>`
                                        }
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            `;
        } else {
            // Display the data
            const tableRows = targets.map(target => {
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 
                                 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                const monthName = monthNames[target.month - 1] || target.month;
                const period = `${monthName} ${target.year}`;
                
                return `
                    <tr>
                        <td>
                            <div class="fw-medium">${target.salesman?.name || 'N/A'}</div>
                            <small class="text-muted">${target.salesman?.salesman_code || ''}</small>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary">${target.region?.name || 'N/A'}</span>
                        </td>
                        <td>
                            <span class="badge bg-info-subtle text-info">${target.channel?.name || 'N/A'}</span>
                        </td>
                        <td>
                            <div class="fw-medium">${target.supplier?.name || 'N/A'}</div>
                            <small class="text-muted">${target.supplier?.supplier_code || ''}</small>
                        </td>
                        <td>
                            <div class="fw-medium">${target.category?.name || 'N/A'}</div>
                            <small class="text-muted">${target.category?.category_code || ''}</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary">${period}</span>
                        </td>
                        <td class="text-end">
                            <span class="fw-bold text-success">$${parseFloat(target.target_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                        </td>
                        <td>
                            <span class="badge bg-success-subtle text-success">
                                <i class="bi bi-check-circle me-1"></i>{{ __('Active') }}
                            </span>
                        </td>
                    </tr>
                `;
            }).join('');
            
            reportsContent.innerHTML = `
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">
                                    <i class="bi bi-person me-1 text-muted"></i>{{ __('Salesman') }}
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-geo-alt me-1 text-muted"></i>{{ __('Region') }}
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-diagram-3 me-1 text-muted"></i>{{ __('Channel') }}
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-building me-1 text-muted"></i>{{ __('Supplier') }}
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-tags me-1 text-muted"></i>{{ __('Category') }}
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-calendar me-1 text-muted"></i>{{ __('Period') }}
                                </th>
                                <th class="border-0 text-end">
                                    <i class="bi bi-currency-dollar me-1 text-muted"></i>{{ __('Target Amount') }}
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-activity me-1 text-muted"></i>{{ __('Status') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            ${tableRows}
                        </tbody>
                    </table>
                </div>
                ${result.data.total ? `
                    <div class="card-footer bg-light">
                        <small class="text-muted">
                            {{ __('Showing') }} ${targets.length} {{ __('of') }} ${result.data.total} {{ __('targets') }}
                            ${activeFilters.length > 0 ? ` | {{ __('Filtered by') }}: ${activeFilters.join(', ')}` : ''}
                        </small>
                    </div>
                ` : ''}
            `;
        }
        
    } catch (error) {
        console.error('Error loading reports:', error);
        reportsContent.innerHTML = `
            <div class="alert alert-danger" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ __('Error loading report data') }}: ${error.message}
                <br><small class="text-muted">{{ __('Please try again or contact support if the problem persists.') }}</small>
            </div>
        `;
    }
}

function exportReport() {
    // Get all filter values
    const filters = {
        year: document.getElementById('yearFilter').value,
        month: document.getElementById('monthFilter').value,
        region_id: document.getElementById('regionFilter').value,
        channel_id: document.getElementById('channelFilter').value,
        supplier_id: document.getElementById('supplierFilter').value,
        category_id: document.getElementById('categoryFilter').value,
        classification: document.getElementById('classificationFilter').value,
        salesman_id: document.getElementById('salesmanFilter').value,
    };
    
    // Remove empty filters
    Object.keys(filters).forEach(key => {
        if (!filters[key]) {
            delete filters[key];
        }
    });
    
    // Build query string
    const queryParams = new URLSearchParams(filters);
    
    // Open the export URL in a new window
    window.open(`/api/v1/reports/export.xlsx?${queryParams}`, '_blank');
}

function clearFilters() {
    // Reset all filter dropdowns to their default values
    document.getElementById('yearFilter').value = '{{ date("Y") }}';
    document.getElementById('monthFilter').value = '{{ date("n") }}';
    
    // Reset filters based on user permissions
    if (userInfo) {
        if (userInfo.is_admin) {
            document.getElementById('regionFilter').value = '';
            document.getElementById('channelFilter').value = '';
            document.getElementById('classificationFilter').value = '';
        } else {
            // Managers keep their assigned values or reset appropriately
            const regionFilter = document.getElementById('regionFilter');
            const channelFilter = document.getElementById('channelFilter');
            const classificationFilter = document.getElementById('classificationFilter');
            
            if (!regionFilter.disabled) {
                regionFilter.value = '';
            }
            if (!channelFilter.disabled) {
                channelFilter.value = '';
            }
            if (!classificationFilter.disabled) {
                classificationFilter.value = '';
            }
        }
    }
    
    document.getElementById('supplierFilter').value = '';
    document.getElementById('categoryFilter').value = '';
    document.getElementById('salesmanFilter').value = '';
    
    // Optionally reload the summary with cleared filters
    loadSummary();
}
</script>
@endsection