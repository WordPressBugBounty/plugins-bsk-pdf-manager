<?php
class BSKPDFM_Dashboard_PDF_List_Action {
    
	public function __construct() {
		
	}
	
    function bulk_action_changecat( $selected_PDFs, $selected_PDFs_to_hidden, $action_url ) {

        $default_enable_permalink = false;
        $default_permalink_base = 'bskpdf';
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
            if( isset($plugin_settings['enable_permalink']) ){
				$default_enable_permalink = $plugin_settings['enable_permalink'];
			}
            
            if( isset($plugin_settings['permalink_base']) ){
				$default_permalink_base = $plugin_settings['permalink_base'];
			}
		}
        
        $permalink_structure = get_option( 'permalink_structure' );
        if ( ! $permalink_structure ) {
            $default_enable_permalink = false;
        }

        ?>
        <div class="wrap">
        <h2><?php esc_html_e( 'Change Category', 'bskpdfmanager' ); ?></h2>
        <?php $this->bsk_pdf_manager_show_pro_tip_box( BSKPDFM_Dashboard::$_pro_tips_for_pdf_bulk_change_category ); ?>
        <form id="bsk_pdf_manager_pdfs_change_category_form_id" method="post" action="<?php echo esc_url($action_url); ?>" enctype="multipart/form-data">
        <?php
        if( $selected_PDFs && is_array($selected_PDFs) && count($selected_PDFs) > 0 ){
        ?>
        <div>
            <h4><?php esc_html_e( 'Choose action to do', 'bskpdfmanager' ); ?></h4>
            <p>
                <label>
                    <input type="radio" name="bsk_pdf_manager_pdfs_change_category_action_to_do" value="add" checked="checked" class="bsk-pdf-manger-pdfs-change-category-action-radio" /><?php esc_html_e( 'Add', 'bskpdfmanager' ); ?>
                </label>
                <label style="margin-left:10px;">
                    <input type="radio" name="bsk_pdf_manager_pdfs_change_category_action_to_do" value="remove" class="bsk-pdf-manger-pdfs-change-category-action-radio" /><?php esc_html_e( 'Remove', 'bskpdfmanager' ); ?>
                </label>
                <label style="margin-left:10px;">
                    <input type="radio" name="bsk_pdf_manager_pdfs_change_category_action_to_do" value="update_to" class="bsk-pdf-manger-pdfs-change-category-action-radio" /><?php esc_html_e( 'Update to', 'bskpdfmanager' ); ?>
                </label>
            </p>
            <h4><?php esc_html_e( 'Choose category to be managed', 'bskpdfmanager' ); ?></h4>
            <p id="bsk_pdfm_batch_update_category_choose_error_message_ID" style="color: #FF0000;"></p>
            <?php
            echo BSKPDFM_Common_Backend::get_category_hierarchy_checkbox( 
                                                                            'bsk_pdf_manager_pdfs_categories_to_manager[]', 
                                                                            'bsk-pdfm-bactch-update-category-checkbox',
                                                                            array()
                                                                        );
            ?>
        </div>
        <?php
        }else{
            echo '<p>'.esc_html_e( 'No PDF items choosen.', 'bskpdfmanager' ).'</p>';
        }
        $_nonce = wp_create_nonce( 'bsk_pdf_manager_bulk_update_pdf_category_nonce' );
        ?>
        <p>
            <input type="hidden" name="_nonce" value="<?php echo esc_attr($_nonce); ?>" />
            <input type="hidden" name="bsk_pdf_manager_action" id="bsk_pdf_manager_action_id" value="" />
            <input type="hidden" name="bsk_pdf_manager_pdf_items_id_hidden" value="<?php echo esc_attr($selected_PDFs_to_hidden); ?>" />
            <input type="button" class="button-primary" value="<?php esc_attr_e( 'Cancel', 'bskpdfmanager' ); ?>" id="bsk_pdf_manager_pdfs_categories_change_cancel_id" />
            <?php if( $selected_PDFs && count($selected_PDFs) > 0 ){ ?>
            <input type="button" class="button-primary" value="<?php esc_attr_e( 'Submit', 'bskpdfmanager' ); ?>" id="bsk_pdf_manager_pdfs_categories_change_submit_id" style="margin-left: 15px;" disabled />
            <?php } ?>
        </p>
        </form>
        </div>
        <?php
    }

    function bulk_action_changetag( $selected_PDFs, $selected_PDFs_to_hidden, $action_url ) {

        $default_enable_permalink = false;
        $default_permalink_base = 'bskpdf';
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
            if( isset($plugin_settings['enable_permalink']) ){
				$default_enable_permalink = $plugin_settings['enable_permalink'];
			}
            
            if( isset($plugin_settings['permalink_base']) ){
				$default_permalink_base = $plugin_settings['permalink_base'];
			}
		}
        
        $permalink_structure = get_option( 'permalink_structure' );
        if ( ! $permalink_structure ) {
            $default_enable_permalink = false;
        }

        ?>
        <div class="wrap">
        <h2><?php esc_html_e( 'Change Tag', 'bskpdfmanager' ); ?></h2>
        <?php $this->bsk_pdf_manager_show_pro_tip_box( BSKPDFM_Dashboard::$_pro_tips_for_pdf_bulk_change_tag ); ?>
        <form id="bsk_pdf_manager_pdfs_change_tag_form_id" method="post" action="<?php echo esc_url($action_url); ?>" enctype="multipart/form-data">
        <?php
        if( $selected_PDFs && is_array($selected_PDFs) && count($selected_PDFs) > 0 ){
        ?>
        <div>
            <h4><?php esc_html_e( 'Choose action to do', 'bskpdfmanager' ); ?></h4>
            <p>
                <label>
                    <input type="radio" name="bsk_pdf_manager_pdfs_change_tag_action_to_do" value="add" checked="checked" class="bsk-pdf-manger-pdfs-change-category-action-radio" /><?php esc_html_e( 'Add', 'bskpdfmanager' ); ?>
                </label>
                <label style="margin-left:10px;">
                    <input type="radio" name="bsk_pdf_manager_pdfs_change_tag_action_to_do" value="remove" class="bsk-pdf-manger-pdfs-change-category-action-radio" /><?php esc_html_e( 'Remove', 'bskpdfmanager' ); ?>
                </label>
                <label style="margin-left:10px;">
                    <input type="radio" name="bsk_pdf_manager_pdfs_change_tag_action_to_do" value="update_to" class="bsk-pdf-manger-pdfs-change-category-action-radio" /><?php esc_html_e( 'Update to', 'bskpdfmanager' ); ?>
                </label>
            </p>
            <h4><?php esc_html_e( 'Choose tag to be managed', 'bskpdfmanager' ); ?></h4>
            <p id="bsk_pdfm_batch_update_tag_choose_error_message_ID" style="color: #FF0000;"></p>
            <?php
            echo BSKPDFM_Common_Backend::get_category_hierarchy_checkbox( 
                                                                            'bsk_pdf_manager_pdfs_categories_to_manager[]', 
                                                                            'bsk-pdfm-bactch-update-category-checkbox',
                                                                            array(),
                                                                            'TAG'
                                                                        );
            ?>
        </div>
        <?php
        }else{
            echo '<p>'.esc_html_e( 'No PDF items choosen.', 'bskpdfmanager' ).'</p>';
        }
        $_nonce = wp_create_nonce( 'bsk_pdf_manager_bulk_update_pdf_category_nonce' );
        ?>
        <p>
            <input type="hidden" name="_nonce" value="<?php echo esc_attr($_nonce); ?>" />
            <input type="hidden" name="bsk_pdf_manager_action" id="bsk_pdf_manager_action_id" value="" />
            <input type="hidden" name="bsk_pdf_manager_pdf_items_id_hidden" value="<?php echo esc_attr($selected_PDFs_to_hidden); ?>" />
            <input type="button" class="button-primary" value="<?php esc_attr_e( 'Cancel', 'bskpdfmanager' ); ?>" id="bsk_pdf_manager_pdfs_tags_change_cancel_id" />
            <?php if( $selected_PDFs && count($selected_PDFs) > 0 ){ ?>
            <input type="button" class="button-primary" value="<?php esc_attr_e( 'Submit', 'bskpdfmanager' ); ?>" id="bsk_pdf_manager_pdfs_tags_change_submit_id" style="margin-left: 15px;" disabled />
            <?php } ?>
        </p>
        </form>
        </div>
    <?php
    }
    
    function bulk_action_changedate( $selected_PDFs, $selected_PDFs_to_hidden, $action_url ) {

        $default_enable_permalink = false;
        $default_permalink_base = 'bskpdf';
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
            if( isset($plugin_settings['enable_permalink']) ){
				$default_enable_permalink = $plugin_settings['enable_permalink'];
			}
            
            if( isset($plugin_settings['permalink_base']) ){
				$default_permalink_base = $plugin_settings['permalink_base'];
			}
		}
        
        $permalink_structure = get_option( 'permalink_structure' );
        if ( ! $permalink_structure ) {
            $default_enable_permalink = false;
        }

        ?>
        <div class="wrap">
            <h2 style="margin-bottom: 20px;"><?php esc_html_e( 'Change Date&amp;Time', 'bskpdfmanager' ); ?></h2>
            <?php $this->bsk_pdf_manager_show_pro_tip_box( BSKPDFM_Dashboard::$_pro_tips_for_pdf_bulk_change_date_time ); ?>
            <form id="bsk_pdf_manager_pdfs_bulk_change_date_form_id" method="post" action="<?php echo esc_url($action_url); ?>" enctype="multipart/form-data">
            <?php
            
            $pdfs_array_to_update = false;
            
            if( $selected_PDFs && is_array($selected_PDFs) && count($selected_PDFs) > 0 ){
                global $wpdb;
                
                $pdfs_id_str = implode( ', ', esc_sql($selected_PDFs) );
                $sql = 'SELECT `id`, `title`, `slug`, `file_name`, `last_date`, `by_media_uploader` '.
                        'FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name).'` '.
                        'WHERE `id` IN('.$pdfs_id_str.')';
                $pdfs_array_to_update = $wpdb->get_results( $sql );
            }
            ?>
            <div>
                <p style="font-weight: bold;"><?php esc_html_e( "Set document's date&amp;time with", 'bskpdfmanager' ); ?>:</p>
                <p>
                    <span class="bsk-pdf-field">
                        <label style="display: block;">
                            <input type="radio" name="bsk_pdfm_bulk_change_date_way_raido" class="bsk-pdfm-bulk-change-date-way-raido" value="Current_Date" /> <?php esc_html_e( "Current server date", 'bskpdfmanager' ); ?>
                        </label>
                        <label style="display: block; margin-top: 10px;">
                            <input type="radio" name="bsk_pdfm_bulk_change_date_way_raido" class="bsk-pdfm-bulk-change-date-way-raido" value="Current_Date_Time" /> <?php esc_html_e( "Current server date&time", 'bskpdfmanager' ); ?>
                        </label>
                        <label style="display: block; margin-top: 10px;">
                            <input type="radio" name="bsk_pdfm_bulk_change_date_way_raido" class="bsk-pdfm-bulk-change-date-way-raido" value="Document_Date_Time" checked /> <?php esc_html_e( "Document's current date&time", 'bskpdfmanager' ); ?>
                        </label>
                    </span>
                </p>
                <table class="widefat bsk-pdfm-bulk-change-date-list-table" style="width:95%;">
                    <thead>
                        <tr>
                            <td class="check-column" style="width:5%; padding-left:10px;"><input type='checkbox' /></td>
                            <td style="width:5%;"><?php esc_html_e( 'ID', 'bskpdfmanager' ); ?></td>
                            <td style="width:30%;"><?php esc_html_e( 'Title', 'bskpdfmanager' ); ?></td>
                            <td style="width:30%;"><?php esc_html_e( 'File', 'bskpdfmanager' ); ?></td>
                            <td style="width:30%;"><?php esc_html_e( 'Date & Time', 'bskpdfmanager' ); ?></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if( count($pdfs_array_to_update) < 1 ){
                        ?>
                        <tr class="alternate">
                            <td colspan="5"><?php esc_html_e( 'No valid PDF files found', 'bskpdfmanager' ); ?></td>
                        </tr>
                        <?php
                        }else{
                            $i = 1;
                            foreach( $pdfs_array_to_update as $file_obj ){
                                $class_str = '';
                                if( $i % 2 == 1 ){
                                    $class_str = ' class="alternate"';
                                }
                                if( $file_obj->file_name && file_exists(BSKPDFManager::$_upload_root_path.$file_obj->file_name) ){
                                    $file_url = site_url().'/'.$file_obj->file_name;
                                    if( $default_enable_permalink ){
                                        $file_url = site_url().'/bsk-pdf-manager/'.$file_obj->slug;
                                    }
                                    $file_str =  '<a href="'.esc_url($file_url).'" target="_blank" title="'.esc_attr__( 'Open PDF', 'bskpdfmanager' ).'">'.esc_html($file_obj->file_name).'</a>';
                                }else{
                                    $file_str = esc_html($file_obj->file_name);
                                    $file_str .= '<p><span style="color: #dc3232; font-weight:bold;">'.esc_html__( 'Missing file', 'bskpdfmanager' ).'</span></p>';
                                }
                        ?>
                        <tr<?php echo $class_str; ?>>
                            <td class="check-column" style="padding-left:18px;">
                                <input type='checkbox' name='bsk_pdfm_bulk_change_date_ids[]' value="<?php echo esc_attr($file_obj->id); ?>" style="padding:0; margin:0;" checked />
                            </td>
                            <td><?php echo esc_html($file_obj->id); ?></td>
                            <td><?php echo esc_html($file_obj->title); ?></td>
                            <td><?php echo $file_str; ?></td>
                            <?php
                            $date = substr( $file_obj->last_date, 0, 10 );
                            $time_h = substr( $file_obj->last_date, 11, 2 );
                            $time_m = substr( $file_obj->last_date, 14, 2 );
                            $time_s = substr( $file_obj->last_date, 17, 2 );
                            ?>
                            <td>
                                <input type="text" name="bsk_pdfm_bulk_change_date_dates[<?php echo esc_attr($file_obj->id); ?>]" value="<?php echo esc_attr($date); ?>" class="bsk-pdfm-date-time-date bsk-date" />
                                <span>@</span>
                                <input type="number" name="bsk_pdfm_bulk_change_date_hour[<?php echo esc_attr($file_obj->id); ?>]" class="bsk-pdfm-date-time-hour" value="<?php echo esc_attr($time_h); ?>" min="0" max="23" step="1" />
                                <span>:</span>
                                <input type="number" name="bsk_pdfm_bulk_change_date_minute[<?php echo esc_attr($file_obj->id); ?>]" class="bsk-pdfm-date-time-minute" value="<?php echo esc_attr($time_m); ?>" min="0" max="59" step="1"  />
                                <span>:</span>
                                <input type="number" name="bsk_pdfm_bulk_change_date_second[<?php echo esc_attr($file_obj->id); ?>]" class="bsk-pdfm-date-time-second" value="<?php echo esc_attr($time_s); ?>" min="0" max="59" step="1"  />
                                <input type="hidden" class="bsk-pdfm-bulk-change-date-document-self" value="<?php echo esc_attr($file_obj->last_date); ?>" />
                            </td>
                        </tr>
                    <?php
                            $i++;
                        }
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="check-column" style="padding-left:10px;"><input type='checkbox' /></td>
                            <td><?php esc_html_e( 'ID', 'bskpdfmanager' ); ?></td>
                            <td><?php esc_html_e( 'Title', 'bskpdfmanager' ); ?></td>
                            <td><?php esc_html_e( 'File', 'bskpdfmanager' ); ?></td>
                            <td><?php esc_html_e( 'Date & Time', 'bskpdfmanager' ); ?></td>
                        </tr>
                    </tfoot>
                    <input type="hidden" class="bsk-pdfm-bulk-change-date-current-server-date-time" value="<?php echo wp_date( 'Y-m-d H:i:s' ) ?>" />
                </table>
            </div>
            <?php
            $_nonce = wp_create_nonce( 'bsk_pdf_manager_bulk_update_pdf_date_nonce' );
            ?>
            <p style="margin-top: 20px;">
                <input type="hidden" name="_nonce" value="<?php echo esc_attr($_nonce); ?>" />
                <input type="hidden" name="bsk_pdf_manager_action" id="bsk_pdf_manager_action_id" value="" />
                <input type="button" class="button-primary" value="<?php esc_attr_e( 'Cancel', 'bskpdfmanager' ); ?>" id="bsk_pdf_manager_pdfs_date_change_cancel_id" />
                <?php if( $pdfs_array_to_update && count($pdfs_array_to_update) > 0 ){ ?>
                <input type="button" class="button-primary" value="<?php esc_attr_e( 'Submit', 'bskpdfmanager' ); ?>" id="bsk_pdf_manager_pdfs_date_change_submit_id" style="margin-left: 15px;" disabled />
                <?php } ?>
            </p>
            </form>
        </div>
        <?php
    }
	
    function bulk_action_changetitle( $selected_PDFs, $selected_PDFs_to_hidden, $action_url ) {

        $default_enable_permalink = false;
        $default_permalink_base = 'bskpdf';
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
            if( isset($plugin_settings['enable_permalink']) ){
				$default_enable_permalink = $plugin_settings['enable_permalink'];
			}
            
            if( isset($plugin_settings['permalink_base']) ){
				$default_permalink_base = $plugin_settings['permalink_base'];
			}
		}
        
        $permalink_structure = get_option( 'permalink_structure' );
        if ( ! $permalink_structure ) {
            $default_enable_permalink = false;
        }

        ?>
        <div class="wrap">
            <h2 style="margin-bottom: 20px;"><?php esc_html_e( 'Change Title', 'bskpdfmanager' ); ?></h2>
            <?php $this->bsk_pdf_manager_show_pro_tip_box( BSKPDFM_Dashboard::$_pro_tips_for_pdf_bulk_change_title ); ?>
            <form id="bsk_pdf_manager_pdfs_bulk_change_title_form_id" method="post" action="<?php echo esc_url($action_url); ?>" enctype="multipart/form-data">
            <?php
            
            $pdfs_array_to_update = false;
            
            if( $selected_PDFs && is_array($selected_PDFs) && count($selected_PDFs) > 0 ){
                global $wpdb;
                
                $pdfs_id_str = implode( ', ', esc_sql($selected_PDFs) );
                $sql = 'SELECT `id`, `title`, `slug`, `file_name`, `last_date`, `by_media_uploader` '.
                        'FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name).'` '.
                        'WHERE `id` IN('.$pdfs_id_str.')';
                $pdfs_array_to_update = $wpdb->get_results( $sql );
            }
            ?>
            <div>
                <table class="widefat bsk-pdfm-bulk-change-date-list-table" style="width:95%;">
                    <thead>
                        <tr>
                            <td class="check-column" style="width:5%; padding-left:10px;"><input type='checkbox' /></td>
                            <td style="width:5%;"><?php esc_html_e( 'ID', 'bskpdfmanager' ); ?></td>
                            <td style="width:50%;"><?php esc_html_e( 'Title', 'bskpdfmanager' ); ?></td>
                            <td style="width:30%;"><?php esc_html_e( 'File', 'bskpdfmanager' ); ?></td>
                            <td style="width:10%;"><?php esc_html_e( 'Date & Time', 'bskpdfmanager' ); ?></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if( !$pdfs_array_to_update || 
                            ( is_array( $pdfs_array_to_update ) && count($pdfs_array_to_update) < 1 ) ){
                        ?>
                        <tr class="alternate">
                            <td colspan="5"><?php esc_html_e( 'No valid PDF files found', 'bskpdfmanager' ); ?></td>
                        </tr>
                        <?php
                        }else{
                            
                            $i = 1;
                            foreach( $pdfs_array_to_update as $file_obj ){
                                $class_str = '';
                                if( $i % 2 == 1 ){
                                    $class_str = ' class="alternate"';
                                }
                                if( $file_obj->file_name && file_exists(BSKPDFManager::$_upload_root_path.$file_obj->file_name) ){
                                    $file_url = site_url().'/'.$file_obj->file_name;
                                    if( $default_enable_permalink ){
                                        $file_url = site_url().'/bsk-pdf-manager/'.$file_obj->slug;
                                    }
                                    $file_str =  '<a href="'.esc_url($file_url).'" target="_blank" title="'.esc_attr__( 'Open PDF', 'bskpdfmanager' ).'">'.esc_html($file_obj->file_name).'</a>';
                                }else{
                                    $file_str = esc_html($file_obj->file_name);
                                    $file_str .= '<p><span style="color: #dc3232; font-weight:bold;">'.esc_attr__( 'Missing file', 'bskpdfmanager' ).'</span></p>';
                                }
                        ?>
                        <tr<?php echo $class_str; ?>>
                            <td class="check-column" style="padding-left:18px;">
                                <input type='checkbox' name='bsk_pdfm_bulk_change_title_ids[]' value="<?php echo esc_attr($file_obj->id); ?>" style="padding:0; margin:0;" checked />
                            </td>
                            <td><?php echo esc_html($file_obj->id); ?></td>
                            <td><input type="text" name='bsk_pdfm_bulk_change_title_texts[<?php echo esc_attr($file_obj->id); ?>]' value="<?php echo esc_attr($file_obj->title); ?>" style="width: 100%;" maxlength="512" /></td>
                            <td><?php echo $file_str; ?></td>
                            <td><?php echo esc_html($file_obj->last_date); ?></td>
                        </tr>
                        <?php
                            $i++;
                        }
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="check-column" style="padding-left:10px;"><input type='checkbox' /></td>
                            <td><?php esc_html_e( 'ID', 'bskpdfmanager' ); ?></td>
                            <td><?php esc_html_e( 'Title', 'bskpdfmanager' ); ?></td>
                            <td><?php esc_html_e( 'File', 'bskpdfmanager' ); ?></td>
                            <td><?php esc_html_e( 'Date & Time', 'bskpdfmanager' ); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php
            $_nonce = wp_create_nonce( 'bsk_pdf_manager_bulk_update_pdf_title_nonce' );
            ?>
            <p style="margin-top: 20px;">
                <input type="hidden" name="_nonce" value="<?php echo esc_attr($_nonce); ?>" />
                <input type="hidden" name="bsk_pdf_manager_action" id="bsk_pdf_manager_action_id" value="" />
                <input type="button" class="button-primary" value="Cancel" id="bsk_pdf_manager_pdfs_title_change_cancel_id" />
                <?php if( $pdfs_array_to_update && count($pdfs_array_to_update) > 0 ){ ?>
                <input type="button" class="button-primary" value="Submit" style="margin-left: 15px;" disabled />
                <?php } ?>
            </p>
            </form>
        </div>
        <?php
    }

    function bulk_action_generatethumb( $selected_PDFs, $selected_PDFs_to_hidden, $action_url ) {

        $default_enable_permalink = false;
        $default_permalink_base = 'bskpdf';
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
            if( isset($plugin_settings['enable_permalink']) ){
				$default_enable_permalink = $plugin_settings['enable_permalink'];
			}
            
            if( isset($plugin_settings['permalink_base']) ){
				$default_permalink_base = $plugin_settings['permalink_base'];
			}
		}
        
        $permalink_structure = get_option( 'permalink_structure' );
        if ( ! $permalink_structure ) {
            $default_enable_permalink = false;
        }

        ?>
        <div class="wrap">
            <h2 style="margin-bottom: 20px;"><?php esc_html_e( 'Generate Featured Image', 'bskpdfmanager' ); ?></h2>
            <p><?php esc_html_e( 'This action will generate a thumbnail from the selected page of the PDF document and set it as the featured image of the PDF document.', 'bskpdfmanager' ); ?></p>
            <p><?php esc_html_e( 'If the selected page number exceeds the maximum page number, the first page will be used by default.', 'bskpdfmanager' ); ?></p>
            <div class="bsk-pdfm-tips-box" style="text-align: left;">
                <p>This feature requires a <span style="font-weight: bold;">CREATOR</span>( or above ) license for <a href="<?php echo esc_url(BSKPDFManager::$url_to_upgrade); ?>" target="_blank">Pro version</a>. </p>
            </div>
            <?php
            
            $load_imagick_return = BSKPDFM_Dashboard_PDF_Image_Editor::bsk_pdfm_check_imagick();
            if ( 0 ) { //is_wp_error( $load_imagick_return ) ) {
                echo '<div class="notice notice-error inline">'.$load_imagick_return->get_error_message().'</div>';
            } else {
            ?>
                <form id="bsk_pdf_manager_pdfs_bulk_generate_thumb_form_id" method="post" action="<?php echo esc_url($action_url); ?>" enctype="multipart/form-data">
                <?php

                $pdfs_array_to_update = false;

                if( $selected_PDFs && is_array($selected_PDFs) && count($selected_PDFs) > 0 ){
                    global $wpdb;

                    $pdfs_id_str = implode( ', ', $selected_PDFs );
                    $sql = 'SELECT `id`, `title`, `slug`, `file_name`, `last_date`, `by_media_uploader`, `thumbnail_id` '.
                            'FROM `'.$wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name.'` '.
                            'WHERE `id` IN('.esc_sql($pdfs_id_str).')';
                    $pdfs_array_to_update = $wpdb->get_results( $sql );
                }
                ?>
                <div>
                    <table class="widefat bsk-pdfm-bulk-change-date-list-table" style="width:95%;">
                        <thead>
                            <tr>
                                <td class="check-column" style="width:5%; padding-left:10px;"><input type='checkbox' /></td>
                                <td style="width:5%;"><?php esc_html_e( 'ID', 'bskpdfmanager' ); ?></td>
                                <td style="width:20%;"><?php esc_html_e( 'Title', 'bskpdfmanager' ); ?></td>
                                <td style="width:30%;"><?php esc_html_e( 'File', 'bskpdfmanager' ); ?></td>
                                <td style="width:30%;"><?php esc_html_e( 'Current Featured Image', 'bskpdfmanager' ); ?></td>
                                <td style="width:10%;"><?php esc_html_e( 'Page No.', 'bskpdfmanager' ); ?></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if( !$pdfs_array_to_update || 
                                ( is_array( $pdfs_array_to_update ) && count($pdfs_array_to_update) < 1 ) ){
                            ?>
                            <tr class="alternate">
                                <td colspan="6"><?php esc_html_e( 'No valid PDF files found', 'bskpdfmanager' ); ?></td>
                            </tr>
                            <?php
                            }else{
                                $i = 1;
                                foreach( $pdfs_array_to_update as $file_obj ){
                                    $file_path = '';
                                    $class_str = '';
                                    $checked = ' checked';
                                    $disabled = '';
                                    $current_featured_image = '';
                                    if( $i % 2 == 1 ){
                                        $class_str = ' class="alternate"';
                                    }
                                    if( $file_obj->file_name && file_exists(BSKPDFManager::$_upload_root_path.$file_obj->file_name) ){
                                        $file_url = site_url().'/'.$file_obj->file_name;
                                        if( $default_enable_permalink ){
                                            $file_url = site_url().'/bsk-pdf-manager/'.$file_obj->slug;
                                        }
                                        $file_str =  '<a href="'.esc_url($file_url).'" target="_blank" title="'.esc_attr__( 'Open PDF', 'bskpdfmanager' ).'">'.esc_html($file_obj->file_name).'</a>';
                                        $file_path = BSKPDFManager::$_upload_root_path.$file_obj->file_name;
                                    }else{
                                        $file_str = esc_html($file_obj->file_name);
                                        $file_str .= '<p><span style="color: #dc3232; font-weight:bold;">'.esc_attr__( 'Missing file', 'bskpdfmanager' ).'</span></p>';
                                        $checked = '';
                                        $disabled = ' disabled';
                                    }
                                    if( $file_path ){
                                        $wp_filetype = wp_check_filetype( $file_path, null );
                                        if( $wp_filetype['type'] != 'application/pdf' ){
                                            $file_str .= '<p><span style="color: #dc3232; font-weight:bold;">'.esc_attr__( 'Not PDF document', 'bskpdfmanager' ).'</span></p>';
                                            $checked = '';
                                            $disabled = ' disabled';
                                        }
                                    }
                                    if( $file_obj->thumbnail_id  && get_post( $file_obj->thumbnail_id ) ){
                                        $list_thumbnail_size = 'bsk-pdf-dashboard-list-thumbnail';
                                        $current_featured_image = wp_get_attachment_image( $file_obj->thumbnail_id, $list_thumbnail_size );
                                    }
                                    $checked = '';
                                    $disabled = ' disabled';
                            ?>
                            <tr<?php echo $class_str; ?>>
                                <td class="check-column" style="padding-left:18px;">
                                    <input type='checkbox' name='bsk_pdfm_bulk_generate_thumb_ids[]' value="<?php echo esc_attr($file_obj->id); ?>" style="padding:0; margin:0;"<?php echo $checked.$disabled; ?> />
                                </td>
                                <td><?php echo esc_html($file_obj->id); ?></td>
                                <td><?php echo esc_attr($file_obj->title); ?></td>
                                <td><?php echo $file_str; ?></td>
                                <td>
                                    <?php if( $current_featured_image ) { ?>
                                    <?php   echo $current_featured_image; ?>
                                    <br />
                                    <label><input type='checkbox' name='bsk_pdfm_bulk_generate_thumb_delete_from_media_lib[]' value="<?php echo esc_attr($file_obj->id); ?>" style="padding:0; margin:0;" /> Delete from Media Library</label>
                                    <?php } ?>
                                </td>
                                <td><input type="number" name="bsk_pdfm_generate_thumb_page_number[<?php echo esc_attr($file_obj->id); ?>]" value="1" min="1" step="1" style="width: 50px;" /></td>
                            </tr>
                            <?php
                                $i++;
                            }
                        }
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="check-column" style="padding-left:10px;"><input type='checkbox' /></td>
                                <td><?php esc_html_e( 'ID', 'bskpdfmanager' ); ?></td>
                                <td><?php esc_html_e( 'Title', 'bskpdfmanager' ); ?></td>
                                <td><?php esc_html_e( 'File', 'bskpdfmanager' ); ?></td>
                                <td><?php esc_html_e( 'Current Featured Image', 'bskpdfmanager' ); ?></td>
                                <td><?php esc_html_e( 'Page No.', 'bskpdfmanager' ); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php
                $_nonce = wp_create_nonce( 'bsk_pdf_manager_bulk_generate_thumb_nonce' );
                $submit_disabled = 'disabled';
                ?>
                <p style="margin-top: 20px;">
                    <input type="hidden" name="_nonce" value="<?php echo $_nonce; ?>" />
                    <input type="hidden" name="bsk_pdf_manager_action" id="bsk_pdf_manager_action_id" value="" />
                    <input type="button" class="button-primary" value="Cancel" id="bsk_pdf_manager_pdfs_generate_thumb_cancel_id" />
                    <?php if( $pdfs_array_to_update && count($pdfs_array_to_update) > 0 ){ ?>
                    <input type="button" class="button-primary" value="Submit" id="bsk_pdf_manager_pdfs_generate_thumb_submit_id" style="margin-left: 15px;" <?php echo $submit_disabled ?>/>
                    <?php } ?>
                </p>
                </form>
            <?php
            }
            ?>
        </div>
        <?php
    }

    function bulk_action_bulkdelete_row_action_delete( $selected_PDFs, $selected_PDFs_to_hidden, $action_url ) {

        $default_enable_permalink = false;
        $default_permalink_base = 'bskpdf';
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
            if( isset($plugin_settings['enable_permalink']) ){
				$default_enable_permalink = $plugin_settings['enable_permalink'];
			}
            
            if( isset($plugin_settings['permalink_base']) ){
				$default_permalink_base = $plugin_settings['permalink_base'];
			}
		}
        
        $permalink_structure = get_option( 'permalink_structure' );
        if ( ! $permalink_structure ) {
            $default_enable_permalink = false;
        }
        
        ?>
        <div class="wrap">
            <h2 style="margin-bottom: 20px;"><?php esc_html_e( 'Confirm to Delete Document and Featured Image', 'bskpdfmanager' ); ?></h2>
            <form id="bsk_pdf_manager_pdfs_bulk_delete_form_id" method="post" action="<?php echo esc_url($action_url); ?>" enctype="multipart/form-data">
            <?php

            $pdfs_array_to_update = false;

            if( $selected_PDFs && is_array($selected_PDFs) && count($selected_PDFs) > 0 ){
                global $wpdb;

                $pdfs_id_str = implode( ', ', $selected_PDFs );
                $sql = 'SELECT `id`, `title`, `slug`, `file_name`, `last_date`, `by_media_uploader`, `thumbnail_id` '.
                        'FROM `'.$wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name.'` '.
                        'WHERE `id` IN('.esc_sql($pdfs_id_str).')';
                $pdfs_array_to_update = $wpdb->get_results( $sql );
            }
            ?>
            <div>
                <table class="widefat bsk-pdfm-bulk-change-date-list-table" style="width:95%;">
                    <thead>
                        <tr>
                            <td class="check-column" style="width:5%; padding-left:10px;"><input type='checkbox' /></td>
                            <td style="width:5%;"><?php esc_html_e( 'ID', 'bskpdfmanager' ); ?></td>
                            <td style="width:25%;"><?php esc_html_e( 'Title', 'bskpdfmanager' ); ?></td>
                            <td style="width:35%;"><?php esc_html_e( 'File', 'bskpdfmanager' ); ?></td>
                            <td style="width:30%;"><label><input type='checkbox' class="bsk-pdfm-bulk-delete-pdfs-featured-image-all" style="padding:0; margin:0 5px 0 0;" /><?php esc_html_e( 'Delete Featured Image', 'bskpdfmanager' ); ?></label></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if( !$pdfs_array_to_update || 
                            ( is_array( $pdfs_array_to_update ) && count($pdfs_array_to_update) < 1 ) ){
                        ?>
                        <tr class="alternate">
                            <td colspan="6"><?php esc_html_e( 'No valid PDF files found', 'bskpdfmanager' ); ?></td>
                        </tr>
                        <?php
                        }else{
                            $i = 1;
                            foreach( $pdfs_array_to_update as $file_obj ){
                                $class_str = '';
                                $current_featured_image = '';
                                if( $i % 2 == 1 ){
                                    $class_str = ' class="alternate"';
                                }
                                if( $file_obj->file_name && file_exists(BSKPDFManager::$_upload_root_path.$file_obj->file_name) ){
                                    $file_str =  esc_html($file_obj->file_name);
                                }else{
                                    $file_str = esc_html($file_obj->file_name);
                                    $file_str .= '<p><span style="color: #dc3232; font-weight:bold;">'.esc_attr__( 'Missing file', 'bskpdfmanager' ).'</span></p>';
                                }
                                
                                if( $file_obj->thumbnail_id  && get_post( $file_obj->thumbnail_id ) ){
                                    $list_thumbnail_size = 'bsk-pdf-dashboard-list-thumbnail';
                                    $current_featured_image = wp_get_attachment_image( $file_obj->thumbnail_id, $list_thumbnail_size );
                                }
                        ?>
                        <tr<?php echo $class_str; ?>>
                            <td class="check-column" style="padding-left:18px;">
                                <input type='checkbox' name='bsk_pdfm_bulk_delete_pdf_ids[]' value="<?php echo esc_attr($file_obj->id); ?>" style="padding:0; margin:0;" checked />
                            </td>
                            <td><?php echo esc_html($file_obj->id); ?></td>
                            <td><?php echo esc_attr($file_obj->title); ?></td>
                            <td><?php echo $file_str; ?></td>
                            <td>
                                <?php if( $current_featured_image ) { ?>
                                <?php   echo $current_featured_image; ?>
                                <br />
                                <label><input type='checkbox' name='bsk_pdfm_bulk_delete_pdf_with_thumb[]' value="<?php echo esc_attr($file_obj->id); ?>" style="padding:0; margin:0 5px 0 0;" class="bsk-pdfm-bulk-delete-pdfs-featured-image" />Delete featured image from Media Library</label>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php
                            $i++;
                        }
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="check-column" style="padding-left:10px;"><input type='checkbox' /></td>
                            <td><?php esc_html_e( 'ID', 'bskpdfmanager' ); ?></td>
                            <td><?php esc_html_e( 'Title', 'bskpdfmanager' ); ?></td>
                            <td><?php esc_html_e( 'File', 'bskpdfmanager' ); ?></td>
                            <td><label><input type='checkbox' class="bsk-pdfm-bulk-delete-pdfs-featured-image-all" style="padding:0; margin:0 5px 0 0;" /><?php esc_html_e( 'Delete Featured Image', 'bskpdfmanager' ); ?></label></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php
            $_nonce = wp_create_nonce( 'bsk_pdf_manager_bulk_generate_thumb_nonce' );
            ?>
            <p style="margin-top: 20px;">
                <input type="hidden" name="_nonce" value="<?php echo esc_attr( $_nonce ); ?>" />
                <input type="hidden" name="bsk_pdf_manager_action" id="bsk_pdf_manager_action_id" value="" />
                <input type="button" class="button-primary" value="Cancel" id="bsk_pdf_manager_pdfs_bulk_delete_cancel_id" />
                <?php if( $pdfs_array_to_update && count($pdfs_array_to_update) > 0 ){ ?>
                <input type="button" class="button-primary" value="Submit" id="bsk_pdf_manager_pdfs_bulk_delete_submit_id" style="margin-left: 15px;" />
                <?php } ?>
            </p>
            </form>
        </div>
    <?php
    }
    
    function bsk_pdf_manager_show_pro_tip_box( $tips_array ){
        $tips = implode( ', ', $tips_array );
		$str = 
        '<div class="bsk-pdfm-tips-box">
			<p><b>Tip: </b> The following features only supported in <a href="'.esc_url(BSKPDFManager::$url_to_upgrade).'" target="_blank">Pro version</a>.</p>
            <p><span class="bsk-pdfm-tips-box-tip">'.$tips.'</span></p>
		 </div>';
		
		echo $str;
	}
}
