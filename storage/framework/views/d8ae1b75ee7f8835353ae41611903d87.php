<!-- Results count and sort -->
<div class="row y-gap-10 items-center justify-between">
    <div class="col-auto">
        <div class="text-18 fw-500 result-count">
            <?php if($rows->total() > 1): ?>
                <?php echo __(":count tours found",['count'=>$rows->total()]); ?>

            <?php else: ?>
                <?php echo __(":count tour found",['count'=>$rows->total()]); ?>

            <?php endif; ?>
        </div>
    </div>

    <div class="col-auto">
        <div class="row x-gap-20 y-gap-20">
            <div class="col-auto bc-form-order">
                <?php echo $__env->make('Layout::global.search.orderby',['routeName'=>'tour.search'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
    </div>
</div>

<!--End Filter mobile-->
<div class="ajax-search-result">
    <?php echo $__env->make('Tour::frontend.ajax.search-result', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>

<?php /**PATH /home/u618310530/domains/dropvoyage.com/public_html/themes/GoTrip/Tour/Views/frontend/layouts/search/list-item.blade.php ENDPATH**/ ?>