<?php get_header(); ?>

<main class="site-wrapper" itemprop="mainContentOfPage" role="main">

	<?php
		try {
			get_template_part( 'templates/project/banner-recent-updates' );
		} catch (\Throwable $th) {
			throw $th;
		}
	?>

	<div class="container">

		<?php if( have_posts() ): while( have_posts() ): the_post(); ?>

			<article id="project-<?php the_ID(); ?>" <?php post_class(); ?> itemscope itemtype="http://schema.org/ApartmentComplex">

				<?php if (!get_query_var('reserve')): ?>

					<?php
						try {
							get_template_part( 'templates/project/components/header' );
						} catch (\Throwable $th) {
							throw $th;
						}
					?>
					<?php 
					try {
						get_template_part( 'templates/project/components/banner' );
					} catch (\Throwable $th) {
						throw $th;
					}
					?>

				<?php endif; ?>

				<div class="project-content">
					<?php if ( get_query_var("floorplan") && get_query_var('reserve') ): ?>
						<?php include locate_template( "templates/project/views/floorplan_reservation.php" ) ?>
					<?php elseif ( get_query_var("floorplan") ): ?>
						<?php include locate_template( "templates/project/views/floorplan.php" ) ?>
					<?php elseif ( get_query_var("floorplans") ): ?>
						<div class="tab-content">
                            <div id="pdfs" role="tabpanel" class="tab-pane fade">
                                <div class="panel-group project-panels">
                                    <?php // get_template_part( 'templates/project/card-googledrive-list' ); ?>
                                    <?php //get_template_part('templates/project/card-googledrive-grid'); ?>
                                    <?php get_template_part('templates/project/card-googledrive-carousel'); ?>
                                </div>
                            </div>
							<div id="overview" role="tabpanel" class="tab-pane fade">
								<?php get_template_part( "templates/project/views/default" ); ?>
							</div>
							<div id="floorplans" role="tabpanel" class="tab-pane fade in active">
								<?php get_template_part( "templates/project/views/floorplans" ); ?>
							</div>
						</div>
					<?php else: ?>
						<div class="tab-content">
                            <div id="pdfs" role="tabpanel" class="tab-pane fade">
                                <div class="panel-group project-panels">
                                    <?php // get_template_part( 'templates/project/card-googledrive-list' ); ?>
                                    <?php // get_template_part('templates/project/card-googledrive-grid'); ?>
                                    <?php get_template_part('templates/project/card-googledrive-carousel'); ?>
                                </div>
                            </div>
							<div id="overview" role="tabpanel" class="tab-pane fade in active">
								<?php 
									try {
										get_template_part( "templates/project/views/default" );
									} catch (\Throwable $th) {
										throw $th;
									}
								?>
							</div>
							<div id="floorplans" role="tabpanel" class="tab-pane fade">
								<?php get_template_part( "templates/project/views/floorplans" ); ?>
							</div>
						</div>
					<?php endif ?>
				</div>

			</article>

		<?php endwhile; else: ?>

			<?php get_template_part( 'error' ); ?>

		<?php endif; ?>

	</div>

<?php /*
	<?php if (!get_query_var('reserve')): ?>

		<?php get_template_part( 'templates/project/sections/assignments' ); ?>

		<div class="condos">
			<?php get_template_part( 'templates/home/condos/condos', 'new' ); ?>
		</div>

		<?php get_template_part( 'templates/project/components/mobile-footer-nav' ); ?>

	<?php endif; ?>
*/ ?>

</main>

<?php get_footer(); ?>

