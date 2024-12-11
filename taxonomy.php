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

				$grid = new talk_project_grid( array(
					'linking' 	=> '',
					'columns' 	=> '4',
					'items'		=> '16',
					'contents' 	=> 'title',
					'sort' 		=> 'yes',
					'paginate' 	=> 'yes',
					'taxonomy'  => 'neighbourhood',
					'set_breadcrumb' => false,
				) );

				$grid->use_global_query();

				echo $grid->html();

				?>

			</div>

		<!--end content-->
		</main>
		<?php

		//get the sidebar
		$avia_config['currently_viewing'] = 'portfolio';
		get_sidebar();

		?>

	</div>

</div>


<?php get_footer(); ?>