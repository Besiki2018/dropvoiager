<section class="layout-pt-lg layout-pb-md relative <?php echo e($layout ?? ''); ?>" id="secondSection">
    <div data-anim-wrap class="container">
        <div data-anim-child="slide-up delay-1" class="row y-gap-20 justify-center text-center">
            <div class="col-auto">
                <div class="sectionTitle -md">
                    <h2 class="sectionTitle__title"><?php echo e($title ?? ''); ?></h2>
                    <p class=" sectionTitle__text mt-5 sm:mt-0"><?php echo e($desc ?? ''); ?></p>
                </div>
            </div>
        </div>
        <div class="row x-gap-10 y-gap-10 pt-40 sm:pt-20">
            <?php if($rows): ?>
                <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $translation = $row->translate(); ?>
                    <div data-anim-child="slide-up delay-<?php echo e($k+2); ?>" class="col-xl col-lg-3 col-6">
                        <?php if(!empty($to_location_detail)): ?> <a href="<?php echo e($row->getDetailUrl()); ?>"> <?php endif; ?>
                            <div class="citiesCard -type-5 d-flex items-center sm:flex-column sm:items-start px-20 py-20 sm:px-15 sm:py-20 bg-light-2 rounded-4">
                                <i class="icon-destination text-24"></i>
                                <div class="ml-10 sm:ml-0 sm:mt-10">
                                    <h4 class="text-16 fw-500"><?php echo e($translation->name); ?></h4>
                                </div>
                            </div>
                        <?php if(!empty($to_location_detail)): ?> </a> <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH /home/u618310530/domains/dropvoyage.com/public_html/themes/GoTrip/Location/Views/frontend/blocks/list-locations/style_9.blade.php ENDPATH**/ ?>