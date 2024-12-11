<?php

/**
 * Template Part: Footer
 *
 * @since 1.4.4
 */

?>

<footer class="entry-footer">

	<?php 
	
	wp_link_pages( array(
		'before' => '<p class="entry-pagination">' . __( 'Pages:', 'ascripta' ),
		'after'  => '</p>'
	) );

	the_tags( '
	<p class="entry-tags">
		<span class="entry-tags-title">' . 
			__( 'Tags:', 'ascripta' ) . 
		'</span>', ', ', '
	</p>' );

	if( function_exists( 'get_author_box' ) ){
		get_author_box();
	}

	if( function_exists( 'get_pager' ) ){
		get_pager();
	}

	if( function_exists( 'get_related_posts' ) ){
		get_related_posts();
	} 
	
	?>

</footer>
