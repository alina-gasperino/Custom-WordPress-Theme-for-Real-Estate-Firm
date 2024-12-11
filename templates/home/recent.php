<?php

/**
 * Template Part: Latest News
 *
 * @since 1.4.4
 */

?>

<div id="recently-updated-projects" data-animation="fadeIn">

	<a name="recently-updated-projects"></a>

	<div class="container">

		<h3 class="title">
			Latest News
		</h3>

		<div class="projects"></div>
		
		<a class='loadmore' href="<?php echo admin_url( 'admin-ajax.php' ); ?>" data-count='5' data-action='project_updates' data-paged='1'>
			Load More...
		</a>
		
	</div>
	
</div>
