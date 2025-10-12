<div class="container">
    <div class="context" <?php if(!empty($bg_image_url)): ?> style="background-image: url(<?php echo e($bg_image_url ?? ""); ?>) !important;" <?php endif; ?>>
        <div class="row">
            <div class="col-md-8">
                <div class="title">
                    <?php echo e($title); ?>

                </div>
                <div class="sub_title">
                    <?php echo e($sub_title); ?>

                </div>
            </div>
            <div class="col-md-4">
                <?php if($link_title): ?>
                    <a class="btn-more" href="<?php echo e($link_more); ?>">
                        <?php echo e($link_title); ?>

                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div><?php /**PATH /home/u618310530/domains/dropvoyage.com/public_html/themes/Base/Template/Views/frontend/blocks/call-to-action/style-2.blade.php ENDPATH**/ ?>