<section class="layout-pt-md layout-pb-md bravo-list-locations <?php if(!empty($layout)): ?> <?php echo e($layout); ?> <?php endif; ?>">
    <div class="container">
        <div class="row">
            <div class="col-auto">
                <div class="sectionTitle -md">
                    <h2 class="sectionTitle__title"><?php echo e($title); ?></h2>
                    <p class=" sectionTitle__text mt-5 sm:mt-0"><?php echo e($desc); ?></p>
                </div>
            </div>
        </div>
        <div class="tabs -pills pt-40 js-tabs">
            <div class="tabs__content pt-30 js-tabs-content">
                <div class="row y-gap-20">
                    <?php if($rows): ?>
                        <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                            <div class="w-1/5 lg:w-1/4 md:w-1/3 sm:w-1/2">
                                <div class="d-block">
                                    <?php if(!empty($link_location)): ?> <a href="<?php echo e($link_location); ?>"> <?php endif; ?>
                                        <div class="text-15 fw-500"><?php echo e($translation->name); ?></div>
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
                                    <?php if(!empty($link_location)): ?> </a> <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Location/Views/frontend/blocks/list-locations/style_2.blade.php ENDPATH**/ ?>