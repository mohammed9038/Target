<?php $__env->startSection('title', __('Suppliers')); ?>

<?php $__env->startSection('content'); ?>
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="h2 mb-1"><?php echo e(__('Suppliers')); ?></h1>
        <p class="text-muted mb-0"><?php echo e(__('Manage supplier information and classifications')); ?></p>
    </div>
    <div class="d-flex gap-2" style="margin-top: 0.5rem;">
        <a href="<?php echo e(route('suppliers.create')); ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i><?php echo e(__('Add Supplier')); ?>

        </a>
    </div>
</div>

<!-- Success Alert -->
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

<!-- Error Alert -->
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

<!-- Suppliers Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">
                <i class="bi bi-building me-2"></i><?php echo e(__('Suppliers List')); ?>

            </h5>
            <small class="text-muted"><?php echo e($suppliers->total() ?? count($suppliers)); ?> <?php echo e(__('suppliers total')); ?></small>
        </div>
        <div class="d-flex gap-2">
            <div class="input-group input-group-sm" style="width: 250px;">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" placeholder="<?php echo e(__('Search suppliers...')); ?>" id="searchInput">
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="suppliersTable">
                <thead class="table-light">
                    <tr>
                        <th class="border-0" style="width: 120px;">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-hash me-1 text-muted"></i><?php echo e(__('Supplier Code')); ?>

                            </div>
                        </th>
                        <th class="border-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-building me-1 text-muted"></i><?php echo e(__('Name')); ?>

                            </div>
                        </th>
                        <th class="border-0" style="width: 150px;">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-tags me-1 text-muted"></i><?php echo e(__('Classification')); ?>

                            </div>
                        </th>
                        <th class="border-0" style="width: 120px;">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-gear me-1 text-muted"></i><?php echo e(__('Actions')); ?>

                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div class="fw-medium text-primary"><?php echo e($supplier->supplier_code); ?></div>
                            </td>
                            <td>
                                <div class="fw-medium"><?php echo e($supplier->name); ?></div>
                            </td>
                            <td>
                                <?php if($supplier->classification === 'food'): ?>
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="bi bi-basket me-1"></i><?php echo e(__('Food')); ?>

                                    </span>
                                <?php elseif($supplier->classification === 'non_food'): ?>
                                    <span class="badge bg-info-subtle text-info">
                                        <i class="bi bi-box me-1"></i><?php echo e(__('Non-Food')); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        <i class="bi bi-question-circle me-1"></i><?php echo e(__('Unknown')); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="<?php echo e(route('suppliers.show', $supplier)); ?>" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="<?php echo e(__('View')); ?>">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('suppliers.edit', $supplier)); ?>" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="<?php echo e(__('Edit')); ?>">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="<?php echo e(route('suppliers.destroy', $supplier)); ?>" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('<?php echo e(__('Are you sure you want to delete this supplier?')); ?>')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="<?php echo e(__('Delete')); ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-building" style="font-size: 2rem;"></i>
                                    <p class="mt-2 mb-0"><?php echo e(__('No suppliers found')); ?></p>
                                    <small class="text-muted"><?php echo e(__('Click "Add Supplier" to create your first supplier')); ?></small>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php if(method_exists($suppliers, 'links') && $suppliers->hasPages()): ?>
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    <?php echo e(__('Showing')); ?> <?php echo e($suppliers->firstItem()); ?> <?php echo e(__('to')); ?> <?php echo e($suppliers->lastItem()); ?> 
                    <?php echo e(__('of')); ?> <?php echo e($suppliers->total()); ?> <?php echo e(__('suppliers')); ?>

                </small>
                <?php echo e($suppliers->links()); ?>

            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('suppliersTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    searchInput.addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;
            
            // Skip empty state row
            if (cells.length === 1) continue;
            
            // Search in supplier code and name
            for (let j = 0; j < 2; j++) {
                if (cells[j] && cells[j].textContent.toLowerCase().includes(filter)) {
                    found = true;
                    break;
                }
            }
            
            row.style.display = found ? '' : 'none';
        }
    });
});
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u925629539/domains/mkalrawi.com/public_html/target/resources/views/suppliers/index.blade.php ENDPATH**/ ?>