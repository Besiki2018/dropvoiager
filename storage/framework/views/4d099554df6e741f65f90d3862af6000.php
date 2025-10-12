<section class="layout-pt-md layout-pb-lg bravo-list-locations <?php echo e($layout ?? ''); ?>">
    <div data-anim-wrap class="container">
        <div data-anim-child="slide-up delay-1" class="row justify-center text-center">
            <div class="col-auto">
                <div class="sectionTitle -md">
                    <h2 class="sectionTitle__title"><?php echo e($title ?? ''); ?></h2>
                    <p class=" sectionTitle__text mt-5 sm:mt-0"><?php echo e($desc ?? ''); ?></p>
                </div>
            </div>
        </div>
        <div class="row y-gap-40 justify-between pt-40 sm:pt-20">
            <?php if($rows): ?>
                <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $translation = $row->translate();
                    $link_location = false;
                    if(is_string($service_type)){
                        $link_location = $row->getLinkForPageSearch($service_type);
                    }
                    if(is_array($service_type) and count($service_type) == 1){
                        $link_location = $row->getLinkForPageSearch($service_type[0] ?? "");
                    }
                    if($to_location_detail){
                        $link_location = $row->getDetailUrl();
                    }
                    ?>
                    <div data-anim-child="slide-up delay-<?php echo e($k+1); ?>" class="<?php if($k > 1): ?> col-xl-3 <?php else: ?> col-xl-6 <?php endif; ?> col-md-4 col-sm-6 item">
                        <?php if(!empty($link_location)): ?> <a href="<?php echo e($link_location); ?>"> <?php endif; ?>
                            <div class="citiesCard -type-3 rounded-4 d-inline">
                                <div class="citiesCard__image">
                                    <img class="col-12 js-lazy" src="#" data-src="<?php echo e(get_file_url($row->image_id)); ?>" alt="image">
                                </div>
                                <div class="citiesCard__content px-30 py-30">
                                    <h4 class="text-26 fw-600 text-white"><?php echo e($translation->name); ?></h4>
                                    <div class="text-15 text-white">
                                        <?php if(is_array($service_type)): ?>
                                            <?php $__currentLoopData = $service_type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php $count = $row->getDisplayNumberServiceInLocation($type) ?>
                                                <?php if(!empty($count)): ?>
                                                    <?php if(empty($link_location)): ?>
                                                        <a href="<?php echo e($row->getLinkForPageSearch( $type )); ?>" target="_blank">
                                                            <span class="me-2"><?php echo e($count); ?></span>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="me-2"><?php echo e($count); ?></span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            <?php if(!empty($text_service = $row->getDisplayNumberServiceInLocation($service_type))): ?>
                                                <span class="me-2"><?php echo e($text_service); ?></span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php if(!empty($link_location)): ?> </a> <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Location/Views/frontend/blocks/list-locations/style_8.blade.php ENDPATH**/ ?>