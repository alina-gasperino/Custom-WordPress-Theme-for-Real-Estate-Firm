<?php

/**
 * Template Part: Excerpt
 *
 * @since 1.4.4
 */

?>

<div class="entry-excerpt" itemprop="text">

	<?php the_excerpt(); ?>

	<div class="entry-more">
		<a href="<?php the_permalink(); ?>" class="btn btn-primary">
			<?php esc_html_e( 'Read More', 'ascripta' ); ?>
		</a>
	</div>
	
</div>
