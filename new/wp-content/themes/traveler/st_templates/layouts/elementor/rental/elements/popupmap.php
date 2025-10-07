<?php
$zoom_map = get_post_meta( get_the_ID(), 'rs_map_room', true );
if ( empty( $zoom_map ) ) {
	$zoom_map = 13;
}

$layout = get_post_meta( get_the_ID(), 'rs_layout_rental', true );
$id_map = $layout == 3 ? 'map-search-form' : 'map-search-form-mb';
?>
<div class="map-view-popup">
	<div class="container view-map-inner">
		<?php echo st()->load_template( 'layouts/elementor/rental/elements/top-filter/top', 'filter', [ 'format' => 'popup' ] ); ?>
		<div class="row page-half-map">
			<div class="col-lg-6 col-md-6 col-right-map col-md-push-6">
				<div class="map-popup-title d-md-none hidden-lg hidden-md">
					<?php echo __( 'Map', 'traveler' ); ?>
					<span class="close-map-view-popup"><?php echo TravelHelper::getNewIcon( 'Ico_close', '#A0A9B2', '20px', '20px' ); ?></span>
				</div>
				<div id="<?= esc_attr( $id_map ) ?>" class="map-full-height" data-disablecontrol="true" data-showcustomcontrol="true" data-zoom="<?php echo esc_attr($zoom_map); ?>"></div>
			</div>
			<div class="col-lg-6 col-md-6 col-left-map col-md-pull-6">
				<?php echo st()->load_template( 'layouts/elementor/common/loader', 'content' ); ?>
				<?php echo st()->load_template( 'layouts/elementor/rental/elements/toolbar', '', [ 'format' => 'popup' ] ); ?>
				<div id="modern-search-result" class="modern-search-result-popup" data-format="halfmap"></div>
				<div class="pagination moderm-pagination" id="moderm-pagination" data-layout="normal"></div>
			</div>
		</div>
	</div>
</div>
