<?php
$info = get_project_data( get_the_ID() );
$floorplans = get_field('floorplans');
$floorplans = sort_floorplans($floorplans);
$total_floorplans = count($floorplans);
$available_floorplans = count(project_available_floorplans());
$soldout_floorplans = project_soldout_floorplans();
$sold_floorplans = $total_floorplans - $available_floorplans;
$default_layout = 'list';
?>

<div class="card floorplans compact" data-animation="fadeIn">

    <div class="project-submenu">
        <div class="dropdown availability-dropdown">
				<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
					<li class="active">
						<a href="#" data-filter="available">
							For Sale <?php echo '(' . $available_floorplans . '<span class = "avg"></span>'. ')'?>
						</a>
					</li>
					<li>
						<a href="#" data-filter="sold-out">
					Sold <?php echo '(' . $sold_floorplans . ')'?>
						</a>
					</li>
					<li>
                        <a href="#floorplate__trigger" id="floorplate__trigger" data-toggle="tab">Floor Plate</a>
                    </li>
					<li>
						<a href="#" data-filter="all">
                            Show All
						</a>
					</li>
				</ul>
			</div>
			
    </div>

	<div class="row">
		<div class="col-md-6">
			<div class="floorplans__layout-toggle">
				<button class="floorplans_more-filters-toggle"><i class="fa fa-filter fa-lg"></i> Filter Floor Plans <i class="fa fa-caret-down"></i></button>
				<a id="activate-floorplans-tab" class="desktop-only floorplans__layout external" href="#floorplans"><i class="fa fa-fw fa-expand-arrows-alt fa-lg"></i></a>
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

		<div class="floorplans_more-filters" style="display: none;">
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
	<div class="floorplans grid quickview-gallery" style="<?php echo ($default_layout != 'grid') ? 'display: none;' : ''; ?>">
		<?php if ($floorplans): ?>
			<?php foreach ($floorplans as $floorplan): ?>
				<?php include get_stylesheet_directory() . '/templates/project/floorplan-grid-entry.php'; ?>
			<?php endforeach ?>
		<?php endif ?>
	</div>
	<div style = "position:relative;">
	    
        <span style='padding: 5px 10px; margin: 0 5px; cursor: pointer; border-radius: 10px; font-size: 14px;' class='beds_number' id='hj'>Studio</span>
        <span style='padding: 5px 10px; margin: 0 5px; cursor: pointer; border-radius: 10px; font-size: 14px;' class='beds_number' id='hj'>1 bed</span>
        <span style='padding: 5px 10px; margin: 0 5px; cursor: pointer; border-radius: 10px; font-size: 14px;' class='beds_number' id='hj'>2 bed</span>
        <span style='padding: 5px 10px; margin: 0 5px; cursor: pointer; border-radius: 10px; font-size: 14px;' class='beds_number' id='hj'>3 bed</span>
        <div class="sort">
			<select name="cars" id="cars">
				<option value="asc">Sort by size (low to high)</option>
				<option value="desc">Sort by size (high to low)</option>
			</select>
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
				<?php 
					count($floorplans);	
				include get_template_directory() . '/templates/project/floorplan-list-compact-entry.php'; ?>
			<?php endforeach ?>
		<?php endif ?>
		</tbody>
	</table>
	<?php //endif;?>
	
	<?php //if ($available_floorplans > 0) : ?>
	<table class="sold_floorplans list quickview-gallery" style="display: none">
		<thead>
			<?php include get_template_directory() . '/templates/project/floorplan-list-headers-new.php'; ?>
		</thead>
		<tbody>
		<?php if ($floorplans): ?>
			<?php foreach ($floorplans as $floorplan): ?>
				<?php 
				include get_template_directory() . '/templates/project/sold-floorplan-list-compact-entry.php'; ?>
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
