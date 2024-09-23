<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class BSKPDFM_Common_Display {
    
    //for get parent category structure
    private static $current_parent_category_depth_to_get_options = 0;
    //for get all category structure
    private static $current_category_depth_to_get_options = 0;

    public static function process_shortcodes_bool_attrs( $attr_name, $attrs_array ) {
        if( !$attrs_array || !is_array($attrs_array) || count($attrs_array) < 1 || $attr_name == "" ){
            return false;
        }
        if( !isset($attrs_array[$attr_name]) ){
            return false;
        }
        
        $return_bool = false;
        if( $attrs_array[$attr_name] && is_string($attrs_array[$attr_name]) ){
			$return_bool = strtoupper($attrs_array[$attr_name]) == "YES" ? true : false;
			if( $return_bool == false ){
				$return_bool = strtoupper($attrs_array[$attr_name]) == 'TRUE' ? true : false;
			}
		}else if( is_bool($attrs_array[$attr_name]) ){
			$return_bool = $attrs_array[$attr_name];
		}

        return $return_bool;
    }//end of function
        
    public static function show_pdf_item_single_div( 
                                                     $pdf_item_obj, 
                                                     $description,
                                                     $featured_image, $featured_image_size, 
                                                     $default_thumbnail_html,
												     $open_target_str, $nofollow_tag_str, $column_class_item,
                                                     $show_pdf_title, $pdf_title_position,
                                                     $show_date_in_title, $date_format_str, $date_before_title
                                                   ){
        $utc_timezone = new DateTimeZone( 'UTC' );
        $date_class = ' data-date="'.wp_date( 'Y-m-d-D', strtotime($pdf_item_obj->last_date), $utc_timezone ).'"';
		
		$file_url = site_url().'/'.$pdf_item_obj->file_name;
		if( $pdf_item_obj->by_media_uploader ){
			$file_url = wp_get_attachment_url( $pdf_item_obj->by_media_uploader );
            if( $file_url == false ){
                return '';
            }
		}
		
        //PDF titile str
		$pdf_item_obj_title = esc_html($pdf_item_obj->title);
        $pdf_title_str = '<span class="bsk-pdfm-pdf-title-string">'.$pdf_item_obj_title.'</span>';
		if( $pdf_title_str == "" ){
			$pdf_item_obj_title_array = explode( '/', $file_url );
			$pdf_title_str = esc_html($pdf_item_obj_title_array[count($pdf_item_obj_title_array) - 1]);
		}
        if( $show_date_in_title ){
            $date_str = '<span class="bsk-pdfm-pdf-date">'.esc_html(date($date_format_str, strtotime($pdf_item_obj->last_date))).'</span>';
            $pdf_title_str = $date_before_title ? $date_str.$pdf_title_str : $pdf_title_str.$date_str;
        }
        
        //pdf title link
        $pdf_title_str = '<a href="'.esc_url($file_url).'"'.esc_attr($open_target_str.$nofollow_tag_str).'  title="'.esc_attr($pdf_item_obj_title).'" class="bsk-pdfm-pdf-link-for-title pdf-id-'.esc_attr($pdf_item_obj->id).'">'.$pdf_title_str.'</a>';
        /*
          * organise return str
          */
        $forStr = '<div class="bsk-pdfm-columns-single'.esc_attr($column_class_item).' pdf-id-'.esc_attr($pdf_item_obj->id).'"'.$date_class.'>';
        $forStr .= '<h3>'.$pdf_title_str.'</h3>';
        $forStr .= '</div>'."\n";
		
		return $forStr;
	}
    
    public static function show_pdfs_in_dropdown( $pdf_items_results, 
                                                $class, 
                                                $option_none_str, 
                                                $target, 
                                                $show_date_in_title, 
                                                $date_format_str,
                                                $date_before_title,
                                                $default_enable_permalink
                                              ){
        if( !$pdf_items_results || !is_array($pdf_items_results) || count($pdf_items_results) < 1 ){
            return '';
        }
        
        //read global embeded viewer settings
        $embedded_viewer_settings = self::get_embedded_viewer_settings();

        $utc_timezone = new DateTimeZone( 'UTC' );
        
        $forStr = '';
        $forStr .= '<select class="'.esc_attr($class).'"'.$target.'>'."\n";
        if( $option_none_str ){
            $forStr .= '<option value="" selected="selected">'.esc_attr($option_none_str).'</option>';
        }
        foreach($pdf_items_results as $pdf_item_obj ){
            if( $pdf_item_obj->file_name == "" &&  $pdf_item_obj->by_media_uploader < 1 ){
                continue;
            }
            $file_url = '';
            $file_extension = '';
            if( file_exists(BSKPDFManager::$_upload_root_path.$pdf_item_obj->file_name) ){
                $file_url = site_url().'/'.$pdf_item_obj->file_name;
            }
            if( $file_url == "" ){
                continue;
            }

            $file_extension_array = explode('.', $file_url );
            if( is_array( $file_extension_array ) && count($file_extension_array) > 1 ){
                $file_extension = strtolower( $file_extension_array[count($file_extension_array) - 1] );
            }

            //if pdfjs enabled
            if ( $embedded_viewer_settings['enable'] && $file_extension == 'pdf' ) {
                $file_url = BSK_PDFM_PLUGIN_URL . 'pdfjs/web/viewer.html?file=' . $file_url . $embedded_viewer_settings['paras'];
            }

            if( $default_enable_permalink ){
                $file_url = site_url().'/bsk-pdf-manager/'.$pdf_item_obj->slug.'/';
            }

            $option_text = $pdf_item_obj->title;
            if( $show_date_in_title ){
                if( $date_before_title ){
                    $option_text = wp_date( 'Y-m-d-D', strtotime($pdf_item_obj->last_date), $utc_timezone ).$option_text;
                }else{
                    $option_text .= wp_date( 'Y-m-d-D', strtotime($pdf_item_obj->last_date), $utc_timezone );
                }
            }
            $id_str = ' id="'.wp_date( 'Y-m-d-D', strtotime($pdf_item_obj->last_date), $utc_timezone ).'-'.$pdf_item_obj->id.'"';
            $forStr .= '<option value="'.esc_attr($file_url).'"'.$id_str.'>'.esc_attr($option_text).'</option>'."\n";
        }
        $forStr .= '</select>';
        
        return $forStr;
    }
    
    public static function show_pdfs_in_dropdown_only_options( $pdf_items_results, 
                                                    $option_none_str, 
                                                    $show_date_in_title, 
                                                    $date_format_str,
                                                    $date_before_title,
                                                    $default_enable_permalink
                                                  ){
        //read global embeded viewer settings
        $embedded_viewer_settings = self::get_embedded_viewer_settings();
        $utc_timezone = new DateTimeZone( 'UTC' );
        
        $forStr = '';
        if( $option_none_str ){
            $forStr .= '<option value="" selected="selected">'.$option_none_str.'</option>';
        }
        if( $pdf_items_results && is_array( $pdf_items_results ) && count( $pdf_items_results ) > 0 ){
            foreach($pdf_items_results as $pdf_item_obj ){
                if( $pdf_item_obj->file_name == "" &&  $pdf_item_obj->by_media_uploader < 1 ){
                    continue;
                }
                $file_url = '';
                $file_extension = '';
                if( file_exists(BSKPDFManager::$_upload_root_path.$pdf_item_obj->file_name) ){
                    $file_url = site_url().'/'.$pdf_item_obj->file_name;
                }
                if( $file_url == "" ){
                    continue;
                }

                $file_extension_array = explode('.', $file_url );
                if( is_array( $file_extension_array ) && count($file_extension_array) > 1 ){
                    $file_extension = strtolower( $file_extension_array[count($file_extension_array) - 1] );
                }
                
                //if pdfjs enabled
                if ( $embedded_viewer_settings['enable'] && $file_extension == 'pdf' ) {
                    $file_url = BSK_PDFM_PLUGIN_URL . 'pdfjs/web/viewer.html?file=' . $file_url . $embedded_viewer_settings['paras'];
                }

                if( $default_enable_permalink ){
                    $file_url = site_url().'/bsk-pdf-manager/'.$pdf_item_obj->slug.'/';
                }
                
                $option_text = $pdf_item_obj->title;
                if( $show_date_in_title ){
                    if( $date_before_title ){
                        $option_text = wp_date( 'Y-m-d-D', strtotime($pdf_item_obj->last_date), $utc_timezone ).$option_text;
                    }else{
                        $option_text .= wp_date( 'Y-m-d-D', strtotime($pdf_item_obj->last_date), $utc_timezone );
                    }
                }

                $id_str = ' id="'.wp_date( 'Y-m-d-D', strtotime($pdf_item_obj->last_date), $utc_timezone ).'-'.$pdf_item_obj->id.'"';
                $forStr .= '<option value="'.esc_attr($file_url).'"'.esc_attr($id_str).'>'.esc_attr($option_text).'</option>'."\n";
            }
        }
        
        return $forStr;
    }
    
    public static function show_pdfs_in_dropdown_only_container( $pdf_items_results, 
                                                    $class, 
                                                    $option_none_str, 
                                                    $target, 
                                                    $show_date_in_title, 
                                                    $date_format_str,
                                                    $date_before_title
                                                  ){

        $forStr = '';
        $forStr .= '<select class="'.esc_attr($class).'"'.$target.' style="display: none;">'."\n";
        if( $option_none_str ){
            $forStr .= '<option value="" selected="selected">'.esc_attr($option_none_str).'</option>';
        }
        $forStr .= '</select>';
        
        return $forStr;
    }
    
    public static function show_pdfs_dropdown_option_for_category(   
                                                                    $pdf_results_of_the_category, 
                                                                    $category_obj,
                                                                    $show_date_in_title, 
                                                                    $date_format_str,
                                                                    $date_before_title,
                                                                    $depth,
                                                                    $option_group,
                                                                    $default_enable_permalink
                                                                 ){
        $prefix = '';
        if( $depth == 2 ){
            $prefix = apply_filters( 'bsk_pdfm_filter_dropdown_option_prefix', '&#8212;&nbsp;', 2 );
        }else if( $depth == 3 ){
            $prefix = apply_filters( 'bsk_pdfm_filter_dropdown_option_prefix', '&#8212;&nbsp;&#8212;&nbsp;', 3 );
        }
        
        
        $forStr = '';
        if( $option_group && $option_group == 'CAT_TITLE' ){
            $forStr .= '<optgroup label="'.esc_attr($prefix.$category_obj->title).'">';
        }
        if( !isset( $pdf_results_of_the_category ) || 
            !is_array( $pdf_results_of_the_category ) || 
            count( $pdf_results_of_the_category ) < 1 ){

            if( $option_group && $option_group == 'CAT_TITLE' ){
                $forStr .= '</optgroup>';
            }
            
            return $forStr;
        }
        
        //read global embeded viewer settings
        $embedded_viewer_settings = self::get_embedded_viewer_settings();
        $utc_timezone = new DateTimeZone( 'UTC' );
        
        foreach( $pdf_results_of_the_category as $pdf_item_obj ){
            if( $pdf_item_obj->file_name == "" &&  $pdf_item_obj->by_media_uploader < 1 ){
                continue;
            }
            $file_url = '';
            $file_extension = '';
            if( file_exists(BSKPDFManager::$_upload_root_path.$pdf_item_obj->file_name) ){
                $file_url = site_url().'/'.$pdf_item_obj->file_name;
            }
            if( $file_url == "" ){
                continue;
            }

            $file_extension_array = explode('.', $file_url );
            if( is_array( $file_extension_array ) && count($file_extension_array) > 1 ){
                $file_extension = strtolower( $file_extension_array[count($file_extension_array) - 1] );
            }

            //if pdfjs enabled
            if ( $embedded_viewer_settings['enable'] && $file_extension == 'pdf' ) {
                $file_url = BSK_PDFM_PLUGIN_URL . 'pdfjs/web/viewer.html?file=' . $file_url . $embedded_viewer_settings['paras'];
            }

            if( $default_enable_permalink ){
                $file_url = site_url().'/bsk-pdf-manager/'.$pdf_item_obj->slug.'/';
            }

            $option_text = $pdf_item_obj->title;
            if( $show_date_in_title ){
                if( $date_before_title ){
                    $option_text = wp_date($date_format_str, strtotime($pdf_item_obj->last_date), $utc_timezone).$option_text;
                }else{
                    $option_text .= wp_date($date_format_str, strtotime($pdf_item_obj->last_date), $utc_timezone);
                }
            }
                            
            $id_str = ' id="'.wp_date($date_format_str, strtotime($pdf_item_obj->last_date), $utc_timezone).'-'.$pdf_item_obj->id.'"';
            $forStr .= '<option value="'.esc_attr($file_url).'"'.esc_attr($id_str).'>'.esc_attr($prefix.$option_text).'</option>'."\n";
        }
        if( $option_group && $option_group == 'CAT_TITLE' ){
            $forStr .= '</optgroup>';
        }
        
        return $forStr;
    }
    
    public static function get_embedded_viewer_settings( $parameters_viewer_settings = array() ) {

        $return_array = array( 'enable' => false, 'paras' => '' );

        $embedded_viewer_settings = array( 'enable' => false );
        $plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, false );
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
            if ( isset( $plugin_settings['embedded_viewer_settings'] ) && is_array( $plugin_settings['embedded_viewer_settings'] ) && count( $plugin_settings['embedded_viewer_settings'] ) > 0 ) {
				$embedded_viewer_settings = $plugin_settings['embedded_viewer_settings'];
			}
		}
        if ( isset( $embedded_viewer_settings['enable'] ) && $embedded_viewer_settings['enable'] ) {
            $return_array['enable'] = true;
        }

        //ovewertie global setitngs by 
        if ( isset( $parameters_viewer_settings['disable_right_click'] ) ) {
            $embedded_viewer_settings['disable_right_click'] = $parameters_viewer_settings['disable_right_click'];
        }
        if ( isset( $parameters_viewer_settings['show_toolbar'] ) ) {
            $embedded_viewer_settings['show_toolbar'] = $parameters_viewer_settings['show_toolbar'];
        }
        if ( isset( $parameters_viewer_settings['text_button'] ) ) {
            $embedded_viewer_settings['text_button'] = $parameters_viewer_settings['text_button'];
        }
        if ( isset( $parameters_viewer_settings['draw_button'] ) ) {
            $embedded_viewer_settings['draw_button'] = $parameters_viewer_settings['draw_button'];
        }
        if ( isset( $parameters_viewer_settings['stamp_button'] ) ) {
            $embedded_viewer_settings['stamp_button'] = $parameters_viewer_settings['stamp_button'];
        }
        if ( isset( $parameters_viewer_settings['download_button'] ) ) {
            $embedded_viewer_settings['download_button'] = $parameters_viewer_settings['download_button'];
        }
        if ( isset( $parameters_viewer_settings['print_button'] ) ) {
            $embedded_viewer_settings['print_button'] = $parameters_viewer_settings['print_button'];
        }
        if ( isset( $parameters_viewer_settings['open_file_button'] ) ) {
            $embedded_viewer_settings['open_file_button'] = $parameters_viewer_settings['open_file_button'];
        }
        if ( isset( $parameters_viewer_settings['text_selection_tool'] ) ) {
            $embedded_viewer_settings['text_selection_tool'] = $parameters_viewer_settings['text_selection_tool'];
        }
        if ( isset( $parameters_viewer_settings['document_properties_menu'] ) ) {
            $embedded_viewer_settings['document_properties_menu'] = $parameters_viewer_settings['document_properties_menu'];
        }

        $paras_string_array = array();
        if ( isset( $embedded_viewer_settings['disable_right_click'] ) && $embedded_viewer_settings['disable_right_click'] == true ) {
            $paras_string_array[] = 'mright=1';
        }
        if ( isset( $embedded_viewer_settings['show_toolbar'] ) && $embedded_viewer_settings['show_toolbar'] == false ) {
            $paras_string_array[] = 'toolbar=1';
        } else {
            if ( isset( $embedded_viewer_settings['text_button'] ) && $embedded_viewer_settings['text_button'] == false ) {
                $paras_string_array[] = 'freetext=1';
            }
            if ( isset( $embedded_viewer_settings['draw_button'] ) && $embedded_viewer_settings['draw_button'] == false ) {
                $paras_string_array[] = 'ink=1';
            }
            if ( isset( $embedded_viewer_settings['stamp_button'] ) && $embedded_viewer_settings['stamp_button'] == false ) {
                $paras_string_array[] = 'stamp=1';
            }
            if ( isset( $embedded_viewer_settings['download_button'] ) && $embedded_viewer_settings['download_button'] == false ) {
                $paras_string_array[] = 'download=1';
            }
            if ( isset( $embedded_viewer_settings['print_button'] ) && $embedded_viewer_settings['print_button'] == false ) {
                $paras_string_array[] = 'print=1';
            }
            if ( isset( $embedded_viewer_settings['open_file_button'] ) && $embedded_viewer_settings['open_file_button'] == false ) {
                $paras_string_array[] = 'open=1';
            }
            if ( isset( $embedded_viewer_settings['text_selection_tool'] ) && $embedded_viewer_settings['text_selection_tool'] == false ) {
                $paras_string_array[] = 'textsel=1';
            }
            if ( isset( $embedded_viewer_settings['document_properties_menu'] ) && $embedded_viewer_settings['document_properties_menu'] == false ) {
                $paras_string_array[] = 'docprop=1';
            }
        }
        
        if ( count( $paras_string_array ) < 1 ) {
            return $return_array;
        }

        $return_array['paras'] = '&' . implode( '&', $paras_string_array );
        return $return_array;
    }

    public static function display_pdfs_in_ul_or_ol(
                                                     $ul_or_ol,
                                                     $only_li,
                                                     $ul_or_ol_class,
                                                     $pdf_items_results, 
                                                     $target, $nofollow_tag, 
                                                     $show_date_in_title, $date_format_str, $date_before_title,
                                                     $pdf_title_tag,
                                                     $default_enable_permalink
                                                  ){
        
        if( !$pdf_items_results || !is_array($pdf_items_results) || count($pdf_items_results) < 1 ){
            return '';
        }
        
        $upload_root = get_home_path();
        
        $ul_or_ol_class .= ' bsk-pdfm-without-featured-image';
        $ul_or_ol_class .= ' bsk-pdfm-without-description';
        $ul_or_ol_class .= ' bsk-pdfm-with-title';
        
        //read global embeded viewer settings
        $embedded_viewer_settings = self::get_embedded_viewer_settings();

        $i_list_item = 1;
        $forStr = $only_li ? '' : '<'.esc_attr($ul_or_ol).' class="'.esc_attr($ul_or_ol_class).'">'."\n";
        foreach($pdf_items_results as $pdf_item_obj ){
            if( $pdf_item_obj->file_name == "" &&  $pdf_item_obj->by_media_uploader < 1 ){
                continue;
            }
            $file_url = '';
            $file_extension = '';
            if( file_exists(BSKPDFManager::$_upload_root_path.$pdf_item_obj->file_name) ){
                $file_url = site_url().'/'.$pdf_item_obj->file_name;
            }
            if( $file_url == "" ){
                continue;
            }

            $file_extension_array = explode('.', $file_url );
            if( is_array( $file_extension_array ) && count($file_extension_array) > 1 ){
                $file_extension = strtolower( $file_extension_array[count($file_extension_array) - 1] );
            }

            //if pdfjs enabled
            if ( $embedded_viewer_settings['enable'] && $file_extension == 'pdf' ) {
                $file_url = BSK_PDFM_PLUGIN_URL . 'pdfjs/web/viewer.html?file=' . $file_url . $embedded_viewer_settings['paras'];
            }

            if( $default_enable_permalink ){
                $file_url = site_url().'/bsk-pdf-manager/'.$pdf_item_obj->slug.'/';
            }

            $list_item_class = $i_list_item % 2 ? ' list-item-odd' : ' list-item-even';
            $forStr .= self::show_pdf_item_single_li(     
                                                                         $ul_or_ol,
                                                                         $pdf_item_obj, 
                                                                         $target, $nofollow_tag, 
                                                                         $show_date_in_title, $date_format_str, $date_before_title,
                                                                         $list_item_class,
                                                                         $pdf_title_tag,
                                                                         $default_enable_permalink
                                                                   );
            $i_list_item++;
        }
        $forStr .= $only_li ? '' : '</'.esc_attr($ul_or_ol).'>';
        
        return $forStr;
    }
    
    public static function display_pdfs_in_ul_or_ol_only_container(
                                             $ul_or_ol,
                                             $only_li,
                                             $ul_or_ol_class,
                                             $pdf_items_results, 
                                             $target, $nofollow_tag, 
                                             $show_date_in_title, $date_format_str, $date_before_title,
                                             $pdf_title_tag
                                          ){
        
        $ul_or_ol_class .= ' bsk-pdfm-without-featured-image';
        $ul_or_ol_class .= ' bsk-pdfm-without-description';
        $ul_or_ol_class .= ' bsk-pdfm-with-title';
        
        
        $forStr = '<'.esc_attr($ul_or_ol).' class="'.esc_attr($ul_or_ol_class).'">'."\n";
        $forStr .= '</'.esc_attr($ul_or_ol).'>';
        
        return $forStr;
    }
    
    public static function show_pdf_item_single_li( 
                                                     $ul_or_ol,
                                                     $pdf_item_obj, 
												     $open_target_str, $nofollow_tag_str, 
                                                     $show_date_in_title, $date_format_str, $date_before_title,
                                                     $list_item_class,
                                                     $pdf_title_tag,
                                                     $default_enable_permalink
                                                   ){
        //read global embeded viewer settings
        $embedded_viewer_settings = self::get_embedded_viewer_settings();
        $utc_timezone = new DateTimeZone( 'UTC' );
		$date_filter = ' data-date="'.wp_date( 'Y-m-d-D', strtotime($pdf_item_obj->last_date), $utc_timezone ).'"';
		
		$file_url = site_url().'/'.$pdf_item_obj->file_name;
        $file_extension_array = explode('.', $file_url );
        if( is_array( $file_extension_array ) && count($file_extension_array) > 1 ){
            $file_extension = strtolower( $file_extension_array[count($file_extension_array) - 1] );
        }

        //if pdfjs enabled
        if ( $embedded_viewer_settings['enable'] && $file_extension == 'pdf' ) {
            $file_url = BSK_PDFM_PLUGIN_URL . 'pdfjs/web/viewer.html?file=' . $file_url . $embedded_viewer_settings['paras'];
        }
        
        if( $default_enable_permalink ){
            $file_url = site_url().'/bsk-pdf-manager/'.$pdf_item_obj->slug.'/';
        }
		
        //PDF titile str
		$pdf_item_obj_title = esc_html($pdf_item_obj->title);
        $pdf_title_str = '<span class="bsk-pdfm-pdf-title-string">'.$pdf_item_obj_title.'</span>';
		if( $pdf_title_str == "" ){
			$pdf_item_obj_title_array = explode( '/', $file_url );
			$pdf_title_str = esc_html($pdf_item_obj_title_array[count($pdf_item_obj_title_array) - 1]);
		}
        if( $show_date_in_title ){
            $date_str = '<span class="bsk-pdfm-pdf-date">'.wp_date($date_format_str, strtotime($pdf_item_obj->last_date), $utc_timezone).'</span>';
            $pdf_title_str = $date_before_title ? $date_str.$pdf_title_str : $pdf_title_str.$date_str;
        }
        
        //pdf title link
        $pdf_title_str = '<a href="'.esc_url($file_url).'"'.esc_attr($open_target_str.$nofollow_tag_str).'  title="'.esc_attr($pdf_item_obj_title).'" class="bsk-pdfm-pdf-link-for-title pdf-id-'.esc_attr($pdf_item_obj->id).'">'.$pdf_title_str.'</a>';

        /*
          * organise return str
          */
        $forStr  = '<li class="bsk-pdfm-list-item'.esc_attr($list_item_class).'"'.$date_filter.' data-id="'.esc_attr($pdf_item_obj->id).'">'."\n";
        $forStr .= $pdf_title_str;
        $forStr .= '</li>'."\n";

        return $forStr;
	}
    
    public static function show_pdfs_link_only(
                                                 $pdf_items_results, 
                                                 $target, $nofollow_tag, 
                                                 $show_date_in_title, $date_format_str, $date_before_title,
                                                 $default_enable_permalink
                                              ){
        
        if( !$pdf_items_results || !is_array($pdf_items_results) || count($pdf_items_results) < 1 ){
            return '';
        }
        
        $utc_timezone = new DateTimeZone( 'UTC' );
        
        $forStr = '';
        foreach($pdf_items_results as $pdf_item_obj ){
            if( $pdf_item_obj->file_name == "" &&  $pdf_item_obj->by_media_uploader < 1 ){
                continue;
            }
            $file_url = '';
            if( file_exists(BSKPDFManager::$_upload_root_path.$pdf_item_obj->file_name) ){
                if( $default_enable_permalink ){
                    $file_url = site_url().'/bsk-pdf-manager/'.$pdf_item_obj->slug.'/';
                }else{
                    $file_url = site_url().'/'.$pdf_item_obj->file_name;
                }
            }
            if( $file_url == "" ){
                continue;
            }

            $pdf_item_obj_title = $pdf_item_obj->title;
            if( $pdf_item_obj_title == "" ){
                $pdf_item_obj_title_array = explode( '/', $file_url );
                $pdf_item_obj_title = $pdf_item_obj_title_array[count($pdf_item_obj_title_array) - 1];
            }


            $link_text = esc_html($pdf_item_obj_title);
            if( $show_date_in_title ){
                $date_str_to_display = wp_date($date_format_str, strtotime($pdf_item_obj->last_date), $utc_timezone);
                if( $date_before_title ){
                    $link_text = '<span class="bsk-pdfm-pdf-date">'.$date_str_to_display.'</span>'.$pdf_item_obj_title;
                }else{
                    $link_text .= '<span class="bsk-pdfm-pdf-date">'.$date_str_to_display.'</span>';
                }
            }

            $forStr .= '<a href="'.esc_url($file_url).'"'.esc_attr($target.$nofollow_tag).'  title="'.esc_attr($pdf_item_obj_title).'" class="bsk-pdfm-pdf-link pdf-id-'.esc_attr($pdf_item_obj->id).'">'.$link_text.'</a>'."\n";
        }
        
        return $forStr;
    }
    
    public static function show_pdfs_url_only(
                                                 $pdf_items_results,
                                                 $default_enable_permalink
                                              ){
        
        if( !$pdf_items_results || !is_array($pdf_items_results) || count($pdf_items_results) < 1 ){
            return '';
        }
        
        $forStr = '';
        foreach($pdf_items_results as $pdf_item_obj ){
            if( $pdf_item_obj->file_name == "" &&  $pdf_item_obj->by_media_uploader < 1 ){
                continue;
            }
            $file_url = '';
            if( file_exists(BSKPDFManager::$_upload_root_path.$pdf_item_obj->file_name) ){
                if( $default_enable_permalink ){
                    $file_url = site_url().'/bsk-pdf-manager/'.$pdf_item_obj->slug.'/';
                }else{
                    $file_url = site_url().'/'.$pdf_item_obj->file_name;
                }
            }
            if( $file_url == "" ){
                continue;
            }

            $forStr .= $file_url;
        }
        
        return $forStr;
    }
    
    public static function get_category_dropdown( 
                                                    $categories_loop_array, 
                                                    $availabe_ids_array, 
                                                    $selected_cat_id,
                                                    $show_cat_hierarchical,
                                                    $cat_order_by_str,
                                                    $cat_order_str,
                                                    $option_null_str,
                                                    $hide_empty_cat
                                                 ) {
        global $wpdb;
        
        $selector_str = '<select class="bsk-pdfm-category-dropdown">';
        $selector_str .= trim($option_null_str) == "" ? '' : '<option value="">'.esc_attr(trim($option_null_str)).'</option>';
        foreach( $categories_loop_array as $cat_obj ){
            if( $hide_empty_cat ){
                $sql = 'SELECT COUNT(*) FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_rels_tbl_name).'` AS R LEFT JOIN  '.
                         '`'.esc_sql($wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name).'` AS P ON R.`pdf_id` = P.`id` '.
                         'WHERE R.`cat_id` = %d';
                $sql = $wpdb->prepare($sql, $cat_obj->id);
                if( $wpdb->get_var( $sql ) < 1 ){
                    continue;
                }
            }
            $selected_str = $selected_cat_id == $cat_obj->id ? ' selected' : '';
            $selector_str .= '<option value="'.esc_attr($cat_obj->id).'"'.$selected_str.'>'.esc_attr($cat_obj->title).'</option>';
            
            if( $show_cat_hierarchical ){
                //show child categories
                $sql_base = 'SELECT * FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name).'` AS C '.
                                 'WHERE 1 AND C.`id` IN('.implode(',', esc_sql($availabe_ids_array)).') ';
                //child categories
                $sql = $sql_base.' AND C.`parent` = '.$cat_obj->id.' ORDER BY '.$cat_order_by_str.$cat_order_str;
                $child_results = $wpdb->get_results( $sql );
                if( $child_results && is_array( $child_results ) && count( $child_results ) > 0 ){
                    foreach( $child_results as $child_cat_obj ){
                        if( $hide_empty_cat ){
                            $sql = 'SELECT COUNT(*) FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_rels_tbl_name).'` AS R LEFT JOIN  '.
                                     '`'.esc_sql($wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name).'` AS P ON R.`pdf_id` = P.`id` '.
                                     'WHERE R.`cat_id` = %d';
                            $sql = $wpdb->prepare($slq, $child_cat_obj->id);
                            if( $wpdb->get_var( $sql ) < 1 ){
                                continue;
                            }
                        }
                        $prefix = apply_filters( 'bsk_pdfm_filter_selector_option_prefix', '&#8212;&nbsp;', 2 );
                        $selected_str = $selected_cat_id == $child_cat_obj->id ? ' selected' : '';
                        $selector_str .= '<option value="'.esc_attr($child_cat_obj->id).'"'.$selected_str.'>'.esc_attr($prefix.$child_cat_obj->title).'</option>';
                        //grand categories
                        $sql = $sql_base.' AND C.`parent` = '.$child_cat_obj->id.' ORDER BY '.$cat_order_by_str.$cat_order_str;
                        $grand_results = $wpdb->get_results( $sql );
                        if( $grand_results && is_array( $grand_results ) && count( $grand_results ) > 0 ){
                            foreach( $grand_results as $grand_cat_obj ){
                                if( $hide_empty_cat ){
                                    $sql = 'SELECT COUNT(*) FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_rels_tbl_name).'` AS R LEFT JOIN  '.
                                             '`'.esc_sql($wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name).'` AS P ON R.`pdf_id` = P.`id` '.
                                             'WHERE R.`cat_id` = %d';
                                    $sql = $wpdb->prepare($sql, $grand_cat_obj->id);
                                    if( $wpdb->get_var( $sql ) < 1 ){
                                        continue;
                                    }
                                }
                                $prefix = apply_filters( 'bsk_pdfm_filter_selector_option_prefix', '&#8212;&nbsp;&#8212;&nbsp;', 3 );
                                $selected_str = $selected_cat_id == $grand_cat_obj->id ? ' selected' : '';
                                $selector_str .= '<option value="'.esc_attr($grand_cat_obj->id).'"'.$selected_str.'>'.esc_attr($prefix.$grand_cat_obj->title).'</option>';
                            }
                        }//end for grand
                    }
                }//end for child
            } //end for hierarchical
        }//end foreach
        $selector_str .= '</select>';
        
        return $selector_str;
    } //end of function
    
    public static function get_shortcode_attributes_and_ajax_nonce( $shortcode_attrs ){
        //output all shortcode parameters
        $str_body = '<div class="bsk-pdfm-pdfs-shortcode-attr">';
        foreach( $shortcode_attrs as $attr_name => $attr_val ){
            if( $attr_name == 'pagination_previous_text' ||
                $attr_name == 'pagination_next_text' ){
                
                $str_body .= '<input type="hidden" class="bsk-pdfm-shortcode-attr" data-attr_name="'.esc_attr($attr_name).'" value="'.esc_attr($attr_val).'" />';
            }else{
                $str_body .= '<input type="hidden" class="bsk-pdfm-shortcode-attr" data-attr_name="'.esc_attr($attr_name).'" value="'.esc_attr($attr_val).'" />';
            }
        }

        $ajax_nonce = wp_create_nonce( "pdfs-ajax-get" );
        $str_body .= '<input type="hidden" class="bsk-pdfm-pdfs-ajax-nonce" value="'.$ajax_nonce.'">';
        $str_body .= '<!-- //bsk-pdfm-pdfs-shortcode-attr -->';
        $str_body .= '</div>';
        
        return $str_body;
    }
    
}//end of class
