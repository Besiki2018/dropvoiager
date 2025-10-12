<div class="bravo-list-tour <?php echo e($style_list); ?>">
    <?php if($style_list == 'normal'): ?>
        <?php echo $__env->make("Tour::frontend.blocks.list-tour.style-normal", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
    <?php if($style_list == "carousel"): ?>
        <?php echo $__env->make("Tour::frontend.blocks.list-tour.style-carousel", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
</div>
<?php /**PATH /home/u618310530/domains/dropvoyage.com/public_html/themes/GoTrip/Tour/Views/frontend/blocks/list-tour/index.blade.php ENDPATH**/ ?>