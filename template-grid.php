<?php
global $avia_config;
get_header();
echo avia_title(array('title' => avia_which_archive()));
?>

<div class='container_wrap container_wrap_first main_color <?php avia_layout_class( 'main' ); ?>'>

	<div class='container'>

		<main class='template-page template-portfolio site-content  <?php avia_layout_class( 'content' ); ?> units' <?php avia_markup_helper(array('context' => 'content','post_type'=>'portfolio'));?>>

			<div class="entry-content-wrapper clearfix">

				<h1><?php single_term_title() ?></h1>

				<?php if ( $description = ( get_field( 'custom_description', get_queried_object() ) ) ?: term_description() ): ?>
				<div class="category-term-description">
					<?php echo $description; ?>
				</div>
				<?php endif; ?>

				<?php if ($map_url = get_field('google_map_url', get_queried_object())): ?>
				<div class="category-term-description">
					<?php echo map_shortcode_from_url($map_url) ?>
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

				$grid->use_global_query();

				echo $grid->html();
				?>

			</div>

			<div id="recently-updated-projects">
				<a name='recently-updated-projects'></a>
				<div class="container">
					<h3 class='title'>Recently Updated Projects in <?php single_term_title() ?></h3>
					<?php $taxnoomy = get_query_var('taxonomy'); ?>
					<?php $term = get_query_var($taxnoomy); ?>
					<div class="projects"></div>
					<a class='loadmore' href="<?php echo admin_url('admin-ajax.php') ?>" data-count='4' data-action='project_updates_by_region' data-paged='1' data-taxonomy='<?php echo $taxonomy ?>' data-term='<?php echo $term ?>'>Load More...</a>
				</div>
			</div>


		<?php wp_reset_query(); ?>

		<!--end content-->
		</main>
		<?php

		//get the sidebar
		$avia_config['currently_viewing'] = 'portfolio';
		get_sidebar();

		?>

	</div><!--end container-->

</div><!-- close default .container_wrap element -->


<?php get_footer(); ?>