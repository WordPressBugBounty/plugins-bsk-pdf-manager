<?php

class BSKPDFM_Dashboard_FTP {

	public function __construct() {
		global $wpdb;
	}
	
	function bsk_pdf_manager_pdfs_add_by_ftp(){
        
        require_once( 'pdf-image-editor.php' );
        
		global $current_user, $wpdb;
		
		//get all categories
		$sql = 'SELECT COUNT(*) FROM '.esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name);
		if( $wpdb->get_var( $sql ) < 1 ){
			$create_category_url = add_query_arg( 'page', BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['base'], admin_url() );
			$create_category_url = add_query_arg( 'view', 'addnew', $create_category_url );
			echo 'Please <a href="'.esc_attr( $create_category_url ).'">'.esc_html__('create category', 'bskpdfmanager' ).'</a> first';
			
			return;
		}
		
        $current_server_datetime = wp_date( 'Y-m-d H:i:s' );
        $supported_extension_and_mime_type = BSKPDFM_Common_Backend::get_supported_extension_with_mime_type();

		$maximum_of_list = 50;
		$pdf_files_under_ftp_folder = array();
		$ftp_folder  = opendir( BSKPDFManager::$_upload_path.BSKPDFManager::$_upload_folder_4_ftp );
		$item_filename = 0;
		while (false !== ($filename = readdir($ftp_folder))) {
			if( $filename == '.' ||
			    $filename == '..' ||
				$filename == 'index.php' ){
				
				continue;
			}
            $title = '';
			$ext_n_type = wp_check_filetype( $filename );
            if ( $ext_n_type['ext'] && $ext_n_type['type'] ) {
                if( !array_key_exists( strtolower($ext_n_type['ext']), $supported_extension_and_mime_type) ){
                    continue;
                }
                if( !in_array( $ext_n_type['type'], $supported_extension_and_mime_type[strtolower($ext_n_type['ext'])] ) ){
                    continue;
                }
                $title = str_replace( '.'.$ext_n_type['ext'], '', $filename );
                $title = str_replace( '.'.strtoupper($ext_n_type['ext']), '', $title );
            } else {
                $ext_array = explode( '.', $filename );
                $ext = strtolower( $ext_array[count($ext_array) - 1] );
                if( !array_key_exists( $ext, $supported_extension_and_mime_type) ){
                    continue;
                }
                $title = str_replace( '.'.$ext, '', $filename );
                $title = str_replace( '.'.strtoupper($ext), '', $title );
            }
            
			$item_filename++;
            $timestamp_of_last_modify = filemtime( BSKPDFManager::$_upload_path.BSKPDFManager::$_upload_folder_4_ftp.$filename );
            $file_unique_id = uniqid();
			$pdf_files_under_ftp_folder[$file_unique_id] = array( 
                                                    'name' => $filename, 
                                                    'title' => str_replace( '.'.$ext_n_type['ext'], '', $filename ),
                                                    'ext' => $ext_n_type['ext'],
                                                    'mimetype' => $ext_n_type['type'],
                                                    'datetime' => wp_date( 'Y-m-d H:i:s', $timestamp_of_last_modify ),
                                                 );
			if( $maximum_of_list <= $item_filename ){
				break;
			}
		}
        
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
        
		$upload_folder_4_ftp_display = str_replace( BSKPDFManager::$_upload_root_path, '', BSKPDFManager::$_upload_path_4_ftp );
        
        $temp_msg = __( 'Please uplaod all you documents( %s ) to the folder <b>%s</b> first.', 'bskpdfmanager' );
        $temp_msg = sprintf( $temp_msg, '<strong>'.esc_html(implode(', ', $supported_extension )).'</strong>', '<strong>'.esc_html($upload_folder_4_ftp_display).'</strong>' );
		echo '  <p style="margin-top:30px;">'.$temp_msg.'</p>';
		echo '  <p>'.esc_html_e( 'After upload, your documents will be moved out from this folder.', 'bskpdfmanager' ).'<p>';
        
        $temp_msg = __( 'To avoid time out error on your server the maximum of documents that can be listed here is %d. It means you may import max %d documents every time. You may come to here to import your PDFs for many times untill all you PDFs uploaded', 'bskpdfmanager' );
        $temp_msg = sprintf( $temp_msg, $maximum_of_list, $maximum_of_list );
		echo '  <p>'.$temp_msg.'</p>';
        
		?>
        <h3><?php esc_html_e( 'Settings', 'bskpdfmanager' ); ?></h3>
        <p style="font-weight: bold;"><?php printf( esc_html__( 'Exclude extension( %s ) from title', 'bskpdfmanager' ), esc_html(implode(', ', $supported_extension ) )); ?>:</p>
        <p>
            <span class="bsk-pdf-field">
                <label>
                    <input type="radio" name="bsk_pdfm_ftp_exclude_extension_raido" class="bsk-pdfm-ftp-exclude-extension-raido" value="YES" checked="checked" /> <?php esc_html_e( 'Yes', 'bskpdfmanager' ); ?>
                </label>
                <label style="margin-left:20px;">
                    <input type="radio" name="bsk_pdfm_ftp_exclude_extension_raido" class="bsk-pdfm-ftp-exclude-extension-raido" value="NO" /> <?php esc_html_e( 'No', 'bskpdfmanager' ); ?>
                </label>
            </span>
        </p>
        <p style="font-weight: bold;"><?php esc_html_e('Replace all _ in title to space', 'bskpdfmanager' ); ?>:</p>
        <p>
            <span class="bsk-pdf-field">
                <label>
                    <input type="radio" name="bsk_pdfm_ftp_replace_underscroe_raido" class="bsk-pdfm-ftp-replace-underscroe-raido" value="YES" /> <?php esc_html_e( 'Yes', 'bskpdfmanager' ); ?>
                </label>
                <label style="margin-left:20px;">
                    <input type="radio" name="bsk_pdfm_ftp_replace_underscroe_raido" class="bsk-pdfm-ftp-replace-underscroe-raido" value="NO" checked="checked" /> <?php esc_html_e( 'No', 'bskpdfmanager' ); ?>
                </label>
            </span>
        </p>
        <p style="font-weight: bold;"><?php esc_html_e('Replace all - in title to space', 'bskpdfmanager' ); ?>:</p>
        <p>
            <span class="bsk-pdf-field">
                <label>
                    <input type="radio" name="bsk_pdfm_ftp_replace_hyphen_raido" class="bsk-pdfm-ftp-replace-hyphen-raido" value="YES" /> <?php esc_html_e( 'Yes', 'bskpdfmanager' ); ?>
                </label>
                <label style="margin-left:20px;">
                    <input type="radio" name="bsk_pdfm_ftp_replace_hyphen_raido" class="bsk-pdfm-ftp-replace-hyphen-raido" value="NO" checked="checked" /> <?php esc_html_e( 'No', 'bskpdfmanager' ); ?>
                </label>
            </span>
        </p>
        <p style="font-weight: bold;"><?php esc_html_e( "Set document's date&time with", 'bskpdfmanager' ); ?>:</p>
        <p>
            <span class="bsk-pdf-field">
                <label>
                    <input type="radio" name="bsk_pdfm_ftp_date_way_raido" class="bsk-pdfm-ftp-date-way-raido" value="Last_Modify" checked="checked" /> <?php esc_html_e( "Document's last modified date&amp;time", 'bskpdfmanager' ); ?>
                </label>
                <label style="margin-left:20px;">
                    <input type="radio" name="bsk_pdfm_ftp_date_way_raido" class="bsk-pdfm-ftp-date-way-raido" value="Current" /> <?php esc_html_e( "Current server date&amp;time", 'bskpdfmanager' ); ?>
                </label>
                <label style="margin-left:20px;">
                    <input type="radio" name="bsk_pdfm_bulk_add_by_media_library_date_way_raido" class="bsk-pdfm-bulk-add-by-media-library-date-way-raido" value="Parsed" disabled /> <?php esc_html_e( "Parsed date from filename", 'bskpdfmanager' ); ?>
                </label>
            </span>
        </p>
        <p style="font-weight: bold;"><?php esc_html_e( "Generate Featured Image", 'bskpdfmanager' ); ?>:</p>
        <p><?php esc_html_e( 'This action will generate a thumbnail from the selected page of the PDF document and set it as the featured image of the PDF document.', 'bskpdfmanager' ); ?></p>
        <p><?php esc_html_e( 'If the selected page number exceeds the maximum page number, the first page will be used by default.', 'bskpdfmanager' ); ?></p>
        <div class="bsk-pdfm-tips-box" style="text-align: left;">
            <p>This feature requires a <span style="font-weight: bold;">CREATOR</span>( or above ) license for <a href="<?php echo esc_url(BSKPDFManager::$url_to_upgrade); ?>" target="_blank">Pro version</a>. </p>
        </div>
        <?php
        $load_imagick_return = BSKPDFM_Dashboard_PDF_Image_Editor::bsk_pdfm_check_imagick();
        if ( is_wp_error( $load_imagick_return ) ) {
            echo '<div class="notice notice-error inline">'.$load_imagick_return->get_error_message().'</div>';
        }
        ?>
        <table class="widefat bsk-pdfm-ftp-files-list-table striped" style="width:100%;table-layout:fixed;">
            <thead>
                <tr>
                    <td class="check-column" style="width:5%; padding-left:10px;"><input type='checkbox' /></td>
                    <td style="width:23%;overflow:visible;"><?php esc_html_e( "File", 'bskpdfmanager' ); ?></td>
                    <td style="width:32%;"><?php esc_html_e( "Title", 'bskpdfmanager' ); ?></td>
                    <td style="width:20%;"><?php esc_html_e( "Date&Time", 'bskpdfmanager' ); ?></td>
                    <td style="width:20%;"><label><input type='checkbox' class="bsk-pdfm-ftp-generate-pdfs-featured-image-all" style="padding:0; margin:0 5px 0 0;" /><?php esc_html_e( 'Generate Featured Image', 'bskpdfmanager' ); ?></label></td>
                </tr>
            </thead>
            <tbody>
                <?php
                if( count($pdf_files_under_ftp_folder) < 1 ){
                ?>
                <tr>
                    <td colspan="5"><?php esc_html_e( "No valid PDF files found", 'bskpdfmanager' ); ?></td>
                </tr>
                <?php
                }else{
                    foreach( $pdf_files_under_ftp_folder as $file_unique_id => $file_obj ){
                        $disable_generate_thumb_p = '';
                        $file_unique_id = esc_attr($file_unique_id);
                        //disable generate featured image if not
                        if( $file_obj['mimetype'] != 'application/pdf' ){
                            $disable_generate_thumb_p = 'disabled';
                        }
                ?>
                    <tr class="bsk-pdfm-ftp-files-list-tr">
                        <td class="check-column" style="padding-left:18px;">
                            <input type='checkbox' name='bsk_pdf_manager_ftp_files[<?php echo esc_attr($file_unique_id); ?>]' value="<?php echo esc_attr($file_obj['name']); ?>" class="bsk-pdf-manager-ftp-files-chk" style="padding:0; margin:0;" />
                        </td>
                        <td class="bsk-pdfm-bulk-add-by-media-filename"><?php echo esc_html($file_obj['name']); ?></td>
                        <?php
                        $title = $file_obj['title'];
                        ?>
                        <td>
                            <input type="text" name="bsk_pdf_manager_ftp_titles[<?php echo esc_attr($file_unique_id); ?>]" value="<?php echo esc_attr($title); ?>" maxlength="512" style="width: 350px;" class="bsk-pdf-manager-ftp-title-input" />
                            <input type="hidden" class="bsk-pdf-manager-ftp-extension-val" value="<?php echo esc_attr($file_obj['ext']); ?>" />
                            <input type="hidden" class="bsk-pdf-manager-ftp-title-hidden" value="<?php echo esc_attr($title); ?>" />
                        </td>
                        <?php
                        $date = esc_attr(substr( $file_obj['datetime'], 0, 10 ));
                        $time_h = esc_attr(substr( $file_obj['datetime'], 11, 2 ));
                        $time_m = esc_attr(substr( $file_obj['datetime'], 14, 2 ));
                        $time_s = esc_attr(substr( $file_obj['datetime'], 17, 2 ));
                        ?>
                        <td>
                            <input type="text" name="bsk_pdf_manager_ftp_dates[<?php echo $file_unique_id; ?>]" value="<?php echo $date; ?>" class="bsk-pdfm-date-time-date bsk-date" />
                            <span>@</span>
                            <input type="number" name="bsk_pdf_manager_ftp_dates_hour[<?php echo $file_unique_id; ?>]" class="bsk-pdfm-date-time-hour" value="<?php echo $time_h; ?>" min="0" max="23" step="1" />
                            <span>:</span>
                            <input type="number" name="bsk_pdf_manager_ftp_dates_minute[<?php echo $file_unique_id; ?>]" class="bsk-pdfm-date-time-minute" value="<?php echo $time_m; ?>" min="0" max="59" step="1"  />
                            <span>:</span>
                            <input type="number" name="bsk_pdf_manager_ftp_dates_second[<?php echo $file_unique_id; ?>]" class="bsk-pdfm-date-time-second" value="<?php echo $time_s; ?>" min="0" max="59" step="1"  />
                            <input type="hidden" class="bsk-pdf-manager-ftp-last-modify-datetime" value="<?php echo esc_attr($file_obj['datetime']); ?>" />
                        </td>
                        <td class="bsk-pdfm-ftp-generte-thumb-td">
                            <p>
                                <label>
                                    <input type="checkbox" name="bsk_pdfm_ftp_generate_thumb_chk[]" class="bsk-pdfm-ftp-generate-thumbnail-chk" value="<?php echo $file_unique_id; ?>" <?php echo $disable_generate_thumb_p; ?>/> <?php esc_html_e( 'Check to generate ', 'bskpdfmanager' ); ?>
                                </label>
                                <span class="bsk-pdfm-ftp-generate-thumbnail-settings" style="display: none;">
                                    <?php 
                                    $text = esc_html__( 'from the page No. %s of the PDF', 'bskpdfmanager' );
                                    $page_number_input = '<input type="number" name="bsk_pdfm_ftp_generate_thumb_chk_page_number['.$file_unique_id.']" value="1" min="1" step="1" style="width: 50px;" />';
                                    printf( $text, $page_number_input ); 
                                    ?>
                                </span>
                            </p>
                        </td>
                        <input type="hidden" name="bsk_pdfm_ftp_skips[<?php echo $file_unique_id; ?>]" class="bsk-pdfm-ftp-skips-hidden-val" value="0" />
                    </tr>
                <?php
                    }
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td class="check-column" style="padding-left:10px;"><input type='checkbox' /></td>
                    <td><?php esc_html_e( "File", 'bskpdfmanager' ); ?></td>
                    <td><?php esc_html_e( "Title", 'bskpdfmanager' ); ?></td>
                    <td><?php esc_html_e( "Date&Time", 'bskpdfmanager' ); ?></td>
                    <td><label><input type='checkbox' class="bsk-pdfm-ftp-generate-pdfs-featured-image-all" style="padding:0; margin:0 5px 0 0;" /><?php esc_html_e( 'Generate Featured Image', 'bskpdfmanager' ); ?></label></td>
                </tr>
            </tfoot>
            <input type="hidden" class="bsk-pdf-manager-ftp-current-server-date-time" value="<?php echo $current_server_datetime; ?>" />
            <input type="hidden" class="bsk-pdfm-ftp-generate-thumb-max" value="<?php echo $maximum_gen_thumb; ?>" />
		</table>
		<?php
		if( count($pdf_files_under_ftp_folder) > 0 ){
		?>
        <h3><?php esc_html_e( "Category", 'bskpdfmanager' ); ?></h3>
		<?php
        echo BSKPDFM_Common_Backend::get_category_hierarchy_checkbox( 'bsk_pdf_manager_ftp_categories[]', 'bsk-pdfm-ftp-category-checkbox', array() );
		$nonce = wp_create_nonce( 'bsk_pdf_manager_pdf_upload_by_ftp_nonce' );
        ?>
		<p style="margin-top:20px;">
        	<input type="hidden" name="nonce" value="<?php echo esc_attr($nonce); ?>" />
        	<input type="hidden" name="bsk_pdf_manager_action" value="pdf_upload_by_ftp" />
        	<input type="button" id="bsk_pdf_manager_add_by_ftp_save_button_ID" class="button-primary" value="<?php esc_attr_e( 'Upload...', 'bskpdfmanager' ); ?>"  disabled />
        </p>
        <?php
		}
	}//end of function
}