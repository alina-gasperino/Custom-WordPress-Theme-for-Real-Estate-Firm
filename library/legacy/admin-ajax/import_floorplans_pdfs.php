<?php

add_action( 'wp_ajax_import_floorplans_pdfs', 'ajax_import_floorplans_pdfs' );
add_action( 'wp_ajax_nopriv_import_floorplans_pdfs', 'ajax_import_floorplans_pdfs' );

function ajax_import_floorplans_pdfs() {

	$post_id = filter_var($_GET['post_id'], FILTER_SANITIZE_NUMBER_INT);

	$data = [
		'post_id' => $post_id,
		'continue' => false,
		'message' => '',
	];

	$googledoc_ids = get_project_googledoc_ids($post_id);

	foreach ($googledoc_ids as $googledoc_id) {
		if ( ! is_floorplan_attached($googledoc_id, $post_id) ) {
			download_floorplans_pdf($googledoc_id, $post_id);
			$data['message'] = "Downloading Floorplan PDF: {$googledoc_id}";
			$data['continue'] = true;
			wp_send_json_success( $data );
		}
	}

	$pdfs = get_field('floorplans_pdfs', $post_id);
	foreach ($pdfs as $pdf) {
		if ( ! floorplan_pdf_converted($pdf['googledoc_id'], $post_id) ) {
			$pages = convert_floorplans_pdf($pdf, $post_id);
			$data['message'] = "Converting Floorplan PDF pages to images: {$pdf['googledoc_id']}";
			$data['continue'] = true;
			wp_send_json_success( $data );
		}
	}

	foreach ($googledoc_ids as $googledoc_id) {
		if ($images = converted_floorplans_pdf_images( $googledoc_id )) {
			foreach ($images as $n => $img) {
				sideload_floorplans_image($img, $post_id, $googledoc_id);
				$total = count($images);
				$data['message'] = "Finished Sideloading Image {$googledoc_id} - ({$total} remaining)";
				$data['continue'] = true;
				wp_send_json_success( $data );
			}
		}
	}

	$data['message'] = "All Done.  Nothing to do";
	wp_send_json_success( $data );
}
