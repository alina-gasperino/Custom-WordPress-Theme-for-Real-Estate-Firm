<?php
$floorplans = get_field('floorplans');
$total = ($floorplans) ? count($floorplans) : 0;
$active_tab = (get_query_var("floorplans") || get_query_var("floorplan")) ? 'floorplans' : 'condos';
?>
<div class="project-submenu">
	<ul>
	<?php if ( get_query_var("floorplan") && get_query_var('reserve') ): ?>
		<li>
			<a href="<?= get_permalink() ?>">Condo Overview</a>
		</li>
		<li class="active">
			<a href="<?= get_permalink() . "floorplans" ?>">
				Floor Plans & Pricing <?= $total ?  "($total)" : '' ?>
			</a>
		</li>
	<?php elseif ( get_query_var("floorplan") ): ?>
		<li>
			<a href="<?= get_permalink() ?>">Condo Overview</a>
		</li>
		<li class="active">
			<a href="<?= get_permalink() . "floorplans" ?>">
				Floor Plans & Pricing <?= $total ?  "($total)" : '' ?>
			</a>
		</li>
	<?php elseif ( get_query_var("floorplans") ): ?>
		<li>
			<a href="#overview" data-toggle="tab">Condo Overview</a>
		</li>
		<li class="active">
			<a href="#floorplans" data-toggle="tab">
				Floor Plans & Pricing <?= $total ?  "($total)" : '' ?>
			</a>
		</li>
	<?php else: ?>
		<li class="active">
			<a href="#overview" data-toggle="tab">Condo Overview</a>
		</li>
		<li>
			<a href="#floorplans" data-toggle="tab">
				Floor Plans & Pricing <?= $total ?  "($total)" : '' ?>
			</a>
		</li>
	<?php endif ?>
		<li>
			<a href="#pdfs" data-toggle="tab">PDF Downloads</a>
		</li>
	</ul>
</div>
