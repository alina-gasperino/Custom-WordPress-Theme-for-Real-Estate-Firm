
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
<div class="card card__header floorplans compact" data-animation="fadeIn">
    <div class="project-submenu">
        <div class="dropdown availability-dropdown">
        	<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
        		<li class="active">
        			<a href="#" data-filter="available">
        				For Sale <?php echo '(' . $available_floorplans . ')'?>
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
</div>
