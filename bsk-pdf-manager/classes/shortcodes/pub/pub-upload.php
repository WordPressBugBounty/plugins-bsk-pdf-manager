<?php

abstract class BSKPDFM_Pub_Upload_Shortcodes {

	protected $_plugin_settings;
	protected $_default_categories;
    protected $_pub_allow_for_subscribers;
    protected $_pub_allow_for_guests;
	protected $_upload_error_messages;
    
    public function __construct($plugin_settings) {
		$this->_plugin_settings = $plugin_settings;
        if ($this->_plugin_settings && is_array($this->_plugin_settings) && 
            isset($this->_plugin_settings['public_upload_enabled']) && $this->_plugin_settings['public_upload_enabled'] == true) {
            
            if (isset($this->_plugin_settings['pub_allow_for_subscribers'])) {
                $this->_pub_allow_for_subscribers = true;
            }

            if (isset($this->_plugin_settings['pub_allow_for_guests'])) {
                $this->_pub_allow_for_guests = true;
            }

            if (isset($this->_plugin_settings['pub_default_upload_categories'])) {
                $this->_default_categories = $this->_plugin_settings['pub_default_upload_categories'];
            }
        }

		$this->_upload_error_messages = array();
		$this->_upload_error_messages[0] = array( 'message' => '', 'type' => 'ERROR' );
		$this->_upload_error_messages[1] = array( 'message' => __( 'The uploaded file exceeds the maximum file size allowed.', 'bskpdfmanager' ), 
												'type' => 'ERROR');
		$this->_upload_error_messages[2] = array( 'message' => __( 'The uploaded file exceeds the maximum file size allowed.', 'bskpdfmanager' ), 
												'type' => 'ERROR');
		$this->_upload_error_messages[3] = array( 'message' => __( 'The uploaded file was only partially uploaded. Please try again in a few minutes.', 'bskpdfmanager' ), 
												'type' => 'ERROR');
		$this->_upload_error_messages[4] = array( 'message' => __( 'No file was uploaded. Please try again in a few minutes.', 'bskpdfmanager' ), 
												'type' => 'ERROR');
		$this->_upload_error_messages[5] = array( 'message' => __( 'File size is 0 please check and try again in a few minutes.', 'bskpdfmanager' ), 
												'type' => 'ERROR');
		$this->_upload_error_messages[6] = array( 'message' => __( 'Failed, seems there is no temporary folder. Please try again in a few minutes.', 'bskpdfmanager' ), 
												'type' => 'ERROR');
		$this->_upload_error_messages[7] = array( 'message' => __( 'Failed to write file to disk. Please try again in a few minutes.', 'bskpdfmanager' ), 
												'type' => 'ERROR');
		$this->_upload_error_messages[8] = array( 'message' => __( 'A PHP extension stopped the file upload. Please try again in a few minutes.', 'bskpdfmanager' ), 
												'type' => 'ERROR');
		$this->_upload_error_messages[15] = array( 'message' => __( 'Invalid file type, the file you uploaded is not allowed.', 'bskpdfmanager' ), 
												 'type' => 'ERROR');
		$this->_upload_error_messages[16] = array( 'message' => __( 'Faild to write file to destination folder.', 'bskpdfmanager' ), 
												 'type' => 'ERROR');
		$this->_upload_error_messages[17] = array( 'message' => __( 'No file was uploaded or the file is not valid. Please try again.', 'bskpdfmanager' ), 
												 'type' => 'ERROR');
		
		$this->_upload_error_messages[20] = array( 'message' => __( 'Add document successfully.', 'bskpdfmanager' ), 
												 'type' => 'SUCCESS');
		$this->_upload_error_messages[21] = array( 'message' => __( 'Failed to add document.', 'bskpdfmanager' ), 
												 'type' => 'ERROR');
        $this->_upload_error_messages[22] = array( 'message' => __( 'Update document successfully.', 'bskpdfmanager' ), 
												 'type' => 'SUCCESS');
        $this->_upload_error_messages[23] = array( 'message' => __( 'Failed to update document.', 'bskpdfmanager' ), 
												 'type' => 'ERROR');
												 
		$this->_upload_error_messages[31] = array( 'message' => __( 'Upload file failed.', 'bskpdfmanager' ), 
												 'type' => 'ERROR');					
		$this->_upload_error_messages[32] = array( 'message' => __( 'Upload file failed.', 'bskpdfmanager' ), 
												 'type' => 'ERROR');					
		$this->_upload_error_messages[33] = array( 'message' => __( 'Upload file failed.', 'bskpdfmanager' ), 
												 'type' => 'ERROR');																 
		$this->_upload_error_messages[34] = array( 'message' => __( 'Upload file failed.', 'bskpdfmanager' ), 
												 'type' => 'ERROR');
        $this->_upload_error_messages[35] = array( 'message' => __( 'The document moved to Trash.', 'bskpdfmanager' ), 
												 'type' => 'SUCCESS');
        $this->_upload_error_messages[36] = array( 'message' => __( 'Invalid document ID', 'bskpdfmanager' ), 
												 'type' => 'ERROR');
        $this->_upload_error_messages[37] = array( 'message' => __( 'Invalid nonce, please refresh page and try again', 'bskpdfmanager' ), 
												 'type' => 'ERROR');
        $this->_upload_error_messages[38] = array( 'message' => __( 'The document was restored.', 'bskpdfmanager' ), 
												 'type' => 'SUCCESS');
        $this->_upload_error_messages[39] = array( 'message' => __( 'The document was set to draft.', 'bskpdfmanager' ), 
												 'type' => 'SUCCESS');
        $this->_upload_error_messages[40] = array( 'message' => __( 'The document was published.', 'bskpdfmanager' ), 
												 'type' => 'SUCCESS');
        $this->_upload_error_messages[41] = array( 'message' => __( 'The document was foced to draft as no file uploaded.', 'bskpdfmanager' ), 
												 'type' => 'WARNING');
        $this->_upload_error_messages[42] = array( 'message' => __( 'The validator failed to detect file type!', 'bskpdfmanager' ), 
												 'type' => 'ERROR');
        $this->_upload_error_messages[43] = array( 'message' => __( 'The file you uploaded may contain malicious code.', 'bskpdfmanager' ), 
												 'type' => 'ERROR');
        $this->_upload_error_messages[44] = array( 'message' => __( 'The validator failed to clean up the file content!', 'bskpdfmanager' ), 
												 'type' => 'ERROR');
        $this->_upload_error_messages[45] = array( 'message' => __( 'The file extension does not match the file type detected by the system!', 'bskpdfmanager' ), 
												 'type' => 'ERROR');
    }
	abstract public function init_shortcode();

	protected function bsk_pdf_manager_pdf_upload_file( $file, $categories ){

        $return_data = array( 'success' => false, 'message' => '' );

		if ( !$file["name"] ) {

            $return_data['message'] = $this->_upload_error_messages[17]['message'];

            return $return_data;
		}
        
		if ( $file["error"] != 0 ){
			$message_id = intval($file["error"]);
            if (isset($this->_upload_error_messages[$message_id])) {
                $return_data['message'] = $this->_upload_error_messages[$message_id]['message'];
            } else {
                $return_data['message'] = $file["error"];
            }
            
			return $return_data;
		}

        $title = '';
        $file_extension_array = explode('.', $file["name"] );
        if( !is_array( $file_extension_array ) || count($file_extension_array) == 1 ){
            $return_data['message'] = $this->_upload_error_messages[15]['message'];

            return $return_data;
        }
        $file_extension = $file_extension_array[count($file_extension_array) - 1];
        unset( $file_extension_array[count($file_extension_array) - 1] );

        $title = implode('.', $file_extension_array);
        $file_extension = strtolower( $file_extension );
        $supported_extension_and_mime_type = BSKPDFM_Common_Backend::get_supported_extension_with_mime_type();
        if( !array_key_exists( $file_extension, $supported_extension_and_mime_type) ){
            $return_data['message'] = $this->_upload_error_messages[15]['message'] . ' File Extension: '.$file_extension;

            return $return_data;
        }
        
		if( !in_array( $file["type"], $supported_extension_and_mime_type[$file_extension] ) && $file["type"] != 'application/octet-stream' ){
            $return_data['message'] = $this->_upload_error_messages[15]['message'] . ' Mime Type: '.$file['type'];

            return $return_data;
        }

        //validate PDF file and svg files
        if ( $file_extension == 'pdf' || $file_extension == 'svg' ) {
            require_once( BSK_PDFM_PLUGIN_DIR . 'classes/dashboard/security-validator.php');
            require_once( BSK_PDFM_PLUGIN_DIR . 'classes/dashboard/security-sanitizer.php');

            $validator = new BSKPDFM_Security_Validator();
            $sanitizer = new BSKPDFM_Security_Sanitizer();

            // Detect file type
            $file_type = $validator->bsk_dd_detect_file_type(
                $file['tmp_name'], 
                $file['name']
            );
            
            if (!$file_type) {
                $return_data['message'] = $this->_upload_error_messages[42]['message'];

                return $return_data;
            }

            if ( $file_type != $file_extension ) {
                $return_data['message'] = $this->_upload_error_messages[45]['message'];

                return $return_data;
            }

            // Validate file content
            if (!$validator->bsk_dd_validate_uploaded_file($file['tmp_name'], $file['name'])) {
                $return_data['message'] = $this->_upload_error_messages[43]['message'];

                return $return_data;
            }
            
            // Sanitize file
            if (!$sanitizer->bsk_dd_sanitize_uploaded_file($file['tmp_name'], $file_type)) {
                $return_data['message'] = $this->_upload_error_messages[44]['message'];

                return $return_data;
            }
        }
		
        $current_upload_path = BSKPDFManager::$_upload_path;
		//save pdf by year/month
        $organise_directory_strucutre_with_year_month = true;
        if( $this->_plugin_settings && is_array($this->_plugin_settings) && count($this->_plugin_settings) > 0 ){
            if( isset($this->_plugin_settings['directory_with_year_month']) ){
                $organise_directory_strucutre_with_year_month = $this->_plugin_settings['directory_with_year_month'];
			}
		}

        $desitnate_path = '';
        $relative_file_name = '';
        if( $organise_directory_strucutre_with_year_month ){
            $year = wp_date( 'Y' );
            $month = wp_date( 'm' );
            if ( !is_dir($current_upload_path.$year) ) {
                if ( !wp_mkdir_p( $current_upload_path.$year ) ) {
                    $message = __( 'Create folder: %s failed.', 'bskpdfmanager' );
                    $message = sprintf( $message, $current_upload_path.$year.'/' );
                    
                    $return_data['message'] = $this->_upload_error_messages[31]['message'] . ' ' . $message;

                    return $return_data;
                }
            }
            if ( !is_writeable( $current_upload_path.$year ) ) {
                $message = __( 'Directory %s not writable.', 'bskpdfmanager' );
                $message = sprintf( $message, $current_upload_path.$year.'/' );
               
                $return_data['message'] = $this->_upload_error_messages[32]['message'] . ' ' . $message;

                return $return_data;
            }
            if ( !is_dir($current_upload_path.$year.'/'.$month) ) {
                if ( !wp_mkdir_p( $current_upload_path.$year.'/'.$month ) ) {
                    $message_id = 33;
                    $message = __( 'Create folder: %s failed.', 'bskpdfmanager' );
                    $message = sprintf( $message, $current_upload_path.$year.'/'.$month.'/' );
                    
                    $return_data['message'] = $this->_upload_error_messages[33]['message'] . ' ' . $message;

                    return $return_data;
                }
            }
            if ( !is_writeable( $current_upload_path.$year.'/'.$month ) ) {
                $message = __( 'Directory %s not writable.', 'bskpdfmanager' );
                $message = sprintf( $message, $current_upload_path.$year.'/'.$month.'/' );
                
                $return_data['message'] = $this->_upload_error_messages[34]['message'] . ' ' . $message;

                return $return_data;
            }
            if( !file_exists($current_upload_path.$year.'/'.$month.'/index.php') ){
                copy( BSK_PDFM_PRO_PLUGIN_DIR.'/assets/index.php',
                      $current_upload_path.$year.'/'.$month.'/index.php' );
            }
            
            //unique file name
            $upload_pdf_name = $file["name"];
            $destinate_file_name = wp_unique_filename( $current_upload_path.$year.'/'.$month.'/', $upload_pdf_name);
            $desitnate_path = $current_upload_path.$year.'/'.$month.'/'.$destinate_file_name;            
        } else {
            //unique file name
            $upload_pdf_name = $file["name"];
            $destinate_file_name = wp_unique_filename( $current_upload_path.'/', $upload_pdf_name);
            $desitnate_path = $current_upload_path.'/'.$destinate_file_name;
        }
        

        //move file
        $ret = move_uploaded_file($file["tmp_name"], $desitnate_path);
        if( !$ret ){
            $message = __( 'Upload file failed.', 'bskpdfmanager' );
            //$message = sprintf( $message, $current_upload_path.$year.'/'.$month.'/' );
            
            $return_data['message'] = $this->_upload_error_messages[16]['message'] . ' ' . $message;

            return $return_data;
        }
        $relative_file_name = str_replace(BSKPDFManager::$_upload_root_path, '', $desitnate_path);

        global $current_user;
        global $wpdb;
        

        //for author & contributor, need to get available categories
        $default_enable_permalink = false;
        $default_permalink_base = 'bskpdf';
		if( isset($this->_plugin_settings['enable_permalink']) ){
			$default_enable_permalink = $this->_plugin_settings['enable_permalink'];
		}
		
		if( isset($this->_plugin_settings['permalink_base']) ){
			$default_permalink_base = $this->_plugin_settings['permalink_base'];
		}
        
        $permalink_structure = get_option( 'permalink_structure' );
        if ( ! $permalink_structure ) {
            $default_enable_permalink = false;
        }

        $pdf_data = array();
        $pdf_data['cat_id'] = '999999';
        $pdf_data['title'] = stripslashes($title);
        $pdf_data['slug'] = BSKPDFM_Permalink_AccessCtrl::get_document_slug( $pdf_data['title'], 0 );
        $pdf_data['last_date'] = wp_date('Y-m-d H:i:s');
        $pdf_data['file_name'] = $relative_file_name;
        $pdf_data['author_id'] = $current_user->ID;
        $pdf_data['draft'] = true;
        if ( ! current_user_can( 'bsk_pdfm_publish' ) ) {
            $pdf_data['draft'] = true;
        }
        $pdf_data['size'] = 0;
        if ( file_exists( $desitnate_path ) ) {
            $file_size = filesize( $desitnate_path );
            $pdf_data['size'] = $file_size;
        }

        //insert
        $return = $wpdb->insert( $wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name, $pdf_data, array( '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d' ) );
        if ( !$return ){
            $message = __( 'Upload file failed, cannot insert database record.', 'bskpdfmanager' );
            //$message = sprintf( $message, $current_upload_path.$year.'/'.$month.'/' );
            
            $return_data['message'] = $this->_upload_error_messages[16]['message'] . ' ' . $message;

            return $return_data;
        }
        $pdf_id = $wpdb->insert_id;
        
        //update pdf categories
        $pdfs_categories = array();
        foreach( $categories as $key => $cat_id ){
            $cat_id = intval(sanitize_text_field($cat_id));
            $wpdb->insert( 
                            $wpdb->prefix.BSKPDFManager::$_rels_tbl_name, 
                            array( 'cat_id' => $cat_id, 'pdf_id' => $pdf_id ), 
                            array('%d', '%d') 
                            );
            
            $pdfs_categories[] = $cat_id;
        }

        $file_url = site_url().'/'.$relative_file_name;
        if( $default_enable_permalink ){
            $file_url = site_url().'/'.$default_permalink_base.'/'.$pdf_data['slug'].'/';
        }
        $pdfs_with_url_array[$pdf_id] = array( 
                                                'title' => $pdf_data['title'],
                                                'url' => $file_url,
                                                'categories' => $pdfs_categories   
                                                );
        //TO DO
        /* if( !is_wp_error($load_imagick_return) &&
            isset($data['bsk_pdfm_ftp_generate_thumb_chk']) && 
            is_array($data['bsk_pdfm_ftp_generate_thumb_chk']) && 
            in_array( $index, $data['bsk_pdfm_ftp_generate_thumb_chk'] ) && 
            in_array( $license_type, array( 'CREATOR', 'BUSINESS', 'ELITE' ) ) ){
            $pdf_page_number = $data['bsk_pdfm_ftp_generate_thumb_chk_page_number'][$index];
            $thumbnail_id = BSKPDFMPro_Dashboard_PDF_Image_Editor::bsk_pdfm_generate_thumbnail( $destinate_file_path, $pdf_page_number );
            if( !is_wp_error( $thumbnail_id ) ) {
                $pdf_thumbnail_data['thumbnail_id'] = $thumbnail_id;
                $wpdb->update( $wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name, $pdf_thumbnail_data, array( 'id' => $pdf_id ), array( '%d' ) );
            }
        } */

        do_action('bsk_pdfm_after_pub_upload_added', $pdfs_with_url_array);

        $return_data['success'] = true;
        $return_data['relative_file_name'] = $relative_file_name;

        return $return_data;
	}
	
} //end of class

require_once 'pub-upload-simple.php';


$plugin_settings = get_option(BSKPDFManager::$_plugin_settings_option, '');

//upload by shortcodes objects
$pub_upload_shortcode = new BSKPDFM_Pub_Upload_Shortcode_Simple($plugin_settings);
$pub_upload_shortcode->init_shortcode();
