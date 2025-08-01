

<?php $__env->startSection('title', __('Regions')); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h2 mb-1"><?php echo e(__('Regions')); ?></h1>
        <p class="text-muted mb-0"><?php echo e(__('Manage sales regions and territories')); ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('regions.create')); ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i><?php echo e(__('Add Region')); ?>

        </a>
    </div>
</div>

<!-- Messages -->
<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle me-2"></i>
            <div>
                <strong><?php echo e(__('Success!')); ?></strong> <?php echo e(session('success')); ?>

            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <div>
                <strong><?php echo e(__('Error!')); ?></strong> <?php echo e(session('error')); ?>

            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Regions Table -->
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <h5 class="mb-0 me-3">
                    <i class="bi bi-geo-alt me-2"></i><?php echo e(__('Regions List')); ?>

                </h5>
                <small class="text-muted" id="resultsCount">
                    <?php echo e(method_exists($regions, 'total') ? $regions->total() : count($regions)); ?> <?php echo e(__('records')); ?>

                </small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="input-group" style="width: 250px;">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" id="searchInput" placeholder="<?php echo e(__('Search regions...')); ?>">
                </div>
                <select class="form-select" id="statusFilter" style="width: 130px;">
                    <option value=""><?php echo e(__('All Status')); ?></option>
                    <option value="active"><?php echo e(__('Active')); ?></option>
                    <option value="inactive"><?php echo e(__('Inactive')); ?></option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="regionsTable">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 px-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-hash me-2 text-muted"></i><?php echo e(__('Region Code')); ?>

                            </div>
                        </th>
                        <th class="border-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-geo-alt me-2 text-muted"></i><?php echo e(__('Name')); ?>

                            </div>
                        </th>
                        <th class="border-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-activity me-2 text-muted"></i><?php echo e(__('Status')); ?>

                            </div>
                        </th>
                        <th class="border-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar me-2 text-muted"></i><?php echo e(__('Created')); ?>

                            </div>
                        </th>
                        <th class="border-0 text-center" style="width: 120px;">
                            <i class="bi bi-gear me-1 text-muted"></i><?php echo e(__('Actions')); ?>

                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $regions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="region-row" data-status="<?php echo e($region->is_active ? 'active' : 'inactive'); ?>">
                            <td class="px-4">
                                <code class="bg-primary-subtle text-primary px-2 py-1 rounded small"><?php echo e($region->region_code); ?></code>
                            </td>
                            <td>
                                <div class="fw-medium text-dark"><?php echo e($region->name); ?></div>
                            </td>
                            <td>
                                <?php if($region->is_active): ?>
                                    <span class="badge bg-success-subtle text-success px-2">
                                        <i class="bi bi-check-circle me-1"></i><?php echo e(__('Active')); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary px-2">
                                        <i class="bi bi-pause-circle me-1"></i><?php echo e(__('Inactive')); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="text-muted small"><?php echo e($region->created_at->format('M d, Y')); ?></span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="<?php echo e(route('regions.show', $region)); ?>" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="<?php echo e(__('View')); ?>"
                                       data-bs-toggle="tooltip">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('regions.edit', $region)); ?>" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="<?php echo e(__('Edit')); ?>"
                                       data-bs-toggle="tooltip">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            onclick="confirmDelete('<?php echo e($region->id); ?>', '<?php echo e($region->name); ?>')"
                                            title="<?php echo e(__('Delete')); ?>"
                                            data-bs-toggle="tooltip">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                
                                <!-- Hidden Delete Form -->
                                <form id="delete-form-<?php echo e($region->id); ?>" 
                                      action="<?php echo e(route('regions.destroy', $region)); ?>" 
                                      method="POST" 
                                      class="d-none">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-geo-alt" style="font-size: 2rem;"></i>
                                    <p class="mt-2 mb-0"><?php echo e(__('No regions found')); ?></p>
                                    <small class="text-muted"><?php echo e(__('Try adjusting your search or create a new region')); ?></small>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php if(method_exists($regions, 'hasPages') && $regions->hasPages()): ?>
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    <?php echo e(__('Showing')); ?> <?php echo e($regions->firstItem()); ?> <?php echo e(__('to')); ?> <?php echo e($regions->lastItem()); ?> <?php echo e(__('of')); ?> <?php echo e($regions->total()); ?> <?php echo e(__('results')); ?>

                </div>
                <?php echo e($regions->links()); ?>

            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle text-warning me-2"></i><?php echo e(__('Confirm Delete')); ?>

                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><?php echo e(__('Are you sure you want to delete the region')); ?> <strong id="regionName"></strong>?</p>
                <p class="text-muted small mb-0"><?php echo e(__('This action cannot be undone.')); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <?php echo e(__('Cancel')); ?>

                </button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="bi bi-trash me-2"></i><?php echo e(__('Delete Region')); ?>

                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const clearFilters = document.getElementById('clearFilters');
    const resultsCount = document.getElementById('resultsCount');
    const table = document.getElementById('regionsTable');
    const rows = table.querySelectorAll('.region-row');

    // Search functionality
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusFilterValue = statusFilter.value;
        let visibleCount = 0;

        rows.forEach(row => {
            const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const code = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
            const status = row.getAttribute('data-status');
            
            const matchesSearch = name.includes(searchTerm) || code.includes(searchTerm);
            const matchesStatus = !statusFilterValue || status === statusFilterValue;
            
            if (matchesSearch && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update results count
        resultsCount.textContent = `${visibleCount} ${visibleCount === 1 ? 'region' : 'regions'} found`;
    }

    // Event listeners
    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);
    
    clearFilters.addEventListener('click', function() {
        searchInput.value = '';
        statusFilter.value = '';
        filterTable();
    });

    // Initialize
    filterTable();
});

// Delete confirmation
function confirmDelete(regionId, regionName) {
    document.getElementById('regionName').textContent = regionName;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
    
    document.getElementById('confirmDelete').onclick = function() {
        document.getElementById(`delete-form-${regionId}`).submit();
    };
}

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);
</script>

<style>
.badge.bg-success-subtle {
    background-color: rgba(16, 185, 129, 0.1) !important;
}

.badge.bg-secondary-subtle {
    background-color: rgba(100, 116, 139, 0.1) !important;
}

.table tbody tr {
    transition: all 0.2s ease;
}

.table tbody tr:hover {
    background-color: rgba(79, 70, 229, 0.05);
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.modal-content {
    border-radius: 0.75rem;
    border: none;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.pagination {
    gap: 0.25rem;
}

.page-link {
    border-radius: 0.375rem;
    border: 1px solid var(--border-color);
    color: var(--text-primary);
}

.page-link:hover {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.page-item.active .page-link {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}
</style>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\New target\target-system\resources\views/regions/index.blade.php ENDPATH**/ ?>