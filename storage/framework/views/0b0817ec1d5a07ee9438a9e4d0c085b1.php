
<?php $__env->startSection('content'); ?>
    <div class="row y-gap-20 justify-between items-end pb-60 lg:pb-40 md:pb-32">
        <div class="col-auto">
            <h1 class="text-30 lh-14 fw-600"><?php echo e(__("Settings")); ?></h1>
            <div class="text-15 text-light-1"><?php echo e(__('Lorem ipsum dolor sit amet, consectetur.')); ?></div>
        </div>
        <div class="col-auto"></div>
    </div>
    <?php echo $__env->make('admin.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <form action="<?php echo e(route('user.profile.update')); ?>" method="post" class="input-has-icon">
        <?php echo csrf_field(); ?>
        <div class="py-30 px-30 rounded-4 bg-white shadow-3">
        <div class="tabs -underline-2 js-tabs">
            <div class="tabs__controls row x-gap-40 y-gap-10 lg:x-gap-20 js-tabs-controls">
                <div class="col-auto">
                    <span class="tabs__button cursor-pointer text-18 lg:text-16 text-light-1 fw-500 pb-5 lg:pb-0 js-tabs-button is-tab-el-active" data-tab-target=".-tab-item-1"><?php echo e(__("Personal Information")); ?></span>
                </div>
                <div class="col-auto">
                    <span class="tabs__button cursor-pointer text-18 lg:text-16 text-light-1 fw-500 pb-5 lg:pb-0 js-tabs-button " data-tab-target=".-tab-item-2"><?php echo e(__("Location Information")); ?></span>
                </div>
                <div class="col-auto">
                    <a href="<?php echo e(route('user.change_password')); ?>" class="tabs__button text-18 lg:text-16 text-light-1 fw-500 pb-5 lg:pb-0" ><?php echo e(__("Change Password")); ?></a>
                </div>
            </div>
            <div class="tabs__content pt-30 js-tabs-content">
                <div class="tabs__pane -tab-item-1 is-tab-el-active row">
                    <div class="row y-gap-30 items-center upload-btn-wrapper">
                        <div class="col-auto">
                            <div class="d-flex ratio ratio-1:1 w-200">
                                <img class="image-demo img-ratio rounded-4" src="<?php echo e(get_file_url( old('avatar_id',$dataUser->avatar_id) ) ??  $dataUser->getAvatarUrl() ?? ""); ?>"/>
                            </div>
                        </div>
                        <div class="col-auto">
                            <h4 class="text-16 fw-500"><?php echo e(__("Your avatar")); ?></h4>
                            <div class="text-14 mt-5"><?php echo e(__("PNG or JPG no bigger than 800px wide and tall.")); ?></div>
                            <div class="d-inline-block mt-15">
                                <button class="button h-50 px-24 -dark-1 bg-blue-1 text-white btn-file">
                                   <input type="file">
                                </button>
                            </div>
                            <input type="hidden" data-error="<?php echo e(__("Error upload...")); ?>" data-loading="<?php echo e(__("Loading...")); ?>" class="form-control text-view" readonly value="<?php echo e(get_file_url( old('avatar_id',$dataUser->avatar_id) ) ?? $dataUser->getAvatarUrl()?? __("No Image")); ?>">
                            <input type="hidden" class="form-control" name="avatar_id" value="<?php echo e(old('avatar_id',$dataUser->avatar_id)?? ""); ?>">
                        </div>
                    </div>
                    <div class="border-top-light mt-30 mb-30"></div>
                    <div class="col-xl-9">
                        <div class="row x-gap-20 y-gap-20">
                            <?php if($is_vendor_access): ?>
                                <div class="col-12">
                                    <div class="form-input ">
                                        <input type="text" value="<?php echo e(old('business_name',$dataUser->business_name)); ?>" name="business_name">
                                        <label class="lh-1 text-16 text-light-1"><?php echo e(__("Business name")); ?></label>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="col-12">
                                <div class="form-input ">
                                    <input type="text" minlength="4" name="user_name" value="<?php echo e(old('user_name',$dataUser->user_name)); ?>">
                                    <label class="lh-1 text-16 text-light-1"><?php echo e(__('User Name')); ?></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-input ">
                                    <input type="text" value="<?php echo e(old('first_name',$dataUser->first_name)); ?>" name="first_name">
                                    <label class="lh-1 text-16 text-light-1"><?php echo e(__("First Name")); ?></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-input ">
                                    <input type="text" value="<?php echo e(old('last_name',$dataUser->last_name)); ?>" name="last_name">
                                    <label class="lh-1 text-16 text-light-1"><?php echo e(__("Last Name")); ?></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-input ">
                                    <input type="text" name="email" value="<?php echo e(old('email',$dataUser->email)); ?>">
                                    <label class="lh-1 text-16 text-light-1"><?php echo e(__("Email")); ?></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-input ">
                                    <input type="text" value="<?php echo e(old('phone',$dataUser->phone)); ?>" name="phone">
                                    <label class="lh-1 text-16 text-light-1"><?php echo e(__("Phone Number")); ?></label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-input ">
                                    <input type="text" class="date-picker has-value" value="<?php echo e(old('birthday',$dataUser->birthday? display_date($dataUser->birthday) :'')); ?>" name="birthday">
                                    <label class="lh-1 text-16 text-light-1"><?php echo e(__("Birthday")); ?></label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-input ">
                                    <textarea rows="5" class="pt-35" name="bio"><?php echo e(old('bio',$dataUser->bio)); ?></textarea>
                                    <label class="lh-1 text-16 text-light-1"><?php echo e(__("About Yourself")); ?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-inline-block pt-30">
                        <button type="submit" class="button h-50 px-24 -dark-1 bg-blue-1 text-white">
                            <?php echo e(__("Save Changes")); ?>

                            <div class="icon-arrow-top-right ml-15"></div>
                        </button>
                    </div>
                </div>
                <div class="tabs__pane -tab-item-2 row">
                    <div class="col-xl-9">
                        <div class="row x-gap-20 y-gap-20">
                            <div class="col-12">
                                <div class="form-input ">
                                    <input type="text" value="<?php echo e(old('address',$dataUser->address)); ?>" name="address">
                                    <label class="lh-1 text-16 text-light-1"><?php echo e(__("Address Line 1")); ?></label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-input ">
                                    <input type="text" value="<?php echo e(old('address2',$dataUser->address2)); ?>" name="address2">
                                    <label class="lh-1 text-16 text-light-1"><?php echo e(__("Address Line 2")); ?></label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-input ">
                                    <input type="text" value="<?php echo e(old('city',$dataUser->city)); ?>" name="city">
                                    <label class="lh-1 text-16 text-light-1"><?php echo e(__("City")); ?></label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-input ">
                                    <input type="text" value="<?php echo e(old('state',$dataUser->state)); ?>" name="state">
                                    <label class="lh-1 text-16 text-light-1">State</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-input ">
                                    <select name="country" class="form-control">
                                        <option value=""><?php echo e(__('-- Select --')); ?></option>
                                        <?php $__currentLoopData = get_country_lists(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id=>$name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option <?php if((old('country',$dataUser->country ?? '')) == $id): ?> selected <?php endif; ?> value="<?php echo e($id); ?>"><?php echo e($name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <label class="lh-1 text-16 text-light-1"><?php echo e(__("Select Country")); ?></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-input ">
                                    <input type="text" value="<?php echo e(old('zip_code',$dataUser->zip_code)); ?>" name="zip_code">
                                    <label class="lh-1 text-16 text-light-1"><?php echo e(__("ZIP Code")); ?></label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-inline-block">
                                    <button type="submit" class="button h-50 px-24 -dark-1 bg-blue-1 text-white">
                                        <?php echo e(__("Save Changes")); ?>

                                        <div class="icon-arrow-top-right ml-15"></div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>

    <?php if(!empty(setting_item('user_enable_permanently_delete')) and !is_admin()): ?>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <h4 class="text-danger">
                    <?php echo e(__("Delete account")); ?>

                </h4>
                <div class="mb-4 mt-2">
                    <?php echo clean(setting_item_with_lang('user_permanently_delete_content','',__('Your account will be permanently deleted. Once you delete your account, there is no going back. Please be certain.'))); ?>

                </div>
                <a data-toggle="modal" data-target="#permanentlyDeleteAccount" class="btn btn-danger" href=""><?php echo e(__('Delete your account')); ?></a>
            </div>

            <!-- Modal -->
            <div class="modal  fade" id="permanentlyDeleteAccount" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content ">
                        <div class="modal-header">
                            <h5 class="modal-title"><?php echo e(__('Confirm permanently delete account')); ?></h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="my-3">
                                <?php echo clean(setting_item_with_lang('user_permanently_delete_content_confirm')); ?>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo e(__('Close')); ?></button>
                            <a href="<?php echo e(route('user.permanently.delete')); ?>" class="btn btn-danger"><?php echo e(__('Confirm')); ?></a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.user', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u618310530/domains/dropvoyage.com/public_html/themes/GoTrip/User/Views/frontend/profile.blade.php ENDPATH**/ ?>