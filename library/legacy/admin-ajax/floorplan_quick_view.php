<?php

add_action( 'wp_ajax_floorplan_quick_view', 'floorplan_quick_view' );
add_action( 'wp_ajax_nopriv_floorplan_quick_view', 'floorplan_quick_view' );

function floorplan_quick_view() {
	include get_template_directory() . '/templates/project/floorplan-quick-view.php';
	wp_die();
}
