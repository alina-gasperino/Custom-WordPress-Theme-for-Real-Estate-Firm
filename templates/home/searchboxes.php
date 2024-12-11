<?php
$cities = [
	  'Toronto',
	  'Mississauga',
	  'Markham',
	  'Oakville',
	  'Burlington',
	  'Brampton',
	  'Vaughan',
	  'Richmond Hill',
];
?>
<section id="searchboxes" class="condos-group invisible" data-animation="fadeIn">

	<header>
		<h1 class="heading">Find exactly what you're looking for. Instantly.</h1>
	</header>

	<div class="searchbox-container">
		<div class="searchbox" style="background-image: url('https://s3-ca-central-1.amazonaws.com/talkcondo/wp-content/uploads/2018/12/yorkville-estates-interior.jpg')">
			<div class="searchbox-inner">
				<div class="searchtext">
					Condos for Sale in
					<div class="dropdown">
						<div class="dropdown-toggle" data-target="#" data-toggle="dropdown">Toronto</div>
						<ul class="dropdown-menu list">
							<li data-value="nearme"><i class="fa fa-crosshairs"></i> Near Me</li>
							<?php foreach ($cities as $city): ?>
							<li data-value="<?= sanitize_title($city) ?>"><?= $city ?></li>
							<?php endforeach ?>
						</ul>
					</div>
					<br>
					Below
					<div class="dropdown">
						<div class="dropdown-toggle" data-target="#" data-toggle="dropdown">$650,000</div>
						<div class="dropdown-menu">
							<div class="filter-slider" data-outputfmt="$" data-min="200000" data-max="2500000" data-step="50000" data-start="650000" data-bounds="upper" data-field="max_price">
								<div class="slider-labels">
									<span class="upper-label">&nbsp;</span>
								</div>
								<div class="slider"></div>
							</div>
						</div>
					</div>
				</div>
				<form>
					<input type="hidden" name="lat" value=''>
					<input type="hidden" name="lng" value=''>
					<input type="hidden" name="city" value='toronto'>
					<input type="hidden" name="max_price" value=''>
					<input type="hidden" name="include_floorplans" value='true'>
					<button class="btn btn-alt"><i class="far fa-building"></i> Search Now</button>
				</form>
			</div>
		</div>

		<div class="searchbox" style="background-image: url('https://s3-ca-central-1.amazonaws.com/talkcondo/wp-content/uploads/2018/12/prime-condos-large.jpg')">
			<div class="searchbox-inner">
				<div class="searchtext">
					Condo Projects in
					<div class="dropdown">
						<div class="dropdown-toggle" data-target="#" data-toggle="dropdown">Toronto</div>
						<ul class="dropdown-menu list">
							<li data-value="nearme"><i class="fa fa-crosshairs"></i> Near Me</li>
							<?php foreach ($cities as $city): ?>
							<li data-value="<?= sanitize_title($city) ?>"><?= $city ?></li>
							<?php endforeach ?>
						</ul>
					</div>
					<br>
					With less than
					<div class="dropdown">
						<div class="dropdown-toggle" data-target="#"  data-toggle="dropdown">10% Down</div>
						<div class="dropdown-menu">
							<div class="filter-slider" data-outputfmt="%down" data-min="0" data-max="25" data-step="5" data-start="10" data-bounds="upper" data-field="max_deposit">
								<div class="slider-labels">
									<span class="upper-label">&nbsp;</span>
								</div>
								<div class="slider"></div>
							</div>
						</div>
					</div>
				</div>
				<form>
					<input type="hidden" name="lat" value=''>
					<input type="hidden" name="lng" value=''>
					<input type="hidden" name="city" value='toronto'>
					<input type="hidden" name="max_deposit" value=''>
					<button class="btn btn-alt"><i class="far fa-building"></i> Search Now</button>
				</form>
			</div>
		</div>

		<div class="searchbox" style="background-image: url('https://s3-ca-central-1.amazonaws.com/talkcondo/wp-content/uploads/2018/12/interior-large.jpg')">
			<div class="searchbox-inner">
				<div class="searchtext">Condos for Sale in
					<div class="dropdown">
						<div class="dropdown-toggle" data-target="#" data-toggle="dropdown">Toronto</div>
						<ul class="dropdown-menu list">
							<li data-value="nearme"><i class="fa fa-crosshairs"></i> Near Me</li>
							<?php foreach ($cities as $city): ?>
							<li data-value="<?= sanitize_title($city) ?>"><?= $city ?></li>
							<?php endforeach ?>
						</ul>
					</div>
					<br>
					Above
					<div class="dropdown">
						<div class="dropdown-toggle" data-target="#"  data-toggle="dropdown">1000 sq.ft.</div>
						<div class="dropdown-menu">
							<div class="filter-slider" data-outputfmt="sqft" data-min="0" data-max="2000" data-step="100" data-start="1000" data-bounds="upper" data-field="min_size">
								<div class="slider-labels">
									<span class="upper-label">&nbsp;</span>
								</div>
								<div class="slider"></div>
							</div>
						</div>
					</div>
				</div>
				<form>
					<input type="hidden" name="lat" value=''>
					<input type="hidden" name="lng" value=''>
					<input type="hidden" name="city" value='toronto'>
					<input type="hidden" name="min_size" value=''>
					<input type="hidden" name="include_floorplans" value='true'>
					<button class="btn btn-alt"><i class="far fa-building"></i> Search Now</button>
				</form>
			</div>
		</div>

		<div class="searchbox" style="background-image: url('https://s3-ca-central-1.amazonaws.com/talkcondo/wp-content/uploads/2018/12/aqualuna-large.jpg')">
			<div class="searchbox-inner">
				<div class="searchtext">
					Condo Projects in
					<div class="dropdown">
						<div class="dropdown-toggle" data-target="#" data-toggle="dropdown">Toronto</div>
						<ul class="dropdown-menu list">
							<li data-value="nearme"><i class="fa fa-crosshairs"></i> Near Me</li>
							<?php foreach ($cities as $city): ?>
							<li data-value="<?= sanitize_title($city) ?>"><?= $city ?></li>
							<?php endforeach ?>
						</ul>
					</div>
					<br>
					Cheaper than
					<div class="dropdown">
						<div class="dropdown-toggle" data-target="#" data-toggle="dropdown">$1000 per sq.ft.</div>
						<div class="dropdown-menu">
							<div class="filter-slider" data-outputfmt="ppsqft" data-min="500" data-max="2000" data-step="100" data-start="1000" data-bounds="upper" data-field="max_pricepersqft">
								<div class="slider-labels">
									<span class="upper-label">&nbsp;</span>
								</div>
								<div class="slider"></div>
							</div>
						</div>
					</div>
				</div>
				<form>
					<input type="hidden" name="lat" value=''>
					<input type="hidden" name="lng" value=''>
					<input type="hidden" name="city" value='toronto'>
					<input type="hidden" name="max_pricepersqft" value=''>
					<button class="btn btn-alt"><i class="far fa-building"></i> Search Now</button>
				</form>
			</div>
		</div>

	</div>
</section>
