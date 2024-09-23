<?php

class BSKPDFM_Shortcodes_Category_UL_OL extends BSKPDFM_Shortcodes_Category {

	public function __construct() {
        add_shortcode( 'bsk-pdfm-category-ul', array($this, 'bsk_pdf_manager_list_pdfs_by_cat_as_ul') );
        add_shortcode( 'bsk-pdfm-category-ol', array($this, 'bsk_pdf_manager_list_pdfs_by_cat_as_ol') );
        
        //ajax action for pagination & search
        add_action( 'wp_ajax_pdfs_get_category_ul', array( $this, 'bsk_pdfm_ajax_get_category_ul_ol' ) );
		add_action( 'wp_ajax_nopriv_pdfs_get_category_ul', array( $this, 'bsk_pdfm_ajax_get_category_ul_ol' ) );
        add_action( 'wp_ajax_pdfs_get_category_ol', array( $this, 'bsk_pdfm_ajax_get_category_ul_ol' ) );
		add_action( 'wp_ajax_nopriv_pdfs_get_category_ol', array( $this, 'bsk_pdfm_ajax_get_category_ul_ol' ) );
	}
    
    function bsk_pdf_manager_list_pdfs_by_cat_as_ul( $atts, $content ){
        if( !is_array( $atts ) ){
            $atts = array();
        }
        $atts['ul_or_ol'] = 'ul';
        
        return $this->bsk_pdf_manager_list_pdfs_by_cat_as_ul_ol( $atts, $content);
    }
    
    function bsk_pdf_manager_list_pdfs_by_cat_as_ol( $atts, $content ){
        if( !is_array( $atts ) ){
            $atts = array();
        }
        $atts['ul_or_ol'] = 'ol';
        
        return $this->bsk_pdf_manager_list_pdfs_by_cat_as_ul_ol( $atts, $content );
    }
    
    function bsk_pdf_manager_list_pdfs_by_cat_as_ul_ol( $atts, $content ){
		
        $all_shortcode_atts = array( 'ul_or_ol' => 'ul' );
        $all_shortcode_atts = array_merge( 
                                       $all_shortcode_atts,
                                       $this->_shortcode_category_atts,
                                       $this->_shortcode_pdfs_atts,
                                       $this->_shortcode_count_desc_atts,
                                       $this->_shortcode_extension_filter_atts,
                                       $this->_shortcode_tags_filter_atts,
                                       $this->_shortcode_output_container_atts
                                     );
		$shortcode_atts = shortcode_atts( $all_shortcode_atts, $atts );
        $shortcode_atts_processed = $this->process_shortcode_parameters( $shortcode_atts );
        $shortcode_atts['cat_id'] = $shortcode_atts['id'];

        //get all categories id
        $categories_id_array = BSKPDFM_Common_Data_Source::bsk_pdfm_organise_categories_id( $shortcode_atts );
        if( $categories_id_array == false || !is_array( $categories_id_array ) || count( $categories_id_array ) < 1 ){
            $str = '<div class="bsk-pdfm-output-container'.' '.esc_attr($shortcode_atts['output_container_class']).'">'.
                            '<p>'.esc_html__( 'No valid category id found', 'bskpdfmanager' ).'</p>'.
                     '</div>';
            return $str;
        }
        
        $default_enable_permalink = false;
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
        if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
            if( isset($plugin_settings['enable_permalink']) ){
				$default_enable_permalink = $plugin_settings['enable_permalink'];
			}
		}
        
        $permalink_structure = get_option( 'permalink_structure' );
        if ( ! $permalink_structure ) {
            $default_enable_permalink = false;
        }
        
        $shortcode_atts['valid_cat_ids'] = $categories_id_array['ids_array'];
        
        $ul_or_ol = strtoupper($shortcode_atts['ul_or_ol']) == 'OL' ? 'ol' : 'ul';
        //most top
		$most_top = intval( $shortcode_atts['most_top'] );
        
        //
        //process all filters
        //process search bar
        //
        $extension_filter_return = BSKPDFM_Common_Filter_Extension::show_extension_filter_bar( $shortcode_atts );
        $tags_filter_return = BSKPDFM_Common_Filter_Tags::show_tags_filter_bar( $shortcode_atts, 'category', $categories_id_array['ids_array'] );
        
        $query_args = array();
        $query_args['cat_order_by'] = $shortcode_atts['cat_order_by'];
        $query_args['cat_order'] = $shortcode_atts['cat_order'];
        $query_args['order_by'] = $shortcode_atts['order_by'];
        $query_args['order'] = $shortcode_atts['order'];
        $query_args['most_top'] = $most_top;
        $query_args['ids_array'] = $categories_id_array['ids_array'];
        if( $extension_filter_return && 
            is_array( $extension_filter_return ) && 
            isset( $extension_filter_return['only_filters'] ) &&
            !$extension_filter_return['only_filters'] && 
            isset( $extension_filter_return['filters'] ) && 
            $extension_filter_return['filters'] ){

            $query_args['extension'] = trim( $shortcode_atts['extension_filter_default'] );
        }
        
        if( $tags_filter_return && 
            is_array( $tags_filter_return ) && 
            isset( $tags_filter_return['default'] ) &&
            $tags_filter_return['default'] > 0 ){

            $query_args['tags'] = $tags_filter_return['default'];
        }
        
        $cat_pdfs_query_results = BSKPDFM_Common_Data_Source::bsk_pdfm_get_pdfs_by_cat( $query_args );
        $total_pdfs = 0;
        if( $cat_pdfs_query_results && is_array( $cat_pdfs_query_results ) ){
            $total_pdfs = $cat_pdfs_query_results['total'];
        }
        
        $output_container_class = $shortcode_atts['output_container_class'] ? ' '.$shortcode_atts['output_container_class'] : '';
        $str_body = '<div class="bsk-pdfm-output-container shortcode-category layout-'.esc_attr($ul_or_ol.$output_container_class).'">';

        if( $cat_pdfs_query_results ){
            
            //show extension filter bar
            if( $extension_filter_return ){
                $str_body .= $extension_filter_return['filters'];
            }
            
            //show tags filter bar
            if( $tags_filter_return ){
                $str_body .= $tags_filter_return['filters'];
            }
            
            //show count description bar
            $str_body .= BSKPDFM_Common_Count_Desc_Bar::show_count_desc_bar( 
                                                                      $total_pdfs,
                                                                      $shortcode_atts,
                                                                      false,
                                                                      false
                                                                  );

            $category_out = '';
            $pdf_results_for_date_filter = array();
            foreach( $categories_id_array['categories_loop'] as $category_obj ){

                $pdfs_results = false;
                if( isset($cat_pdfs_query_results['pdfs'] ) && 
                    isset($cat_pdfs_query_results['pdfs'][$category_obj->id]) ){

                    $pdfs_results = $cat_pdfs_query_results['pdfs'][$category_obj->id];
                    $pdf_results_for_date_filter = array_merge( $pdf_results_for_date_filter, $pdfs_results );
                }

                $category_out .= $this->show_pdfs_in_ul_or_ol_by_category( 
                                                                        $ul_or_ol,
                                                                        $category_obj,
                                                                        $pdfs_results,
                                                                        $shortcode_atts,
                                                                        1,
                                                                        $default_enable_permalink
                                                                    );
                
            }

            $str_body .= $category_out;
        }
        
        //output all shortcode parameters and ajax nonce
        $str_body .= $this->get_shortcode_parameters_output( $shortcode_atts );

        $str_body .= '</div><!-- //bsk-pdfm-output-container -->';

		return $str_body;
        
	} //end of function
    
    function show_pdfs_in_ul_or_ol_by_category( 
                                            $ul_or_ol,
                                            $category_obj,
                                            $pdfs_results_array,
                                            $shortcode_atts,
                                            $category_depth,
                                            $default_enable_permalink
                                            ){
        
        if( count($shortcode_atts['valid_cat_ids']) > 1 && 
            ( !$pdfs_results_array || !is_array( $pdfs_results_array ) ) ){
            
            return;
        }
        
        $depth_class = ' category-hierarchical-depth-'.$category_depth;
        $caegory_title_tag = 'h'.($category_depth + 1);
        $pdf_title_tag = 'h'.($category_depth + 2);
        
        $categor_output_str = '<div class="bsk-pdfm-category-output cat-'.esc_attr($category_obj->id.$depth_class).' pdfs-in-'.esc_attr($ul_or_ol).'" data-cat-id="'.esc_attr($category_obj->id).'">';
        
        $show_cat_title = BSKPDFM_Common_Display::process_shortcodes_bool_attrs('show_cat_title', $shortcode_atts);
        if( $show_cat_title ){
            $categor_output_str .= apply_filters( 'bsk_pdfm_filter_cat_title', 
                                                '<'.$caegory_title_tag.' class="bsk-pdfm-cat-titile">'.esc_attr($category_obj->title).'</'.$caegory_title_tag.'>',
                                                $category_obj->id,
                                                $category_obj->title );
        }
        
        $show_cat_description = BSKPDFM_Common_Display::process_shortcodes_bool_attrs('show_cat_description', $shortcode_atts);
        if( $show_cat_description ){
            $categor_output_str .= '<div class="bsk-pdfm-category-description">'.esc_html($category_obj->description).'</div>';
        }
        
        //process open target
		$open_target_str = '';
		if( $shortcode_atts['target'] == '_blank' ){
			$open_target_str = ' target="_blank"';
		}
        
        //anchor nofollow tag, noreferrer tag, noopener tag
        $anchor_rel_html_attributes = array();
		$nofollow_tag = BSKPDFM_Common_Display::process_shortcodes_bool_attrs('nofollow_tag', $shortcode_atts);
        if( $nofollow_tag ){
            $anchor_rel_html_attributes[] = 'nofollow';
        }
        $noopener_tag = BSKPDFM_Common_Display::process_shortcodes_bool_attrs('noopener_tag', $shortcode_atts);
        if( $noopener_tag ){
            $anchor_rel_html_attributes[] = 'noopener';
        }
        $noreferrer_tag = BSKPDFM_Common_Display::process_shortcodes_bool_attrs('noreferrer_tag', $shortcode_atts);
        if( $noreferrer_tag ){
            $anchor_rel_html_attributes[] = 'noreferrer';
        }
        $anchor_rel_html_attributes_str = '';
        if( count($anchor_rel_html_attributes) > 0 ){
            $anchor_rel_html_attributes_str = ' rel="'.implode( ' ', $anchor_rel_html_attributes ).'"';
        }
        
        //show date in title
		$show_date = BSKPDFM_Common_Display::process_shortcodes_bool_attrs('show_date', $shortcode_atts);
		//date postion
		$date_before_title = BSKPDFM_Common_Display::process_shortcodes_bool_attrs('date_before_title', $shortcode_atts);
		
		//date format
		$date_format_str = $date_before_title ? 'd/m/Y ' : ' d/m/Y';
		if( $shortcode_atts['date_format'] && is_string($shortcode_atts['date_format']) && $shortcode_atts['date_format'] != ' d/m/Y' ){
			$date_format_str = $shortcode_atts['date_format'];
		}
        
        if( ( !$pdfs_results_array || !is_array( $pdfs_results_array ) || count( $pdfs_results_array ) < 1 ) ){

            $categor_output_str .= BSKPDFM_Common_Display::display_pdfs_in_ul_or_ol_only_container(
                                             $ul_or_ol,
                                             false,
                                             'bsk-pdfm-pdfs-'.$ul_or_ol.'-list',
                                             $pdfs_results_array, 
                                             $open_target_str, $anchor_rel_html_attributes_str, 
                                             $show_date, $date_format_str, $date_before_title,
                                             'h3'
                                            );
        }else{
            $categor_output_str .= BSKPDFM_Common_Display::display_pdfs_in_ul_or_ol(
                                             $ul_or_ol,
                                             false,
                                             'bsk-pdfm-pdfs-'.$ul_or_ol.'-list',
                                             $pdfs_results_array, 
                                             $open_target_str, $anchor_rel_html_attributes_str, 
                                             $show_date, $date_format_str, $date_before_title,
                                             'h3',
                                             $default_enable_permalink
                                            );
        }
        
        $categor_output_str .= '<!--//bsk-pdfm-category-output cat-'.esc_html($category_obj->id).'-->';
        $categor_output_str .= '</div>';

        return $categor_output_str;
    }

    function bsk_pdfm_ajax_get_category_ul_ol(){
        
        if( !check_ajax_referer( 'category-ajax-get', 'nonce', false ) ){
            
            $error_message = '<p class="bsk-pdfm-error-message">'.__( 'Security check, please refresh page and try again!', 'bskpdfmanager' ).'</p>';
            
            $data_to_return = array( 
                                     'category_out' => '', 
                                     'pagination' => '', 
                                     'results_desc' => esc_html__( 'No records found', 'bskpdfmanager' ),
                                     'error_message' => $error_message
                                    );
            wp_die( json_encode( $data_to_return ) );
        }
        
        
        $shortcode_atts = array();
        $shortcode_atts['ul_or_ol'] = sanitize_text_field($_POST['layout']);
        $shortcode_atts['extension'] = isset( $_POST['extension'] ) ? sanitize_text_field($_POST['extension']) : '';
        $shortcode_atts['tags_default'] = isset( $_POST['tags_default'] ) ? sanitize_text_field($_POST['tags_default']) : -1;

        $shortcode_atts = array_merge( 
                       $shortcode_atts,
                       $this->_shortcode_category_atts,
                       $this->_shortcode_pdfs_atts,
                       $this->_shortcode_count_desc_atts,
                       $this->_shortcode_extension_filter_atts,
                       $this->_shortcode_output_container_atts
                    );
        foreach( $shortcode_atts as $key => $default_val ){
            $shortcode_atts[$key] = isset( $_POST[$key] ) ? sanitize_text_field($_POST[$key]) : $default_val;
        }
        $shortcode_atts = $this->process_shortcode_parameters( $shortcode_atts );

        $shortcode_atts['cat_id'] = $shortcode_atts['id'];
        
        //get all categories id
        $categories_id_array = BSKPDFM_Common_Data_Source::bsk_pdfm_organise_categories_id( $shortcode_atts );
        if( $categories_id_array == false || !is_array( $categories_id_array ) || count( $categories_id_array ) < 1 ){
            $str = '<div class="bsk-pdfm-output-container'.' '.esc_attr($shortcode_atts['output_container_class']).'">'.
                            '<p>'.__( 'No valid category id found', 'bskpdfmanager' ).'</p>'.
                     '</div>';
            return $str;
        }
        
        $default_enable_permalink = false;
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
        if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
            if( isset($plugin_settings['enable_permalink']) ){
				$default_enable_permalink = $plugin_settings['enable_permalink'];
			}
		}
        
        $permalink_structure = get_option( 'permalink_structure' );
        if ( ! $permalink_structure ) {
            $default_enable_permalink = false;
        }
        
        $shortcode_atts['valid_cat_ids'] = $categories_id_array['ids_array'];
        
        $ul_or_ol = strtoupper($shortcode_atts['ul_or_ol']) == 'OL' ? 'ol' : 'ul';
        //most top
		$most_top = intval( $shortcode_atts['most_top'] );
        
        $query_args = array();
        $query_args['cat_order_by'] = $shortcode_atts['cat_order_by'];
        $query_args['cat_order'] = $shortcode_atts['cat_order'];
        $query_args['order_by'] = $shortcode_atts['order_by'];
        $query_args['order'] = $shortcode_atts['order'];
        $query_args['most_top'] = $most_top;
        $query_args['ids_array'] = $categories_id_array['ids_array'];
        $extension = trim( $shortcode_atts['extension'] );
        if( $extension ){
            $query_args['extension'] = $extension;
        }
        if( intval( $shortcode_atts['tags_default'] ) > 0 ){
            $query_args['tags'] = intval( $shortcode_atts['tags_default'] );
        }

        $cat_pdfs_query_results = BSKPDFM_Common_Data_Source::bsk_pdfm_get_pdfs_by_cat( $query_args );
        $total_pdfs = 0;
        if( $cat_pdfs_query_results && is_array( $cat_pdfs_query_results ) ){
            $total_pdfs = $cat_pdfs_query_results['total'];
        }

        
        $str_body = '';
        if( $cat_pdfs_query_results ){

            //show count description bar
            $count_desc = BSKPDFM_Common_Count_Desc_Bar::show_count_desc_bar( 
                                                                      $total_pdfs,
                                                                      $shortcode_atts,
                                                                      false,
                                                                      false
                                                                  );
            
            $pdf_results_for_date_filter = array();
            $category_out = '';
            foreach( $categories_id_array['categories_loop'] as $category_obj ){

                $pdfs_results = false;
                if( isset($cat_pdfs_query_results['pdfs'] ) && 
                    isset($cat_pdfs_query_results['pdfs'][$category_obj->id]) ){

                    $pdfs_results = $cat_pdfs_query_results['pdfs'][$category_obj->id];
                    $pdf_results_for_date_filter = array_merge( $pdf_results_for_date_filter, $pdfs_results );
                }

                $category_out .= $this->show_pdfs_in_ul_or_ol_by_category( 
                                                                        $ul_or_ol,
                                                                        $category_obj,
                                                                        $pdfs_results,
                                                                        $shortcode_atts,
                                                                        1,
                                                                        $default_enable_permalink
                                                                    );
            }
            
            $str_body .= $category_out;
        }
        
        $data_to_return = array( 
                                'category_out' => $str_body, 
                                'pagination' => '',
                                'results_desc' => $count_desc,
                               );
        wp_die( json_encode( $data_to_return ) );

    }
    
}