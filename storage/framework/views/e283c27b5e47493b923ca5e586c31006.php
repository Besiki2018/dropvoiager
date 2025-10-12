<section class="layout-pt-md layout-pb-lg">
    <div data-anim-wrap class="container">
        <div data-anim-child="slide-up delay-1" class="row y-gap-20 justify-center text-center">
            <div class="col-auto">
                <div class="sectionTitle -md">
                    <h2 class="sectionTitle__title"><?php echo e($title ?? ''); ?></h2>
                    <p class=" sectionTitle__text mt-5 sm:mt-0"><?php echo e($desc ?? ''); ?></p>
                </div>
            </div>
        </div>
        <div class="row y-gap-30 pt-40">
            <?php if($rows): ?>
                <?php $index = 2; ?>
                <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $translation = $row->translate();
                    ?>
                    <div data-anim-child="slide-up delay-<?php echo e($index); ?>" class="col-xl-2 col-lg-3 col-sm-6">
                        <?php if($to_location_detail): ?> <a href="<?php echo e($row->getDetailUrl()); ?>"> <?php endif; ?>
                            <div class="citiesCard -type-4 d-block text-center">
                                <div class="citiesCard__image size-160 rounded-full mx-auto">
                                    <img class="object-cover js-lazy" data-src="<?php echo e($row->getImageUrl()); ?>" src="<?php echo e($row->getImageUrl()); ?>" alt="<?php echo e($translation->name); ?>">
                                </div>
                                <div class="citiesCard__content mt-10">
                                    <h4 class="text-18 lh-13 fw-500 text-dark-1"><?php echo e($translation->name); ?></h4>
                                    <?php if(is_array($service_type)): ?>
                                        <?php $count_all = 0; ?>
                                        <?php $__currentLoopData = $service_type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php $count_all += (int) $row->getDisplayNumberServiceInLocation($type) ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(!empty($count_all)): ?>
                                            <span class="text-14 text-light-1"><?php echo e($count_all); ?> <?php echo e(__("travellers")); ?></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if(!empty($text_service = $row->getDisplayNumberServiceInLocation($service_type))): ?>
                                            <span class="text-14 text-light-1"><?php echo e($text_service); ?></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php if($to_location_detail): ?> </a> <?php endif; ?>
                    </div>
                    <?php $index++; if($key == 7) $index = 2; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Location/Views/frontend/blocks/list-locations/style_6.blade.php ENDPATH**/ ?>