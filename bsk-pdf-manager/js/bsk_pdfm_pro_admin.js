jQuery(document).ready( function($) {
	
	/*$("#bsk_pdf_manager_categories_id").change( function() {
		var cat_id = $(this).val();
		var new_action = $("#bsk-pdf-manager-pdfs-form-id").attr('action') + '&cat=' + cat_id;
		
		$("#bsk-pdf-manager-pdfs-form-id").attr('action', new_action);
		
		$("#bsk-pdf-manager-pdfs-form-id").submit();
	});
    
    $("#bsk_pdfm_extension_dropdown_id").change( function() {
		var extension = $(this).val();
		var new_action = $("#bsk-pdf-manager-pdfs-form-id").attr('action') + '&ext=' + extension;
		
		$("#bsk-pdf-manager-pdfs-form-id").attr('action', new_action);
		
		$("#bsk-pdf-manager-pdfs-form-id").submit();
	});*/
	
	$("#doaction, #bsk_pdfm_pdfs_filter_submit_id").click( function() {
		var cat_id = $("#bsk_pdf_manager_categories_id").val();
        var extension = '';
        if( $("#bsk-pdf-manager-pdfs-form-id").length > 0 ){
            extension = $("#bsk-pdf-manager-pdfs-form-id").val();
        }
        
		var new_action = $("#bsk-pdf-manager-pdfs-form-id").attr('action') + '&cat=' + cat_id;
        if( extension ){
            new_action = $("#bsk-pdf-manager-pdfs-form-id").attr('action') + '&ext=' + extension;
        }
		
		$("#bsk-pdf-manager-pdfs-form-id").attr('action', new_action);
		
		return true;
	});
    
    $(".bsk-pdfm-date-time-hour, .bsk-pdfm-date-time-minute, .bsk-pdfm-date-time-second").change( function(){
        var value = $(this).val();
        if( value.length < 2 ){
            value = '0' + value;
            $(this).val( value );
        } 
    });
	
    /*
     category
     *
     */
    $("#cat_password_id").keyup( function(){
        //only number & letters
        this.value = this.value.replace(/[^0-9a-zA-Z]/g, '');
    });
    
	$("#bsk_pdf_manager_category_save").click( function() {
		var cat_title = $("#cat_title_id").val();
		if ($.trim(cat_title) == ''){
            $( "#bsk_pdfm_category_title_error_ID" ).html( '<label>&nbsp;</label><span>Category title cannot be NULL</span>' );
            $( "#bsk_pdfm_category_title_error_ID" ).css( "display", "block" );
			
			$("#cat_title_id").focus();
			return false;
		}
		
		$("#bsk_pdf_manager_categories_form_id").submit();
	});
    
    $( "#cat_title_id" ).keyup( function() {
        $( "#bsk_pdfm_category_title_error_ID" ).html( '' );
        $( "#bsk_pdfm_category_title_error_ID" ).css( "display", "none" );
    });

    $( "#bsk_pdf_manager_category_cancel_btn_ID" ).click( function() {
		$( "#bsk_pdf_manager_category_id_ID" ).val( '' );
        $( "#bsk_pdf_manager_action_ID" ).val( '' );
		$( "#bsk_pdf_manager_categories_form_id" ).submit();
	});

    /*
     tag
     *
     */
    $("#bsk_pdf_manager_tag_save").click( function() {
		var tag_title = $("#tag_title_id").val();
		if ($.trim(tag_title) == ''){
			
            $("#bsk_pdfm_tag_title_error").find( '.error-message' ).html( $("#bsk_pdfm_tag_valid_name_txt_ID").val() );
            $("#bsk_pdfm_tag_title_error").css( "display", "block" );
			
			$("#tag_title_id").focus();
			return false;
		}
        
        //ajax check tag name
        var nonce_val = $("#bsk_pdfm_tag_edit_ajax_nonce_ID").val();
        var tag_id_val = $("#bsk_pdf_manager_tag_id_ID").val();
        var data = { 
                        action: 'bsk_pdfm_tag_validate',
                        name: tag_title,
                        id: tag_id_val,
                        nonce: nonce_val
                   };
        $( "#bsk_pdfm_tag_save_ajax_loader_ID" ).css( "display", "inline-block" );
        $.post( ajaxurl, data, function( response ) {
            $( "#bsk_pdfm_tag_save_ajax_loader_ID" ).css( "display", "none" );
            
            var return_data = $.parseJSON( response );
            if( return_data.success == false ){
                $("#bsk_pdfm_tag_title_error").find( '.error-message' ).html( return_data.msg );
                $("#bsk_pdfm_tag_title_error").css( "display", "block" );
            }else if( return_data.success == true ){
                $("#bsk_pdf_manager_tags_form_id").submit();
            }
        });
		
	});
    
    $( "#tag_title_id" ).on( 'keypress', function(){
        $("#bsk_pdfm_tag_title_error").find( '.error-message' ).html( '' );
        $("#bsk_pdfm_tag_title_error").css( "display", "none" );
    });
    
    /*
     notification
     *
     */
     $( ".bsk-pdfm-notification-trigger-by" ).click( function() {
        var trigger_by = $( 'input[name="bsk_pdfm_notify_trigger_by"]:checked' ).val();
        
        $( "#bsk_pdfm_notification_merge_tags_MANUALLY_ID" ).css( "display", "none" );
        $( "#bsk_pdfm_notification_merge_tags_AUTO_ID" ).css( "display", "none" );
        $( "#bsk_pdfm_notification_merge_tags_" + trigger_by + "_ID" ).css( "display", "block" );
        if ( trigger_by == 'MANUALLY' ) {
            $( "#bsk_pdfm_notify_auto_container_ID" ).css( "display", "none" );
        } else {
            $( "#bsk_pdfm_notify_auto_container_ID" ).css( "display", "block" );
        }

    })

    $( ".bsk-pdfm-notification-auto-category" ).click( function() {
        var category = $( 'input[name="bsk_pdfm_notify_auto_category"]:checked' ).val();
        
        if ( category == 'SPECIFIC' ) {
            $( "#bsk_pdfm_notify_specific_categories_container_ID" ).css( "display", "block" );
        } else {
            $( "#bsk_pdfm_notify_specific_categories_container_ID" ).css( "display", "none" );
        }
    })
    
	$( "#bsk_pdfm_notifiy_save_btn_ID" ).click( function() {
		var noitfy_name = $("#bsk_pdfm_notify_name_ID").val();
		if ( $.trim( noitfy_name ) == '' ){
			$( "#bsk_pdfm_notify_name_error_ID" ).html( '<label class="left-column">&nbsp;</label><span>Notification name can not be NULL</span>' );
            $( "#bsk_pdfm_notify_name_error_ID" ).css( "display", "block" );
			
			$("#bsk_pdfm_notify_name_ID").focus();
			return false;
		}

        var notify_auto = $( "input[name='bsk_pdfm_notify_trigger_by']:checked" ).val();
        if ( notify_auto == 'AUTO' ) {
            var error_occured = false;
            var error_message = '';
            var auto_stauts = $( "input[name='bsk_pdfm_notify_trigger_rule_status_chk[]']:checked" ).val();
            if ( auto_stauts == '' || auto_stauts == undefined ) {
                //$( "#bsk_pdfm_notify_trigger_rules_error_ID" ).html( 'Please check at least one status' );
                //$( "#bsk_pdfm_notify_trigger_rules_error_ID" ).css( "display", "inline-block" );
                error_message += 'Please check at least one status<br />';
                error_occured = true;
            }
            var auto_category = $( "input[name='bsk_pdfm_notify_auto_category']:checked" ).val();
            if ( auto_category == 'SPECIFIC' ) {
                var specific_categories = $( "input[name='bsk_pdfm_notify_specific_categories[]']:checked" ).val();
                if ( specific_categories == '' || specific_categories == undefined ) {
                    error_message += 'Please check at least one category<br />';
                    error_occured = true;
                }
            }

            if ( error_occured ) {
                $( "#bsk_pdfm_notify_trigger_rules_error_ID" ).html( error_message );
                $( "#bsk_pdfm_notify_trigger_rules_error_ID" ).css( "display", "inline-block" );

                $( "#bsk_pdfm_notify_trigger_rule_select_ID" ).focus();

                return false;
            }
        }

        var send_to_type = $( "input[name='bsk_pdfm_notify_send_to_type']:checked" ).val();
        switch( send_to_type ) {
            case 'email':
                var send_to_type_email = $( "#bsk_pdfm_notify_send_to_type_email_ID" ).val();
                if ( $.trim( send_to_type_email ) == '' ) {
                    $( "#bsk_pdfm_send_to_error_ID" ).html( '<label class="left-column">&nbsp;</label><span>Please enter a valid email address</span>' );
                    $( "#bsk_pdfm_send_to_error_ID" ).css( "display", "block" );
                    
                    $("#bsk_pdfm_notify_send_to_type_email_ID").focus();
                    return false;
                }
            break;
            case 'user_role':
                var send_to_type_role = $( "input[name='bsk_pdfm_notify_send_to_type_user_role[]']:checked" ).val();
                if ( $.trim( send_to_type_role ) == '' ) {
                    $( "#bsk_pdfm_send_to_error_ID" ).html( '<label class="left-column">&nbsp;</label><span>Please check a user role</span>' );
                    $( "#bsk_pdfm_send_to_error_ID" ).css( "display", "block" );
                    
                    return false;
                }
            break;
            case 'user':
                var send_to_type_user = $( "#bsk_pdfm_notify_send_to_user_selected_users_id_ID" ).val();
                if ( $.trim( send_to_type_user ) == '' ) {
                    $( "#bsk_pdfm_send_to_error_ID" ).html( '<label class="left-column">&nbsp;</label><span>Please select a user</span>' );
                    $( "#bsk_pdfm_send_to_error_ID" ).css( "display", "block" );
                    
                    $("#bsk_pdfm_notify_send_to_user_select_list_ID").focus();
                    return false;
                }
            break;
        }

        var notify_subject = $("#bsk_pdfm_notify_subject_ID").val();
		if ( $.trim( notify_subject ) == '' ){
			$( "#bsk_pdfm_notify_subject_error_ID" ).html( '<label class="left-column">&nbsp;</label><span>Mail subject can not be NULL</span>' );
            $( "#bsk_pdfm_notify_subject_error_ID" ).css( "display", "block" );
			
			$("#bsk_pdfm_notify_subject_ID").focus();
			return false;
		}


        var notify_body = $("#bsk_pdfm_notify_body").val();
		if ( $.trim( notify_body ) == '' ){
			$( "#bsk_pdfm_notify_body_error_ID" ).html( '<label class="left-column">&nbsp;</label><span>Mail body can not be NULL</span>' );
            $( "#bsk_pdfm_notify_body_error_ID" ).css( "display", "block" );
			
			$("#bsk_pdfm_notify_body").focus();
			return false;
		}

        $( "#bsk_pdf_manager_action_ID" ).val( 'notification_save' );
		$( "#bsk_pdf_manager_notification_form_id" ).submit();
	});

    $( "#bsk_pdfm_notify_name_ID, #bsk_pdfm_notify_subject_ID, #bsk_pdfm_notify_body, #bsk_pdfm_notify_send_to_type_email_ID" ).keyup( function() {

        var current_id = $( this ).attr( "id" );
        if ( current_id == 'bsk_pdfm_notify_name_ID' ) {
            $( "#bsk_pdfm_notify_name_error_ID" ).html( '' );
            $( "#bsk_pdfm_notify_name_error_ID" ).css( "display", "none" );
        } else if ( current_id == 'bsk_pdfm_notify_send_to_type_email_ID' ) {
            $( "#bsk_pdfm_send_to_error_ID" ).html( '' );
            $( "#bsk_pdfm_send_to_error_ID" ).css( "display", "none" );
        } else if ( current_id == 'bsk_pdfm_notify_subject_ID' ) {
            $( "#bsk_pdfm_notify_subject_error_ID" ).html( '' );
            $( "#bsk_pdfm_notify_subject_error_ID" ).css( "display", "none" );
        } else if ( current_id == 'notify_body' ) {
            $( "#bsk_pdfm_notify_body_error_ID" ).html( '' );
            $( "#bsk_pdfm_notify_body_error_ID" ).css( "display", "none" );
        }
    });

    $( ".bsk-pdfm-notify-trigger-rule-status-chk, .bsk-pdfm-notify-specific-category-checkbox, .bsk-pdfm-notification-auto-category" ).click( function() {
        var auto_stauts = $( "input[name='bsk_pdfm_notify_trigger_rule_status_chk[]']:checked" ).val();
        var error_message = '';
        var error_occured = false;
        if ( auto_stauts == '' || auto_stauts == undefined ) {
            error_message += 'Please check at least one status<br />';
            error_occured = true;
        }
        var auto_category = $( "input[name='bsk_pdfm_notify_auto_category']:checked" ).val();
        if ( auto_category == 'SPECIFIC' ) {
            var specific_categories = $( "input[name='bsk_pdfm_notify_specific_categories[]']:checked" ).val();
            if ( specific_categories == '' || specific_categories == undefined ) {
                error_message += 'Please check at least one category<br />';
                error_occured = true;
            }
        }

        if ( error_occured ) {
            $( "#bsk_pdfm_notify_trigger_rules_error_ID" ).html( error_message );
            $( "#bsk_pdfm_notify_trigger_rules_error_ID" ).css( "display", "inline-block" );
        } else {
            $( "#bsk_pdfm_notify_trigger_rules_error_ID" ).html( error_message );
            $( "#bsk_pdfm_notify_trigger_rules_error_ID" ).css( "display", "none" );
        }
    });

    $( ".bsk-pdfm-notification-send-to-user-role" ).click( function() {
        $( "#bsk_pdfm_send_to_error_ID" ).html( '' );
        $( "#bsk_pdfm_send_to_error_ID" ).css( "display", "none" );
    });

    $( "#bsk_pdfm_notify_send_to_user_select_list_ID" ).change( function() {
        $( "#bsk_pdfm_send_to_error_ID" ).html( '' );
        $( "#bsk_pdfm_send_to_error_ID" ).css( "display", "none" );
    });
    
    $( "#bsk_pdfm_notifiy_cancel_btn_ID" ).click( function() {
		$( "#bsk_pdfm_notify_id" ).val( '' );
        $( "#bsk_pdf_manager_action_ID" ).val( '' );
		$( "#bsk_pdf_manager_notification_form_id" ).submit();
	});

    $( ".bsk-pdfm-notification-send-to-type" ).click( function() {
        var send_to_type = $("input[name='bsk_pdfm_notify_send_to_type']:checked").val();

        $( "#bsk_pdfm_notify_send_to_type_email_P_ID" ).css( "display", "none" );
        $( "#bsk_pdfm_notify_send_to_type_user_role_P_ID" ).css( "display", "none" );
        $( "#bsk_pdfm_notify_send_to_type_user_P_ID" ).css( "display", "none" );
        if ( send_to_type == 'email' ) {
            $( "#bsk_pdfm_notify_send_to_type_email_P_ID" ).css( "display", "block" );
        } else if ( send_to_type == 'user_role' ) {
            $( "#bsk_pdfm_notify_send_to_type_user_role_P_ID" ).css( "display", "block" );
        } else if ( send_to_type == 'user' ) {
            $( "#bsk_pdfm_notify_send_to_type_user_P_ID" ).css( "display", "block" );
        }
    });

    $( "#bsk_pdfm_notify_send_to_user_select_role_ID" ).change( function() {

        $( '#bsk_pdfm_notify_send_to_user_select_list_error_ID' ).html( '' );
        $( '#bsk_pdfm_notify_send_to_user_select_list_error_ID' ).css( "display", "none" );
        var list_select_id = 'bsk_pdfm_notify_send_to_user_select_list_ID';
        var list_ajax_loader_id = 'bsk_pdfm_notify_send_to_user_select_list_ajax_loader_ID';

        var selected_role = $( this ).val();
        if ( selected_role == '' ) {
            var option_none_text = $( '#bsk_pdfm_notify_send_to_user_select_list_opt_none_text_ID' ).val();
            $( '#' + list_select_id ).html( '<option value="">' + option_none_text + '</option>' );

            return ;
        }
        var loading_users_text = $( '#bsk_pdfm_notify_send_to_user_select_list_loading_text_ID' ).val();
        $( '#' + list_select_id ).html( '<option value="">' + loading_users_text + selected_role + '</option>' );

        //ajax loader
        $( '#' + list_ajax_loader_id ).css( 'display', 'inline-block' );
        nonce_val = $( '#bsk_pdfm_notify_send_to_user_select_list_ajax_oper_nonce_ID' ).val();
		var data = 
				{ 
				  action: 'bsk_pdfm_notification_get_users_by_role',
				  nonce: nonce_val, 
				  role: selected_role
				};
		$.post(ajaxurl, data, function(response) {
            $( '#' + list_ajax_loader_id ).css( 'display', 'none' );
			return_val = $.parseJSON( response );
            if ( ! return_val.success ) {
                $( '#bsk_pdfm_notify_send_to_user_select_list_error_ID' ).html( return_val.message );
                $( '#bsk_pdfm_notify_send_to_user_select_list_error_ID' ).css( "display", "block" );
                return;
            }
			$( '#' + list_select_id ).html( return_val.options );
		});
        
    });

    $( "#bsk_pdfm_notify_send_to_user_select_list_ID" ).change( function() {
        $( '#bsk_pdfm_notify_send_to_user_select_list_error_ID' ).html( '' );
        $( '#bsk_pdfm_notify_send_to_user_select_list_error_ID' ).css( "display", "none" );

        var selected_user = $( this ).val();
        var list_ajax_loader_id = 'bsk_pdfm_notify_send_to_user_select_list_ajax_loader_ID';
        if( selected_user == '' ) {
            return;
        }

        //check if the user added or not
        if ( $( '#bsk_pdfm_notify_send_to_user_selected_users_container_ID' ).find( ".selected-user-id-of-" + selected_user ).length > 0 ) {
            $( '#bsk_pdfm_notify_send_to_user_selected_users_container_ID' ).find( ".selected-user-id-of-" + selected_user ).addClass( 'selected-user-existed' );
            setTimeout( bsk_pdfm_notification_remove_selected_user_existed_class_FUN, 3000 );

            return;
        }

        //ajax loader
        $( '#' + list_ajax_loader_id ).css( 'display', 'inline-block' );
        nonce_val = $( '#bsk_pdfm_notify_send_to_user_select_list_ajax_oper_nonce_ID' ).val();
		var data = 
				{ 
				  action: 'bsk_pdfm_notification_get_user_info',
				  nonce: nonce_val, 
				  userid: selected_user
				};
		$.post( ajaxurl, data, function( response ) {
            $( '#' + list_ajax_loader_id ).css( 'display', 'none' );
			return_val = $.parseJSON( response );
            if ( ! return_val.success ) {
                $( '#bsk_pdfm_notify_send_to_user_select_list_error_ID' ).html( return_val.message );
                $( '#bsk_pdfm_notify_send_to_user_select_list_error_ID' ).css( "display", "block" );

                return;
            }

            $( "#bsk_pdfm_notify_send_to_user_selected_users_container_ID" ).append( return_val.html );
            var exist_users = $( "#bsk_pdfm_notify_send_to_user_selected_users_id_ID" ).val();
            var exist_users_id_array = false;
            if ( exist_users == '' ) {
                exist_users_id_array = new Array();
            } else {
                exist_users_id_array = exist_users.split( ',' );
            }
            exist_users_id_array.push( selected_user );
            $( "#bsk_pdfm_notify_send_to_user_selected_users_id_ID" ).val( exist_users_id_array.join( ',' ) );
		});
    });

    function bsk_pdfm_notification_remove_selected_user_existed_class_FUN() {
        $( '#bsk_pdfm_notify_send_to_user_selected_users_container_ID' ).find( ".bsk-pdfm-notify-user-selected" ).removeClass( "selected-user-existed")
    }

    $( "#bsk_pdfm_notify_send_to_user_selected_users_container_ID" ).on( "click", ".bsk-pdfm-notify-user-del-icon", function() {
        var user_id = $( this ).data( "userid" );
        $( '#bsk_pdfm_notify_send_to_user_selected_users_container_ID' ).find( ".selected-user-id-of-" + user_id ).remove();

        var exist_users = $( "#bsk_pdfm_notify_send_to_user_selected_users_id_ID" ).val();
        var exist_users_id_array = exist_users.split( ',' );
        var to_be_removed_index = exist_users_id_array.indexOf( '' + user_id );
        if ( to_be_removed_index !== -1 ) {
            exist_users_id_array.splice( to_be_removed_index, 1 );
        }
        if ( exist_users_id_array.length > 0 ) {
            $( "#bsk_pdfm_notify_send_to_user_selected_users_id_ID" ).val( exist_users_id_array.join( ',' ) );
        } else {
            $( "#bsk_pdfm_notify_send_to_user_selected_users_id_ID" ).val( '' );
        }
    });

    $( ".bsk-pdfm-status-switch" ).click( function (){
        var status_to_be = 1;
        var status_text_to_be  = 'Active';
        if ( $( this ).hasClass( 'is-checked' ) ) {
            status_to_be = 0;
            status_text_to_be = 'Inactive';
        }

        var notification_id = $( this ).data( 'notification_id' );
        var status_switch = $( this );
        var ajax_loader = $( this ).parents( ".bsk-pdfm-status-container" ).find( ".bsk-pdfm-status-change-ajax-loader" );
        var status_text_span = $( this ).parents( ".bsk-pdfm-status-container" ).find( ".bsk-pdfm-status-text" ).find( 'span' );
        var error_div = $( this ).parents( ".bsk-pdfm-status-container" ).find( ".bsk-pdfm-status-error" );
        
        ajax_loader.css( 'display', 'inline-block' );
        nonce_val = $( '#bsk_pdfm_notifications_list_ajax_oper_nonce_ID' ).val();
		var data = 
				{ 
				  action: 'bsk_pdfm_notification_set_status',
				  nonce: nonce_val, 
                  notification: notification_id,
				  status: status_to_be
				};
		$.post( ajaxurl, data, function( response ) {
            ajax_loader.css( 'display', 'none' );
			return_val = $.parseJSON( response );
            if ( ! return_val.success ) {
                error_div.html( return_val.message );
                error_div.css( "display", "block" );

                return;
            }

            
            status_switch.removeClass( 'is-checked' );
            if ( status_to_be ) {
                status_switch.addClass( 'is-checked' );
            }
            status_text_span.html( status_text_to_be );
		});
    });

    function bsk_pdfm_notifications_row_actions_clear() {
        $( "#bsk_pdf_manager_notifications_form_id" ).find( ".bsk-pdfm-notification-delete-confirm" ).css( "display", "none" );
        $( "#bsk_pdf_manager_notifications_form_id" ).find( ".bsk-pdfm-notification-send-confirm" ).css( "display", "none" );
        $( "#bsk_pdf_manager_notifications_form_id" ).find( ".bsk-pdfm-notification-test-email" ).css( "display", "none" );
        $( "#bsk_pdf_manager_notifications_form_id" ).find( '.bsk-pdfm-error' ).html( '' );
        $( "#bsk_pdf_manager_notifications_form_id" ).find( '.bsk-pdfm-error' ).css( "display", "none" );
    }

    $( ".bsk-pdfm-notification-delete" ).click( function() {
        bsk_pdfm_notifications_row_actions_clear();

        $( this ).blur();
        var td_obj = $( this ).parents( '.notification-name' );
        td_obj.find( '.bsk-pdfm-notification-delete-confirm' ).css( "display", "block" );
    });

    $( ".bsk-pdfm-notification-delete-cfm-yes-anchor" ).click( function() {
        $( this ).blur();

        var td_obj = $( this ).parents( '.notification-name' );

        td_obj.find( '.bsk-pdfm-error' ).html( '' );
        td_obj.find( '.bsk-pdfm-error' ).css( "display", "none" );
        td_obj.find( '.bsk-pdfm-notification-delete-ajax-loader' ).css( "display", "inline-block" );

        var notification_id = $( this ).data( 'notification_id' );
        
        nonce_val = $( '#bsk_pdfm_notifications_list_ajax_oper_nonce_ID' ).val();
		var data = 
				{ 
				  action: 'bsk_pdfm_notification_delete',
				  nonce: nonce_val, 
                  notification: notification_id,
				};
		$.post( ajaxurl, data, function( response ) {
            td_obj.find( '.bsk-pdfm-notification-delete-ajax-loader' ).css( 'display', 'none' );
			return_val = $.parseJSON( response );
            if ( ! return_val.success ) {
                td_obj.find( '.bsk-pdfm-error' ).html( return_val.message );
                td_obj.find( '.bsk-pdfm-error' ).css( "display", "block" );

                return;
            }

            
            td_obj.parent().remove();
		});
    });

    $( ".bsk-pdfm-notification-delete-cfm-no-anchor" ).click( function() {
        $( this ).blur();
        var td_obj = $( this ).parents( '.notification-name' );

        td_obj.find( '.bsk-pdfm-error' ).html( '' );
        td_obj.find( '.bsk-pdfm-error' ).css( "display", "none" );

        td_obj.find( '.bsk-pdfm-notification-delete-ajax-loader' ).css( "display", "none" );
        td_obj.find( '.bsk-pdfm-notification-delete-confirm' ).css( "display", "none" );
    });

    $( ".bsk-pdfm-notification-send" ).click( function() {
        bsk_pdfm_notifications_row_actions_clear();

        $( this ).blur();
        var td_obj = $( this ).parents( '.notification-name' );
        td_obj.find( '.bsk-pdfm-notification-send-confirm' ).css( "display", "block" );
    });

    $( ".bsk-pdfm-notification-send-cfm-no-anchor" ).click( function() {
        $( this ).blur();
        var td_obj = $( this ).parents( '.notification-name' );

        td_obj.find( '.bsk-pdfm-error' ).html( '' );
        td_obj.find( '.bsk-pdfm-error' ).css( "display", "none" );

        td_obj.find( '.bsk-pdfm-notification-send-ajax-loader' ).css( "display", "none" );
        td_obj.find( '.bsk-pdfm-notification-send-confirm' ).css( "display", "none" );
    });
    
    $( ".bsk-pdfm-notification-test" ).click( function() {
        bsk_pdfm_notifications_row_actions_clear();
        
        $( this ).blur();
        var td_obj = $( this ).parents( '.notification-name' );
        td_obj.find( '.bsk-pdfm-notification-test-email' ).css( "display", "block" );
    });

    $( ".bsk-pdfm-notification-test-send-anchor, .bsk-pdfm-notification-send-cfm-yes-anchor" ).click( function() {
        $( this ).blur();

        var td_obj = $( this ).parents( '.notification-name' );

        td_obj.find( '.bsk-pdfm-error' ).html( '' );
        td_obj.find( '.bsk-pdfm-error' ).css( "display", "none" );

        var is_test = $( this ).hasClass( 'bsk-pdfm-notification-test-send-anchor' );
        var is_test_str = is_test ? 'YES' : 'NO';

        if ( is_test ) {
            //check send to
            var test_send_to = td_obj.find( ".bsk-pdfm-notification-test-email-input" ).val();
            test_send_to = $.trim( test_send_to );
            if ( test_send_to == '' ) {
                td_obj.find( '.bsk-pdfm-error' ).html( 'Please enter an invalid email address.' );
                td_obj.find( '.bsk-pdfm-error' ).css( "display", "block" );

                return;
            }

            var pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);
            if( ! pattern.test( test_send_to ) ){
                td_obj.find( '.bsk-pdfm-error' ).html( 'Please enter an invalid email address.' );
                td_obj.find( '.bsk-pdfm-error' ).css( "display", "block" );

                return;
            }
            td_obj.find( '.bsk-pdfm-notification-test-ajax-loader' ).css( "display", "inline-block" );
        } else {
            td_obj.find( '.bsk-pdfm-notification-send-ajax-loader' ).css( "display", "inline-block" );
        }

        var notification_id = $( this ).data( 'notification_id' );
        
        nonce_val = $( '#bsk_pdfm_notifications_list_ajax_oper_nonce_ID' ).val();
		var data = 
				{ 
				  action: 'bsk_pdfm_notification_send',
				  nonce: nonce_val, 
                  notification: notification_id,
                  email: test_send_to,
                  test: is_test_str,
				};
		$.post( ajaxurl, data, function( response ) {
            if ( is_test ) {
                td_obj.find( '.bsk-pdfm-notification-test-ajax-loader' ).css( "display", "none" );
            } else {
                td_obj.find( '.bsk-pdfm-notification-send-ajax-loader' ).css( "display", "none" );
            }
			return_val = $.parseJSON( response );
            if ( ! return_val.success ) {
                td_obj.find( '.bsk-pdfm-error' ).html( return_val.message );
                td_obj.find( '.bsk-pdfm-error' ).css( "display", "block" );

                return;
            }

            td_obj.find( '.bsk-pdfm-notification-send-msg' ).html( return_val.message );
            td_obj.find( '.bsk-pdfm-notification-send-msg' ).attr( 'id', return_val.message_span_ID );
            td_obj.find( '.bsk-pdfm-notification-send-msg' ).css( "display", "block" );

            td_obj.find( '.bsk-pdfm-notification-send-confirm' ).css( "display", "none" );
            td_obj.find( '.bsk-pdfm-notification-test-email' ).css( "display", "none" );

            setTimeout( 'document.getElementById( "' + return_val.message_span_ID + '").style.display="none";', 3000 );
		});
    });

    $( ".bsk-pdfm-notification-test-cancel-anchor" ).click( function() {
        $( this ).blur();
        var td_obj = $( this ).parents( '.notification-name' );

        td_obj.find( '.bsk-pdfm-error' ).html( '' );
        td_obj.find( '.bsk-pdfm-error' ).css( "display", "none" );

        td_obj.find( '.bsk-pdfm-notification-test-ajax-loader' ).css( "display", "none" );
        td_obj.find( '.bsk-pdfm-notification-test-email' ).css( "display", "none" );
    });

	/*
      pdf
      *
      */
    
    $("#bsk_pdfm_doc_box_right").on( "click", ".bsk-pdfm-doc-category-checkbox, .bsk-pdfm-doc-tag-checkbox", function() {
        $("#bsk_pdfm_edit_document_category_error_container_ID").html( '' );
        $("#bsk_pdfm_edit_document_category_error_container_ID").css( "display", "none" );
        
        var hidden_ids_ID = 'bsk_pdf_edit_cat_ids_ID';
        var processing_class_name = 'bsk-pdfm-doc-category-checkbox';
        if( $(this).hasClass( 'bsk-pdfm-doc-tag-checkbox' ) ){
            hidden_ids_ID = 'bsk_pdf_edit_tag_ids_ID';
            processing_class_name = 'bsk-pdfm-doc-tag-checkbox';
        }
        
        var cat_id = $(this).val();
        if( cat_id < 1 ){
            return;
        }
        
        var checked = $(this).is( ':checked' );
        var exist_cat_ids = $( "#" + hidden_ids_ID ).val();
        var exist_cat_ids_array = new Array;
        
        if( checked ){
            //unchecked all others
            $("#bsk_pdfm_doc_box_right").find( "." + processing_class_name ).each( function(){
                var uncheck_cat_id = $(this).val();
                if( uncheck_cat_id != cat_id ){
                    $(this).prop( "checked", false );
                }
            });
            
            //add new cat id
            exist_cat_ids_array.push( cat_id );
        }else{
            //remove
        }
        $("#" + hidden_ids_ID ).val( exist_cat_ids_array.join(',') );
    });
    
    $("#bsk_pdf_manager_pdf_titile_id").keypress( function(){
        $( "#bsk_pdfm_doc_title_prompt_text" ).css( 'display', 'none' );
        
        $("#bsk_pdfm_pdf_titile_error_ID").html( "" );
        $("#bsk_pdfm_pdf_titile_error_ID").css( "display", "none" );
    })
    
    $("#bsk_pdf_manager_pdf_titile_id").keyup( function(){
        var title_val = $.trim( $(this).val() );
        if( title_val == '' ){
            $( "#bsk_pdfm_doc_title_prompt_text" ).css( 'display', 'block' );
        }
    });
    
    $("#bsk_pdf_manager_pdf_titile_id").blur( function(){
        var pdf_id = $( "#bsk_pdf_manager_pdf_id_ID" ).val();
        var text_input_slug = $(this).val();
        if( pdf_id > 0 ){
            return;
        }
        
        if( text_input_slug == '' ){
            $( "#bsk_pdfm_doc_hidden_slug_ID" ).val( '' );
            $( "#bsk_pdfm_doc_edit_slug_box_ID" ).css( "display", "none" );
            $( "#bsk_pdfm_doc_edit_slug_permalink_span_ID" ).css( "display", "none" );
            $( "#bsk_pdfm_doc_edit_slug_buttons_span_ID" ).css( "display", "none" );
            
            return;
        }
        bsk_pdfm_ajax_check_set_slug_fun( text_input_slug, true );
    })
    
    $("#bsk_pdfm_doc_edit_slug_buttons_span_ID .edit-slug").click( function(){
        var pdf_id = $( "#bsk_pdf_manager_pdf_id_ID" ).val();
        var editable_slug = $( "#bsk_pdfm_doc_hidden_slug_ID" ).val();
        
        var permalink_edit_html = '';
        if( pdf_id > 0 ){
            //edit 
            permalink_edit_html = $( "#bsk_pdfm_doc_edit_slug_permalink_span_ID" ).find( "a" ).html();
        }else{
            //new
            permalink_edit_html = $( "#bsk_pdfm_doc_edit_slug_permalink_span_ID" ).html();
        }
        var editable_span_html_to_replace = '<span id="bsk_pdfm_doc_editable_slug_ID">' + editable_slug + '</span>';
        var editable_span_html_edit_input = '<span id="bsk_pdfm_doc_editable_slug_ID"><input type="text" value="' + editable_slug + '" id="new-post-slug" autocomplete size="50" max="256" /></span>';

        permalink_edit_html = permalink_edit_html.replace( /<span id="bsk_pdfm_doc_editable_slug_ID">(.*?)<\/span>/s, editable_span_html_edit_input );
        $( "#bsk_pdfm_doc_edit_slug_permalink_span_ID" ).html( permalink_edit_html );
        
        $(this).css( "display", "none" );
        $("#bsk_pdfm_doc_edit_slug_buttons_span_ID .save").css( "display", "inline-block" );
        $("#bsk_pdfm_doc_edit_slug_buttons_span_ID .cancel").css( "display", "inline-block" );
    });
    
    $("#bsk_pdfm_doc_edit_slug_buttons_span_ID .cancel").click( function(){
        var pdf_id = $( "#bsk_pdf_manager_pdf_id_ID" ).val();
        var editable_slug = $( "#bsk_pdfm_doc_hidden_slug_ID" ).val();
        var permalink_edit_html = $( "#bsk_pdfm_doc_edit_slug_permalink_span_ID" ).html();
        
        var permalink_href = permalink_edit_html.replace( /<span id="bsk_pdfm_doc_editable_slug_ID">(.*?)<\/span>/s, editable_slug );
        var permalink_edit_span = permalink_edit_html.replace( /<span id="bsk_pdfm_doc_editable_slug_ID">(.*?)<\/span>/s, '<span id="bsk_pdfm_doc_editable_slug_ID">' + editable_slug + '</span>' );
        
        var new_a_html = '<a href="'+permalink_href+'" target="_blank">' + permalink_edit_span + '</a>';
        if( pdf_id < 1 ){
            new_a_html = permalink_edit_span;
        }
        $( "#bsk_pdfm_doc_edit_slug_permalink_span_ID" ).html( new_a_html );
        
        $("#bsk_pdfm_doc_edit_slug_buttons_span_ID .edit-slug").css( "display", "inline-block" );
        $("#bsk_pdfm_doc_edit_slug_buttons_span_ID .save").css( "display", "none" );
        $("#bsk_pdfm_doc_edit_slug_buttons_span_ID .cancel").css( "display", "none" );
    });
    
    function bsk_pdfm_ajax_check_set_slug_fun( text_input_slug, edit_save ){
        var pdf_id = $( "#bsk_pdf_manager_pdf_id_ID" ).val();
        if( pdf_id > 0 && edit_save == false ){
            //for edit document, do not change slug when edit title
            return;
        }
        
        if( pdf_id < 1 ){
            var permalink_edit_html = $( "#bsk_pdfm_doc_edit_slug_permalink_span_ID" ).html();
            var new_text_html = permalink_edit_html.replace( /<a href(.*?)>/s, '' );
            new_text_html = new_text_html.replace( '</a>', '' );
            $( "#bsk_pdfm_doc_edit_slug_permalink_span_ID" ).html( new_text_html );
        }

        $( "#bsk_pdfm_doc_edit_slug_permalink_span_ID" ).css( "display", "inline-block" );
        $( "#bsk_pdfm_doc_edit_slug_buttons_span_ID" ).css( "display", "inline-block" );
        
        if( pdf_id < 1 && text_input_slug == '' ){
            $("#bsk_pdfm_doc_hidden_slug_ID").val( '' );
            $( "#bsk_pdfm_doc_edit_slug_permalink_span_ID" ).css( "display", "none" );
            $( "#bsk_pdfm_doc_edit_slug_buttons_span_ID" ).css( "display", "none" );
            
            return;
        }
        
        //ajax check slug
        var nonce_val = $("#bsk_pdf_manager_pdf_page_ajax_oper_nonce_ID").val();
        var data = { 
                        action: 'bsk_pdfm_check_slug',
                        slug: text_input_slug,
                        pdfid: pdf_id,
                        nonce: nonce_val
                   };
        $("#bsk_pdfm_doc_edit_slug_ajax_loader_ID").css( "display", "inline-block" );
        $.post( ajaxurl, data, function( response ) {
            $("#bsk_pdfm_doc_edit_slug_ajax_loader_ID").css( "display", "none" );
            var return_data = $.parseJSON( response );
            if( return_data.success == false ){
                $("#bsk_pdfm_pdf_slug_error_ID").html( return_data.msg );
                $("#bsk_pdfm_pdf_slug_error_ID").css( "display", "block" );
            }else if( return_data.success == true ){
                $("#bsk_pdfm_doc_hidden_slug_ID").val( return_data.data );
                $("#bsk_pdfm_pdf_slug_error_ID").css( "display", "none" );
                
                var editable_slug = return_data.data;
                var permalink_edit_html = $( "#bsk_pdfm_doc_edit_slug_permalink_span_ID" ).html();
        
                var permalink_href = permalink_edit_html.replace( /<span id="bsk_pdfm_doc_editable_slug_ID">(.*?)<\/span>/s, editable_slug );
                var permalink_edit_span = permalink_edit_html.replace( /<span id="bsk_pdfm_doc_editable_slug_ID">(.*?)<\/span>/s, '<span id="bsk_pdfm_doc_editable_slug_ID">' + editable_slug + '</span>' );
                if( pdf_id > 0 ){
                    var new_a_html = '<a href="'+permalink_href+'" target="_blank">' + permalink_edit_span + '</a>';
                    $( "#bsk_pdfm_doc_edit_slug_permalink_span_ID" ).html( new_a_html );
                }else{
                    var new_a_html = permalink_edit_span.replace( /<a href(.*?)>/s, '' );
                    new_a_html = new_a_html.replace( '</a>', '' );
                    $( "#bsk_pdfm_doc_edit_slug_permalink_span_ID" ).html( new_a_html );
                }
                
                if( $( "#bsk_pdfm_doc_file_upload_div" ).find( "#bsk_pdfm_file_location_url_ID" ).length > 0 ){
                    $( "#bsk_pdfm_file_location_url_ID" ).attr( 'href', permalink_href );
                }

                $("#bsk_pdfm_doc_edit_slug_buttons_span_ID .edit-slug").css( "display", "inline-block" );
                $("#bsk_pdfm_doc_edit_slug_buttons_span_ID .save").css( "display", "none" );
                $("#bsk_pdfm_doc_edit_slug_buttons_span_ID .cancel").css( "display", "none" );
            }
        });
    }
    
    $( "#bsk_pdfm_doc_edit_slug_buttons_span_ID .save" ).click( function(){
        var text_input_slug = $( "#bsk_pdfm_doc_editable_slug_ID #new-post-slug" ).val();
        
        bsk_pdfm_ajax_check_set_slug_fun( text_input_slug, true );
    });
    
    
    $("#pdf_date_use_current_server_datetime_chk_ID").click( function() {
        var current_server_datetime = $("#bsk_pdfm_current_server_datetime_text_ID").html();
        if( current_server_datetime.length != 19 ){
            $("#bsk_pdfm_current_server_datetime_text_ID").html( "" );
            $("#pdf_date_use_current_server_datetime_chk_ID").prop( "checked", false );
            $("#pdf_date_use_current_server_datetime_chk_ID").prop( "disabled", true);
            
            //enable datepicker
            //bsk_pdfm_disalb_or_enable_date_time_fields( $("#bsk_pdfm_date_time_section_ID"), false );
            
            return;
        }
        
        if( $(this).is(":checked") ){
            $("#bsk_pdfm_date_time_section_ID").find(".bsk-pdfm-date-time-date").val( current_server_datetime.substr(0, 10) );
            $("#bsk_pdfm_date_time_section_ID").find(".bsk-pdfm-date-time-hour").val( current_server_datetime.substr(11, 2) );
            $("#bsk_pdfm_date_time_section_ID").find(".bsk-pdfm-date-time-minute").val( current_server_datetime.substr(14, 2) );
            $("#bsk_pdfm_date_time_section_ID").find(".bsk-pdfm-date-time-second").val( current_server_datetime.substr(17, 2) );
            
            //bsk_pdfm_disalb_or_enable_date_time_fields( $("#bsk_pdfm_date_time_section_ID"), true );
            //$("#pdf_date_use_file_last_modify_chk_ID").prop( "checked", false );
            //$("#pdf_date_use_parsed_from_filename_chk_ID").prop( "checked", false );
        }else{
            //bsk_pdfm_disalb_or_enable_date_time_fields( $("#bsk_pdfm_date_time_section_ID"), false );
        }
    });
    
    $("#bsk_pdf_file_id").change( function( event ){
        
        $( "#bsk_pdfm_browse_error_ID" ).css( "display", "none" );
        
        if( event.target.files.length < 1 ){
            //uncheck
            $("#pdf_date_use_file_last_modify_ID").prop( "checked", false );
            //set last moifided to null
            $("#bsk_pdfm_lastmodified_text_ID").html( "" );
            $("#bsk_pdfm_lastmodified_val_ID").val( "" );
            //enable datepicker
            $("#pdf_date_id").datepicker( "option", "disabled", false );
            
            return;
        }
        
        //set last modified
        var date = new Date( event.target.files[0].lastModified );
        var date_full = dateFormat( date, 'yyyy-mm-dd HH:MM:ss' );
        var date_new = dateFormat( date, 'yyyy-mm-dd' );
        $("#bsk_pdfm_lastmodified_text_ID").html( date_full );
        $("#bsk_pdfm_lastmodified_val_ID").val( date_full );
    });
    
	$("#bsk_pdfm_doc_save_btn_ID").click( function() {
		//check category
		var category = new Array();
		var category_str = $("#bsk_pdf_edit_cat_ids_ID").val();
        category_str = $.trim( category_str );
        if( category_str ){
            category = category_str.split(',');
        }
        if( category.length < 1 ){
            $("#bsk_pdf_edit_cat_ids_ID").val( "" );
            $("#bsk_pdfm_edit_document_category_error_container_ID").html( 'Please select at least one category.' );
            $("#bsk_pdfm_edit_document_category_error_container_ID").css( "display", "block" );
            $('html, body').animate({
                scrollTop: $("#categorydiv").offset().top - 100
            }, 1000);
            
            return false;
        }
        
		//check title
		var pdf_title = $("#bsk_pdf_manager_pdf_titile_id").val();
		if( $.trim( pdf_title ) == '' ){
            
            var error_message = 'Pleae enter a vaild title';
			$("#bsk_pdfm_pdf_titile_error_ID").html( error_message );
            $("#bsk_pdfm_pdf_titile_error_ID").css( "display", "block" );
            $("#bsk_pdf_manager_pdf_titile_id").focus();
            $('html, body').animate({
                scrollTop: $("#bsk_pdf_manager_pdf_titile_id").offset().top - 100
            }, 1000);
            return false;
		}
		
		$("#bsk-pdf-manager-pdfs-form-id").submit();
	});

    /* status edit */
    $( "#bsk_pdfm_post_status_edit_anchor" ).click( function() {
        $( this ).css( "display", "none" );
        $( "#bsk_pdfm_post_status_edit_container" ).css( "display", "block" );
    })

    $( "#bsk_pdfm_post_status_edit_OK_ID" ).click( function() {
        var new_status = $( "#bsk_pdfm_post_status_select_ID option:selected" ).text();
        $( "#post_status_display_span_ID" ).html( new_status );
        $( "#bsk_pdfm_post_status_edit_container" ).css( "display", "none" );
        $( "#bsk_pdfm_post_status_edit_anchor" ).css( "display", "inline-block" );
    });

    $( "#bsk_pdfm_post_status_edit_Cancel_ID" ).click( function() {
        $( "#bsk_pdfm_post_status_edit_container" ).css( "display", "none" );
        $( "#bsk_pdfm_post_status_edit_anchor" ).css( "display", "inline-block" );
    });
	
	$(".bsk-date").datepicker({
        dateFormat : 'yy-mm-dd'
    });
	
	var uploader_frame;
	
	$('#bsk_pdf_manager_set_featured_image_anchor_ID').click(function( event ){
		event.preventDefault();
		
		if ( uploader_frame ) {
			uploader_frame.open();
			return;
		}
		 
		uploader_frame = wp.media.frames.uploader_frame = wp.media({
			title: "Set featured image",
			button: { text: 'Set featured image' },
			multiple: false
		});
		// open
		uploader_frame.on('open',function() {
			var attachment_id = $("#bsk_pdf_manager_thumbnail_id_ID").val();
			if( attachment_id < 1 ){
				return;
			}
			// set selection
			// set selection
			var selection = uploader_frame.state().get('selection'),
			attachment = wp.media.model.Attachment.get( attachment_id );
			attachment.fetch();
			selection.reset( attachment ? [ attachment ] : [] );
		});
			
		uploader_frame.on( 'select', function() {
			attachment = uploader_frame.state().get('selection').first().toJSON();
			// Do something with attachment.id and/or attachment.url here
			//alert(attachment.url);
			//alert( attachment.id );
			$("#bsk_pdf_manager_set_featured_image_anchor_ID").html( 'Only support in Pro version' );
		});
		
		uploader_frame.on( 'close', function() {
		});
		 
		// Finally, open the modal
		uploader_frame.open();
	});

	/*Settings*/
	//var uploader_frame;
	
	$('#bsk_pdf_manager_set_default_featured_image_anchor_ID').click(function( event ){
		event.preventDefault();
		
		var uploader_frame;
		/*if ( uploader_frame ) {
			uploader_frame.open();
			return;
		}
		 */
		uploader_frame = wp.media.frames.uploader_frame = wp.media({
			title: "Set default featured image",
			button: { text: 'Set default featured image' },
			multiple: false
		});
		// open
		uploader_frame.on('open',function() {
			var attachment_id = $("#bsk_pdf_manager_default_thumbnail_id_ID").val();
			if( attachment_id < 1 ){
				return;
			}
			// set selection
			var selection	=	wp.media.frame.state().get('selection'),
				 attachment	=	wp.media.attachment( attachment_id );

			// to fetch or not to fetch
			if( $.isEmptyObject(attachment.changed) )
			{
				attachment.fetch();
			}
			selection.add( attachment );
		});
			
		uploader_frame.on( 'select', function() {
			attachment = uploader_frame.state().get('selection').first().toJSON();
			// Do something with attachment.id and/or attachment.url here
			//alert(attachment.url);
			//alert( attachment.id );
			bsk_pdf_manager_set_default_thumbnail( attachment.id );
		});
		
		uploader_frame.on( 'close', function() {
		});
		 
		// Finally, open the modal
		uploader_frame.open();
	});
    
    $("#bsk_pdfm_generate_thumbnail_chk_ID").click( function(){
        var is_checked = $(this).is( ":checked" );
        if( is_checked ) {
            $("#bsk_pdfm_featured_image_uploader_ID").css( "display", "none" );
            $("#bsk_pdfm_featured_image_generate_settings_ID").css( "display", "block" );
        }else{
            $("#bsk_pdfm_featured_image_uploader_ID").css( "display", "block" );
            $("#bsk_pdfm_featured_image_generate_settings_ID").css( "display", "none" );
        }
    })
	
	function bsk_pdf_manager_set_default_thumbnail( thumbnail_id_to_set ){
		$("#bsk_pdf_manager_set_default_featured_image_ajax_loader_ID").css("display", "inline-block");
		var nonce_val = $("#bsk_pdf_manager_settings_page_ajax_nonce_ID").val();
        var size_val = $("#bsk_pdf_manager_default_thumbnail_size_ID").val();
        var size_dimission = '';
        if( size_val ){
            size_dimission = $("#bsk_pdfm_size_dimission_" + size_val + "_ID" ).val();
        }
		var data = 
				{ 
				  action: 'bsk_pdf_manager_settings_get_default_featured_image', 
				  nonce: nonce_val,
				  thumbnail_id: thumbnail_id_to_set,
                  size: size_val
				};
				
		$.post(ajaxurl, data, function(response) {
			$("#bsk_pdf_manager_set_default_featured_image_ajax_loader_ID").css("display", "none");
			if( response.indexOf('ERROR') != -1 ){
				alert( response );
				return false;
			}
            if( size_dimission ){
                var size_dimission_array = size_dimission.split('_');
                $("#postimagediv .inside").css("width", size_dimission_array[0]);
            }
			$("#bsk_pdf_manager_default_thumbnail_id_ID").val( thumbnail_id_to_set );
			$("#bsk_pdf_manager_set_default_featured_image_anchor_ID").html(response);
            $("#bsk_pdf_manager_set_default_featured_image_anchor_ID").blur();
			$("#bsk_pdf_manger_default_image_icon_container_ID").css("display", "none");
			$("#bsk_pdf_manager_remove_default_featured_image_anchor_ID").css("display", "inline-block");
		});
	}
	
	$("#bsk_pdf_manager_remove_default_featured_image_anchor_ID").click(function(){
		$("#bsk_pdf_manager_default_thumbnail_id_ID").val( "" );
		$("#bsk_pdf_manager_set_default_featured_image_anchor_ID").html( "Change default featured image" );
		$("#bsk_pdf_manger_default_image_icon_container_ID").css("display", "block");
		$("#bsk_pdf_manager_remove_default_featured_image_anchor_ID").css("display", "none");
	});
	
	$("#bsk_pdf_manager_pdfs_categories_change_cancel_id").click(function(){
		$("#bsk_pdf_manager_action_id").val("");
		$("#bsk_pdf_manager_pdfs_change_category_form_id").submit();
	});
    
    $("#bsk_pdf_manager_pdfs_tags_change_cancel_id").click(function(){
		$("#bsk_pdf_manager_action_id").val("");
		$("#bsk_pdf_manager_pdfs_change_tag_form_id").submit();
	});
    
    /*
      * bulk change date
      */
    $(".bsk-pdfm-bulk-change-date-way-raido").click(function(){
        var date_way = $("input[name='bsk_pdfm_bulk_change_date_way_raido']:checked").val();
        var system_current = $(".bsk-pdfm-bulk-change-date-list-table").find( ".bsk-pdfm-bulk-change-date-current-server-date-time" ).val();
        
        $(".bsk-pdfm-bulk-change-date-list-table tbody tr").each( function(){
            var date = '';
            var time_h = '';
            var time_m = '';
            var time_s = '';
            if( date_way == 'Current_Date' ){
                date = system_current.substr( 0, 10 );
                $(this).find(".bsk-pdfm-date-time-date").val( date );
            }else if( date_way == 'Current_Date_Time' ){
                date = system_current.substr( 0, 10 );
                time_h = system_current.substr( 11, 2 );
                time_m = system_current.substr( 14, 2 );
                time_s = system_current.substr( 17, 2 );
                
                $(this).find(".bsk-pdfm-date-time-date").val( date );
                $(this).find(".bsk-pdfm-date-time-hour").val( time_h );
                $(this).find(".bsk-pdfm-date-time-minute").val( time_m );
                $(this).find(".bsk-pdfm-date-time-second").val( time_s );
            }else if( date_way == 'Document_Date_Time' ){
                var document_date_time = $(this).find(".bsk-pdfm-bulk-change-date-document-self").val();
                date = document_date_time.substr( 0, 10 );
                time_h = document_date_time.substr( 11, 2 );
                time_m = document_date_time.substr( 14, 2 );
                time_s = document_date_time.substr( 17, 2 );
                
                $(this).find(".bsk-pdfm-date-time-date").val( date );
                $(this).find(".bsk-pdfm-date-time-hour").val( time_h );
                $(this).find(".bsk-pdfm-date-time-minute").val( time_m );
                $(this).find(".bsk-pdfm-date-time-second").val( time_s );
            }
        });
    });
    
    $("#bsk_pdf_manager_pdfs_date_change_cancel_id").click(function(){
		$("#bsk_pdf_manager_action_id").val("");
		$("#bsk_pdf_manager_pdfs_bulk_change_date_form_id").submit();
	});
    
    $("#bsk_pdf_manager_pdfs_title_change_cancel_id").click(function(){
		$("#bsk_pdf_manager_action_id").val("");
		$("#bsk_pdf_manager_pdfs_bulk_change_title_form_id").submit();
	});
    
    $(".bsk-pdfm-bactch-update-category-checkbox").click(function(){
        var category = new Array();
        $('input[name="bsk_pdf_manager_pdfs_categories_to_manager[]"]:checked').each(function() {
		   category.push($(this).val());
		});
		if( category.length < 1 ){
            $("#bsk_pdfm_batch_update_category_choose_error_message_ID").css("display", "block");
			$("#bsk_pdfm_batch_update_category_choose_error_message_ID").html( 'Please check at least one category' );
		}else{
            $("#bsk_pdfm_batch_update_category_choose_error_message_ID").css("display", "none");
        }
    });
    
    /*
     * bulk delete
     */
    $(".bsk-pdfm-bulk-delete-pdfs-featured-image-all").click( function() {
        var is_checked = $(this).is( ":checked" );
        if( is_checked ){
            $("#bsk_pdf_manager_pdfs_bulk_delete_form_id").find(".bsk-pdfm-bulk-delete-pdfs-featured-image").prop( "checked", true );
        }else{
            $("#bsk_pdf_manager_pdfs_bulk_delete_form_id").find(".bsk-pdfm-bulk-delete-pdfs-featured-image").prop( "checked", false );
        }
    });
    
    $("#bsk_pdf_manager_pdfs_bulk_delete_cancel_id").click(function(){
		$("#bsk_pdf_manager_action_id").val("");
		$("#bsk_pdf_manager_pdfs_bulk_delete_form_id").submit();
	});
	
    $("#bsk_pdf_manager_pdfs_bulk_delete_submit_id").click(function(){        
		$("#bsk_pdf_manager_action_id").val("bulk_delete");
		$("#bsk_pdf_manager_pdfs_bulk_delete_form_id").submit();
	});
    
    /*
      * bulk generate thumb
      */

	$("#bsk_pdf_manager_pdfs_generate_thumb_cancel_id").click(function(){
		$("#bsk_pdf_manager_action_id").val("");
		$("#bsk_pdf_manager_pdfs_bulk_generate_thumb_form_id").submit();
	});
	
    /*
     * Add by FTP
     */
    $(".bsk-pdfm-ftp-exclude-extension-raido, .bsk-pdfm-ftp-replace-underscroe-raido, .bsk-pdfm-ftp-replace-hyphen-raido").change(function(){
        var exclude_extension = $("input[name='bsk_pdfm_ftp_exclude_extension_raido']:checked").val();
        var replace_underscore = $("input[name='bsk_pdfm_ftp_replace_underscroe_raido']:checked").val();
        var replace_hyphen = $("input[name='bsk_pdfm_ftp_replace_hyphen_raido']:checked").val();
        
        $(".bsk-pdfm-ftp-files-list-table tbody tr").each( function(){
            var title = $(this).find( ".bsk-pdf-manager-ftp-title-hidden" ).val();
            var title_array = title.split( '.' );
            var new_title = title;

            if( exclude_extension == 'NO' ){
                var ext = $(this).find( ".bsk-pdf-manager-ftp-extension-val" ).val();
                new_title = new_title + '.' + ext;
            }
            if( replace_underscore == 'YES' ){
                new_title = new_title.replace( /_/g, ' ' );
            }
            if( replace_hyphen == 'YES' ){
                new_title = new_title.replace( /-/g, ' ' );
            }
            
            $(this).find( ".bsk-pdf-manager-ftp-title-input" ).val( new_title );
        });
    });
    
    $(".bsk-pdfm-ftp-date-way-raido").click(function(){
        var date_way = $("input[name='bsk_pdfm_ftp_date_way_raido']:checked").val();
        var system_current = $(".bsk-pdfm-ftp-files-list-table").find( ".bsk-pdf-manager-ftp-current-server-date-time" ).val();
        
        $(".bsk-pdfm-ftp-files-list-table tbody tr").each( function(){
            var date = '';
            var time_h = '';
            var time_m = '';
            var time_s = '';
            if( date_way == 'Last_Modify' ){
                var last_modify = $(this).find( ".bsk-pdf-manager-ftp-last-modify-datetime" ).val();
                date = last_modify.substr( 0, 10 );
                time_h = last_modify.substr( 11, 2 );
                time_m = last_modify.substr( 14, 2 );
                time_s = last_modify.substr( 17, 2 );
            }else if( date_way == 'Current' ){
                date = system_current.substr( 0, 10 );
                time_h = system_current.substr( 11, 2 );
                time_m = system_current.substr( 14, 2 );
                time_s = system_current.substr( 17, 2 );
            }
            $(this).find(".bsk-pdfm-date-time-date").val( date );
            $(this).find(".bsk-pdfm-date-time-hour").val( time_h );
            $(this).find(".bsk-pdfm-date-time-minute").val( time_m );
            $(this).find(".bsk-pdfm-date-time-second").val( time_s );
        });
    });
	$("#bsk_pdf_manager_add_by_ftp_save_button_ID").click(function(){
		//check selected files
		var selected_files = new Array();
		$('input[name="bsk_pdf_manager_ftp_files[]"]:checked').each(function() {
		   selected_files.push($(this).val());
		});
		if( selected_files.length < 1 ){
			alert( 'Please check at least one file' );
			return false;
		}
		
		//check category
		var category = new Array();
		$('input[name="bsk_pdf_manager_ftp_categories[]"]:checked').each(function() {
		   category.push($(this).val());
		});
		if( category.length < 1 ){
			alert( 'Please check at least one category' );
			return false;
		}
		
		$("#bsk-pdf-manager-add-by-ftp-form-id").submit();
	});
    
    $(".bsk-pdfm-ftp-generate-thumbnail-chk").click( function(){
        var is_checked = $(this).is( ":checked" );
        var td_container = $(this).parents( ".bsk-pdfm-ftp-generte-thumb-td" );
        if( is_checked ) {
            td_container.find( ".bsk-pdfm-ftp-generate-thumbnail-settings" ).css( "display", "block" );
        }else{
            td_container.find( ".bsk-pdfm-ftp-generate-thumbnail-settings" ).css( "display", "none" );
        }
    })
    
    $(".bsk-pdfm-ftp-generate-pdfs-featured-image-all").click( function() {
        var is_checked = $(this).is( ":checked" );
        var table_container = $(this).parents( ".bsk-pdfm-ftp-files-list-table" );
        var maximum_to_generate_thumb = $(".bsk-pdfm-ftp-generate-thumb-max").val();
        
        var amount_checked_to_generate = 0;
        table_container.find(".bsk-pdfm-ftp-generate-thumbnail-chk").each( function( index, element ){
            if( $(this).prop( "disabled" ) == true ){
               return;
            }
            amount_checked_to_generate++;
            if( is_checked ){
                $(this).prop( "checked", true );
                $(this).parents( ".bsk-pdfm-ftp-generte-thumb-td" ).find(".bsk-pdfm-ftp-generate-thumbnail-settings" ).css( "display", "block" );
                if( amount_checked_to_generate > maximum_to_generate_thumb ){
                    $(this).parents( "tr" ).css( "display", "none" );
                    $(this).parents( "tr" ).find( ".bsk-pdfm-ftp-skips-hidden-val" ).val( 1 );
                }
            }else{
                $(this).prop( "checked", false );
                $(this).parents( ".bsk-pdfm-ftp-generte-thumb-td" ).find(".bsk-pdfm-ftp-generate-thumbnail-settings" ).css( "display", "none" );
                $(this).parents( "tr" ).css( "display", "table-row" );
                $(this).parents( "tr" ).find( ".bsk-pdfm-ftp-skips-hidden-val" ).val( 0 );
            }
        });
    });
	
	
	/*
     * featured image setting
     */
    $("#bsk_pdf_manager_enable_featured_image_ID").click(function(){
		var is_checked = $(this).prop('checked');
		if ( is_checked ) {
			$("#bsk_pdf_manager_featured_image_settings_containder_ID").css( "display", "block" );
		} else {
			$("#bsk_pdf_manager_featured_image_settings_containder_ID").css( "display", "none" );
		}
	});

    //var uploader_frame;
	
	$( "#bsk_pdf_manager_featured_image_settings_containder_ID" ).on( 'click', '.bsk-pdfm-upload-featured-image, .bsk-pdfm-settings-featured-image-wrap', function( event ){
		event.preventDefault();

        var anchor_parent_tr = $( this ).parents( 'tr' );
		
		var uploader_frame;
		/*if ( uploader_frame ) {
			uploader_frame.open();
			return;
		}
		 */
		uploader_frame = wp.media.frames.uploader_frame = wp.media({
			title: "Set default featured image",
			button: { text: 'Set default featured image' },
			multiple: false
		});
		// open
		uploader_frame.on('open',function() {
			var attachment_id = anchor_parent_tr.find( ".bsk-pdfm-featured-image-thumbnail-id" ).val();
			if( attachment_id < 1 ){
				return;
			}
			// set selection
			var selection	=	wp.media.frame.state().get('selection'),
				 attachment	=	wp.media.attachment( attachment_id );

			// to fetch or not to fetch
			if( $.isEmptyObject(attachment.changed) )
			{
				attachment.fetch();
			}
			selection.add( attachment );
		});
			
		uploader_frame.on( 'select', function() {
			attachment = uploader_frame.state().get('selection').first().toJSON();
			// Do something with attachment.id and/or attachment.url here
			//alert(attachment.url);
			//alert( attachment.id );
			bsk_pdf_manager_set_default_thumbnail( attachment.id, anchor_parent_tr );
		});
		
		uploader_frame.on( 'close', function() {
		});
		 
		// Finally, open the modal
		uploader_frame.open();
	});
	
	function bsk_pdf_manager_set_default_thumbnail( thumbnail_id_to_set, table_tr_obj ){
		table_tr_obj.find( ".bsk-pdf-manager-set-default-featured-image-ajax-loader" ).css("display", "inline-block");
		var nonce_val = $("#bsk_pdf_manager_settings_page_ajax_nonce_ID").val();
		var data = 
				{ 
				  action: 'bsk_pdf_manager_settings_get_default_featured_image', 
				  nonce: nonce_val,
				  thumbnail_id: thumbnail_id_to_set,
				};
				
		$.post(ajaxurl, data, function(response) {
			table_tr_obj.find( ".bsk-pdf-manager-set-default-featured-image-ajax-loader" ).css("display", "none");
			if( response.indexOf('ERROR') != -1 ){
				alert( response );
				return false;
			}

			table_tr_obj.find( ".bsk-pdfm-featured-image-thumbnail-id" ).val( thumbnail_id_to_set );
			table_tr_obj.find( ".bsk-pdfm-featured-image-container" ).find( "div" ).html( response );
            table_tr_obj.find( '.bsk-pdfm-featured-image-size' ).css( "display", "block" );

            var option_obj = table_tr_obj.find( '.bsk-pdfm-featured-image-size' ).find( 'option:selected' );
            var width = option_obj.data( 'width' );
            var height = option_obj.data( 'height' );
            var crop = option_obj.data( 'crop' );
            if ( width && height && crop ) {
                table_tr_obj.find( ".bsk-pdfm-featured-image-size-details" ).html( 'Width: ' + width + ' px, Height: ' + height + ' px, ' + crop );
            } else {
                table_tr_obj.find( ".bsk-pdfm-featured-image-size-details" ).html( '' );
            }

            table_tr_obj.find( '.bsk-pdfm-featured-image-size-details' ).css( "display", "block" );
            table_tr_obj.find( ".bsk-pdfm-upload-featured-image" ).blur();
            table_tr_obj.find( ".bsk-pdfm-upload-featured-image" ).html( 'Update' );
            table_tr_obj.find( ".bsk-pdfm-remove-featured-image" ).css( "display", "inline-block" );
		});
	}
	
	$(".bsk-pdfm-remove-featured-image").click(function(){
        
        var table_tr_obj = $( this ).parents( 'tr' );

        var default_pdf_icon_url = $( "#bsk_pdf_manager_default_pdf_icon_url" ).val();

		table_tr_obj.find( ".bsk-pdfm-featured-image-thumbnail-id" ).val( '' );

        if ( $( this ).hasClass( 'bsk-pdfm-featured-image-by-file-type' ) ) {
            table_tr_obj.find( ".bsk-pdfm-featured-image-container" ).find( "div" ).html( '' );
            table_tr_obj.find( '.bsk-pdfm-featured-image-size' ).css( "display", "none" );
            table_tr_obj.find( '.bsk-pdfm-featured-image-size-details' ).css( "display", "none" );
        } else {
            //default for all file types
            table_tr_obj.find( ".bsk-pdfm-featured-image-container" ).find( "div" ).html( '<a href="javascript:void(0);" class="bsk-pdfm-settings-featured-image-wrap"><img src="' + default_pdf_icon_url + '" style="width: 60px;" /></a>' );
        }
        table_tr_obj.find( ".bsk-pdfm-upload-featured-image" ).html( 'Set' );

        $( this ).css( "display", "none" );
	});

    $( ".bsk-pdfm-featured-image-size" ).change( function() {
        var selected = $(this).find( 'option:selected' );
        var width = selected.data( 'width' );
        var height = selected.data( 'height' );
        var crop = selected.data( 'crop' );

        if ( width && height && crop ) {
            $( this ).parent().find( ".bsk-pdfm-featured-image-size-details" ).html( 'Width: ' + width + ' px, Height: ' + height + ' px, ' + crop );
        } else {
            $( this ).parent().find( ".bsk-pdfm-featured-image-size-details" ).html( '' );
        }
    })
	
    $( "#bsk_pdfm_enable_featured_imagge_by_file_type_ID" ).click(function(){
        var is_checked = $( "#bsk_pdfm_enable_featured_imagge_by_file_type_ID" ).is( ":checked" );
        if ( is_checked ) {
            $( "#bsk_pdfm_set_featured_image_by_file_type" ).css( "display", "block" );
        } else {
            $( "#bsk_pdfm_set_featured_image_by_file_type" ).css( "display", "none" );
        }
    });
	
    //register image size
	$("#bsk_pdfm_register_image_sizes_save_form_ID").click(function(){
		var register_size_name_1 = $("#bsk_pdf_manager_register_image_size_name_1_ID").val();
		var register_size_width_1 = $("#bsk_pdf_manager_register_image_size_width_1_ID").val();
		var register_size_height_1 = $("#bsk_pdf_manager_register_image_size_height_1_ID").val();
		
		register_size_name_1 = $.trim( register_size_name_1 );
		if( register_size_name_1 ){
			if( register_size_width_1 < 1 ){
				alert( "Invalid width" );
				$("#bsk_pdf_manager_register_image_size_width_1_ID").focus();
				return false;
			}
			if( register_size_height_1 < 1 ){
				alert( "Invalid height" );
				$("#bsk_pdf_manager_register_image_size_height_1_ID").focus();
				return false;
			}
		}else if( register_size_width_1 || register_size_height_1 ){
			alert( "Please enter name" );
			$("#bsk_pdf_manager_register_image_size_name_1_ID").focus();
			return false;
		}
		
		var register_size_name_2 = $("#bsk_pdf_manager_register_image_size_name_2_ID").val();
		var register_size_width_2 = $("#bsk_pdf_manager_register_image_size_width_2_ID").val();
		var register_size_height_2 = $("#bsk_pdf_manager_register_image_size_height_2_ID").val();
		register_size_name_2 = $.trim( register_size_name_2 );
		if( register_size_name_2 ){
			if( register_size_name_2 == register_size_name_1 ){
				alert( "Same size name is not allowed" );
				$("#bsk_pdf_manager_register_image_size_name_2_ID").focus();
				return false;
			}
			if( register_size_width_2 < 1 ){
				alert( "Invalid width" );
				$("#bsk_pdf_manager_register_image_size_width_2_ID").focus();
				return false;
			}
			if( register_size_height_2 < 1 ){
				alert( "Invalid height" );
				$("#bsk_pdf_manager_register_image_size_height_2_ID").focus();
				return false;
			}
		}else if( register_size_width_2 || register_size_height_2 ){
			alert( "Please enter name" );
			$("#bsk_pdf_manager_register_image_size_name_2_ID").focus();
			return false;
		}

		$("#bsk_pdfm_register_image_sizes_form_ID").submit();
	});

    $( "#bsk_pdf_manager_settings_featured_image_tab_save_form_ID" ).click( function() {
        $("#bsk_pdfm_featured_image_settings_form_ID").submit();
    });
	
	/* general settings */
	$("#bsk_pdf_manager_settings_general_tab_save_form_ID").click(function(){
		$("#bsk_pdfm_general_settings_form_ID").submit();
	});
	
	/* multi-column settings */
	$("#bsk_pdf_manager_settings_styles_save_form_ID").click(function(){
		$("#bsk_pdfm_styles_form_ID").submit();
	});
	
	$("#bsk_pdfm_multi_column_layout_enable_ID").click(function(){
		var is_checked = $(this).is(':checked');
		
		if( is_checked ){
			$("#bks_pdfm_multi_column_enabled_settings_container_ID").css("display", "block");
		}else{
			$("#bks_pdfm_multi_column_enabled_settings_container_ID").css("display", "none");
		}
	});
	
	/* settings tab switch */
	$("#bsk_pdfm_setings_wrap_ID .nav-tab-wrapper a").click(function(){
		//alert( $(this).index() );
		$('#bsk_pdfm_setings_wrap_ID section').hide();
		$('#bsk_pdfm_setings_wrap_ID section').eq($(this).index()).show();
		
		$(".nav-tab").removeClass( "nav-tab-active" );
		$(this).addClass( "nav-tab-active" );
		
		return false;
	});
	//settings target tab
	if( $("#bsk_pdfm_settings_target_tab_ID").length > 0 ){
		var target = $("#bsk_pdfm_settings_target_tab_ID").val();
		if( target ){
			$("#bsk_pdfm_setings_tab-" + target).click();
		}
	}
	/* help tab switch */
	$("#bsk_pdfm_help_wrap_ID .nav-tab-wrapper a").click(function(){
		//alert( $(this).index() );
		$('#bsk_pdfm_help_wrap_ID section').hide();
		$('#bsk_pdfm_help_wrap_ID section').eq($(this).index()).show();
		
		$(".nav-tab").removeClass( "nav-tab-active" );
		$(this).addClass( "nav-tab-active" );
		
		return false;
	});
    $("#bsk_pdfm_help_tab-quick-start").click();
    
	/*
	 * Upload pdf from way
	 */
	$(".bsk-pdfm-upload-from-radio").click( function(){
		var upload_way = $('input[name="bsk_pdfm_upload_from"]:checked').val();
		if( upload_way == 'computer' ){
			$("#bsk_pdfm_upload_from_computer_row_ID").css( "display", "block" );
			$("#bsk_pdfm_upload_from_media_library_row_ID").css( "display", "none" );
            $("#bsk_pdfm_upload_to_row_ID").css( "display", "block" );
		}else if( upload_way == 'media_library' ){
			$("#bsk_pdfm_upload_from_computer_row_ID").css( "display", "none" );
			$("#bsk_pdfm_upload_from_media_library_row_ID").css( "display", "block" );
            $("#bsk_pdfm_upload_to_row_ID").css( "display", "none" );
		}
	} );
	
	$("#bsk_pdf_manager_upload_pdf_anchor_ID").on("click", function( event ){
        var supported_extension_and_mime = bsk_pdfm_admin.extension_and_mime;
        
		var uploader_frame;
		/*if ( uploader_frame ) {
			uploader_frame.open();
			return;
		}*/
		 
		uploader_frame = wp.media.frames.uploader_frame = wp.media({
			title: "Set PDF Document",
			button: { text: 'Set PDF Document' },
			multiple: false
		});
		// open
		uploader_frame.on('open',function() {
			var attachment_id = $("#bsk_pdf_upload_attachment_id_ID").val();
			if( attachment_id < 1 ){
				return;
			}
			// set selection
			// set selection
			var selection = uploader_frame.state().get('selection'),
			attachment = wp.media.model.Attachment.get( attachment_id );
			attachment.fetch();
			selection.reset( attachment ? [ attachment ] : [] );
		});
			
		uploader_frame.on( 'select', function() {
			$("#bsk_pdf_manager_upload_pdf_anchor_ID").html( "Only support in Pro version" );
		    $("#bsk_pdf_manager_upload_pdf_anchor_ID").toggleClass( 'button-secondary' );
		});
		
		uploader_frame.on( 'close', function() {
		});
		 
		// Finally, open the modal
		uploader_frame.open();
	});
	
    
    /* *
      * Settings page
      */
    $("#bsk_pdfm_set_upload_folder_ID").click(function(){
        var is_checked = $(this).is(":checked");
        if( is_checked ){
            $("#bsk_pdfm_set_upload_folder_hint_text_ID").css("display", "block");
            $("#bsk_pdfm_set_upload_folder_input_ID").css("display", "block");
            $("#bsk_pdf_upload_folder_tree").css("display", "block");
            if( $("#bsk_pdf_upload_folder_tree_root_label_ID").length > 0 ){
                var node_root = $("#bsk_pdf_upload_folder_tree").jstree().get_node( 'j1_1' );
                $("#bsk_pdf_upload_folder_tree").jstree('rename_node', node_root, $("#bsk_pdf_upload_folder_tree_root_label_ID").val() );
            }
        }else{
            $("#bsk_pdfm_set_upload_folder_hint_text_ID").css("display", "none");
            $("#bsk_pdfm_set_upload_folder_input_ID").css("display", "none");
            $("#bsk_pdf_upload_folder_tree").css("display", "none");
        }
    });
    
    $("#bsk_pdf_upload_folder_tree").on("changed.jstree", function (e, data) {
        if(data.selected.length) {
            if( $("#bsk_pdf_upload_folder_tree_root_relative_path").length > 0 && data.selected[0] == "j1_1" ){
                //for multiple site not Super Admin
                $("#bsk_pdfm_set_upload_folder_path_ID").html( $("#bsk_pdf_upload_folder_tree_root_relative_path").val() );
                $("#bsk_pdfm_set_upload_folder_path_val_ID").val( $("#bsk_pdf_upload_folder_tree_root_relative_path").val() );
            }else{
                $("#bsk_pdfm_set_upload_folder_path_ID").html( data.instance.get_node(data.selected[0]).li_attr.relative_path );
                $("#bsk_pdfm_set_upload_folder_path_val_ID").val( data.instance.get_node(data.selected[0]).li_attr.relative_path );
            }
        }
    });
    $("#bsk_pdf_upload_folder_tree").jstree(
        {
        'core': {
                    'check_callback': true,
                    /// rest of the options...
                 }
        }
    );

    $("#bsk_pdfm_set_upload_folder_sub_ID").keyup( function(){
        //only number & letters
        this.value = this.value.replace(/[^a-zA-z0-9\-]/g, '');
    });
    
    //credit setting
    $("#bsk_pdf_manaer_credit_link_enable_ID").click(function(){
        var is_checked = $(this).is(":checked");
        if( is_checked ){
            $("#bsk_pdf_manager_credit_text_ID").css( "display", "block" );
            $("#bsk_pdf_manager_credit_example_ID").css( "display", "block" );
        }else{
            $("#bsk_pdf_manager_credit_text_ID").css( "display", "none" );
            $("#bsk_pdf_manager_credit_example_ID").css( "display", "none" );
        }
    });
    
    $("#bsk_pdf_manaer_credit_link_text_ID").keyup(function(){
        var link_text = $(this).val();
        link_text = $.trim( link_text );
        if( link_text == "" ){
            link_text = 'PDFs powered by PDF Manager Pro';
        }
        $("#bsk_pdfm_credit_link_ID").html( link_text );
    });
    
    
    /*
      * widget
      */
    
    //control featured image size & PDF title
    $("#wpbody-content").on("click", ".bsk-pdfm-widget-show-thumbnail", function(){
		if( $(this).is(":checked") ){
			$(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-widget-thumbnail-size-p").css( "display", "block" );
            $(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-widget-thumbnail-with-pdf-title-p").css( "display", "block" );
		}else{
			$(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-widget-thumbnail-size-p").css( "display", "none" );
            $(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-widget-thumbnail-with-pdf-title-p").css( "display", "none" );
		}
	});
    
    //control PDFs ID input
    $("#wpbody-content").on("click", ".bsk-pdfm-show-all-or-specific-pdfs", function(){
		if( $(this).val() == 'SPECIFIC' ){
			$(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-widget-specific-ids-input-p").css( "display", "block" );
            
            $(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-order-by > option").show();
		}else{
			$(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-widget-specific-ids-input-p").css( "display", "none" );
            //get order by
            if( $(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-order-by").val() == 'IDS_SEQUENCE' ){
                $(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-order-by > option").each(function(){
                    if( $(this).val() == 'IDS_SEQUENCE' ){
                        $(this).hide();
                    }
                });
                $(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-order-by").val( "title" );
            }
		}
	});
    
    $("#wpbody-content").on("keyup", ".bsk-pdfm-ids-input", function(){
        //only number and .
        this.value = this.value.replace(/[^0-9\,]/g, '');
        //first must be number
        this.value = this.value.replace(/^\,/g, '');
        //.. is not allowed
        this.value = this.value.replace(/\,{2,}/g, ',');
    });
    
    $("#wpbody-content").on("focusout", ".bsk-pdfm-ids-input", function(){
        //if the last one is . then remove it number and .
        this.value = this.value.replace(/\,$/g, ''); 
    });
    
    //control date format string & date before title option
    $("#wpbody-content").on("click", ".bsk-pdfm-widget-show-date", function(){
		if( $(this).is(":checked") ){
			$(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-widget-date-format-p").css( "display", "block" );
            $(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-widget-date-before-title-p").css( "display", "block" );
		}else{
			$(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-widget-date-format-p").css( "display", "none" );
            $(this).parents(".bsk-pdfm-widget-setting-container").find(".bsk-pdfm-widget-date-before-title-p").css( "display", "none" );
		}
	});
    
    /*
	 * Buld add pdf by wordpress Media
	 */
    
    $(".bsk-pdfm-bulk-add-by-media-library-files-list-table").on("focus", '.bsk-date', function(){
         $(this).datepicker( {dateFormat : 'yy-mm-dd'} );
    });
    
    function convert_datetime_to_other_timezone( current_datetime, new_time_zone_offset ) {

        // convert to msec
        // add local time zone offset 
        // get UTC time in msec
        utc = current_datetime.getTime() + (current_datetime.getTimezoneOffset() * 60000);

        // create new Date object for different city
        // using supplied offset
        new_datetime = new Date( utc + (3600000*new_time_zone_offset) );

        return new_datetime;

    }
    
    $("#bsk_pdfm_bulk_add_by_media_library_anchor_ID").on("click", function( event ){
        var supported_extension_and_mime = bsk_pdfm_admin.extension_and_mime;
        var supported_mime_type = $(".bsk-pdfm-bulk-add-by-meida-librry-allowed-mime-type").val();
        var supported_mime_type_array = supported_mime_type.split( ',' );
        
		var bulk_add_uploader_frame;
		/*if ( uploader_frame ) {
			uploader_frame.open();
			return;
		}*/
		 
		bulk_add_uploader_frame = wp.media.frames.uploader_frame = wp.media({
			title: "Select Documents",
			button: { text: 'Select Documents' },
			multiple: true,
            library: {
                type: supported_mime_type_array
            },
		});
		// open
		bulk_add_uploader_frame.on('open',function() {
			//
		});
			
		bulk_add_uploader_frame.on( 'select', function() {
			//attachment = uploader_frame.state().get('selection').first().toJSON();
			// Do something with attachment.id and/or attachment.url here
			//alert(attachment.url);
			//alert( attachment.id );
            //console.log( attachment );
            //console.log( attachment.date );
            
            selected_documents = bulk_add_uploader_frame.state().get('selection');
            if( selected_documents.length < 1 ){
                return;
            }
            
            var wp_gmt_offset = $(".bsk-pdfm-bulk-add-by-meida-library-wp-gmt-offset").val();
            var site_url = $(".bsk-pdfm-bulk-add-by-meida-library-site-url").val();
            
            selected_documents.each( function(element, index){
                var id = element.attributes['id'];
                var title = element.attributes['title'];
                //var filename = element.attributes['filename'];
                var url = element.attributes['url'];
                var date = element.attributes['date'];
                if( typeof date === "object" ){
                    //
                }else{
                    date = new Date( date );
                }
                var utc_date = date.getUTCDate();
                var utc_day = date.getUTCDate();
                
                var converted_time = convert_datetime_to_other_timezone( date, wp_gmt_offset );
                var t_y = converted_time.getFullYear();
                var t_m = converted_time.getMonth() + 1;
                var t_d = converted_time.getDate();
                var t_h = converted_time.getHours();
                var t_min = converted_time.getMinutes();
                var t_s = converted_time.getSeconds();
                
                t_m = t_m < 10 ? ('0' + t_m) : t_m;
                t_d = t_d < 10 ? ('0' + t_d) : t_d;
                t_h = t_h < 10 ? ('0' + t_h) : t_h;
                t_min = t_min < 10 ? ('0' + t_min) : t_min;
                t_s = t_s < 10 ? ('0' + t_s) : t_s;
                
                var t_date = t_y + '-' + t_m + '-' + t_d;
                var t_date_time = t_date + ' ' + t_h + ':' + t_min + ':' + t_s;
                //console.log( date );
                //console.log( t_date_time );
                
                var file_name = url.replace( site_url, '' );
                
                //generate thumb
                var check_to_generate_str = $(".bsk-pdfm-bulk-add-by-meida-library-check_to_generate_str").html();
                var from_the_page_no_str = $(".bsk-pdfm-bulk-add-by-meida-library-from_the_page_no_str").html();
                var disable_generate_thumb = element.attributes['mime'] == 'application/pdf' ? '' : 'disabled';
                
                var page_dropdown_str = '<input type="number" name="bsk_pdfm_media_generate_thumb_chk_page_number['+id+']" value="1" min="1" step="1" style="width: 50px;" />';
                from_the_page_no_str = from_the_page_no_str.replace( '%s', page_dropdown_str );
                
                var row_content = '<tr><td class="check-column" style="padding-left:18px;"><input type="checkbox" name="bsk_pdfm_bulk_add_by_media_documents[]" value="'+id+'" class="bsk-pdfm-bulk-add-by-media-documents-chk" style="padding:0; margin:0;" /></td><td class="bsk-pdfm-bulk-add-by-media-filename">'+file_name+'<input type="hidden" name="bsk_pdfm_bulk_add_by_media_filenames['+id+']" value="'+file_name+'" /></td><td><input type="text" name="bsk_pdfm_bulk_add_by_media_titles['+id+']" value="'+title+'" maxlength="512" style="width: 100%;" class="bsk-pdfm-bulk-add-by-media-title-input" /><input type="hidden" value="'+title+'" class="bsk-pdfm-bulk-add-by-media-title-hidden" /></td><td class="bsk-pdfm-date-time-td"><input type="text" name="bsk_pdfm_bulk_add_by_media_dates['+id+']" value="'+t_date+'" class="bsk-pdfm-date-time-date bsk-date" /><span>@</span><input type="number" name="bsk_pdfm_bulk_add_by_media_dates_hour['+id+']" class="bsk-pdfm-date-time-hour" value="'+t_h+'" min="0" max="23" step="1" /><span>:</span><input type="number" name="bsk_pdfm_bulk_add_by_media_dates_minute['+id+']" class="bsk-pdfm-date-time-minute" value="'+t_min+'" min="0" max="59" step="1" /><span>:</span><input type="number" name="bsk_pdfm_bulk_add_by_media_dates_second['+id+']" class="bsk-pdfm-date-time-second" value="'+t_s+'" min="0" max="59" step="1"  /><span class="bsk-pdfm-add-by-media-library-bulk-parse-date-ajax-loader" style="display:none;"><img src="'+bsk_pdfm_admin.ajax_loader_url+'" /></span><input type="hidden" class="bsk-pdfm-bulk-add-by-media-last-modify-datetime" value="'+t_date_time+'" /></td><td class="bsk-pdfm-media-generte-thumb-td"><p><label><input type="checkbox" name="bsk_pdfm_media_generate_thumb_chk[]" class="bsk-pdfm-media-generate-thumbnail-chk" value="'+id+'" '+disable_generate_thumb+'/> '+check_to_generate_str+'</label><span class="bsk-pdfm-media-generate-thumbnail-settings" style="display: none;">'+from_the_page_no_str+'</span> </p></td><input type="hidden" name="bsk_pdfm_media_skips['+id+']" class="bsk-pdfm-media-skips-hidden-val" value="0" /></tr>';
                
                $("#bsk_pdfm_add_by_media_library_tboday_ID").append( row_content );
            });
            
		});
		
		bulk_add_uploader_frame.on( 'close', function() {
            //
		});
		 
		// Finally, open the modal
		bulk_add_uploader_frame.open();
	});
    
    $(".bsk-pdfm-bulk-add-by-media-library-date-way-raido").click(function(){
        var date_way = $("input[name='bsk_pdfm_bulk_add_by_media_library_date_way_raido']:checked").val();
        var system_current = $( ".bsk-pdfm-bulk-add-by-meida-library-current-server-date-time" ).val();
        var has_parse_executed = $( ".bsk-pdfm-bulk-add-by-media-library-files-list-table" ).find( ".bsk-pdfm-ftp-parsed-datetime" ).length > 0 ? true : false;
        
        $(".bsk-pdfm-parse-date-from-filename-failed-desc").css( "display", "none");
        if( date_way == 'Parsed' && has_parse_executed == false ){
            $(".bsk-pdfm-add-by-media-library-bulk-parse-date-ajax-loader").css( "display", "inline-block" );
        }
        
        $(".bsk-pdfm-bulk-add-by-media-library-files-list-table tbody tr").each( function(){
            var date = '';
            var time_h = '';
            var time_m = '';
            var time_s = '';
            if( date_way == 'Last_Modify' ){
                var last_modify = $(this).find( ".bsk-pdfm-bulk-add-by-media-last-modify-datetime" ).val();
                date = last_modify.substr( 0, 10 );
                time_h = last_modify.substr( 11, 2 );
                time_m = last_modify.substr( 14, 2 );
                time_s = last_modify.substr( 17, 2 );
                
                $(this).find(".bsk-pdfm-date-time-date").val( date );
                $(this).find(".bsk-pdfm-date-time-hour").val( time_h );
                $(this).find(".bsk-pdfm-date-time-minute").val( time_m );
                $(this).find(".bsk-pdfm-date-time-second").val( time_s );
            }else if( date_way == 'Current' ){
                date = system_current.substr( 0, 10 );
                time_h = system_current.substr( 11, 2 );
                time_m = system_current.substr( 14, 2 );
                time_s = system_current.substr( 17, 2 );
                
                $(this).find(".bsk-pdfm-date-time-date").val( date );
                $(this).find(".bsk-pdfm-date-time-hour").val( time_h );
                $(this).find(".bsk-pdfm-date-time-minute").val( time_m );
                $(this).find(".bsk-pdfm-date-time-second").val( time_s );
            }else if( date_way == 'Parsed' ){
                var tr_boj = $(this);
                
                //do nothing
            }
        });
    });
    
	//replace underscore and hyphen in title
    $(".bsk-pdfm-bulk-media-replace-underscroe-raido, .bsk-pdfm-bulk-media-replace-hyphen-raido").change(function(){
        var replace_underscroe = $("input[name='bsk_pdfm_bulk_media_replace_underscroe_raido']:checked").val();
        var replace_hyphen = $("input[name='bsk_pdfm_bulk_media_replace_hyphen_raido']:checked").val();
        
        $(".bsk-pdfm-bulk-add-by-media-library-files-list-table tbody tr").each( function(){
            var title = $(this).find( ".bsk-pdfm-bulk-add-by-media-title-hidden" ).val();
            var new_title = title;

            if( replace_underscroe == 'YES' ){
                new_title = new_title.replace( /_/g, ' ' );
            }
            if( replace_hyphen == 'YES' ){
                new_title = new_title.replace( /-/g, ' ' );
            }
            
            $(this).find( ".bsk-pdfm-bulk-add-by-media-title-input" ).val( new_title );
        });
    });
    
    $(".bsk-pdfm-media-generate-pdfs-featured-image-all").click( function() {
        var is_checked = $(this).is( ":checked" );
        var table_container = $(this).parents( ".bsk-pdfm-bulk-add-by-media-library-files-list-table" );
        var maximum_to_generate_thumb = $(".bsk-pdfm-media-generate-thumb-max").val();
        
        var amount_checked_to_generate = 0;
        table_container.find(".bsk-pdfm-media-generate-thumbnail-chk").each( function( index, element ){
            if( $(this).prop( "disabled" ) == true ){
               return;
            }
            amount_checked_to_generate++;
            if( is_checked ){
                $(this).prop( "checked", true );
                $(this).parents( ".bsk-pdfm-media-generte-thumb-td" ).find(".bsk-pdfm-media-generate-thumbnail-settings" ).css( "display", "block" );
                if( amount_checked_to_generate > maximum_to_generate_thumb ){
                    $(this).parents( "tr" ).css( "display", "none" );
                    $(this).parents( "tr" ).find( ".bsk-pdfm-media-skips-hidden-val" ).val( 1 );
                }
            }else{
                $(this).prop( "checked", false );
                $(this).parents( ".bsk-pdfm-media-generte-thumb-td" ).find(".bsk-pdfm-media-generate-thumbnail-settings" ).css( "display", "none" );
                $(this).parents( "tr" ).css( "display", "table-row" );
                $(this).parents( "tr" ).find( ".bsk-pdfm-media-skips-hidden-val" ).val( 0 );
            }
        });
    });
    
    /*
     * permalink settings
     *
     */
    $( ".bsk-pdfm-permalink-base" ).keyup( function(){
        //only number & letters
        this.value = this.value.replace(/[^0-9a-z-]/g, '');
        
        $( "#bsk_pdfm_permalink_settings_form_ID" ).find( ".bsk-pdfm-permalink-demo-base" ).html( this.value );
    });
    
    $( "#bsk_pdf_manager_enable_permalink_ID" ).click( function(){
        var is_checked = $(this).is( ":checked" );
        if( is_checked ){
            $( "#bsk_pdf_manager_permalink_settings_containder_ID" ).css( "display", "block" );
        }else{
            $( "#bsk_pdf_manager_permalink_settings_containder_ID" ).css( "display", "none" );
        }
    });
    
    $( ".bsk-pdfm-permalink-to-url-global-settings-radio" ).click( function() {
        var setting = $('input[name="bsk_pdf_manager_permalink_redirect"]:checked').val();

        $( "#bsk_pdfm_redirect_permalink_to_url_desc_ID" ).css( "display", "none" );
        $( "#bsk_pdfm_permalink_only_desc_ID" ).css( "display", "none" );
        if ( setting == 'YES' ) {
            $( "#bsk_pdfm_redirect_permalink_to_url_desc_ID" ).css( "display", "block" );
        } else {
            $( "#bsk_pdfm_permalink_only_desc_ID" ).css( "display", "block" );
        }
    });

    $( "#bsk_pdf_manager_settings_permalink_tab_save_form_ID" ).click( function(){
        $( "#bsk_pdfm_permalink_settings_form_ID" ).submit();
    });

    /*
     * new Javascript
     */
    $(".accrodion-container").on( "click", ".accordion-button", function(){
        // Toggle between adding and removing the "active" class,
        // to highlight the button that controls the panel
        var accordion_button = $(this);
        $(this).toggleClass( "accordion-button-active" );
        // Toggle between hiding and showing the active panel
        var accordion_panel = $(this).parent().find( ".accordion-panel" );
        var accordion_panel_display = accordion_panel.css( "display" );
        if( accordion_panel_display === 'block' ){
            accordion_panel.slideUp( "slow", function(){
                accordion_panel.css( "display", "none" );
                accordion_button.removeClass( 'accordion-button-active' );
            } );
        }else{
            accordion_panel.slideDown( "slow", function(){
                accordion_panel.css( "display", "block" );
                accordion_button.addClass( 'accordion-button-active' );
            } );
        }
        
        
        return false;
    });
    
    /*
     * Embedded viewer
     */
    $( "#bsk_pdfm_enable_embedded_viewer_ID" ).click( function() {
        var eanble_embedded_viewer = $( this ).is( ":checked" );

        if ( eanble_embedded_viewer ) {
            $( "#bsk_pdfm_enable_embedded_viewer_settings_mime_type_error_containder_ID" ).css( "display", "block" );
            $( "#bsk_pdfm_enable_embedded_viewer_settings_containder_ID" ).css( "display", "block" );
        } else {
            $( "#bsk_pdfm_enable_embedded_viewer_settings_mime_type_error_containder_ID" ).css( "display", "none" );
            $( "#bsk_pdfm_enable_embedded_viewer_settings_containder_ID" ).css( "display", "none" );
        }
    });

    $( "#bsk_pdfm_embedded_viewer_show_toolbar_ID" ).click( function() {
        var show_tool_bar = $( this ).is( ":checked" );

        if ( show_tool_bar ) {
            $( "#bsk_pdfm_embedded_viewer_toolbar_settings_containder_ID" ).css( "display", "block" );
        } else {
            $( "#bsk_pdfm_embedded_viewer_toolbar_settings_containder_ID" ).css( "display", "none" );
        }
    });
    
});
