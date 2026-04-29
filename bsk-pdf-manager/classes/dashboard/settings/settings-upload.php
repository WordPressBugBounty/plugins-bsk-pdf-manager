<?php

class BSKPDFM_Dashboard_Settings_Upload {
	
	private static $_bsk_pdf_settings_page_url = '';
	   
	public function __construct() {		
		self::$_bsk_pdf_settings_page_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['setting'] );

        add_action( 'bsk_pdf_manager_upload_settings_save', array($this, 'bsk_pdf_manager_settings_upload_settings_tab_save_fun') );
	}
	
	
	function show_settings( $plugin_settings ){
        //scan all subfolder to granise directory tree
        //for not superadmin user, only can set on /wp-content/uploads/sites/{blog_id}/
        $root_path_to_scan = BSKPDFManager::$_upload_root_path;
        if( is_multisite() && !is_super_admin() ){
            $root_path_to_scan = BSKPDFManager::$_upload_root_path;
        }
        $default_upload_path = $custom_upload_path = BSKPDFManager::$_upload_path;
        $site_directory_structure = $this->bsk_pdfm_scan_all_subfolders( $root_path_to_scan, $default_upload_path, $custom_upload_path);

        $organise_directory_strucutre_with_year_month = true;
        $public_upload_enabled = false;
        $allow_for_subscribers = true;
        $allow_for_guests = true;
        $supported_form_plugins = array();
        $default_upload_categories_array = array();
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
            if( isset($plugin_settings['directory_with_year_month']) ){
                $organise_directory_strucutre_with_year_month = $plugin_settings['directory_with_year_month'];
			}
            if( isset($plugin_settings['public_upload_enabled']) ){
                $public_upload_enabled = $plugin_settings['public_upload_enabled'];
                if ( $public_upload_enabled ) {
                    if( isset($plugin_settings['pub_allow_for_subscribers']) ){
                        $allow_for_subscribers = $plugin_settings['pub_allow_for_subscribers'];
                    }
                    if( isset($plugin_settings['pub_allow_for_guests']) ){
                        $allow_for_guests = $plugin_settings['pub_allow_for_guests'];
                    }
                    if ( $allow_for_subscribers == false && $allow_for_guests == false ) {
                        $public_upload_enabled = false;
                    }
                    if( isset($plugin_settings['pub_supported_form_plugins']) ){
                        $supported_form_plugins = $plugin_settings['pub_supported_form_plugins'];
                    }
                    if( isset($plugin_settings['pub_default_upload_categories']) ){
                        $default_upload_categories_array = $plugin_settings['pub_default_upload_categories'];
                    }
                }
			}
		}
	?>
    <form action="<?php echo add_query_arg( 'target', 'upload', self::$_bsk_pdf_settings_page_url ); ?>" method="POST" id="bsk_pdfm_upload_settings_form_ID">
    <div class="bsk_pdf_manager_settings">
        <?php
        $current_user_can_edit = '';
        if( !current_user_can('manage_options') ){
            $current_user_can_edit = ' disabled';
        }
        
        $current_upload_path_to_show = str_replace( BSKPDFManager::$_upload_root_path, '', BSKPDFManager::$_upload_path );
        ?>
        <div class="bsk-pdfm-settings-global-file-upload-upload-directory">
            <h2><?php esc_html_e( 'Upload Directory', 'bskpdfmanager' ); ?></h2>
            <p>
                <label><?php esc_html_e( 'Current upload directory', 'bskpdfmanager' ); ?>: </label>
                <span style="font-size: 14px; font-weight: bold;"><?php echo $current_upload_path_to_show; ?></span>
            </p>
            <?php
            $checked_str = $organise_directory_strucutre_with_year_month ? ' checked="checked"' : '';
            $hint_display = $organise_directory_strucutre_with_year_month ? 'none' : 'block';
            ?>
            <p>
                <label>
                    <input type="checkbox" name="bsk_pdfm_organise_by_month_year" id="bsk_pdfm_organise_by_month_year_ID" value="Yes" <?php echo $checked_str; ?> disabled /> <?php esc_html_e( 'Organize uploads into month and year based folders', 'bskpdfmanager' ); ?>
                </label>
            </p>
            <p id="bsk_pdfm_organise_by_month_year_hint_text_ID" style="display: <?php echo $hint_display; ?>;">
                <span style="display: block; font-style: italic;"><?php esc_html_e( "To prevents your files from taxing the server's resources and negatively affect its load time. It is better to limit your directories to no more than 1,024 files/inodes", 'bskpdfmanager' ); ?></span>
            </p>
            <p style="margin-top:  20px;">
                <label>
                    <input type="checkbox" name="bsk_pdfm_set_upload_folder" id="bsk_pdfm_set_upload_folder_ID" value="Yes"  disabled /> <?php esc_html_e( 'Change upload directory to', 'bskpdfmanager' ); ?>: 
                </label>
            </p>
            <p id="bsk_pdfm_set_upload_folder_input_ID">
                <span style="font-size: 14px; font-weight: bold; color: #dedddd; " id="bsk_pdfm_set_upload_folder_path_ID">
                        <?php echo esc_html( $current_upload_path_to_show ); ?>
                </span>
                <input type="text" name="bsk_pdfm_set_upload_folder_sub" id="bsk_pdfm_set_upload_folder_sub_ID" value="" placeholder="<?php esc_attr_e( 'create sub folder if not blank', 'bskpdfmanager' ); ?>" style="width: 200px;" disabled />
                <input type="hidden" name="bsk_pdfm_set_upload_folder_path_val" id="bsk_pdfm_set_upload_folder_path_val_ID" value="<?php echo esc_attr( str_replace( BSKPDFManager::$_upload_root_path, '', $current_upload_path ) ); ?>" placeholder="create sub folder if not blank" style="width: 200px;" disabled />
            </p>
            <p id="bsk_pdfm_set_upload_folder_hint_text_ID">
                <span style="display: block; font-style: italic;"><?php esc_html_e( 'Select destination path in the below diretory tree', 'bskpdfmanager' ); ?></span>
                <?php if( is_multisite() && !is_super_admin() ){ ?>
                <span style="display: block; font-style: italic;"><span style="font-weight: bold;font-size: 1.2em;color: #ff5b00;">*</span><?php esc_html_e( 'Only Super Admin can visit full directory structure', 'bskpdfmanager' ); ?></span>
                <?php } ?>
                <span style="display: block; font-style: italic;"><span style="font-weight: bold;font-size: 1.2em;color: #ff5b00;">*</span><?php esc_attr_e( 'Removing previous upload folder may cause PDFs link broken', 'bskpdfmanager' ); ?></span>
            </p>
            <div id="bsk_pdf_upload_folder_tree" style="overflow:auto; border:1px solid silver; min-height:100px;">
                <ul>
                    <li data-jstree='{ "opened" : true }' relative_path="<?php echo esc_attr( DIRECTORY_SEPARATOR ); ?>"><?php echo esc_html( DIRECTORY_SEPARATOR ); ?>
                        <ul>
                            <?php $this->bsk_pdfm_display_all_subfolders( $site_directory_structure, $default_upload_path, $custom_upload_path ); ?>
                        </ul>
                    </li>
                </ul>
            </div>
            <?php
            if( is_multisite() && !is_super_admin() ){
                $root_path_to_scan = BSKPDFManager::$_upload_path;
                $label_to_set = str_replace( BSKPDFManager::$_upload_root_path, '', $root_path_to_scan );
                $relative_path_to_set = str_replace( BSKPDFManager::$_upload_root_path, '', $root_path_to_scan );
                
                $this->bsk_pdfm_rename_jstree_root_node_label( $label_to_set, $relative_path_to_set );
            }
            ?>
        </div>
        <?php
        $checked_str = $public_upload_enabled ? ' checked="checked"' : '';
        $allow_for_subscribers_checked_str = $allow_for_subscribers ? ' checked="checked"' : '';
        $allow_for_guests_checked_str = $allow_for_guests ? ' checked="checked"' : '';
        ?>
        <h2 style="margin-top:40px;"><?php esc_html_e( 'Allow Front File Uploads for Subscribers and Guests', 'bskpdfmanager' ); ?></h2>
        <div class="bsk-pdfm-settings-upload-for-public">
            <p>
                <label>
                    <input type="checkbox" name="bsk_pdfm_allow_file_uploads_for_subscribers_and_guests" id="bsk_pdfm_allow_file_uploads_for_subscribers_and_guests_ID" value="Yes" <?php echo $checked_str; ?>/> <?php esc_html_e( 'Enable this option to permit file uploads from both logged-in Subscriber users and non-logged-in visitors.', 'bskpdfmanager' ); ?>
                </label>
            </p>
            <div id="bsk_pdfm_allow_file_uploads_for_subscribers_and_guests_panel_ID" style="display: <?php echo ( $public_upload_enabled ? 'block' : 'none' ); ?>">
                <p>
                    <label>
                        <input type="checkbox" name="bsk_pdfm_allow_file_uploads_for_subscribers" id="bsk_pdfm_allow_file_uploads_for_subscribers_ID" value="Yes" <?php echo $allow_for_subscribers_checked_str; ?>/> <?php esc_html_e( 'Allow logged-in Subscriber users to upload', 'bskpdfmanager' ); ?>
                    </label>
                </p>
                <p>
                    <label>
                        <input type="checkbox" name="bsk_pdfm_allow_file_uploads_for_guests" id="bsk_pdfm_allow_file_uploads_for_guests_ID" value="Yes" <?php echo $allow_for_guests_checked_str; ?>/> <?php esc_html_e( 'Allow non-logged-in visitors to upload', 'bskpdfmanager' ); ?>
                    </label>
                </p>
                <div id="bsk_pdfm_public_file_uploads_settings_panel_ID" style="display: <?php echo ( ( $allow_for_subscribers || $allow_for_guests )  ? 'block' : 'none' ); ?>">
                    <p>&nbsp;</p>
                    <h3>Default category for the files to be uploaded</h3>
                    <?php
                    global $wpdb, $current_user;
                    
                    //get all categories
                    $sql = 'SELECT COUNT(*) FROM '.esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name).' WHERE 1 AND `type` LIKE "CAT"';
                    $categories_count = $wpdb->get_var( $sql );
                    if( $categories_count ){
                        echo BSKPDFM_Common_Backend::get_category_hierarchy_checkbox( 'bsk_pdfm_public_default_upload_categories_array[]', 'bsk-pdfm-pub-upload-default-catgory-checkbox', $default_upload_categories_array, 'CAT', false );
                    }else{
                        $create_category_url = add_query_arg( 'page', 
                                                                BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['category'], 
                                                                admin_url('admin.php') );
                        $create_category_url = add_query_arg( 'view', 'addnew', $create_category_url );
                        $create_category_str = sprintf( __( 'Please %s first', 'bskpdfmanager' ), '<a href="'.esc_url($create_category_url).'">'.__('create category', 'bskpdfmanager' ).'</a>' );
                        
                        echo '<p>'.$create_category_str.'</p>';
                    }
                    echo '<p class="bsk-pdfm-pub-upload-default-catgory-error-message" style="display: none; color: #FF0000"></p>';
                    ?>
                    <h3>Allow users to upload files on the frontend of your website. Two integration methods are available.</h3>
                    <h4>1. Shortcode Method</h4>
                    <p>Use the <code>[bsk-pdfm-pub-upload]</code> shortcode to embed an upload form on any page or post. This method works independently without requiring additional form plugins.</p>
                    <p>&nbsp;</p>
                    <h4>2. Form Plugin Integration</h4>
                    <p>Integrate file upload functionality directly with your existing form plugins. Enable the plugins you wish to support from the list below:</p>
                    <div class="bsk-pdfm-tips-box">
                        <p>This feature is only supported in <a href="https://bannersky.com/bsk-pdf-manager/" target="_blank">Pro version</a>.</p>
                    </div>
                    <p>Supported Form Plugins:</p>
                    <ul>
                        <li>
                            <label for="bsk_pdfm_public_file_uploads_supported_form_plugin_gf">
                                <input type="checkbox" name="bsk_pdfm_public_file_uploads_supported_form_plugins[]" id="bsk_pdfm_public_file_uploads_supported_form_plugin_gf" value="gravity_forms" <?php if( in_array( 'gravity_forms', $supported_form_plugins ) ) echo 'checked'; ?> disabled />
                                <strong>Gravity Forms</strong>
                            </label>
                        </li>
                        <li>
                            <label for="bsk_pdfm_public_file_uploads_supported_form_plugin_ff">
                                <input type="checkbox" name="bsk_pdfm_public_file_uploads_supported_form_plugins[]" id="bsk_pdfm_public_file_uploads_supported_form_plugin_ff" value="formidable_forms" <?php if( in_array( 'formidable_forms', $supported_form_plugins ) ) echo 'checked'; ?> disabled />
                                <strong>Formidable Forms</strong>
                            </label>
                        </li>
                        <li>
                            <label for="bsk_pdfm_public_file_uploads_supported_form_plugin_wp">
                                <input type="checkbox" name="bsk_pdfm_public_file_uploads_supported_form_plugins[]" id="bsk_pdfm_public_file_uploads_supported_form_plugin_wp" value="wpforms" <?php if( in_array( 'wpforms', $supported_form_plugins ) ) echo 'checked'; ?> disabled />
                                <strong>WPForms</strong>
                            </label>
                        </li>
                        <li>
                            <label for="bsk_pdfm_public_file_uploads_supported_form_plugin_ninja">
                                <input type="checkbox" name="bsk_pdfm_public_file_uploads_supported_form_plugins[]" id="ninja_forms" value="bsk_pdfm_public_file_uploads_supported_form_plugin_ninja" value="ninja_forms" <?php if( in_array( 'ninja_forms', $supported_form_plugins ) ) echo 'checked'; ?> disabled />
                                <strong>Ninja Forms</strong>
                            </label>
                        </li>
                        <li>
                            <label for="bsk_pdfm_public_file_uploads_supported_form_plugin_forminator">
                                <input type="checkbox" name="bsk_pdfm_public_file_uploads_supported_form_plugins[]" id="bsk_pdfm_public_file_uploads_supported_form_plugin_forminator" value="forminator_forms" <?php if( in_array( 'forminator_forms', $supported_form_plugins ) ) echo 'checked'; ?> disabled />
                                <strong>Forminator Forms</strong>
                            </label>
                        </li>
                        <li>
                            <label for="bsk_pdfm_public_file_uploads_supported_form_plugin_fluent">
                                <input type="checkbox" name="bsk_pdfm_public_file_uploads_supported_form_plugins[]" id="bsk_pdfm_public_file_uploads_supported_form_plugin_fluent" value="fluent_forms" <?php if( in_array( 'fluent_forms', $supported_form_plugins ) ) echo 'checked'; ?> disabled />
                                <strong>Fluent Forms</strong>
                            </label>
                        </li>
                        <li>
                            <label for="bsk_pdfm_public_file_uploads_supported_form_plugin_gutenverse">
                                <input type="checkbox" name="bsk_pdfm_public_file_uploads_supported_form_plugins[]" id="bsk_pdfm_public_file_uploads_supported_form_plugin_gutenverse" value="gutenverse_forms" <?php if( in_array( 'gutenverse_forms', $supported_form_plugins ) ) echo 'checked'; ?> disabled />
                                <strong>Gutenverse Form</strong>
                            </label>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <p style="margin-top:20px;">
        	<input type="button" id="bsk_pdf_manager_settings_upload_tab_save_form_ID" class="button-primary" value="<?php esc_html_e( 'Save Upload Settings', 'bskpdfmanager' ); ?>" />
            <input type="hidden" name="bsk_pdf_manager_action" value="upload_settings_save" />
        </p>
        <?php echo wp_nonce_field( plugin_basename( __FILE__ ), 'bsk_pdf_manager_settings_upload_tab_save_oper_nonce', true, false ); ?>
    </div>
    </form>
    <?php
	}

    function bsk_pdf_manager_settings_upload_settings_tab_save_fun( $data ) {
		global $wpdb, $current_user;
		//check nonce field
		if (!wp_verify_nonce(sanitize_text_field($data['bsk_pdf_manager_settings_upload_tab_save_oper_nonce']), plugin_basename( __FILE__ ) )) {
			wp_die( esc_html__( 'Security issue, please refresh page and test again', 'bskpdfmanager' ) );
		}
		
		if ( ! current_user_can( 'moderate_comments' ) ) {
            wp_die( esc_html__( 'You are now allowed to do this', 'bskpdfmanager' ) );
        }

		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
		if( !$plugin_settings || !is_array($plugin_settings) || count($plugin_settings) < 1 ){
			$plugin_settings = array();
		}
        

        //Allow File Uploads for Subscribers and Guests settings 
        if ( isset($data['bsk_pdfm_allow_file_uploads_for_subscribers_and_guests']) && sanitize_text_field($data['bsk_pdfm_allow_file_uploads_for_subscribers_and_guests']) == 'Yes' ) {
            $plugin_settings['public_upload_enabled'] = true;

            if( isset($data['bsk_pdfm_allow_file_uploads_for_subscribers']) && 
                sanitize_text_field($data['bsk_pdfm_allow_file_uploads_for_subscribers']) == 'Yes' ){
                $plugin_settings['pub_allow_for_subscribers'] = true;
            } else {
                $plugin_settings['pub_allow_for_subscribers'] = false;
            }

            if( isset($data['bsk_pdfm_allow_file_uploads_for_guests']) && 
                sanitize_text_field($data['bsk_pdfm_allow_file_uploads_for_guests']) == 'Yes' ){
                $plugin_settings['pub_allow_for_guests'] = true;
            } else {
                $plugin_settings['pub_allow_for_guests'] = false;
            }

            if ( $plugin_settings['pub_allow_for_subscribers'] == false && $plugin_settings['pub_allow_for_guests'] == false ) {
                $plugin_settings['public_upload_enabled'] = false;
            } else {
                if( isset($data['bsk_pdfm_public_file_uploads_supported_form_plugins']) && 
                    is_array($data['bsk_pdfm_public_file_uploads_supported_form_plugins']) &&
                    count($data['bsk_pdfm_public_file_uploads_supported_form_plugins'] ) ){
                    
                    $supported_form_plugins = array();
                    foreach ($data['bsk_pdfm_public_file_uploads_supported_form_plugins'] as $form_plugin ) {
                        $form_plugin = sanitize_text_field($form_plugin);
                        if ($form_plugin) {
                            $supported_form_plugins[] = $form_plugin;
                        }
                    }
                    $plugin_settings['pub_supported_form_plugins'] = $supported_form_plugins;
                }
            }

            

            if( isset($data['bsk_pdfm_public_default_upload_categories_array']) && 
                is_array($data['bsk_pdfm_public_default_upload_categories_array']) &&
                count($data['bsk_pdfm_public_default_upload_categories_array'] ) ){
                
                $default_upload_categories = array();
                foreach ($data['bsk_pdfm_public_default_upload_categories_array'] as $category ) {
                    $category = intval(sanitize_text_field($category));
                    if ($category) {
                        $default_upload_categories[] = $category;
                    }
                }
                $plugin_settings['pub_default_upload_categories'] = $default_upload_categories;
            }
        } else {
            $plugin_settings['public_upload_enabled'] = false;
        }
        
		update_option( BSKPDFManager::$_plugin_settings_option, $plugin_settings );
	}
	
    function bsk_pdfm_scan_all_subfolders( $path, $default_uploader_path, $custom_upload_path ){
        $result = array(); 

        $scaned_results = @scandir( $path );
        if( false === $scaned_results ){
            return $result;
        }
        foreach ( $scaned_results as $key => $value ) { 
            $current_full_path = $path.$value.DIRECTORY_SEPARATOR;
            if (!in_array($value,array(".",".."))) { 
                if (!@is_dir($current_full_path)  ) { 
                   continue;
                }
                if( $current_full_path == $default_uploader_path ||
                    $current_full_path == $custom_upload_path ){
                    $result[$current_full_path] = $current_full_path;
                    continue;
                }
                $result[$current_full_path] = $this->bsk_pdfm_scan_all_subfolders( $current_full_path, 
                                                                                    $default_uploader_path, 
                                                                                    $custom_upload_path );
            } 
        } 

        return $result; 
    }
    
    function bsk_pdfm_display_all_subfolders( $folder_name_array, $default_upload_path, $custom_upload_path ){
        $upload_path_to_set = $custom_upload_path ? $custom_upload_path : $default_upload_path;
        foreach( $folder_name_array as $key => $sub_folders ) {
            $li_data = '';
            
            $folder_name_to_show_array = explode(DIRECTORY_SEPARATOR, $key );
            $tree_node_label = $folder_name_to_show_array[count($folder_name_to_show_array) - 2];
            $relative_path = str_replace(BSKPDFManager::$_upload_root_path, '', $key );
            if( $upload_path_to_set && $key == $upload_path_to_set ){
                ?><li data-jstree='{ "selected" : true }' relative_path="<?php echo esc_attr( $relative_path ); ?>"><?php echo esc_html( $tree_node_label );
            }else if( strpos( $upload_path_to_set, $key ) === 0 ){
                ?><li data-jstree='{ "opened" : true }' relative_path="<?php echo esc_attr( $relative_path ); ?>"><?php echo esc_html( $tree_node_label );
            }else{
                ?><li relative_path="<?php echo esc_attr( $relative_path ); ?>"><?php echo esc_html( $tree_node_label );
            }
            if( is_array( $sub_folders ) ){
                echo '<ul>';
                $this->bsk_pdfm_display_all_subfolders( $sub_folders, $default_upload_path, $upload_path_to_set );
                echo '</ul>';
            }
            echo '</li>';
        }
    }
    
    function bsk_pdfm_rename_jstree_root_node_label( $label_to_set, $relative_path ){
        ?>
        <input type="hidden" id="bsk_pdf_upload_folder_tree_root_label_ID" value="<?php echo $label_to_set; ?>" />
        <input type="hidden" id="bsk_pdf_upload_folder_tree_root_relative_path" value="<?php echo $relative_path; ?>" />
        <?php
    }
    
    function bsk_pdfm_create_custom_upload_folder_fialed_notice(){
        $class = 'notice notice-error';
        $msg = sprintf( esc_html__( 'Directory %s can not be created. Please create it first yourself.', 'bskpdfmanager' ), '<strong>'.BSKPDFManager::$_custom_upload_folder_path.'</strong>' );
        
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $msg ); 
    }
    
    function bsk_pdfm_set_custom_upload_folder_writable_fialed_notice(){
        $class = 'notice notice-error';
        $msg = sprintf( esc_html__( 'Directory %s is not writeable ! ', 'bskpdfmanager' ), '<strong>'.BSKPDFManager::$_custom_upload_folder_path.'</strong>' );
        $msg .= sprintf( esc_html__( 'Check %s for how to set the permission.', 'bskpdfmanager' ), '<a href="http://codex.wordpress.org/Changing_File_Permissions">http://codex.wordpress.org/Changing_File_Permissions</a>' );

        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $msg ) ); 
    }
}