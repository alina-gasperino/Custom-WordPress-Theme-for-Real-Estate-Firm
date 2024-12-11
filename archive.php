<?php

/**
 * Archive
 *
 * The archive template. Used when a category, author, or date is queried.
 * Note that this template will be overridden by category.php, author.php, and date.php for their respective query types.
 *
 * @link http://codex.wordpress.org/Theme_Development#Archive_.28archive.php.29
 */

?>

<?php get_header(); ?>

<div class="main_color fullsize">

	<div class="container template-blog">

		<main class="site-content av-content-full units">

			<div class="category-term-description">
				<?php echo term_description(); ?>
			</div>

			<?php global $posts;

			$post_ids = array();
			foreach( $posts as $post ) {
				$post_ids[] = $post->ID;
			} 

			if( !empty( $post_ids ) ) {

				$atts = array(
					'type' => 'grid',
					'items' => get_option('posts_per_page'),
					'animation' => 'fadeIn',
					'columns' => 3,
					'class' => 'avia-builder-el-no-sibling',
					'paginate' => 'yes',
					'use_main_query_pagination' => 'yes',
					'custom_query' => array( 'post__in'=>$post_ids, 'post_type'=>get_post_types() )
				);

				$blog = new avia_post_slider($atts);
				$blog->query_entries();
				echo "<div class='entry-content-wrapper'>" . $blog->html() . "</div>";

			} else {
				
				get_template_part( 'library/legacy/includes/loop', 'index' );

			} ?>

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