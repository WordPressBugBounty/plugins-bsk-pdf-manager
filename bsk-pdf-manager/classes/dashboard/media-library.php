<?php

class BSKPDFM_Dashboard_Media_Library {

	public function __construct() {
	}

	function bsk_pdf_manager_pdfs_add_by_media_library(){
		global $wpdb;
        
        require_once( 'pdf-image-editor.php' );

        //get all categories
		$sql = 'SELECT COUNT(*) FROM '.esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name);
		if( $wpdb->get_var( $sql ) < 1 ){
			$create_category_url = add_query_arg( 'page', BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['base'], admin_url() );
			$create_category_url = add_query_arg( 'view', 'addnew', $create_category_url );

            printf( __( 'Please %s first', 'bskpdfmanager' ), '<a href="'.$create_category_url.'">'.__('create category', 'bskpdfmanager' ).'</a>' );
            
			return;
		}
						
		$maximum_of_list = 50;
		$maximum_gen_thumb = 20;
        $plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
        $supported_extension = false;
        $organise_directory_strucutre_with_year_month = true;
        if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
            if( isset($plugin_settings['directory_with_year_month']) ){
                $organise_directory_strucutre_with_year_month = $plugin_settings['directory_with_year_month'];
			}
            if( isset($plugin_settings['supported_extension']) ){
                $supported_extension = $plugin_settings['supported_extension'];
            }
		}
        
            if( !$supported_extension || !is_array($supported_extension) || !in_array( 'pdf', $supported_extension ) ){
                $supported_extension = array( 'pdf' );
            }
        
		$upload_folder_4_ftp_display = str_replace( BSKPDFManager::$_upload_root_path, '', BSKPDFManager::$_upload_path_4_ftp );
        
        $temp_msg = __( 'To avoid server timeout errors, up to %d documents can be listed here. This means that you can import up to %d documents at a time but you can come here multiple times until all PDFs are uploaded. If you enable "Generate Featured Images", you can upload up to %d PDFs at a time.', 'bskpdfmanager' );
        $temp_msg = sprintf( $temp_msg, $maximum_of_list, $maximum_of_list, $maximum_gen_thumb );
		echo '  <p>'.esc_html( $temp_msg ).'</p>';
        
        $allowed_extension_with_mime_type = BSKPDFM_Common_Backend::get_available_extension_with_mime_type();
        $allowed_mime_type = array();
        foreach( $allowed_extension_with_mime_type as $extension => $mime_type ){
            if( !in_array( $extension, $supported_extension ) ){
                continue;
            }
            $allowed_mime_type = array_merge( $allowed_mime_type, $mime_type );
        }
        ?>
        <p class="hide-if-no-js">
            <a title="Open Media Library" href="javascript:void(0);" id="bsk_pdfm_bulk_add_by_media_library_anchor_ID" class="button-primary"><?php esc_html_e( 'Open Media Library to Select Documents', 'bskpdfmanager' ); ?></a>
        </p>
        <h3><?php esc_html_e( 'Settings', 'bskpdfmanager' ); ?></h3>
        <p style="font-weight: bold;"><?php esc_html_e('Replace all _ in title to space', 'bskpdfmanager' ); ?>:</p>
        <p>
            <span class="bsk-pdf-field">
                <label>
                    <input type="radio" name="bsk_pdfm_bulk_media_replace_underscroe_raido" class="bsk-pdfm-bulk-media-replace-underscroe-raido" value="YES" /> <?php esc_html_e( 'Yes', 'bskpdfmanager' ); ?>
                </label>
                <label style="margin-left:20px;">
                    <input type="radio" name="bsk_pdfm_bulk_media_replace_underscroe_raido" class="bsk-pdfm-bulk-media-replace-underscroe-raido" value="NO" checked="checked" /> <?php esc_html_e( 'No', 'bskpdfmanager' ); ?>
                </label>
            </span>
        </p>
        <p style="font-weight: bold;"><?php esc_html_e('Replace all - in title to space', 'bskpdfmanager' ); ?>:</p>
        <p>
            <span class="bsk-pdf-field">
                <label>
                    <input type="radio" name="bsk_pdfm_bulk_media_replace_hyphen_raido" class="bsk-pdfm-bulk-media-replace-hyphen-raido" value="YES" /> <?php esc_html_e( 'Yes', 'bskpdfmanager' ); ?>
                </label>
                <label style="margin-left:20px;">
                    <input type="radio" name="bsk_pdfm_bulk_media_replace_hyphen_raido" class="bsk-pdfm-bulk-media-replace-hyphen-raido" value="NO" checked="checked" /> <?php esc_html_e( 'No', 'bskpdfmanager' ); ?>
                </label>
            </span>
        </p>
        <p style="font-weight: bold;"><?php esc_html_e( "Set document's date&time with:", 'bskpdfmanager' ); ?></p>
        <p>
            <span class="bsk-pdf-field">
                <label>
                    <input type="radio" name="bsk_pdfm_bulk_add_by_media_library_date_way_raido" class="bsk-pdfm-bulk-add-by-media-library-date-way-raido" value="Last_Modify" checked="checked" /> <?php esc_html_e( "Set Document's last modify date&time", 'bskpdfmanager' ); ?>
                </label>
                <label style="margin-left:20px;">
                    <input type="radio" name="bsk_pdfm_bulk_add_by_media_library_date_way_raido" class="bsk-pdfm-bulk-add-by-media-library-date-way-raido" value="Current" /> <?php esc_html_e( "Current server date&amp;time", 'bskpdfmanager' ); ?>
                </label>
                <label style="margin-left:20px;">
                    <input type="radio" name="bsk_pdfm_bulk_add_by_media_library_date_way_raido" class="bsk-pdfm-bulk-add-by-media-library-date-way-raido" value="Parsed" disabled /> <?php esc_html_e( "Parsed date from filename", 'bskpdfmanager' ); ?>
                </label>
            </span>
        </p>
        <?php
            $current_upload_path = BSKPDFManager::$_upload_path;
            if( $organise_directory_strucutre_with_year_month ){
                $current_upload_path .= wp_date( 'Y/m/' );
            }
        
            $current_upload_path_to_show = $current_upload_path;
            $current_upload_path_to_show = str_replace(BSKPDFManager::$_upload_root_path, '', $current_upload_path_to_show);
        ?>
        <p style="font-weight: bold; margin-top: 10px;">* <?php esc_html_e( "Upload and Move option:", 'bskpdfmanager' ); ?></p>
        <p>
            <label>
                <input type="checkbox" name="bsk_pdfm_move_doc_out_meida_library" value="YES" id="bsk_pdfm_move_doc_out_meida_library_ID" disabled /> <?php esc_html_e( 'Upload the selected document to current upload directory: ', 'bskpdfmanager' );?>
            </label>
            <span style="font-weight: bold; padding-left: 5px; width: 70%;"><?php echo esc_html($current_upload_path_to_show); ?></span>
        </p>
        <p id="bsk_pdfm_move_doc_out_media_library_delete_option_container_ID" style="display: block;">
            <label>
                <input type="checkbox" name="bsk_pdfm_move_doc_out_meida_library_delete" value="YES" disabled /> <?php esc_html_e( 'Delete the document from Meida Library. This means the document will be only managed by BSK PDF Manager.', 'bskpdfmanager' );?>
            </label>
        </p>
        <p><span style="font-style: italic;">The above option(s) help you to place all documents in same upload directoy in your server.</span></p>
        <p style="font-weight: bold;"><?php esc_html_e( "Generate Featured Image", 'bskpdfmanager' ); ?>:</p>
        <p><?php esc_html_e( 'This action will generate a thumbnail from the selected page of the PDF document and set it as the featured image of the PDF document.', 'bskpdfmanager' ); ?></p>
        <p><?php esc_html_e( 'If the selected page number exceeds the maximum page number, the first page will be used by default.', 'bskpdfmanager' ); ?></p>
        <?php
        $disable_generate_thumb = 'disabled';
        ?>
        <div class="bsk-pdfm-tips-box" style="text-align: left;">
            <p>This feature requires a <span style="font-weight: bold;">CREATOR</span>( or above ) license for <a href="<?php echo esc_url(BSKPDFManager::$url_to_upgrade); ?>" target="_blank">Pro version</a>. </p>
        </div>
        <?php
        $load_imagick_return = BSKPDFM_Dashboard_PDF_Image_Editor::bsk_pdfm_check_imagick();
        if ( is_wp_error( $load_imagick_return ) ) {
            $disable_generate_thumb = 'disabled';
            echo '<div class="notice notice-error inline">'.$load_imagick_return->get_error_message().'</div>';
        }
        ?>
        <table class="widefat bsk-pdfm-bulk-add-by-media-library-files-list-table striped" style="width:100%;">
            <thead>
                <tr>
                    <td class="check-column" style="width:5%; padding-left:10px;"><input type='checkbox' /></td>
                    <td style="width:23%;"><?php esc_html_e( "File", 'bskpdfmanager' ); ?></td>
                    <td style="width:32%;"><?php esc_html_e( "Title", 'bskpdfmanager' ); ?></td>
                    <td style="width:20%;"><?php esc_html_e( "Date&Time", 'bskpdfmanager' ); ?></td>
                    <td style="width:20%;"><label><input type='checkbox' class="bsk-pdfm-media-generate-pdfs-featured-image-all" style="padding:0; margin:0 5px 0 0;" /><?php esc_html_e( 'Generate Featured Image', 'bskpdfmanager' ); ?></label></td>
                </tr>
            </thead>
            <tbody id="bsk_pdfm_add_by_media_library_tboday_ID"></tbody>
            <tfoot>
                <tr>
                    <td class="check-column" style="padding-left:10px;"><input type='checkbox' /></td>
                    <td style="width:23%;"><?php esc_html_e( "File", 'bskpdfmanager' ); ?></td>
                    <td style="width:32%;"><?php esc_html_e( "Title", 'bskpdfmanager' ); ?></td>
                    <td style="width:20%;"><?php esc_html_e( "Date&Time", 'bskpdfmanager' ); ?></td>
                    <td style="width:20%;"><label><input type='checkbox' class="bsk-pdfm-media-generate-pdfs-featured-image-all" style="padding:0; margin:0 5px 0 0;" /><?php esc_html_e( 'Generate Featured Image', 'bskpdfmanager' ); ?></label></td>
                </tr>
            </tfoot>
            <input type="hidden" class="bsk-pdfm-bulk-add-by-meida-library-current-server-date-time" value="<?php echo wp_date( 'Y-m-d H:i:s' ) ?>" />
            <input type="hidden" class="bsk-pdfm-bulk-add-by-meida-library-wp-gmt-offset" value="<?php echo get_option( 'gmt_offset' ); ?>" />
            <input type="hidden" class="bsk-pdfm-bulk-add-by-meida-library-site-url" value="<?php echo site_url(); ?>" />
            <input type="hidden" class="bsk-pdfm-bulk-add-by-meida-librry-allowed-mime-type" value="<?php echo implode( ',', $allowed_mime_type ); ?>" />
            <span class="bsk-pdfm-bulk-add-by-meida-library-check_to_generate_str" style="display: none"><?php esc_html_e( 'Check to generate ', 'bskpdfmanager' ); ?></span>
            <span class="bsk-pdfm-bulk-add-by-meida-library-from_the_page_no_str" style="display: none"><?php esc_html_e( 'from the page No. %s of the PDF', 'bskpdfmanager' ); ?></span>
            <input type="hidden" class="bsk-pdfm-media-generate-thumb-max" value="<?php echo $maximum_gen_thumb; ?>" />
		</table>
        <p class="bsk-pdfm-error" id="bsk_pdfm_bulk_add_by_media_library_seelct_files_error_ID" style="display: none;"></p>
        <h3><?php esc_html_e( "Category", 'bskpdfmanager' ); ?></h3>
        <p class="bsk-pdfm-error" id="bsk_pdfm_bulk_add_by_media_library_select_category_error_ID" style="display: none;"></p>
		<?php
        echo BSKPDFM_Common_Backend::get_category_hierarchy_checkbox( 'bsk_pdfm_bulk_add_by_media_library_categories[]', 'bsk-pdfm-bulk-add-by-media-category-checkbox', array() );
		$nonce = wp_create_nonce( 'bsk_pdf_manager_pdf_upload_by_media_library_nonce' );
        $parse_date_ajax_nonce = wp_create_nonce( "bsk-pdfm-parse-date-ajax-nonce" );
        ?>
		<p style="margin-top:20px;">
        	<input type="hidden" name="nonce" value="<?php echo esc_attr($nonce); ?>" />
            <input type="hidden" class="bsk-pdfm-parse-date-ajax-nonce" value="<?php echo esc_attr($parse_date_ajax_nonce); ?>" />
        	<input type="hidden" name="bsk_pdf_manager_action" value="pdf_upload_by_media_library" />
            <input type="hidden" class="bsk-pdfm-bulk-add-by-media-library-ajax-url" value="<?php echo esc_attr(BSKPDFManager::$_ajax_loader_img_url); ?>" />
        	<input type="button" id="bsk_pdf_manager_add_by_media_library_save_button_ID" class="button-primary" value="<?php esc_attr_e( "Submit...", 'bskpdfmanager' ); ?>" disabled />
        </p>
        <?php
	}
	
	function bsk_pdm_pdf_upload_by_media_library_save_fun( $data ){
		global $wpdb;
		//check nonce field
		if( !wp_verify_nonce( $data['nonce'], 'bsk_pdf_manager_pdf_upload_by_media_library_nonce' ) ){
			return;
		}
		if( !isset($data['bsk_pdfm_bulk_add_by_media_documents']) ||
			!is_array($data['bsk_pdfm_bulk_add_by_media_documents']) || 
			!isset($data['bsk_pdfm_bulk_add_by_media_titles']) ||
            count($data['bsk_pdfm_bulk_add_by_media_titles']) < 1 ||
			!is_array($data['bsk_pdfm_bulk_add_by_media_library_categories']) || 
			count($data['bsk_pdfm_bulk_add_by_media_library_categories']) < 1 ){
				
			return;
		}
        
        $added_document_ID_array = array();
        $failed_document_post_ID_array = array();
		//insert pdf into database & move file to destination
		foreach( $data['bsk_pdfm_bulk_add_by_media_documents'] as $index => $post_ID ){
            
            $time_h = sanitize_text_field($data['bsk_pdfm_bulk_add_by_media_dates_hour'][$post_ID]);
            $time_m = sanitize_text_field($data['bsk_pdfm_bulk_add_by_media_dates_minute'][$post_ID]);
            $time_s = sanitize_text_field($data['bsk_pdfm_bulk_add_by_media_dates_second'][$post_ID]);
            $time_h = sprintf( '%02d', $time_h );
            $time_m = sprintf( '%02d', $time_m );
            $time_s = sprintf( '%02d', $time_s );
			
			$pdf_data = array();
			$pdf_data['cat_id'] = '999999';
			$pdf_data['title'] = wp_unslash($data['bsk_pdfm_bulk_add_by_media_titles'][$post_ID]);
			$pdf_data['last_date'] = $data['bsk_pdfm_bulk_add_by_media_dates'][$post_ID].' '.$time_h.':'.$time_m.':'.$time_s;
			$pdf_data['by_media_uploader'] = $post_ID;
            $pdf_data['description'] = '';
            $pdf_data['publish_date'] = NULL;
            $pdf_data['expiry_date'] = NULL;
			
			//insert
			$return = $wpdb->insert( 
                                       $wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name, 
                                       $pdf_data, 
                                       array('%s', '%s', '%s', '%d') 
                                   );
			if ( !$return ){
                $failed_document_post_ID_array[] = $post_ID;
				continue;
			}
            $pdf_id = $wpdb->insert_id; 
            
            //update pdf categories
            if( isset($data['bsk_pdfm_bulk_add_by_media_library_categories']) && 
                is_array($data['bsk_pdfm_bulk_add_by_media_library_categories']) && 
                count($data['bsk_pdfm_bulk_add_by_media_library_categories']) > 0 ){
                
                foreach( $data['bsk_pdfm_bulk_add_by_media_library_categories'] as $cat_id ){
                    $wpdb->insert( 
                                   $wpdb->prefix.BSKPDFManager::$_rels_tbl_name, 
                                   array( 'cat_id' => $cat_id, 'pdf_id' => $pdf_id ), 
                                   array('%d', '%d') 
                                 );
                }
            }
			$added_document_ID_array[] = $pdf_id; 
		}
		
		$message = count($added_document_ID_array).' '.esc_html__( 'file(s) have been uploaded succesfully', 'bskpdfmanager' );
		if( count($failed_document_post_ID_array) > 0 ){
			$message .= ', '.count($failed_document_post_ID_array).' failed.';
		}
		
		update_option( BSKPDFManager::$_plugin_temp_option_prefix.'message_id_51', $message);
		$message_id = 51;
		$redirect_to = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['add_by_media_library'].'&message='.$message_id  );
		wp_redirect( $redirect_to );
		exit;
	}
    
    function bsk_pdf_manager_admin_notice(){
		$current_page = isset($_REQUEST['page']) ? sanitize_text_field($_REQUEST['page']) : '';
		if( !$current_page || 
            !in_array($current_page, BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages) ||
            $current_page != BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['add_by_media_library'] ){
			return;
		}
		
		$message_id = isset($_REQUEST['message']) ? intval(sanitize_text_field($_REQUEST['message'])) : 0;
		if( !$message_id ){
			return;
		}
		
        $type = 'WARNING';
        $msg_to_show = '<p>'.esc_html(get_option( BSKPDFManager::$_plugin_temp_option_prefix.'message_id_'.$message_id )).'</p>';
        
        //admin message
		if( $type == 'WARNING' ){
			echo '<div class="updated">';
			echo $msg_to_show;
			echo '</div>';
		}else if( $type == 'ERROR' ){
			echo '<div class="error">';
			echo $msg_to_show;
			echo '</div>';
		}
	}
    
}