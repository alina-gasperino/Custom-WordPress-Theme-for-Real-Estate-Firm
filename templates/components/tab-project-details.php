<table itemprop="availableAtOrFrom" itemscope itemtype="http://schema.org/Place" style="table-layout: fixed">
	<tbody itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
		<?php if ($developer = custom_cat_link('developer')): ?>
		<tr>
			<td>Developer:</td>
			<td>
				<?php echo $developer ?>
			</td>
		</tr>
		<?php endif; ?>

		<?php if ($address = get_post_meta( get_the_ID(), 'address', true)): ?>
		<tr>
			<td>Address:</td>
			<td itemprop="streetAddress">
				<?php echo $address ?>
			</td>
		</tr>
		<?php endif; ?>

		<?php if ($district = custom_cat_link('district')): ?>
		<tr>
			<td>District:</td>
			<td>
				<?php echo $district ?>
			</td>
		</tr>
		<?php endif; ?>

		<?php if ($city = custom_cat_link('city')): ?>
		<tr>
			<td>City:</td>
			<td itemprop="addressLocality">
				<?php echo $city ?>
			</td>
		</tr>
		<?php endif; ?>

		<?php if ($neighbourhood = custom_cat_link('neighbourhood')): ?>
		<tr>
			<td>Neighbourhood:</td>
			<td>
				<?php echo $neighbourhood ?>
			</td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>
<table style="table-layout: fixed">
	<?php if ($majorintersection = get_post_meta( get_the_ID(), 'majorintersection', true)): ?>
	<tr>
		<td>Major Intersection:</td>
		<td>
			<?php echo $majorintersection ?>
		</td>
	</tr>
	<?php endif; ?>

	<?php if ($type = custom_cat_link('type')): ?>
	<tr>
		<td>Type:</td>
		<td>
			<?php echo $type ?>
		</td>
	</tr>
	<?php endif; ?>

	<?php if ($status = custom_cat_link('status')): ?>
	<tr>
		<td>Development Status:</td>
		<td>
			<?php echo $status ?>
		</td>
	</tr>
	<?php endif; ?>

	<?php if ($salesstatus = custom_cat_link('salesstatus')): ?>
	<tr>
		<td>Sales Status:</td>
		<td>
			<?php echo $salesstatus ?>
		</td>
	</tr>
	<?php endif; ?>

	<?php if ($occupancydate): ?>
	<tr>
		<td>Completion Date:</td>
		<td>
			<?php echo $occupancydate ?>
		</td>
	</tr>
	<?php endif; ?>

	<?php if ($parking = get_post_meta( get_the_ID(), 'parking', true)): ?>
	<tr>
		<td>Parking ($):</td>
		<td>
			<?php echo $parking ?>
		</td>
	</tr>
	<?php endif; ?>

	<?php if ($maintenancefees = get_post_meta( get_the_ID(), 'maintenancefeessq.ft', true)): ?>
	<tr>
		<td>Maintenance Fees:</td>
		<td>
			<?php echo $maintenancefees ?>
		</td>
	</tr>
	<?php endif; ?>

	<?php if ($storeys = get_post_meta( get_the_ID(), 'storeys', true)): ?>
	<tr>
		<td># Storeys:</td>
		<td>
			<?php echo $storeys ?>
		</td>
	</tr>
	<?php endif; ?>

	<?php if ($suites = get_post_meta( get_the_ID(), 'suites', true)): ?>
	<tr>
		<td># Suites:</td>
		<td>
			<?php echo $suites ?>
		</td>
	</tr>
	<?php endif; ?>

	<?php if ($sqftfrom = get_post_meta( get_the_ID(), 'sq.ftfrom', true)): ?>
	<?php $sqftto = get_post_meta( get_the_ID(), 'sq.ftto', true); ?>
	<tr>
		<td>Square Footage:</td>
		<td>
			From
			<?php echo $sqftfrom; ?>
			<?php if ($sqftto): ?>
			to
			<?php echo $sqftto; ?>
			<?php endif; ?>
			sq. ft
		</td>
	</tr>
	<?php endif; ?>

	<?php if ($pricedfrom = get_post_meta( get_the_ID(), 'pricedfrom', true)): ?>
	<?php $pricedto = get_post_meta( get_the_ID(), 'pricedto', true); ?>
	<tr>
		<td>Pricing:</td>
		<td itemprop="price">
			From
			<?php echo $pricedfrom ?>
			<?php if ($pricedto): ?>
			to
			<?php echo $pricedto ?>
			<?php endif; ?>
		</td>
		<meta itemprop="priceCurrency" content="CAD" />
	</tr>
	<?php endif; ?>

</table>
