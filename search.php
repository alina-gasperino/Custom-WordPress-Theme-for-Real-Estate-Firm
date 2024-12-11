<?php

/**
 * Search
 *
 * Used to display a search form to search for entries.
 *
 * @link http://codex.wordpress.org/Theme_Development#Search_Results_.28search.php.29
 */

?>

<?php
global $avia_config;


	/*
	 * get_header is a basic wordpress function, used to retrieve the header.php file in your theme directory.
	 */
	 get_header();

	 $results = avia_which_archive();
	 echo avia_title(array('title' => $results ));
	 ?>

		<div class='container_wrap container_wrap_first main_color <?php avia_layout_class( 'main' ); ?>'>

			<div class='container'>

				<main class='site-content template-search <?php avia_layout_class( 'content' ); ?> units' <?php avia_markup_helper(array('context' => 'content'));?>>

                    <div class='page-heading-container clearfix'>
                        <section class="search_form_field">
                            <?php
                            echo "<h4>".__('New Search','avia_framework')."</h4>";
                            echo "<p>".__('If you are not happy with the results below please do another search','avia_framework')."</p>";

                            get_search_form();
                            echo "<span class='author-extra-border'></span>";
                            ?>
                        </section>
                    </div>


                    <?php
                    if(!empty($_GET['s']) || have_posts())
                    {
                        echo "<h4 class='extra-mini-title widgettitle'>{$results}</h4>";

                        /* Run the loop to output the posts.
                        * If you want to overload this in a child theme then include a file
                        * called loop-search.php and that will be used instead.
                        */
                        $more = 0;
                        get_template_part( 'library/legacy/includes/loop', 'search' );

                    }

                    ?>

				<!--end content-->
				</main>

				<?php

				//get the sidebar
				$avia_config['currently_viewing'] = 'page';

				get_sidebar();

				?>

			</div><!--end container-->

		</div><!-- close default .container_wrap element -->




<?php get_footer(); ?>

<?php get_header(); ?>

<?php /*

<div class="wrapper">

	<div class="container">

		<div class="row">

			<div class="<?php AE_Structure::layout(); ?>">

				<main class="site-content">

					<h1 class="page-title">
						<span><?php esc_html_e( 'Search Results for:', 'ascripta' ); ?></span> <?php echo esc_attr( get_search_query() ); ?>
					</h1>

					<?php get_template_part( 'loop' ); ?>

				</main>

			</div>

			<?php get_sidebar(); ?>

		</div>

	</div>

</div> */ ?>