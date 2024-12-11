<?php
$info = get_project_data( get_the_ID() );
$floorplans = get_field('floorplans');
$floorplans = sort_floorplans($floorplans);
$total_floorplans = count($floorplans);
$available_floorplans = count(project_available_floorplans());
$sold_floorplans = $total_floorplans - $available_floorplans;
$default_layout = 'list';
?>

<div class="card floorplans" data-animation="fadeIn">

    <div class="row">
		<div class="col-md-6">
			<div class="dropdown availability-dropdown">
				<button class="dropdown-toggle" type="button" data-filter="available" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
					<i class="fa fa-circle fa--available"></i>
					Show Available Only <span class="floorplan-count available"> (<?php echo $available_floorplans; ?>)</span>
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
					<li class="active">
						<a href="#" data-filter="available"><i class="fa fa-circle fa-fw fa--available"></i>
							Show Available Only <span class="floorplan-count available"> (<?php echo $available_floorplans; ?>)</span>
						</a>
					</li>
					<li>
						<a href="#" data-filter="sold-out">
							<i class="fa fa-circle fa-fw fa--sold"></i>
							Show Sold Only <span class="floorplan-count sold">(<?php echo $sold_floorplans; ?>)</span>
						</a>
					</li>
					<li>
						<a href="#" data-filter="all">
							<img src="<?php echo get_template_directory_uri(); ?>/assets/images/icon-mixed-available.svg" width="14" height="14" alt="">
                            Show Available & Sold <span class="floorplan-count all">(<?php echo $total_floorplans; ?>)</span>
						</a>
					</li>
                    <?php if ( is_user_logged_in() ):?>
                        <li>
                            <a href="#" data-filter="saved">
                                <i class="fa fa-heart fa-fw fa--saved"></i>
                                Show Only Saved <span class="floorplan-count saved">(0)</span>
                            </a>
                        </li>
                    <?php endif;?>
					<li>
						<label class="availability-sort" for="available-first">
							<input type="checkbox" id="available-first">
							Show Available First
						</label>
					</li>
				</ul>
			</div>
		</div>
		<div class="col-md-6">
			<div class="floorplans__layout-toggle">
				<button class="floorplans_more-filters-toggle"><i class="fa fa-filter fa-lg"></i> Filter Floor Plans <i class="fa fa-caret-down"></i></button>
<!--				<a class="desktop-only floorplans__layout active" data-layout="list"><i class="fa fa-fw fa-list fa-lg"></i></a>-->
<!--				<a class="desktop-only floorplans__layout" data-layout="grid"><i class="fa fa-fw fa-th fa-lg"></i></a>-->
<!--				<a class="desktop-only floorplans__layout external" href="--><?php //echo get_permalink() . "floorplans" ?><!--"><i class="fa fa-fw fa-expand-arrows-alt fa-lg"></i></a>-->
			</div>
		</div>
	</div>

	<div class="floorplans__filter-sliders">
		<div class="floorplans__filter-slider hide">
			<label>Price</label>
			<?php $chunk = 50000; ?>
			<div id="priceslider"
				data-min="<?php echo floor(floorplans_bounds('price', 'min') / $chunk) * $chunk ?>"
				data-max="<?php echo ceil(floorplans_bounds('price', 'max') / $chunk) * $chunk ?>"></div>
		</div>

		<div class="hide">
			<button class="floorplans_more-filters-toggle">Filter by # Beds, # Baths, Sq. Ft. <i class="fa fa-caret-down"></i></button>
		</div>

		<div class="floorplans_more-filters">
			<div class="floorplans__filter-slider">
				<label>BEDS</label>
				<div id="bedslider" data-min="<?php echo floorplans_bounds('beds', 'min') ?>" data-max="<?php echo floorplans_bounds('beds', 'max') ?>"></div>
			</div>
			<div class="floorplans__filter-slider">
				<label>BATHS</label>
				<div id="bathslider" data-min="<?php echo floorplans_bounds('baths', 'min') ?>" data-max="<?php echo floorplans_bounds('baths', 'max') ?>"></div>
			</div>
			<div class="floorplans__filter-slider">
				<label>SQ.FT</label>
				<div id="sizeslider" data-min="<?php echo floorplans_bounds('size', 'min') ?>" data-max="<?php echo floorplans_bounds('size', 'max') ?>"></div>
			</div>

			<a class="btn btn-link floorplans_reset-filters">RESET</a>
		</div>
	</div>

	<?php //if ($available_floorplans > 0) : ?>
	<table class="floorplans list quickview-gallery" style="<?php echo ($default_layout != 'list') ? 'display: none;' : ''; ?>">
		<thead>
			<?php include get_template_directory() . '/templates/project/floorplan-list-compact-entry-headers.php'; ?>
		</thead>
		<tbody>
		<?php if ($floorplans): ?>
			<?php foreach ($floorplans as $floorplan): ?>
				<?php include get_template_directory() . '/templates/project/floorplan-list-compact-entry.php'; ?>
			<?php endforeach ?>
		<?php endif ?>
		</tbody>
	</table>
	<?php //endif;?>

	<?php if ($available_floorplans == 0) include get_template_directory() . '/templates/project/card-floorplans-empty.php'; ?>

	<div class="responder-section__footnote">
		<p>
			All prices, availability, figures and materials are preliminary and are subject to change without notice. E&OE <?php echo date('Y'); ?><br>
			Floor Premiums apply, please speak to sales representative for further information.
		</p>
	</div>

</div>
