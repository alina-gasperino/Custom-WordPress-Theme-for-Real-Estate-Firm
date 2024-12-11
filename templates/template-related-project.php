<div class="related__post">
	<a href="<?php the_permalink() ?>">
	<?php if (has_post_thumbnail()): ?>
	<div class="related__thumbnail">
		<?php the_post_thumbnail('flexslider'); ?>
	</div>
	<?php endif; ?>
	</a>
	<a href="<?php the_permalink() ?>">
	<div class="related__title"><?php the_title(); ?></div>
	</a>
	<div class="related__meta">
		<div class="related__author"><i class="fa fa-fw fa-user"></i> <?php the_author(); ?></div>
		<?php if (get_the_date()): ?>
		<div class="related__date"><i class="fa fa-fw fa-calendar"></i> <?php the_date(); ?></div>
		<?php endif; ?>
	</div>
</div>
