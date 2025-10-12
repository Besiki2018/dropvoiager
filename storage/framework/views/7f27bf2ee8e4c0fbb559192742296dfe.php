
<?php $__env->startSection('title',__("Oops! It looks like you're lost.")); ?>
<?php $__env->startSection('message',!empty($exception->getMessage())? $exception->getMessage() :__("The page you're looking for isn't available. Try to search again or use the go to.")); ?>
<?php $__env->startSection('code',404); ?>
<?php $__env->startSection('image',asset('images/404.svg')); ?>

<?php echo $__env->make('errors.illustrated-layout',['title'=>__('Page not found')], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u618310530/domains/dropvoyage.com/public_html/themes/GoTrip/resources/views/errors/404.blade.php ENDPATH**/ ?>