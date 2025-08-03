<?php $__env->startSection('title', __('Channels')); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="h2 mb-1"><?php echo e(__('Channels')); ?></h1>
        <p class="text-muted mb-0"><?php echo e(__('Manage sales channels')); ?></p>
    </div>
    <div class="d-flex gap-2" style="margin-top: 0.5rem;">
        <a href="<?php echo e(route('channels.create')); ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i><?php echo e(__('Add Channel')); ?>

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

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <h5 class="mb-0 me-3">
                    <i class="bi bi-diagram-3 me-2"></i><?php echo e(__('Channels List')); ?>

                </h5>
                <small class="text-muted">
                    <?php echo e(count($channels)); ?> <?php echo e(__('records')); ?>

                </small>
            </div>
            <div class="input-group" style="width: 250px;">
                <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" class="form-control border-start-0" id="searchInput" placeholder="<?php echo e(__('Search channels...')); ?>">
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="channelsTable">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 px-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-hash me-2 text-muted"></i><?php echo e(__('Channel Code')); ?>

                            </div>
                        </th>
                        <th class="border-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-diagram-3 me-2 text-muted"></i><?php echo e(__('Name')); ?>

                            </div>
                        </th>
                        <th class="border-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-activity me-2 text-muted"></i><?php echo e(__('Status')); ?>

                            </div>
                        </th>
                        <th class="border-0 text-center" style="width: 120px;">
                            <i class="bi bi-gear me-1 text-muted"></i><?php echo e(__('Actions')); ?>

                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $channels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $channel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4">
                                <code class="bg-info-subtle text-info px-2 py-1 rounded small"><?php echo e($channel->channel_code); ?></code>
                            </td>
                            <td>
                                <div class="fw-medium text-dark"><?php echo e($channel->name); ?></div>
                            </td>
                            <td>
                                <?php if($channel->is_active): ?>
                                    <span class="badge bg-success-subtle text-success px-2">
                                        <i class="bi bi-check-circle me-1"></i><?php echo e(__('Active')); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary px-2">
                                        <i class="bi bi-x-circle me-1"></i><?php echo e(__('Inactive')); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="<?php echo e(route('channels.show', $channel)); ?>" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="<?php echo e(__('View')); ?>"
                                       data-bs-toggle="tooltip">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('channels.edit', $channel)); ?>" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="<?php echo e(__('Edit')); ?>"
                                       data-bs-toggle="tooltip">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="<?php echo e(route('channels.destroy', $channel)); ?>" method="POST" class="d-inline">
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
                            <td colspan="4" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-diagram-3" style="font-size: 2rem;"></i>
                                    <p class="mt-2 mb-0"><?php echo e(__('No channels found')); ?></p>
                                    <small class="text-muted"><?php echo e(__('Try adjusting your search or create a new channel')); ?></small>
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
    const rows = document.querySelectorAll('#channelsTable tbody tr');
    
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
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u925629539/domains/mkalrawi.com/public_html/target/resources/views/channels/index.blade.php ENDPATH**/ ?>