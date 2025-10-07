<?php
get_header();
wp_enqueue_script( 'filter-activity' );

?>
	<div id="st-content-wrapper" class="st-style-elementor search-result-page activity-layout9 layout5" data-layout="5" data-format="halfmap">
		<?php
			echo stt_elementorv2()->loadView( 'services/activity/components/banner' );
			echo stt_elementorv2()->loadView( 'services/activity/components/top-filter' );
		?>
		<div class="st-results st-hotel-result st-search-tour">
			<div class="row">
				<?php
				$query = [
					'post_type'   => 'st_activity',
					'post_status' => 'publish',
					's'           => '',
				];
				global $wp_query , $st_search_query;
				$activity = STActivity::inst();
				$activity->alter_search_query();
				query_posts( $query );
				$st_search_query = $wp_query;
				$activity->remove_alter_search_query();
				wp_reset_query();

				if ( TravelHelper::is_wpml() ) {
					global $sitepress;
					$sitepress->switch_lang( $current_lang, true );
				}

				echo stt_elementorv2()->loadView( 'services/activity/components/content-halfmap' );

				?>
			</div>
		</div>
	</div>
	<?php
	get_footer();