<?php $translation = $row->translate(); ?>

<?php if($style == 'style_5'): ?>
    <a href="<?php echo e($row->getDetailUrl()); ?>" class="blogCard -type-2 d-block bg-white rounded-4 shadow-4">
        <div class="blogCard__image">
            <div class="ratio ratio-1:1 rounded-4">
                <?php if($row->image_id): ?>
                    <?php if(!empty($disable_lazyload)): ?>
                        <img class="img-ratio js-lazy" src="#" data-src="<?php echo e(get_file_url($row->image_id,'medium')); ?>" alt="<?php echo e($translation->name ?? ''); ?>">
                    <?php else: ?>
                        <?php echo get_image_tag($row->image_id,'medium',['class'=>'img-ratio js-lazy','alt'=>$row->title]); ?>

                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="px-20 py-20">
            <h4 class="text-dark-1 text-16 lh-18 fw-500"><?php echo clean($translation->title); ?></h4>
            <div class="text-light-1 text-15 lh-14 mt-10"><?php echo e(display_date($row->updated_at)); ?></div>
        </div>
    </a>
<?php elseif(!in_array($style,['style_2','style_4','style_6'])): ?>
    <div class="item-news">
        <a href="<?php echo e($row->getDetailUrl()); ?>" class="blogCard -type-1 d-block ">
            <div class="blogCard__image">
                <div class="ratio ratio-4:3 rounded-4 rounded-8">
                    <?php if($row->image_id): ?>
                        <?php if(!empty($disable_lazyload)): ?>
                            <img class="img-ratio js-lazy" src="#" data-src="<?php echo e(get_file_url($row->image_id,'medium')); ?>" alt="<?php echo e($translation->name ?? ''); ?>">
                        <?php else: ?>
                            <?php echo get_image_tag($row->image_id,'medium',['class'=>'img-ratio js-lazy','alt'=>$row->title]); ?>

                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mt-20">
                <h4 class="text-dark-1 text-18 fw-500"><?php echo clean($translation->title); ?></h4>
                <div class="text-light-1 text-15 lh-14 mt-5"><?php echo e(display_date($row->updated_at)); ?></div>
            </div>
        </a>
    </div>
<?php elseif($style == 'style_4'): ?>
    <a href="<?php echo e($row->getDetailUrl()); ?>" class="blogCard -type-3 ">
        <div class="blogCard__image rounded-4">
            <?php if($row->image_id): ?>
                <?php if(!empty($disable_lazyload)): ?>
                    <img class="object-cover size-130 js-lazy" src="#" data-src="<?php echo e(get_file_url($row->image_id,'medium')); ?>" alt="<?php echo e($translation->name ?? ''); ?>">
                <?php else: ?>
                    <?php echo get_image_tag($row->image_id,'medium',['class'=>'object-cover size-130 js-lazy','alt'=>$row->title]); ?>

                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="blogCard__content px-50 pb-30 lg:px-20 pb-20">
            <h4 class="<?php if(!$k): ?> text-26 <?php else: ?> text-18 <?php endif; ?> lg:text-18 fw-600 lh-16 text-white"><?php echo clean($translation->title); ?></h4>
            <div class="text-15 lh-14 text-white mt-10"><?php echo e(display_date($row->updated_at)); ?></div>
        </div>
    </a>
<?php elseif($style == 'style_6'): ?>
    <a href="<?php echo e($row->getDetailUrl()); ?>" class="row y-gap-20 items-center news-item">
        <div class="col-md-auto col-xs-12">
            <?php if($row->image_id): ?>
                <?php if(!empty($disable_lazyload)): ?>
                    <img class="size-250 size-mb-100 rounded-4" src="#" data-src="<?php echo e(get_file_url($row->image_id,'medium')); ?>" alt="<?php echo e($translation->name ?? ''); ?>">
                <?php else: ?>
                    <?php echo get_image_tag($row->image_id,'medium',['class'=>'size-250 size-mb-100 rounded-4 js-lazy','alt'=>$row->title]); ?>

                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="col">
            <div class="text-15 text-light-1"><?php echo e(display_date($row->updated_at)); ?></div>
            <h4 class="text-22 fw-600 text-dark-1 mt-10"><?php echo clean($translation->title); ?></h4>
            <p class="mt-10"><?php echo clean(\Illuminate\Support\Str::words($row->content,15,'')); ?></p>
        </div>
    </a>
<?php else: ?>
<a href="<?php echo e($row->getDetailUrl()); ?>" class="blogCard -type-1 d-block ">
    <div class="blogCard__image">
        <div class="ratio ratio-1:1 rounded-4 rounded-8">
            <?php if($row->image_id): ?>
                <?php if(!empty($disable_lazyload)): ?>
                    <img class="img-ratio js-lazy" src="#" data-src="<?php echo e(get_file_url($row->image_id,'medium')); ?>" alt="<?php echo e($translation->name ?? ''); ?>">
                <?php else: ?>
                    <?php echo get_image_tag($row->image_id,'medium',['class'=>'img-ratio js-lazy','alt'=>$row->title]); ?>

                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-20">
        <h4 class="text-dark-1 text-18 fw-500"><?php echo clean($translation->title); ?></h4>
        <div class="text-light-1 text-15 lh-14 mt-5"><?php echo e(display_date($row->updated_at)); ?></div>
    </div>
</a>
<?php endif; ?>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/News/Views/frontend/blocks/list-news/loop.blade.php ENDPATH**/ ?>