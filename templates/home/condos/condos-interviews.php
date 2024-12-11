<?php

/**
 * Template Part: Interviews
 *
 * @since 1.4.4
 */

$query = get_transient( 'condos_query_interviews' );

if( $query === false ) {

	$query = new WP_Query( array(
		'post_type'      => 'post',
		'posts_per_page' => 12,
		'category_name'  => 'videos'
	) );

	set_transient( 'condos_query_interviews', $query, 12 * HOUR_IN_SECONDS );

}

if ( $query->have_posts() ) { ?>

	<div class="condos-group" data-animation="fadeIn">

		<header>
			<h1 class='heading'>TalkCondo Videos</h1>
		</header>

		<div id="condos-slider--interviews" class="project-carousel condos-slider condos-slider--large gscroll">
			<div class="condos-slider__scroller">
				<?php while( $query->have_posts() ) { $query->the_post(); ?>
					<?php get_template_part( 'templates/home/carousel-video' ); ?>
				<?php } ?>
			</div>
		</div>

	</div>

	<?php wp_reset_postdata();

}
