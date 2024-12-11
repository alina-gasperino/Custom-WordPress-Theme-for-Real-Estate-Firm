<?php

/**
 * Template Part: Title
 *
 * @since 1.4.4
 */

?>

<?php if( is_single() ): ?>

	<?php the_title( '<h1 class="entry-title" itemprop="headline">', '</h1>' ); ?>

<?php else: ?>

	<h2 class="entry-title entry-title--loop" itemprop="headline">
		<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" rel="bookmark">
			<?php the_title(); ?>
		</a>
	</h2>

<?php endif; ?>
