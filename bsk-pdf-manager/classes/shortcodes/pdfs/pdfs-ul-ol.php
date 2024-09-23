<?php

class BSKPDFM_Shortcodes_PDFs_UL_OL {
    
	public function __construct() {
        add_shortcode('bsk-pdfm-pdfs-ul', array($this, 'bsk_pdf_manager_show_pdfs_in_ul') );
        add_shortcode('bsk-pdfm-pdfs-ol', array($this, 'bsk_pdf_manager_show_pdfs_in_ol') );
        
        //ajax action for pagination & search
        add_action( 'wp_ajax_pdfs_get_pdfs_ul', array( $this, 'bsk_pdfm_ajax_get_pdfs_ul_ol' ) );
		add_action( 'wp_ajax_nopriv_pdfs_get_pdfs_ul', array( $this, 'bsk_pdfm_ajax_get_pdfs_ul_ol' ) );
        add_action( 'wp_ajax_pdfs_get_pdfs_ol', array( $this, 'bsk_pdfm_ajax_get_pdfs_ul_ol' ) );
		add_action( 'wp_ajax_nopriv_pdfs_get_pdfs_ol', array( $this, 'bsk_pdfm_ajax_get_pdfs_ul_ol' ) );
	}
    
	function bsk_pdf_manager_show_pdfs_in_ul( $atts, $content ){
        if( !is_array( $atts ) ){
            $atts = array();
        }
        $atts['ul_or_ol'] = 'ul';
        
        return $this->bsk_pdf_manager_show_pdfs_in_ul_ol( $atts );
    }
    
    function bsk_pdf_manager_show_pdfs_in_ol( $atts, $content ){
        if( !is_array( $atts ) ){
            $atts = array();
        }
        $atts['ul_or_ol'] = 'ol';
        
        return $this->bsk_pdf_manager_show_pdfs_in_ul_ol( $atts );
    }
    
	function bsk_pdf_manager_show_pdfs_in_ul_ol( $atts ){		
		
		$shortcode_atts = shortcode_atts( 
                              array(
                                       'ul_or_ol' => 'ul',
                                       'id' => '',
                                       'link_only' => 'no',
                                       'url_only' => 'no',
                                       'order_by' => '',
                                       'order' => '', 
                                       'target' => '',
                                       'most_top' => 0,
                                       'nofollow_tag' => 'no',
                                       'noopener_tag' => 'no',
                                       'noreferrer_tag' => 'no',
                                       'show_date' => 'no',
                                       'date_format' => ' d/m/Y',
                                       'date_before_title' => 'no',
                                       
                                       'show_count_desc' => 'no',
                                       'extension_filter' => 'no',
                                       
                                       'tags' => 'no',
                                       'tags_specific' => '',
                                       'tags_default' => '',
                                       'tags_exclude' => '',
                                       'tags_align_right' => 'no',
                                  
                                       'output_container_class' => ''
                                     ), 
                               $atts
                            );
        
        $ul_or_ol = strtoupper($shortcode_atts['ul_or_ol']) == 'OL' ? 'ol' : 'ul';
        
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
        
		//organise ids array
		$ids_array = array();
        $show_all_pdfs = false;
		if( trim($shortcode_atts['id']) == "" ){
			return '';
		}
        if( strtoupper(trim($shortcode_atts['id'])) == 'ALL' ){
            $show_all_pdfs = true;  
        }else if( is_string($shortcode_atts['id']) ){
			$ids_array = explode(',', $shortcode_atts['id']);
			foreach($ids_array as $key => $pdf_id){
				$pdf_id = intval(trim($pdf_id));
				if( $pdf_id < 1 ){
					unset($ids_array[$key]);
                    continue;
				}
				$ids_array[$key] = $pdf_id;
			}
		}
        
		if( ( !is_array($ids_array) || count($ids_array) < 1 ) && $show_all_pdfs == false ){
			return '';
		}
        
		//process open target
		$open_target_str = '';
		if( $shortcode_atts['target'] == '_blank' ){
			$open_target_str = ' target="_blank"';
		}
        
		//link only
		$show_link_only = BSKPDFM_Common_Display::process_shortcodes_bool_attrs('link_only', $shortcode_atts);
		//most top
		$most_top = intval( $shortcode_atts['most_top'] );

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

        //show pdf ulr only
        $show_PDF_url_only = BSKPDFM_Common_Display::process_shortcodes_bool_attrs('url_only', $shortcode_atts);
        
        //
        //process all filters
        //
        $extension_filter_return = BSKPDFM_Common_Filter_Extension::show_extension_filter_bar( $shortcode_atts );
        $tags_filter_return = BSKPDFM_Common_Filter_Tags::show_tags_filter_bar( $shortcode_atts, 'pdfs', false );

        $query_args = array();
        $query_args['show_all_pdfs'] = $show_all_pdfs;
        $query_args['ids_array'] = $ids_array;
        $query_args['order_by'] = $shortcode_atts['order_by'];
        $query_args['order'] = $shortcode_atts['order'];
        $query_args['most_top'] = $most_top;
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

        $pdfs_query_return = BSKPDFM_Common_Data_Source::bsk_pdfm_get_pdfs( $query_args );
        $pdfs_results_array = false;
        $total_pdfs = 0;
        if( $pdfs_query_return && is_array( $pdfs_query_return ) ){
            $pdfs_results_array = $pdfs_query_return['pdfs'];
            $total_pdfs = $pdfs_query_return['total'];
        }
        
        if( $show_link_only ){
            if( !$pdfs_query_return || !is_array($pdfs_query_return) ){
                return '';
            }
            $pdfs_results_array = $pdfs_query_return['pdfs'];
            $str_body = BSKPDFM_Common_Display::show_pdfs_link_only(
                                                                 $pdfs_results_array, 
                                                                 $open_target_str, $anchor_rel_html_attributes_str, 
                                                                 $show_date, $date_format_str, $date_before_title,
                                                                 $default_enable_permalink
                                                                );
            return $str_body;
        }
        
        if( $show_PDF_url_only ){
            if( !$pdfs_query_return || !is_array($pdfs_query_return) || count($pdfs_query_return) < 1 ){
                return '';
            }
            $pdfs_results_array = $pdfs_query_return['pdfs'];
            $str_body = BSKPDFM_Common_Display::show_pdfs_url_only( $pdfs_results_array, $default_enable_permalink );
            return $str_body;
        }
        
        $output_container_class = trim($shortcode_atts['output_container_class']) ? ' '.trim($shortcode_atts['output_container_class']) : '';
        $str_body = '<div class="bsk-pdfm-output-container shortcode-pdfs layout-'.esc_attr($ul_or_ol.$output_container_class).'">';
        $str_body .= '<div class="bsk-pdfm-pdfs-output pdfs-in-'.esc_attr($ul_or_ol).'">';
        
        //show extension filter bar
        if( $extension_filter_return ){
            $str_body .= $extension_filter_return['filters'];
        }
        
        //show tags filter bar
        if( $tags_filter_return ){
            $str_body .= $tags_filter_return['filters'];
        }
        
        //show count description bar
        if( !$pdfs_query_return || !is_array( $pdfs_query_return ) || count( $pdfs_query_return ) < 1 ){
            $str_body .= BSKPDFM_Common_Count_Desc_Bar::show_count_desc_bar( false, $shortcode_atts, false, false );
            
            $str_body .= BSKPDFM_Common_Display::display_pdfs_in_ul_or_ol_only_container(
                                             $ul_or_ol,
                                             false,
                                             'bsk-pdfm-pdfs-'.$ul_or_ol.'-list',
                                             $pdfs_results_array,
                                             $open_target_str, $anchor_rel_html_attributes_str, 
                                             $show_date, $date_format_str, $date_before_title,
                                             'h3'
                                            );
            
        }else{
            //show count description bar
            $str_body .= BSKPDFM_Common_Count_Desc_Bar::show_count_desc_bar( 
                                                                      $total_pdfs,
                                                                      $shortcode_atts,
                                                                      false,
                                                                      false
                                                                  );
            
            $str_body .= BSKPDFM_Common_Display::display_pdfs_in_ul_or_ol(
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
        
        $str_body .= '</div><!-- //end for bsk-pdfm-pdfs-output -->';
        
        //output all shortcode parameters and ajax nonce
        $str_body .= BSKPDFM_Common_Display::get_shortcode_attributes_and_ajax_nonce( $shortcode_atts );
        
        
        $str_body .= '</div><!-- //end for bsk-pdfm-output-container-->';

        return $str_body;
	} //end of function
    
    function bsk_pdfm_ajax_get_pdfs_ul_ol(){
        if( !check_ajax_referer( 'pdfs-ajax-get', 'nonce', false ) ){
            $error_message = '<p class="bsk-pdfm-error-message">'.esc_html__( 'Security check, please refresh page and try again', 'bskpdfmanager' ).'!</p>';
            
            $data_to_return = array( 
                                     'pdfs' => '', 
                                     'pdfs_count' => 0, 
                                     'results_desc' => esc_html__( 'No records found', 'bskpdfmanager' ),
                                     'error_message' => $error_message
                                    );
            wp_die( json_encode( $data_to_return ) );
            
        }

        
        $shortcode_atts = array(  
                   'ul_or_ol' => sanitize_text_field($_POST['layout']),
                   'id' => sanitize_text_field($_POST['id']),
                   'order_by' => sanitize_text_field($_POST['order_by']),
                   'order' => sanitize_text_field($_POST['order']),
                   'target' => sanitize_text_field($_POST['target']),
                   'nofollow_tag' => sanitize_text_field($_POST['nofollow_tag']),
                   'noopener_tag' => sanitize_text_field($_POST['noopener_tag']),
                   'noreferrer_tag' => sanitize_text_field($_POST['noreferrer_tag']),
                   'show_date' => sanitize_text_field($_POST['show_date']),
                   'date_format' => sanitize_text_field($_POST['date_format']),
                   'date_before_title' => sanitize_text_field($_POST['date_before_title']),
                   'most_top' => absint(sanitize_text_field($_POST['most_top'])),
                   'show_count_desc' => isset($_POST['show_count_desc']) ? sanitize_text_field($_POST['show_count_desc']) : '',
                   'extension' => isset($_POST['extension']) ? sanitize_text_field($_POST['extension']) : '',
                   'tags_default' => sanitize_text_field($_POST['tags_default']),
                  );
        $ul_or_ol = strtoupper($shortcode_atts['ul_or_ol']) == 'OL' ? 'ol' : 'ul';
        
        //organise ids array
		$ids_array = array();
        $show_all_pdfs = false;
		if( trim($shortcode_atts['id']) == "" ){
            $error_message = '<p class="bsk-pdfm-error-message">'.esc_html__( 'No valid document ID', 'bskpdfmanager' ).'!</p>';
            $data_to_return = array( 
                                     'pdfs' => '', 
                                     'pdfs_count' => 0, 
                                     'results_desc' => esc_html__( 'No records found', 'bskpdfmanager' ),
                                     'error_message' => $error_message
                                    );
            wp_die( json_encode( $data_to_return ) );
		}
        if( strtoupper(trim($shortcode_atts['id'])) == 'ALL' ){
            $show_all_pdfs = true;  
        }else if( is_string($shortcode_atts['id']) ){
			$ids_array = explode(',', $shortcode_atts['id']);
			foreach($ids_array as $key => $pdf_id){
				$pdf_id = intval(trim($pdf_id));
				if( $pdf_id < 1 ){
					unset($ids_array[$key]);
                    continue;
				}
				$ids_array[$key] = $pdf_id;
			}
		}
        
		if( ( !is_array($ids_array) || count($ids_array) < 1 ) && $show_all_pdfs == false ){
            $error_message = '<p class="bsk-pdfm-error-message">No valid document ID!</p>';
            $data_to_return = array( 
                                     'pdfs' => '', 
                                     'pdfs_count' => 0, 
                                     'results_desc' => esc_html__( 'No records found', 'bskpdfmanager' ),
                                     'error_message' => $error_message
                                    );
            wp_die( json_encode( $data_to_return ) );
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
        
        $query_args = array();
        $query_args['show_all_pdfs'] = $show_all_pdfs;
        $query_args['ids_array'] = $ids_array;
        $query_args['order_by'] = $shortcode_atts['order_by'];
        $query_args['order'] = $shortcode_atts['order'];
        $query_args['most_top'] = $shortcode_atts['most_top'];
        $query_args['extension'] = $shortcode_atts['extension'];
        if( intval( $shortcode_atts['tags_default'] ) > 0 ){
            $query_args['tags'] = intval( $shortcode_atts['tags_default'] );
        }
        
        $pdfs_query_return = BSKPDFM_Common_Data_Source::bsk_pdfm_get_pdfs( $query_args );
        if( !$pdfs_query_return || !is_array($pdfs_query_return) || count($pdfs_query_return) < 1 ){
            
            $count_desc = BSKPDFM_Common_Count_Desc_Bar::show_count_desc_bar( 
                                                                      $total_pdfs,
                                                                      $shortcode_atts,
                                                                      false,
                                                                      true
                                                                  );
            $data_to_return = array( 
                                     'pdfs' => '', 
                                     'pdfs_count' => 0, 
                                     'results_desc' => $count_desc,
                                     'error_message' => ''
                                    );
            wp_die( json_encode( $data_to_return ) );
        }
        
        $pdfs_results_array = $pdfs_query_return['pdfs'];
        $total_pdfs = $pdfs_query_return['total'];

        $pdfs_str = BSKPDFM_Common_Display::display_pdfs_in_ul_or_ol(
                                                             $ul_or_ol,
                                                             true,
                                                             'bsk-pdfm-pdfs-'.$ul_or_ol.'-list',
                                                             $pdfs_results_array, 
                                                             $open_target_str, $anchor_rel_html_attributes_str, 
                                                             $show_date, $date_format_str, $date_before_title,
                                                             'h3',
                                                             $default_enable_permalink
                                                        );
        
        $count_desc = BSKPDFM_Common_Count_Desc_Bar::show_count_desc_bar( 
                                                                      $total_pdfs,
                                                                      $shortcode_atts,
                                                                      false,
                                                                      true
                                                                  );
        $data_to_return = array( 
                                'pdfs' => $pdfs_str, 
                                'pdfs_count' => $total_pdfs,
                                'results_desc' => $count_desc,
                                'error_message' => ''
                              );
        wp_die( json_encode( $data_to_return ) );
        
    } //end of function
}//end of class
