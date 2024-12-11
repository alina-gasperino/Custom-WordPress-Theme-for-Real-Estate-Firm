<?php

/**
 * Single
 *
 * The single post template. Used when a single post is queried.
 * For this and all other query templates, index.php is used if the query template is not present.
 *
 * @link http://codex.wordpress.org/Theme_Development
 */

?>


<?php
	global $avia_config;

	/*
	 * get_header is a basic wordpress function, used to retrieve the header.php file in your theme directory.
	 */
	 get_header();

	$title  = __('Blog - Latest News', 'avia_framework'); //default blog title
	$t_link = home_url('/');
	$t_sub = "";

	if(avia_get_option('frontpage') && $new = avia_get_option('blogpage'))
	{
		$title 	= get_the_title($new); //if the blog is attached to a page use this title
		$t_link = get_permalink($new);
		$t_sub =  avia_post_meta($new, 'subtitle');
	}

	if( get_post_meta(get_the_ID(), 'header', true) != 'no') echo avia_title(array('heading'=>'strong', 'title' => $title, 'link' => $t_link, 'subtitle' => $t_sub));

?>

<div class='container_wrap container_wrap_first main_color <?php avia_layout_class( 'main' ); ?>'>

	<div class='container template-blog template-single-blog '>

		<main class='site-content units <?php avia_layout_class( 'content' ); ?> <?php echo avia_blog_class_string(); ?>' <?php avia_markup_helper(array('context' => 'content','post_type'=>'post'));?>>

			<?php
			/* Run the loop to output the posts.
			* If you want to overload this in a child theme then include a file
			* called loop-index.php and that will be used instead.
			*
			*/

				get_template_part( 'library/legacy/includes/loop', 'index' );

				//show related posts based on tags if there are any
				get_template_part( 'library/legacy/includes/related-posts');

				//wordpress function that loads the comments template "comments.php"
				// comments_template();

			?>

		<!--end content-->
		</main>

		<?php
		$avia_config['currently_viewing'] = "blog";
		//get the sidebar
		get_sidebar();


		?>


	</div><!--end container-->

		<?php get_template_part( 'library/legacy/includes/projects-mentioned' ); ?>

</div><!-- close default .container_wrap element -->

<?php get_footer(); ?>


<?php /*

<div class="wrapper">

	<div class="container">

		<div class="row">

			<div class="<?php AE_Structure::layout(); ?>">

				<main class="site-content" itemprop="mainEntity" itemscope itemtype="http://schema.org/Blog">

					<?php get_template_part( 'templates/layout/breadcrumb' ); ?>

					<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

						<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry-post' ); ?> itemscope itemtype="http://schema.org/BlogPosting" itemprop="blogPost">

							<header class="entry-header">

								<?php get_template_part( 'templates/entry/common/title' ); ?>

								<?php get_template_part( 'templates/entry/common/meta' ); ?>

							</header>

							<?php get_template_part( 'templates/entry/common/thumbnail' ); ?>

							<?php get_template_part( 'templates/entry/single/content' ); ?>

							<?php get_template_part( 'templates/entry/single/footer' ); ?>

							<?php comments_template(); ?>

						</article>

					<?php endwhile; else : ?>

						<?php get_template_part( 'error' ); ?>

					<?php endif; ?>

				</main>

			</div>

			<?php get_sidebar(); ?>

		</div>

	</div>

</div> */ ?>