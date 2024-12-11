<?php
global $project_floorplan;
$floorplans = get_field('floorplans', false);
$floorplans = sort_floorplans($floorplans);

foreach ($floorplans as $key => $f) {
	if ($project_floorplan->ID == $f['image']) {
		$floorplan = $f;
		break;
	}
}

$unitPrice = preg_replace('/[^0-9]/', '', $floorplan['price']) ?: 0;

$parking_fee = 0;
$locker_fee = 0;
$parking = str_replace("\n", ' ', get_field('parking'));
$locker = str_replace("\n", ' ', get_field('locker'));

if (preg_match_all('/[$,\.0-9]{2,}/', $parking, $matches)) {
	$parking_fee = preg_replace('/[^0-9]/', '', $matches[0][0]);
}

if (preg_match_all('/[$,\.0-9]{2,}/', $locker, $matches)) {
	$locker_fee = preg_replace('/[^0-9]/', '', $matches[0][0]);
}

?>

<div id="floorplan-reservation" v-show="loaded" style="display: none;">

	<div class="row" v-show="submitted && !gform_is_valid">
		<div class="alert alert-danger">
			<i class="fa fa-exclamation-triangle"></i> There was an error with your form submission
		</div>
	</div>

	<ul class="tabs" v-show="!gform_is_valid && step > 1">
		<li :class="{ active: step == 1, past: step > 1 }"><a>Suite Selected</a></li>
		<li :class="{ active: step == 2, past: step > 2 }"><a @click="goToStep(2)">Floor Ranges, Parking, etc</a></li>
		<li :class="{ active: step == 3, past: step > 3 }"><a @click="goToStep(3)">Personal Information</a></li>
		<li :class="{ active: step == 4, past: step > 4 }"><a @click="goToStep(4)">Submit for Review</a></li>
	</ul>

	<div class="row" v-show="submitted && gform_is_valid">
		<div class="col-sm-12">
			<div class="step step1 success"  style="text-align: center;">
				<div><span class="circle-icon"><i class='material-icons md-36' style="font-weight: bold;">check</i></span></div>
				<h2>Congratulations!  Suite reservation request submitted.<br><?= $floorplan['suite_name'] ?> at <?= get_the_title() ?></h2>
				<p>Check your email for a confirmation of your reservation request.  A member of the TalkCondo team will be in touch to confirm your suite and the final price and to arrange an appointment to sign your agreement.</p>
				<div>
					<a class="btn btn-default" href="<?= $post->guid ?>">
						<i class="material-icons">arrow_back</i>
						<span class="text">Back to <?= get_the_title() ?></span>
					</a>
					<a class="btn btn-primary" href="<?= home_url() ?>">
						<span class="text">Go Home</span>
						<i class='material-icons'>arrow_forward</i>
					</a>
				</div>
			</div>
		</div>
	</div>

	<div class="row" v-show="!gform_is_valid">
		<div :class='{"col-sm-8": step > 1, "col-sm-12": step == 1}'>
			<div class="steps">
				<div class="step step1 disclaimer" v-show="step == 1" style="text-align: center;">

					<div><span class="circle-icon"><i class='material-icons md-36'>shopping_cart</i></span></div>
					<h2>You are about to begin the reservation request for:<br><?= $floorplan['suite_name'] ?> at <?= get_the_title() ?></h2>
					<p>Please note that this is a request to purchase. Prices are based on lowest available and premiums may apply. Upon completeing the form, a team member from TalkCondo will confirm the suite and availability with you prior to signing.
						<i class="fa fa-close"></i>
					</p>
					<div>
						<a class="btn btn-default" href="<?= get_floorplans_link( $floorplan['image'] ) ?>"><i class="material-icons">arrow_back</i><span class="text">Back</span></a>
						<button @click="next" class=" btn btn-primary"><span class="text">Next</span><i class='material-icons'>arrow_forward</i></button>
					</div>
					<p>Prices and availability are subject to change without notice. E&EO.</p>
				</div>

				<div class="step step2" v-show="step == 2">
					<h2>Select Your Preferred Floor Range</h2>
					<p class='disclaimer'>Floor Range for this suite: <?= floorplan_floor_ranges($floorplan) ?>. Floor premiums may apply. Sales representative will confirm availability and final pricing.</p>
					<div class="options">
						<button class="option" :class="{ active: form.floors.indexOf('high') !== -1 }" @click="toggleFloor('high')">
							<i class="fa fa-check"></i>
							<span>High Floor</span>
						</button>
						<button class="option" :class="{ active: form.floors.indexOf('mid') !== -1 }" @click="toggleFloor('mid')">
							<i class="fa fa-check"></i>
							<span>Mid Floor</span>
						</button>
						<button class="option" :class="{ active: form.floors.indexOf('low') !== -1 }" @click="toggleFloor('low')">
							<i class="fa fa-check"></i>
							<span>Low Floor</span>
						</button>
					</div>

					<h2>Add Parking (if eligible)</h2>
					<p class="disclaimer">Parking Price: <?= get_field('parking') ?></p>
					<div class="options">
						<button class="option" :class="{ active: form.parking === 1 }" @click="form.parking = 1">
							<i class="fa fa-check"></i>
							<span>Yes</span>
							<span class="price">+ {{ parkingPrice | currency }}</span>
						</button>
						<button class="option" :class="{ active: form.parking === 0 }" @click="form.parking = 0">
							<i class="fa fa-check"></i>
							<span>No</span>
						</button>
						<button class="option" :class="{ active: form.parking === 'later' }" @click="form.parking = 'later'">
							<i class="fa fa-check"></i>
							<span>Decide Later</span>
						</button>
					</div>

					<h2>Add Locker (if eligible)</h2>
					<p class="disclaimer">Locker Price: <?= get_field('locker') ?></p>
					<div class="options">
						<button class="option" :class="{ active: form.locker === 1 }" @click="form.locker = 1">
							<i class="fa fa-check"></i>
							<span>Yes</span>
							<span class="price">+ {{ lockerPrice | currency }}</span>
						</button>
						<button class="option" :class="{ active: form.locker === 0 }" @click="form.locker = 0">
							<i class="fa fa-check"></i>
							<span>No</span>
						</button>
						<button class="option" :class="{ active: form.locker === 'later' }" @click="form.locker = 'later'">
							<i class="fa fa-check"></i>
							<span>Decide Later</span>
						</button>
					</div>

					<div class="form-group">
						<label>Additional Notes (optional)</label>
						<input type="text" name="notes" class="form-control" v-model="form.notes">
					</div>
				</div>

				<div class="step step3" v-show="step == 3">
					<h2>Purchaser Information</h2>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label>First Name</label>
								<input class="form-control" type="text" name="" v-model="form.firstname">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>Last Name</label>
								<input class="form-control" type="text" name="" v-model="form.lastname">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label>Primary Phone Number</label>
								<input class="form-control" type="text" name="" v-model="form.phone">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>Email Address</label>
								<input class="form-control" type="text" name="" v-model="form.email">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label>Date of Birth</label>
								<input class="form-control" type="text" name="" v-model="form.dob">
							</div>
						</div>
						<div class="col-sm-6">
						</div>
					</div>

					<h2>Mailing Address</h2>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label>Address</label>
								<input class="form-control" type="text" name="" v-model="form.address">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>Suite Number</label>
								<input class="form-control" type="text" name="" v-model="form.suite">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label>City</label>
								<input class="form-control" type="text" name="" v-model="form.city">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>Province</label>
								<input class="form-control" type="text" name="" v-model="form.province">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label>Postal Code</label>
								<input class="form-control" type="text" name="" v-model="form.post">
							</div>
						</div>
						<div class="col-sm-6">
						</div>
					</div>

					<h2>Additional Purchaser (optional)</h2>
					<button class="addtlPurchaser btn btn-default" @click="toggleAddtlPurchaser" style="margin-bottom: 30px;">
						<i class='fa fa-lg fa-user-plus' v-if="!addtlPurchaser"></i>
						<i class='fa fa-lg fa-user-minus' v-if="addtlPurchaser"></i>
						<span>{{ (addtlPurchaser) ? 'Remove Second Purchaser' : 'Add Second Purchaser'}}</span>
					</button>
					<div class="addtl-purchaser" v-show="addtlPurchaser">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label>First Name</label>
									<input class="form-control" type="text" name="" v-model="form.addtl_firstname">
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label>Last Name</label>
									<input class="form-control" type="text" name="" v-model="form.addtl_lastname">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label>Primary Phone Number</label>
									<input class="form-control" type="text" name="" v-model="form.addtl_phone">
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label>Email Address</label>
									<input class="form-control" type="text" name="" v-model="form.addtl_email">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label>Date of Birth</label>
									<input class="form-control" type="text" name="" v-model="form.addtl_dob">
								</div>
							</div>
							<div class="col-sm-6">
							</div>
						</div>
						<h1>Mailing Address</h1>
						<div class="row">
							<div class="col-xs-12">
								<div class="checkbox">
									<label>
										<input type="checkbox" id="same" @change="copyAddress"> Same as above
									</label>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label>Address</label>
									<input class="form-control" type="text" name="" v-model="form.addtl_address">
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label>Suite Number</label>
									<input class="form-control" type="text" name="" v-model="form.addtl_suite">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label>City</label>
									<input class="form-control" type="text" name="" v-model="form.addtl_city">
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label>Province</label>
									<input class="form-control" type="text" name="" v-model="form.addtl_province">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label>Postal Code</label>
									<input class="form-control" type="text" name="" v-model="form.addtl_post">
								</div>
							</div>
							<div class="col-sm-6">
							</div>
						</div>
					</div>
				</div>

				<div class="step step4" v-show="step == 4">

					<div class="row">
						<div class="col-sm-6">
							<h2>Purchaser Information</h2>
							<div class="row">
								<div class="col-sm-6">
									{{ form.firstname }} {{ form.lastname }}<br>
									{{ form.address }}<br>
									{{ form.city }}, {{ form.province }}<br>
									{{ form.post }}<br>
									{{ form.dob }}<br>
									{{ form.email }}<br>
									{{ form.phone }}
								</div>
							</div>
							<div class="row" v-show="addtlPurchaser">
								<div class="col-sm-6">
									{{ form.addtl_firstname }} {{ form.addtl_lastname }}<br>
									{{ form.addtl_address }}<br>
									{{ form.addtl_city }}, {{ form.addtl_province }}<br>
									{{ form.addtl_post }}<br>
									{{ form.addtl_dob }}<br>
									{{ form.addtl_email }}<br>
									{{ form.addtl_phone }}
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="deposit-structure">
								<h1>Deposit Structure</h1>
								<h4>Total Deposit: {{ totalDeposit }}</h4>
								<table class="deposit table table-condensed table-basic">
									<tr v-for="i in depositStructure">
										<td>{{ depositAmt(i) | currency }}</td>
										<?php /*<td>(<i>{{ i.deposit_amount + ' ' + i.deposit_type }}</i>)</td>*/ ?>
										<td>{{ depositDesc(i) }}</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class='summary-sidebar col-sm-4' v-show="step > 1">
			<h2 class='sidebar-heading'>Your Condo <i class="material-icons">shopping_cart</i></h2>
			<div class="row">
				<div class="col-xs-6">
					<h2 class="suite-name"><?= $floorplan['suite_name'] ?></h2>
					<h5 class="details">
						<?= implode('&nbsp;&middot;&nbsp;', [
							$floorplan['size'] . ' sq.ft.',
							$floorplan['beds'] . ' Bed',
							$floorplan['baths'] . ' Bath',
						]) ?>
					</h5>
					<h5 class="project"><?= $post->post_title ?></h5>
					<p class="address"><?= get_field('address') ?></p>
					<p class="address"><?= get_field('city') ?></p>
				</div>
				<div class="col-xs-6">
					<?php if( $thumbnail = wp_get_attachment_image_src( $floorplan['image'], 'full' )[0] ): ?>
						<meta itemprop="photo" content="<?= wp_get_attachment_image_src( $floorplan['image'], 'full' )[0]; ?>">
						<a class="noLightbox floorplan-thumbnail" href="<?= wp_get_attachment_image_src( $floorplan['image'], 'full' )[0]; ?>" data-fancybox="floorplan">
							<img class="noLightbox lazy" alt="<?= floorplan_alt_text($floorplan, $post) ?>" data-original="<?= wp_get_attachment_image_src( $floorplan['image'], 'small' )[0]; ?>" />
							<i class="fa fa-search-plus"></i>
						</a>
					<?php endif; ?>
				</div>
			</div>

			<table class="pricing table table-condensed table-basic">
				<tbody>
					<tr>
						<td>Unit Price</td>
						<td class="price">{{ unitPrice | currency }}</td>
					</tr>
					<tr>
						<td>Parking</td>
						<td class="price">{{ (form.parking === 1) ? parkingPrice: 0 | currency }}</td>
					</tr>
					<tr>
						<td>Locker</td>
						<td class="price">{{ (form.locker === 1) ? lockerPrice: 0 | currency }}</td>
					</tr>
					<tr>
						<td>Total Purchase Price</td>
						<td class="price total">{{ totalPrice | currency }}</td>
					</tr>
				</tbody>
			</table>

			<div class="deposit-structure" v-show="step < 4">
				<h2>Deposit Structure</h2>
				<h4>Total Deposit: {{ totalDeposit }}</h4>
				<table class="deposit table table-condensed table-basic">
					<tr v-for="i in depositStructure">
						<td>{{ depositAmt(i) | currency }}</td>
						<?php /*<td>(<i>{{ i.deposit_amount + ' ' + i.deposit_type }}</i>)</td>*/ ?>
						<td>{{ depositDesc(i) }}</td>
					</tr>
				</table>
			</div>

			<p class='disclaimer'>This purchase price is an estimate and may vary based on additional premiums and availability. Final price and confirmation of availability will be verified by your TalkCondo representative before final purchase.</p>

		</div>
	</div>

	<div class="banner" v-show="step > 1">
		<div class="inner">
			<i class="material-icons icon">shopping_cart</i>
			<div class="text">
				<h2 class="title"><?= $floorplan['suite_name'] ?></h2>
				<h3 class="subtitle"><?= get_the_title() ?></h3>
			</div>
			<div class="buttons">
				<a class="btn btn-default" href="<?= get_floorplans_link( $floorplan['image'] ) ?>" v-show="step == 2">
					<i class="material-icons">arrow_back</i>
					</i><span class="text">Back</span>
				</a>
				<button class="btn btn-default" v-show="step > 2" @click="prev">
					<i class="material-icons">arrow_back</i>
					</i><span class="text">Back</span>
				</button>
				<button class="btn btn-primary" v-show="step < 4" @click="next">
					<span class="text">Next</span>
					<i class="material-icons">arrow_forward</i>
				</button>
				<button class="btn btn-primary" v-show="step == 4" @click="submit">
					<span class="text">Submit</span>
					<i class="material-icons">arrow_forward</i>
				</button>
			</div>
		</div>
	</div>

</div>

<div class="hidden">
	<?= do_shortcode('[gravityforms id=9 ajax=false]') ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/vue@2.5.17/dist/vue.js"></script>
<script src="https://unpkg.com/vue-currency-filter@3.2.3/dist/vue-currency-filter.iife.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>

<script type="text/javascript">

if (VueCurrencyFilter) {
  Vue.use(VueCurrencyFilter, {
    symbol: "$",
    thousandsSeparator: ",",
    fractionCount: 0,
    fractionSeparator: ".",
    symbolPosition: "front",
    symbolSpacing: false
  })
}

Vue.prototype.moment = moment;

var reservation = new Vue({
		el: '#floorplan-reservation',
		methods: {
			toggleAddtlPurchaser: function() {
				this.addtlPurchaser = !this.addtlPurchaser;
			},
			toggleFloor: function(floor) {
				var i = this.form.floors.indexOf(floor);
				if (i > -1) {
					this.form.floors.splice(i, 1);
				} else {
					this.form.floors.push(floor);
				}
			},
			goToStep: function(n) {
				if (n == this.step) return;
				if (n >= 1 && n <= 4) this.step = n;
			},
			next: function() {
				if (this.step == 4) return;
				this.step++;
			},
			prev: function() {
				if (this.step == 1) return;
				this.step--;
			},
			copyAddress: function() {
				this.form.addtl_address = this.form.address;
				this.form.addtl_suite = this.form.suite;
				this.form.addtl_city = this.form.city;
				this.form.addtl_province = this.form.province;
				this.form.addtl_post = this.form.post;
			},
			depositAmt: function(i) {
				if (i.deposit_type == 'amount') {
					return i.deposit_amount;
				} else if (i.deposit_type == 'balance') {
					return (this.totalPrice * i.deposit_amount / 100) - this.downpayment;
				} else if (i.deposit_type == 'percent') {
					return (this.totalPrice * i.deposit_amount / 100);
				}

				return 0;
			},
			depositDesc: function(i) {
				if (i.deposit_type == 'amount')      return 'On Signing';
				if (i.deposit_due_on == 'occupancy') return 'Occupancy';
				if (i.deposit_due_on == 'days')      return i.deposit_time + ' days';
				if (i.deposit_due_on == 'date')      return moment(i.deposit_time).format('MMM DD, YYYY');
			},
			submit: function() {
				var $form = $('#gform_5');

				$form.find('input[name=input_5]').val(this.form.firstname);
				$form.find('input[name=input_6]').val(this.form.lastname);
				$form.find('input[name=input_7]').val(this.form.phone);
				$form.find('input[name=input_8]').val(this.form.email);
				$form.find('input[name=input_11]').val(this.form.dob);
				$form.find('input[name=input_12]').val(this.form.address);
				$form.find('input[name=input_13]').val(this.form.suite);
				$form.find('input[name=input_14]').val(this.form.city);
				$form.find('input[name=input_15]').val(this.form.province);
				$form.find('input[name=input_16]').val(this.form.post);

				$form.find('input[name=input_18]').val(this.form.addtl_firstname);
				$form.find('input[name=input_19]').val(this.form.addtl_lastname);
				$form.find('input[name=input_20]').val(this.form.addtl_phone);
				$form.find('input[name=input_21]').val(this.form.addtl_email);
				$form.find('input[name=input_22]').val(this.form.addtl_dob);
				$form.find('input[name=input_23]').val(this.form.addtl_address);
				$form.find('input[name=input_24]').val(this.form.addtl_suite);
				$form.find('input[name=input_25]').val(this.form.addtl_city);
				$form.find('input[name=input_26]').val(this.form.addtl_province);
				$form.find('input[name=input_27]').val(this.form.addtl_post);

				$form.find('input[name=input_28]').val(this.floorString);
				$form.find('input[name=input_29]').val(this.form.parking);
				$form.find('input[name=input_30]').val(this.form.locker);
				$form.find('input[name=input_31]').val(this.form.notes);

				$form.find('input[name=input_32]').val(this.form.project_name);
				$form.find('input[name=input_33]').val(this.form.floorplan);

				$form.submit();
			}
		},
		computed: {
			downpayment: function() {
				for (i in this.depositStructure) {
					if (this.depositStructure[i].deposit_type == 'amount') return parseInt(this.depositStructure[i].deposit_amount);
				}
				return 0;
			},
			totalPrice: function() {
				var parking = (this.form.parking === 1) ? this.parkingPrice : 0;
				var locker = (this.form.locker === 1) ? this.lockerPrice : 0;
				return this.unitPrice + parking + locker;
			},
			totalDeposit: function() {
				var deposit = 0;
				var occupancy = 0;
				for (i in this.depositStructure) {
					var row = this.depositStructure[i];
					if (row.deposit_due_on == 'occupancy') {
						occupancy = occupancy + parseInt(row.deposit_amount);
					} else if (row.deposit_type == 'balance' || row.deposit_type == 'percent') {
						deposit = deposit + parseInt(row.deposit_amount);
					}
				}
				var output = '';
				if (deposit) output += deposit +'%';
				if (deposit && occupancy) output += " + " + occupancy + '% (occupancy)';
				return output;
			},
			floorString: function() {
				var string = '';
				for (i in this.form.floors) {
					if (i > 0) string += ' ';
					string += this.form.floors[i];
				}
				return string;
			}
		},
		data: {
			loaded: true,
			step: <?= isset($_POST['gform_is_valid']) && !$_POST['gform_is_valid'] ? 4 : 1 ?>,
			submitted: <?= isset($_POST['gform_is_valid']) ? 'true' : 'false' ?>,
			gform_is_valid: <?= isset($_POST['gform_is_valid']) && $_POST['gform_is_valid'] ? 'true' : 'false' ?>,
			addtlPurchaser: false,
			unitPrice: <?= $unitPrice ?>,
			parking: "<?= $parking ?>",
			locker: "<?= $locker ?>",
			parkingPrice: <?= $parking_fee ?: 0 ?>,
			lockerPrice: <?= $locker_fee ?: 0 ?>,
			floorplan: <?= json_encode( $floorplan ) ?>,
			projectFloorplan: <?= json_encode( $project_floorplan ) ?>,
			depositStructure: <?= json_encode( get_field('deposit_structure')) ?>,
			form: {
				project_name: "<?= get_the_title() ?>",
				floorplan: "<?= $floorplan['suite_name'] ?>",
				floors: [],
				parking: '',
				locker: '',
				notes: '',
				firstname: '',
				lastname: '',
				phone: '',
				email: '',
				dob: '',
				address: '',
				suite: '',
				city: '',
				province: '',
				post: '',
				addtl_firstname: '',
				addtl_lastname: '',
				addtl_phone: '',
				addtl_email: '',
				addtl_dob: '',
				addtl_address: '',
				addtl_suite: '',
				addtl_city: '',
				addtl_province: '',
				addtl_post: '',
			}
		}
});
</script>
