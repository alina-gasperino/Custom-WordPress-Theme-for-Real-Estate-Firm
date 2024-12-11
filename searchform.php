<?php

/**
 * Search Form
 *
 * Used to output a search form when using <?php get_search_form( $echo ); ?>
 *
 * @link http://codex.wordpress.org/Function_Reference/get_search_form
 */

$search_params = apply_filters('avf_frontend_search_form_param', array(
	'placeholder'  	=> 'Search condos',
	'search_id'	   	=> 's',
	'form_action'	=> home_url( '/' ),
	'ajax_disable'	=> false
));

$disable_ajax = $search_params['ajax_disable'] == false ? "" : "av_disable_ajax_search";
$icon  = av_icon_char('search');
$class = av_icon_class('search');
?>

<form method="get" action="<?= $search_params['form_action'] ?>" id="searchform" class="search-form <?= $disable_ajax ?>">
	<div>
		<input type="text" id="s" name="<?= $search_params['search_id'] ?>" value="<?= get_search_query() ?>" placeholder='<?= $search_params['placeholder'] ?>' autocomplete="off">
		<button type="submit" id="searchsubmit" class="button <?= $class ?>"><i class='fa fa-search'></i></button>
		<div id="clear_form" style="display: none;">
			<i class="fa fa-close"></i>
		</div>
		<div class="ajax_load">
			<i class="fa fa-spin fa-spinner"></i>
		</div>
		<?php do_action('ava_frontend_search_form') ?>
	</div>
</form>
<script type="text/javascript">
	jQuery(document).ready(function(){
		if (jQuery(window).width() < 1200){
	        jQuery('#searchform div input').attr('placeholder','Search condos');
	        jQuery('#map-search #searchform div input').attr('placeholder','Jump to...');
	    } else {
            jQuery('#map-search #searchform div input').attr('placeholder','Jump to Location or Project');
        }

		jQuery(window).resize(function(){
			if (jQuery(window).width() < 1200){
		        jQuery('#map-search #searchform div input').attr('placeholder','Jump to...');
		    }else{
		    	jQuery('#map-search #searchform div input').attr('placeholder','Jump to Location or Project');
		    }
		})
	})
</script>
