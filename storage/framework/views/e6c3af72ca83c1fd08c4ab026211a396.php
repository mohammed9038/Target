

<?php $__env->startSection('title', __('Edit Salesman')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0"><?php echo e(__('Edit Salesman')); ?></h1>
                <a href="<?php echo e(route('salesmen.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> <?php echo e(__('Back to Salesmen')); ?>

                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="<?php echo e(route('salesmen.update', $salesman)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        
                                                <div class="mb-3">
                            <label for="salesman_code" class="form-label"><?php echo e(__('Salesman Code')); ?></label>
                            <input type="text" class="form-control" id="salesman_code" value="<?php echo e($salesman->salesman_code); ?>" readonly>
                            <div class="form-text">
                                <i class="bi bi-lock me-1"></i><?php echo e(__('Salesman code is auto-generated and cannot be changed')); ?>

                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="employee_code" class="form-label"><?php echo e(__('Employee Code')); ?> <small class="text-muted">(<?php echo e(__('Optional')); ?>)</small></label>
                            <input type="text" class="form-control <?php $__errorArgs = ['employee_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   id="employee_code" name="employee_code" value="<?php echo e(old('employee_code', $salesman->employee_code)); ?>">
                            <?php $__errorArgs = ['employee_code'];
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
                            <label for="name" class="form-label"><?php echo e(__('Name')); ?> *</label>
                            <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="name" name="name" value="<?php echo e(old('name', $salesman->name)); ?>" required>
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

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="region_id" class="form-label"><?php echo e(__('Region')); ?> *</label>
                                    <select class="form-select <?php $__errorArgs = ['region_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="region_id" name="region_id" required>
                                        <option value=""><?php echo e(__('Select Region')); ?></option>
                                        <?php $__currentLoopData = $regions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($region->id); ?>" <?php echo e(old('region_id', $salesman->region_id) == $region->id ? 'selected' : ''); ?>>
                                                <?php echo e($region->name); ?> (<?php echo e($region->region_code); ?>)
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['region_id'];
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
                                    <label for="channel_id" class="form-label"><?php echo e(__('Channel')); ?> *</label>
                                    <select class="form-select <?php $__errorArgs = ['channel_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="channel_id" name="channel_id" required>
                                        <option value=""><?php echo e(__('Select Channel')); ?></option>
                                        <?php $__currentLoopData = $channels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $channel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($channel->id); ?>" <?php echo e(old('channel_id', $salesman->channel_id) == $channel->id ? 'selected' : ''); ?>>
                                                <?php echo e($channel->name); ?> (<?php echo e($channel->channel_code); ?>)
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['channel_id'];
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
                            <label class="form-label"><?php echo e(__('Classifications')); ?> *</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input <?php $__errorArgs = ['classifications'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           type="checkbox" name="classifications[]" value="food" id="classification_food"
                                           <?php echo e(in_array('food', old('classifications', $salesman->getClassificationListAttribute())) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="classification_food">
                                        <i class="bi bi-diagram-2 me-1 text-success"></i><?php echo e(__('Food')); ?>

                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input <?php $__errorArgs = ['classifications'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           type="checkbox" name="classifications[]" value="non_food" id="classification_non_food"
                                           <?php echo e(in_array('non_food', old('classifications', $salesman->getClassificationListAttribute())) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="classification_non_food">
                                        <i class="bi bi-diagram-2 me-1 text-info"></i><?php echo e(__('Non-Food')); ?>

                                    </label>
                                </div>
                            </div>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i><?php echo e(__('Select one or more classifications for this salesman')); ?>

                            </div>
                            <?php $__errorArgs = ['classifications'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?php echo e(__('Update Salesman')); ?>

                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\New target\target-system\resources\views/salesmen/edit.blade.php ENDPATH**/ ?>