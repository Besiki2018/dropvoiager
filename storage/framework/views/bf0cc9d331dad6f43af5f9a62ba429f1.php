<div class="bravo-list-item <?php if(!$rows->count()): ?> not-found <?php endif; ?>">

    <?php if($rows->count()): ?>
        <div class="row y-gap-30 pt-20">
            <?php if($rows->total() > 0): ?>
                <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($layout == 'grid'): ?>
                        <div class="col-lg-4 col-sm-6">
                            <?php echo $__env->make('Tour::frontend.layouts.search.loop-grid',['wrap_class'=>'item-loop-wrap'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div>
                    <?php else: ?>
                        <div class="col-12">
                            <?php echo $__env->make('Tour::frontend.layouts.search.loop-list',['wrap_class'=>'item-loop-wrap inner-loop-wrap'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <div class="col-lg-12">
                    <?php echo e(__("Tour not found")); ?>

                </div>
            <?php endif; ?>
        </div>

        <div class="bravo-pagination">
            <?php echo e($rows->appends(request()->except(['_ajax']))->links()); ?>

            <?php if($rows->total() > 0): ?>
                <div class="text-center mt-30 md:mt-10">
                    <div class="text-14 text-light-1"><?php echo e(__("Showing :from - :to of :total tours",["from"=>$rows->firstItem(),"to"=>$rows->lastItem(),"total"=>$rows->total()])); ?></div>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="not-found-box">
            <h3 class="n-title"><?php echo e(__("We couldn't find any tours.")); ?></h3>
            <p class="p-desc"><?php echo e(__("Try changing your filter criteria")); ?></p>

        </div>
    <?php endif; ?>
</div>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Tour/Views/frontend/ajax/search-result-map.blade.php ENDPATH**/ ?>