<?php
    $terms_ids = $row->terms->pluck('term_id');
    $attributes = \Modules\Core\Models\Terms::getTermsById($terms_ids);
?>
<?php if(!empty($terms_ids) and !empty($attributes)): ?>
    <?php $__currentLoopData = $attributes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attribute): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $translate_attribute = $attribute['parent']->translate() ?>
        <?php if(empty($attribute['parent']['hide_in_single'])): ?>
            <?php $terms = $attribute['child'] ?>
            <div class="<?php echo e($attribute['parent']->slug); ?> attr-<?php echo e($attribute['parent']->id); ?>">
                <h3 class="text-22 fw-500"><?php echo e($translate_attribute->name); ?></h3>
                <div class="row y-gap-10 pt-15 <?php echo e($attribute['parent']['display_type'] ?? ""); ?>">
                    <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $translate_term = $term->translate() ?>
                        <div class="col-sm-5">
                            <div class="d-flex items-center item <?php echo e($term->slug); ?> term-<?php echo e($term->id); ?>">
                                <?php if(!empty($term->image_id)): ?>
                                    <?php $image_url = get_file_url($term->image_id, 'full'); ?>
                                    <img src="<?php echo e($image_url); ?>" class="img-responsive mr-15" alt="<?php echo e($translate_term->name); ?>">
                                <?php else: ?>
                                    <i class="<?php echo e($term->icon ?? "icon-check"); ?> text-10 mr-15"></i>
                                <?php endif; ?>
                                <div class="text-15"><?php echo e($translate_term->name); ?></div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Car/Views/frontend/layouts/details/attributes.blade.php ENDPATH**/ ?>