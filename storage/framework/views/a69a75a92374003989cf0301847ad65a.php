

<?php $__env->startSection('content'); ?>
<div class="layout-pt-lg layout-pb-lg bg-blue-2 header-margin">
    <div class="container">
        <div class="row justify-center bravo-login-form-page bravo-login-page">
            <div class="col-xl-6 col-lg-7 col-md-9">
                <?php echo $__env->make('Layout::admin.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <div class="px-50 py-50 sm:px-20 sm:py-20 bg-white shadow-4 rounded-4">
                    <div class="row">
                        <?php echo $__env->make('Layout::auth.login-form',['captcha_action'=>'login_normal'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u618310530/domains/dropvoyage.com/public_html/themes/GoTrip/resources/views/auth/login.blade.php ENDPATH**/ ?>