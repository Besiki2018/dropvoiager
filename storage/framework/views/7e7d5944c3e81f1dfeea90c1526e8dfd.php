<section class="layout-pt-md layout-pb-md bravo-list-locations <?php if(!empty($layout)): ?> <?php echo e($layout); ?> <?php endif; ?>">
    <div data-anim-wrap class="container">
        <div data-anim-child="slide-up delay-1" class="row y-gap-20 justify-between items-end">
            <div class="col-auto">
                <div class="sectionTitle -md">
                    <h2 class="sectionTitle__title"><?php echo e($title ?? ''); ?></h2>
                    <p class=" sectionTitle__text mt-5 sm:mt-0"><?php echo e($desc ?? ''); ?></p>
                </div>
            </div>

            <?php if(!empty($view_all_url)): ?>
            <div class="col-auto">
                <a href="<?php echo e($view_all_url); ?>" class="button -md -blue-1 bg-blue-1-05 text-blue-1">
                    <?php echo e(__('More')); ?> <div class="icon-arrow-top-right ml-15"></div>
                </a>
            </div>
            <?php endif; ?>
        </div>

        <?php if(!empty($rows)): ?>
            <div class="row y-gap-30 pt-40 sm:pt-20">
                <?php $delay = 2; ?>
                <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $translation = $row->translate(); ?>
                    <div data-anim-child="slide-up delay-<?php echo e($delay); ?>" class="col-xl-3 col-lg-4 col-md-6">
                        <a href="<?php echo e($row->getDetailUrl()); ?>" class="destCard -type-1 d-block">
                            <div class="row x-gap-20 y-gap-20 items-center">
                                <div class="col-auto">
                                    <div class="destCard__image rounded-4">
                                        <?php if($row->image_id): ?>
                                        <img class="size-100 rounded-4" src="<?php echo e(get_file_url($row->image_id)); ?>" alt="<?php echo e($translation->name ?? ''); ?>">
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-auto">
                                    <div class="text-18 fw-500"><?php echo e($translation->name); ?></div>
                                    <?php if(is_array($service_type)): ?>
                                        <?php $count_all = 0; ?>
                                        <?php $__currentLoopData = $service_type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php $count_all += (int) $row->getDisplayNumberServiceInLocation($type) ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(!empty($count_all)): ?>
                                            <span class="text-14 lh-14 text-light-1 mt-5"><?php echo e($count_all); ?> <?php echo e(__("travellers")); ?></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if(!empty($text_service = $row->getDisplayNumberServiceInLocation($service_type))): ?>
                                            <span class="text-14 lh-14 text-light-1 mt-5"><?php echo e($text_service); ?></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php $delay++ ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Location/Views/frontend/blocks/list-locations/style_5.blade.php ENDPATH**/ ?>