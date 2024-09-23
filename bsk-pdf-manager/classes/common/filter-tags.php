<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class BSKPDFM_Common_Filter_Tags {
    
    public static function show_tags_filter_bar( $shortcode_atts, $shortcode_type, $categories_id_array ){
        $tags_filter_type = strtoupper( $shortcode_atts['tags'] );
        if( $tags_filter_type != 'ALL' && 
            $tags_filter_type != 'ASSIGNED' &&
            $tags_filter_type != 'SPECIFIC' ){
            
            return false;
        }
        
        $tags_specific = trim( $shortcode_atts['tags_specific'] );
        $tags_default = intval( trim( $shortcode_atts['tags_default'] ) );
        $tags_exclude = $shortcode_atts['tags_exclude'];
        $tags_right = BSKPDFM_Common_Display::process_shortcodes_bool_attrs( 'tags_align_right', $shortcode_atts );
        
        global $wpdb;
        $tags_table = esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name);
        $tags_rel_tble = esc_sql($wpdb->prefix.BSKPDFManager::$_rels_tbl_name);
        
        //read plugin settings
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
        
        $tags_array = array();
        if( $tags_filter_type == 'ALL' ){
            $sql = 'SELECT `id`, `title` FROM `'.$tags_table.'` WHERE 1 AND `type` LIKE "TAG" ORDER BY `title` ASC ';
            $results = $wpdb->get_results( $sql );
            if( $results && is_array( $results ) && count( $results ) ){
                foreach( $results as $tag_obj ){
                    $tags_array[$tag_obj->id] = $tag_obj->title;
                }
            }
        }else if( $tags_filter_type == 'ASSIGNED' ){
            $sql = '';
            
            if( $shortcode_type == 'pdfs' ){
                $sql = 'SELECT DISTINCT( R.`cat_id` ) AS tagid, T.`title` FROM `'.$tags_rel_tble.'` AS R '.
                       'LEFT JOIN `'.$tags_table.'` AS T ON R.`cat_id` = T.`id` '.
                       'WHERE 1 '.
                       'AND T.`type` LIKE "TAG" '.
                       'ORDER BY T.`title` ASC';
            }else if( ( $shortcode_type == 'category' || $shortcode_type == 'selector' ) &&
                      $categories_id_array && count( $categories_id_array ) > 0 ){
                
                $sql = 'SELECT DISTINCT( R.`cat_id` ) AS tagid, T.`title` FROM `'.$tags_rel_tble.'` AS R '.
                           'LEFT JOIN `'.$tags_table.'` AS T ON R.`cat_id` = T.`id` '.
                           'WHERE R.`pdf_id` IN ( SELECT `pdf_id` FROM `'.$tags_rel_tble.'` WHERE `cat_id` IN ('.( implode(',', esc_sql($categories_id_array) ) ).') ) '.
                           'AND T.`type` LIKE "TAG" '.
                           'ORDER BY T.`title` ASC';
            }

            if( $sql ){
                $results = $wpdb->get_results( $sql );
                if( $results && is_array( $results ) && count( $results ) ){
                    foreach( $results as $tag_obj ){
                        $tags_array[$tag_obj->tagid] = $tag_obj->title;
                    }
                }
            }

        }else if( $tags_filter_type == 'SPECIFIC' && !empty($tags_specific) && trim($tags_specific) ){
            $tags_specific_array = explode( ',', $tags_specific );
            if( $tags_specific_array && is_array( $tags_specific_array ) && count( $tags_specific_array ) ){
                $sql = 'SELECT `id`, `title` FROM `'.$tags_table.'` '.
                       'WHERE `id` IN('.( implode(',', esc_sql($tags_specific_array) ) ).') AND `type` LIKE "TAG" ORDER BY `title` ASC';
                $results = $wpdb->get_results( $sql );
                if( $results && is_array( $results ) && count( $results ) ){
                    foreach( $results as $tag_obj ){
                        $tags_array[$tag_obj->id] = $tag_obj->title;
                    }
                }
            }
        }
        
        //remove exclude
        if( count( $tags_array ) ){
            $tags_exclude_array = explode( ',', $tags_exclude ); 
            foreach( $tags_exclude_array as $tag_id_str ){
                unset( $tags_array[intval($tag_id_str)] );
            }
        }
        
        if( count($tags_array) < 1 ){
            return false;
        }
        
        $class = $tags_right ? ' bsk-pdfm-tags-filter-align-right' : '';
        $tags_filter_str  = '<div class="bsk-pdfm-tags-filter-container'.esc_attr($class).'">';
        
        $default_existed = false;
        $tags_filter_loop = '';
        foreach( $tags_array as $tag_id => $tags_title ){
            $class = "bsk-pdfm-tags-filter-anchor";
            if( $tags_default == $tag_id ){
                $class .= ' active';
                $default_existed = true;
            }
            $tags_filter_loop .= '<a href="javascript:void(0);" class="'.esc_attr($class).'" data-tagid="'.esc_attr($tag_id).'">'.esc_html($tags_title).'</a>';
        }
        $tags_filter_loop .= '';
        
        //add all tags
        $tags_filter_str .= '<div class="bsk-pdfm-tags-filter-anchors">';
        $class = "bsk-pdfm-tags-filter-anchor";
        if( !$default_existed ){
            $class .= ' active';
        }
        $tags_filter_str .= '<a href="javascript:void(0);" class="'.esc_attr($class).'" data-tagid="-1">All</a>';
        $tags_filter_str .= $tags_filter_loop;
        $tags_filter_str .= '</div>';
        
        //add ajax loader
        $ajax_loader_span_class = 'bsk-pdfm-tags-filter-ajax-loader';
        $ajax_loader_img_url = apply_filters( 'bsk-pdfm-tags-ajax-loader-url', BSKPDFManager::$_ajax_loader_img_url, $ajax_loader_span_class );
        $tags_filter_str .= '<div class="bsk-pdfm-tags-filter-ajax-loader" style="display:none;"><span class="'.esc_attr($ajax_loader_span_class).'"><img src="'.esc_url($ajax_loader_img_url).'" /></span></div>';
            
        $tags_filter_str .= '</div>';
        
        $data_to_return = array( 'default' => $default_existed ? $tags_default : -1, 'filters' => $tags_filter_str );
        
        return $data_to_return;
    }//end of function
    
}//end of class
