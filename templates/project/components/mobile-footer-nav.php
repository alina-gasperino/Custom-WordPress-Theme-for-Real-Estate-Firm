<ul class='mobile-only project-tag-buttons'>

	<?php if (get_query_var('floorplan')): ?>

		<li>
			<a class='tag-button' href='<?= get_permalink() ?>'>
				<i class='far fa-building'></i>Project Info
			</a>
		</li>
		<li class='active'>
			<a class='tag-button' href='<?= get_permalink() . 'floorplans/' ?>'>
				<i class='fa fa-layer-group'></i>Floor Plans
			</a>
		</li>

	<?php elseif (get_query_var('floorplans')): ?>

		<li>
			<a class='tag-button' href='#overview' data-toggle='tab'>
				<i class='far fa-building'></i>Project Info
			</a>
		</li>
		<li class='active'>
			<a class='tag-button' href='#floorplans' data-toggle='tab'>
				<i class='fa fa-layer-group'></i>Floor Plans
			</a>
		</li>

	<?php else: ?>

		<li class='active'>
			<a class='tag-button' href='#overview' data-toggle='tab'>
				<i class='far fa-building'></i>Project Info
			</a>
		</li>
		<li>
			<a class='tag-button' href='#floorplans' data-toggle='tab'>
				<i class='fa fa-layer-group'></i>Floor Plans
			</a>
		</li>

	<?php endif ?>

	<li>
		<a class='tag-button' href='#pdfs' data-toggle='tab'>
			<i class='far fa-file'></i>PDF Files
		</a>
	</li>
	<li>
		<a class='btn btn-alt register-url' href='<?= leadpages_form_url() ?>' data-leadbox="<?= leadpages_form_data_id() ?>" target="_blank">Register</a>
	</li>

</ul>

<?php /*
<?php if (get_query_var('floorplan')): ?>
<div class='desktop-only single-floor-tag-buttons'>
	<div class='single-floor-bottom-left'>
		<p><?= $floorplan['suite_name'] . ' &middot; ' . get_the_title() ?></p>
		<p>From $<?= number_format($floorplan['price']) ?></p>
	</div>
	<div class='single-floor-bottom-right'>
		<a href="<?php echo $_SERVER['REQUEST_URI'] . 'reserve/' ?>" class="btn btn-blue btn-block btn-round">Reserve This Condo</a>
	</div>
</div>
<?php endif ?>
*/ ?>
