<?php

class BSKPDFM_Dashboard_Tag {

	var $_plugin_pages_name = array();
    
	public function __construct() {
		
        add_action( 'wp_ajax_bsk_pdfm_tag_validate', array( $this, 'bsk_pdfm_bsk_pdfm_tag_validate_fun' ) );
		add_action('bsk_pdf_manager_tag_save', array($this, 'bsk_pdf_manager_tag_save_fun'));
        
	}
	
	function bsk_pdf_manager_tag_edit( $tag_id = -1 ){
		global $wpdb;
		
		$tag_title = '';
        $current_edit_tag_id = 0;
        $chosen_parent_id = 0;
        $current_tag_depth = 0;
        $description = '';
        $tag_date = wp_date( 'Y-m-d' );
        $tag_time_h = wp_date( 'H' );
        $tag_time_m = wp_date( 'i' );
        $tag_time_s = wp_date( 's' );
		if ($tag_id > 0){
			$sql = 'SELECT * FROM '.esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name).' WHERE id = %d';
			$sql = $wpdb->prepare( $sql, $tag_id );
			$tag_obj_array = $wpdb->get_results( $sql );
			if (count($tag_obj_array) > 0){
                $current_edit_tag_id = $tag_id;
				$tag_title = $tag_obj_array[0]->title;
                $description = $tag_obj_array[0]->description;
                $tag_date_f = $tag_obj_array[0]->last_date;
                if( !empty($tag_date_f) ){
                    $tag_date = substr( $tag_date_f, 0, 10 );
                    $tag_time_h = substr( $tag_date_f, 11, 2 );
                    $tag_time_m = substr( $tag_date_f, 14, 2 );
                    $tag_time_s = substr( $tag_date_f, 17, 2 );
                }
			}
		}
        
		?>
        <div class="bsk_pdf_manager_tag_edit" style="padding-top: 50px;">
            <p>
                <label><?php esc_html_e( 'Tag Name *', 'bskpdfmanager' ); ?>:</label>
                <input type="text" name="tag_title" id="tag_title_id" class="bsk-pdfm-tag-title" value="<?php echo esc_attr($tag_title); ?>" maxlength="512" />
            </p>
            <p id="bsk_pdfm_tag_title_error" style="display: none;">
                <label>&nbsp;</label>
                <span class="error-message"></span>
            </p>
            <div style="width: 100%;">
                <div style="width: 150px; height: 160px; float: left; vertical-align: middle; display: table;">
                    <span style="vertical-align:middle; display: table-cell;"><?php esc_html_e( 'Description:', 'bskpdfmanager' ); ?></span>
                </div>
                <div style="width: 65%; float: left; padding-left: 5px;">
                    <?php 
                        $description = '<p>'.esc_html__( 'Description only support in Pro version', 'bskpdfmanager' ).'</p>';
                        $description .= '<p><a style="color: #ff5b00;" href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/how-to-upgrade-to-pro-version/" target="_blank" rel="noopener">'.esc_html__( 'Upgrade to Pro', 'bskpdfmanager' ).'</a></p>';
                        $settings = array( 
                                            'media_buttons' => false,
                                            'editor_height' => 150,
                                            'wpautop' => false,
                                         );
                        wp_editor( $description, 'tag_description', $settings );
                    ?>
                    <div style="clear: both;"></div>
                </div>
                <div style="clear: both;"></div>
            </div>
            <p>
                <label><?php esc_html_e( 'Date * :', 'bskpdfmanager' ); ?></label>
                <input type="text" name="tag_date" id="tag_date_id" value="<?php echo esc_attr($tag_date); ?>" class="bsk-date bsk-pdfm-date-time-date" />
                <span>@</span>
                <input type="number" name="tag_time_hour" class="bsk-pdfm-date-time-hour" value="<?php echo esc_attr($tag_time_h); ?>" min="0" max="23" step="1" disabled />
                <span>:</span>
                <input type="number" name="tag_time_minute" class="bsk-pdfm-date-time-minute" value="<?php echo esc_attr($tag_time_m); ?>" min="0" max="59" step="1" disabled />
                <span>:</span>
                <input type="number" name="tag_time_second" class="bsk-pdfm-date-time-second" value="<?php echo esc_attr($tag_time_s); ?>" min="0" max="59" step="1" disabled />
            </p>
            <?php
                $ajax_nonce = wp_create_nonce( 'bsk-pdfm-tag-edit-ajax-nonce' );
            ?>
            <p>
                <input type="hidden" id="bsk_pdfm_tag_valid_name_txt_ID" value="<?php esc_attr_e( 'Please enter a valid tag name', 'bskpdfmanager' ); ?>" />
                <input type="hidden" name="bsk_pdf_manager_action" value="tag_save" />
                <input type="hidden" name="bsk_pdf_manager_tag_id" id="bsk_pdf_manager_tag_id_ID" value="<?php echo esc_attr($tag_id); ?>" />
                <input type="hidden" id="bsk_pdfm_tag_edit_ajax_nonce_ID" value="<?php echo esc_attr($ajax_nonce); ?>" />
                <?php wp_nonce_field( plugin_basename( __FILE__ ), 'bsk_pdf_manager_tag_save_oper_nonce', true, true ); ?>
            </p>
        </div>
        <?php		
	}
	
    function bsk_pdfm_bsk_pdfm_tag_validate_fun(){
        $data_to_return = array();
        
        if( !check_ajax_referer( 'bsk-pdfm-tag-edit-ajax-nonce', 'nonce' ) ){
            $data_to_return['success'] = false;
            $data_to_return['msg'] = __( 'Invalid nonce, please refresh page to try again', 'bskpdfmanager' );
            
            wp_die( json_encode($data_to_return) );
        }
        
        //check the tag name exist or not
        $tag_name = sanitize_text_field($_POST['name']);
        $tag_id = intval(sanitize_text_field($_POST['id']));
        
        global $wpdb;
        
        $sql = 'SELECT COUNT(*) AS `count` FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name).'` WHERE UPPER(`title`) LIKE %s AND `type` = "TAG"';
        $sql = $wpdb->prepare( $sql, strtoupper( $tag_name ) );
        if( $tag_id > 0 ){
            $sql .= ' AND `id` != %d';
            $sql = $wpdb->prepare( $sql, $tag_id );
        }
        $results = $wpdb->get_results( $sql );
        if( $results && is_array( $results ) && count( $results ) ){
            $querid_count = $results[0]->count;
            if( $querid_count ){
                $data_to_return['success'] = false;
                $data_to_return['msg'] = __( 'The tag name exist already '.$queried_count, 'bskpdfmanager' );
            }else{
                $data_to_return['success'] = true;
                $data_to_return['msg'] = '';
            }
        }else{
            $data_to_return['success'] = false;
            $data_to_return['msg'] = __( 'Unknow SQL error: '.$sql, 'bskpdfmanager' );
        }
        
        wp_die( json_encode($data_to_return) );
    }
    
	function bsk_pdf_manager_tag_save_fun( $data ){
		global $wpdb;

        //check nonce field
		if (!wp_verify_nonce( sanitize_text_field($data['bsk_pdf_manager_tag_save_oper_nonce']), plugin_basename( __FILE__ ) )) {
			wp_die( esc_html__( 'Security issue, please refresh the page to test again!', 'bskpdfmanager' ) );
		}
		
        if (!BSKPDFM_Common_Backend::bsk_pdfm_current_user_can()) {
            wp_die( esc_html__( 'You are now allowed to do this', 'bskpdfmanager' ) );
        }
        
		if (!isset($data['bsk_pdf_manager_tag_id'])) {
			wp_die( esc_html__( 'No tag ID field found', 'bskpdfmanager' ) );
		}
		$id = intval(sanitize_text_field($data['bsk_pdf_manager_tag_id']));
		$title = trim(sanitize_text_field($data['tag_title']));
        
        $last_date = trim(sanitize_text_field($data['tag_date'])) ? trim(sanitize_text_field($data['tag_date'])).' 00:00:00' : wp_date( 'Y-m-d 00:00:00' );
		
		$title = wp_unslash($title); 
        $empty_message = wp_unslash($empty_message); 
		
		$data_to_update = array();
		$data_to_update['title'] = $title;
        $data_to_update['type'] = 'TAG';
        $data_to_update['description'] = '';
        $data_to_update['last_date'] = $last_date;
		if ( $id > 0 ){
			$wpdb->update( $wpdb->prefix.BSKPDFManager::$_cats_tbl_name, $data_to_update, array( 'id' => $id ) );
		}else if($id == -1){
			//insert
			$wpdb->insert( $wpdb->prefix.BSKPDFManager::$_cats_tbl_name, $data_to_update );
		}
		
		$redirect_to = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['tag'] );
		wp_redirect( $redirect_to );
		exit;
	}
}