<section class="layout-pt-md layout-pb-lg">
    <div data-anim-wrap class="container">
        <?php if(!empty($title) || !empty($desc)): ?>
            <div data-anim-child="slide-up delay-1" class="row y-gap-20 justify-between items-end">
                <div class="col-auto">
                    <div class="sectionTitle -md">
                        <h2 class="sectionTitle__title"><?php echo e($title); ?></h2>
                        <p class=" sectionTitle__text mt-5 sm:mt-0"><?php echo e($desc); ?></p>
                    </div>
                </div>
                <div class="col-auto">
                    <a href="<?php echo e(route('boat.search')); ?>" class="button -md -blue-1 bg-blue-1-05 text-blue-1">
                        <?php echo e(__('More')); ?>

                        <div class="icon-arrow-top-right ml-15"></div>
                    </a>
                </div>
            </div>
        <?php endif; ?>
        <div class="row y-gap-30 pt-40 sm:pt-20">
            <?php
                $index = 2;
                $classes = 'col-xl-3 col-lg-3';
                if($columns == '3'){
                    $classes = 'col-xl-4 col-lg-4';
                }
            ?>
            <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div data-anim-child="slide-up delay-<?php echo e($index); ?>" class="<?php echo e($classes); ?> col-sm-6">
                    <?php echo $__env->make('Boat::frontend.layouts.search.loop-grid-2', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                <?php
                    $index++;
                    if($key == 5){
                        $index = 2;
                    }
                ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Boat/Views/frontend/blocks/list-boat/style_3.blade.php ENDPATH**/ ?>