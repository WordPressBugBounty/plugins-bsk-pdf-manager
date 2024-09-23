<?php

class BSKPDFM_Dashboard_PDF {

    private $_file_upload_message = array();

    public function __construct() {
		
        require_once( 'pdf-image-editor.php' );
		
		$this->bsk_pdf_manager_init_message();

		add_action( 'admin_notices', array($this, 'bsk_pdf_manager_admin_notice') );
		add_action( 'bsk_pdf_manager_pdf_save', array($this, 'bsk_pdf_manager_pdf_save_fun') );
        add_action( 'bsk_pdf_manager_bulk_delete', array($this, 'bsk_pdf_manager_bulk_delete_fun') );
        add_action( 
                    'wp_ajax_bsk_pdfm_check_slug', 
                    array( $this, 'bsk_pdfm_check_slug_fun' )
                  );
        
        add_action( 'admin_init', array( $this, 'bsk_pdf_manager_process_row_actions_fun' ) );
	}
	
	function bsk_pdf_manager_init_message(){
	
		$this->_file_upload_message[1] = array( 'message' => __( 'The uploaded file exceeds the maximum file size allowed.', 'bskpdfmanager' ), 
												'type' => 'ERROR');
		$this->_file_upload_message[2] = array( 'message' => __( 'The uploaded file exceeds the maximum file size allowed.', 'bskpdfmanager' ), 
												'type' => 'ERROR');
		$this->_file_upload_message[3] = array( 'message' => __( 'The uploaded file was only partially uploaded. Please try again in a few minutes.', 'bskpdfmanager' ), 
												'type' => 'ERROR');
		$this->_file_upload_message[4] = array( 'message' => __( 'No file was uploaded. Please try again in a few minutes.', 'bskpdfmanager' ), 
												'type' => 'ERROR');
		$this->_file_upload_message[5] = array( 'message' => __( 'File size is 0 please check and try again in a few minutes.', 'bskpdfmanager' ), 
												'type' => 'ERROR');
		$this->_file_upload_message[6] = array( 'message' => __( 'Failed, seems there is no temporary folder. Please try again in a few minutes.', 'bskpdfmanager' ), 
												'type' => 'ERROR');
		$this->_file_upload_message[7] = array( 'message' => __( 'Failed to write file to disk. Please try again in a few minutes.', 'bskpdfmanager' ), 
												'type' => 'ERROR');
		$this->_file_upload_message[8] = array( 'message' => __( 'A PHP extension stopped the file upload. Please try again in a few minutes.', 'bskpdfmanager' ), 
												'type' => 'ERROR');
		$this->_file_upload_message[15] = array( 'message' => __( 'Invalid file type, the file you uploaded is not allowed.', 'bskpdfmanager' ), 
												 'type' => 'ERROR');
		$this->_file_upload_message[16] = array( 'message' => __( 'Faild to write file to destination folder.', 'bskpdfmanager' ), 
												 'type' => 'ERROR');
		$this->_file_upload_message[17] = array( 'message' => __( 'No file was uploaded or the file is not valid. Please try again.', 'bskpdfmanager' ), 
												 'type' => 'ERROR');
		
		$this->_file_upload_message[20] = array( 'message' => __( 'Add document successfully.', 'bskpdfmanager' ), 
												 'type' => 'SUCCESS');
		$this->_file_upload_message[21] = array( 'message' => __( 'Failed to add document.', 'bskpdfmanager' ), 
												 'type' => 'ERROR');
        $this->_file_upload_message[22] = array( 'message' => __( 'Update document successfully.', 'bskpdfmanager' ), 
												 'type' => 'SUCCESS');
        $this->_file_upload_message[23] = array( 'message' => __( 'Failed to update document.', 'bskpdfmanager' ), 
												 'type' => 'ERROR');
												 
		$this->_file_upload_message[31] = array( 'message' => __( 'Upload file failed.', 'bskpdfmanager' ), 
												 'type' => 'ERROR');					
		$this->_file_upload_message[32] = array( 'message' => __( 'Upload file failed.', 'bskpdfmanager' ), 
												 'type' => 'ERROR');					
		$this->_file_upload_message[33] = array( 'message' => __( 'Upload file failed.', 'bskpdfmanager' ), 
												 'type' => 'ERROR');																 
		$this->_file_upload_message[34] = array( 'message' => __( 'Upload file failed.', 'bskpdfmanager' ), 
												 'type' => 'ERROR');
        $this->_file_upload_message[35] = array( 'message' => __( 'The document moved to Trash.', 'bskpdfmanager' ), 
												 'type' => 'SUCCESS');
        $this->_file_upload_message[36] = array( 'message' => __( 'Invalid document ID', 'bskpdfmanager' ), 
												 'type' => 'ERROR');
        $this->_file_upload_message[37] = array( 'message' => __( 'Invalid nonce, please refresh page and try again', 'bskpdfmanager' ), 
												 'type' => 'ERROR');
        $this->_file_upload_message[38] = array( 'message' => __( 'The document was restored.', 'bskpdfmanager' ), 
												 'type' => 'SUCCESS');
        $this->_file_upload_message[39] = array( 'message' => __( 'The document was set to draft.', 'bskpdfmanager' ), 
												 'type' => 'SUCCESS');
        $this->_file_upload_message[40] = array( 'message' => __( 'The document was published.', 'bskpdfmanager' ), 
												 'type' => 'SUCCESS');
        $this->_file_upload_message[41] = array( 'message' => __( 'The document was foced to draft as no file uploaded.', 'bskpdfmanager' ), 
												 'type' => 'WARNING');
	}
	
	function bsk_pdf_manager_admin_notice(){
		$current_page = isset($_REQUEST['page']) ? sanitize_text_field($_REQUEST['page']) : '';
		if( !$current_page || !in_array($current_page, BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages) ){
			return;
		}
		
		$message_id = isset($_REQUEST['message']) ? intval(sanitize_text_field($_REQUEST['message'])) : 0;
		if( !$message_id ){
			return;
		}
		if( !isset($this->_file_upload_message[ $message_id ]) ){
			return;
		}
		
		$type = $this->_file_upload_message[ $message_id ]['type'];
		$msg_to_show = $this->_file_upload_message[ $message_id ]['message'];
		if( !$msg_to_show ){
			return;
		}
		$msg_to_show = '<p>'.esc_html($msg_to_show).'</p>';
		if( in_array( $message_id, array(15, 16, 31, 32, 33, 34) ) ){
			$msg_to_show .= '<p>'.esc_html(get_option( BSKPDFManager::$_plugin_temp_option_prefix.'message_id_'.$message_id )).'</p>';
		}
		
		//admin message
		if( $type == 'SUCCESS' ){
			echo '<div class="notice notice-success is-dismissible">';
			echo $msg_to_show;
			echo '</div>';
		}else if( $type == 'ERROR' ){
			echo '<div class="notice notice-error is-dismissible">';
			echo $msg_to_show;
			echo '</div>';
		}else if( $type == 'WARNING' ){
			echo '<div class="notice notice-warning is-dismissible">';
			echo $msg_to_show;
			echo '</div>';
		}
	}

	
	function pdf_edit( $pdf_id = -1 ){
		global $wpdb;

		//get all categories
		$sql = 'SELECT COUNT(*) FROM '.esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name).' WHERE 1 AND `type` LIKE "CAT"';
		$categories_count = $wpdb->get_var( $sql );

        //get all tags
        $sql = 'SELECT COUNT(*) FROM '.esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name).' WHERE 1 AND `type` LIKE "TAG"';
		$tags_count = $wpdb->get_var( $sql );

        $server_date_time = wp_date( 'Y-m-d H:i:s' );
        $pdf_date = $server_date = substr( $server_date_time, 0, 10 );
        $pdf_time_h = $server_time_h = substr( $server_date_time, 11, 2 );
        $pdf_time_m = $server_time_m = substr( $server_date_time, 14, 2 );
        $pdf_time_s = $server_time_s = substr( $server_date_time, 17, 2 );

		$pdf_obj_array = false;
        $pdf_categories_array = array();
        $pdf_tags_array = array();
		if ($pdf_id > 0){
			$sql = 'SELECT * FROM '.$wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name.' WHERE id = %d';
			$sql = $wpdb->prepare( $sql, $pdf_id );
			$pdfs_obj_array = $wpdb->get_results( $sql );
			if (count($pdfs_obj_array) > 0){
				$pdf_obj_array = (array)$pdfs_obj_array[0];
				$pdf_date_time = esc_attr($pdf_obj_array['last_date']);
                $pdf_date = substr( $pdf_date_time, 0, 10 );
                $pdf_time_h = substr( $pdf_date_time, 11, 2 );
                $pdf_time_m = substr( $pdf_date_time, 14, 2 );
                $pdf_time_s = substr( $pdf_date_time, 17, 2 );
			}
		}

		if( $pdf_obj_array && is_array( $pdf_obj_array ) && isset($pdf_obj_array['id']) && $pdf_obj_array['id'] ){
			//get all categories which the PDF associated
            $sql = 'SELECT * FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_rels_tbl_name).'` WHERE `pdf_id` = %d AND `type` LIKE "CAT"';
            $sql = $wpdb->prepare($sql, $pdf_obj_array['id']);
            $results = $wpdb->get_results( $sql );
            if( $results && is_array($results) && count($results) > 0 ){
                foreach( $results as $rel_obj ){
                    $pdf_categories_array[] = $rel_obj->cat_id;
                }
            }

            //get all tags which the PDF associated
            $sql = 'SELECT * FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_rels_tbl_name).'` WHERE `pdf_id` = %d AND `type` LIKE "TAG"';
            $sql = $wpdb->prepare($sql, $pdf_obj_array['id']);
            $results = $wpdb->get_results( $sql );
            if( $results && is_array($results) && count($results) > 0 ){
                foreach( $results as $rel_obj ){
                    $pdf_tags_array[] = $rel_obj->cat_id;
                }
            }
		}

		$default_enable_featured_image = true;
        $organise_directory_strucutre_with_year_month = true;
        $supported_extension = false;
        $default_enable_permalink = false;
        $default_permalink_base = 'bskpdf';
        $default_redirect_permalink_to_url = false;
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
			if( isset($plugin_settings['enable_featured_image']) ){
				$default_enable_featured_image = $plugin_settings['enable_featured_image'];
			}
            if( isset($plugin_settings['directory_with_year_month']) ){
                $organise_directory_strucutre_with_year_month = $plugin_settings['directory_with_year_month'];
			}
            if( isset($plugin_settings['supported_extension']) ){
                $supported_extension = $plugin_settings['supported_extension'];
			}
            if( isset($plugin_settings['enable_permalink']) ){
				$default_enable_permalink = $plugin_settings['enable_permalink'];
			}
            if( isset($plugin_settings['permalink_redirect_to']) ){
				$default_redirect_permalink_to_url = $plugin_settings['permalink_redirect_to'];
			}
		}
        
        $permalink_structure = get_option( 'permalink_structure' );
        if ( ! $permalink_structure ) {
            $default_enable_permalink = false;
        }
        
        if( !$supported_extension || !is_array($supported_extension) || !in_array( 'pdf', $supported_extension ) ){
            $supported_extension = array( 'pdf' );
        }
        $is_by_media_uploader = false;
        $file_url = '';
        $file_size = 0;
        if( $pdf_obj_array && is_array( $pdf_obj_array ) ){
            if( isset($pdf_obj_array['by_media_uploader']) && $pdf_obj_array['by_media_uploader'] > 1 ){
                $file_url = wp_get_attachment_url( $pdf_obj_array['by_media_uploader'] ); 
                $is_by_media_uploader = true;
            }else{
                if( $pdf_obj_array['file_name'] &&
                    file_exists( BSKPDFManager::$_upload_root_path.$pdf_obj_array['file_name'] ) ){
                    $file_url = site_url().'/'.$pdf_obj_array['file_name'];
                }
            }
            $file_size = $pdf_obj_array['size'];
        }
		?>
        <div id="bsk_pdfm_doc_stuff">
            <div id="bsk_pdfm_doc_body" class="metabox-holder columns-2">
                <div id="bsk_pdfm_doc_body_content" style="position: relative;">
                    <?php
                        $title_value = '';
                        if( $pdf_obj_array && isset($pdf_obj_array['title']) ){
                            $title_value = $pdf_obj_array['title'];
                        }
                        $prompt_text_display = $title_value == '' ? 'block' : 'none';
                    ?>
                    <div id="bsk_pdfm_doc_titile_div">
                        <div id="bsk_pdfm_doc_titile_wrap">
                            <label class="" id="bsk_pdfm_doc_title_prompt_text" for="title" style="display: <?php echo $prompt_text_display; ?>;"><?php esc_html_e( 'Add title', 'bskpdfmanager' ); ?></label>
                            <input type="text" name="bsk_pdf_manager_pdf_titile" size="30" value="<?php echo esc_attr($title_value); ?>" id="bsk_pdf_manager_pdf_titile_id" spellcheck="true" autocomplete="off">
                            <input type="hidden" value="" id="bsk_pdfm_filename_hidden_ID" />
                        </div>
                        <p id="bsk_pdfm_pdf_titile_error_ID" class="bsk-pdfm-error" style="display: none;">error message</p>
                    </div><!-- /bsk_pdfm_doc_titile_div -->
                    <div id="bsk_pdfm_doc_filename_as_titile_div" class="postbox">
                        <div class="inside">
                            <div class="row">
                                <div class="left-column">
                                    <label><?php esc_html_e( 'Use file name as title', 'bskpdfmanager' ); ?>:</label>
                                </div>
                                <div class="right-column">
                                    <input type="checkbox" name="bsk_pdf_manager_pdf_file_new_use_name_as_title" id="bsk_pdf_manager_pdf_file_new_use_name_as_title_ID" disabled />
                                </div>
                                <div style="clear: both"></div>
                            </div>
                            <?php
                            $supported_extension_data = implode( ',', $supported_extension );                                           
                            ?>
                            <div class="row" id="bsk_pdf_manager_pdf_title_exclude_extension_container_ID">
                                <div class="left-column">
                                    <label><?php esc_html_e('Exclude extension'); ?></label>
                                </div>
                                <div class="right-column">
                                    <label><input type="radio" name="bsk_pdfm_exclude_extension_from_title" value="YES" class="bsk-pdfm-exclude-extension-from-title" disabled /> <?php esc_html_e( 'Yes', 'bskpdfmanager' ); ?></label>
                                    <label style="margin-left:20px;"><input type="radio" name="bsk_pdfm_exclude_extension_from_title" value="NO" checked="checked" class="bsk-pdfm-exclude-extension-from-title" /> <?php esc_html_e( 'No', 'bskpdfmanager' ); ?></label>
                                    <input type="hidden" id="bsk_pdfm_supported_extension_data_hidden_ID" value="<?php echo esc_attr($supported_extension_data); ?>" />
                                </div>
                                <div style="clear: both"></div>
                            </div><!-- /#bsk_pdf_manager_pdf_title_exclude_extension_container_ID -->
                            <div class="row" id="bsk_pdf_manager_pdf_title_replace_underscroe_container_ID">
                                <div class="left-column">
                                    <label><?php esc_html_e('Replace _ to space'); ?></label>
                                </div>
                                <div class="right-column">
                                    <label><input type="radio" name="bsk_pdfm_replace_underscroe_from_title" value="YES" class="bsk-pdfm-replace-underscore-to-space" disabled /> <?php esc_html_e( 'Yes', 'bskpdfmanager' ); ?></label>
                                    <label style="margin-left:20px;"><input type="radio" name="bsk_pdfm_replace_underscroe_from_title" value="NO" checked="checked" class="bsk-pdfm-replace-underscore-to-space" /> <?php esc_html_e( 'No', 'bskpdfmanager' ); ?></label>
                                </div>
                                <div style="clear: both"></div>
                            </div><!-- /#bsk_pdf_manager_pdf_title_replace_underscroe_container_ID -->
                            <div class="row" id="bsk_pdf_manager_pdf_title_replace_hyphen_container_ID">
                                <div class="left-column">
                                    <label><?php esc_html_e('Replace - to space'); ?></label>
                                </div>
                                <div class="right-column">
                                    <label><input type="radio" name="bsk_pdfm_replace_hyphen_from_title" value="YES" class="bsk-pdfm-replace-hyphen-to-space" disabled /> <?php esc_html_e( 'Yes', 'bskpdfmanager' ); ?></label>
                                    <label style="margin-left:20px;"><input type="radio" name="bsk_pdfm_replace_hyphen_from_title" value="NO" checked="checked" class="bsk-pdfm-replace-hyphen-to-space" /> <?php esc_html_e( 'No', 'bskpdfmanager' ); ?></label>
                                </div>
                                <div style="clear: both"></div>
                            </div><!-- /#bsk_pdf_manager_pdf_title_replace_hyphen_container_ID -->
                        </div><!-- /inside -->
                    </div><!-- /#bsk_pdfm_doc_filename_as_titile_div -->
                    <?php
                    if( $default_enable_permalink ) {
                        $permalink = '';
                        $slug = '';
                        $permalink_edit_display = 'none';
                        if( $pdf_obj_array && isset($pdf_obj_array['slug']) ){
                            $permalink = site_url().'/'.$default_permalink_base.'/'.$pdf_obj_array['slug'].'/';
                            $slug = $pdf_obj_array['slug'];
                            $permalink_edit_display = 'inline-block';
                        }
                    ?>
                    <div id="bsk_pdfm_permalink_div" class="postbox" style="margin-top: 30px;">
                        <div class="postbox-header">
                            <h2 class="hndle"><?php esc_html_e( 'Permalink', 'bskpdfmanager' ); ?></h2>
                        </div>
                        
                        <div class="inside">
                            <div id="bsk_pdfm_doc_edit_slug_box_ID" class="hide-if-no-js" style="margin-top: 10px;">
                                <strong>Permalink:</strong>
                                <span id="bsk_pdfm_doc_edit_slug_permalink_span_ID" style="display: <?php echo $permalink_edit_display; ?>;">
                                    <a href="<?php echo $permalink; ?>" target="_blank"><?php echo site_url().'/'.$default_permalink_base.'/'; ?><span id="bsk_pdfm_doc_editable_slug_ID"><?php echo $slug; ?></span>/</a>
                                </span>
                                <span id="bsk_pdfm_doc_edit_slug_buttons_span_ID" style="display: <?php echo $permalink_edit_display; ?>;">
                                    <button type="button" class="edit-slug button button-small hide-if-no-js" aria-label="<?php esc_html_e( 'Edit permalink' ); ?>"><?php esc_html_e( 'Edit' ) ?></button>
                                    <button type="button" class="save button button-small" style="display: none;">OK</button>
                                    <button type="button" class="cancel button-link" style="display: none;">Cancel</button>
                                </span>
                                <span id="bsk_pdfm_doc_edit_slug_ajax_loader_ID" style="display: none;"><img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" /></span>
                                <input type="hidden" name="bsk_pdfm_doc_hidden_slug" id="bsk_pdfm_doc_hidden_slug_ID" value="<?php echo $slug; ?>" style="display: none;" />
                            </div>
                            <div id="bsk_pdfm_redirect_permalink_to_url_box_ID" class="row">
                                <?php 
                                if ( $default_redirect_permalink_to_url == 'NO' ) { 
                                ?>
                                <p>
                                    <label>Redirect permalink to file URL: </label>
                                    <label style="margin-left:20px;"><input type="radio" name="bsk_pdfm_redirect_permalink_to_url" value="YES" disabled class="bsk-pdfm-redirect-permalink-to-url" /> <?php esc_html_e( 'Yes', 'bskpdfmanager' ); ?></label>
                                    <label style="margin-left:20px;"><input type="radio" name="bsk_pdfm_redirect_permalink_to_url" value="NO" disabled checked class="bsk-pdfm-redirect-permalink-to-url" /> <?php esc_html_e( 'No', 'bskpdfmanager' ); ?></label>
                                </p>
                                <?php 
                                } 
                                ?>
                                <div style="clear: both;"></div>
                            </div>
                        </div>
                        <p id="bsk_pdfm_pdf_slug_error_ID" class="bsk-pdfm-error" style="display: none;">error message</p>
                        <div class="inside">
                            <div id="bsk-pdfm-doc-edit-slug-box" class="hide-if-no-js">
                            </div>
                        </div>
                    </div>
                    <?php
                    }
                    ?>
                    <?php
                    $current_upload_path = BSKPDFManager::$_upload_path;
                    if( $organise_directory_strucutre_with_year_month ){
                        $current_upload_path .= wp_date('Y/m/');
                    }
                    $current_upload_path_to_show = str_replace(BSKPDFManager::$_upload_root_path, '', $current_upload_path);
        
                    $upload_from_computer_checked = $is_by_media_uploader ? '' : 'checked';
                    $upload_from_media_library_checked = $is_by_media_uploader ? 'checked' : '';
                    $old_upload_from = $is_by_media_uploader ? 'media_library' : 'computer';
                    $media_upload_container_display = $is_by_media_uploader ? 'block' : 'none';
                    $upload_computer_row_display = $is_by_media_uploader ? 'none' : 'block';
                    ?>
                    <div id="bsk_pdfm_doc_file_upload_div" class="postbox" style="margin-top: 30px;">
                        <div class="postbox-header">
                            <h2 class="hndle"><?php esc_html_e( 'File', 'bskpdfmanager' ); ?></h2>
                        </div>
                        <div class="inside">
                            <?php if( $pdf_id > 0 && $file_url ){ ?>
                            <div class="row">
                                <div class="left-column">
                                    <label><?php esc_html_e( 'File Location', 'bskpdfmanager' ); ?>:</label>
                                </div>
                                <div class="right-column">
                                    <?php
                                    $file_name_location = $pdf_obj_array['file_name'];
                                    if( $is_by_media_uploader ){
                                        $file_name_location = str_replace( site_url().'/', '', $file_url );
                                    }
                                    $file_url_view = $file_url;
                                    if( $default_enable_permalink ){
                                        $file_url_view = site_url().'/bsk-pdf-manager/'.$pdf_obj_array['slug'].'/';
                                    }
                                    ?>
                                    <a href="<?php echo esc_url($file_url_view); ?>" target="_blank" id="bsk_pdfm_file_location_url_ID"><?php echo esc_html($file_name_location); ?></a>
                                </div>
                                <div style="clear: both"></div>
                            </div><!-- /row -->
                            <div class="row">
                                <div class="left-column">
                                    <label><?php _e( 'File size:' ); ?></label>
                                </div>
                                <div class="right-column">
                                    <?php
                                    $file_size = $pdf_obj_array['size'];
                                    ?>
                                    <strong><?php echo size_format( $file_size ); ?></strong>
                                </div>
                                <div style="clear: both;"></div>
                            </div><!-- /row -->
                            <?php } ?>
                            <div class="row">
                                <div class="left-column">
                                    <label><?php esc_html_e( 'Upload from', 'bskpdfmanager' ); ?>:</label>
                                </div>
                                <div class="right-column">
                                    <label for="bsk_pdfm_upload_from_computer_ID" class="upload-way-radio">
                                        <input type="radio" name="bsk_pdfm_upload_from" value="computer" id="bsk_pdfm_upload_from_computer_ID" class="bsk-pdfm-upload-from-radio" <?php echo $upload_from_computer_checked; ?> /> <?php esc_html_e( 'Your Computer', 'bskpdfmanager' ); ?>
                                    </label>
                                    <label for="bsk_pdfm_upload_from_media_library_ID" class="upload-way-radio">
                                        <input type="radio" name="bsk_pdfm_upload_from" value="media_library" id="bsk_pdfm_upload_from_media_library_ID" class="bsk-pdfm-upload-from-radio" <?php echo $upload_from_media_library_checked; ?> /> <?php esc_html_e( 'Media Library', 'bskpdfmanager' ); ?>
                                    </label>
                                </div>
                                <input type="hidden" name="bsk_pdfm_old_upload_from" value="<?php echo $old_upload_from; ?>" />
                                <div style="clear: both"></div>
                            </div><!-- /row -->
                            <div class="row" id="bsk_pdfm_upload_from_media_library_row_ID" style="margin-top:20px;display:<?php echo $media_upload_container_display; ?>;">
                                <div class="left-column">
                                    <label></label>
                                </div>
                                <div class="right-column">
                                    <div id="bsk_pdfm_wordpress_uploader_ID">
                                        <?php
                                            $attachment_id = $is_by_media_uploader ? $pdf_obj_array['by_media_uploader'] : 0;
                                            $attachment_extension = $pdf_obj_array ? $pdf_obj_array['media_ext'] : '';
                                            $remove_container_display = "none";
                                            $thumbnail_html = '';
                                            $class = '" class="button-secondary"';
                                            if( $attachment_id && get_post( $attachment_id ) ){
                                                $thumbnail_html = wp_get_attachment_url( $attachment_id );
                                                $remove_container_display = "inline-block";
                                                $class = '';
                                            }
                                        ?>
                                        <p class="hide-if-no-js">
                                            <a title="Upload Document" href="javascript:void(0);" id="bsk_pdf_manager_upload_pdf_anchor_ID" <?php echo $class; ?>>
                                                <?php echo $thumbnail_html ? $thumbnail_html : esc_html__( 'Select from Media Library', 'bskpdfmanager' );?>
                                            </a>
                                        </p>
                                        <p class="hide-if-no-js">
                                            <a href="javascript:void(0);" id="bsk_pdf_manager_remove_pdf_anchor_ID" style="display:<?php echo $remove_container_display ?>" class="button-secondary"><?php esc_html_e( 'Remove the selected', 'bskpdfmanager' ); ?></a>
                                        </p>
                                        <p class="bsk-pdfm-error" id="bsk_pdfm_upload_from_media_library_error_container_ID"></p>
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
                                    </div><!-- /#bsk_pdfm_wordpress_uploader_ID -->
                                    <input type="hidden" name="bsk_pdfm_old_upload_attachment_id" value="<?php echo esc_attr($attachment_id); ?>" />
                                    <input type="hidden" name="bsk_pdf_upload_attachment_id" id="bsk_pdf_upload_attachment_id_ID" value="<?php echo esc_attr($attachment_id); ?>" />
                                    <input type="hidden" name="bsk_pdf_upload_attachment_extension" id="bsk_pdf_upload_attachment_extension_ID" value="<?php echo esc_attr($attachment_extension); ?>" />
                                </div><!-- /right-column -->
                                <div style="clear: both"></div>
                            </div><!-- /row -->
                            <?php
                            $u_bytes = BSKPDFM_Common_Backend::bsk_pdf_manager_pdf_convert_hr_to_bytes( ini_get( 'upload_max_filesize' ) );
                            $p_bytes = BSKPDFM_Common_Backend::bsk_pdf_manager_pdf_convert_hr_to_bytes( ini_get( 'post_max_size' ) );
                            $maximum_uploaded_numeric = floor(min($u_bytes, $p_bytes) / 1024);
                            $maximum_uploaded_numeric_str = floor(min($u_bytes, $p_bytes) / 1024).' K bytes.';
                            if ($maximum_uploaded_numeric > 1024){
                                $maximum_uploaded_numeric_str = floor( $maximum_uploaded_numeric / 1024).' M bytes.';
                            }
                            ?>
                            <div class="row" id="bsk_pdfm_upload_from_computer_row_ID" style="margin-top: 20px;display: <?php echo $upload_computer_row_display;?>;">
                                <div class="left-column">
                                    <label></label>
                                </div>
                                <div class="right-column">
                                    <div id="bsk_pdfm_upload_from_computer_div_ID">
                                        <input type="file" name="bsk_pdf_file" id="bsk_pdf_file_id" value="<?php esc_html_e( 'Browse', 'bskpdfmanager' ); ?>" />
                                    </div>
                                    <p style="font-style:italic;"><?php printf( esc_html__( 'Maximum file size: %s To change this please modify your hosting configuration in php.ini or .htaccess file.', 'bskpdfmanager' ), $maximum_uploaded_numeric_str ); ?></p>
                                    <p style="font-style:italic;"><?php printf( esc_html__( 'Only %s allowed', 'bskpdfmanager' ), '<strong>'.implode( ', ', $supported_extension ).'</strong>' ); ?></p>
                                </div><!-- /right-column -->
                                <div style="clear: both"></div>
                                <?php
                                $old_upload_from_computer_file = '';
                                if( $pdf_obj_array ){
                                    $old_upload_from_computer_file = $pdf_obj_array['file_name'];
                                }
                                ?>
                                <input type="hidden" name="bsk_pdfm_old_upload_from_computer_file_name" value="<?php echo esc_attr($old_upload_from_computer_file); ?>" />
                            </div><!-- /row -->
                            <div class="row" id="bsk_pdfm_upload_to_row_ID">
                                <div class="left-column">
                                    <label><?php esc_html_e( 'Upload to', 'bskpdfmanager' ); ?>:</label>
                                </div>
                                <div class="right-column">
                                    <span style="font-weight: bold;"><?php echo esc_html($current_upload_path_to_show); ?></span>
                                </div><!-- /right-column -->
                                <div style="clear: both"></div>
                            </div><!-- /row -->
                        </div><!-- /inside -->
                    </div><!-- /#bsk_pdfm_doc_file_upload_div -->
                    <div id="bsk_pdfm_doc_date_div" class="postbox" style="margin-top: 30px;">
                        <div class="postbox-header">
                            <h2 class="hndle"><?php esc_html_e( 'Date&amp;Time', 'bskpdfmanager' ); ?></h2>
                        </div>
                        <div class="inside">
                            <div class="row" id="bsk_pdfm_last_date_row_ID">
                                <div class="left-column">
                                    <label><?php esc_html_e( 'Date&amp;Time', 'bskpdfmanager' ); ?>:</label>
                                </div>
                                <div class="right-column" id="bsk_pdfm_date_time_section_ID">
                                    <div>
                                        <input type="text" name="pdf_date" value="<?php echo esc_attr($pdf_date) ?>" class="bsk-date bsk-pdfm-date-time-date" autocomplete="off" />
                                        <span>@</span>
                                        <input type="number" name="pdf_date_hour" class="bsk-pdfm-date-time-hour" value="<?php echo esc_attr($pdf_time_h); ?>" min="0" max="23" step="1" disabled />
                                        <span>:</span>
                                        <input type="number" name="pdf_date_minute" class="bsk-pdfm-date-time-minute" value="<?php echo esc_attr($pdf_time_m); ?>" min="0" max="59" step="1"  disabled />
                                        <span>:</span>
                                        <input type="number" name="pdf_date_second" class="bsk-pdfm-date-time-second" value="<?php echo esc_attr($pdf_time_s); ?>" min="0" max="59" step="1"  disabled />
                                    </div>
                                    <p>
                                        <span id="bsk_pdfm_current_server_datetime_section_ID" style="display: inline-block;">
                                            <label style="display: inline-block; width: auto;">
                                                <input type="checkbox" id="pdf_date_use_current_server_datetime_chk_ID" value="Yes" /> <?php esc_html_e( 'Use server date&time', 'bskpdfmanager' ); ?>
                                            </label>
                                            <span id="bsk_pdfm_current_server_datetime_text_ID"><?php echo esc_html($server_date.' '.$server_time_h.':'.$server_time_m.':'.$server_time_s); ?></span>
                                        </span>
                                    </p>
                                    <p>
                                        <span id="bsk_pdfm_lastmodified_section_ID" style="display: inline-block;">
                                            <label style="display: inline-block; width: auto;">
                                                <input type="checkbox" id="pdf_date_use_file_last_modify_chk_ID" value="Yes" disabled /> <?php esc_html_e( 'Use file last modified date&time', 'bskpdfmanager' ); ?> 
                                            </label>
                                            <span id="bsk_pdfm_lastmodified_text_ID"></span>
                                        </span>
                                    </p>
                                    <p>
                                        <span id="bsk_pdfm_parsed_from_filename_section_ID" style="display: inline-block;">
                                            <label style="display: inline-block; width: auto;">
                                                <input type="checkbox" id="pdf_date_use_parsed_from_filename_chk_ID" value="Yes" disabled /> <?php esc_html_e( 'Use parsed date from filename', 'bskpdfmanager' ); ?>
                                            </label>
                                            <span id="bsk_pdfm_parsed_from_filename_text_ID"></span>
                                        </span>
                                    </p>
                                </div><!-- /right-column -->
                                <div style="clear: both"></div>
                            </div><!-- /row -->
                            <?php
                            $utc_timezone = new DateTimeZone( 'UTC' );
                            $pdf_publish_date = '';
                            if( $pdf_obj_array && isset($pdf_obj_array['publish_date']) && $pdf_obj_array['publish_date'] ){
                                $date_time_full = wp_date( 'Y-m-d H:i:s', strtotime($pdf_obj_array['publish_date']), $utc_timezone );
                                $pdf_publish_date = substr( $date_time_full, 0, 10 );
                            }
                            $pdf_expiry_date = '';
                            if( $pdf_obj_array && isset($pdf_obj_array['expiry_date']) && $pdf_obj_array['expiry_date'] ){
                                $date_time_full = wp_date( 'Y-m-d H:i:s', strtotime($pdf_obj_array['expiry_date']), $utc_timezone );
                                $pdf_expiry_date = substr( $date_time_full, 0, 10 );
                            }
                            ?>
                            <div class="row" id="bsk_pdfm_publsih_date_row_ID">
                                <div class="left-column">
                                    <label><?php esc_html_e( 'Publish Date&amp;Time', 'bskpdfmanager' ); ?>:</label>
                                </div>
                                <div class="right-column" id="bsk_pdfm_publish_date_time_section_ID">
                                    <span class="bsk-pdf-field">
                                        <input type="text" name="pdf_publish_date" value="<?php echo esc_attr($pdf_publish_date) ?>" class="bsk-date bsk-pdfm-date-time-date" autocomplete="off" disabled />
                                        <span>@</span>
                                        <input type="number" name="pdf_publish_date_hour" class="bsk-pdfm-date-time-hour" value="" min="0" max="23" step="1" disabled />
                                        <span>:</span>
                                        <input type="number" name="pdf_publish_date_minute" class="bsk-pdfm-date-time-minute" value="" min="0" max="59" step="1" disabled />
                                        <span>:</span>
                                        <input type="number" name="pdf_publish_date_second" class="bsk-pdfm-date-time-second" value="" min="0" max="59" step="1" disabled />
                                        <span style="display:inline-block; font-style:italic; margin-left: 20px;"><?php esc_html_e( 'Only available ', 'bskpdfmanager' ); ?><strong><?php esc_html_e( 'same or after', 'bskpdfmanager' ); ?></strong> <?php esc_html_e( 'this date, leave blank for available always', 'bskpdfmanager' ); ?></span>
                                    </span>
                                </div><!-- /right-column -->
                                <div style="clear: both"></div>
                            </div><!-- /row -->
                            <div class="row" id="bsk_pdfm_expiry_date_row_ID">
                                <div class="left-column">
                                    <label><?php esc_html_e( 'Expiry Date&amp;Time', 'bskpdfmanager' ); ?>:</label>
                                </div>
                                <div class="right-column" id="bsk_pdfm_expiry_date_time_section_ID">
                                    <span class="bsk-pdf-field">
                                        <input type="text" name="pdf_expiry_date" value="<?php echo esc_attr($pdf_expiry_date) ?>" class="bsk-date bsk-pdfm-date-time-date" autocomplete="off" disabled />
                                        <span>@</span>
                                        <input type="number" name="pdf_expiry_date_hour" class="bsk-pdfm-date-time-hour" value="" min="0" max="23" step="1" disabled />
                                        <span>:</span>
                                        <input type="number" name="pdf_expiry_date_minute" class="bsk-pdfm-date-time-minute" value="" min="0" max="59" step="1" disabled />
                                        <span>:</span>
                                        <input type="number" name="pdf_expiry_date_second" class="bsk-pdfm-date-time-second" value="" min="0" max="59" step="1" disabled />
                                        <span style="display:inline-block; font-style:italic; margin-left: 20px;"><?php esc_html_e( 'Only available ', 'bskpdfmanager' ); ?><strong><?php esc_html_e( 'before', 'bskpdfmanager' ); ?></strong> <?php esc_html_e( 'this date, leave blank for available always', 'bskpdfmanager' ); ?></span>
                                    </span>
                                </div><!-- /right-column -->
                                <div style="clear: both"></div>
                            </div><!-- /row -->
                        </div>
                    </div>
                    <?php if ( $default_enable_featured_image ) { ?>
                    <div id="bsk_pdfm_doc_featured_image_div" class="postbox" style="margin-top: 30px;">
                        <div class="postbox-header">
                            <h2 class="hndle"><?php esc_html_e( 'Featured Image', 'bskpdfmanager' ); ?></h2>
                        </div>
                        <div class="inside">
                            <div class="row" id="bsk_pdfm_last_date_row_ID">
                                <div class="left-column">
                                    <label><?php esc_html_e( 'Featured Image', 'bskpdfmanager' ); ?>:</label>
                                </div>
                                <div class="right-column" id="bsk_pdfm_date_time_section_ID">
                                    <div id="bsk_pdfm_featured_image_uploader_ID">
                                        <?php
                                            $remove_container_display = "none";
                                            $thumbnail_html = '';
                                            $thumbnail_id = isset($pdf_obj_array['thumbnail_id']) && $pdf_obj_array['thumbnail_id'] ? $pdf_obj_array['thumbnail_id'] : 0;
                                            if( $thumbnail_id && get_post( $thumbnail_id ) ){
                                                $thumbnail_html = wp_get_attachment_image( $thumbnail_id, 'thumbnail' );
                                                $thumbnail_html = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $thumbnail_html);
                                                $remove_container_display = "inline-block";
                                            }
                                        ?>
                                        <div class="hide-if-no-js">
                                            <a title="Set featured image" href="javascript:void(0);" id="bsk_pdf_manager_set_featured_image_anchor_ID">
                                                <?php echo $thumbnail_html ? $thumbnail_html : ' Set featured image'; ?>
                                            </a>
                                            <span id="bsk_pdf_manager_set_featured_image_ajax_loader_ID" style="display:none;"><img src="<?php echo esc_url(BSKPDFManager::$_ajax_loader_img_url); ?>" /></span>
                                        </div>
                                        <div id="bsk_pdfm_click_featured_iamge_to_edit_div" style="display: none;"><p>Click the image to edit or update</p></div>
                                        <div class="hide-if-no-js" id="bsk_pdfm_remove_featured_iamge_div" style="display:<?php echo $remove_container_display ?>">
                                            <p>
                                                <a href="javascript:void(0);" id="bsk_pdf_manager_remove_featured_image_anchor_ID" ><?php esc_html_e( 'Remove featured image', 'bskpdfmanager' ); ?></a>
                                            <span id="bsk_pdf_manager_remove_featured_image_ajax_loader_ID" style="display:none;"><img src="<?php echo esc_url(BSKPDFManager::$_ajax_loader_img_url); ?>" /></span>
                                            </p>
                                        </div>
                                        <input type="hidden" name="bsk_pdf_manager_thumbnail_id" id="bsk_pdf_manager_thumbnail_id_ID" value="<?php echo esc_attr($thumbnail_id); ?>" />
                                        <input type="hidden" name="bsk_pdfm_old_thumbnail_id" value="<?php echo esc_attr($thumbnail_id); ?>" />
                                    </div><!-- /#bsk_pdfm_featured_image_uploader_ID -->
                                    <p>
                                        <label>
                                            <input type="checkbox" name="bsk_pdfm_generate_thumbnail_chk" id="bsk_pdfm_generate_thumbnail_chk_ID" value="YES" /> <?php esc_html_e( 'Generate featured image from PDF document.', 'bskpdfmanager' ); ?><span style="font-style:italic; margin-left: 20px;"><?php esc_html_e( 'Only work for PDF document', 'bskpdfmanager' ); ?></span>
                                        </label>
                                    </p>
                                    <div id="bsk_pdfm_featured_image_generate_settings_ID" style="display: none;">
                                        <?php
                                        $load_imagick_return = BSKPDFM_Dashboard_PDF_Image_Editor::bsk_pdfm_check_imagick();
                                        if ( is_wp_error( $load_imagick_return ) ) {
                                            echo '<div class="notice notice-error inline">'.$load_imagick_return->get_error_message().'</div>';
                                        } else {
                                        ?>
                                        <div>
                                            <p>
                                                <?php 
                                                $text = esc_html__( 'Use page number %s of the PDF to generate thumbnail. If the selected page number exceeds the maximum page number, the first page will be used by default.', 'bskpdfmanager' );
                                                $page_number_input = '<input type="number" name="bsk_pdfm_generate_thumbnail_page_number" value="1" min="1" step="1" style="width: 50px;" />';
                                                printf( $text, $page_number_input ); 
                                                ?>
                                            </p>
                                            <p>
                                                <label>
                                                    <input type="checkbox" name="bsk_pdfm_generate_thumbnail_rmv_old_from_media_library" value="YES" /> Delete the old featured image from Media Library.
                                                </label>
                                            </p>
                                        </div>
                                        <?php
                                        }
                                        ?>
                                        <div class="bsk-pdfm-tips-box" style="text-align: left;">
                                            <p>This feature requires a <span style="font-weight: bold;">CREATOR</span>( or above ) license for <a href="<?php echo esc_url(BSKPDFManager::$url_to_upgrade); ?>" target="_blank">Pro version</a>. </p>
                                        </div>
                                    </div>
                                </div><!-- /right-column -->
                                <div style="clear: both"></div>
                            </div><!-- /row -->
                        </div>
                    </div><!-- /#bsk_pdfm_doc_featured_image_div -->
                    <?php } ?>
                    <?php
                        $description = '';
                        if( $pdf_obj_array && isset($pdf_obj_array['description']) ){
                            $description = $pdf_obj_array['description'];
                        }
                    ?>
                    <div id="bsk_pdfm_doc_desc_div" class="postbox" style="margin-top: 30px;">
                        <div class="postbox-header">
                            <h2 class="hndle"><?php esc_html_e( 'Description', 'bskpdfmanager' ); ?></h2>
                        </div>
                        <div class="inside">
                        <?php 
                            $settings = array( 
                                                'media_buttons' => false,
                                                'editor_height' => 150,
                                                'wpautop' => false,
                                             );
                            $description = '<p>'.esc_html__( 'Description only support in Pro version', 'bskpdfmanager' ).'</p>';
                            $description .= '<p><a style="color: #ff5b00;" href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/how-to-upgrade-to-pro-version/" target="_blank" rel="noopener">'.esc_html__( 'Upgrade to Pro', 'bskpdfmanager' ).'</a></p>';
                            wp_editor( $description, 'pdf_description', $settings );
                        ?>
                        </div>
                    </div>
                </div><!-- /bsk_pdfm_doc_body_content -->
                <div id="bsk_pdfm_doc_box_right" class="postbox-container">
                    <div id="side-sortables" class="meta-box-sortables ui-sortable" style="">
                        <?php
                        $trash_url = '';
                        $state_label = 'Published';
                        $state = 'published';
                        $is_draft = false;
                        $is_file_missed = false;
                        if( $pdf_obj_array && is_array( $pdf_obj_array ) && isset( $pdf_obj_array['id'] ) && $pdf_obj_array['id'] ){
                            $is_draft = false;
                            if( $pdf_obj_array['by_media_uploader'] > 1 ){
                                $file_url = wp_get_attachment_url( $pdf_obj_array['by_media_uploader'] );
                                if( $file_url ){
                                    //
                                }else{
                                    $is_file_missed = true;
                                }
                            }else if( $pdf_obj_array['file_name'] ){
                                if( file_exists( BSKPDFManager::$_upload_root_path.$pdf_obj_array['file_name'] ) ){
                                    //
                                }else{
                                    $is_file_missed = true;
                                }
                            }else{
                                //draft
                                $is_draft = true;
                            }

                            if ( ! $is_draft ) {
                                if ( $pdf_obj_array['draft'] ) {
                                    $is_draft = true;
                                }
                            }
                            
                            if( $is_draft ){
                                $state_label = __( 'Draft', 'bskpdfmanager' );     
                                $state = 'draft';
                            }else{
                                $state_label = __( 'Published', 'bskpdfmanager' );     
                                $state = 'published';
                            }
                            if ( $is_file_missed ) {
                                $state_label = '<span class="bsk-pdf-documentation-attr">' . __( 'File Missed', 'bskpdfmanager' ) . '</span>';     
                                $state = 'published';
                            }
                            if( !empty( $pdf_obj_array['publish_date'] ) && $pdf_obj_array['publish_date'] > wp_date( 'Y-m-d H:i:s' ) ){
                                $state_label = __( 'Scheduled' );
                                $state = 'scheduled';
                            }
                            if( !empty( $pdf_obj_array['expiry_date'] ) && $pdf_obj_array['expiry_date'] <= wp_date( 'Y-m-d H:i:s' ) ){
                                $state_label = __( 'Expired', 'bskpdfmanager' );
                                $state = 'expired';
                            }
			                
                            
                            $pdfs_page_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['base'] );
                            $trash_url = add_query_arg( 'action', 'trash', $pdfs_page_url );
                            $trash_url = add_query_arg( 'pdfid', $pdf_obj_array['id'], $trash_url );
                        }
        
                        
                        ?>
                        <div id="submitdiv" class="postbox ">
                            <div class="postbox-header">
                                <h2 class="hndle">Publish</h2>
                            </div>
                            <div class="inside">
                                <div class="submitbox" id="submitpost">
                                    <div id="minor-publishing">
                                        <div id="misc-publishing-actions">
                                            <div class="misc-pub-section misc-pub-post-status">
                                                <?php esc_html_e( 'Status', 'bskpdfmanager' ); ?>: <span id="post_status_display_span_ID"><?php echo $state_label; ?></span>&nbsp;&nbsp;<a href="javascript:void(0);" id="bsk_pdfm_post_status_edit_anchor">Edit</a>
                                                <div id="bsk_pdfm_post_status_edit_container" style="display: none;">
                                                    <p>
                                                        <select name="bsk_pdfm_post_status_select" id="bsk_pdfm_post_status_select_ID">
                                                            <?php
                                                            $published_selected = '';
                                                            if ( $state == 'published' ) {
                                                                $published_selected = ' selected';
                                                            }
                                                            echo '<option value="published"' . $published_selected . '>Published</option>';
                                                            $draft_selected = '';
                                                            if ( $state == 'draft' ) {
                                                                $draft_selected = ' selected';
                                                            }
                                                            ?>
                                                            <option value="draft"<?php echo $draft_selected; ?>>Draft</option>
                                                        </select>
                                                        <span>&nbsp;&nbsp;<a href="javascript:void(0);" id="bsk_pdfm_post_status_edit_OK_ID">OK</a></span>
                                                        <span>&nbsp;&nbsp;<a href="javascript:void(0);" id="bsk_pdfm_post_status_edit_Cancel_ID">Cancel</a></span>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="misc-pub-section misc-pub-post-visibility">
                                                <a href="https://bannersky.com/faq/bsk-pdf-manager/about-pdf-document-status/" target="_blank">About status</a>
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                    <div id="major-publishing-actions">
                                        <?php if( $trash_url ) { ?>
                                        <div id="delete-action">
                                            <a class="submitdelete deletion" href="<?php echo wp_nonce_url( $trash_url, 'trash-pdf_' . $pdf_obj_array['id'] ); ?>">Move to Trash</a>
                                        </div>
                                        <?php } ?>
                                        <div id="publishing-action">
                                            <span class="spinner"></span>
                                            <input type="button" name="publish" id="bsk_pdfm_doc_save_btn_ID" class="button button-primary button-large" value="<?php esc_attr_e( 'Save', 'bskpdfmanager' ); ?>" />
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
	                        </div>
                        </div>
                        <div id="categorydiv" class="postbox ">
                            <div class="postbox-header">
                                <h2 class="hndle">Categories</h2>
                            </div>
                            <div class="inside">
                                <div id="taxonomy-category" class="categorydiv">
                                    <ul class="category-tabs">
                                        <li class="tabs"><a href="#category-all">All Categories</a></li>
                                    </ul>
                                    <div id="category-all" class="tabs-panel" style="">
                                        <?php
                                        if( $categories_count ){
                                            echo BSKPDFM_Common_Backend::get_category_hierarchy_checkbox( 'bsk_pdfm_doc_categories[]', 'bsk-pdfm-doc-category-checkbox', $pdf_categories_array, 'CAT', false );
                                        }else{
                                            $create_category_url = add_query_arg( 'page', 
                                                                                    BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['category'], 
                                                                                    admin_url('admin.php') );
                                            $create_category_url = add_query_arg( 'view', 'addnew', $create_category_url );
                                            $create_category_str = sprintf( __( 'Please %s first', 'bskpdfmanager' ), '<a href="'.esc_url($create_category_url).'">'.__('create category', 'bskpdfmanager' ).'</a>' );
                                            
                                            echo '<p>'.$create_category_str.'</p>';
                                        }
                                        ?>
                                    </div>
                                </div><!-- /#taxonomy-category -->
                                <div class="row">
                                    <p id="bsk_pdfm_edit_document_category_error_container_ID" class="bsk-pdfm-error" style="display: none;"></p>
                                </div>
                                <input type="hidden" name="bsk_pdf_edit_cat_ids" id="bsk_pdf_edit_cat_ids_ID" value="<?php echo esc_attr( implode( ',', $pdf_categories_array) ); ?>" />
                            </div><!-- /inside -->
                        </div><!-- /#categorydiv -->
                        <div id="tagdiv" class="postbox ">
                            <div class="postbox-header">
                                <h2 class="hndle">Tags</h2>
                            </div>
                            <div class="inside">
                                <div id="taxonomy-category" class="categorydiv">
                                    <ul class="category-tabs">
                                        <li class="tabs"><a href="#category-all">All Tags</a></li>
                                    </ul>
                                    <div id="category-all" class="tabs-panel" style="">
                                        <?php
                                        if( $tags_count ){
                                            echo BSKPDFM_Common_Backend::get_category_hierarchy_checkbox( 'bsk_pdfm_doc_tags[]', 'bsk-pdfm-doc-tag-checkbox', $pdf_tags_array, 'TAG', false );
                                        }else{
                                            $create_category_url = add_query_arg( 'page', 
                                                                                    BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['tag'], 
                                                                                    admin_url('admin.php') );
                                            $create_category_url = add_query_arg( 'view', 'addnew', $create_category_url );
                                            $create_tag_str = sprintf( __( 'Please %s first', 'bskpdfmanager' ), '<a href="'.esc_url($create_category_url).'">'.__('create tag', 'bskpdfmanager' ).'</a>' );
                                            
                                            echo '<p>'.$create_tag_str.'</p>';
                                        }
                                        ?>
                                    </div>
                                </div><!-- /#taxonomy-category -->
                                <div class="row">
                                    <p id="bsk_pdfm_edit_document_category_error_container_ID" class="bsk-pdfm-error" style="display: none;"></p>
                                </div>
                                <input type="hidden" name="bsk_pdf_edit_tag_ids" id="bsk_pdf_edit_tag_ids_ID" value="<?php echo esc_attr( implode( ',', $pdf_tags_array ) ); ?>" />
                            </div><!-- /inside -->
                        </div><!-- /#categorydiv -->
                    </div>
                </div>
                <?php if( 0 ) : ?>
                <div id="bsk_pdfm_doc_box_bottom" class="postbox-container">
                    <div id="bsk_pdfm_doc_publish_date_time_div" class="postbox ">
                        <div class="postbox-header"></div>
                        <div class="inside"></div>
                    </div>
                </div><!-- /#bsk_pdfm_doc_box_bottom -->
                <?php endif; ?>
                <?php
                $list_cat_id = isset( $_REQUEST['cat'] ) ? intval( sanitize_text_field($_REQUEST['cat']) ) : 0;
                ?>
                <p>
                    <input type="hidden" name="bsk_pdf_manager_action" value="pdf_save" />
                    <input type="hidden" name="bsk_pdf_manager_pdf_id" id="bsk_pdf_manager_pdf_id_ID" value="<?php echo esc_attr($pdf_id); ?>" />
                    <input type="hidden" name="bsk_pdf_manager_list_cat_id" value="<?php echo esc_attr($list_cat_id); ?>" />
                    <?php 
                        echo wp_nonce_field( plugin_basename( __FILE__ ), 'bsk_pdf_manager_pdf_save_oper_nonce', true, false );
                        $ajax_nonce = wp_create_nonce( 'bsk_pdf_manager_pdf_page_ajax_oper_nonce' );
                    ?>
                    <input type="hidden" id="bsk_pdf_manager_pdf_page_ajax_oper_nonce_ID" value="<?php echo $ajax_nonce; ?>" />
                </p>
            </div><!-- /bsk_pdfm_doc_body -->
            <br class="clear">
        </div>
        <?php
	}
	
	function bsk_pdf_manager_pdf_save_fun( $data ){
        
        require_once( 'pdf-image-editor.php' );
        
		global $wpdb;
		//check nonce field
		if (!wp_verify_nonce(sanitize_text_field($data['bsk_pdf_manager_pdf_save_oper_nonce']), plugin_basename( __FILE__ ) )) {
			wp_die( esc_html__( 'Security issue, please refresh the page to test again!', 'bskpdfmanager' ) );
		}
        if (!BSKPDFM_Common_Backend::bsk_pdfm_current_user_can()) {
            wp_die( esc_html__( 'You are now allowed to do this', 'bskpdfmanager' ) );
        }

        if (!isset($data['bsk_pdf_edit_cat_ids']) ||
			trim(sanitize_text_field($data['bsk_pdf_edit_cat_ids']) == "")) {
				
			wp_die( esc_html__( 'No category found', 'bskpdfmanager' ) );
		}
        $bsk_pdf_manager_pdf_edit_categories = explode(',', sanitize_text_field( $data['bsk_pdf_edit_cat_ids'] ));
        if( !is_array($bsk_pdf_manager_pdf_edit_categories) || 
            count($bsk_pdf_manager_pdf_edit_categories) < 1 ){
            
            wp_die( esc_html__( 'No valid category found', 'bskpdfmanager' ) );
        }
        foreach( $bsk_pdf_manager_pdf_edit_categories as $key => $cat_id ){
            $bsk_pdf_manager_pdf_edit_categories[$key] = intval($cat_id);
        }
        
        $bsk_pdf_manager_pdf_edit_tags = explode( ',', sanitize_text_field( $data['bsk_pdf_edit_tag_ids'] ) );
        if( is_array( $bsk_pdf_manager_pdf_edit_tags ) && count( $bsk_pdf_manager_pdf_edit_tags ) ){
            foreach( $bsk_pdf_manager_pdf_edit_tags as $key => $tag_id ){
                $bsk_pdf_manager_pdf_edit_tags[$key] = intval($tag_id);
            }
        }
        
        $pdf_id = intval( sanitize_text_field( $data['bsk_pdf_manager_pdf_id'] ) );
		$pdf_data = array();
        $pdf_data_format = array();
        //titile
		$pdf_data['title'] = sanitize_text_field( $data['bsk_pdf_manager_pdf_titile'] );
        if( $pdf_data['title'] == '' ){
            wp_die( esc_html__( 'No title found', 'bskpdfmanager' ) );
        }
        $pdf_data_format['title'] = '%s';
        //category
        $pdf_data['cat_id'] = '999999';
        $pdf_data_format['cat_id'] = '%s';
        
        //thmbnail
        $pdf_data['thumbnail_id'] = 0;
        $pdf_data_format['thumbnail_id'] = '%d';
        if ( isset( $data['bsk_pdf_manager_thumbnail_id'] ) ){
			$pdf_data['thumbnail_id'] = intval( sanitize_text_field($data['bsk_pdf_manager_thumbnail_id']) );
		}
        
        //date
		$pdf_data['last_date'] = wp_date( 'Y-m-d H:i:s' );
        if( isset($data['pdf_date']) && trim(sanitize_text_field($data['pdf_date'])) ){
            $pdf_data['last_date'] = trim(sanitize_text_field($data['pdf_date'])).' ';
            $pdf_data['last_date'] .= '00:00:00';
        }
        $pdf_data_format['last_date'] = '%s';
		
        $pdf_data['description'] = '';
		$pdf_data_format['description'] = '%s';
        
        $pdf_publish_date = NULL;
        $pdf_expiry_date = NULL;
		$pdf_data['publish_date'] = $pdf_publish_date;
		$pdf_data['expiry_date'] = $pdf_expiry_date;
        
        $pdf_data_format['publish_date'] = '%s';
        $pdf_data_format['expiry_date'] = '%s';
		
        foreach( $pdf_data as $key => $element ){
            $pdf_data[$key] = wp_unslash( $element );
        }
        
        //slug
        $slug = '';
        if( isset( $data['bsk_pdfm_doc_hidden_slug'] ) && 
            sanitize_text_field($data['bsk_pdfm_doc_hidden_slug']) ){
            //do nothing
            $slug = sanitize_text_field($data['bsk_pdfm_doc_hidden_slug']);
		}else{
            $all_supported_extensions = BSKPDFM_Common_Backend::get_supported_extension_with_mime_type();
            $ext_array = explode( '.', $pdf_data['title'] );
            if( count($ext_array) >= 2 ){
                //check if extention includes
                if( array_key_exists( $ext_array[count($ext_array) - 1], $all_supported_extensions ) ){
                    unset( $ext_array[count($ext_array) - 1] );
                }
            }
            $slug = implode( '.', $ext_array );
        }
        $slug = BSKPDFM_Permalink_AccessCtrl::get_document_slug( $slug, $pdf_id );
        $pdf_data['slug'] = $slug;
        $pdf_data_format['slug'] = '%s';
        
        //save to database first
        $new_pdf_id = 0;
        if( $pdf_id > 0 ){
            unset( $pdf_data['id'] ); //for update, dont't chagne id
			$return = $wpdb->update( $wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name, $pdf_data, array( 'id' => $pdf_id ), $pdf_data_format );
            if( $return === false ){
                $message_id = 23;
				
				$redirect_to = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['base'].'&view=edit&pdfid='.$pdf_id.'&message='.$message_id );
				wp_redirect( $redirect_to );
				exit;
            }
        }else{
            //insert
			$return = $wpdb->insert( $wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name, $pdf_data, $pdf_data_format );
			if ( $return === false ){
				$message_id = 21;
				
				$redirect_to = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['base'] .'&message='.$message_id );
				wp_redirect( $redirect_to );
				exit;
			}
            $new_pdf_id = $wpdb->insert_id;
        }
        //
        // update categories and tags
        //
        $pdf_id = $pdf_id > 0 ? $pdf_id : $new_pdf_id;
        //update pdf's category
        $this->bsk_pdf_manager_update_category( $pdf_id, $bsk_pdf_manager_pdf_edit_categories );
        //update pdf's tag
        $this->bsk_pdf_manager_update_tag( $pdf_id, $bsk_pdf_manager_pdf_edit_tags );
        
        //
        // process file
        //
        $destination_file_name = '';
        $attachment_id = 0;
        $file_url = '';
        $file_path = '';
        $status = 'published';
        
        //upload new file
        $old_upload_from = 'computer';//sanitize_text_field( $data['bsk_pdfm_old_upload_from'] );
        $old_upload_from_computer_file_name = sanitize_text_field( $data['bsk_pdfm_old_upload_from_computer_file_name'] );
        $old_upload_from_attachment_id = intval( sanitize_text_field( $data['bsk_pdfm_old_upload_attachment_id'] ) );
        $upload_from = 'computer';//sanitize_text_field( $data['bsk_pdfm_upload_from'] );
        $upload_from_attachment_id = intval( sanitize_text_field( $data['bsk_pdf_upload_attachment_id'] ) );
        if( $old_upload_from != $upload_from || 
            ( $upload_from == 'computer' && strlen( $_FILES['bsk_pdf_file']['name'] ) > 0 ) ){
            
            if( $upload_from == 'computer' && strlen( $_FILES['bsk_pdf_file']['name'] ) > 0 ){
                //by computer
                $destination_file_name = $this->bsk_pdf_manager_pdf_upload_file( $_FILES['bsk_pdf_file'], $pdf_id, $message_id );
                if ($destination_file_name !== false ){
                    $file_url = site_url().'/'.$destination_file_name;
                    $file_path = BSKPDFManager::$_upload_root_path.$destination_file_name;
                } else {
                    $destination_file_name = '';
                    $status = 'draft';
                }
            }
            
        }
        
        //if it is edit and no file change, then file_path will be empty here
        if( $file_path == '' && $new_pdf_id < 1 ){
            if( $old_upload_from == 'computer' && strlen( $old_upload_from_computer_file_name ) > 0 ){
                if( file_exists(BSKPDFManager::$_upload_root_path.$old_upload_from_computer_file_name) ){
                    $file_url = site_url().'/'.$old_upload_from_computer_file_name;
                    $file_path = BSKPDFManager::$_upload_root_path.$old_upload_from_computer_file_name;
                }else{
                    $status = 'draft';
                }
            }else{
                $status = 'draft';
            }
        }

        //delete old file
        if( ( $destination_file_name || $attachment_id > 0 ) && $pdf_id > 0 && $new_pdf_id < 1 && strlen( $old_upload_from_computer_file_name ) ){
            if( file_exists( BSKPDFManager::$_upload_root_path.$old_upload_from_computer_file_name ) ){
                unlink(BSKPDFManager::$_upload_root_path.$old_upload_from_computer_file_name);
            }
        }
        
        //update file data && thumbnail to database
        $pdf_file_thumb_data = array();
        $pdf_file_thumb_data_format = array();

        if( $destination_file_name ){
            $pdf_file_thumb_data['file_name'] = $destination_file_name;
            $pdf_file_thumb_data_format['file_name'] = '%s';
            $pdf_file_thumb_data['by_media_uploader'] = 0;
            $pdf_file_thumb_data_format['by_media_uploader'] = '%d';
        }

        //file size
        if ( file_exists( $file_path ) ) {
            $file_size = filesize( $file_path );

            $pdf_file_thumb_data['size'] = $file_size;
            $pdf_file_thumb_data_format['size'] = '%d';
        }

        /*
         * process status
         */
        $pdf_file_thumb_data['draft'] = false;
        $pdf_file_thumb_data_format['draft'] = '%d';
        $dest_status = sanitize_text_field( $data['bsk_pdfm_post_status_select'] );
        if ( $dest_status == 'published' ) {
            $pdf_file_thumb_data['draft'] = false;
            /* if file_name is null and by_media_uploader = 0, then froce to draft */
            if ( ( isset( $pdf_file_thumb_data['file_name'] ) && $pdf_file_thumb_data['file_name'] == '' ) && 
                 ( isset( $pdf_file_thumb_data['by_media_uploader'] ) && $pdf_file_thumb_data['by_media_uploader'] == '' ) ) {
                $pdf_file_thumb_data['draft'] = true;
                $dest_status = 'draft';
            }
        } else if ( $dest_status == 'draft' ) {
            $pdf_file_thumb_data['draft'] = true;
        }
        
        if( count( $pdf_file_thumb_data ) > 0 ){
            $wpdb->update( $wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name, $pdf_file_thumb_data, array( 'id' => $pdf_id ), $pdf_file_thumb_data_format );
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
        
        if( $default_enable_permalink ){
            $file_url = site_url().'/bsk-pdf-manager/'.$slug.'/';
        }
        
        if( $status != 'draft' ){
            if( $pdf_data['publish_date'] && $pdf_data['publish_date'] > wp_date( 'Y-m-d H:i:s' ) ){
                $status = 'scheduled';
            }
            if( $pdf_data['expiry_date'] && $pdf_data['expiry_date'] <= wp_date( 'Y-m-d H:i:s' ) ){
                $status = 'expired';
            }
        }
        
        //$message_id = 0;
        
        $redirect_to = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['edit'].'&view=edit&pdfid='.$pdf_id );
        //$pdf_status = sanitize_text_field($_REQUEST['pdf_status']);
        //$redirect_to = add_query_arg( 'pdf_status', $pdf_status, $redirect_to );
		
        //$redirect_to = add_query_arg( 'message', $message_id, $redirect_to );
        if( isset( $data['bsk_pdf_manager_list_cat_id'] ) ){
            $bsk_pdf_manager_list_cat_id = intval(sanitize_text_field($data['bsk_pdf_manager_list_cat_id']));
            if( $bsk_pdf_manager_list_cat_id ){
                $redirect_to = add_query_arg( 'cat', $bsk_pdf_manager_list_cat_id, $redirect_to );
            }
        }
		
		wp_redirect( $redirect_to );
		exit;
	}
	
    function bsk_pdf_manager_update_category( $pdf_id, $categories_id_array ){
        global $wpdb;
        
        $relationship_tbl_name = $wpdb->prefix.BSKPDFManager::$_rels_tbl_name;
        $sql = 'DELETE FROM `'.esc_sql($relationship_tbl_name).'` WHERE `pdf_id` = %d AND `type` LIKE %s';
        $sql = $wpdb->prepare( $sql, $pdf_id, 'CAT' );
        $wpdb->query( $sql );
        
        if( !is_array($categories_id_array) || count($categories_id_array) < 1 ){
            return;
        }

        //insert new
        $wpdb->insert( $relationship_tbl_name, array( 'pdf_id' => $pdf_id, 'cat_id' => $categories_id_array[0], 'type' => 'CAT' ), array( '%d', '%d', '%s' ) );
    }
    
    function bsk_pdf_manager_update_tag( $pdf_id, $tags_id_array ){
        global $wpdb;
        
        $relationship_tbl_name = esc_sql($wpdb->prefix.BSKPDFManager::$_rels_tbl_name);
        $sql = 'DELETE FROM `'.$relationship_tbl_name.'` WHERE `pdf_id` = %d AND `type` LIKE %s';
        $sql = $wpdb->prepare( $sql, $pdf_id, 'TAG' );
        $wpdb->query( $sql );
        
        if( !is_array( $tags_id_array ) || count( $tags_id_array ) < 1 ){
            return;
        }

        //insert new
        $wpdb->insert( $relationship_tbl_name, array( 'pdf_id' => $pdf_id, 'cat_id' => $tags_id_array[0], 'type' => 'TAG' ), array( '%d', '%d', '%s' ) );
    }
    
	function bsk_pdf_manager_pdf_upload_file( $file, $destination_name_prefix, &$message_id ){
		if( !$file["name"] ){
            $message_id = 17;

            return false;
		}
        
		if ( $file["error"] != 0 ){
			$message_id = $file["error"];
            
			return false;
		}
        $file_extension_array = explode('.', $file["name"] );
        if( !is_array( $file_extension_array ) || count($file_extension_array) == 1 ){
            $message_id = 15;
			return false;
        }
        $file_extension = $file_extension_array[count($file_extension_array) - 1];
        
        $supported_extension_and_mime_type = BSKPDFM_Common_Backend::get_supported_extension_with_mime_type();
        if( !array_key_exists( strtolower($file_extension), $supported_extension_and_mime_type) ){
            update_option( BSKPDFManager::$_plugin_temp_option_prefix.'message_id_15', 'File Extension: '.$file_extension );
            $message_id = 15;
			return false;
        }
        
		if( !in_array( $file["type"], $supported_extension_and_mime_type[strtolower($file_extension)] ) ){
            update_option( BSKPDFManager::$_plugin_temp_option_prefix.'message_id_15', 'Mime Type: '.$file['type'] );
            $message_id = 15;
            return false;
        }
		
        $current_upload_path = BSKPDFManager::$_upload_path; 
		//save pdf by year/month
        $organise_directory_strucutre_with_year_month = true;
        $plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
        if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
            if( isset($plugin_settings['directory_with_year_month']) ){
                $organise_directory_strucutre_with_year_month = $plugin_settings['directory_with_year_month'];
			}
		}
        if( $organise_directory_strucutre_with_year_month ){
            $year = wp_date( 'Y' );
            $month = wp_date( 'm' );
            if ( !is_dir($current_upload_path.$year) ) {
                if ( !wp_mkdir_p( $current_upload_path.$year ) ) {
                    $message_id = 31;
                    $message = __( 'Create folder: %s failed.', 'bskpdfmanager' );
                    $message = sprintf( $message, $current_upload_path.$year.'/' );
                    update_option( BSKPDFManager::$_plugin_temp_option_prefix.'message_id_31', $message );
                    return false;
                }
            }
            if ( !is_writeable( $current_upload_path.$year ) ) {
                $message = __( 'Directory %s not writable.', 'bskpdfmanager' );
                $message = sprintf( $message, $current_upload_path.$year.'/' );
                update_option( BSKPDFManager::$_plugin_temp_option_prefix.'message_id_32', $message );
                $message_id = 32;
                return false;
            }
            if ( !is_dir($current_upload_path.$year.'/'.$month) ) {
                if ( !wp_mkdir_p( $current_upload_path.$year.'/'.$month ) ) {
                    $message_id = 33;
                    $message = __( 'Create folder: %s failed.', 'bskpdfmanager' );
                    $message = sprintf( $message, $current_upload_path.$year.'/'.$month.'/' );
                    update_option( BSKPDFManager::$_plugin_temp_option_prefix.'message_id_33', $message );
                    return false;
                }
            }
            if ( !is_writeable( $current_upload_path.$year.'/'.$month ) ) {
                $message = __( 'Directory %s not writable.', 'bskpdfmanager' );
                $message = sprintf( $message, $current_upload_path.$year.'/'.$month.'/' );
                update_option( BSKPDFManager::$_plugin_temp_option_prefix.'message_id_34', $message );
                $message_id = 34;
                return false;
            }
            if( !file_exists($current_upload_path.$year.'/'.$month.'/index.php') ){
                copy( BSK_PDFM_PLUGIN_DIR.'/assets/index.php',
                         $current_upload_path.$year.'/'.$month.'/index.php' );
            }
            
            //unique file name
            $upload_pdf_name = $file["name"];
            $destinate_file_name = wp_unique_filename( $current_upload_path.$year.'/'.$month.'/', $upload_pdf_name);

            //move file
            $ret = move_uploaded_file( 
                                         $file["tmp_name"], 
                                         $current_upload_path.$year.'/'.$month.'/'.$destinate_file_name
                                        );
            if( !$ret ){
                $message = __( 'Upload file to: %s failed.', 'bskpdfmanager' );
                $message = sprintf( $message, $current_upload_path.$year.'/'.$month.'/' );
                update_option( BSKPDFManager::$_plugin_temp_option_prefix.'message_id_16', $message );
                $message_id = 16;
                return false;
            }
            
            $relative_file_name = $year.'/'.$month.'/'.$destinate_file_name;
            $relative_file_name = str_replace(BSKPDFManager::$_upload_root_path, '', $current_upload_path).$relative_file_name;
            
            return $relative_file_name;
        }
        //unique file name
        $upload_pdf_name = $file["name"];
        $destinate_file_name = wp_unique_filename( $current_upload_path.'/', $upload_pdf_name);

        //move file
        $ret = move_uploaded_file( 
                                     $file["tmp_name"], 
                                     $current_upload_path.'/'.$destinate_file_name
                                  );
        if( !$ret ){
            $message = __( 'Upload file to: %s failed.', 'bskpdfmanager' );
            $message = sprintf( $message, $current_upload_path.$year.'/'.$month.'/' );
            update_option( BSKPDFManager::$_plugin_temp_option_prefix.'message_id_16', $message );
            $message_id = 16;
            return false;
        }
        
        $relative_file_name = str_replace(BSKPDFManager::$_upload_root_path, '', $current_upload_path).$destinate_file_name;
        
        return $relative_file_name;
	}
    
    function bsk_pdf_manager_bulk_delete_fun( $data ){
        //check nonce field
		if (!wp_verify_nonce(sanitize_text_field($data['_nonce']), 'bsk_pdf_manager_bulk_generate_thumb_nonce' )) {
			return;
		}
		
		if (!BSKPDFM_Common_Backend::bsk_pdfm_current_user_can()) {
            wp_die( esc_html__( 'You are now allowed to do this', 'bskpdfmanager' ) );
        }
        
        if( isset( $data['bsk_pdfm_bulk_delete_pdf_ids'] ) && 
            is_array( $data['bsk_pdfm_bulk_delete_pdf_ids'] ) && 
            count( $data['bsk_pdfm_bulk_delete_pdf_ids'] ) > 0 ){
            
            global $wpdb;
            
            //organize pdf ids to delete featured image
            $pdfs_id_to_delete_featured_image = array();
            if( isset($data['bsk_pdfm_bulk_delete_pdf_with_thumb']) && 
                is_array($data['bsk_pdfm_bulk_delete_pdf_with_thumb']) && 
                count($data['bsk_pdfm_bulk_delete_pdf_with_thumb']) > 0 ) {
                
                foreach( $data['bsk_pdfm_bulk_delete_pdf_with_thumb'] as $pdf_id_to_delete ){
                    $pdfs_id_to_delete_featured_image[] = intval( sanitize_text_field($pdf_id_to_delete) );
                }
            }
            
            //organize pdf ids
            $pdfs_id = array();
            foreach( $data['bsk_pdfm_bulk_delete_pdf_ids'] as $pdf_id ){
                $pdfs_id[] = intval( sanitize_text_field($pdf_id) );
            }
				
            $ids = implode(',', $pdfs_id);
            $ids = trim($ids,',');

            //delete all files
            $sql = 'SELECT `id`, `file_name`, `thumbnail_id` FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name).'` WHERE id IN('.$ids.')';
            $pdfs_records = $wpdb->get_results( $sql );
            if ($pdfs_records && count($pdfs_records) > 0){
                foreach($pdfs_records as $pdf_record ){
                    if( $pdf_record->file_name ){
                        if( file_exists( BSKPDFManager::$_upload_root_path.$pdf_record->file_name ) ){
                            unlink(BSKPDFManager::$_upload_root_path.$pdf_record->file_name);
                        }
                    }
                    if( $pdf_record->thumbnail_id && in_array( $pdf_record->id, $pdfs_id_to_delete_featured_image ) ){
                        wp_delete_attachment( $pdf_record->thumbnail_id, true );
                    }
                }
            }
            
            //delete relationships
            $sql = 'DELETE FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_rels_tbl_name).'` WHERE `pdf_id` IN('.$ids.')';
            $wpdb->query( $sql );

            $sql = 'DELETE FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name).'` WHERE `id` IN('.$ids.')';
            $wpdb->query( $sql );
        }
    }
    
    function bsk_pdf_manager_process_row_actions_fun(){
        if( !isset( $_GET['action'] ) || !isset( $_GET['page'] ) || $_GET['page'] != BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['base'] ){
			return;
		}
        $row_action = sanitize_text_field( $_GET['action'] );
        if( !in_array( $row_action, array( 'draft', 'publish', 'trash', 'untrash' ) ) ){
            //for delete, it need to confirm in dashboard.php
            return;
        }
        
        if( !BSKPDFM_Common_Backend::bsk_pdfm_current_user_can() ) {
            wp_die( esc_html__( 'You are now allowed to do this', 'bskpdfmanager' ) );
        }
        
        $destination_url = remove_query_arg( array( 'action', 'pdfid', '_wpnonce' ) );
        $pdf_id = -1;
        $return_message_id = 0;
        if( isset( $_GET['pdfid'] ) && sanitize_text_field( $_GET['pdfid'] ) ){
            $pdf_id = intval( sanitize_text_field( $_GET['pdfid'] ) );
        }
        
        if( $pdf_id < 1 ){
            $destination_url = add_query_arg( 'message', 36, $destination_url );
            
            wp_redirect( $destination_url );
            exit;
        }
        
        if( $row_action == 'draft' ){
            if ( ! wp_verify_nonce( sanitize_text_field($_GET['_wpnonce']), 'draft-pdf_'.$pdf_id ) ) {
                $destination_url = add_query_arg( 'message', 39, $destination_url );
            
                wp_redirect( $destination_url );
                exit;
            }
            $return = $this->pdf_draft( $pdf_id );
            
            $destination_url = add_query_arg( 'message', $return, $destination_url );
            
            wp_redirect( $destination_url );
            exit;
        }

        if( $row_action == 'publish' ){
            if ( ! wp_verify_nonce( sanitize_text_field($_GET['_wpnonce']), 'publish-pdf_'.$pdf_id ) ) {
                $destination_url = add_query_arg( 'message', 40, $destination_url );
            
                wp_redirect( $destination_url );
                exit;
            }
            $return = $this->pdf_publish( $pdf_id );
            
            $destination_url = add_query_arg( 'message', $return, $destination_url );
            
            wp_redirect( $destination_url );
            exit;
        }

        if( $row_action == 'trash' ){
            if ( ! wp_verify_nonce( sanitize_text_field($_GET['_wpnonce']), 'trash-pdf_'.$pdf_id ) ) {
                $destination_url = add_query_arg( 'message', 37, $destination_url );
            
                wp_redirect( $destination_url );
                exit;
            }
            $return = $this->pdf_trash( $pdf_id );
            
            $destination_url = add_query_arg( 'message', $return, $destination_url );
            
            wp_redirect( $destination_url );
            exit;
        }
        
        if( $row_action == 'untrash' ){
            if ( ! wp_verify_nonce( sanitize_text_field($_GET['_wpnonce']), 'untrash-pdf_'.$pdf_id ) ) {
                $destination_url = add_query_arg( 'message', 37, $destination_url );
            
                wp_redirect( $destination_url );
                exit;
            }
            $return = $this->pdf_untrash( $pdf_id );
            
            $destination_url = add_query_arg( 'message', $return, $destination_url );
            
            wp_redirect( $destination_url );
            exit;
        }
        
    }
    
    private function pdf_draft( $pdf_id ){
        global $wpdb;
        
        $sql = 'UPDATE `'.$wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name.'` SET `draft` = 1 WHERE `id` = %d';
        $sql = $wpdb->prepare( $sql, $pdf_id );
        $wpdb->query( $sql );
        
        return 39;
    }

    private function pdf_publish( $pdf_id ){
        global $wpdb;
		

        //force to draft if no file uploaded
        $sql = 'SELECT `by_media_uploader`, `file_name` FROM `'.$wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name.'` WHERE `id` = %d';
        $sql = $wpdb->prepare( $sql, $pdf_id );
        $pdf_obj_results = $wpdb->get_results( $sql );
        if ( ! $pdf_obj_results || ! is_array( $pdf_obj_results ) || count( $pdf_obj_results ) < 1 ) {
            wp_die( sprintf( __( 'ERROR - Invalid PDF ID: %s.', 'bskpdfmanager' ), $pdf_id ) );
        }
        $pdf_obj = $pdf_obj_results[0];
        if ( $pdf_obj->by_media_uploader == 0 && $pdf_obj->file_name == '' ) {
            $sql = 'UPDATE `'.$wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name.'` SET `draft` = 1 WHERE `id` = %d';
            $sql = $wpdb->prepare( $sql, $pdf_id );
            $wpdb->query( $sql );

            return 41;
        }
        
        $sql = 'UPDATE `'.$wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name.'` SET `draft` = 0 WHERE `id` = %d';
        $sql = $wpdb->prepare( $sql, $pdf_id );
        $wpdb->query( $sql );

        return 40;
    }

    function pdf_trash( $pdf_id ){
        global $wpdb;
		
        $sql = 'UPDATE `'.$wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name.'` SET `trash` = 1 WHERE `id` = %d';
        $sql = $wpdb->prepare( $sql, $pdf_id );
        $wpdb->query( $sql );
        
        return 35;
    }
    
    function pdf_untrash( $pdf_id ){
        global $wpdb;
		
        $sql = 'UPDATE `'.$wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name.'` SET `trash` = 0 WHERE `id` = %d';
        $sql = $wpdb->prepare( $sql, $pdf_id );
        $wpdb->query( $sql );
        
        return 38;
    }
    
    function bsk_pdfm_check_slug_fun(){
        $data_to_return = array();
        
        if( !check_ajax_referer( 'bsk_pdf_manager_pdf_page_ajax_oper_nonce', 'nonce' ) ){
            $data_to_return['success'] = false;
            $data_to_return['msg'] = '<p class="bsk-pdfm-parse-date-from-filename-failed-desc">'.__( 'Invalid nonce, please refresh page to try again', 'bskpdfmanager' ).'</p>';
            
            wp_die( json_encode($data_to_return) );
        }
        
        $slug = sanitize_text_field($_POST['slug']);
        if( trim($slug) == '' || strlen($slug) < 1 ){
            $data_to_return['success'] = false;
            $data_to_return['msg'] = '<p class="bsk-pdfm-parse-date-from-filename-failed-desc">'.__( 'Invalid slug, please enter a valid slug.', 'bskpdfmanager').'</p>';
            
            wp_die( json_encode($data_to_return) );
        }
        $pdf_id = intval( sanitize_text_field($_POST['pdfid']) );
        $slug = BSKPDFM_Permalink_AccessCtrl::get_document_slug( $slug, $pdf_id );
        
        if( $pdf_id > 0 ){
            global $wpdb;
            
            $sql = 'UPDATE `'.$wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name.'` SET `slug` = %s WHERE `id` = %d';
            $sql = $wpdb->prepare( $sql, $slug, $pdf_id );
            $wpdb->query( $sql );
        }
        
        $data_to_return['success'] = true;
        $data_to_return['msg'] = 'SUCCESS';
        $data_to_return['data'] = $slug;

        wp_die( json_encode($data_to_return) );
    }
    
} //end of class
