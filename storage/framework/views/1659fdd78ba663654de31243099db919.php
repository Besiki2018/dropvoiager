
<?php $__env->startSection('content'); ?>
    <div class="layout-pt-lg layout-pb-lg bg-blue-2 header-margin">
        <div class="container">
            <div class="row justify-content-center bravo-login-form-page bravo-login-page">
                <div class="col-xl-6 col-lg-7 col-md-9">
                    <div class="px-50 py-50 sm:px-20 sm:py-20 bg-white shadow-4 rounded-4">
                        <h4 class="form-title text-22 fw-500 mb-3"><?php echo e(__('Reset Password')); ?></h4>
                        <div class="row">
                            <?php if(session('status')): ?>
                                <div class="alert alert-success" role="alert">
                                    <?php echo e(session('status')); ?>

                                </div>
                            <?php endif; ?>
                            <form method="POST" action="<?php echo e(route('password.email')); ?>" class="y-gap-20">
                                <?php echo csrf_field(); ?>
                                <div class="col-12">
                                    <div class="form-input">
                                        <input id="email" type="email" class="form-control<?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>" name="email" value="<?php echo e(old('email')); ?>" required>
                                        <label class="lh-1 text-14 text-light-1"><?php echo e(__('E-Mail Address')); ?></label>
                                    </div>
                                    <?php if($errors->has('email')): ?>
                                        <span class="invalid-feedback show mt-1" role="alert">
                                            <strong><?php echo e($errors->first('email')); ?></strong>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="button py-20 -dark-1 bg-blue-1 text-white w-100 form-submit">
                                        <?php echo e(__('Send Password Reset Link')); ?>

                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u618310530/domains/dropvoyage.com/public_html/themes/GoTrip/resources/views/auth/passwords/email.blade.php ENDPATH**/ ?>