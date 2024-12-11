<div class="project_detail">
	<?php while ( have_rows('deposit_structure') ) : ?>
		<?php the_row() ?>
		<?php
		$deposit_type = get_sub_field('deposit_type');
		$deposit_amount = get_sub_field('deposit_amount');
		$deposit_time = get_sub_field('deposit_time');
		$deposit_due_on = get_sub_field('deposit_due_on');
		$dp_label = '';
		if ($deposit_type == 'amount') {
			$dp_price = '$'.number_format($deposit_amount, 2);
		} elseif ($deposit_type == 'percent') {
			$dp_price = $deposit_amount.'%';
			//'$'.number_format($deposit_amount * $floorplan['price']/100, 2);
			if ($deposit_due_on != 'occupancy') {
				$dp_total_pro += $deposit_amount;
				$dp_total = $dp_total_pro.'%';
			}
		} else {
			// $dp_price = '$'.number_format($deposit_amount * $floorplan['price']/100 - $dp_on_sign, 2);
			$dp_price = 'Balance to '.$deposit_amount.'%';
			$dp_total_pro += $deposit_amount;
			$dp_total = $dp_total_pro.'%';
		}

		if ($deposit_due_on == 'days') {
			if ($deposit_time == 0) {
				$dp_label = 'On Signing';
				$dp_on_sign = $deposit_amount;
			}
			// if($deposit_time > 0) $dp_label = '- '.date('M j, Y', strtotime('+'.$deposit_time.' days')).' ('.$deposit_time.' days)';
			if ($deposit_time > 0) $dp_label = '- '.$deposit_time.' days';
		} elseif ($deposit_due_on == 'date') {
			$dp_label = '- '.date('M j, Y', strtotime($deposit_time));
		} elseif ($deposit_due_on == 'occupancy'){
			$dp_label = '- Occupancy';

			if ($dp_total) {
				$dp_total .= ' + '.$deposit_amount.'%';
			} else {
				$dp_total = $deposit_amount.'%';
			}
		}
		$dep[] = array('dp_price' => $dp_price, 'dp_label' => $dp_label);
	?>
	<?php endwhile ?>

	<p><span><b><?= $dp_total ? "($dp_total)" : '' ?></b></span></p>

	<?php foreach($dep as $dep_value): ?>
		<p><span><?= $dep_value['dp_price'] . ' ' . $dep_value['dp_label'] ?></span></p>
	<?php endforeach ?>
</div>
