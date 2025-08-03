<?php $__env->startSection('title', __('Add Period')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0"><?php echo e(__('Add Period')); ?></h1>
                <a href="<?php echo e(route('periods.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> <?php echo e(__('Back to Periods')); ?>

                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="<?php echo e(route('periods.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="year" class="form-label"><?php echo e(__('Year')); ?> *</label>
                                    <select class="form-select <?php $__errorArgs = ['year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="year" name="year" required>
                                        <option value=""><?php echo e(__('Select Year')); ?></option>
                                        <?php for($y = date('Y') - 2; $y <= date('Y') + 2; $y++): ?>
                                            <option value="<?php echo e($y); ?>" <?php echo e(old('year', date('Y')) == $y ? 'selected' : ''); ?>>
                                                <?php echo e($y); ?>

                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                    <?php $__errorArgs = ['year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="month" class="form-label"><?php echo e(__('Month')); ?> *</label>
                                    <select class="form-select <?php $__errorArgs = ['month'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="month" name="month" required>
                                        <option value=""><?php echo e(__('Select Month')); ?></option>
                                        <?php for($m = 1; $m <= 12; $m++): ?>
                                            <option value="<?php echo e($m); ?>" <?php echo e(old('month', date('n')) == $m ? 'selected' : ''); ?>>
                                                <?php echo e(date('F', mktime(0, 0, 0, $m, 1))); ?>

                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                    <?php $__errorArgs = ['month'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_open" name="is_open" value="1" 
                                       <?php echo e(old('is_open', true) ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="is_open">
                                    <?php echo e(__('Open for Target Entry')); ?>

                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?php echo e(__('Save Period')); ?>

                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u925629539/domains/mkalrawi.com/public_html/target/resources/views/periods/create.blade.php ENDPATH**/ ?>