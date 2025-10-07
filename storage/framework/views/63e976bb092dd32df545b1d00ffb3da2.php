<?php echo $__env->make("Layout::parts.topbar", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<header data-add-bg="bg-dark-1" class="header mt-40 js-header <?php echo e($headerStyle); ?>" data-x="header" data-x-toggle="is-menu-opened">
    <div data-anim="fade" class="header__container container gotrip-header-<?php echo e($headerStyle); ?> ">
        <div class="row justify-between items-center">
            <?php
                $logo = setting_item('logo_id');
                $logoDark = setting_item('logo_id_dark');
            ?>
            <div class="col-auto">
                <div class="d-flex items-center">
                    <a href="<?php echo e(url(app_get_locale(false,'/'))); ?>" class="header-logo mr-20" data-x="header-logo" data-x-toggle="is-logo-dark">
                        <?php if($logo): ?>
                            <img class="logo-light" src="<?php echo e(get_file_url($logo,'full')); ?>" alt="<?php echo e(setting_item("site_title")); ?>">
                        <?php endif; ?>
                        <?php if($logoDark): ?>
                            <img class="logo-dark" src="<?php echo e(get_file_url($logoDark,'full')); ?>" alt="<?php echo e(setting_item("site_title")); ?>">
                        <?php endif; ?>
                    </a>
                </div>
            </div>

            <div class="col-auto">
                <div class="d-flex items-center">

                    <div class="header-menu " data-x="mobile-menu" data-x-toggle="is-menu-active">
                        <div class="mobile-overlay"></div>

                        <div class="header-menu__content">
                            <div class="mobile-bg js-mobile-bg"></div>

                            <div class="menu js-navList">
                                <?php $textColor = 'text-white';
                                    generate_menu('primary',[
                                        'walker'=>\Themes\GoTrip\Core\Walkers\MenuWalker::class,
                                        'custom_class' => $textColor,
                                        'desktop_menu' => true,
                                        'enable_mega_menu' => true
                                    ])
                                ?>
                            </div>

                            <div class="mobile-footer px-20 py-20 border-top-light js-mobile-footer">
                            </div>
                        </div>
                    </div>

                    <div class="d-none xl:d-flex x-gap-20 items-center pl-20 text-white" data-x="header-mobile-icons" data-x-toggle="text-white">
                        <div>
                            <?php if(!Auth::check()): ?>
                                <a href="<?php echo e(route('vendor.dashboard')); ?>" class="d-flex items-center icon-user text-inherit text-22"></a>
                            <?php else: ?>
                                <a data-bs-toggle="modal" href="#login" class="d-flex items-center icon-user text-inherit text-22"></a>
                            <?php endif; ?>
                        </div>
                        <div><button class="d-flex items-center icon-menu text-inherit text-20" data-x-click="header, header-logo, header-mobile-icons, mobile-menu"></button></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>
<?php /**PATH /home/u618310530/domains/dropvoyage.com/public_html/themes/GoTrip/Layout/parts/header-style/transparent_v8.blade.php ENDPATH**/ ?>