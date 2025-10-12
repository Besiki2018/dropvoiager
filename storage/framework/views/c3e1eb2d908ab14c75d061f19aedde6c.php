<?php $textColor = $textColor ?? 'text-white' ?>
<div class="d-flex items-center <?php if($headerStyle != 'transparent_v4'): ?> xl:d-none <?php endif; ?> gotrip-header-<?php echo e($headerStyle); ?>">
    <div class="mr-20">
        <button class="d-flex items-center icon-menu <?php echo e($textColor); ?> text-20" data-x-click="desktopMenu"></button>
    </div>
    <a href="<?php echo e(url(app_get_locale(false,'/'))); ?>" class="header-logo mr-20" data-x="header-logo" data-x-toggle="is-logo-dark">
        <?php if($logo): ?>
            <img class="logo-light" src="<?php echo e(get_file_url($logo,'full')); ?>" alt="<?php echo e(setting_item("site_title")); ?>">
        <?php endif; ?>
        <?php if($logoDark): ?>
            <img class="logo-dark" src="<?php echo e(get_file_url($logoDark,'full')); ?>" alt="<?php echo e(setting_item("site_title")); ?>">
        <?php endif; ?>
    </a>
     <?php if($headerStyle == 'transparent_v4'): ?>
        <div class="header-menu " data-x="mobile-menu" data-x-toggle="is-menu-active">
            <div class="mobile-overlay"></div>
            <div class="header-menu__content">
                <div class="mobile-bg js-mobile-bg"></div>
                <div class="menu js-navList">
                    <?php generate_menu('primary',[
                        'walker'=>\Themes\GoTrip\Core\Walkers\MenuWalker::class,
                        'custom_class' => 'text-dark-1',
                        'desktop_menu' => true,
                        "enable_mega_menu" => true
                     ])
                    ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="desktopMenu js-desktopMenu" data-x="desktopMenu" data-x-toggle="is-menu-active">
        <div class="desktopMenu-overlay"></div>
        <div class="desktopMenu__content">
            <div class="mobile-bg js-mobile-bg"></div>
            <div class="px-30 py-20 sm:px-20 sm:py-10 border-bottom-light">
                <div class="row justify-between items-center">
                    <div class="col-auto">
                        <div class="text-20 fw-500"><?php echo e(__('Main Menu')); ?></div>
                    </div>

                    <div class="col-auto">
                        <button class="icon-close text-15" data-x-click="desktopMenu"></button>
                    </div>
                </div>
            </div>
            <div class="h-full px-30 py-30 sm:px-0 sm:py-10">
                <div class="menu js-navList">
                    <?php generate_menu('primary',[
                                            'walker'=>\Themes\GoTrip\Core\Walkers\MenuWalker::class,
                                            'custom_class' => ($row->header_style ?? '' == 'transparent') ? 'text-white' : 'text-dark-1',
                                            'desktop_menu' => true
                                         ])
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Layout/parts/header-style/transparent_v2.blade.php ENDPATH**/ ?>