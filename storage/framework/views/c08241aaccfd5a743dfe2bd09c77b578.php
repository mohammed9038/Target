

<?php $__env->startSection('title', __('Active Periods')); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h2 mb-1"><?php echo e(__('Active Periods')); ?></h1>
        <p class="text-muted mb-0"><?php echo e(__('Manage active sales periods')); ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('periods.create')); ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i><?php echo e(__('Add Period')); ?>

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

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <h5 class="mb-0 me-3">
                    <i class="bi bi-calendar me-2"></i><?php echo e(__('Periods List')); ?>

                </h5>
                <small class="text-muted">
                    <?php echo e(count($periods)); ?> <?php echo e(__('records')); ?>

                </small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <form action="<?php echo e(route('periods.index')); ?>" method="GET" class="d-flex align-items-center gap-2">
                    <select class="form-select form-select-sm" id="status" name="status" style="width: 150px;">
                        <option value="open" <?php echo e($status === 'open' ? 'selected' : ''); ?>><?php echo e(__('Open Periods')); ?></option>
                        <option value="closed" <?php echo e($status === 'closed' ? 'selected' : ''); ?>><?php echo e(__('Closed Periods')); ?></option>
                        <option value="all" <?php echo e($status === 'all' ? 'selected' : ''); ?>><?php echo e(__('All Periods')); ?></option>
                    </select>
                    <select class="form-select form-select-sm" id="year" name="year" style="width: 120px;">
                        <option value=""><?php echo e(__('All Years')); ?></option>
                        <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($y); ?>" <?php echo e(request('year') == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <select class="form-select form-select-sm" id="month" name="month" style="width: 130px;">
                        <option value=""><?php echo e(__('All Months')); ?></option>
                        <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($m); ?>" <?php echo e(request('month') == $m ? 'selected' : ''); ?>>
                                <?php echo e(date('M', mktime(0, 0, 0, $m, 1))); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-search me-1"></i><?php echo e(__('Filter')); ?>

                    </button>
                    <a href="<?php echo e(route('periods.index')); ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i><?php echo e(__('Clear')); ?>

                    </a>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="periodsTable">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 px-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar-date me-2 text-muted"></i><?php echo e(__('Year')); ?>

                            </div>
                        </th>
                        <th class="border-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar-month me-2 text-muted"></i><?php echo e(__('Month')); ?>

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
                    <?php $__empty_1 = true; $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $period): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4">
                                <code class="bg-primary-subtle text-primary px-2 py-1 rounded small"><?php echo e($period->year); ?></code>
                            </td>
                            <td>
                                <div class="fw-medium text-dark"><?php echo e(date('F', mktime(0, 0, 0, $period->month, 1))); ?></div>
                            </td>
                            <td>
                                <?php if($period->is_open): ?>
                                    <span class="badge bg-success-subtle text-success px-2">
                                        <i class="bi bi-unlock me-1"></i><?php echo e(__('Open')); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary px-2">
                                        <i class="bi bi-lock me-1"></i><?php echo e(__('Closed')); ?>

                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <form action="<?php echo e(route('periods.update', $period)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <input type="hidden" name="is_open" value="<?php echo e($period->is_open ? '0' : '1'); ?>">
                                        <button type="submit" 
                                                class="btn btn-sm <?php echo e($period->is_open ? 'btn-outline-warning' : 'btn-outline-success'); ?>" 
                                                title="<?php echo e($period->is_open ? __('Close Period') : __('Open Period')); ?>"
                                                data-bs-toggle="tooltip">
                                            <i class="bi <?php echo e($period->is_open ? 'bi-lock' : 'bi-unlock'); ?>"></i>
                                        </button>
                                    </form>
                                    <a href="<?php echo e(route('periods.show', $period)); ?>" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="<?php echo e(__('View')); ?>"
                                       data-bs-toggle="tooltip">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form action="<?php echo e(route('periods.destroy', $period)); ?>" method="POST" class="d-inline">
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
                                    <i class="bi bi-calendar-x" style="font-size: 2rem;"></i>
                                    <p class="mt-2 mb-0"><?php echo e(__('No periods found')); ?></p>
                                    <small class="text-muted"><?php echo e(__('Try adjusting your filters or create a new period')); ?></small>
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
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u925629539/domains/mkalrawi.com/public_html/target-system/resources/views/periods/index.blade.php ENDPATH**/ ?>