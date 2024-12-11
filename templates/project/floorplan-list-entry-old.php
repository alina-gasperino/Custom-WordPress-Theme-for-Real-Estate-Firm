<?php

if ($floorplan['availability'] == 'Available') {
	$availability = 'available';
} elseif ($floorplan['availability'] == 'Sold Out') {
	$availability = 'sold-out';
}

$title = $floorplan['suite_name'];
if ($floorplan['specialty_type']) $title .= " (" . $floorplan['specialty_type'] . ")";
$title = trim_title($title, 26);

// $attachment = get_post($floorplan['image']);
$thumbnail = wp_get_attachment_image_src( $floorplan['image'], 'medium' );
$fullimage = wp_get_attachment_image_src( $floorplan['image'], 'full' );

$data_attributes = [
	'fullimage' => $fullimage[0],
	'thumbnail' => $thumbnail[0],
	'price' => $floorplan['price'],
	'formattedprice' => $floorplan['price'] ? number_format($floorplan['price']) : '',
	'beds' => $floorplan['beds'],
	'baths' => $floorplan['baths'],
	'size' => $floorplan['size'],
	'availability' => $availability,
	'suite-name' => $floorplan['suite_name'],
	'project' => get_the_title(),
	'projectname' => get_the_title(),
	'exposure' => @implode('/', $floorplan['exposure']),
	'floorplan-url' => get_floorplans_link( $floorplan['image'] ),
	'project-url' => get_permalink(),
	'reserve-link' => get_floorplans_link( $floorplan['image'] ) . 'reserve',
];

$quick_view_url = admin_url('admin-ajax.php?action=floorplan_quick_view&attachment_id=' . $floorplan['image']);
$quick_view_url = $fullimage[0];

?>

<tr class="floorplan <?php echo $availability ?>" <?php echo html_data_attributes($data_attributes) ?> itemprop="containsPlace" itemscope itemtype="http://schema.org/Apartment">

	<link itemprop="containedInPlace" href="#project-<?php the_ID(); ?>">

	<td class="floorplan__availability clickable <?php echo $availability ?>">
		<i class='fa fa-circle'>
			<span class="popup"><?php echo $floorplan['availability'] ?></span>
		</i>
	</td>

	<td class="floorplan__thumbnail clickable">
		<?php if( $thumbnail = wp_get_attachment_image_src( $floorplan['image'], 'thumbnail' )[0] ): ?>
			<meta itemprop="photo" content="<?php echo wp_get_attachment_image_src( $floorplan['image'], 'full' )[0]; ?>">
			<img class="floorplan__image lazy" height="80" width="80" alt="<?php echo floorplan_alt_text($floorplan, $post) ?>" src="<?= get_stylesheet_directory_uri() . '/assets/images/gallery-placeholder-square.jpg' ?>" data-original="<?php echo $thumbnail ?>" />
		<?php endif; ?>
	</td>

	<td class="floorplan__title" itemprop="name">
		<a href="<?php echo get_floorplans_link( $floorplan['image'] ); ?>" class="passthrough">
			<b><?php echo $title; ?> <i class="fas fa-chevron-right fa-xs"></i></b>
		</a>
	</td>

	<td class="floorplan__type clickable">
		<span itemprop="numberOfRooms">
			<?php echo $floorplan['beds'] ?> Bed
		</span>
		<span itemprop="amenityFeature">
			<?php echo $floorplan['baths'] ?> Bath
		</span>
	</td>

	<td class="floorplan__size clickable" itemprop="floorSize">
		<?php echo $floorplan['size'] ?> sq.ft
	</td>

	<td class="floorplan__view clickable" itemprop="amenityFeature">
		<?php echo (is_array($floorplan['exposure']) && $floorplan['exposure']) ? implode('<br>', $floorplan['exposure']) : '' ?>
	</td>

	<td class="floorplan__range clickable mobile-only">
		<meta itemprop="amenityFeature" content="<?php printf( esc_html( 'Floor Range(s): %s', 'talkcondo' ), floorplan_floor_ranges($floorplan) ); ?>">
		<?php echo floorplan_floor_ranges($floorplan) ?>
	</td>

	<td class="floorplan__pricefrom">

		<?php if ($floorplan['availability'] == 'Sold Out'): ?>
			<?php esc_html_e( 'Sold Out', 'talkcondo' ); ?>
		<?php elseif (get_field('hide_pricing')): ?>
			<a class="floorplan__latest-pricing-lock" href="<?php echo leadpages_form_url() ?>" data-leadbox="<?php echo leadpages_form_data_id() ?>">
				<?php esc_html_e( 'Contact For Pricing', 'talkcondo' ); ?>
			</a>
		<?php elseif (project_locked()): ?>
			<a class="floorplan__latest-pricing-lock" href="<?php echo leadpages_form_url() ?>" data-leadbox="<?php echo leadpages_form_data_id() ?>">
				<style type="text/css">
					#Lock #Top {
						transform-origin: 2.7em;
						transition: all 500ms;
					}
					#Lock:hover #Top {
						transform: rotateY(180deg);
					}
				</style>
				<svg id="Lock" width="28px" height="36px" preserveAspectRatio="none" viewBox="0 0 48 54" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="overflow: visible">
					<defs>
						<path d="M44.0544027,0.0191972344 C44.6434062,0.0246323266 45.2348762,0.136504642 45.7661632,0.373384081 C46.4035104,0.657367652 46.9510765,1.10349814 47.3541048,1.63704303 C47.7541733,2.16469991 47.9643203,2.80015278 47.9983582,3.44013489 L47.9983582,25.3671088 C47.9653069,26.0637065 47.7152024,26.7544161 47.2510045,27.3097014 C46.7142911,27.9601007 45.953125,28.4660172 45.0873786,28.6707391 C44.7371337,28.7604181 44.3725829,28.7812526 44.0119786,28.808881 L4.11414499,28.808881 C3.37024443,28.7980108 2.6125314,28.6689274 1.9687713,28.3124759 C1.20711185,27.8966913 0.575191014,27.2594268 0.252571011,26.4971551 C0.101620368,26.1461387 0.0295981654,25.7711173 0,25.396096 L0,3.44602291 C0.0315713764,2.83683965 0.219519727,2.22992101 0.585057069,1.71766357 C1.12226377,0.961732819 1.95446552,0.37021361 2.91196617,0.142845583 C3.70223719,-0.0510060408 43.5911914,0.0160267639 44.0544027,0.0191972344 Z" id="path-1"></path>
					</defs>
					<g id="Bottom" transform="translate(0.000000, 24.961119)">
						<mask id="mask-2" fill="white">
							<use xlink:href="#path-1"></use>
						</mask>
						<use fill="#5B5B5B" fill-rule="nonzero" xlink:href="#path-1"></use>
					</g>
					<g id="Top" transform="translate(5.278339, 0.000000)" fill="#5B5B5B" fill-rule="nonzero">
						<path d="M19.8332373,0.0725155052 C21.6870691,0.167722937 23.5310348,0.52438083 25.2738734,1.16814093 C28.281047,2.26721947 30.9251498,4.23204435 33.0226731,6.63344217 C34.1187919,7.88889768 35.0570537,9.28642439 35.7644498,10.7969174 C36.5344954,12.4307362 37.0287848,14.1913337 37.2463313,15.9835026 C37.3686704,16.9479095 37.3958021,17.9216891 37.3864293,18.8930023 C37.3869226,21.3156121 37.385936,23.7387152 37.3869226,26.1613251 C37.8496406,26.162805 -0.752286704,26.1845103 0.054756606,26.1613251 C0.0557432115,23.673106 0.054756606,21.1848869 0.0552499087,18.6966678 C0.036504404,17.2981545 0.146510919,15.8947081 0.434599728,14.5248064 C0.814442851,12.681334 1.50999974,10.9039641 2.47884635,9.29086412 C3.34113957,7.84844686 4.41407307,6.53724813 5.6187184,5.36762729 C6.83372309,4.1871538 8.18882576,3.14628498 9.66873403,2.32000286 C10.9843725,1.58300855 12.3952184,1.01669698 13.8558878,0.640800281 C15.8019672,0.136644864 17.8279616,-0.0305847709 19.8332373,0.0725155052 Z M13.4854175,9.19417678 C11.9645651,10.0574566 10.6548462,11.288247 9.6825465,12.7405303 C8.63477145,14.3047933 8.088192,16.1803304 8.05711392,18.058334 C8.0531675,20.7596599 8.05563401,23.4604925 8.05612732,26.1618184 C15.1660999,26.1613251 22.2760726,26.1618184 29.3860452,26.1618184 C29.3865385,23.4915706 29.3875251,20.821816 29.3855519,18.1520615 C29.3697662,16.3026695 28.8596912,14.4498243 27.8558201,12.890001 C26.9160783,11.4298249 25.6315179,10.1896617 24.1402637,9.30023687 C22.5197641,8.33385677 20.6249882,7.87311199 18.7450114,7.87903163 C16.9192979,7.86768566 15.0782921,8.28995282 13.4854175,9.19417678 Z"></path>
					</g>
				</svg>
			</a>
		<?php elseif ( $floorplan['availability'] == 'Available' && $floorplan['price'] ) : ?>
			<b>$<?php echo number_format( $floorplan['price'] ); ?></b><br>
			<?php $price = floorplan_price_per_sqft($floorplan);?>
			<?php echo ($price) ? "$" . number_format($price) . "/sq.ft" : '-'; ?>
		<?php else:?>
			<a class="floorplan__latest-pricing-lock" href="<?php echo leadpages_form_url() ?>" data-leadbox="<?php echo leadpages_form_data_id() ?>">
				<?php esc_html_e( 'Contact For Pricing', 'talkcondo' ); ?>
			</a>
		<?php endif;?>
	</td>

	<?php if ($floorplan['price_history'] && is_user_logged_in()): ?>
		<?php $first_price = $floorplan['price_history'][0]['history_price'] ?>
		<td class="floorplan__pricefrom floorplan__view-all">
			<b>$<?php echo number_format($first_price) ?></b>
			<?php $diff = $floorplan['price'] - $first_price ?>
			<?php if ($diff < 0): ?>
				<span class='diff negative' style="color: red;">-$<?= number_format(abs($diff)) ?></span>
			<?php elseif ($diff > 0): ?>
				<span class='diff positive' style="color: green;">+$<?= number_format(abs($diff)) ?></span>
			<?php elseif ($diff == 0): ?>
				<span class='diff'>+$<?= number_format(abs($diff)) ?></span>
			<?php endif ?>
			<a class="quick-view noLightbox" data-thumb="<?php echo $thumbnail[0] ?>" href="<?php echo $quick_view_url ?>"></a>
		</td>
	<?php else: ?>
	<td class="floorplan__view-all">
		<a class="view-all" href="<?php echo get_floorplans_link() ?>" itemprop="sameAs">
			<i class="fa fa-fw fa-angle-right fa-2x"></i>
		</a>
		<a class="btn full-info btn-gray" href="<?php echo get_floorplans_link( $floorplan['image'] ) ?>" itemprop="url">
			<?php esc_html_e( 'More Info', 'talkcondo' ); ?>
		</a>
		<a class="quick-view noLightbox" data-thumb="<?php echo $thumbnail[0] ?>" href="<?php echo $quick_view_url ?>"></a>
	</td>
	<?php endif ?>

</tr>
