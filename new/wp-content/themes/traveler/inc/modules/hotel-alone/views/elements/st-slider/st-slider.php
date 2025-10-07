<?php
extract( $data );
$style = sanitize_file_name( $style );
?>

<div class="helios-slider st-<?php echo esc_attr( $style ) ?>">
	<?php echo st_hotel_alone_load_view( 'elements/st-slider/style/' . $style, false, [ 'data' => $data ] ); ?>
</div>
