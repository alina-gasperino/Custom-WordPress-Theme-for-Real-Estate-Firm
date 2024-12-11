<?php

defined( 'ABSPATH' ) || die();

//now hook into wordpress ajax function to catch any ajax requests
remove_action( 'wp_ajax_avia_ajax_search', 'avia_ajax_search' );
remove_action( 'wp_ajax_nopriv_avia_ajax_search', 'avia_ajax_search' );
add_action( 'wp_ajax_avia_ajax_search', 'talk_ajax_search' );
add_action( 'wp_ajax_nopriv_avia_ajax_search', 'talk_ajax_search' );

function talk_ajax_search() {
	$out = '';

	$s = filter_var($_REQUEST['s'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES );
    $for_map = filter_var($_REQUEST['for_map'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES );
    if($for_map == 'true'){
        talk_ajax_search_for_map();
        return;
    }


	$args = array(
		's' => $s,
    );

	$results = false;

	$projects = TalkCondo_Ajax_Map_Query::get_projects($args);
    $projectsHtml = "";

//    $out .= '<pre style="display: none">'.json_encode($projects[0]).'</pre>';

	if ($projects) {
//		$out .='<h4>Projects</h4>';
        $projectsHtml .='<h4>Condos</h4>';
		foreach ($projects as $project) {
			$result = array(
				'title'       => $project['title'],
				'link'        => $project['permalink'],
				'image'       => '',
				'icon'        => '<i class="far fa-building"></i>',
                'description' => ' (' . str_ireplace( $s, '<em>' . $s . '</em>', $project['address'] ) . ')',
			);
//			$out .=talk_ajax_result( $result );
            $projectsHtml .= talk_ajax_result( $result );
		}

		$results = true;
	}

	// Include tax labels to avoid needing additional function calls
	$taxes = array(
		'city' => 'Cities',
		'neighbourhood' => 'Neighbourhoods  ',
		'developer' => 'Developers'
	);

	$q = new WP_Term_Query( array( 'search' => htmlspecialchars( $s ), 'taxonomy' => array_keys($taxes), 'number' => 5 ) );
	$terms = $q->get_terms();

	if( $terms && !is_wp_error( $terms ) ){
	    $html = [];

        foreach ( $terms as $term ){
            /** @var WP_Term $term */
	        $permalink = get_term_link( $term );

	        $result = array(
	            'title' => htmlspecialchars_decode($term->name),
                'description' => ' (' . sprintf( _n( '%s Project', '%s Projects', $term->count, 'talkcondo' ), $term->count ) . ')',
				'link' => ! is_wp_error( $permalink ) ? $permalink : '',
	            'image' => '',
	            'icon' => '<i class="fa fa-map-marker"></i>',
            );

	        if( 'neighbourhood' == $term->taxonomy ){
//                $test = array(
//                    'meta__map_geometry' => get_term_meta($term->term_id,'map_geometry', true ),
//                    'meta__center' => get_term_meta($term->term_id,'center', true )
//                );
                $term->{'map_geometry'} = get_term_meta($term->term_id,'map_geometry', true );
                $term->{'center'} = get_term_meta($term->term_id,'center', true );
            }

	        if( 'developer' == $term->taxonomy ){
		        $result['icon'] = '<span data-av_iconfont="entypo-fontello" data-av_icon="" aria-hidden="true" class="label iconfont"></span>';

		        if( $logo = get_term_meta($term->term_id,'logo', true ) ) {
			        $result['image'] = sprintf( '<img src="%s" />', $logo['sizes']['thumbnail'] );
		        }
	        }

	        $html[ $term->taxonomy ] .= talk_ajax_result( $result );
        }



        foreach ( $taxes as $tax => $label ){
        	if( ! empty( $html[ $tax ] ) ){
		        $out .='<h4>' . $label . '</h4>';
		        $out .=$html[ $tax ];
	        }
            if('neighbourhood' == $tax)
                $out .=$projectsHtml;
        }
		$results = true;
	}

    if(empty($out) && !empty($projectsHtml))
        $out .=$projectsHtml;

	if (!$results) {
		$out .="<span class='ajax_search_entry ajax_not_found'>";
		$out .="<span class='ajax_search_content'>";
		$out .="<span class='ajax_search_title'>" . __("Sorry, nothing matched your criteria", 'avia_framework') . "</span>";
		$out .="</span>";
		$out .="</span>";
	}

	wp_send_json( [ 'content' => $out ] );

}

function talk_ajax_result( $args ) {

	$s = filter_var( $_REQUEST['s'], FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_AMP );
	$s = str_replace('&#39;', '&#8217;', $s);
	$s = str_replace('&#38;', '&#038;', $s);

	$title = str_ireplace( $s, '<em>' . $s . '</em>', $args['title'] );

	$out = '<a class="ajax_search_entry" href="' . $args['link'] . '">';
		$out .= '<span class="ajax_search_content">';
			$out .= '<span class="ajax_search_title">';
				$out .= $title;
				if( $args['description'] ) {
					$out .= $args['description'];
				}
				// $out .= ' (' . $args['address'] . ')';
			$out .= '</span>';
		$out .= '</span>';
	$out .= '</a>';

	return $out;

}

function talk_ajax_result_for_map( $args ) {

	$s = filter_var( $_REQUEST['s'], FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_AMP );
	$s = str_replace('&#39;', '&#8217;', $s);
	$s = str_replace('&#38;', '&#038;', $s);

	$title = str_ireplace( $s, '<em>' . $s . '</em>', $args['title'] );

	$out = '<a class="ajax_search_entry for_map" href="javascript:void(0)" data-json="'.htmlspecialchars(json_encode($args), ENT_QUOTES, 'UTF-8').'">';
		$out .= '<span class="ajax_search_content">';
			$out .= '<span class="ajax_search_title">';
				$out .= $title;
			$out .= '</span>';
		$out .= '</span>';
	$out .= '</a>';

	return $out;

}

function talk_ajax_search_for_map() {
    $out = '';
    $s = filter_var($_REQUEST['s'], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES );

    $args = array(
        's' => $s,
    );

    $results = false;


    // Include tax labels to avoid needing additional function calls
    $taxes = array(
        'neighbourhood' => 'Neighbourhoods',
    );

    $q = new WP_Term_Query( array( 'search' => htmlspecialchars( $s ), 'taxonomy' => array_keys($taxes), 'number' => 10 ) );
    $terms = $q->get_terms();

    if( $terms && ! is_wp_error( $terms ) ){
        $html = [];

//        $out .= '<pre style="display: none">'.json_encode($terms).'</pre>';
        foreach ( $terms as $term ){
            $result = array(
                'id'       => $term->term_id,
                'taxonomy' => 'neighbourhood',
                'title'    => htmlspecialchars_decode($term->name),
                'location' => array(
                    'type'         => 'Region',
                    'center'       => get_term_meta($term->term_id,'center', true ),
                    'geometry'     => get_term_meta($term->term_id,'map_geometry', true ),
                ),
            );

            $html[ $term->taxonomy ] .= talk_ajax_result_for_map( $result );
        }

        foreach ( $taxes as $tax => $label ){
            if( ! empty( $html[ $tax ] ) ){
                $out .='<h4>' . $label . '</h4>';
                $out .=$html[ $tax ];
            }
        }

        $results = true;
    }

    if(strlen($s)>1){
        $projects = TalkCondo_Ajax_Map_Query::get_projects($args);

        if ($projects) {
            $out .='<h4>Condos</h4>';
            foreach ($projects as $project) {
                $result = array(
                    'id'          => $project['post_id'],
                    'taxonomy'    => 'project',
                    'title'       => $project['title'],
                    'location'    => $project['location'],
                );
                $out .=talk_ajax_result_for_map( $result );
            }

            $results = true;
        }
    }

    if (!$results) {
        $out .="<span class='ajax_search_entry ajax_not_found'>";
        $out .="<span class='ajax_search_content'>";
        $out .="<span class='ajax_search_title'>" . __("Sorry, nothing matched your criteria", 'avia_framework') . "</span>";
        $out .="</span>";
        $out .="</span>";
    }

    wp_send_json( [ 'content' => $out ] );

}
