<?php
//if(!setting_item('tour_enable_inbox')) return;
$vendor = $row->author;
?>
<?php if(!empty($vendor->id)): ?>
<div class="owner-info widget-boxrounded-4 border-light shadow-4 bg-white w-100 mb-20 rounded-1">
    <div class="media d-flex">
        <div class="media-left mr-5 pt-1">
            <a href="<?php echo e(route('user.profile',['id'=>$vendor->user_name ?? $vendor->id])); ?>" class="avatar-cover" style="background-image: url('<?php echo e($vendor->getAvatarUrl()); ?>');background-size: cover" >
            </a>
        </div>
        <div class="media-body">
            <h4 class="media-heading"><a class="author-link" href="<?php echo e(route('user.profile',['id'=>$vendor->user_name ?? $vendor->id])); ?>"><?php echo e($vendor->getDisplayName()); ?></a>
                <?php if($vendor->is_verified): ?>
                    <img data-toggle="tooltip" data-placement="top" src="<?php echo e(asset('icon/ico-vefified-1.svg')); ?>" title="<?php echo e(__("Verified")); ?>" alt="<?php echo e(__("Verified")); ?>">
                <?php else: ?>
                    <img data-toggle="tooltip" data-placement="top" src="<?php echo e(asset('icon/ico-not-vefified-1.svg')); ?>" title="<?php echo e(__("Not verified")); ?>" alt="<?php echo e(__("Verified")); ?>">
                <?php endif; ?>
            </h4>
            <p><?php echo e(__("Member Since :time",["time"=> date("M Y",strtotime($vendor->created_at))])); ?></p>
            <?php if((!Auth::check() or Auth::id() != $row->author_id ) and setting_item('inbox_enable')): ?>
                <a class="btn bc_start_chat btn bc_start_chat button -dark-1 py-5  rounded-4 bg-blue-1 text-white cursor-pointer btn-primary" href="<?php echo e(route('user.chat',['user_id'=>$row->author_id])); ?>" ><i class="fa fa-wechat me-1"></i> <?php echo e(__('Message host')); ?></a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Layout/common/detail/vendor.blade.php ENDPATH**/ ?>