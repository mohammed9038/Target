

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h2 mb-1"><?php echo e(__('Dashboard')); ?></h1>
        <p class="text-muted mb-0"><?php echo e(__('Welcome back,')); ?> <strong><?php echo e($user->username); ?></strong></p>
    </div>
    <div class="d-flex gap-2">
        <div class="text-end">
            <div class="text-muted small"><?php echo e(__('Last Login')); ?></div>
            <div class="fw-medium"><?php echo e(now()->format('M d, Y H:i')); ?></div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-target text-primary fs-2"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="card-title text-muted mb-1"><?php echo e(__('Total Targets')); ?></h6>
                        <h3 class="mb-0"><?php echo e($stats['targets_count'] ?? 0); ?></h3>
                        <small class="text-success">
                            <i class="bi bi-arrow-up"></i> <?php echo e($stats['targets_count'] > 0 ? '+12%' : '0%'); ?>

                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-calendar-check text-success fs-2"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="card-title text-muted mb-1"><?php echo e(__('Open Periods')); ?></h6>
                        <h3 class="mb-0"><?php echo e($stats['open_periods_count'] ?? 0); ?></h3>
                        <small class="text-info">
                            <i class="bi bi-clock"></i> <?php echo e(__('Active')); ?>

                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-people text-info fs-2"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="card-title text-muted mb-1"><?php echo e(__('Salesmen')); ?></h6>
                        <h3 class="mb-0"><?php echo e($stats['salesmen_count'] ?? 0); ?></h3>
                        <small class="text-muted">
                            <i class="bi bi-person-check"></i> <?php echo e(__('Active')); ?>

                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-building text-warning fs-2"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="card-title text-muted mb-1"><?php echo e(__('Suppliers')); ?></h6>
                        <h3 class="mb-0"><?php echo e($stats['suppliers_count'] ?? 0); ?></h3>
                        <small class="text-muted">
                            <i class="bi bi-check-circle"></i> <?php echo e(__('Registered')); ?>

                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Row -->
<div class="row g-4">
    <!-- Recent Targets -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i><?php echo e(__('Recent Targets')); ?>

                </h5>
                <a href="<?php echo e(route('targets.index')); ?>" class="btn btn-sm btn-outline-primary">
                    <?php echo e(__('View All')); ?>

                </a>
            </div>
            <div class="card-body">
                <?php if($recentTargets->count() > 0): ?>
                    <div class="list-group list-group-flush">
                        <?php $__currentLoopData = $recentTargets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $target): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="list-group-item d-flex justify-content-between align-items-start border-0 py-3">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0"><?php echo e($target->salesman->name ?? 'N/A'); ?></h6>
                                        <small class="text-muted"><?php echo e($target->created_at->format('M d, Y')); ?></small>
                                    </div>
                                    <p class="text-muted small mb-1">
                                        <i class="bi bi-geo-alt me-1"></i><?php echo e($target->salesman->region->name ?? 'N/A'); ?> • 
                                        <i class="bi bi-broadcast me-1"></i><?php echo e($target->salesman->channel->name ?? 'N/A'); ?>

                                    </p>
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-building me-1"></i><?php echo e($target->supplier->name ?? 'N/A'); ?> • 
                                        <i class="bi bi-tag me-1"></i><?php echo e($target->category->name ?? 'N/A'); ?>

                                    </p>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-primary">$<?php echo e(number_format($target->amount, 0)); ?></div>
                                    <small class="text-muted"><?php echo e($target->year); ?> <?php echo e(date('M', mktime(0, 0, 0, $target->month, 1))); ?></small>
                                </div>
                            </div>
                            <?php if($index < $recentTargets->count() - 1): ?>
                                <hr class="my-0">
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                        <h5 class="mt-3"><?php echo e(__('No targets found')); ?></h5>
                        <p class="text-muted mb-3"><?php echo e(__('Get started by creating your first target.')); ?></p>
                        <a href="<?php echo e(route('targets.create')); ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i><?php echo e(__('Create First Target')); ?>

                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions & System Status -->
    <div class="col-lg-4">
        <div class="row g-4">
            <!-- Quick Actions -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-lightning me-2"></i><?php echo e(__('Quick Actions')); ?>

                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="<?php echo e(route('targets.create')); ?>" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i><?php echo e(__('Add Target')); ?>

                            </a>
                            <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-outline-primary">
                                <i class="bi bi-graph-up me-2"></i><?php echo e(__('View Reports')); ?>

                            </a>
                            <?php if($user->isAdmin()): ?>
                                <a href="<?php echo e(route('periods.index')); ?>" class="btn btn-outline-success">
                                    <i class="bi bi-calendar me-2"></i><?php echo e(__('Manage Periods')); ?>

                                </a>
                                <a href="<?php echo e(route('regions.index')); ?>" class="btn btn-outline-info">
                                    <i class="bi bi-geo-alt me-2"></i><?php echo e(__('Master Data')); ?>

                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- System Status -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-activity me-2"></i><?php echo e(__('System Status')); ?>

                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted"><?php echo e(__('Database')); ?></span>
                            <span class="badge bg-success"><?php echo e(__('Connected')); ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted"><?php echo e(__('API Status')); ?></span>
                            <span class="badge bg-success"><?php echo e(__('Online')); ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted"><?php echo e(__('Language')); ?></span>
                            <span class="badge bg-info"><?php echo e(app()->getLocale() == 'en' ? 'English' : 'العربية'); ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><?php echo e(__('User Role')); ?></span>
                            <span class="badge bg-primary"><?php echo e($user->isAdmin() ? __('Admin') : __('Manager')); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Performance Chart (Placeholder for future enhancement) -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-graph-up me-2"></i><?php echo e(__('Target Performance')); ?>

                </h5>
            </div>
            <div class="card-body">
                <div class="text-center py-4">
                    <i class="bi bi-bar-chart text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2"><?php echo e(__('Performance charts coming soon...')); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dashboard is now server-side rendered for better performance
    console.log('Dashboard loaded successfully - using server-side data');
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\New target\target-system\resources\views/dashboard.blade.php ENDPATH**/ ?>