<?php

class BSKPDFM_Dashboard_Category {

	var $_plugin_pages_name = array();
    
	public function __construct() {
        add_action('bsk_pdf_manager_category_save', array($this, 'bsk_pdf_manager_category_save_fun'));
	}
	
	function bsk_pdf_manager_category_edit( $category_id = -1 ){
		global $wpdb;
		
		$cat_title = '';
        $current_edit_cat_id = 0;
        $chosen_parent_id = 0;
        $current_cat_depth = 0;
        $description = '';
		$cat_date = wp_date( 'Y-m-d' );
        $cat_time_h = wp_date( 'H' );
        $cat_time_m = wp_date( 'i' );
        $cat_time_s = wp_date( 's' );
		$cat_password = '';
		$cat_empty_message = '';
		if ($category_id > 0){
			$sql = 'SELECT * FROM '.esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name).' WHERE id = %d';
			$sql = $wpdb->prepare( $sql, $category_id );
			$category_obj_array = $wpdb->get_results( $sql );
			if (count($category_obj_array) > 0){
                $current_edit_cat_id = $category_id;
                $chosen_parent_id = $category_obj_array[0]->parent;
				$cat_title = $category_obj_array[0]->title;
                $description = $category_obj_array[0]->description;
				$cat_date = substr( $category_obj_array[0]->last_date, 0, 10 );
                $cat_time_h = substr( $category_obj_array[0]->last_date, 11, 2 );
                $cat_time_m = substr( $category_obj_array[0]->last_date, 14, 2 );
                $cat_time_s = substr( $category_obj_array[0]->last_date, 17, 2 );
				$cat_password = $category_obj_array[0]->password;
				$cat_empty_message = $category_obj_array[0]->empty_message;
                
                $current_cat_depth = BSKPDFM_Common_Backend::get_category_children_depth( $category_id );
			}
		}
        
        $parent_category_select_text = esc_html__( 'Select parent category...', 'bskpdfmanager' );
		?>
		<div class="bsk_pdf_manager_category_edit" style="padding-top: 10px;">
            <p>
                <label><?php esc_html_e( 'Category Title *', 'bskpdfmanager' ); ?>:</label>
                <input type="text" name="cat_title" id="cat_title_id" value="<?php echo esc_attr($cat_title); ?>" maxlength="512" class="bsk-pdfm-category-title" />
            </p>
            <p id="bsk_pdfm_category_title_error_ID" class="bsk-pdfm-error" style="display: none;"></p>
            <p>
                <label><?php esc_html_e( 'Parent Category', 'bskpdfmanager' ); ?>:</label>
                <?php 
                    echo BSKPDFM_Common_Backend::get_parent_category_dropdown( 
                                                                          2 - $current_cat_depth, 
                                                                          'cat_parent_category', 
                                                                          'cat_parent_category_ID', 
                                                                          $parent_category_select_text, 
                                                                          $chosen_parent_id, 
                                                                          $current_edit_cat_id );
                ?>
            </p>
            <p>
                <label>&nbsp;</label>
                <i><?php esc_html_e( 'The max category depth is 3, it means you can only chose categories with depth 1 or 2 as parent category.', 'bskpdfmanager' ); ?></i>
            </p>
            <?php if( $current_cat_depth > 0 ){ ?>
            <p>
                <label>&nbsp;</label>
                <i><?php esc_html_e( 'The current category alreay have child category so you can only chose categoreis with depth 1 as its parent category.', 'bskpdfmanager' ); ?></i>
            </p>
            <?php } ?>
            <div style="width: 100%;">
                <div style="width: 150px; height: 160px; float: left; vertical-align: middle; display: table;">
                    <span style="vertical-align:middle; display: table-cell;"><?php esc_html_e( 'Description:', 'bskpdfmanager' ); ?></span>
                </div>
                <div style="width: 65%; float: left; padding-left: 5px;">
                    <?php 
                        $settings = array( 
                                            'media_buttons' => false,
                                            'editor_height' => 150,
                                            'wpautop' => false,
                                         );
                        $description = '<p>'.esc_html__( 'Description only support in Pro version', 'bskpdfmanager' ).'</p>';
                        $description .= '<p><a style="color: #ff5b00;" href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/how-to-upgrade-to-pro-version/" target="_blank" rel="noopener">'.esc_html__( 'Upgrade to Pro', 'bskpdfmanager' ).'</a></p>';
                        wp_editor( $description, 'cat_description', $settings );
                    ?>
                    <div style="clear: both;"></div>
                </div>
                <div style="clear: both;"></div>
            </div>
		    <p>
                <label><?php esc_html_e( 'Category Password:', 'bskpdfmanager' ); ?></label>
                <input type="text" name="cat_password" id="cat_password_id" value="<?php echo esc_attr($cat_password); ?>" maxlength="32" class="bsk-pdfm-category-password"  disabled />
            </p>
            <div style="width: 150px; height: 160px; float: left; vertical-align: middle; display: table;">
                <span style="vertical-align:middle; display: table-cell;"><?php esc_html_e( 'Message for empty category', 'bskpdfmanager' ); ?></span>
            </div>
            <div style="width: 65%; float: left; padding-left: 5px;">
                <?php 
                    //name="pdf_description" name="pdf_description" id="pdf_description_id" maxlength="512" style="width:85%; height: 150px;"
                    $settings = array( 
                                        'media_buttons' => false,
                                        'editor_height' => 150,
                                        'wpautop' => false,
                                     );
                    $cat_empty_message = '<p>'.esc_html__( 'Empty message only support in Pro version', 'bskpdfmanager' ).'</p>';
                    $cat_empty_message .= '<p><a style="color: #ff5b00;" href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/how-to-upgrade-to-pro-version/" target="_blank" rel="noopener">'.esc_html__( 'Upgrade to Pro', 'bskpdfmanager' ).'</a></p>';
                    wp_editor( $cat_empty_message, 'cat_empty_message', $settings );
                ?>
                <div style="clear: both;"></div>
            </div>
            <div style="clear: both;"></div>
            <p>
                <label><?php esc_html_e( 'Date * :', 'bskpdfmanager' ); ?></label>
                <input type="text" name="cat_date" id="cat_date_id" value="<?php echo esc_attr($cat_date); ?>" class="bsk-date bsk-pdfm-date-time-date" />
                <span>@</span>
                <input type="number" name="cat_time_hour" class="bsk-pdfm-date-time-hour" value="<?php echo esc_attr($cat_time_h); ?>" min="0" max="23" step="1" disabled />
                <span>:</span>
                <input type="number" name="cat_time_minute" class="bsk-pdfm-date-time-minute" value="<?php echo esc_attr($cat_time_m); ?>" min="0" max="59" step="1"  disabled />
                <span>:</span>
                <input type="number" name="cat_time_second" class="bsk-pdfm-date-time-second" value="<?php echo esc_attr($cat_time_s); ?>" min="0" max="59" step="1"  disabled />
            </p>
		    <p>
                <input type="hidden" name="bsk_pdf_manager_action" value="category_save" />
                <input type="hidden" name="bsk_pdf_manager_category_id" value="<?php echo esc_attr($category_id); ?>" />
                <?php wp_nonce_field( plugin_basename( __FILE__ ), 'bsk_pdf_manager_category_save_oper_nonce', true, true ); ?>
            </p>
        </div>
		<?php
	}
	
	function bsk_pdf_manager_category_save_fun( $data ){
		global $wpdb;

        //check nonce field
		if (!wp_verify_nonce( sanitize_text_field($data['bsk_pdf_manager_category_save_oper_nonce']), plugin_basename( __FILE__ ) )){
            wp_die( esc_html__( 'Security issue, please refresh the page to test again!', 'bskpdfmanager' ) );
		}
        
        if (!BSKPDFM_Common_Backend::bsk_pdfm_current_user_can()) {
            wp_die( esc_html__( 'You are now allowed to do this', 'bskpdfmanager' ) );
        }
		
		if (!isset($data['bsk_pdf_manager_category_id'])){
            wp_die( esc_html__( 'No category ID field found', 'bskpdfmanager' ) );
		}
        
        $id = intval(sanitize_text_field($data['bsk_pdf_manager_category_id']));
		$title = trim(sanitize_text_field($data['cat_title']));
        $cat_parent_category = 0;
		$last_date = trim(sanitize_text_field($data['cat_date']));
		$last_date = $last_date ? $last_date.' 00:00:00' : wp_date( 'Y-m-d 00:00:00' );
		
		$title = wp_unslash($title); 

        $data_to_update = array();
		$data_to_update['title'] = $title;
        $data_to_update['parent'] = $cat_parent_category;
        $data_to_update['description'] = '';
		$data_to_update['last_date'] = $last_date;
		$data_to_update['password'] = '';
		$data_to_update['empty_message'] = '';
        if ( $id > 0 ){
			$wpdb->update( $wpdb->prefix.BSKPDFManager::$_cats_tbl_name, $data_to_update, array( 'id' => $id ) );
		}else if($id == -1){
			//insert
			$wpdb->insert( $wpdb->prefix.BSKPDFManager::$_cats_tbl_name, $data_to_update );
		}
		
		$redirect_to = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['category'] );
		wp_redirect( $redirect_to );
		exit;
	} //end of function
}
