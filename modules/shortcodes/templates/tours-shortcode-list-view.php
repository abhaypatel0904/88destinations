<?php
/**
 * Template for displaying tours by location shortcode
 *
 * Variables:
 * @var WP_Query $tour_query
 */
$count = 0;
?>
<div class="cw-tour-fe-wrepper">
	<div class="mixed-block cw-fe-holder">

		<?php
		// Split posts into two columns of 3 items each (like your HTML)
		$left_col  = array();
		$right_col = array();

		while ( $tour_query->have_posts() ) :
			$tour_query->the_post();

			$count++;
			$tour_id   = get_the_ID();
			$title     = get_the_title();
			$permalink = get_permalink();
			$image_url = get_the_post_thumbnail_url( $tour_id, 'large' );

			$nights   = get_post_meta( $tour_id, 'nights', true );
			$places   = get_post_meta( $tour_id, 'places_left', true );
            
			$locations = wp_get_post_terms( $tour_id, 'location', array( 'fields' => 'names' ) );
			$location  = ! empty( $locations ) ? implode( ' - ', $locations ) : '';

			// Alternate card style classes
			$card_classes = array( 'card' );
			if ( $count === 1 ) {
				$card_classes[] = 'leisure-left';
			} elseif ( $count === 3 || $count === 4 ) {
				$card_classes[] = 'active-left';
			} elseif ( $count === 5 ) {
				$card_classes[] = 'highlight-left';
			} else {
				$card_classes[] = 'culture-left';
			}

			ob_start();
			?>
			<div class="grid-<?php echo ( $count === 1 || $count === 6 ) ? '12' : '6'; ?>">
				<a href="<?php echo esc_url( $permalink ); ?>">
					<div class="<?php echo esc_attr( implode( ' ', $card_classes ) ); ?>">
						<div class="cw-bg-img zoom"
							style="background-image: url('<?php echo esc_url( $image_url ); ?>'); background-size: cover; background-position: 50% 50%;">
							
							<div class="card-content content-bl">
								<?php if ( ! empty( $places ) ) : ?>
									<p class="content-tl">
										<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/group.png' ); ?>" alt="">
										<?php echo esc_html( $places ) . ' ' . _x( 'places left', 'tour spots left', 'promptly-ai-assistance' ); ?>
									</p>
								<?php endif; ?>

								<h4><?php echo esc_html( $title ); ?></h4>

								<p>
									<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/calendar-silhouette.png' ); ?>" alt="">
									<?php echo esc_html( $nights ) . ' ' . _x( 'nights', 'tour duration', 'promptly-ai-assistance' ); ?>
									<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/web.png' ); ?>" alt="">
									<?php echo esc_html( $location ); ?>
								</p>
							</div>
						</div>
					</div>
				</a>
			</div>
			<?php
			$item_html = ob_get_clean();

			if ( $count <= 3 ) {
				$left_col[] = $item_html;
			} else {
				$right_col[] = $item_html;
			}
		endwhile;
		wp_reset_postdata();
		?>

		<div class="grid-6">
			<div class="cw-row animated row3 fadeInUp">
				<?php echo implode( "\n", $left_col ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>

		<div class="grid-6">
			<div class="cw-row animated row4 fadeInUp">
				<?php echo implode( "\n", $right_col ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
	</div>
</div>
