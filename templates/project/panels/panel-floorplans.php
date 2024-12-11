<?php

/**
 * Template Part: Floorplans
 *
 * @package    Project
 * @subpackage Panel
 */

$info = get_project_data( get_the_ID() );
$data = get_field("floorplans");
$city_price = project_priceavg_tax( 'city', $info['city']);
$neighbourhood_price = project_priceavg_tax( 'neighbourhood', $info['neighbourhood']);
// var_dump($info);
?>

<div class="panel panel-default">

	<div class="panel-heading" role="tab" id="collapsePPSHeading">

		<h2 class="panel-title">
			<a class="collapsed" role="button" data-toggle="collapse" href="#collapsePPS" aria-expanded="true" aria-controls="collapsePPS" itemprop="name">Price Per Square Foot</a>
		</h2>

		<button role="button" class="panel-toggle" data-toggle="collapse" href="#collapsePPS" aria-expanded="true" aria-controls="collapsePPS">
			<i class="fa fa-angle-up"></i>Hide
		</button>

	</div>
	<div id="collapsePPS" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="collapseOverviewHeading">
	<?php
		$bed1_count = 0;
		$bed1_price = 0;
		$bed1_avg = 0;
		$bed2_count = 0;
		$bed2_price = 0;
		$bed2_avg = 0;
		$bed3_count = 0;
		$bed3_price = 0;
		$bed3_avg = 0;
			foreach($data as $item){
				$bed_num = $item['beds'];
				if($bed_num == 1){
					$bed1_count = $bed1_count + 1;
					$bed1_price = $bed1_price + $item['price']/$item['size'];
				}
				else if($bed_num == 2){
					$bed2_count = $bed2_count + 1;
					$bed2_price = $bed2_price + $item['price']/$item['size'];
				}
				else if($bed_num == 3){
					$bed3_count = $bed3_count + 1;
					$bed3_price = $bed3_price + $item['price']/$item['size'];
				}
			}
			$bed1_avg = $bed1_price/$bed1_count;
			$bed2_avg = $bed2_price/$bed2_count;
			$bed3_avg = $bed3_price/$bed3_count;
		?>

		<div class="panel-body">
			<div class="psf_details">
				<div class="detail_item">
					<div>THIS PROJECT</div>
					<div class="detail_psf">
						$<?php echo number_format($info['pricepersqft'], 0, '.', '');?><small>/sq.ft</small>
					</div>
				</div>
				<div class="detail_item">
					<div>STUDIO</div>
					<div class="detail_psf">
						<?php echo "NAN";?>
					</div>
				</div>
				<div class="detail_item">
					<div>1 BED AVERAGE</div>
					<div class="detail_psf">
						$<?php echo number_format($bed1_avg, 0, '.', '');?><small>/sq.ft</small>
					</div>
				</div>
				<div class="detail_item">
					<div>2 BED AVERAGE</div>
					<div class="detail_psf">
						$<?php echo number_format($bed2_avg, 0, '.', '');?><small>/sq.ft</small>
					</div>
				</div>
				<div class="detail_item">
					<div>3 BED AVERAGE</div>
					<div class="detail_psf">
						<?php echo "NAN";?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">

	<div class="panel-heading" role="tab" id="collapseFloorplansHeading">

		<h2 class="panel-title">
			<a class="collapsed" role="button" data-toggle="collapse" href="#collapseFloorplans" aria-expanded="true" aria-controls="collapseFloorplans" itemprop="name">
				<?php the_title(); ?> Floor Plans & Prices
			</a>
		</h2>

		<button role="button" class="panel-toggle" data-toggle="collapse" href="#collapseFloorplans" aria-expanded="true" aria-controls="collapseFloorplans">
			<i class="fa fa-caret-up"></i>Hide
		</button>

	</div>

	<div id="collapseFloorplans" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="collapseFloorplansHeading">

		<div class="panel-body">

			<?php if ( platinum_access() ): ?>
				<?php get_template_part( 'templates/project/card-floorplans-platinum-access' ); ?>
			<?php elseif ( have_rows( 'floorplans' ) ): ?>
				<?php get_template_part( 'templates/project/card-floorplans-preview-compact' ); ?>
			<?php else: ?>
				<?php get_template_part( 'templates/project/card-floorplans-none' ); ?>
			<?php endif; ?>

		</div>

	</div>

</div>
