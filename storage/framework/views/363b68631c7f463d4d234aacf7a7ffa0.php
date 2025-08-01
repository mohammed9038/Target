

<?php $__env->startSection('title', __('Salesmen')); ?>

<?php $__env->startSection('content'); ?>
<!-- Modern Page Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="h2 mb-2 fw-bold d-flex align-items-center">
            <div class="p-2 rounded-circle bg-primary bg-opacity-10 me-3">
                <i class="bi bi-people text-primary"></i>
            </div>
            <?php echo e(__('Salesmen')); ?>

        </h1>
        <p class="text-muted mb-0 ms-5 ps-2"><?php echo e(__('Manage sales team members and assignments')); ?></p>
    </div>
    <div class="d-flex gap-2" style="margin-top: 0.5rem;">
        <a href="<?php echo e(route('salesmen.create')); ?>" class="btn btn-primary shadow-sm" style="border-radius: 8px;">
            <i class="bi bi-plus-circle me-2"></i><?php echo e(__('Add Salesman')); ?>

        </a>
    </div>
</div>

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

<!-- Modern Salesmen Card -->
<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-header border-0 bg-white d-flex justify-content-between align-items-center" style="border-radius: 12px 12px 0 0;">
        <div class="d-flex align-items-center">
            <h5 class="mb-0 me-3 fw-semibold d-flex align-items-center">
                <div class="p-2 rounded-circle bg-success bg-opacity-10 me-3">
                    <i class="bi bi-people text-success"></i>
                </div>
                <?php echo e(__('Salesmen List')); ?>

            </h5>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                <?php echo e(count($salesmen)); ?> <?php echo e(__('records')); ?>

            </span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="input-group shadow-sm" style="width: 280px; border-radius: 8px;">
                <span class="input-group-text bg-light border-0" style="border-radius: 8px 0 0 8px;">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" class="form-control border-0" id="searchInput" 
                       placeholder="<?php echo e(__('Search salesmen...')); ?>" 
                       style="border-radius: 0 8px 8px 0; box-shadow: none;">
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="salesmenTable">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 px-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-badge me-2 text-muted"></i><?php echo e(__('Employee Code')); ?>

                            </div>
                        </th>
                        <th class="border-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person me-2 text-muted"></i><?php echo e(__('Name')); ?>

                            </div>
                        </th>
                        <th class="border-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-geo-alt me-2 text-muted"></i><?php echo e(__('Region')); ?>

                            </div>
                        </th>
                        <th class="border-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-diagram-3 me-2 text-muted"></i><?php echo e(__('Channel')); ?>

                            </div>
                        </th>
                        <th class="border-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-tags me-2 text-muted"></i><?php echo e(__('Classification')); ?>

                            </div>
                        </th>
                        <th class="border-0 text-center" style="width: 120px;">
                            <i class="bi bi-gear me-1 text-muted"></i><?php echo e(__('Actions')); ?>

                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $salesmen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salesman): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4">
                                <code class="bg-primary-subtle text-primary px-2 py-1 rounded small"><?php echo e($salesman->salesman_code); ?></code>
                            </td>
                            <td>
                                <div class="fw-medium text-dark"><?php echo e($salesman->name); ?></div>
                            </td>
                            <td>
                                <?php if($salesman->region): ?>
                                    <div class="text-muted small"><?php echo e($salesman->region->name); ?></div>
                                <?php else: ?>
                                    <span class="text-muted small">
                                        <i class="bi bi-dash-circle me-1"></i><?php echo e(__('N/A')); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($salesman->channel): ?>
                                    <div class="text-muted small"><?php echo e($salesman->channel->name); ?></div>
                                <?php else: ?>
                                    <span class="text-muted small">
                                        <i class="bi bi-dash-circle me-1"></i><?php echo e(__('N/A')); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($salesman->classification === 'food'): ?>
                                    <span class="badge bg-success-subtle text-success px-2">
                                        <i class="bi bi-cup-hot me-1"></i><?php echo e(__('Food')); ?>

                                    </span>
                                <?php elseif($salesman->classification === 'non_food'): ?>
                                    <span class="badge bg-info-subtle text-info px-2">
                                        <i class="bi bi-box me-1"></i><?php echo e(__('Non-Food')); ?>

                                    </span>
                                <?php elseif($salesman->classification === 'both'): ?>
                                    <span class="badge bg-warning-subtle text-warning px-2">
                                        <i class="bi bi-collection me-1"></i><?php echo e(__('Both')); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary px-2">
                                        <i class="bi bi-question-circle me-1"></i><?php echo e(__('Unknown')); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="<?php echo e(route('salesmen.show', $salesman)); ?>" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="<?php echo e(__('View')); ?>"
                                       data-bs-toggle="tooltip">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('salesmen.edit', $salesman)); ?>" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="<?php echo e(__('Edit')); ?>"
                                       data-bs-toggle="tooltip">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="<?php echo e(route('salesmen.destroy', $salesman)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('<?php echo e(__('Are you sure?')); ?>')" 
                                                title="<?php echo e(__('Delete')); ?>"
                                                data-bs-toggle="tooltip">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-people" style="font-size: 2rem;"></i>
                                    <p class="mt-2 mb-0"><?php echo e(__('No salesmen found')); ?></p>
                                    <small class="text-muted"><?php echo e(__('Try adjusting your search or add a new salesman')); ?></small>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
// Search functionality
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#salesmenTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\New target\target-system\resources\views/salesmen/index.blade.php ENDPATH**/ ?>