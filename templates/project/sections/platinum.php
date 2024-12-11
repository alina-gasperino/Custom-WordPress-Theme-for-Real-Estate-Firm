<?php if ($similar_projects = get_platinum_projects( get_the_ID() )): ?>
	
	<?php
	$grid_params = array(
		'linking' 	=> '',
		'columns' 	=> '4',
		'items'		=> '8',
		'contents' 	=> 'title',
		'sort' 		=> 'no',
		'paginate' 	=> 'no',
		'set_breadcrumb' => false,
	);

	$similar_projects_grid = new talk_project_grid( $grid_params );
	$similar_projects_grid->set_entries($similar_projects); ?>

	<div class="similar container" data-animation="fadeIn">
		<h3 class='title'>Platinum Access Condos</h3>
		<?php echo $similar_projects_grid->html() ?>
	</div>

<?php endif; ?>
