<?php if($translation->faqs): ?>
    <div class="bravo-faq-lists">
        <div class="accordion -simple row y-gap-20 js-accordion">
            <?php $__currentLoopData = $translation->faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-12">
                    <div class="accordion__item px-20 py-20 border-light rounded-4">
                        <div class="accordion__button d-flex items-center">
                            <div class="accordion__icon size-40 flex-center bg-light-2 rounded-full mr-20">
                                <i class="icon-plus"></i>
                                <i class="icon-minus"></i>
                            </div>

                            <div class="button text-dark-1"><?php echo e($item['title']); ?></div>
                        </div>

                        <div class="accordion__content">
                            <div class="pt-20 pl-60">
                                <p class="text-15"><?php echo clean($item['content']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH /home/u618310530/domains/dropvoyage.com/public_html/themes/GoTrip/Tour/Views/frontend/layouts/details/tour-faqs.blade.php ENDPATH**/ ?>