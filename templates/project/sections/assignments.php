<?php if ($assignments = get_project_assignments(get_the_ID())): ?>
<?php $grid = new talk_assignment_grid(); ?>
<?php $grid->set_entries( $assignments ); ?>
<div class="assignments">
	<div class="container">
		<h3 class='title'>Available Assignments at <?php the_title() ?></h3>
		<?php echo $grid->html() ?>
	</div>
</div>
<?php endif; ?>
