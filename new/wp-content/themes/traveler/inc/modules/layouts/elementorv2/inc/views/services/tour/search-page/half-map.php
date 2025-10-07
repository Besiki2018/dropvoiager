<?php
get_header();
wp_enqueue_script( 'filter-tour' );

$sidebar_pos = get_post_meta( get_the_ID(), 'rs_tour_sidebar_pos', true );
if ( empty( $sidebar_pos ) ) {
	$sidebar_pos = 'left';
}
?>
	<div id="st-content-wrapper" class="st-style-elementor search-result-page tour-layout9 layout5" data-layout="5" data-format="halfmap">
		<?php
			echo stt_elementorv2()->loadView( 'services/tour/components/banner' );
			echo stt_elementorv2()->loadView( 'services/tour/components/top-filter' );
		?>
		<div class="st-results st-hotel-result st-search-tour">
			<div class="row">
				<?php
				$query = [
					'post_type'   => 'st_tours',
					'post_status' => 'publish',
					's'           => '',
				];
				global $wp_query , $st_search_query;
				$tour = STTour::get_instance();
				$tour->alter_search_query();
				query_posts( $query );
				$st_search_query = $wp_query;
				$tour->remove_alter_search_query();
				wp_reset_query();

				if ( TravelHelper::is_wpml() ) {
					global $sitepress;
					$sitepress->switch_lang( $current_lang, true );
				}

				echo stt_elementorv2()->loadView( 'services/tour/components/content-halfmap' );

				?>
			</div>
		</div>
	</div>
	<?php
	get_footer();
