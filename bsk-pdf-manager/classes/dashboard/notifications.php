<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class BSKPDFM_Dashboard_Notifications extends WP_List_Table {
   
    private static $_plugin_license = '_bsk_pdf_manager_pro_license_';
    private static $_plugin_license_stauts = '_bsk_pdf_manager_pro_license_stauts_';
    private static $_plugin_license_actived = '_bsk_pdf_manager_pro_license_actived_';
    private static $_plugin_license_activated_manually = '_bsk_pdf_manager_pro_license_activated_manually_';
    
    function __construct() {
        global $wpdb;
		
        //Set parent defaults
        parent::__construct( array( 
            'singular' => 'bsk-pdf-manager-notifications',  //singular name of the listed records
            'plural'   => 'bsk-pdf-manager-notifications', //plural name of the listed records
            'ajax'     => false                          //does this table support ajax?
        ) );
    }

    

    function get_columns() {
    
        $columns = array( 
			'id'			=> __( 'ID', 'bskpdfmanager' ), 
            'status'     	=> __( 'Status', 'bskpdfmanager' ), 
            'name'     	    => __( 'Name', 'bskpdfmanager' ), 
			'trigger_by'    => __( 'Trigger by', 'bskpdfmanager' ), 
            'send_to'       => __( 'Send to', 'bskpdfmanager' ), 
            'subject'       => __( 'Subject', 'bskpdfmanager' ), 
        );
        
        return $columns;
    }

    function get_column_info() {
		
		$columns = array( 
                            'id'			=> __( 'ID', 'bskpdfmanager' ), 
                            'status'     	=> __( 'Status', 'bskpdfmanager' ), 
                            'name'     	    => __( 'Name', 'bskpdfmanager' ), 
                            'trigger_by' => __( 'Trigger by', 'bskpdfmanager' ), 
                            'send_to'       => __( 'Send to', 'bskpdfmanager' ), 
                            'subject'       => __( 'Subject', 'bskpdfmanager' ), 
                        );
		
		$hidden = array();
		$sortable = array();

		$_column_headers = array( $columns, $hidden, $sortable, array() );

		return $_column_headers;
	}

    function column_default( $item, $column_name ) {
        switch( $column_name ) {
			case 'id':
				echo $item['id_link'];
			break;
            case 'status':
                $status_text = 'Active';
                $status_class = ' is-checked';
                if ( ! $item['status'] ) {
                    $status_text = 'Inactive';
                    $status_class = '';
                }
                ?>
                <div class="bsk-pdfm-status-container">
                    <div class="bsk-pdfm-status-switch-container">
                        <div class="bsk-pdfm-status-switch<?php echo $status_class; ?>" data-notification_id="<?php echo $item['id']; ?>">
                            <input type="checkbox" name="" class="bsk-pdfm-status-switch-input" /><!---->
                            <span class="bsk-pdfm-status-switch-core" style="width: 40px;"></span><!---->
                        </div>
                        <span class="bsk-pdfm-status-text">
                            <span><?php echo $status_text; ?></span>
                        </span>
                    </div>
                    <div class="bsk-pdfm-status-change-ajax-loader" style="display: none;"><img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" /></div>
                    <div class="bsk-pdfm-status-error bsk-pdfm-error" style="display: inline-block;"></div>
                </div>
                <?php
            break;
			case 'name':
				echo $item['name'];
			break;
            case 'subject':
				echo $item['subject'];
			break;
			case 'trigger_by':
                echo $item['trigger_by_str'];
            break;
            case 'send_to':
                echo $item['send_to'];
            break;
        }
    }

    protected function _column_name( $item, $classes, $data, $primary ) {

        $confirm_msg = 'Are you sure to delete?<a class="bsk-pdfm-notification-delete-cfm-yes-anchor" style="margin-left: 10px;" data-notification_id="' . $item['id'] . '">' . __( 'Yes', 'bskpdfmanager' ) . '</a>' . 
                       '<a class="bsk-pdfm-notification-delete-cfm-no-anchor" style="margin-left: 10px;">' . __( 'No', 'bskpdfmanager' ) . '</a>' . 
                       '<span class="bsk-pdfm-notification-delete-ajax-loader" style="display: none; margin-left: 10px;"><img src="' . BSKPDFManager::$_ajax_loader_img_url . '" /></span>';
        
        $send_confirm_msg = '<a class="bsk-pdfm-notification-send-cfm-no-anchor">This feature only available in Pro version with a <span style="font-weight: bold;">CREATOR</span>( or above ) license.</a>';
        $test_input = '<a class="bsk-pdfm-notification-test-cancel-anchor" style="color: #FE5B00;">This feature only available in Pro version with a <span style="font-weight: bold;">CREATOR</span>( or above ) license.</a>';

        echo '<td class="' . $classes . ' notification-name" ', $data, '>';
		echo $item['name'];
		echo $this->handle_row_actions( $item, 'name', 'name' );
        echo '<p class="bsk-pdfm-notification-delete-confirm" style="display: none; margin-top: 10px;">' . $confirm_msg . '</p>';
        echo '<p class="bsk-pdfm-notification-send-confirm" style="display: none; margin-top: 10px;">' . $send_confirm_msg . '</p>';
        echo '<p class="bsk-pdfm-notification-test-email" style="display: none; margin-top: 10px;">' . $test_input . '</p>';
        echo '<p class="bsk-pdfm-notification-send-msg" style="display: none; margin-top: 10px;">' . __( 'The notificaiton has been sent out.', 'bskpdfmanager' ) . '</p>';
        echo '<p class="bsk-pdfm-error" style="display: none;"></p>';
		echo '</td>';

	}

    protected function handle_row_actions( $item, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return '';
		}
        
        $title = $item['row_title'];
        
        $pdfs_page_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['base']  );
        if( isset( $_REQUEST['pdf_status'] ) && sanitize_text_field( $_REQUEST['pdf_status'] ) ){
            $pdfs_page_url = add_query_arg( 'pdf_status', sanitize_text_field( $_REQUEST['pdf_status'] ), $pdfs_page_url );
        }
        
        $edit_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['notification'].'&view=edit&notificationid='.$item['id'] );        
        //$delete_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['notification'].'&action=delete&notificationid='.$item['id'] ); 
        
        $action_edit = sprintf(
                            '<a href="%s" aria-label="%s">%s</a>',
                            $edit_url,
                            /* translators: %s: Post title. */
                            esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $title ) ),
                            __( 'Edit' )
                        );
        $action_delete = sprintf(
                            '<a href="javascript:void(0);" class="bsk-pdfm-notification-delete" aria-label="%s" data-notification_id="'.$item['id'].'">%s</a>',
                            esc_attr( sprintf( __( 'Delete &#8220;%s&#8221;' ), $title ) ),
                            __( 'Delete' )
                          );
       
        $action_send = sprintf(
                            '<a href="javascript:void(0);" class="bsk-pdfm-notification-send" aria-label="%s" data-notification_id="'.$item['id'].'">%s</a>',
                            esc_attr( sprintf( __( 'Send &#8220;%s&#8221;' ), $title ) ),
                            __( 'Send' )
                            );

        $action_test = sprintf(
                            '<a href="javascript:void(0);" class="bsk-pdfm-notification-test" aria-label="%s" data-notification_id="'.$item['id'].'">%s</a>',
                            esc_attr( sprintf( __( 'Test &#8220;%s&#8221;' ), $title ) ),
                            __( 'Test' )
                            );
            
        $actions = array();
        
        $actions['edit'] = $action_edit;
        $actions['delete'] = $action_delete;
        if ( $item['trigger_by'] == 'MANUALLY' && $item['status'] ) {
            $actions['send'] = $action_send;
        }
        $actions['test'] = $action_test;

		return $this->row_actions( $actions );
	}
    
    function get_data() {
		global $wpdb;
		
		$license = get_option( self::$_plugin_license );
		$license_status = get_option( self::$_plugin_license_stauts );
		if( trim($license) == '' || 
		    ( $license_status != 'VALID' && $license_status != 'EXPIRED' ) ){
			return NULL;
		}
        
		$sql = 'SELECT * FROM '.
		       esc_sql( $wpdb->prefix.BSKPDFManager::$_notifications_tbl_name ).' AS N WHERE 1 '.
               'ORDER BY N.`name` ASC ';
		$notifications = $wpdb->get_results($sql);
		if ( ! $notifications || ! is_array( $notifications ) || count( $notifications ) < 1 ){
			return NULL;
		}

		$notification_page_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['notification'] );
		$notifications_data = array();
		foreach ( $notifications as $notification ) {
			$notification_edit_page = add_query_arg( 
                                                    array(
                                                        'view' => 'edit', 
                                                        'notificationid' => $notification->id
                                                    ),
                                                    $notification_page_url
                                                );
            $auto_meta_str = '';
            if ( $notification->trigger_by == 'AUTO' ) {
                $auto_meta = unserialize( $notification->auto_meta );
                if ( $auto_meta && is_array( $auto_meta ) && count( $auto_meta ) > 0 ) {
                    $auto_meta_str .= '<p><hr /></p>';
                    $auto_meta_str .= '<p>Event: <span style="font-weight: bold;">' . BSKPDFM_Dashboard::$notification_trigger_auto_action[$auto_meta['auto_action']] . '</span></p>';
                    $auto_meta_str .= '<p>Status: ';
                    if ( $auto_meta['auto_status'] && is_array( $auto_meta['auto_status'] ) && count( $auto_meta['auto_status'] ) > 0 ) {
                        $auto_stauts_text_val = array();
                        foreach ( $auto_meta['auto_status'] as $status_val ) {
                            if ( ! isset( BSKPDFM_Dashboard_Notification::$pdf_document_available_status[$status_val] ) ) {
                                continue;
                            }
                            $auto_stauts_text_val[] = BSKPDFM_Dashboard_Notification::$pdf_document_available_status[$status_val];
                        }
                        $auto_meta_str .= implode( ', ', $auto_stauts_text_val ) . '</p>';
                    }
                    if ( $auto_meta['auto_category'] == 'ALL' ) {
                        $auto_meta_str .= '<p>Category: ' . $auto_meta['auto_category'] . '</p>';
                    } else if ( $auto_meta['auto_category'] == 'SPECIFIC' ) {
                        $category_title_array = array();
                        if ( isset( $auto_meta['auto_specific_categories'] ) && 
                             is_array( $auto_meta['auto_specific_categories'] ) && 
                             count( $auto_meta['auto_specific_categories'] ) > 0 ) {

                            $sql = 'SELECT `title` FROM `' . esc_sql( $wpdb->prefix.BSKPDFManager::$_cats_tbl_name ) . '` WHERE `id` IN( ' . implode( ',', $auto_meta['auto_specific_categories'] ) . ' )';
                            $category_title_reuslts = $wpdb->get_results( $sql );
                            if ( $category_title_reuslts && is_array( $category_title_reuslts ) && count( $category_title_reuslts ) > 0 ) {
                                foreach( $category_title_reuslts as $cat_title_obj ) {
                                    $category_title_array[] = $cat_title_obj->title;
                                }
                            }
                        }
                        $auto_meta_str .= '<p>Category: ' . implode( ', ', $category_title_array ) . '</p>';
                    }
                    
                }
            }
            $send_to_str = 'To be done...';
            $send_to_meta = unserialize( $notification->send_to_type_meta );
            switch( $notification->send_to_type ) {
                case 'email':
                    $send_to_email = $send_to_meta['send_to_email'];
                    $send_to_email = str_replace( '{admin_email}', get_option( 'admin_email' ), $send_to_email );
                    $send_to_str = '<span>' . esc_html__( 'Email', 'bskpdfmanager' ) . ': ' . $send_to_email . '</span>';
                break;
                case 'user_role':
                    $send_to_role_name_array = array();
                    $editable_roles = get_editable_roles();
                    foreach ( $editable_roles as $role => $details ) {
                        $name = translate_user_role( $details['name'] );
                        if ( in_array( $role, $send_to_meta['send_to_role'] ) ) {
                            $send_to_role_name_array[] = $name;
                        }
                    }  

                    $send_to_str = '<span>' . esc_html__( 'By User Role', 'bskpdfmanager' ) . ': ' . implode( ', ', $send_to_role_name_array ) . '</span>';
                break;
                case 'user':
                    $send_to_users_array = array();
                    $users_id_array = $send_to_meta['send_to_user'];
                    if ( $users_id_array && is_array( $users_id_array ) && count( $users_id_array ) > 0 ) {
                        foreach ( $users_id_array as $user_id ) {
                            $user = get_user_by( 'id', $user_id );
                            $user_name_email = $user->first_name . ' ' . $user->last_name . '( ' . $user->user_email . ' )';
                            $send_to_users_array[] = $user_name_email;
                        }   
                    }
                    $send_to_str = '<span>' . esc_html__( 'User', 'bskpdfmanager' ) . ': ' . implode( ', ', $send_to_users_array ) . '</span>';
                break;
            }
			$notifications_data[] = array( 
			    'id' 				=> $notification->id,
                'id_link'           => '<strong><a class="row-title" href="'.esc_url( $notification_edit_page ).'">'.esc_html( $notification->id ).'</a></strong>',
				'name'     	        => '<strong><a class="row-title" href="'.esc_url( $notification_edit_page ).'">'.esc_html( $notification->name ).'</a></strong>',
                'row_title'         => esc_html( $notification->name ),
                'subject' 			=> $notification->subject,
                'status' 			=> $notification->status,
                'trigger_by'        => $notification->trigger_by,
                'trigger_by_str'    => $notification->trigger_by . $auto_meta_str,
                'send_to'           => $send_to_str
			);
            
		}
		
		return $notifications_data;
    }

    function prepare_items() {

        $data = array();
        $data = $this->get_data();
        $total_items = 0;
        if( $data && is_array( $data ) ){
            $total_items = count( $data );
        }
        $this->items = $data;
        $this->set_pagination_args( array( 
            'total_items' => $total_items,                  // We have to calculate the total number of items
            'per_page'    => $total_items,                     // We have to determine how many items to show on a page
            'total_pages' => 1 // We have to calculate the total number of pages
        ) );
    }
	
}