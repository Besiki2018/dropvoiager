<div class="bravo-call-to-action <?php echo e($style); ?>">
    <?php switch($style):
        case ("style_2"): ?>
            <?php echo $__env->make("Template::frontend.blocks.call-to-action.style-2", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php break; ?>
        <?php case ("style_3"): ?>
            <?php echo $__env->make("Template::frontend.blocks.call-to-action.style-3", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php break; ?>
        <?php default: ?>
            <?php echo $__env->make("Template::frontend.blocks.call-to-action.style-normal", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endswitch; ?>
</div>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/Base/Template/Views/frontend/blocks/call-to-action/index.blade.php ENDPATH**/ ?>