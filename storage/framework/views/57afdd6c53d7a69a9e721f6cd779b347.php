<?php $__env->startSection('title', __('Reports')); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="h2 mb-1"><?php echo e(__('Reports')); ?></h1>
        <p class="text-muted mb-0"><?php echo e(__('View and analyze sales target reports')); ?></p>
    </div>
    <div class="d-flex gap-2" style="margin-top: 0.5rem;">
        <span class="badge bg-info-subtle text-info px-3 py-2">
            <i class="bi bi-graph-up me-1"></i><?php echo e(__('Analytics Dashboard')); ?>

        </span>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-funnel me-2"></i><?php echo e(__('Report Filters')); ?>

        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <!-- Period Filters -->
            <div class="col-md-2">
                <label for="yearFilter" class="form-label small fw-medium">
                    <i class="bi bi-calendar-date me-1"></i><?php echo e(__('Year')); ?>

                </label>
                <select class="form-select form-select-sm" id="yearFilter">
                    <option value=""><?php echo e(__('All Years')); ?></option>
                    <?php for($y = date('Y'); $y >= date('Y') - 2; $y--): ?>
                        <option value="<?php echo e($y); ?>" <?php echo e($y == date('Y') ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="monthFilter" class="form-label small fw-medium">
                    <i class="bi bi-calendar-month me-1"></i><?php echo e(__('Month')); ?>

                </label>
                <select class="form-select form-select-sm" id="monthFilter">
                    <option value=""><?php echo e(__('All Months')); ?></option>
                    <?php for($m = 1; $m <= 12; $m++): ?>
                        <option value="<?php echo e($m); ?>" <?php echo e($m == date('n') ? 'selected' : ''); ?>>
                            <?php echo e(date('M', mktime(0, 0, 0, $m, 1))); ?>

                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <!-- Location Filters -->
            <?php if(auth()->user()->isAdmin()): ?>
            <div class="col-md-2">
                <label for="regionFilter" class="form-label small fw-medium">
                    <i class="bi bi-geo-alt me-1"></i><?php echo e(__('Region')); ?>

                </label>
                <select class="form-select form-select-sm" id="regionFilter">
                    <option value=""><?php echo e(__('All Regions')); ?></option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="channelFilter" class="form-label small fw-medium">
                    <i class="bi bi-diagram-3 me-1"></i><?php echo e(__('Channel')); ?>

                </label>
                <select class="form-select form-select-sm" id="channelFilter">
                    <option value=""><?php echo e(__('All Channels')); ?></option>
                </select>
            </div>
            <?php else: ?>
            <!-- Manager sees their assigned region/channel only -->
            <div class="col-md-2">
                <label for="regionFilter" class="form-label small fw-medium">
                    <i class="bi bi-geo-alt me-1"></i><?php echo e(__('Region')); ?>

                </label>
                <select class="form-select form-select-sm" id="regionFilter" 
                        <?php if(auth()->user()->isManager() && auth()->user()->regions->count() <= 1): ?> disabled <?php endif; ?>>
                    <?php if(auth()->user()->isAdmin()): ?>
                        <option value=""><?php echo e(__('All Regions')); ?></option>
                    <?php elseif(auth()->user()->regions->count() == 1): ?>
                        <option value="<?php echo e(auth()->user()->regions->first()->id); ?>" selected>
                            <?php echo e(auth()->user()->regions->first()->name); ?>

                        </option>
                    <?php else: ?>
                        <option value=""><?php echo e(__('Select Region')); ?></option>
                        <?php $__currentLoopData = auth()->user()->regions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($region->id); ?>"><?php echo e($region->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="channelFilter" class="form-label small fw-medium">
                    <i class="bi bi-diagram-3 me-1"></i><?php echo e(__('Channel')); ?>

                </label>
                <select class="form-select form-select-sm" id="channelFilter" 
                        <?php if(auth()->user()->isManager() && auth()->user()->channels->count() <= 1): ?> disabled <?php endif; ?>>
                    <?php if(auth()->user()->isAdmin()): ?>
                        <option value=""><?php echo e(__('All Channels')); ?></option>
                    <?php elseif(auth()->user()->channels->count() == 1): ?>
                        <option value="<?php echo e(auth()->user()->channels->first()->id); ?>" selected>
                            <?php echo e(auth()->user()->channels->first()->name); ?>

                        </option>
                    <?php else: ?>
                        <option value=""><?php echo e(__('Select Channel')); ?></option>
                        <?php $__currentLoopData = auth()->user()->channels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $channel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($channel->id); ?>"><?php echo e($channel->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </select>
            </div>
            <?php endif; ?>
            
            <!-- Product Filters -->
            <div class="col-md-2">
                <label for="supplierFilter" class="form-label small fw-medium">
                    <i class="bi bi-building me-1"></i><?php echo e(__('Supplier')); ?>

                </label>
                <select class="form-select form-select-sm" id="supplierFilter">
                    <option value=""><?php echo e(__('All Suppliers')); ?></option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="categoryFilter" class="form-label small fw-medium">
                    <i class="bi bi-tags me-1"></i><?php echo e(__('Category')); ?>

                </label>
                <select class="form-select form-select-sm" id="categoryFilter">
                    <option value=""><?php echo e(__('All Categories')); ?></option>
                </select>
            </div>
        </div>
        
        <div class="row g-3 mt-2">
            <!-- Classification Filter -->
            <div class="col-md-2">
                <label for="classificationFilter" class="form-label small fw-medium">
                    <i class="bi bi-collection me-1"></i><?php echo e(__('Classification')); ?>

                </label>
                <select class="form-select form-select-sm" id="classificationFilter" <?php echo e(auth()->user()->isManager() && auth()->user()->classification && auth()->user()->classification !== 'both' ? 'disabled' : ''); ?>>
                    <?php if(auth()->user()->isAdmin()): ?>
                        <option value=""><?php echo e(__('All Classifications')); ?></option>
                        <option value="food"><?php echo e(__('Food')); ?></option>
                        <option value="non_food"><?php echo e(__('Non-Food')); ?></option>
                        <option value="both"><?php echo e(__('Both')); ?></option>
                    <?php else: ?>
                        <?php if(auth()->user()->classification): ?>
                            <?php if(auth()->user()->classification === 'both'): ?>
                                <option value=""><?php echo e(__('All Classifications')); ?></option>
                                <option value="food"><?php echo e(__('Food')); ?></option>
                                <option value="non_food"><?php echo e(__('Non-Food')); ?></option>
                                <option value="both" selected><?php echo e(__('Both')); ?></option>
                            <?php else: ?>
                                <option value="<?php echo e(auth()->user()->classification); ?>" selected>
                                    <?php echo e(auth()->user()->classification === 'food' ? __('Food') : __('Non-Food')); ?>

                                </option>
                            <?php endif; ?>
                        <?php else: ?>
                            <option value=""><?php echo e(__('All Classifications')); ?></option>
                            <option value="food"><?php echo e(__('Food')); ?></option>
                            <option value="non_food"><?php echo e(__('Non-Food')); ?></option>
                            <option value="both"><?php echo e(__('Both')); ?></option>
                        <?php endif; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <!-- Salesman Filter -->
            <div class="col-md-3">
                <label for="salesmanFilter" class="form-label small fw-medium">
                    <i class="bi bi-people me-1"></i><?php echo e(__('Salesman')); ?>

                </label>
                <select class="form-select form-select-sm" id="salesmanFilter">
                    <option value=""><?php echo e(__('All Salesmen')); ?></option>
                </select>
            </div>
            
            <!-- Action Buttons -->
            <div class="col-md-7 d-flex align-items-end gap-2">
                <button class="btn btn-primary btn-sm" onclick="loadReports()">
                    <i class="bi bi-search me-1"></i><?php echo e(__('Generate Report')); ?>

                </button>
                <button class="btn btn-outline-secondary btn-sm" onclick="clearFilters()">
                    <i class="bi bi-x-circle me-1"></i><?php echo e(__('Clear Filters')); ?>

                </button>
                <button class="btn btn-success btn-sm" onclick="exportReport()">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i><?php echo e(__('Export Excel')); ?>

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
                        <h6 class="card-title text-muted mb-1"><?php echo e(__('Total Targets')); ?></h6>
                        <h3 class="mb-0" id="totalTargets">$0</h3>
                        <small class="text-muted"><?php echo e(__('All Periods')); ?></small>
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
                        <h6 class="card-title text-muted mb-1"><?php echo e(__('Achieved')); ?></h6>
                        <h3 class="mb-0" id="achievedTargets">$0</h3>
                        <small class="text-success"><?php echo e(__('100%')); ?></small>
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
                        <h6 class="card-title text-muted mb-1"><?php echo e(__('Pending')); ?></h6>
                        <h3 class="mb-0" id="pendingTargets">$0</h3>
                        <small class="text-warning"><?php echo e(__('0%')); ?></small>
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
                        <h6 class="card-title text-muted mb-1"><?php echo e(__('Active Salesmen')); ?></h6>
                        <h3 class="mb-0" id="activeSalesmen">0</h3>
                        <small class="text-muted"><?php echo e(__('This Period')); ?></small>
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
            <i class="bi bi-table me-2"></i><?php echo e(__('Detailed Report')); ?>

        </h5>
    </div>
    <div class="card-body">
        <div id="reportsContent">
            <div class="text-center py-5">
                <i class="bi bi-graph-up text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3"><?php echo e(__('Generate Report')); ?></h5>
                <p class="text-muted"><?php echo e(__('Select filters and click "Generate Report" to view data')); ?></p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadFilters();
    loadSummary();
});

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
        const regionsResponse = await fetch('/api/deps/regions', fetchOptions);
        if (regionsResponse.ok) {
            const regions = await regionsResponse.json();
            const regionSelect = document.getElementById('regionFilter');
            regions.forEach(region => {
                const option = document.createElement('option');
                option.value = region.id;
                option.textContent = region.name;
                regionSelect.appendChild(option);
            });
        }

        // Load channels
        const channelsResponse = await fetch('/api/deps/channels', fetchOptions);
        if (channelsResponse.ok) {
            const channels = await channelsResponse.json();
            const channelSelect = document.getElementById('channelFilter');
            channels.forEach(channel => {
                const option = document.createElement('option');
                option.value = channel.id;
                option.textContent = channel.name;
                channelSelect.appendChild(option);
            });
        }

        // Load suppliers
        const suppliersResponse = await fetch('/api/deps/suppliers', fetchOptions);
        if (suppliersResponse.ok) {
            const suppliers = await suppliersResponse.json();
            const supplierSelect = document.getElementById('supplierFilter');
            suppliers.forEach(supplier => {
                const option = document.createElement('option');
                option.value = supplier.id;
                option.textContent = supplier.name;
                supplierSelect.appendChild(option);
            });
        }

        // Load categories
        const categoriesResponse = await fetch('/api/deps/categories', fetchOptions);
        if (categoriesResponse.ok) {
            const categories = await categoriesResponse.json();
            const categorySelect = document.getElementById('categoryFilter');
            categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                categorySelect.appendChild(option);
            });
        }

        // Load salesmen
        const salesmenResponse = await fetch('/api/deps/salesmen', fetchOptions);
        if (salesmenResponse.ok) {
            const salesmen = await salesmenResponse.json();
            const salesmanSelect = document.getElementById('salesmanFilter');
            salesmen.forEach(salesman => {
                const option = document.createElement('option');
                option.value = salesman.id;
                option.textContent = `${salesman.name} (${salesman.salesman_code})`;
                salesmanSelect.appendChild(option);
            });
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
        const response = await fetch('/api/reports/summary', fetchOptions);
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
    
    // Remove empty filters
    Object.keys(filters).forEach(key => {
        if (!filters[key]) {
            delete filters[key];
        }
    });
    
    // Show loading state
    reportsContent.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden"><?php echo e(__('Loading...')); ?></span>
            </div>
            <p class="text-muted mt-2"><?php echo e(__('Generating report with selected filters...')); ?></p>
        </div>
    `;
    
    try {
        // Build query string
        const queryParams = new URLSearchParams(filters);
        
        // Fetch targets data from API
        const response = await fetch(`/api/targets?${queryParams}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        const targets = result.data.data || []; // Handle pagination
        
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
                                    <i class="bi bi-person me-1 text-muted"></i><?php echo e(__('Salesman')); ?>

                                </th>
                                <th class="border-0">
                                    <i class="bi bi-geo-alt me-1 text-muted"></i><?php echo e(__('Region')); ?>

                                </th>
                                <th class="border-0">
                                    <i class="bi bi-diagram-3 me-1 text-muted"></i><?php echo e(__('Channel')); ?>

                                </th>
                                <th class="border-0">
                                    <i class="bi bi-building me-1 text-muted"></i><?php echo e(__('Supplier')); ?>

                                </th>
                                <th class="border-0">
                                    <i class="bi bi-tags me-1 text-muted"></i><?php echo e(__('Category')); ?>

                                </th>
                                <th class="border-0">
                                    <i class="bi bi-calendar me-1 text-muted"></i><?php echo e(__('Period')); ?>

                                </th>
                                <th class="border-0">
                                    <i class="bi bi-currency-dollar me-1 text-muted"></i><?php echo e(__('Target Amount')); ?>

                                </th>
                                <th class="border-0">
                                    <i class="bi bi-activity me-1 text-muted"></i><?php echo e(__('Status')); ?>

                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-graph-up" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-1"><?php echo e(__('No data available for the selected filters')); ?></p>
                                        ${activeFilters.length > 0 ? 
                                            `<small class="text-muted"><?php echo e(__('Active filters')); ?>: ${activeFilters.join(', ')}</small>` : 
                                            `<small class="text-muted"><?php echo e(__('Try selecting specific filters to narrow down the results')); ?></small>`
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
                                <i class="bi bi-check-circle me-1"></i><?php echo e(__('Active')); ?>

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
                                    <i class="bi bi-person me-1 text-muted"></i><?php echo e(__('Salesman')); ?>

                                </th>
                                <th class="border-0">
                                    <i class="bi bi-geo-alt me-1 text-muted"></i><?php echo e(__('Region')); ?>

                                </th>
                                <th class="border-0">
                                    <i class="bi bi-diagram-3 me-1 text-muted"></i><?php echo e(__('Channel')); ?>

                                </th>
                                <th class="border-0">
                                    <i class="bi bi-building me-1 text-muted"></i><?php echo e(__('Supplier')); ?>

                                </th>
                                <th class="border-0">
                                    <i class="bi bi-tags me-1 text-muted"></i><?php echo e(__('Category')); ?>

                                </th>
                                <th class="border-0">
                                    <i class="bi bi-calendar me-1 text-muted"></i><?php echo e(__('Period')); ?>

                                </th>
                                <th class="border-0 text-end">
                                    <i class="bi bi-currency-dollar me-1 text-muted"></i><?php echo e(__('Target Amount')); ?>

                                </th>
                                <th class="border-0">
                                    <i class="bi bi-activity me-1 text-muted"></i><?php echo e(__('Status')); ?>

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
                            <?php echo e(__('Showing')); ?> ${targets.length} <?php echo e(__('of')); ?> ${result.data.total} <?php echo e(__('targets')); ?>

                            ${activeFilters.length > 0 ? ` | <?php echo e(__('Filtered by')); ?>: ${activeFilters.join(', ')}` : ''}
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
                <?php echo e(__('Error loading report data')); ?>: ${error.message}
                <br><small class="text-muted"><?php echo e(__('Please try again or contact support if the problem persists.')); ?></small>
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
    window.open(`/api/reports/export.xlsx?${queryParams}`, '_blank');
}

function clearFilters() {
    // Reset all filter dropdowns to their default values
    document.getElementById('yearFilter').value = '<?php echo e(date("Y")); ?>';
    document.getElementById('monthFilter').value = '<?php echo e(date("n")); ?>';
    
    <?php if(auth()->user()->isAdmin()): ?>
        document.getElementById('regionFilter').value = '';
        document.getElementById('channelFilter').value = '';
        document.getElementById('classificationFilter').value = '';
    <?php else: ?>
        // Managers keep their assigned values
        <?php if(auth()->user()->isManager()): ?>
            <?php if(auth()->user()->regions->count() == 1): ?>
                document.getElementById('regionFilter').value = '<?php echo e(auth()->user()->regions->first()->id); ?>';
            <?php endif; ?>
            <?php if(auth()->user()->channels->count() == 1): ?>
                document.getElementById('channelFilter').value = '<?php echo e(auth()->user()->channels->first()->id); ?>';
            <?php endif; ?>
        <?php endif; ?>
        <?php if(auth()->user()->classification && auth()->user()->classification !== 'both'): ?>
            document.getElementById('classificationFilter').value = '<?php echo e(auth()->user()->classification); ?>';
        <?php else: ?>
            document.getElementById('classificationFilter').value = '';
        <?php endif; ?>
    <?php endif; ?>
    
    document.getElementById('supplierFilter').value = '';
    document.getElementById('categoryFilter').value = '';
    document.getElementById('salesmanFilter').value = '';
    
    // Optionally reload the summary with cleared filters
    loadSummary();
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\New target\target-system\resources\views/reports/index.blade.php ENDPATH**/ ?>