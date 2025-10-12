<section class="layout-pt-lg layout-pb-md">
    <div data-anim-wrap class="container">
        <div data-anim-child="slide-up delay-1" class="row justify-center <?php echo e($headerAlign); ?>">
            <div class="col-auto">
                <div class="sectionTitle -md">
                    <h2 class="sectionTitle__title"><?php echo e($title); ?></h2>
                    <?php if(!empty($desc)): ?>
                        <p class=" sectionTitle__text mt-5 sm:mt-0"><?php echo e($desc); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="blog-grid-1 pt-40">
            <?php $stt = 2; ?>
            <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $translation = $row->translate();?>
                <div data-anim-child="slide-up delay-<?php echo e($stt); ?>">
                    <?php echo $__env->make('News::frontend.blocks.list-news.loop', ['style' => $style,'k' => $key], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                <?php $stt++; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </div>
    </div>
</section>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/News/Views/frontend/blocks/list-news/style_4.blade.php ENDPATH**/ ?>