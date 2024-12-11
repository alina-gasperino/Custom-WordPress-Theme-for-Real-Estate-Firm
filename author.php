<?php

/**
 * Author
 *
 * Used for the author archive template.
 */

?>

<?php get_header(); ?>

<div class='main_color fullsize'>

	<div class="container template-blog template-author">

		<main class="site-content av-content-full units">

			<div class='page-heading-container clearfix'>
				<?php get_template_part( 'library/legacy/includes/loop', 'about-author' ); ?>
			</div>

			<h4 class='extra-mini-title widgettitle'>
				<?php printf( __( 'Entries by %s', 'talkcondo' ), get_the_author() ); ?>
			</h4>

			<?php get_template_part( 'library/legacy/includes/loop', 'author' ); ?>

		</main>

	</div>

</div>

<?php get_footer(); ?>

<?php /*

<div class="wrapper">

	<div class="container">

		<div class="row">

			<div class="<?php AE_Structure::layout(); ?>">

				<main class="site-content" itemprop="mainEntity" itemscope itemtype="http://schema.org/Blog">

					<header class="page-header">
						<?php
							the_archive_title( '<h1 class="page-title" itemprop="headline">', '</h1>' );
							the_archive_description( '<div class="archive-description">', '</div>' );
						?>
					</header>

					<?php get_template_part( 'loop' ); ?>

				</main>

			</div>

			<?php get_sidebar(); ?>

		</div>

	</div>

</div>

*/ ?>
