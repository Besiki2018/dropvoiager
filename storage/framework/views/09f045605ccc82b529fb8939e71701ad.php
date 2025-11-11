<?php if(count($car_related) > 0): ?>
    <div class="bravo-list-car-related">
        <div class="row justify-between items-end">
            <div class="col-auto">
                <div class="sectionTitle -md">
                    <h2 class="sectionTitle__title"><?php echo e(__("You might also like")); ?></h2>
                    <p class=" sectionTitle__text mt-5 sm:mt-0"></p>
                </div>
            </div>
            <div class="col-auto">
                <a href="<?php echo e(route('car.search', ['location_id' => $row->location_id])); ?>" class="button h-50 px-24 -blue-1 bg-blue-1-05 text-blue-1">
                    <?php echo e(__('See All')); ?> <div class="icon-arrow-top-right ml-15"></div>
                </a>
            </div>
        </div>
        <div class="row">
            <?php $__currentLoopData = $car_related; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-3">
                    <?php echo $__env->make('Car::frontend.layouts.search.loop-grid',['row'=>$item,'include_param'=>0], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Car/Views/frontend/layouts/details/related.blade.php ENDPATH**/ ?>