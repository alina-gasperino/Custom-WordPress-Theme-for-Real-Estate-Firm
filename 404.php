<?php

/**
 * 404 Error
 *
 * The 404 Not Found template. Used when WordPress cannot find a post or page that matches the query.
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 */

?>

<?php get_header(); ?>

<div class="wrapper">

    <div class="container_wrap main_color">

        <div class="container">

            <div class="row">

                <div class="<?php AE_Structure::layout(); ?>">

                    <main class="site-content" itemprop="mainEntity" itemscope itemtype="http://schema.org/Blog">

                        <?php get_template_part( 'error' ); ?>

                    </main>

                </div>

            </div>

        </div>

    </div>

</div>

<?php get_footer(); ?>
