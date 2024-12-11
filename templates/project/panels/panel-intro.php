<?php

/**
 * Template Part: Intro
 *
 * @package    Project
 * @subpackage Panel
 */

?>

<div class="panel panel-default">

	<div class="panel-heading" role="tab" id="collapseIntroHeading">

		<h2 class="panel-title">
			<a class="collapsed" role="button" data-toggle="collapse" href="#collapseIntro" aria-expanded="true" aria-controls="collapseIntro" itemprop="name">
				<?php if ( get_field('h2') ): ?>
					<?php the_field('h2'); ?>
				<?php elseif (custom_cat_text('neighbourhood') && custom_cat_text('city')): ?>
					<?php echo 'Condos in ' . custom_cat_text('neighbourhood') . ', ' . custom_cat_text('city') ?>
				<?php elseif (custom_cat_text('neighbourhood')): ?>
					<?php echo 'Condos in ' . custom_cat_text('neighbourhood') ?>
				<?php elseif (custom_cat_text('city')): ?>
					<?php echo 'Condos in ' . custom_cat_text('city') ?>
				<?php else: ?>
					<?php the_title(); ?>
				<?php endif; ?>
			</a>
		</h2>

		<button role="button" class="panel-toggle" data-toggle="collapse" href="#collapseIntro" aria-expanded="true" aria-controls="collapseIntro">
			<i class="fa fa-caret-up"></i>Hide
		</button>

	</div>

	<div id="collapseIntro" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="collapseIntroHeading">

		<div class="panel-body">

			<div class="card description" data-animation="fadeIn">

				<div class="card__content">
					<?php echo project_text(); ?>
				</div>

			</div>

		</div>
		
	</div>

</div>
