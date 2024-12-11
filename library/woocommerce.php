<?php

/**
 * Products Per Row
 * 
 * Change number or products per row to 3.
 */

if ( !function_exists( 'talk_loop_shop_columns' ) ) {

	function talk_loop_shop_columns() {

		return 3;

	}

}

add_filter( 'loop_shop_columns', 'talk_loop_shop_columns' );
