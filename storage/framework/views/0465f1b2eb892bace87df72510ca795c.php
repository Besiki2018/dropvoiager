
<?php $__env->startSection('content'); ?>
    <div class="row y-gap-20 justify-between items-end pb-60 lg:pb-40 md:pb-32">
        <div class="col-auto">
            <h1 class="text-30 lh-14 fw-600"> <?php echo e(__("Change Password")); ?></h1>
            <div class="text-15 text-light-1"><?php echo e(__('Lorem ipsum dolor sit amet, consectetur.')); ?></div>
        </div>
        <div class="col-auto"></div>
    </div>
    <div class="bravo-list-item py-30 px-30 rounded-4 bg-white shadow-3 col-md-6">
        <form action="<?php echo e(route("user.change_password.update")); ?>" method="post">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label><?php echo e(__("Current Password")); ?></label>
                <input type="password" required name="current-password" placeholder="<?php echo e(__("Current Password")); ?>" class="form-control">
            </div>
            <div class="form-group">
                <label><?php echo e(__("New Password")); ?></label>
                <input type="password" required name="new-password" minlength="8" placeholder="<?php echo e(__("New Password")); ?>" class="form-control">
                <p><i><?php echo e(__("* Require at least one uppercase, one lowercase letter, one number and one symbol.")); ?></i></p>
            </div>
            <div class="form-group">
                <label><?php echo e(__("New Password Again")); ?></label>
                <input type="password" required name="new-password_confirmation" minlength="8" placeholder="<?php echo e(__("New Password Again")); ?>" class="form-control">
            </div>
            <?php echo $__env->make('admin.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="d-flex justify-content-between mt-3">
                <button class="button h-50 px-24 -dark-1 bg-blue-1 text-white" type="submit"><i class="fa fa-save mr-2"></i> <?php echo e(__('Change Password')); ?></button>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('js'); ?>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.user', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/User/Views/frontend/changePassword.blade.php ENDPATH**/ ?>