<?php
get_header();
wp_enqueue_script( 'filter-activity-js' );

?>
	<div id="st-content-wrapper" class="search-result-page st-tours">
		<?php echo st()->load_template( 'layouts/modern/hotel/elements/banner' ); ?>
		<div class="full-map hidden-xs hidden-sm">
			<?php echo st()->load_template( 'layouts/modern/activity/elements/search-form' ); ?>
		</div>
		<div class="container">
			<div class="st-hotel-result" id="sticky-halfmap">
				<div class="row">
					<?php

					echo st()->load_template('layouts/modern/activity/elements/top-filter/top-filter');

					$query       = [
						'post_type'   => 'st_activity',
						'post_status' => 'publish',
						's'           => '',
						'orderby'     => 'date',
						'order'       => 'DESC',
					];
					$is_featured = st()->get_option( 'is_featured_search_activity', 'off' );
					if ( ! empty( $is_featured ) && $is_featured == 'on' ) {
						$query['meta_query'] = [
							'relation' => 'OR',
							[
								'key'     => 'is_featured',
								'compare' => 'EXISTS',
							],
							[
								'key'     => 'is_featured',
								'compare' => 'NOT EXISTS',
							],
						];
						$query['orderby']    = 'meta_value date';
					}

					global $wp_query , $st_search_query;
					$activity = STActivity::inst();
					$activity->alter_search_query();
					query_posts( $query );
					$st_search_query = $wp_query;
					$activity->remove_alter_search_query();
					wp_reset_query();
					echo st()->load_template( 'layouts/modern/activity/elements/content-halfmap' );

					?>
				</div>
			</div>
		</div>
	</div>
<?php
echo st()->load_template( 'layouts/modern/hotel/elements/popup/date' );
echo st()->load_template( 'layouts/modern/hotel/elements/popup/guest' );
get_footer();
