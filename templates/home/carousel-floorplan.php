<div class="project floorplan available">

	<a href="<?= $floorplan->url ?>">

		<figure>
			<img src="<?= $floorplan->medium && !is_local() ? $floorplan->medium : get_template_directory_uri() . '/assets/images/layout/condo-placeholder.jpg' ?>">
		</figure>

		<figcaption>

			<h4 class="condo-title">
				<span class="title"><?= $floorplan->suite_name ?></span>
				<span class="floorplan__availability--label"></span>
			</h4>

			<p class="prices dot-divider">
				<?php if ($floorplan->price): ?>
					<span class="price">$<?= number_format($floorplan->price, 0) ?></span>
				<?php else: ?>
					<a class="price" href="<?= leadpages_form_url() ?>" data-leadbox="<?= leadpages_form_data_id() ?>">
						<?php esc_html_e( 'Contact For Pricing', 'talkcondo' ); ?>
					</a>
				<?php endif ?>

				<?php if ( $floorplan->pricepersqft ): ?>
					<span>$<?= number_format($floorplan->pricepersqft) ?>/ft.</span>
				<?php endif ?>
			</p>

			<p class="dot-divider">
				<?php if ($floorplan->beds || (isset($options) && $options["show_0_beds"])): ?>
				<span class="beds">
                    <?= ($floorplan->beds == 0) ? $floorplan->beds . " Bed" : "" ?>
					<?= ($floorplan->beds > 1) ? $floorplan->beds . " Beds" : "" ?>
					<?= ($floorplan->beds == 1) ? $floorplan->beds . " Bed" : "" ?>
				</span>
				<?php endif ?>

				<?php if ($floorplan->baths): ?>
				<span class="baths">
					<?= ($floorplan->baths > 1) ? $floorplan->baths . " Baths" : "" ?>
					<?= ($floorplan->baths == 1) ? $floorplan->baths . " Bath" : "" ?>
				</span>
				<?php endif ?>

				<?php if ($floorplan->size): ?>
				<span class="size">
					<?= $floorplan->size ?> sq.ft.
				</span>
				<?php endif ?>
			</p>

			<p><?= $floorplan->project->title ?></p>

		</figcaption>

	</a>

</div>
