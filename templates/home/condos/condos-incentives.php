<?php

/**
 * Template Part: Condos Soon
 *
 * @since 1.4.4
 */

$transient = 'condos_query_incentives';
$query = get_transient( $transient );

if( $query === false ) {

	$query = new WP_Query( array(
		'post_type' => 'project',
		'posts_per_page' => 12,
		'tax_query' => array(
			array(
				'taxonomy' => 'salesstatus',
				'field' => 'slug',
				'terms' => 'special-incentives',
			)
		)
	) );

	set_transient( $transient, $query, 12 * HOUR_IN_SECONDS );

}

if ( $query->have_posts() ): ?>

	<div class="condos-group" data-animation="fadeIn">

		<header>
			<h1 class="heading">Special Incentives</h1>
		</header>

		<div id="condos-slider--incentives" class="project-carousel condos-slider gscroll">
			<div class="condos-slider__scroller">
				<?php while ( $query->have_posts() ): $query->the_post(); ?>
					<?php get_template_part( 'templates/home/carousel' ); ?>
				<?php endwhile; ?>
			</div>
		</div>

	</div>

	<?php wp_reset_query(); ?>

<?php endif; ?>
