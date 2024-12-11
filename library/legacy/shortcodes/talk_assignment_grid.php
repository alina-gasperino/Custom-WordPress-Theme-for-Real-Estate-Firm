<?php


if ( !class_exists( 'talk_assignment_grid' ) )
{
	class talk_assignment_grid
	{
		static  $grid = 0;
		static  $preview_template = array();
		protected $atts;
		protected $entries;

		function __construct($atts = array())
		{
			$this->atts = shortcode_atts(array(	'style'		=> '',
										 		'linking' 	=> '',
										 		'columns' 	=> '4',
		                                 		'items' 	=> '16',
		                                 		'contents' 	=> 'title',
		                                 		'sort' 		=> 'no',
		                                 		'paginate' 	=> 'yes',
		                                 		'categories'=> '',
		                                 		'preview_mode' => 'auto',
                                                'image_size' => 'portfolio',
		                                 		'post_type'	=> 'assignment',
		                                 		'taxonomy'  => 'city',
		                                 		'one_column_template' => 'special',
		                                 		'set_breadcrumb' => true, //no shortcode option for this, modifies the breadcrumb nav, must be false on taxonomy overview
		                                 		'class'		=> "",
		                                 		'custom_markup'	=> '',
		                                 		'fullscreen'	=> false,
		                                 		), $atts, 'av_portfolio');



		    if($this->atts['linking'] == 'ajax')
				add_action('wp_footer' , array($this, 'print_preview_templates'));
		}

		//generates the html of the post grid
		public function html()
		{
			if(empty($this->entries) || empty($this->entries->posts)) return;

			avia_post_grid::$grid ++;
			extract($this->atts);

			$container_id 		= avia_post_grid::$grid;
			$extraClass 		= 'first';
			$grid 				= 'one_fourth';
			if($preview_mode == 'auto') $image_size = 'portfolio';
			$post_loop_count 	= 1;
			$loop_counter		= 1;
			$output				= "";
			$style_class		= empty($style) ? 'no_margin' : $style;
			$total				= $this->entries->post_count % 2 ? "odd" : "even";

			if($set_breadcrumb && is_page())
			{
				$_SESSION["avia_{$post_type}"] = get_the_ID();
			}

			switch($columns)
			{
				case "1": $grid = 'av_fullwidth';  if($preview_mode == 'auto') $image_size = 'featured'; break;
				case "2": $grid = 'av_one_half';   break;
				case "3": $grid = 'av_one_third';  break;
				case "4": $grid = 'av_one_fourth'; if($preview_mode == 'auto') $image_size = 'portfolio_small'; break;
				case "5": $grid = 'av_one_fifth';  if($preview_mode == 'auto') $image_size = 'portfolio_small'; break;
				case "6": $grid = 'av_one_sixth';  if($preview_mode == 'auto') $image_size = 'portfolio_small'; break;
			}
			
			if($fullscreen && $preview_mode =='auto' && $image_size == "portfolio_small") $image_size = 'portfolio';

			$output .= $sort != "no" ? $this->sort_buttons($this->entries->posts, $this->atts) : "";

			if($linking == "ajax")
			{
				global $avia_config;
				
				$container_class = $fullscreen ? "container" : "";
				
				$output .= "<div class='portfolio_preview_container {$container_class}' data-portfolio-id='{$container_id}'>
								<div class='ajax_controlls iconfont'>
									<a href='#prev' class='ajax_previous' 	".av_icon_string('prev')."></a>
									<a href='#next' class='ajax_next'		".av_icon_string('next')."></a>
									<a class='avia_close' href='#close'		".av_icon_string('close')."></a>
								</div>
								<div class='portfolio-details-inner'></div>
							</div>";
			}
			$output .= "<div class='{$class} grid-sort-container isotope {$style_class}-container with-{$contents}-container grid-total-{$total} grid-col-{$columns} grid-links-{$linking}' data-portfolio-id='{$container_id}'>";

			foreach ($this->entries->posts as $entry)
			{
				$the_id 	= $entry->ID;
				$parity		= $post_loop_count % 2 ? 'odd' : 'even';
				$last       = $this->entries->post_count == $post_loop_count ? " post-entry-last " : "";
				$post_class = "post-entry post-entry-{$the_id} grid-entry-overview grid-loop-{$post_loop_count} grid-parity-{$parity} {$last}";
				$sort_class = $this->sort_cat_string($the_id, $this->atts);

				switch($linking)
				{
					case "lightbox":  $link = aviaHelper::get_url('lightbox', get_post_thumbnail_id($the_id));	break;
					default: 		  $link = get_permalink($the_id); break;
				}

				$title_link  = get_permalink($the_id);
				$custom_link = get_post_meta( $the_id ,'_portfolio_custom_link', true) != "" ? get_post_meta( $the_id ,'_portfolio_custom_link_url', true) : false;

				if($custom_link)
				{
					$title_link = $link = $custom_link;
				}

				$excerpt 	= '';
				$title 		= '';

				switch($contents)
				{
					case "excerpt": $excerpt = $entry->post_excerpt; $title = $entry->post_title; break;
					case "title": $excerpt = ''; $title = $entry->post_title;  break;
					case "only_excerpt": $excerpt = $entry->post_excerpt; $title = ''; break;
					case "no": $excerpt = ''; $title = ''; break;
				}

				$custom_overlay = apply_filters('avf_portfolio_custom_overlay', "", $entry);
				$link_markup 	= apply_filters('avf_portfolio_custom_image_container', array("a href='{$link}' title='".esc_attr(strip_tags($title))."' ",'a'), $entry);

				$title 			= apply_filters('avf_portfolio_title', $title, $entry);
				$title_link    	= apply_filters('avf_portfolio_title_link', $title_link, $entry);
				$image_attrs    = apply_filters('avf_portfolio_image_attrs', array(), $entry);
				
				$title = '<h5>' . $title . '</h5>';
				$title .= '<p>' . get_field('squarefootage', $entry->ID) . 'sq ft. </p>';
				$title .= (get_field('bedrooms', $entry->ID)) ? '<p>' . get_field('bedrooms', $entry->ID) . ' bedrooms</p>' : '';
				$title .= (get_field('exposure', $entry->ID)) ? '<p>Exposure: ' . get_field('exposure', $entry->ID) . '</p>' : '';
				$title .= (get_field('price', $entry->ID)) ? '<p class="price">' . get_field('price', $entry->ID) . '</p>' : '';

				$labels = '';

				// get the sales status terms				
				$terms = get_the_terms( $entry->ID, 'salesstatus' );

				// if (is_array($terms)) $salesstatus = reset($terms)->name;
				$salesstatus = (is_array($terms)) ? reset($terms)->name : '';

				if ( $salesstatus ) {
					// $labels .= '<span class="project-label">' . $salesstatus . '</span>';
					$labels .= '<a class="salesstatus" href="' . get_term_link( reset($terms)->slug, 'salesstatus') . '"><span class="project-label">' . $salesstatus . '</span></a>';
				}

				// explode the extra info labels field
				foreach ( explode( '&', get_field( 'customtags', $entry->ID ) ) as $tag ) {
					if ($tag) {
						$labels .= '<span class="project-label infotag">' . $tag . '</span>';
					}
				}

				if ($labels) $labels = "<div class='project-labels'>$labels</div>";

                if($columns == "1" && $one_column_template == 'special')
                {
                    $extraClass .= ' special_av_fullwidth ';

                    $output .= "<div data-ajax-id='{$the_id}' class=' grid-entry flex_column isotope-item all_sort {$style_class} {$post_class} {$sort_class} {$grid} {$extraClass}'>";
                    $output .= "<article class='main_color inner-entry' ".avia_markup_helper(array('context' => 'entry','echo'=>false, 'id'=>$the_id, 'custom_markup'=>$custom_markup)).">";
                    $output .= apply_filters('avf_portfolio_extra', "", $entry);

                    $output .= "<div class='av_table_col first portfolio-entry grid-content'>";

                    if(!empty($title))
                    {
                        $markup = avia_markup_helper(array('context' => 'entry_title','echo'=>false, 'id'=>$the_id, 'custom_markup'=>$custom_markup));
                        $output .= '<header class="entry-content-header">';
                        $output .= "<h2 class='portfolio-grid-title entry-title' $markup><a href='{$title_link}'>".$title."</a></h2>";
                        $output .= '</header>';
                    }

                    if(!empty($excerpt))
                    {
                        $markup = avia_markup_helper(array('context' => 'entry_content','echo'=>false, 'id'=>$the_id, 'custom_markup'=>$custom_markup));

                        $output .= "<div class='entry-content-wrapper'>";
                        $output .= "<div class='grid-entry-excerpt entry-content' $markup>".$excerpt."</div>";
                        $output .= "</div>";
                    }
                    $output .= '<div class="avia-arrow"></div>';
                    $output .= "</div>";

                    $image = get_the_post_thumbnail( $the_id, $image_size, $image_attrs );
                    if(!empty($image))
                    {
                        $output .= "<div class='av_table_col portfolio-grid-image'>";
                        $output .= "<".$link_markup[0]." data-rel='grid-".avia_post_grid::$grid."' class='grid-image avia-hover-fx'>";
                        $output .= $custom_overlay.$image;
                        $output .= "</".$link_markup[1].">";
	                    $output .= $labels;
                        $output .= "</div>";
                    }
                    $output .= '<footer class="entry-footer"></footer>';
                    $output .= "</article>";
                    $output .= "</div>";
                }
                else
                {
                    $extraClass .= ' default_av_fullwidth ';

                    $imgData = wp_get_attachment_image_src( get_post_thumbnail_id( $the_id ) , $image_size );

                    $output .= "<div data-ajax-id='{$the_id}' class=' grid-entry flex_column isotope-item all_sort {$style_class} {$post_class} {$sort_class} {$grid} {$extraClass}'>";
                    $output .= "<article class='main_color inner-entry' ".avia_markup_helper(array('context' => 'entry','echo'=>false, 'id'=>$the_id, 'custom_markup'=>$custom_markup)).">";
                    $output .= apply_filters('avf_portfolio_extra', "", $entry);
                    $output .= '<div class="project-grid-image-container">';
                    $output .= "<".$link_markup[0]." data-rel='grid-".avia_post_grid::$grid."' class='grid-image avia-hover-fx'>".$custom_overlay;
                    $output .= (has_post_thumbnail($the_id)) ?
	                    '<img class="lazy" data-original="' . $imgData[0] . '" width="' . $imgData[1] . '" height="' . $imgData[2] . '" />' :
                    	'<img class="placeholder" src="' . get_stylesheet_directory_uri() . '/assets/images/thumbnail-placeholder.jpg' . '" />';
                    $output .= "</".$link_markup[1].">";
                    $output .= $labels;
                    $output .= '</div>';
                    $output .= !empty($title) || !empty($excerpt) ? "<div class='grid-content'><div class='avia-arrow'></div>" : '';

                    if(!empty($title))
                    {
                        $markup = avia_markup_helper(array('context' => 'entry_title','echo'=>false, 'id'=>$the_id, 'custom_markup'=>$custom_markup));
                        $output .= '<header class="entry-content-header">';
                        $output .= "<h3 class='grid-entry-title entry-title' $markup><a href='{$title_link}' title='".esc_attr(strip_tags($title))."'>".$title."</a></h3>";
                        $output .= '</header>';
                    }
                    $output .= !empty($excerpt) ? "<div class='grid-entry-excerpt entry-content' ".avia_markup_helper(array('context'=>'entry_content','echo'=>false, 'id'=>$the_id, 'custom_markup'=>$custom_markup)).">".$excerpt."</div>" : '';
                    $output .= !empty($title) || !empty($excerpt) ? "</div>" : '';
                    $output .= '<footer class="entry-footer"></footer>';
                    $output .= "</article>";
                    $output .= "</div>";
                }


				$loop_counter ++;
				$post_loop_count ++;
				$extraClass = "";

				if($loop_counter > $columns)
				{
					$loop_counter = 1;
					$extraClass = 'first';
				}
			}

			$output .= "</div>";

			//append pagination
			if($paginate == "yes" && $avia_pagination = avia_pagination($this->entries->max_num_pages, 'nav')) $output .= "<div class='pagination-wrap pagination-{$post_type}'>{$avia_pagination}</div>";

			return $output;
		}

		//generates the html for the sort buttons
		protected function sort_buttons($entries, $params)
		{
			//get all categories that are actually listed on the page
			$categories = get_categories(array(
				'taxonomy'	=> $params['taxonomy'],
				'hide_empty'=> 1
			));

			$current_page_cats 	= array();
			$cat_count 			= array();
			$display_cats 		= is_array($params['categories']) ? $params['categories'] : array_filter(explode(',',$params['categories']));

			foreach ($entries as $entry)
			{
				if($current_item_cats = get_the_terms( $entry->ID, $params['taxonomy'] ))
				{
					if(!empty($current_item_cats))
					{
						foreach($current_item_cats as $current_item_cat)
						{
							if(empty($display_cats) || in_array($current_item_cat->term_id, $display_cats))
							{
								$current_page_cats[$current_item_cat->term_id] = $current_item_cat->term_id;

								if(!isset($cat_count[$current_item_cat->term_id] ))
								{
									$cat_count[$current_item_cat->term_id] = 0;
								}

								$cat_count[$current_item_cat->term_id] ++;
							}
						}
					}
				}
			}

			$output = "<div class='sort_width_container av-sort-".$this->atts['sort']."' data-portfolio-id='".avia_post_grid::$grid."' ><div id='js_sort_items' >";
			$hide 	= count($current_page_cats) <= 1 ? "hidden" : "";


			$first_item_name = apply_filters('avf_portfolio_sort_first_label', __('All','avia_framework' ), $params);
			$first_item_html = '<span class="inner_sort_button"><span>'.$first_item_name.'</span><small class="av-cat-count"> '.count($entries).' </small></span>';
			$output .= apply_filters('avf_portfolio_sort_heading', "", $params);
			
			
			if(strpos($this->atts['sort'], 'tax') !== false) $output .= "<div class='av-current-sort-title'>{$first_item_html}</div>";
			$output .= "<div class='sort_by_cat {$hide} '>";
			$output .= '<a href="#" data-filter="all_sort" class="all_sort_button active_sort">'.$first_item_html.'</a>';


			foreach($categories as $category)
			{
				if(in_array($category->term_id, $current_page_cats))
				{
					//fix for cyrillic, etc. characters - isotope does not support the % char
					$category->category_nicename = str_replace('%', '', $category->category_nicename);

					$output .= 	"<span class='text-sep ".$category->category_nicename."_sort_sep'>/</span>";
					$output .= 		'<a href="#" data-filter="'.$category->category_nicename.'_sort" class="'.$category->category_nicename.'_sort_button" ><span class="inner_sort_button">';
					$output .= 			"<span>".esc_html(trim($category->cat_name))."</span>";
					$output .= 			"<small class='av-cat-count'> ".$cat_count[$category->term_id]." </small></span>";
					$output .= 		"</a>";
				}
			}

			$output .= "</div></div></div>";

			return $output;
		}


		//get the categories for each post and create a string that serves as classes so the javascript can sort by those classes
		protected function sort_cat_string($the_id, $params)
		{
			$sort_classes = "";
			$item_categories = get_the_terms( $the_id, $params['taxonomy']);

			if(is_object($item_categories) || is_array($item_categories))
			{
				foreach ($item_categories as $cat)
				{
					//fix for cyrillic, etc. characters - isotope does not support the % char
					$cat->slug = str_replace('%', '', $cat->slug);
					
					$sort_classes .= $cat->slug.'_sort ';
				}
			}

			return $sort_classes;
		}

		protected function build_preview_template( $entry )
		{
			if(isset(avia_post_grid::$preview_template[$entry->ID])) return;
			avia_post_grid::$preview_template[$entry->ID] = true;

			$id 					= $entry->ID;
			$output 				= "";
			$defaults 				= array( 'ids' => get_post_thumbnail_id( $id ), 'text' => apply_filters( 'get_the_excerpt', $entry->post_excerpt) , "method" => 'gallery' , "auto" => "", "columns" => 5);
			$params['ids'] 			= get_post_meta( $id ,'_preview_ids', true);
			$params['text']		  	= get_post_meta( $id ,'_preview_text', true);
			$params['method']	  	= get_post_meta( $id ,'_preview_display', true);
			$params['interval']		= get_post_meta( $id ,'_preview_autorotation', true);
			$params['columns']      = get_post_meta( $id ,'_preview_columns', true);
			$params['preview_size'] = apply_filters('avf_ajax_preview_image_size',"gallery");
			$params['autoplay']		= is_numeric($params['interval']) ? "true" : "false";

			$link = get_post_meta( $id ,'_portfolio_custom_link', true) != "" ? get_post_meta( $id ,'_portfolio_custom_link_url', true) : get_permalink($id);


			//merge default and params array. remove empty params with array_filter
			$params = array_merge($defaults, array_filter($params));
			
			$params = apply_filters('avf_portfolio_preview_template_params', $params, $entry);

			//set the content
			$content = str_replace(']]>', ']]&gt;', apply_filters('the_content', $params['text'] )); unset($params['text']);

			//set images
			$string = "";

			//set first class if preview images are deactivated
			$nogalleryclass = '';
			$params['ajax_request'] = true;
			switch($params['method'])
			{
				case 'gallery':

					$params['style'] =  "big_thumb";
					foreach($params as $key => $param) $string .= $key."='".$param."' ";
					$images = do_shortcode("[av_gallery {$string}]");
				break;

				case 'slideshow':
					$params['size'] = $params['preview_size'];
					foreach($params as $key => $param) $string .= $key."='".$param."' ";
					$images = do_shortcode("[av_slideshow {$string}]");
				break;

				case 'list':
					$images = $this->post_images($params['ids']);
				break;

				case 'no':
					$images = false;
					$nogalleryclass = ' no_portfolio_preview_gallery ';
				break;
			}

			$output .= "<div class='ajax_slide ajax_slide_{$id}' data-slide-id='{$id}' >";

				$output .= "<article class='inner_slide $nogalleryclass' ".avia_markup_helper(array('context' => 'entry','echo'=>false, 'id'=>$id, 'custom_markup'=>$this->atts['custom_markup'])).">";

				if(!empty($images))
				{
					$output .= "<div class='av_table_col first portfolio-preview-image'>";
					$output .= $images;
					$output .= "</div>";
				}

				if(!empty($nogalleryclass)) $nogalleryclass .= ' first ';

					$output .= "<div class='av_table_col $nogalleryclass portfolio-entry portfolio-preview-content'>";

                        $markup = avia_markup_helper(array('context' => 'entry_title','echo'=>false, 'id'=>$id, 'custom_markup'=>$this->atts['custom_markup']));
                        $output .= '<header class="entry-content-header">';
						$output .= "<h2 class='portfolio-preview-title entry-title' $markup><a href='{$link}'>".$entry->post_title."</a></h2>";
                        $output .= '</header>';

						$output .= "<div class='entry-content-wrapper entry-content' ".avia_markup_helper(array('context' => 'entry_content','echo'=>false, 'id'=>$id, 'custom_markup'=>$this->atts['custom_markup'])).">";
						$output .= $content;
						$output .= "</div>";
						$output .= "<span class='avia-arrow'></span>";
					$output .= "</div>";

                $output .= '<footer class="entry-footer"></footer>';
				$output .= "</article>";

			$output .= "</div>";

		return "<script type='text/html' id='avia-tmpl-portfolio-preview-{$id}'>\n{$output}\n</script>\n\n";

		}

		protected function post_images($ids)
		{
			if(empty($ids)) return;

			$attachments = get_posts(array(
				'include' => $ids,
				'post_status' => 'inherit',
				'post_type' => 'attachment',
				'post_mime_type' => 'image',
				'order' => 'ASC',
				'orderby' => 'post__in')
				);

			$output = "";

			foreach($attachments as $attachment)
			{
				$img	 = wp_get_attachment_image_src($attachment->ID, 'large');

                $alt = get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);
                $alt = !empty($alt) ? esc_attr($alt) : '';
                $title = trim($attachment->post_title) ? esc_attr($attachment->post_title) : "";
                $description = trim($attachment->post_content) ? esc_attr($attachment->post_content) : "";

				$output .= " <a href='".$img[0]."' class='portolio-preview-list-image' title='".$description."' ><img src='".$img[0]."' title='".$title."' alt='".$alt."' /></a>";
			}

			return $output;
		}




		public function print_preview_templates()
		{
			foreach ($this->entries->posts as $entry)
			{
				echo $this->build_preview_template( $entry );
			}
		}



		//fetch new entries
		public function query_entries($params = array())
		{
			$query = array();
			if(empty($params)) $params = $this->atts;

			if(!empty($params['categories']))
			{
				//get the portfolio categories
				$terms 	= explode(',', $params['categories']);
			}

			$page = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : get_query_var( 'page' );
			if(!$page || $params['paginate'] == 'no') $page = 1;

			//if we find categories perform complex query, otherwise simple one
			if(isset($terms[0]) && !empty($terms[0]) && !is_null($terms[0]) && $terms[0] != "null")
			{
				$query = array(	'orderby' 	=> 'post_date',
								'order' 	=> 'DESC',
								'paged' 	=> $page,
								'posts_per_page' => $params['items'],
								'post_type' => $params['post_type'],
								'tax_query' => array( 	array( 	'taxonomy' 	=> $params['taxonomy'],
																'field' 	=> 'id',
																'terms' 	=> $terms,
																'operator' 	=> 'IN')));
			}
			else
			{
				$query = array(	'paged'=> $page, 'posts_per_page' => $params['items'], 'post_type' => $params['post_type']);
			}

			$query = apply_filters('avia_post_grid_query', $query, $params);

			$this->entries = new WP_Query( $query );

		}


		//function that allows to set the query to an existing post query. usually only needed on pages that already did a query for the entries, like taxonomy archive pages.
		//Shortcode uses the query_entries function above
		public function use_global_query()
		{
			global $wp_query;
			$this->entries = $wp_query;
		}


		public function set_entries( $entries ) {
			$this->entries = $entries;
		}



	}
}


/*
Example: how to order posts randomly on page load. put this into functions.php

add_filter('avia_post_grid_query','avia_order_by_random');
function avia_order_by_random($query)
{
	$query['orderby'] = 'rand';
	return $query;
}
*/
