<?php

$projects = TalkCondo_Ajax_Map_Query::get_projects([
	'max_deposit' => 15,
	'min_deposit' => 15,
]);

?>

<?php if ( $projects ): ?>

	<div class="condos-group" data-animation="fadeIn">

		<header>
			<h1 class='heading'>15% Deposit</h1>
		</header>

		<div id="condos-slider--movein" class="project-carousel condos-slider gscroll">
			<div class="condos-slider__scroller">
				<?php foreach ($projects as $project): ?>
					<?php global $post ?>
					<?php $post = $project->post_id ?>
					<?php setup_postdata( $post ); ?>
					<?php get_template_part( 'templates/home/carousel' ); ?>
				<?php endforeach ?>
			</div>
		</div>

	</div>

	<?php wp_reset_postdata(); ?>

<?php endif; ?>
