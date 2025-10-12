<section class="layout-pt-xl layout-pb-md">
    <div class="container">
        <div class="row y-gap-20 justify-center text-center">
            <div class="col-auto">
                <div class="sectionTitle -md">
                    <h2 class="sectionTitle__title"><?php echo e($title ?? ''); ?></h2>
                    <p class=" sectionTitle__text mt-5 sm:mt-0"><?php echo e($desc ?? ''); ?></p>
                </div>
            </div>
        </div>

        <div class="row y-gap-30 pt-40">
            <?php if($rows): ?>
                <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $translation = $row->translate() ?>
                    <div class="w-1/5 lg:w-1/3 sm:w-1/2">
                        <a href="<?php echo e($row->getDetailUrl()); ?>">
                            <div class="citiesCard -type-2 ">
                                <div class="citiesCard__image rounded-4 ratio ratio-23:18">
                                    <img class="img-ratio rounded-4 js-lazy" data-src="<?php echo e(get_file_url($row->image_id)); ?>" src="#" alt="<?php echo e($translation->name ?? ''); ?>">
                                </div>
                                <div class="citiesCard__content mt-10">
                                    <h4 class="text-18 lh-13 fw-500 text-dark-1"><?php echo e($translation->name ?? ''); ?></h4>
                                    <?php if(is_array($service_type)): ?>
                                        <?php $count_all = 0; ?>
                                        <?php $__currentLoopData = $service_type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php $count_all += (int) $row->getDisplayNumberServiceInLocation($type) ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(!empty($count_all)): ?>
                                            <span><?php echo e($count_all); ?> <?php echo e(__("travellers")); ?></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if(!empty($text_service = $row->getDisplayNumberServiceInLocation($service_type))): ?>
                                            <span><?php echo e($text_service); ?></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH /home/u618310530/domains/dropvoyage.com/public_html/themes/GoTrip/Location/Views/frontend/blocks/list-locations/style_7.blade.php ENDPATH**/ ?>