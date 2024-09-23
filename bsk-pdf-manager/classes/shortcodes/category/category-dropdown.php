<?php

class BSKPDFM_Shortcodes_Category_Dropdown extends BSKPDFM_Shortcodes_Category {

	public function __construct() {
        add_shortcode( 'bsk-pdfm-category-dropdown', array($this, 'bsk_pdf_manager_list_pdfs_by_cat_as_dropdown') );
        
        //ajax action for verify password
        add_action( 'wp_ajax_pdfs_get_category_dropdown', array( $this, 'bsk_pdfm_ajax_get_category_dropdown' ) );
		add_action( 'wp_ajax_nopriv_pdfs_get_category_dropdown', array( $this, 'bsk_pdfm_ajax_get_category_dropdown' ) );
	}
    
    function bsk_pdf_manager_list_pdfs_by_cat_as_dropdown( $atts, $content ){		
        $all_shortcode_atts = array( 
                                        'option_none' => esc_html__( 'Select to open...', 'bskpdfmanager' ),
                                        'option_group_label' => 'CAT_TITLE', 
                                   );
        $all_shortcode_atts = array_merge( 
                                   $all_shortcode_atts,
                                   $this->_shortcode_category_atts,
                                   $this->_shortcode_pdfs_atts,
                                   $this->_shortcode_count_desc_atts,
                                   $this->_shortcode_extension_filter_atts,
                                   $this->_shortcode_tags_filter_atts,
                                   $this->_shortcode_output_container_atts
                                 );
        $all_shortcode_atts['target'] = '_blank';
		$shortcode_atts = shortcode_atts( $all_shortcode_atts, $atts );
        $shortcode_atts_processed = $this->process_shortcode_parameters( $shortcode_atts );

        $shortcode_atts['cat_id'] = $shortcode_atts['id'];

        //get all categories id
        $categories_id_array = BSKPDFM_Common_Data_Source::bsk_pdfm_organise_categories_id( $shortcode_atts );
        if( $categories_id_array == false || !is_array( $categories_id_array ) || count( $categories_id_array ) < 1 ){
            $str = '<div class="bsk-pdfm-output-container  shortcode-category layout-dropdown ' . esc_attr( $shortcode_atts['output_container_class'] ) . '">'.
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
        $str_body = '<div class="bsk-pdfm-output-container shortcode-category layout-dropdown' . esc_attr( $output_container_class ) . '">';

        $target_str = '';
        if( $cat_pdfs_query_results ){
            
            //show extension filter bar
            if( $extension_filter_return ){
                $str_body .= $extension_filter_return['filters'];
            }
            
            //show tags filter bar
            if( $tags_filter_return ){
                $str_body .= $tags_filter_return['filters'];
            }

            if( $shortcode_atts['option_group_label'] != 'CAT_TITLE' &&
                $shortcode_atts['option_group_label'] != 'HIDE' ){
                /*
                 *
                 * every category has its own category
                 * 
                 */
                //show count description bar
                $str_body .= BSKPDFM_Common_Count_Desc_Bar::show_count_desc_bar( 
                                                                          $total_pdfs,
                                                                          $shortcode_atts,
                                                                          false,
                                                                          false
                                                                      );

                foreach( $categories_id_array['categories_loop'] as $category_obj ){

                    $pdfs_results = false;
                    if( isset($cat_pdfs_query_results['pdfs'] ) && 
                        isset($cat_pdfs_query_results['pdfs'][$category_obj->id]) ){

                        $pdfs_results = $cat_pdfs_query_results['pdfs'][$category_obj->id];
                    }

                    $str_body .= $this->show_pdfs_in_dropdown_by_category( 
                                                                            $category_obj,
                                                                            $pdfs_results,
                                                                            $shortcode_atts,
                                                                            1,
                                                                            $default_enable_permalink
                                                                        );
                }
            }else{
                /*
                 *
                 * All category and PDFs in one dropdown, category title as option group label
                 * 
                 */
                //show count description bar
                $str_body .= BSKPDFM_Common_Count_Desc_Bar::show_count_desc_bar( 
                                                                          $total_pdfs,
                                                                          $shortcode_atts,
                                                                          false,
                                                                          false
                                                                      );
                
                if( trim($shortcode_atts['target']) == '_blank' ){
                    $target_str = ' data-target="_blank"';
                }

                $dropdown_output = '<select class="bsk-pdfm-pdfs-dropdown"'.$target_str.'>';
                $option_none_str = trim($shortcode_atts['option_none']);
                if( $option_none_str ){
                    $dropdown_output .= '<option value="">'.esc_attr($option_none_str).'</option>';
                }

                $cat_ids_for_container = array();
                foreach( $categories_id_array['categories_loop'] as $category_obj ){

                    $pdfs_results = false;
                    if( isset($cat_pdfs_query_results['pdfs'] ) && 
                        isset($cat_pdfs_query_results['pdfs'][$category_obj->id]) ){

                        $pdfs_results = $cat_pdfs_query_results['pdfs'][$category_obj->id];
                    }
                    
                    $category_return = $this->get_password_form_and_dropdown_options_by_category( 
                                                                            $category_obj,
                                                                            $pdfs_results,
                                                                            $shortcode_atts,
                                                                            1,
                                                                            $default_enable_permalink
                                                                        );
                    $dropdown_output .= $category_return['options'];
                    if( $category_return['options'] ){
                        $cat_ids_for_container[] = $category_obj->id;
                    }
                    
                }
                $dropdown_output .= '</select>';

                $str_body .= '<div class="bsk-pdfm-category-output cat-'.esc_attr(implode('-', $cat_ids_for_container)).' category-hierarchical-depth-1 pdfs-in-dropdown" data-cat-id="'.esc_attr(implode('-', $cat_ids_for_container)).'">';
                $str_body .= $dropdown_output;
                $str_body .= '</div>';
            }
        }
        
        //output all shortcode parameters and ajax nonce
        $str_body .= $this->get_shortcode_parameters_output( $shortcode_atts );

        $str_body .= '</div><!-- //bsk-pdfm-output-container -->';

        global $post;
        
        if ( $post->ID && $target_str ) {
            $_dropdown_shortcodes_pages = get_option( BSKPDFManager::$_dropdown_shortcodes_pages_option, array() );
            $_dropdown_shortcodes_pages[$post->ID] = $post->ID;

            update_option( BSKPDFManager::$_dropdown_shortcodes_pages_option, $_dropdown_shortcodes_pages );
        }

		return $str_body;
	}//end of function
    
    function show_pdfs_in_dropdown_by_category( 
                                            $category_obj,
                                            $pdfs_results_array,
                                            $shortcode_atts,
                                            $category_depth,
                                            $default_enable_permalink
                                            ){
        
        $depth_class = ' category-hierarchical-depth-'.$category_depth;
        $caegory_title_tag = 'h'.($category_depth + 1);
        $pdf_title_tag = 'h'.($category_depth + 2);
        
        $categor_output_str = '<div class="bsk-pdfm-category-output cat-'.esc_attr($category_obj->id.$depth_class).' pdfs-in-dropdown" data-cat-id="'.esc_attr($category_obj->id).'">';
        
        $show_cat_title = BSKPDFM_Common_Display::process_shortcodes_bool_attrs('show_cat_title', $shortcode_atts);
        if( $show_cat_title ){
            $categor_output_str .= apply_filters( 'bsk_pdfm_filter_cat_title', 
                                                '<'.$caegory_title_tag.' class="bsk-pdfm-cat-titile">'.esc_attr($category_obj->title).'</'.$caegory_title_tag.'>',
                                                $category_obj->id,
                                                $category_obj->title );
        }
        
        //process open target
		$target_str = '';
        if( trim($shortcode_atts['target']) == '_blank' ){
            $target_str = ' data-target="_blank"';
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
        
        $categor_output_str .= '<select class="bsk-pdfm-pdfs-dropdown"'.$target_str.'>';
        $option_none_str = trim($shortcode_atts['option_none']);
        if( $option_none_str ){
            $categor_output_str .= '<option value="">'.esc_attr($option_none_str).'</option>';
        }
        $categor_output_str .= BSKPDFM_Common_Display::show_pdfs_dropdown_option_for_category(
                             $pdfs_results_array,
                             $category_obj,
                             $show_date, 
                             $date_format_str,
                             $date_before_title,
                             $category_depth,
                             false,
                             $default_enable_permalink
                        );
        $categor_output_str .= '</select>';
        
        $categor_output_str .= '<!--//bsk-pdfm-category-output cat-'.esc_attr($category_obj->id).'-->';
        $categor_output_str .= '</div>';

        return $categor_output_str;
    }
    
    /* the function only for option_group_label == 'CAT_TITLE' */
    function get_password_form_and_dropdown_options_by_category( 
                                            $category_obj,
                                            $pdfs_results_array,
                                            $shortcode_atts,
                                            $depth,
                                            $default_enable_permalink
                                            ){
        
        $depth_class = ' category-hierarchical-depth-'.$depth;
        $caegory_title_tag = 'h'.($depth+1);
        $pdf_title_tag = 'h'.($depth+2);
        
        $category_options = '';
        
        //show date in title
		$show_date = BSKPDFM_Common_Display::process_shortcodes_bool_attrs('show_date', $shortcode_atts);
		//date postion
		$date_before_title = BSKPDFM_Common_Display::process_shortcodes_bool_attrs('date_before_title', $shortcode_atts);
		
		//date format
		$date_format_str = $date_before_title ? 'd/m/Y ' : ' d/m/Y';
		if( $shortcode_atts['date_format'] && is_string($shortcode_atts['date_format']) && $shortcode_atts['date_format'] != ' d/m/Y' ){
			$date_format_str = $shortcode_atts['date_format'];
		}
        
        $category_options .= BSKPDFM_Common_Display::show_pdfs_dropdown_option_for_category(
                                     $pdfs_results_array,
                                     $category_obj,
                                     $show_date, 
                                     $date_format_str,
                                     $date_before_title,
                                     $depth,
                                     $shortcode_atts['option_group_label'],
                                     $default_enable_permalink
                                );
        

        return array( 'password_form' => '', 'options' => $category_options );
    }

    function bsk_pdfm_ajax_get_category_dropdown(){
        
        if( !check_ajax_referer( 'category-ajax-get', 'nonce', false ) ){
            
            $error_message = '<p class="bsk-pdfm-error-message">'.__( 'Security check, please refresh page and try again', 'bskpdfmanager' ).'!</p>';
            
            $data_to_return = array( 
                                     'category_out' => '', 
                                     'pagination' => '', 
                                     'results_desc' => esc_html__( 'No records found', 'bskpdfmanager' ),
                                     'error_message' => $error_message
                                    );
            wp_die( json_encode( $data_to_return ) );
        }

        $shortcode_atts = array();
        $shortcode_atts['option_none'] = 'Select to open...';
        if (isset( $_POST['option_none'] )) {
            $shortcode_atts['option_none'] = sanitize_text_field($_POST['option_none']);
        }
        $shortcode_atts['option_group_label'] = 'CAT_TITLE';
        if (isset($_POST['option_group_label'])) {
            $shortcode_atts['option_group_label'] = sanitize_text_field($_POST['option_group_label']);
        }
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
            /*
             *
             * every category has its own category
             * 
             */
            //show count description bar
            $count_desc = BSKPDFM_Common_Count_Desc_Bar::show_count_desc_bar( 
                                                                      $total_pdfs,
                                                                      $shortcode_atts,
                                                                      false,
                                                                      false
                                                                  );
            if( $shortcode_atts['option_group_label'] != 'CAT_TITLE' &&
                $shortcode_atts['option_group_label'] != 'HIDE' ){
                
                foreach( $categories_id_array['categories_loop'] as $category_obj ){

                    $pdfs_results = false;
                    if( isset($cat_pdfs_query_results['pdfs'] ) && 
                        isset($cat_pdfs_query_results['pdfs'][$category_obj->id]) ){

                        $pdfs_results = $cat_pdfs_query_results['pdfs'][$category_obj->id];
                    }

                    $str_body .= $this->show_pdfs_in_dropdown_by_category( 
                                                                            $category_obj,
                                                                            $pdfs_results,
                                                                            $shortcode_atts,
                                                                            1,
                                                                            $default_enable_permalink
                                                                        );
                }
            }else{
                /*
                 *
                 * All category and PDFs in one dropdown, category title as option group label
                 * 
                 */
                $target_str = '';
                if( trim($shortcode_atts['target']) == '_blank' ){
                    $target_str = ' data-target="_blank"';
                }

                $dropdown_output = '<select class="bsk-pdfm-pdfs-dropdown"'.$target_str.'>';
                $option_none_str = trim($shortcode_atts['option_none']);
                if( $option_none_str ){
                    $dropdown_output .= '<option value="">'.esc_attr($option_none_str).'</option>';
                }
                
                $pdf_results_for_date_filter = array();
                $cat_ids_for_container = array();
                foreach( $categories_id_array['categories_loop'] as $category_obj ){

                    $pdfs_results = false;
                    if( isset($cat_pdfs_query_results['pdfs'] ) && 
                        isset($cat_pdfs_query_results['pdfs'][$category_obj->id]) ){

                        $pdfs_results = $cat_pdfs_query_results['pdfs'][$category_obj->id];
                        $pdf_results_for_date_filter = array_merge( $pdf_results_for_date_filter, $pdfs_results );
                    }

                    $category_return = $this->get_password_form_and_dropdown_options_by_category( 
                                                                            $category_obj,
                                                                            $pdfs_results,
                                                                            $shortcode_atts,
                                                                            1,
                                                                            $default_enable_permalink
                                                                        );
                    $dropdown_output .= $category_return['options'];
                    if( $category_return['options'] ){
                        $cat_ids_for_container[] = $category_obj->id;
                    }
                }
                $dropdown_output .= '</select>';
                
                $str_body .= '<div class="bsk-pdfm-category-output cat-'.implode('-', esc_attr($cat_ids_for_container)).' category-hierarchical-depth-1 pdfs-in-dropdown" data-cat-id="'.esc_attr(implode('-', $cat_ids_for_container)).'">';
                $str_body .= $dropdown_output;
                $str_body .= '</div>';
            }
        }
        
        $data_to_return = array( 
                                'category_out' => $str_body, 
                                'pagination' => '',
                                'results_desc' => $count_desc,
                               );
        wp_die( json_encode( $data_to_return ) );
    } //end of function
    
}