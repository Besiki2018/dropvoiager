<section class="section-bg layout-pt-lg layout-pb-lg">
    <div class="section-bg__item -right -w-1165 bg-light-2"></div>

    <?php if(!empty($bg_image_1) || !empty($bg_image_2)): ?>
        <?php
            $image_url1 = get_file_url($bg_image_1, 'full');
            $image_url2 = get_file_url($bg_image_2, 'full');
            $class = 'col-sm-6';
            if(empty($image_url1) || empty($image_url2)){
                $class = 'col-sm-12';
            }
        ?>
        <div class="section-bg__item -video-left">
            <div class="row y-gap-30">
                <?php if($image_url1): ?>
                    <div class="<?php echo e($class); ?>">
                        <img src="<?php echo e($image_url1); ?>" alt="image">
                    </div>
                <?php endif; ?>

                <?php if($image_url2): ?>
                    <div class="<?php echo e($class); ?>">
                        <img src="<?php echo e($image_url2); ?>" alt="image">
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="container lg:mt-30">
        <div class="row">
            <div class="offset-xl-6 col-xl-5 col-lg-6">
                <h2 class="text-30 fw-600"><?php echo e($title ?? ''); ?></h2>
                <?php if(!empty($desc)): ?>
                    <p class="text-dark-1 mt-40 lg:mt-20 sm:mt-15"><?php echo e($desc); ?></p>
                <?php endif; ?>

                <?php if(!empty($link_title)): ?>
                <div class="d-inline-block mt-40 lg:mt-30 sm:mt-20">

                    <a href="<?php echo e($link_more ?? ''); ?>" class="button -md -blue-1 bg-yellow-1 text-dark-1">
                        <?php echo e($link_title); ?> <div class="icon-arrow-top-right ml-15"></div>
                    </a>

                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php /**PATH /home/u618310530/domains/dropvoyage.com/public_html/themes/GoTrip/Template/Views/frontend/blocks/text-image/index.blade.php ENDPATH**/ ?>