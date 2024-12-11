<?php

/**
 * Template Part: Content
 *
 * @since 1.4.4
 */

?>

<section class="<?php echo !AE_Helpers::is_woocommerce() ? 'entry-content' : ''; ?> clearfix" itemprop="text">

	<?php the_content(); ?>

	<?php wp_link_pages( array(
		'before' => '<div class="entry-links">' . esc_html__( 'Pages:', 'ascripta' ),
		'after'  => '</div>',
	) ); ?>

</section>
