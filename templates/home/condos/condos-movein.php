<?php

/**
 * Template Part: Move In Now
 *
 * Complete and at least 1 floor plan available.
 *
 * @since 1.4.4
 */

$query = get_transient( 'condos_query_movein' );

if( $query === false ) {

	$query = new WP_Query( array(
		'post_type'      => 'project',
		'posts_per_page' => 12,
		'meta_query' => array(
			array(
				'key'	  => 'floorplans_$_availability',
				'value'	  => 'Available',
			)
		),
		'tax_query' => array(
			array(
				'taxonomy' => 'status',
				'field'    => 'slug',
				'terms'    => 'complete',
			)
		),
		'orderby' => 'modified',
		'order'   => 'DESC'
	) );

	set_transient( 'condos_query_movein', $query, 12 * HOUR_IN_SECONDS );

}

if ( $query->have_posts() ): ?>

	<div class="condos-group" data-animation="fadeIn">

		<header>
			<h1 class='heading'>Move in Now</h1>
		</header>

		<div id="condos-slider--movein" class="project-carousel condos-slider gscroll">
			<div class="condos-slider__scroller">
				<?php while( $query->have_posts() ): $query->the_post(); ?>
					<?php get_template_part( 'templates/home/carousel' ); ?>
				<?php endwhile; ?>
			</div>
		</div>

	</div>

	<?php wp_reset_query(); ?>

<?php endif; ?>
