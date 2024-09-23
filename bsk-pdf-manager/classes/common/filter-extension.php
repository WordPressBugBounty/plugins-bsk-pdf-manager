<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class BSKPDFM_Common_Filter_Extension {
    
    public static function show_extension_filter_bar( $shortcode_atts ){
        $extension_filter_type = strtoupper( $shortcode_atts['extension_filter'] );
        if( $extension_filter_type != 'ALL' && 
            $extension_filter_type != 'ALL_EXISTED' ){
            
            return false;
        }
        
        $extension_filter_bar_only = BSKPDFM_Common_Display::process_shortcodes_bool_attrs('extension_filter_only', $shortcode_atts);
        
        $extension_filter_default = '';
        $extension_filter_specific= '';
        $extension_filter_exclude = '';
        $extension_filter_labels = '';

        //read plugin settings
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
        $supported_extension = false;
        if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
            if( isset($plugin_settings['supported_extension']) ){
                $supported_extension = $plugin_settings['supported_extension'];
			}
		}
        if( !$supported_extension || !is_array($supported_extension) || !in_array( 'pdf', $supported_extension ) ){
            $supported_extension = array( 'pdf' );
        }
        
        $extension_filter_anchor_array = array();
        if( $extension_filter_type == 'ALL' ){
            $extension_filter_anchor_array = $supported_extension;
        }else if( $extension_filter_type == 'ALL_EXISTED' ){
            global $wpdb;
            
            $table = $wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name;
            $sql = 'SELECT DISTINCT( SUBSTRING_INDEX(`file_name`, \'.\', -1 ) ) AS S FROM `'.esc_sql($table).'` '.
                   'WHERE 1 ORDER BY S ASC';
            $results = $wpdb->get_results( $sql );
            
            foreach( $results as $obj ){
                if( trim($obj->S) == '' ){
                    continue;
                }
                $extension_filter_anchor_array[] = $obj->S;
            }
            sort( $extension_filter_anchor_array );
        }
        
        if( count($extension_filter_anchor_array) < 1 ){
            return false;
        }
        
        $extension_filter_str  = '<div class="bsk-pdfm-extension-filter-container">';
        $extension_filter_str .= '<div class="bsk-pdfm-extension-filter-anchors">';
        foreach( $extension_filter_anchor_array as $anchor_text ){
            $class="bsk-pdfm-extension-filter-anchor";
            if( $extension_filter_default && 
                strtoupper($extension_filter_default) == strtoupper($anchor_text) ){
                
                $class .= ' active';
            }
            $anchor_text = strip_tags( $anchor_text );
            $anchor_text = strtolower( $anchor_text );
            $label = isset( $extension_filter_labels_array[$anchor_text] ) ? $extension_filter_labels_array[$anchor_text] : $anchor_text; 
            $extension_filter_str .= '<a href="javascript:void(0);" class="'.esc_attr($class).'" data-extension="'.esc_attr($anchor_text).'">'.esc_html($label).'</a>';
        }
        $extension_filter_str .= '</div>';
        
        $ajax_loader_span_class = 'bsk-pdfm-extension-filter-ajax-loader';
        $ajax_loader_img_url = apply_filters( 'bsk-pdfm-extension-ajax-loader-url', BSKPDFManager::$_ajax_loader_img_url, $ajax_loader_span_class );
        $extension_filter_str .= '<div class="bsk-pdfm-extension-filter-ajax-loader" style="display:none;"><span class="'.esc_attr($ajax_loader_span_class).'"><img src="'.esc_url($ajax_loader_img_url).'" /></span></div>';
            
        $extension_filter_str .= '</div>';
        
        $data_to_return = array( 'filters' => $extension_filter_str );
        
        return $data_to_return;
    }//end of function
    
}//end of class
