<?php

/**
 * Template Name: Home
 *
 * This template is used as the homepage when a page is set from the Reading Settings.
 */

?>

<?php get_header() ?>

<div class="wrapper wrapper--home">

	<main class="home-content" itemprop="mainEntity" itemscope itemtype="http://schema.org/Blog">

		<?php global $wpdb ?>

		<?php get_template_part( 'templates/home/jumbo' ) ?>
		<?php get_template_part( 'templates/home/trending' ) ?>
		<?php get_template_part( 'templates/home/welcome' ) ?>

		<div class="condos">

			<div class="condos-group condos-group--heading">
				<h3 class="h1 condos-heading">
					<?php esc_html_e( 'Browse', 'talkcondo' ) ?> TEST
				</h3>
			</div>

			<?php get_template_part( 'templates/home/condos/condos-picks' ) ?>
			<?php get_template_part( 'templates/home/condos/condos-new' ) ?>
			<?php get_template_part( 'templates/home/events' ) ?>
			<?php get_template_part( 'templates/home/condos/condos-soon' ) ?>
			<?php get_template_part( 'templates/home/condos/condos-soon-carousel' ) ?>
			<?php get_template_part( 'templates/home/condos/condos-upcoming-events' ) ?>
			<?php get_template_part( 'templates/home/condos/condos-incentives' ) ?>

			<?php /* slow queries ... need to investigate
			<?php get_template_part( 'templates/home/condos/condos-deposit-10' ) ?>
			<?php get_template_part( 'templates/home/condos/condos-deposit-15' ) ?>
			*/ ?>

			<?php get_template_part( 'templates/home/searchboxes' ) ?>

			<?php /*
			<?php get_template_part( 'templates/home/condos/condos', 'movein' ) ?>
			<?php get_template_part( 'templates/home/condos/condos', 'penthouse' ) ?>
			*/ ?>

			<?php get_template_part( 'templates/home/condos/condos', 'interviews' ) ?>

			<?php /*
			<?php get_template_part( 'templates/home/floorplans-platinum-access-studio' ) ?>
			<?php get_template_part( 'templates/home/floorplans-platinum-access-1-bedroom' ) ?>
			<?php get_template_part( 'templates/home/floorplans-platinum-access-2-bedroom' ) ?>
			<?php get_template_part( 'templates/home/floorplans-platinum-access-sub-600k' ) ?>
			<?php get_template_part( 'templates/home/floorplans-investor-friendly-2-bedroom' ) ?>
			<?php get_template_part( 'templates/home/floorplans-luxury-condos' ) ?>
			<?php get_template_part( 'templates/home/floorplans-largest-condos' ) ?>
			*/ ?>

		</div>

		<?php // get_template_part( 'templates/home/featured' ) ?>

	</main>

</div>

<?php get_footer() ?>
