<?php

/**
 * WooCommerce
 *
 * Used for WooCommerce page templates.
 *
 * @link http://docs.woothemes.com/document/third-party-custom-theme-compatibility/
 */

	// TO DO: Execute this template

?>

<?php get_header(); ?>

<div class="wrapper">

	<div class="container">

		<div class="row">

			<div class="<?php // AE_Structure::layout(); ?>">

				<main class="site-content" itemprop="mainEntity" itemscope itemtype="http://schema.org/Blog">

					<?php // get_template_part( 'templates/layout/breadcrumb' ); ?>

					<?php woocommerce_content(); ?>

				</main>

			</div>

			<?php get_sidebar(); ?>

		</div>

	</div>

</div>

<?php get_footer(); ?>
