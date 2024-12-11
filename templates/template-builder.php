<?php

/**
 * Template Name: Builder
 */

?>

<?php get_header(); ?>

	<div class="wrapper">

		<div class="container">

			<main class="site-content" itemprop="mainEntity" itemscope itemtype="http://schema.org/Blog">

				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemtype="http://schema.org/Article">

						<?php if( have_rows('builder') ):

							while ( have_rows('builder') ) : the_row();

								get_template_part( 'templates/builder/' . get_row_layout() );

							endwhile;

						else :

							// No layouts found.

						endif; ?>

						<?php foreach( $builder as $section ){ ?>

							<?php ?>

						<?php } ?>

					</article>

				<?php endwhile; else : ?>

					<?php get_template_part( 'error' ); ?>

				<?php endif; ?>

			</main>

		</div>

	</div>

<?php get_footer(); ?>
