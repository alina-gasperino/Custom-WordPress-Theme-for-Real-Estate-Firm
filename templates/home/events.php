<?php

/**
 * Template Part: Interviews
 *
 * @since 1.4.4
 */
?>

<?php if (have_rows('events')): ?>

	<div class="condos-group" data-animation="fadeIn">

		<header>
			<h1 class='heading'>Upcoming Events</h1>
		</header>

		<div id="condos-slider--interviews" class="project-carousel condos-slider condos-slider--large condos-slider--events">
			<div class="condos-slider__scroller">
				<?php while (have_rows('events')): the_row(); ?>
					<?php get_template_part( 'templates/home/carousel-events' ); ?>
				<?php endwhile ?>
			</div>
		</div>

	</div>

<?php endif ?>
