<?php

// add_action( 'wp_ajax_fix_duplicate_attachments', 'fix_duplicate_attachments' );
// add_action( 'wp_ajax_nopriv_fix_duplicate_attachments', 'fix_duplicate_attachments' );

function fix_duplicate_attachments() {
	global $wpdb;

	if ( ! function_exists('wp_generate_attachment_metadata') ) include( ABSPATH . 'wp-admin/includes/image.php' );

	$imgs = $wpdb->get_results("SELECT p.ID, p.post_name, p.post_title, p.guid, j.meta_value from $wpdb->postmeta as m join $wpdb->posts as p on p.ID = m.post_id join (
		select meta_value
		from $wpdb->postmeta
	    where meta_key = '_wp_attached_file'
		group by meta_value
		having count(meta_value) > 1 limit 1) as j on j.meta_value = m.meta_value
		where p.post_type = 'attachment'
		and m.meta_value like '%floor-plan%.jpg'
		order by m.meta_value;");

	foreach ($imgs as $n => $img) {

		if (preg_match('/\-jpg$/', $img->post_name)) continue;

		preg_match('/\-\d*$/', $img->post_name, $matches);

		$img->new_meta_value = preg_replace('/\.jpg$/', $matches[0] . '.jpg', $img->meta_value);
		$img->attached_file = get_attached_file($img->ID);
		$img->new_file = preg_replace('/\.jpg$/', $matches[0] . '.jpg', $img->attached_file);
		$img->guid = preg_replace('/\.jpg$/', $matches[0] . '.jpg', $img->guid);

		echo '<pre>'; print_r($img); echo '</pre>';

		if ( file_exists($img->attached_file) ) {
			copy( $img->attached_file, $img->new_file );
		}

		wp_update_post( $img, $wp_error );
		update_post_meta( $img->ID, '_wp_attached_file', $img->new_meta_value );
		$metadata = wp_generate_attachment_metadata( $img->ID, $img->new_file );
		wp_update_attachment_metadata( $img->ID, $metadata );

	}

	wp_die();

}

