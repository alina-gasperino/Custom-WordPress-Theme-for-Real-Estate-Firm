<?php

// add_action( 'wp_ajax_update_floorplans_alt_tags', 'update_floorplans_alt_tags' );
// add_action( 'wp_ajax_nopriv_update_floorplans_alt_tags', 'update_floorplans_alt_tags' );

function update_floorplans_alt_tags() {

	global $wpdb;

	$images = $wpdb->get_results("SELECT a.ID as 'id', a.post_parent, a.post_name, a.post_title, a.guid, p.post_name as 'parent_slug', p.post_title as 'parent_title' from by_posts as a join $wpdb->posts as p on p.ID = a.post_parent where a.post_type = 'attachment' and a.post_mime_type = 'image/jpeg' and a.post_name like 'floorplans_%'");

	$n = 0;

	foreach ($images as $image) {

		$file = get_attached_file( $image->id, $unfiltered = true );

		$document_id = substr( trim( $image->post_title, 'floorplans_' ), 0, 28 );
		$old_prefix = "floorplans_{$document_id}";
		$new_prefix = "{$image->parent_slug}-condos-floor-plan";
		$new_file = str_replace($old_prefix, $new_prefix, $file);

		// copy($file, $new_file);
		rename($file, $new_file);
		// unlink($file);

		$alt_text = "{$image->parent_title} Condos Floor Plans";
		$guid = str_replace(WP_CONTENT_DIR, WP_CONTENT_URL, $new_file);
		$post_data = [
			'ID' => $image->id,
			'post_title' => basename( $new_file ),
			'post_name' => sanitize_title( basename( $new_file ) ),
			'post_excerpt' => $alt_text,
			'post_content' => $alt_text,
		];

		wp_update_post( $post_data );

		$wpdb->query( $wpdb->prepare("UPDATE $wpdb->posts set guid = %s where ID = %d", $guid, $image->id) );

		$meta = wp_get_attachment_metadata( $image->id, $unfiltered = true );
		$meta['file'] = str_replace($old_prefix, $new_prefix, $meta['file']);

		foreach ($meta['sizes'] as &$thumbnail) {
			$old_thumbnail = pathinfo($file, PATHINFO_DIRNAME) . '/' . $thumbnail['file'];
			$new_thumbnail = str_replace($old_prefix, $new_prefix, $old_thumbnail);
			// copy($old_thumbnail, $new_thumbnail);
			rename($old_thumbnail, $new_thumbnail);
			// unlink($old_thumbnail);
			$thumbnail['file'] = basename($new_thumbnail);
		}

		update_post_meta( $image->id, '_wp_attachment_image_alt', $alt_text );

		$wp_upload_dir = wp_upload_dir();
		$upload_basedir = $wp_upload_dir['basedir'];
		$attached_file = ltrim( $new_file, trailingslashit( $upload_basedir ) );
		update_post_meta( $image->id, '_wp_attached_file', $attached_file );

		wp_update_attachment_metadata( $image->id, $meta );

		$n++;

	}

	echo '<pre>'; print_r($n); echo '</pre>';

	wp_die();

}


