<?php $__env->startSection('title', __('Edit Supplier')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0"><?php echo e(__('Edit Supplier')); ?></h1>
                <a href="<?php echo e(route('suppliers.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> <?php echo e(__('Back to Suppliers')); ?>

                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="<?php echo e(route('suppliers.update', $supplier)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        
                        <div class="mb-3">
                            <label for="supplier_code" class="form-label"><?php echo e(__('Supplier Code')); ?></label>
                            <input type="text" class="form-control" id="supplier_code" value="<?php echo e($supplier->supplier_code); ?>" readonly>
                            <div class="form-text">
                                <i class="bi bi-lock me-1"></i><?php echo e(__('Supplier code is auto-generated and cannot be changed')); ?>

                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label"><?php echo e(__('Supplier Name')); ?> *</label>
                            <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="name" name="name" value="<?php echo e(old('name', $supplier->name)); ?>" required>
                            <?php $__errorArgs = ['name'];
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

                        <div class="mb-3">
                            <label for="classification" class="form-label"><?php echo e(__('Classification')); ?> *</label>
                            <select class="form-select <?php $__errorArgs = ['classification'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    id="classification" name="classification" required>
                                <option value=""><?php echo e(__('Select Classification')); ?></option>
                                <option value="food" <?php echo e(old('classification', $supplier->classification) === 'food' ? 'selected' : ''); ?>><?php echo e(__('Food')); ?></option>
                                <option value="non_food" <?php echo e(old('classification', $supplier->classification) === 'non_food' ? 'selected' : ''); ?>><?php echo e(__('Non-Food')); ?></option>
                            </select>
                            <?php $__errorArgs = ['classification'];
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

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?php echo e(__('Update Supplier')); ?>

                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u925629539/domains/mkalrawi.com/public_html/target/resources/views/suppliers/edit.blade.php ENDPATH**/ ?>