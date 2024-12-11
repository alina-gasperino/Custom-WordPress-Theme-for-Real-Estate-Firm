<?php

global $avia_config;

get_header(); ?>

<div class="wrapper">

	<div class="container_wrap main_color">

		<div class="container">

			<main class="site-content" itemprop="mainEntity" itemscope itemtype="http://schema.org/Blog">

				<h1>Assignments</h1>

				<?php if ( $description = ( get_field( 'custom_description', get_queried_object() ) ) ?: term_description() ): ?>
					<div class="category-term-description">
						<?php echo $description; ?>
					</div>
				<?php endif; ?>

				<?php 
				
				$params = array(
					'linking' 	=> '',
					'columns' 	=> '4',
					'items'		=> '16',
					'contents' 	=> 'title',
					'sort' 		=> 'yes',
					'paginate' 	=> 'yes',
					'taxonomy'  => 'neighbourhood',
					'set_breadcrumb' => false,
				);

				$grid = new talk_project_grid( $params );

				$query_params = array(
					'post_type' => 'assignment',
					'posts_per_page' => 16
				);

				$entries = new WP_Query($query_params);
				$grid->set_entries( $entries );

				echo $grid->html();

				wp_reset_query(); 
				
				?>
				
			</main>

		</div>

	</div>

</div>	

<?php get_footer(); ?>
