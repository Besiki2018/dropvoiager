<section class="layout-pt-md layout-pb-md bravo-list-locations <?php if(!empty($layout)): ?> <?php echo e($layout); ?> <?php endif; ?>">
    <div data-anim-wrap class="container">
        <div data-anim-child="slide-up delay-1" class="row y-gap-20 justify-between items-end">
            <div class="col-auto">
                <div class="sectionTitle -md">
                    <h2 class="sectionTitle__title"><?php echo e($title); ?></h2>
                    <p class=" sectionTitle__text mt-5 sm:mt-0"><?php echo e($desc); ?></p>
                </div>
            </div>
            <div class="col-auto">
                <div class="d-flex x-gap-15 items-center justify-center pt-40 sm:pt-20">
                    <div class="col-auto">
                        <button class="d-flex items-center text-24 arrow-left-hover js-places-prev">
                            <i class="icon icon-arrow-left"></i>
                        </button>
                    </div>
                    <div class="col-auto">
                        <div class="pagination -dots text-border js-places-pag"></div>
                    </div>
                    <div class="col-auto">
                        <button class="d-flex items-center text-24 arrow-right-hover js-places-next">
                            <i class="icon icon-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="pt-40 overflow-hidden js-section-slider" data-gap="30" data-slider-cols="xl-5 lg-3 md-2 sm-2 base-1" data-nav-prev="js-places-prev" data-pagination="js-places-pag" data-nav-next="js-places-next">
            <div class="swiper-wrapper">
                <?php if($rows): ?>
                    <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $translation = $row->translate();
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
                        <div data-anim-child="slide-left delay-<?php echo e($key + 3); ?>" class="swiper-slide">
                            <?php if($to_location_detail): ?>
                                <a href="<?php echo e($link_location); ?>">
                            <?php endif; ?>
                                <div class="citiesCard -type-2">
                                    <div class="citiesCard__image rounded-4 ratio ratio-3:4">
                                        <img class="img-ratio rounded-4 js-lazy" data-src="<?php echo e($row->getImageUrl()); ?>" src="#" alt="<?php echo e($translation->name ?? ''); ?>">
                                    </div>
                                    <div class="citiesCard__content mt-10">
                                        <h4 class="text-18 lh-13 fw-500 text-dark-1"><?php echo e($translation->name); ?></h4>
                                        <div class="text-14 text-light-1">
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
                            <?php if($to_location_detail): ?> </a> <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Location/Views/frontend/blocks/list-locations/style_4.blade.php ENDPATH**/ ?>