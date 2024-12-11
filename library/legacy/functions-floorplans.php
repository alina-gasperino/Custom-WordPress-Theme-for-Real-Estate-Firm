<?php

function download_floorplans_pdfs( $post_id ) {

	if (!$post_id) return;

	$urls = explode( "\n", get_field('floorplanspdfs', $post_id) );
	$urls = array_map('trim', $urls);
	$urls = array_filter($urls);

	foreach ($urls as $url) {
		$googledoc_id = extract_google_doc_id_from_url( $url );
		download_floorplans_pdf( $googledoc_id, $post_id );
	}

}


function in_initial_batch( $googledoc_id ) {

	$file = get_stylesheet_directory() . '/library/legacy/gsheet_import/floorplans-previously-imported.php';

	if (!file_exists($file)) return false;

	// $previously_downloaded_ids = json_decode( file_get_contents($file) );
	$previously_downloaded_ids = include $file;

	return (is_array($previously_downloaded_ids) && in_array($googledoc_id, $previously_downloaded_ids));

}


function is_floorplan_attached($googledoc_id, $post_id) {

	global $wpdb;

	// if (in_initial_batch($googledoc_id)) return true;

	$filename = "floorplans_{$googledoc_id}.pdf";
	$query = $wpdb->prepare("SELECT id from $wpdb->posts where post_type = 'attachment' and post_mime_type = 'application/pdf' and post_parent = %d and post_title like '%s'", $post_id, pathinfo($filename, PATHINFO_FILENAME) . '%');
	$attachment_id = $wpdb->get_var($query);
	if ($attachment_id) return true;

	return false;

}


function download_floorplans_pdf( $googledoc_id, $post_id ) {

	if (!$post_id) return;

	global $wpdb;

	if ( ! function_exists('media_handle_sideload') ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
	}

	$download_link = google_download_link($googledoc_id);
	$filename = "floorplans_{$googledoc_id}.pdf";
	$file_path = floorplans_temp_dir() . $filename;

	if ( ! file_exists( $file_path ) ) {
		$dl = download_url( $download_link );
		if ( ! is_wp_error($dl) ) {
			@rename($dl, $file_path);
		}
	}

	$file_array = ['tmp_name' => $file_path, 'name' => $filename];

	$attachment_id = media_handle_sideload( $file_array, $post_id );

	if ( is_wp_error($attachment_id) ) {
		return;
	}

	## must use field id in case no rows exist
	$converted = (in_initial_batch($googledoc_id)) ? '1' : '0';
	$custom_fields = ['pdf' => $attachment_id, 'googledoc_id' => $googledoc_id, 'converted' => $converted];
	add_row( 'field_58fe9cea12cdd', $custom_fields, $post_id );

	return $attachment_id;

}


function converted_floorplans_pdf_images( $googledoc_id ) {

	$images = glob( floorplans_temp_dir() . "floorplans_{$googledoc_id}*.jpg" );

	return $images;

}


function attached_floorplans_pdf_images( $googledoc_id, $post_id ) {

	global $wpdb;

	$qry = $wpdb->prepare("SELECT * from $wpdb->posts as p join $wpdb->postmeta as m on m.post_id = p.id where post_type = 'attachment'and post_mime_type = 'image/jpeg'and guid like '%%condos-floor-plan%%.jpg'and post_parent = %d and m.meta_key = 'googledoc_id' and m.meta_value = %s"
		, $post_id, $googledoc_id );
	$attachments = $wpdb->get_results($qry);

	return $attachments;
}


function floorplan_pdf_converted($googledoc_id, $post_id) {

	if (in_initial_batch($googledoc_id)) return true;
	if (converted_floorplans_pdf_images($googledoc_id)) return true;
	if (attached_floorplans_pdf_images( $googledoc_id, $post_id )) return true;

}


function convert_floorplans_pdfs( $post_id ) {

	$pdfs = get_field( 'floorplans_pdfs', $post_id );

	foreach ($pdfs as $n => $pdf) {

		if (isset($pdf['converted']) && $pdf['converted'] == '1') continue;

		if (in_initial_batch($pdf['document_id'])) {
			$pdf['converted'] = '1';
			update_row( 'field_58fe9cea12cdd', $n+1, $pdf, $post_id );
			continue;
		}

		if (converted_floorplans_pdf_images($pdf['googledoc_id'])) continue;

		convert_floorplans_pdf( $pdf, $post_id );

		$pdf['converted'] = '1';
		update_row( 'field_58fe9cea12cdd', $n+1, $pdf, $post_id );
	}

}


function convert_floorplans_pdf( $pdf, $post_id ) {

	$googledoc_id = $pdf['googledoc_id'];
	$pdf_filepath = get_attached_file($pdf['pdf']);

	try {
		$pdf_imagick = new Imagick;
		$pdf_imagick->setResolution(200,200);
		$pdf_imagick->readimage($pdf_filepath);
	} catch (Exception $e) {
		// echo '<pre>'; print_r($e->getMessage()); echo '</pre>';
	}

	foreach ($pdf_imagick as $i => $image) {
		try {
			$n = str_pad($i, 2, "0", STR_PAD_LEFT);
			$img_file = floorplans_temp_dir() . "floorplans_{$googledoc_id}-{$n}.jpg";

			if (defined('Imagick::ALPHACHANNEL_REMOVE')) {
				$image->setImageAlphaChannel( Imagick::ALPHACHANNEL_REMOVE );
				$image->setImageBackgroundColor( new ImagickPixel('white') );
				$image->setImageFormat("jpg");
				$image->setCompressionQuality("95");
				$image->writeImage( $img_file );
			} else {
				$blank_imagick = new Imagick;
				$blank_imagick->newImage($image->getImageWidth(), $image->getImageHeight(), new ImagickPixel("white"));
				$blank_imagick->compositeImage($image, Imagick::COMPOSITE_OVER, 0, 0);
				$blank_imagick->setImageColorspace( $image->getImageColorspace() );
				$blank_imagick->setImageFormat("jpg");
				$blank_imagick->setCompressionQuality("95");
				$blank_imagick->writeImage( $img_file );
				$blank_imagick->clear();
			}

		} catch (Exception $e) {
			// echo '<pre>'; print_r($e->getMessage()); echo '</pre>';
		}
	}

	// $pdf['converted'] = '1';
	// update_row( 'field_58fe9cea12cdd', $i+1, $pdf, $post_id );

	$pdf_imagick->clear();

	return count($pdf_imagick);

}


function sideload_floorplans_images( $post_id ) {

	global $wpdb;

	if (!$post_id) return;

	$pdfs = get_field( 'floorplans_pdfs', $post_id );

	$urls = explode( "\n", get_field('floorplanspdfs', $post_id) );
	$urls = array_map('trim', $urls);
	$urls = array_filter($urls);
	$document_ids = array_map('extract_google_doc_id_from_url', $urls);

	// echo '<pre>'; print_r($document_ids); echo '</pre>';

	foreach ($document_ids as $document_id) {

		$query = $wpdb->prepare( "SELECT count('id') from $wpdb->posts where post_type = 'attachment' and post_mime_type = 'image/jpeg' and post_parent = %d and post_title like %s" , $post_id, "%{$document_id}%" );
		// echo '<pre>'; print_r($query); echo '</pre>';

		$attachment_ids = $wpdb->get_var($query);
		// echo '<pre>'; print_r($attachment_ids); echo '</pre>';
		if ($attachment_ids > 0) return;

		$images = glob( floorplans_temp_dir() . "floorplans_{$document_id}*.jpg" );
		// echo '<pre>'; print_r($images); echo '</pre>';

		foreach ($images as $img) {
			sideload_floorplans_image($img, $post_id, $document_id);
		}
	}
}


function sideload_floorplans_image( $filepath, $post_id, $document_id ) {

	global $wpdb;

	if (!$post_id) return;

	if ( ! function_exists('media_handle_sideload') ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
	}

	$parent = get_post( $post_id );

	## get the count of the current attachments
	$qry = $wpdb->prepare("SELECT * from $wpdb->posts where post_type = 'attachment' and post_mime_type = 'image/jpeg' and guid like '%%condos-floor-plan%%.jpg' and post_parent = %d", $post_id );
	$attachments = $wpdb->get_results($qry);

	$n = count( $attachments ) + 1;
	$n = str_pad( $n, 2, "0", STR_PAD_LEFT);

	$new_prefix = "{$parent->post_name}-condos-floor-plan-{$n}";
	$new_file = preg_replace('/floorplans_.*(?=\.jpg)/', $new_prefix, $filepath);

	rename($filepath, $new_file);

	$file_array = [
		'name' => basename($new_file),
		'tmp_name' => $new_file,
		'type' => 'image/jpeg',
	];

	$attachment_id = media_handle_sideload( $file_array, $post_id, null );

	if (is_wp_error($attachment_id)) return false;

	$alt_text = "{$parent->post_title} Condos Floor Plans";
	$post_data = [
		'ID' => $attachment_id,
		// 'post_title' => basename( $new_file ),
		// 'post_name' => sanitize_title( basename( $new_file ) ),
		'post_excerpt' => $alt_text,
		'post_content' => $alt_text,
	];

	wp_update_post( $post_data );

	update_post_meta( $attachment_id, 'previous_filename', pathinfo($filepath, PATHINFO_BASENAME) );
	update_post_meta( $attachment_id, 'googledoc_id', $document_id );
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', $alt_text );

	return add_row( 'field_585bee96d8174', ['image' => $attachment_id], $post_id );

}


function clear_floorplans_pdfs( $post_id ) {

	if (!$post_id) return;

	while ( have_rows('floorplans_pdfs', $post_id) ) {
		the_row();
		$pdf = get_sub_field('pdf');
		if ($pdf && $pdf['id']) wp_delete_attachment( $pdf['id'], true );
		delete_row('floorplans_pdfs');
	}

	return;

	foreach ($pdfs as $pdf) {
		wp_delete_attachment( get, true );
	}
	while (have_rows('floorplans_pdfs', $post_id)) {
		the_row();
		wp_delete_attachment( get, true );
	}

	$attachment_ids = $wpdb->get_col( $wpdb->prepare( "select meta_value from $wpdb->postmeta where post_id = %d and meta_key like 'floorplans_pdfs_%%_pdf'" , $post_id ) );
	if ($attachment_ids) {
		foreach ($attachment_ids as $id) {
			wp_delete_attachment( $id, true );
		}
	}

	$wpdb->query( $wpdb->prepare( "delete from $wpdb->postmeta where post_id = %d and meta_key like 'floorplans_pdfs_%%'" , $post_id ) );
	$wpdb->query( $wpdb->prepare( "delete from $wpdb->postmeta where post_id = %d and meta_key like '_floorplans_pdfs_%%'" , $post_id ) );
	$wpdb->query( $wpdb->prepare( "delete from $wpdb->postmeta where post_id = %d and meta_key = 'floorplans_pdfs" , $post_id ) );
	// update_post_meta( $post_id, 'floorplans', '0' );

}

function clear_floorplans_images( $post_id ) {

	global $wpdb;

	$attachment_ids = $wpdb->get_col( $wpdb->prepare( "select meta_value from $wpdb->postmeta where post_id = %d and meta_key like 'floorplans_%%_image'" , $post_id ) );
	if ($attachment_ids) {
		foreach ($attachment_ids as $id) {
			wp_delete_attachment( $id, true );
		}
	}

	$wpdb->query( $wpdb->prepare( "delete from $wpdb->postmeta where post_id = %d and meta_key like 'floorplans_%%'" , $post_id ) );
	$wpdb->query( $wpdb->prepare( "delete from $wpdb->postmeta where post_id = %d and meta_key like '_floorplans_%%'" , $post_id ) );
	$wpdb->query( $wpdb->prepare( "delete from $wpdb->postmeta where post_id = %d and meta_key = 'floorplans" , $post_id ) );
	// update_post_meta( $post_id, 'floorplans', '0' );

}


function get_project_googledoc_links($post_id = '') {
	global $post;
	$post_id = ($post_id) ?: $post->ID;

	$text = get_field('floorplanspdfs', $post_id);
	$links = explode("\n", $text);

	return $links;
}


function get_project_googledoc_ids($post_id = '') {
	global $post;
	$post_id = ($post_id) ?: $post->ID;

	$links = get_project_googledoc_links($post_id);
	$ids = array_map("extract_google_doc_id_from_url", $links);

	return $ids;
}


function floorplans_bounds( $prop, $limit, $post_id = '' ) {
	global $post;
	$post_id = ($post_id) ?: $post->ID;

	$floorplans = get_field( 'floorplans', $post_id );

	$arr = [];
	foreach ($floorplans as $floorplan) {
		if(isset($floorplan[$prop]) && is_numeric($floorplan[$prop])) {
			$arr[] = $floorplan[$prop];
		}
	}

	if (empty($arr)) return 0;

	if ($limit == 'min') $val = floor(min($arr));
	if ($limit == 'max') $val = ceil(max($arr));

	if ($prop == 'size' && $limit == 'min' && $val > 100) {
		$val = floor( $val / 100 ) * 100;
	}

	if ($prop == 'size' && $limit == 'max' && $val > 100) {
		$val = ceil( $val / 100 ) * 100;
	}

	return $val;
}


function floorplan_floor_ranges( $floorplan ) {

	$output = '';
	$arr = [];
	$min = 999999;
	$max = 0;

	if ($floorplan['floor_ranges']) {
		foreach ($floorplan['floor_ranges'] as $f) {
			if(strpos($f['range'], '-') === false){
				if($min > $f['range']) $min = $f['range'];
				if($max < $f['range']) $max = $f['range'];
			}else{
				list($fmin, $fmax) = explode('-', $f['range']);
				if($min > $fmin) $min = $fmin;
				if($max < $fmax) $max = $fmax;
			}
			// $arr[] = $f['range'];
		}
	}else{
		return '-';
	}

	// $arr = array_filter($arr);
	if($min == $max) return $min;
	return $min.'-'.$max;
}

function floorplan_alt_text($floorplan, $project) {
	$project = get_post( $project );


	if ($floorplan['suite_name']) {
		$output = $floorplan['suite_name'] . ' Floor Plan at ' . $project->post_title . ' - ' . $floorplan['size'] . ' sq.ft';
	} else {
		$output = ' Floor Plan at ' . $project->post_title;
	}

	return $output;

}
