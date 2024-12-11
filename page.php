<?php

/**
 * Page
 *
 * The page template. Used when an individual Page is queried.
 *
 * @link http://codex.wordpress.org/Theme_Development#Pages_.28page.php.29
 */

?>

<?php get_header(); ?>

	<div class="wrapper">

		<div class="container_wrap main_color">

			<div class="container">

				<div class="row">

					<div class="<?php AE_Structure::layout(); ?>">

						<main class="site-content" itemprop="mainEntity" itemscope itemtype="http://schema.org/Blog">

							<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

								<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemtype="http://schema.org/Article">

									<header class="entry-header">

										<?php get_template_part( 'templates/entry/common/title' ); ?>

									</header>

									<?php get_template_part( 'templates/entry/single/content' ); ?>

									<?php // comments_template(); ?>

								</article>

							<?php endwhile; else : ?>

								<?php get_template_part( 'error' ); ?>

							<?php endif; ?>

						</main>

					</div>

					<?php get_sidebar(); ?>

				</div>

			</div>

		</div>

	</div>

<?php get_footer(); ?>
