<?php

/**
 * Template Part: Pagination
 *
 * @since 1.4.4
 */

?>

<?php if( function_exists( 'get_pagination' ) ): ?>

	<nav class="entry-pagination">
		<?php get_pagination(); ?>
	</nav>

<?php endif; ?>
