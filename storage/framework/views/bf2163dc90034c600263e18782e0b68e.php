<?php if(!empty($location_category) and !empty($translation->surrounding)): ?>
    <?php if(!empty($line_top)): ?>
        <div class="mt-20 mb-40">
            <div class="border-top-light"></div>
        </div>
    <?php endif; ?>
    <div class="gotrip-surrounding">
        <div class="location-title">
            <div class="row x-gap-40 y-gap-40">
                <div class="col-auto">
                    <h3 class="text-22 fw-500"><?php echo e(__("What's Nearby")); ?></h3>
                </div>
            </div>
            <div class="row pt-20">
                <?php $__currentLoopData = $location_category; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(!empty($translation->surrounding[$category->id])): ?>
                        <h6 class="font-weight-bold mb-3"><i class="<?php echo e(clean($category->icon_class)); ?> "></i> <?php echo e($category->location_category_translations->name??$category->name); ?></h6>
                        <?php $__currentLoopData = $translation->surrounding[$category->id]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="row mb-3">
                                <div class="col-lg-4"><?php echo e($item['name']); ?> (<?php echo e($item['value']); ?><?php echo e($item['type']); ?>)</div>
                                <div class="col-lg-8"><?php echo e($item['content']); ?></div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

        </div>
    </div>
    <div class="bravo-hr"></div>
<?php endif; ?><?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Layout/common/detail/surrounding.blade.php ENDPATH**/ ?>