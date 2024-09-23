<?php

class BSKPDFM_Dashboard_Settings_Upload {
	
	private static $_bsk_pdf_settings_page_url = '';
	   
	public function __construct() {		
		self::$_bsk_pdf_settings_page_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['setting'] );
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
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
            if( isset($plugin_settings['directory_with_year_month']) ){
                $organise_directory_strucutre_with_year_month = $plugin_settings['directory_with_year_month'];
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
        <h3 style="margin-top: 40px;"><?php esc_html_e( 'Upload Directory', 'bskpdfmanager' ); ?></h3>
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
        <p style="margin-top:20px;">
        	<input type="button" id="bsk_pdf_manager_settings_upload_tab_save_form_ID" class="button-primary" value="<?php esc_html_e( 'Save Upload Settings', 'bskpdfmanager' ); ?>" disabled />
            <input type="hidden" name="bsk_pdf_manager_action" value="" />
        </p>
        <?php echo wp_nonce_field( plugin_basename( __FILE__ ), 'bsk_pdf_manager_settings_upload_tab_save_oper_nonce', true, false ); ?>
    </div>
    </form>
    <?php
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