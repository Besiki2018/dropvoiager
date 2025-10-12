<div class="relative">
    <div class="border-test"></div>
    <div class="accordion -map row y-gap-20 js-accordion">
        <?php if($translation->itinerary): ?>
            <?php $__currentLoopData = $translation->itinerary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-12">
                    <div class="accordion__item <?php if($key == 0): ?> js-accordion-item-active <?php endif; ?>">
                        <div class="d-flex">
                            <div class="accordion__icon size-40 flex-center bg-blue-2 text-blue-1 rounded-full">
                                <div class="text-14 fw-500"><?php echo e($key+1); ?></div>
                            </div>
                            <div class="ml-20">
                                <div class="text-16 lh-15 fw-500"><?php echo e($item['title']); ?></div>
                                <div class="text-14 lh-15 text-light-1 mt-5"><?php echo e($item['desc']); ?></div>

                                <div class="accordion__content">
                                    <div class="pt-15 pb-15">
                                        <img src="<?php echo e(!empty($item['image_id']) ? get_file_url($item['image_id'],"full") : ""); ?>" alt="<?php echo e($item['desc']); ?>" class="rounded-4 mt-15">
                                        <div class="text-14 lh-17 mt-15"><?php echo clean($item['content']); ?></div>
                                    </div>
                                </div>
                                <div class="accordion__button">
                                    <button data-open-change-title="<?php echo e(__('See less')); ?>" class="d-block lh-15 text-14 text-blue-1 underline fw-500 mt-5">
                                        <?php echo e(__('See details & photo')); ?>

                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Tour/Views/frontend/layouts/details/tour-itinerary.blade.php ENDPATH**/ ?>