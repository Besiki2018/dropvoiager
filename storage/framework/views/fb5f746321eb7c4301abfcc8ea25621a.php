<?php
    /**
    * @var $row \Modules\Location\Models\Location
    * @var $to_location_detail bool
    * @var $service_type string
    */
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
<div class="swiper-slide">
    <span class="citiesCard -type-1 d-block rounded-4 ">
        <?php if(!empty($link_location)): ?> <a href="<?php echo e($link_location); ?>"> <?php endif; ?>
            <div class="citiesCard__image ratio ratio-3:4">
                <?php if($row->image_id): ?>
                    <img src="#" data-src="<?php echo e($row->getImageUrl()); ?>" alt="<?php echo e($translation->name ?? ''); ?>" class="js-lazy">
                <?php endif; ?>
            </div>
            <div class="citiesCard__content d-flex flex-column justify-between text-center pt-30 pb-20 px-20">
                <div class="citiesCard__bg"></div>
                <div class="citiesCard__top">
                    <?php if( !empty($layout) and $layout == "style_1"): ?>
                        <?php if(is_array($service_type)): ?>
                            <div class="text-14 text-white">
                                <?php $__currentLoopData = $service_type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $count = $row->getDisplayNumberServiceInLocation($type) ?>
                                    <?php if(!empty($count)): ?>
                                        <?php if(empty($link_location)): ?>
                                            <a href="<?php echo e($row->getLinkForPageSearch( $type )); ?>" target="_blank" class="d-inline-block me-2">
                                                <?php echo e($count); ?>

                                            </a>
                                        <?php else: ?>
                                            <span class="d-inline-block"><?php echo e($count); ?></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <?php if(!empty($text_service = $row->getDisplayNumberServiceInLocation($service_type))): ?>
                                <div class="text-14 text-white"><?php echo e($text_service); ?></div>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="citiesCard__bottom">
                    <h4 class="text-26 md:text-20 lh-13 text-white mb-20"><?php echo e($translation->name); ?></h4>
                    <?php if(!empty($link_location)): ?>
                        <button class="button col-12 h-60 -blue-1 bg-white text-dark-1" onclick="window.open('<?php echo e($row->getDetailUrl()); ?>','_self')"><?php echo e(__('Discover')); ?></button>
                    <?php endif; ?>
                </div>
            </div>
            <?php if(!empty($link_location)): ?> </a> <?php endif; ?>
    </span>
</div>
<?php /**PATH /home/u618310530/domains/dropvoyage.com/public_html/themes/GoTrip/Location/Views/frontend/blocks/list-locations/loop.blade.php ENDPATH**/ ?>