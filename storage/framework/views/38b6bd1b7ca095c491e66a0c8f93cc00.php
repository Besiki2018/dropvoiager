<?php
    $terms_ids = $row->terms->pluck('term_id');
    $attributes = \Modules\Core\Models\Terms::getTermsById($terms_ids);
?>
<?php if(!empty($terms_ids) and !empty($attributes)): ?>
    <?php $__currentLoopData = $attributes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $attribute): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $translate_attribute = $attribute['parent']->translate() ?>
        <?php if(empty($attribute['parent']['hide_in_single'])): ?>
            <?php if($key): ?>
                <div class="border-top-light mt-40 mb-40"></div>
            <?php endif; ?>
            <div class="g-attributes <?php echo e($attribute['parent']->slug); ?> attr-<?php echo e($attribute['parent']->id); ?>">
                <h3 class="text-22 fw-500"><?php echo e($translate_attribute->name); ?></h3>
                <?php $terms = $attribute['child'] ?>
                <div class="list-attributes d-flex flex-wrap">
                    <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $translate_term = $term->translate() ?>
                        <div class="item <?php echo e($term->slug); ?> term-<?php echo e($term->id); ?>">
                            <?php if(!empty($term->image_id)): ?>
                                <?php $image_url = get_file_url($term->image_id, 'full'); ?>
                                <img src="<?php echo e($image_url); ?>" class="img-responsive" alt="<?php echo e($translate_term->name); ?>">
                            <?php else: ?>
                                <i class="<?php echo e($term->icon ?? "icon-check text-blue-1 text-14 me-1"); ?>"></i>
                            <?php endif; ?>
                            <?php echo e($translate_term->name); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH /home/u618310530/domains/dropvoyage.com/public_html/themes/GoTrip/Event/Views/frontend/layouts/details/attributes.blade.php ENDPATH**/ ?>