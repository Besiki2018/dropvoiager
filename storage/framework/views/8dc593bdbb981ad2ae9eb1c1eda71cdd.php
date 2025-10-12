<?php switch($style ?? ''):
    case ('style2'): ?> <?php echo $__env->make("Template::frontend.blocks.offer-block.style2", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> <?php break; ?>
    <?php default: ?> <?php echo $__env->make("Template::frontend.blocks.offer-block.style1", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endswitch; ?>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Template/Views/frontend/blocks/offer-block/index.blade.php ENDPATH**/ ?>