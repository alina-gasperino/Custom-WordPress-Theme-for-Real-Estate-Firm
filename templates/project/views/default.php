<?php //get_template_part('templates/project/card-googledrive-grid'); ?>
<?php get_template_part('templates/project/card-googledrive-carousel'); ?>

<?php get_template_part( 'templates/project/template-project-gallery' ); ?>

<div class="flex-row">

	<div class="flex-col first">

		<div class="panel-group project-panels" id="panels" role="tablist" aria-multiselectable="true">
            <?php // get_template_part( 'templates/project/card-googledrive-list' ); ?>



            <?php get_template_part( 'templates/project/card-events-list-panel' ); ?>

			<?php get_template_part( 'templates/project/panels/panel-intro' ); ?>

			<?php get_template_part( 'templates/project/panels/panel-floorplans' ); ?>



			<?php get_template_part( 'templates/project/panels/panel-overview' ); ?>

		</div>

	</div>

	<div class="flex-col project__sidebar panel-group project-panels">

        <?php get_template_part( 'templates/project/panels/panel-map' ); ?>

		<div class="infusionsoft-form" id = 'project-save'>

			<section class="av_textblock_section">
				<?php the_field( 'infusionsoftform' ); ?>
			</section>

			<section class="av_textblock_section realtor">
				<?php $profile = get_post( (rand() % 2)  ? '10102' : '10104' ); ?>
				<span itemprop="name" class="hidden"><?php echo get_the_title( $profile->ID ); ?></span>
				<div class="realtor__image">
					<?php echo get_the_post_thumbnail( $profile->ID, 'thumbnail' ); ?>
				</div>
				<div class="realtor__content">
					<h4 class='realtor__title'><?php echo get_the_title( $profile->ID ); ?></h4>
					<?php echo apply_filters('the_content', $profile->post_content) ?>
				</div>
			</section>

		</div>

		<?php // get_template_part( 'templates/project/card-googledrive-list' ); ?>

		<?php get_template_part( 'templates/project/card-updates' ); ?>

		<?php get_template_part( 'templates/project/card-related' ); ?>

	</div>

</div>
