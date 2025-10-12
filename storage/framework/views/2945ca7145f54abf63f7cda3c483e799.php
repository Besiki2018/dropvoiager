<?php
    if(!empty($translation->include)){
        $title = __("Included");
    }
    if(!empty($translation->exclude)){
        $title = __("Excluded");
    }
    if(!empty($translation->exclude) and !empty($translation->include)){
        $title = __("Included/Excluded");
    }
?>
<?php if(!empty($title)): ?>
    <h3 class="text-22 fw-500"><?php echo e($title); ?></h3>

    <div class="row x-gap-40 y-gap-40 pt-20">
        <?php if($translation->include): ?>
            <div class="col-md-6">
                <?php $__currentLoopData = $translation->include; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="text-dark-1 text-15">
                        <i class="icon-check text-10 mr-10"></i> <?php echo e($item['title']); ?>

                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <?php if($translation->exclude): ?>
            <div class="col-md-6">
                <?php $__currentLoopData = $translation->exclude; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="text-dark-1 text-15">
                        <i class="icon-close text-green-2 text-10 mr-10"></i> <?php echo e($item['title']); ?>

                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Tour/Views/frontend/layouts/details/tour-include-exclude.blade.php ENDPATH**/ ?>