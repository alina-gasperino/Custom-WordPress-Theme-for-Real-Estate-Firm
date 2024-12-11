<?php

/**
 * Template Part: Carousel
 *
 * @since 1.4.4
 */

?>

<div class="project">

	<a href="<?php the_permalink(); ?>">

		<figure>
			<?php
			$thumbnails = wp_get_attachment_image_src( get_post_thumbnail_id(), 'project_small' );
			if ( is_array($thumbnails) && !empty($thumbnails) && !is_local() ) :
				echo '<img src="' . $thumbnails[0] . '">';
			else :
				echo '<img src="' . get_template_directory_uri() . '/assets/images/layout/condo-placeholder.jpg">';
			endif;
			?>
		</figure>

		<figcaption>

			<h4 class="condo-title"><?php the_title() ?></h4>

			<p class="condo-area">
				<?php
				$neighbourhood = custom_cat_text('neighbourhood');
				$city = custom_cat_text('city');

				if( isset( $neighbourhood, $city ) ){
					echo $neighbourhood . ', ' . $city;
				} else {
					echo ( $neighbourhood ) ? $neighbourhood : '';
					echo ( $city ) ? $city : '';
				} ?>
			</p>

			<?php $pricedfrom = project_pricedfrom(get_the_ID()) ?>
			<?php $avg_price = project_avg_price_per_sqft(get_the_ID()) ?>
			<?php $available = project_available_floorplans(get_the_ID()) ?>

			<?php if ($pricedfrom || ($avg_price && get_field('platinum_access') == false )): ?>
			<p>
				<?php if ($pricedfrom): ?>
					<span>From <?= $pricedfrom ?></span>
				<?php endif ?>

				<?php if ($pricedfrom && ($avg_price && get_field('platinum_access') == false )): ?>
					&middot;
				<?php endif ?>

				<?php if ( $avg_price && get_field('platinum_access') == false ): ?>
					<span>$<?php echo $avg_price; ?>/ft Avg.</span>
				<?php endif; ?>
			</p>
			<?php endif ?>

			<?php if (get_field('carouseltextoverride')): ?>
				<p class="announcement"><?= get_field('carouseltextoverride') ?></p>
			<?php else: ?>
				<p>
					<span class="condo-floorplan-availability">
						<?php if ( get_field('platinum_access') ):
							esc_html_e( 'Floor Plans Not Public', 'talkcondo' );
						elseif ( $count = count( $available ) ):
							printf( esc_html( _n( '%d Condo for Sale', '%d Condos for Sale', $count, 'talkcondo' ) ), $count );
						elseif ( $sold_out = project_soldout_floorplans( get_the_ID() ) ):
							esc_html_e( 'Floor Plans Sold Out', 'talkcondo' );
						else :
							esc_html_e( 'Floor Plans Coming Soon', 'talkcondo' );
						endif; ?>
					</span>
				</p>
			<?php endif ?>

		</figcaption>

	</a>

</div>
