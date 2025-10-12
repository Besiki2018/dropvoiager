<section class="layout-pt-md layout-pb-lg">
    <div data-anim-wrap class="container">
        <div data-anim-child="slide-up delay-1" class="row y-gap-20 justify-between items-end">
            <div class="col-auto">
                <div class="sectionTitle -md">
                    <h2 class="sectionTitle__title"><?php echo e($title); ?></h2>
                    <?php if(!empty($desc)): ?>
                        <p class=" sectionTitle__text mt-5 sm:mt-0"><?php echo e($desc); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if(!empty($link_title) && !empty($link_more)): ?>
                <div class="col-auto">

                    <a href="<?php echo e($link_more); ?>" class="button -md -blue-1 bg-blue-1-05 text-dark-1">
                        <?php echo e($link_title); ?> <div class="icon-arrow-top-right ml-15"></div>
                    </a>

                </div>
            <?php endif; ?>
        </div>

        <div class="row y-gap-30 pt-40">

            <?php $i = 2; ?>
            <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                <div data-anim-child="slide-up delay-<?php echo e($i); ?>" class="col-lg-4 col-sm-6">

                    <?php echo $__env->make('News::frontend.blocks.list-news.loop', ['style' => $style], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                </div>
                <?php $i++; ?>
                <?php if($key == 1): ?>
                    <?php break; ?>
                <?php endif; ?>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <div class="col-lg-4">
                <div class="row y-gap-30">
                    <?php $i = 2; ?>
                    <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $translation = $row->translate();?>
                        <?php if($key > 1): ?>
                            <div data-anim-child="slide-up delay-<?php echo e($i); ?>" class="col-lg-12 col-md-6">
                                <a href="<?php echo e($row->getDetailUrl()); ?>" class="blogCard -type-1 d-flex items-center">
                                    <div class="blogCard__image size-130 rounded-8">
                                        <?php if($row->image_id): ?>
                                            <?php if(!empty($disable_lazyload)): ?>
                                                <img class="object-cover size-130 js-lazy" src="#" data-src="<?php echo e(get_file_url($row->image_id,'medium')); ?>" alt="<?php echo e($translation->name ?? ''); ?>">
                                            <?php else: ?>
                                                <?php echo get_image_tag($row->image_id,'medium',['class'=>'object-cover size-130 js-lazy','alt'=>$row->title]); ?>

                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>

                                    <div class="ml-24">
                                        <h4 class="text-18 lh-14 fw-500 text-dark-1"><?php echo clean($translation->title); ?></h4>
                                        <p class="text-15"><?php echo e(display_date($row->updated_at)); ?></p>
                                    </div>
                                </a>
                            </div>
                            <?php $i++; ?>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </div>
            </div>
        </div>
    </div>
</section>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/News/Views/frontend/blocks/list-news/style_3.blade.php ENDPATH**/ ?>