<?php

class BSKPDFM_Dashboard_Notification {

	var $_plugin_pages_name = array();
    
    public static $pdf_document_available_status = array( 
                                                            'draft' => 'Draft',
                                                            'pending' => 'Pending',
                                                            'published' => 'Published',
                                                            'scheduled' => 'Scheduled',
                                                            'with_expiry_date' => 'With Expiry Date',
                                                        );

	public function __construct() {
		
		
		add_action( 'bsk_pdf_manager_notification_save', array( $this, 'bsk_pdf_manager_notification_save_fun' ) );

        add_action( 'wp_ajax_bsk_pdfm_notification_get_users_by_role', array( $this, 'bsk_pdfm_send_to_user_list_get_users_option_by_role_fun' ) );
        add_action( 'wp_ajax_bsk_pdfm_notification_get_user_info', array( $this, 'bsk_pdfm_send_to_user_list_get_user_info_fun' ) );
        add_action( 'wp_ajax_bsk_pdfm_notification_set_status', array( $this, 'bsk_pdfm_notification_set_status_fun' ) );
        add_action( 'wp_ajax_bsk_pdfm_notification_delete', array( $this, 'bsk_pdfm_notification_delete_fun' ) );
        add_action( 'wp_ajax_bsk_pdfm_notification_send', array( $this, 'bsk_pdfm_notification_ajax_send_fun' ) );
        
	}
	
	function bsk_pdf_manager_notification_edit( $notification_id = -1 ){
		global $wpdb, $current_user;

        $plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
		
        $notify_name = '';
        $notify_trigger_by = 'AUTO';
        $notify_auto_action = 'NEW_SAVED';
        $notify_auto_status = array_keys( self::$pdf_document_available_status );
        $notify_auto_category = 'ALL';
        $notify_auto_specific_categories = array();
        $send_to_type = 'email';
        $send_to_email = '{admin_email}';
        $send_to_role = array( 'administrator' );
        $send_to_user = array();
        $notify_from_name = '';
        $notify_from_email = '{admin_email}';
        $notify_subject = '';
        $notify_body = '';
		if ( $notification_id > 0 ) {
			$sql = 'SELECT * FROM ' . esc_sql( $wpdb->prefix.BSKPDFManager::$_notifications_tbl_name ) . ' WHERE id = %d';
			$sql = $wpdb->prepare( $sql, $notification_id );
			$notification_obj_array = $wpdb->get_results( $sql );
			if ( count( $notification_obj_array ) > 0 ) {
                $notification_obj = $notification_obj_array[0];
				$notify_name = $notification_obj->name;
                $notify_trigger_by = $notification_obj->trigger_by;
                $notify_auto_meta = $notification_obj->auto_meta ? unserialize( $notification_obj->auto_meta ) : false;
                if ( $notify_auto_meta && is_array( $notify_auto_meta ) ) {
                    $notify_auto_action = isset( $notify_auto_meta['auto_action'] ) ? $notify_auto_meta['auto_action'] : $notify_auto_action;
                    $notify_auto_status = isset( $notify_auto_meta['auto_status'] ) ? $notify_auto_meta['auto_status'] : $notify_auto_status;
                    $notify_auto_category = isset( $notify_auto_meta['auto_category'] ) ? $notify_auto_meta['auto_category'] : $notify_auto_category;
                    $notify_auto_specific_categories = isset( $notify_auto_meta['auto_specific_categories'] ) ? $notify_auto_meta['auto_specific_categories'] : $notify_auto_specific_categories;
                }
                $send_to_type = $notification_obj->send_to_type;
                $send_to_type_meta = $notification_obj->send_to_type_meta ? unserialize( $notification_obj->send_to_type_meta ) : false;
                if ( $send_to_type_meta && is_array( $send_to_type_meta ) ) {
                    $send_to_email = isset( $send_to_type_meta['send_to_email'] ) ? $send_to_type_meta['send_to_email'] : $send_to_email;
                    $send_to_role = isset( $send_to_type_meta['send_to_role'] ) && is_array( $send_to_type_meta['send_to_role'] ) ? $send_to_type_meta['send_to_role'] : $send_to_role;
                    $send_to_user = isset( $send_to_type_meta['send_to_user'] ) && is_array( $send_to_type_meta['send_to_user'] ) ? $send_to_type_meta['send_to_user'] : $send_to_user;
                }
                
                $notify_from_name = $notification_obj->from_name;
                $notify_from_email = $notification_obj->from_email;
                $notify_subject = $notification_obj->subject;
                $notify_body = $notification_obj->body;

			} else {
                $notification_id = -1;
            }
		}

		?>
        <div class="bsk_pdf_manager_notification_edit" style="padding-top: 50px;">
            <p>
                <label class="left-column"><?php esc_html_e( 'Name', 'bskpdfmanager' ); ?> *:</label>
                <input type="text" name="bsk_pdfm_notify_name" id="bsk_pdfm_notify_name_ID" class="bsk-pdfm-notification-name" value="<?php echo esc_attr( $notify_name ); ?>" maxlength="256" />
            </p>
            <p id="bsk_pdfm_notify_name_error_ID" class="bsk-pdfm-error" style="display: none;"></p>
            <?php
            $trigger_by_auto_checked = ' checked';
            $trigger_by_manually_checked = '';
            $notify_auto_container_display = 'block';

            $merge_tags_manually_display = 'none';
            $merge_tags_auto_display = 'block';
            if ( $notify_trigger_by == 'MANUALLY' ) {
                $trigger_by_auto_checked = '';
                $trigger_by_manually_checked = ' checked';
                $notify_auto_container_display = 'none';

                $merge_tags_manually_display = 'block';
                $merge_tags_auto_display = 'none';
            }
            ?>
            <p>
                <label class="left-column"><?php esc_html_e( 'Trigger by', 'bskpdfmanager' ); ?> *:</label>
                <label>
                    <input type="radio" name="bsk_pdfm_notify_trigger_by" class="bsk-pdfm-notification-trigger-by" value="AUTO"<?php echo $trigger_by_auto_checked; ?> /> <?php esc_html_e( 'Rules', 'bskpdfmanager' ); ?>
                </label>
                <label style="margin-left: 20px;">
                    <input type="radio" name="bsk_pdfm_notify_trigger_by" class="bsk-pdfm-notification-trigger-by" value="MANUALLY"<?php echo $trigger_by_manually_checked; ?> /> <?php esc_html_e( 'Manually', 'bskpdfmanager' ); ?>
                </label>
            </p>
            <div id="bsk_pdfm_notify_auto_container_ID" style="display: <?php echo $notify_auto_container_display; ?>">
                <p>
                    <label class="left-column"></label>
                    <span class="right-column">
                        <span class="bsk-pdfm-notify-trigger-rule-label"><?php esc_html_e( 'Event', 'bskpdfmanager' ); ?> *:</span>
                        <select name="bsk_pdfm_notify_trigger_rule_action_select" id="bsk_pdfm_notify_trigger_rule_select_ID">
                            <?php
                            foreach( BSKPDFM_Dashboard::$notification_trigger_auto_action as $action_val => $action_text ) {
                                $checked = '';
                                if ( $notify_auto_action == $action_val ) {
                                    $checked = ' selected';
                                }
                                echo '<option value="' . $action_val . '"' . $checked . '> ' . $action_text . '</option>';
                            }
                            ?>
                        </select>
                    </span>
                </p>
                <p>
                    <label class="left-column"></label>
                    <span class="right-column">
                        <span class="bsk-pdfm-notify-trigger-rule-label"><?php esc_html_e( 'Status', 'bskpdfmanager' ); ?> *:</span>
                        <?php foreach ( self::$pdf_document_available_status as $key => $text ) {
                            $checked = in_array( $key, $notify_auto_status ) ? ' checked' : '';
                        ?>
                        <label style="margin-right: 10px;"><input type="checkbox" name="bsk_pdfm_notify_trigger_rule_status_chk[]" class="bsk-pdfm-notify-trigger-rule-status-chk" value="<?php echo $key ?>"<?php echo $checked; ?> /><?php echo $text ?></label>
                        <?php
                        }
                        ?>
                    </span>
                </p>
                <?php
                $auto_category_all_checked = ' checked';
                $auto_category_all_manually_checked = '';
                $auto_category_specific_categories_container_display = 'none';
                if ( $notify_auto_category == 'SPECIFIC' ) {
                    $auto_category_all_checked = '';
                    $auto_category_all_manually_checked = ' checked';
                    $auto_category_specific_categories_container_display = 'block';
                }
                ?>
                <p>
                    <label class="left-column"></label>
                    <span class="right-column">
                        <span class="bsk-pdfm-notify-trigger-rule-label"><?php esc_html_e( 'Category', 'bskpdfmanager' ); ?> *:</span>
                        <label style="margin-right: 10px;"><input type="radio" name="bsk_pdfm_notify_auto_category" class="bsk-pdfm-notification-auto-category" value="ALL"<?php echo $auto_category_all_checked; ?> /><?php esc_html_e( 'All categories', 'bskpdfmanager' ); ?></label>
                        <label> <input type="radio" name="bsk_pdfm_notify_auto_category" class="bsk-pdfm-notification-auto-category" value="SPECIFIC"<?php echo $auto_category_all_manually_checked; ?> /><?php esc_html_e( 'Specific categories', 'bskpdfmanager' ); ?></label>
                    </span>
                </p>
                <div id="bsk_pdfm_notify_specific_categories_container_ID" style="display: <?php echo $auto_category_specific_categories_container_display; ?>;">
                    <label class="left-column"></label>
                    <div class="right-column">
                        <div class="bsk-pdfm-notify-trigger-rule-label">&nbsp;</div>
                        <?php 
                        $current_user_assigned_categories = false;
                        $saved_categories_array = $notify_auto_specific_categories;
                        echo BSKPDFM_Common_Backend::get_category_hierarchy_checkbox( 'bsk_pdfm_notify_specific_categories[]', 'bsk-pdfm-notify-specific-category-checkbox', $saved_categories_array, 'CAT', false ); 
                        ?>
                    </div>
                </div>
            </div>
            <p>
                    <label class="left-column"></label>
                    <span class="right-column bsk-pdfm-error" id="bsk_pdfm_notify_trigger_rules_error_ID" style="display: none;"></span>         
            </p>
            <hr style="margin-top: 40px;" />
            <p>
                <label class="left-column"><?php esc_html_e( 'Send to', 'bskpdfmanager' ); ?> *:</label>
                <label><input type="radio" name="bsk_pdfm_notify_send_to_type" class="bsk-pdfm-notification-send-to-type" value="email"<?php echo ( $send_to_type == 'email' ? ' checked' : '' ); ?> /> <?php echo esc_html__( 'Enter Email', 'bskpdfmanager' ); ?></label>
                <label style="margin-left: 20px;"><input type="radio" name="bsk_pdfm_notify_send_to_type" class="bsk-pdfm-notification-send-to-type" value="user_role"<?php echo ( $send_to_type == 'user_role' ? ' checked' : '' ); ?> /> <?php echo esc_html__( 'By User Role', 'bskpdfmanager' ); ?></label>
                <label style="margin-left: 20px;"><input type="radio" name="bsk_pdfm_notify_send_to_type" class="bsk-pdfm-notification-send-to-type" value="user"<?php echo ( $send_to_type == 'user' ? ' checked' : '' ); ?> /> <?php echo esc_html__( 'User', 'bskpdfmanager' ); ?></label>
            </p>
            <p id="bsk_pdfm_notify_send_to_type_email_P_ID" style="display: <?php echo ( $send_to_type == 'email' ? 'block' : 'none' ); ?>">
                <label class="left-column">&nbsp;</label>
                <span class="right-column">
                    <input type="text" name="bsk_pdfm_notify_send_to_type_email" id="bsk_pdfm_notify_send_to_type_email_ID" class="bsk-pdfm-notification-send-to-email" style="width: 100%;" value="<?php echo esc_attr( $send_to_email ); ?>" maxlength="256" />
                    <span style="display: block; font-style:italic;">use comma (,) to separate multiple emails</span>
                </span>
            </p>
            <p id="bsk_pdfm_notify_send_to_type_user_role_P_ID" style="display: <?php echo ( $send_to_type == 'user_role' ? 'block' : 'none' ); ?>">
                <label class="left-column"></label>
                <span id="bsk_pdfm_notify_send_to_type_user_role_chk_container_ID">
                <?php
                $selected = '';
                $class = 'bsk-pdfm-notification-send-to-user-role';
                $editable_roles = get_editable_roles();
                foreach ( $editable_roles as $role => $details ) {
                    $name = translate_user_role( $details['name'] );
                    // Preselect specified role.
                    $checked = in_array( $role, $send_to_role ) ? ' checked' : '';
                    $checkbox_str = '<label><input type="checkbox" name="bsk_pdfm_notify_send_to_type_user_role[]" class="'.$class.'" value="'.esc_attr( $role ).'"' . $checked .' />'.$name.'</label>';
                    echo $checkbox_str;
                }            
                ?>
                </span>
            </p>
            <?php 
                $ajax_nonce = wp_create_nonce( 'bsk_pdfm_notify_send_to_user_select_list_ajax_oper_nonce' );
            ?>
            <div id="bsk_pdfm_notify_send_to_type_user_P_ID" style="display: <?php echo ( $send_to_type == 'user' ? 'block' : 'none' ); ?>">
                <p>
                    <label class="left-column"></label>
                    <span class="right-column">
                        <select name="bsk_pdfm_notify_send_to_user_select_role" id="bsk_pdfm_notify_send_to_user_select_role_ID">
                            <option value=""><?php echo esc_html__( 'Please select a user role...', 'bskpdfmanager' ); ?></option>
                            <?php wp_dropdown_roles(); ?>
                        </select>
                        <select name="bsk_pdfm_notify_send_to_user_select_list" id="bsk_pdfm_notify_send_to_user_select_list_ID">
                            <option value=""><?php echo esc_html__( 'Please select a user role first...', 'bskpdfmanager' ); ?></option>
                        </select>
                        <input type="hidden" id="bsk_pdfm_notify_send_to_user_select_list_loading_text_ID" value="<?php echo esc_attr__( 'Loading users in role: ', 'bskpdfmanager' ); ?>" />
                        <input type="hidden" id="bsk_pdfm_notify_send_to_user_select_list_opt_none_text_ID" value="<?php echo esc_attr__( 'Please select a role first', 'bskpdfmanager' ); ?>" />
                        <span id="bsk_pdfm_notify_send_to_user_select_list_ajax_loader_ID" style="display: none;"><img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" /></span>
                        <input type="hidden" id="bsk_pdfm_notify_send_to_user_select_list_ajax_oper_nonce_ID" value="<?php echo $ajax_nonce; ?>" />
                        <span class="bsk-pdfm-error" style="display: none;" id="bsk_pdfm_notify_send_to_user_select_list_error_ID"></span>
                        <?php
                        $selected_users_html_str = '';
                        if ( count( $send_to_user ) > 0 ) {
                            foreach ( $send_to_user as $user_id ) {
                                if ( intval( $user_id ) < 1 ) {
                                    continue;
                                }
                                $user = get_user_by( 'id', $user_id );
                                $user_name_email = $user->first_name . ' ' . $user->last_name . '( ' . $user->user_email . ' )';
                                $selected_users_html_str .= '<span class="bsk-pdfm-notify-user-selected selected-user-id-of-' . $user_id .'">
                                            <a style="cursor: pointer;" class="bsk-pdfm-notify-user-del-icon" data-userid="' . $user_id .'">X</a>&nbsp;' . $user_name_email . '
                                        </span>';
                            }
                            
                        }
                        ?>
                        <span style="display: block;" id="bsk_pdfm_notify_send_to_user_selected_users_container_ID"><?php echo $selected_users_html_str; ?></span>
                        <input type="hidden" name="bsk_pdfm_notify_send_to_user_selected_users_id" id="bsk_pdfm_notify_send_to_user_selected_users_id_ID" value="<?php echo implode( ',', $send_to_user ); ?>" />
                    </span>
                </p>
            </div>
            <p id="bsk_pdfm_send_to_error_ID" class="bsk-pdfm-error" style="display: none;"></p>
            <p>
                <label class="left-column"><?php esc_html_e( 'From Name', 'bskpdfmanager' ); ?>:</label>
                <input type="text" name="bsk_pdfm_notify_from_name" id="bsk_pdfm_notify_from_name_ID" class="right-column" value="<?php echo esc_attr( $notify_from_name ); ?>" maxlength="256" />
            </p>
            <p>
                <label class="left-column"><?php esc_html_e( 'From Email', 'bskpdfmanager' ); ?>:</label>
                <input type="text" name="bsk_pdfm_notify_from_email" id="bsk_pdfm_notify_from_email_ID" class="right-column" value="<?php echo esc_attr( $notify_from_email ); ?>" maxlength="256" />
            </p>
            <p>
                <label class="left-column"><?php esc_html_e( 'Subject', 'bskpdfmanager' ); ?> *:</label>
                <input type="text" name="bsk_pdfm_notify_subject" id="bsk_pdfm_notify_subject_ID" class="bsk-pdfm-notification-subject" value="<?php echo esc_attr( $notify_subject ); ?>" maxlength="256" />
            </p>
            <p id="bsk_pdfm_notify_subject_error_ID" class="bsk-pdfm-error" style="display: none;"></p>
            <div style="width: 100%;">
                <div style="width: 150px; height: 160px; float: left; vertical-align: middle; display: table;">
                    <span style="vertical-align:middle; display: table-cell;"><?php esc_html_e( 'Message', 'bskpdfmanager' ); ?> *:</span>
                </div>
                <div style="width: 65%; float: left; padding-left: 5px;">
                    <?php 
                        if ( $notify_body == '' ) {
                            $default_merge_tag = '{pdf_links}';
                            if ( $notify_trigger_by == 'MANUALLY' ) {
                                $default_merge_tag = '{pdf_links id="1,2,3"}';
                            }
                            $notify_body = '<p>Hi,</p>
                                            <p>Please check: </p>
                                            <p>' . $default_merge_tag . '</p>
                                            <p>Regards, <br />BannerSky.com</p> ';
                        }
                        $settings = array( 
                                            'media_buttons' => true,
                                            'editor_height' => 350,
                                            'wpautop' => false,
                                            'default_editor' => 'tinymce',
                                         );
                        wp_editor( $notify_body, 'bsk_pdfm_notify_body', $settings );
                    ?>
                    <div style="clear: both;"></div>
                    <div id="bsk_pdfm_notification_merge_tags_MANUALLY_ID" style="display: <?php echo $merge_tags_manually_display; ?>;">
                        <p><span class="bsk-pdfm-notification-merge-tags">{pdf_links id="1,2,3"}</span>, <span style="font-style:italic;">replaced by links of documents / PDFs of id 1, 2, 3. Skip pending, draft documents / pdfs.</span></p>
                        <p><span class="bsk-pdfm-notification-merge-tags">{pdf_links_by_category id="1,2"}</span>, <span style="font-style:italic;">replaced by links of documents / PDFs in categories of id 1 and 2. Skip pending, draft documents / pdfs.</span></p>
                        <p><span class="bsk-pdfm-notification-merge-tags">{pdf_edit_links id="1,2,3"}</span>, <span style="font-style:italic;">replaced by backend edit links of documents / PDFs of id 1, 2, 3.</span></p>
                        <p><span class="bsk-pdfm-notification-merge-tags">{pdf_edit_links_by_category id="1,2"}</span>, <span style="font-style:italic;">replaced by backend edit links of documents / PDFs in categories of id 1 and 2.</span></p>
                    </div>
                    <div id="bsk_pdfm_notification_merge_tags_AUTO_ID" style="display: <?php echo $merge_tags_auto_display; ?>;">
                        <p><span class="bsk-pdfm-notification-merge-tags">{pdf_links}</span>, <span style="font-style:italic;">replaced by links of documents / PDFs that match the rules, skip pending, draft documents / pdfs.</span></p>
                        <p><span class="bsk-pdfm-notification-merge-tags">{pdf_edit_links}</span>, <span style="font-style:italic;">replaced by backend edit links of documents / PDFs that match the rules.</span></p>
                    </div>
                </div>
                <div style="clear: both;"></div>
            </div>
            <p id="bsk_pdfm_notify_body_error_ID" class="bsk-pdfm-error" style="display: none;"></p>
            <input type="hidden" name="bsk_pdf_manager_action" id="bsk_pdf_manager_action_ID" value="" />
            <input type="hidden" name="bsk_pdfm_notify_id" value="<?php echo $notification_id; ?>" />
            <?php wp_nonce_field( plugin_basename( __FILE__ ), 'bsk_pdfm_notification_save_oper_nonce', true, true ); ?>
        </div>
        <?php		
	}
	
	function bsk_pdf_manager_notification_save_fun( $data ){
		global $wpdb;

        //check nonce field
		if ( ! isset($data['bsk_pdfm_notification_save_oper_nonce']) || 
             ! wp_verify_nonce( sanitize_text_field($data['bsk_pdfm_notification_save_oper_nonce']), plugin_basename( __FILE__ ) ) ){
			wp_die( esc_html__( 'Security issue, please refresh the page to test again!', 'bskpdfmanager' ) );
		}
        
        if ( ! current_user_can( 'bsk_pdfm_manage_notifications' ) ) {
            wp_die( __('You do not have sufficient permissions to access this page.', 'bskpdfmanager' ) );
        }

        if ( !isset( $data['bsk_pdfm_notify_id'] ) ){
			return;
		}

        
        $redirect_to = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['notification'] );
        wp_redirect( $redirect_to );
        exit;
	}

    function bsk_pdfm_send_to_user_list_get_users_option_by_role_fun() {

		$data_to_return = array();

		if( ! check_ajax_referer( 'bsk_pdfm_notify_send_to_user_select_list_ajax_oper_nonce', 'nonce' ) ){
            $data_to_return['success'] = false;
            $data_to_return['message'] = __( 'Invalid nonce, please refresh page to try again', 'bskpdfmanager' );
            
            wp_die( json_encode( $data_to_return ) );
        }

		$role = sanitize_text_field( $_POST['role'] );
		if ( $role == '' ) {
			$data_to_return['success'] = false;
            $data_to_return['message'] = __( 'Invalid role name', 'bskpdfmanager' );
            
            wp_die( json_encode( $data_to_return ) );
		}

		$users = get_users( array( 'role__in' => array( $role ), 'orderby' => 'display_name' ) );
		if ( ! $users || ! is_array( $users ) || count( $users ) < 1 ) {
			$data_to_return['success'] = true;
            $data_to_return['message'] = '';

			$option_str = '<option value="">'.esc_html__( 'No user found in role: ', 'bskpdfmanager' ) . $role.'</option>';
            $data_to_return['options'] = $option_str;

            wp_die( json_encode( $data_to_return ) );
		}
		
		$option_str = '<option value="">'.esc_html__( 'Please select a user...', 'bskpdfmanager' ).'</option>';
		foreach ( $users as $user ) {
			$option_str .= '<option value="' . $user->ID . '">' . esc_html( $user->user_login ) . ' ('.$user->user_email.')</option>';
		}

		$data_to_return['success'] = true;
		$data_to_return['message'] = '';
		$data_to_return['options'] = $option_str;
		
		wp_die( json_encode( $data_to_return ) );
	}

    function bsk_pdfm_send_to_user_list_get_user_info_fun(){
        $data_to_return = array();

		if( ! check_ajax_referer( 'bsk_pdfm_notify_send_to_user_select_list_ajax_oper_nonce', 'nonce' ) ){
            $data_to_return['success'] = false;
            $data_to_return['message'] = __( 'Invalid nonce, please refresh page to try again', 'bskpdfmanager' );
            
            wp_die( json_encode( $data_to_return ) );
        }

		$user_id = intval( sanitize_text_field( $_POST['userid'] ) );
		if ( $user_id < 1 ) {
			$data_to_return['success'] = false;
            $data_to_return['message'] = __( 'Invalid user ID', 'bskpdfmanager' );
            
            wp_die( json_encode( $data_to_return ) );
		}

        $user = get_user_by( 'id', $user_id );
        $user_name_email = $user->first_name . ' ' . $user->last_name . '( ' . $user->user_email . ' )';
        $html = '<span class="bsk-pdfm-notify-user-selected selected-user-id-of-' . $user_id .'">
                    <a style="cursor: pointer;" class="bsk-pdfm-notify-user-del-icon" data-userid="' . $user_id .'">X</a>&nbsp;' . $user_name_email . '
                </span>';
        $data_to_return['success'] = true;
        $data_to_return['html'] = $html;

        wp_die( json_encode( $data_to_return ) );
    }

    function bsk_pdfm_notification_set_status_fun(){
        $data_to_return = array();

		if( ! check_ajax_referer( 'bsk_pdfm_notifications_list_ajax_oper_nonce', 'nonce' ) ){
            $data_to_return['success'] = false;
            $data_to_return['message'] = __( 'Invalid nonce, please refresh page to try again', 'bskpdfmanager' );
            
            wp_die( json_encode( $data_to_return ) );
        }

        $notification_id = intval( sanitize_text_field( $_POST['notification'] ) );
		$status = intval( sanitize_text_field( $_POST['status'] ) );
		if ( $notification_id < 1 ) {
			$data_to_return['success'] = false;
            $data_to_return['message'] = __( 'Invalid notification ID', 'bskpdfmanager' );
            
            wp_die( json_encode( $data_to_return ) );
		}

        global $wpdb;

        $data_to_update = array( 'status' => $status );
        $wpdb->update( $wpdb->prefix.BSKPDFManager::$_notifications_tbl_name, $data_to_update, array( 'id' => $notification_id ) );
        
        $data_to_return['success'] = true;
        $data_to_return['html'] = '';

        wp_die( json_encode( $data_to_return ) );
    }


    function bsk_pdfm_notification_delete_fun(){
        $data_to_return = array();

		if( ! check_ajax_referer( 'bsk_pdfm_notifications_list_ajax_oper_nonce', 'nonce' ) ){
            $data_to_return['success'] = false;
            $data_to_return['message'] = __( 'Invalid nonce, please refresh page to try again', 'bskpdfmanager' );
            
            wp_die( json_encode( $data_to_return ) );
        }

        $notification_id = intval( sanitize_text_field( $_POST['notification'] ) );
		if ( $notification_id < 1 ) {
			$data_to_return['success'] = false;
            $data_to_return['message'] = __( 'Invalid notification ID', 'bskpdfmanager' );
            
            wp_die( json_encode( $data_to_return ) );
		}

        $data_to_return['success'] = false;
        $data_to_return['message'] = __( 'Onlay availalbe in Pro version with a CREATOR( or above ) license.', 'bskpdfmanager' );
        wp_die( json_encode( $data_to_return ) );
    }

    function bsk_pdfm_notification_ajax_send_fun(){
        $data_to_return = array();

		if( ! check_ajax_referer( 'bsk_pdfm_notifications_list_ajax_oper_nonce', 'nonce' ) ){
            $data_to_return['success'] = false;
            $data_to_return['message'] = __( 'Invalid nonce, please refresh page to try again.', 'bskpdfmanager' );
            
            wp_die( json_encode( $data_to_return ) );
        }

        $notification_id = intval( sanitize_text_field( $_POST['notification'] ) );
		if ( $notification_id < 1 ) {
			$data_to_return['success'] = false;
            $data_to_return['message'] = __( 'Invalid notification ID.', 'bskpdfmanager' );
            
            wp_die( json_encode( $data_to_return ) );
		}

        $data_to_return['success'] = false;
        $data_to_return['message'] = __( 'Onlay availalbe in Pro version with a CREATOR( or above ) license.', 'bskpdfmanager' );
        wp_die( json_encode( $data_to_return ) );
    }

}