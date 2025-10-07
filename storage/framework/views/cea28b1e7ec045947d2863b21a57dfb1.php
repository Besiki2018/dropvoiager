<section class="header-banner gotrip-topbar py-5 bg-blue-1 z-2">
    <div class="container">
        <div class="row items-center justify-between">
            <div class="col-auto">
                <?php
                $topbar_left_text = setting_item('topbar_left_text');
                ?>
                <?php if(!empty($topbar_left_text)): ?>
                    <?php echo clean($topbar_left_text); ?>

                <?php endif; ?>
            </div>

            <div class="col-auto">
                <div class="row x-gap-15 items-center jusify-between">
                    <div class="col-auto">

                        <div class="row x-gap-20 items-center xxl:d-none">
                            <div class="col-auto">
                                <ul class="gotrip-dropdown">
                                    <?php echo $__env->make('Core::frontend.currency-switcher', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </ul>
                            </div>

                            <div class="col-auto">
                                <div class="w-1 h-20 bg-white-20"></div>
                            </div>

                            <div class="col-auto">
                                <ul class="gotrip-dropdown">
                                    <?php echo $__env->make('Language::frontend.switcher-dropdown', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </ul>
                            </div>
                        </div>

                    </div>

                    <div class="col-auto xxl:d-none">
                        <div class="w-1 h-20 bg-white-20"></div>
                    </div>

                    <div class="col-auto md:d-none">
                        <?php if(!empty($page_vendor = get_page_url ( setting_item('vendor_page_become_an_expert')))): ?>
                            <a href="<?php echo e($page_vendor); ?>" class="text-12 text-white"><?php echo e(__("Become An Expert")); ?></a>
                        <?php endif; ?>
                    </div>

                    <div class="col-auto md:d-none">
                        <div class="w-1 h-20 bg-white-20"></div>
                    </div>

                    <div class="col-auto">
                        <?php if(!Auth::check()): ?>
                            <a data-bs-toggle="modal" href="#login" class="text-12 text-white"><?php echo e(__("Sign In / Register")); ?></a>
                        <?php else: ?>
                            <a href="<?php echo e(route('vendor.dashboard')); ?>" class="text-12 text-white"><?php echo e(__("Hi, :Name",['name'=>Auth::user()->getDisplayName()])); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php /**PATH /home/u618310530/domains/dropvoyage.com/public_html/themes/GoTrip/Layout/parts/topbar.blade.php ENDPATH**/ ?>