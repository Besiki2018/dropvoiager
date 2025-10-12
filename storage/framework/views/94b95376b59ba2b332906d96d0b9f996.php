<section class="layout-pt-lg layout-pb-md">
    <div class="container">
        <div class="row y-gap-30 justify-between items-center">
            <div class="col-xl-5 col-lg-6">
                <h2 class="text-30 fw-600"><?php echo e($title ?? ''); ?></h2>
                <p class="mt-40 lg:mt-20"><?php echo e($desc ?? ''); ?></p>

                <div class="d-inline-block mt-40 lg:mt-20">

                    <?php if(!empty($link_title) && !empty($link_more)): ?>
                        <a href="<?php echo e($link_more); ?>" class="button -md -blue-1 bg-dark-1 text-white">
                            <?php echo e($link_title); ?> <div class="icon-arrow-top-right ml-15"></div>
                        </a>
                    <?php endif; ?>

                </div>
            </div>

            <?php if(!empty($list_item)): ?>
            <div class="col-xl-5 col-lg-6">
                <div class="shadow-4">
                    <div class="row border-center">

                        <?php $__currentLoopData = $list_item; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-sm-6">
                            <div class="py-60 sm:py-30 text-center">
                                <div class="text-40 lg:text-30 lh-13 text-dark-1 fw-600"><?php echo e($val['number']); ?></div>
                                <div class="text-14 lh-14 text-light-1 mt-10"><?php echo e($val['title']); ?></div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH /home/u618310530/domains/dropvoyage.com/public_html/themes/GoTrip/Template/Views/frontend/blocks/text-featured-box/index.blade.php ENDPATH**/ ?>