<?php

/*
* Plugin Name: BSK PDF Manager
* Plugin URI: http://www.bannersky.com/bsk-pdf-manager/
* Description: Help you manage your PDF documents. PDF documents can be filter by category. Support short code to show special PDF documents or all PDF documents under  category. Widget supported.
* Version: 3.6.2
* Author: BannerSky.com
* Author URI: http://www.bannersky.com/
* License: GPLv2 or later
* License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin Folder Path.
if ( ! defined( 'BSK_PDFM_PLUGIN_DIR' ) ) {
    define( 'BSK_PDFM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
// Plugin Folder URL.
if ( ! defined( 'BSK_PDFM_PLUGIN_URL' ) ) {
    define( 'BSK_PDFM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

class BSKPDFManager {
	
    private static $instance;
    public static $_cats_tbl_name = 'bsk_pdf_manager_cats';
	public static $_pdfs_tbl_name = 'bsk_pdf_manager_pdfs';
    public static $_rels_tbl_name = 'bsk_pdf_manager_relationships';
    public static $_user_available_tbl_name = 'bsk_pdf_manager_user_available';
    public static $_notifications_tbl_name = 'bsk_pdf_manager_notifications';
    
	public static $_PLUGIN_VERSION_ = '3.6.2';
	private static $_plugin_db_version = '3.0';
	private static $_plugin_saved_db_version_option = '_bsk_pdf_manager_db_ver_';
    private static $_plugin_db_rels_done_option = '_bsk_pdf_manager_rels_done_';
    private static $_plugin_db_upgrading = '_bsk_pdf_manager_db_upgrading_';
    
    private static $_plugin_db_doc_slug_done_option = '_bsk_pdf_manager_update_doc_slug_done_';
    private static $_plugin_db_doc_slug_doing_option = '_bsk_pdf_manager_update_doc_slug_doing_';
    
    private static $_plugin_db_file_size_done_option = '_bsk_pdf_manager_update_file_size_done_';
    private static $_plugin_db_file_size_doing_option = '_bsk_pdf_manager_update_file_size_doing_';

	public static $_upload_path = '';
	public static $_upload_folder = 'bsk-pdf-manager/';
    public static $_upload_path_4_ftp = '';
	public static $_upload_folder_4_ftp = 'ftp/';
    public static $_upload_url = '';
    
    public static $_upload_root_path = '';
    public static $_upload_root_appendix = '';
    
	private static $_plugin_admin_notice_message = array();

	public static $_plugin_settings_option = '_bsk_pdf_manager_pro_settings_';
	public static $_plugin_temp_option_prefix = '_bsk_pdf_manager_pro_temp_';

    public static $_default_pdf_icon_url = '';
	public static $_ajax_loader_img_url = '';
    public static $_delete_cat_icon_url = '';
    
    public static $url_to_upgrade = 'https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/how-to-upgrade-to-pro-version/';
    
    public static $_category_max_depth = 3;

    public static $_dropdown_shortcodes_pages_option = 'bsk_pdfm_dropdown_shortcodes_pages_';
	
	//objects
	public $_bsk_pdfm_pro_OBJ_dashboard = NULL;
    public $_bsk_pdfm_pro_OBJ_shortcodes = NULL;
    public $_bsk_pdfm_pro_OBJ_permalink_accessCtrl = NULL;
	
	public static function instance() {
        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof BSKPDFManager ) ) {
			self::$instance = new BSKPDFManager;

            require_once(ABSPATH . 'wp-admin/includes/file.php');
                
            //default upload folder, path, url
            $uploads = wp_upload_dir();
            self::$_upload_path = $uploads['basedir'].'/'.self::$_upload_folder;
            self::$_upload_url = $uploads['baseurl'].'/'.self::$_upload_folder;
            self::$_upload_path_4_ftp = self::$_upload_path.self::$_upload_folder_4_ftp;
            
            $site_url_no_prefix = str_replace( 'http://www.', '', site_url() );
            $site_url_no_prefix = str_replace( 'https://www.', '', $site_url_no_prefix );
            $site_url_no_prefix = str_replace( 'http://', '', $site_url_no_prefix );
            $site_url_no_prefix = str_replace( 'https://', '', $site_url_no_prefix );
            $uploads_base_url_no_prefix = str_replace( 'http://www.', '', $uploads['baseurl'] );
            $uploads_base_url_no_prefix = str_replace( 'https://www.', '', $uploads_base_url_no_prefix );
            $uploads_base_url_no_prefix = str_replace( 'http://', '', $uploads_base_url_no_prefix );
            $uploads_base_url_no_prefix = str_replace( 'https://', '', $uploads_base_url_no_prefix );
            self::$_upload_root_appendix = str_replace($site_url_no_prefix, '', $uploads_base_url_no_prefix); //general, /wp-cotnent/uploads/
            self::$_upload_root_path = str_replace(self::$_upload_root_appendix, '', $uploads['basedir']).'/'; //general, same as ABSPATH
            
            self::$_ajax_loader_img_url = BSK_PDFM_PLUGIN_URL.'images/ajax-loader.gif';
            self::$_default_pdf_icon_url = BSK_PDFM_PLUGIN_URL.'images/default_PDF_icon.png';
            self::$_delete_cat_icon_url = BSK_PDFM_PLUGIN_URL.'images/delete-2.png';
            
            
            //read plugin setting to set custom upload folder
            $plugin_settings = get_option( self::$_plugin_settings_option, false );
            if( !$plugin_settings || !is_array($plugin_settings) || count($plugin_settings) < 1 ){
                $plugin_settings = array();
            }

            add_action( 'admin_notices', array(self::$instance, 'bsk_pdf_manager_admin_notice') );
            add_action( 'admin_enqueue_scripts', array(self::$instance, 'bsk_pdf_manager_enqueue_scripts_n_css') );
            add_action( 'wp_enqueue_scripts', array(self::$instance, 'bsk_pdf_manager_enqueue_scripts_n_css') );
                
            //include others class
            require_once( BSK_PDFM_PLUGIN_DIR.'classes/common/backend.php' );
            require_once( BSK_PDFM_PLUGIN_DIR.'classes/common/display.php' );
            require_once( BSK_PDFM_PLUGIN_DIR.'classes/common/filter-extension.php' );
            require_once( BSK_PDFM_PLUGIN_DIR.'classes/common/filter-tags.php' );
            require_once( BSK_PDFM_PLUGIN_DIR.'classes/common/data-source.php' );
            require_once( BSK_PDFM_PLUGIN_DIR.'classes/common/count-desc-bar.php' );
            
            require_once( BSK_PDFM_PLUGIN_DIR.'classes/dashboard/dashboard.php' );
            require_once( BSK_PDFM_PLUGIN_DIR.'classes/shortcodes/shortcodes.php' );
    
            
            require_once( BSK_PDFM_PLUGIN_DIR.'classes/widgets/widget.php' );
            require_once( BSK_PDFM_PLUGIN_DIR.'classes/widgets/widget-category.php' );
            
            require_once( BSK_PDFM_PLUGIN_DIR.'classes/permalink-access/permalink-access.php' );

            self::$instance->_bsk_pdfm_pro_OBJ_dashboard = new BSKPDFM_Dashboard();
            self::$instance->_bsk_pdfm_pro_OBJ_shortcodes = new BSKPDFM_Shortcodes();
            self::$instance->_bsk_pdfm_pro_OBJ_permalink_accessCtrl = new BSKPDFM_Permalink_AccessCtrl();

            //hooks
            register_activation_hook(__FILE__, array(self::$instance, 'bsk_pdf_manager_activate') );
            register_deactivation_hook( __FILE__, array(self::$instance, 'bsk_pdf_manager_deactivate') );
            register_uninstall_hook( __FILE__, 'BSKPDFManager::bsk_pdf_manager_uninstall' );

            add_action( 'widgets_init', array(self::$instance, 'bsk_pdf_manager_pro_register_widgets'));

            add_action( 'init', array(self::$instance, 'bsk_pdf_manager_post_action') );

            self::$instance->bsk_pdf_create_upload_folder_and_set_secure();

            add_action( 'plugins_loaded', array(self::$instance, 'bsk_pdf_manager_update_database'), 10 );
            add_action( 'plugins_loaded', array(self::$instance, 'bsk_pdf_manager_update_doc_slug_fun'), 16 );
            add_action( 'plugins_loaded', array(self::$instance, 'bsk_pdf_manager_update_file_size_fun'), 18 );
            add_action( 'plugins_loaded', array(self::$instance, 'bsk_pdf_manager_load_language'), 10 );
        }
        
		return self::$instance;
	}
    
    private function __construct() {
        //
    }
    
    public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__,  'Cheatin&#8217;', '1.0' );
	}
    
    public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__,  'Cheatin&#8217;', '1.0' );
	}
    
	function bsk_pdf_manager_activate( $network_wide ){
		//create or update table
        self::$instance->bsk_pdf_manager_pro_create_table();
	}
	
	function bsk_pdf_manager_deactivate(){
        wp_clear_scheduled_hook( 'bsk_pdfm_free_schedule_check_promote_weekly' );
	}
    
    function bsk_pdf_manager_update_doc_slug_fun(){
        //check db version first
        $db_version = get_option( self::$_plugin_saved_db_version_option );
        if ( version_compare( $db_version, '2.6', '<' ) ) {
            return;
        }
        $is_done = get_option( self::$_plugin_db_doc_slug_done_option, false );
        if( $is_done ){
            return;
        }
        $is_doing = get_option( self::$_plugin_db_doc_slug_doing_option, false );
        if( $is_doing ){
            return;
        }
        update_option( self::$_plugin_db_doc_slug_doing_option, true );
        
        global $wpdb;
        $sql = 'SELECT `title`, `id` '.
               'FROM `'.esc_sql($wpdb->prefix . self::$_pdfs_tbl_name).'` '.
               'WHERE `slug` is NULL OR LENGTH(`slug`) < 1 '.
               'ORDER BY `id` ASC '.
               'LIMIT 0, 100';
        $pdfs_to_process = $wpdb->get_results( $sql );
        if( !$pdfs_to_process || !is_array( $pdfs_to_process ) || count( $pdfs_to_process ) < 1 ){
            update_option( self::$_plugin_db_doc_slug_doing_option, false );
            update_option( self::$_plugin_db_doc_slug_done_option, true );
            return;
        }
        
        $all_supported_extensions = BSKPDFM_Common_Backend::get_supported_extension_with_mime_type();
        foreach( $pdfs_to_process as $pdf_record ){
            $ext_array = explode( '.', $pdf_record->title );
            if( count($ext_array) >= 2 ){
                //check if extention includes
                if( array_key_exists( $ext_array[count($ext_array) - 1], $all_supported_extensions ) ){
                    unset( $ext_array[count($ext_array) - 1] );
                }
            }
            $title_without_ext = implode( '.', $ext_array );
            $slug = BSKPDFM_Permalink_AccessCtrl::get_document_slug( $title_without_ext, $pdf_record->id );
            $data_to_update = array( 'slug' => $slug );
            $wpdb->update( $wpdb->prefix . self::$_pdfs_tbl_name, $data_to_update, array( 'id' => $pdf_record->id ) );
        }
        
        update_option( self::$_plugin_db_doc_slug_doing_option, false );
        update_option( self::$_plugin_db_doc_slug_done_option, false );

        return;
    }
	
	public static function bsk_pdf_manager_pro_remove_tables_n_options(){
		global $wpdb;
		
		delete_option( '_bsk_pdf_manager_open_target' );
		delete_option( '_bsk_pdf_manager_category_list_has_title' );
		delete_option( '_bsk_pdf_manager_pdf_order_by_' );
		delete_option( '_bsk_pdf_manager_pdf_order_' );
		delete_option( '_bsk_pdf_manager_db_ver_');
		delete_option( '_bsk_pdf_manager_rels_done_');
		delete_option( '_bsk_pdf_manager_free_to_pro_done_');
		
        $table_cats = $wpdb->prefix."bsk_pdf_manager_cats";
		$table_pdfs = $wpdb->prefix."bsk_pdf_manager_pdfs";
        $table_rels = $wpdb->prefix."bsk_pdf_manager_relationships";
        $table_user_available = $wpdb->prefix."bsk_pdf_manager_user_available";
        $table_notifications = $wpdb->prefix."notifications_tbl_name";
		
		$wpdb->query("DROP TABLE IF EXISTS $table_cats");
		$wpdb->query("DROP TABLE IF EXISTS $table_pdfs");
        $wpdb->query("DROP TABLE IF EXISTS $table_rels");
        $wpdb->query("DROP TABLE IF EXISTS $table_user_available");
		$wpdb->query("DROP TABLE IF EXISTS $table_notifications");
        
		$sql = 'DELETE FROM `'.$wpdb->options.'` WHERE `option_name` LIKE "_bsk_pdf_manager%"';
		$wpdb->query( $sql );
        $sql = 'DELETE FROM `'.$wpdb->options.'` WHERE `option_name` LIKE "_bsk_pdfm%"';
		$wpdb->query( $sql );
	}
	
	public static function bsk_pdf_manager_uninstall(){
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        $has_active_pro_verison = false;
        $plugins = get_plugins();
        foreach( $plugins as $plugin_key => $data ){
            if( 'bsk-pdf-manager-pro/bsk-pdf-manager-pro.php' == $plugin_key && 
                is_plugin_active( $plugin_key ) ){
                $has_active_pro_verison = true;
                break;
            }
        }
        if( $has_active_pro_verison == true ){
            return;
        }
        
		//create or update table
        self::bsk_pdf_manager_pro_remove_tables_n_options();
	}
    
    function bsk_pdf_manager_pro_register_widgets(){
        register_widget( "BSKPDFManagerWidget" );
        register_widget( "BSKPDFManagerWidget_Category" );
    }
	
	function bsk_pdf_manager_enqueue_scripts_n_css(){
		global $wp_version;
		
		wp_enqueue_script('jquery');
		if( is_admin() ){
			if( function_exists( 'wp_enqueue_media' ) && version_compare( $wp_version, '3.5', '>=' ) ) {
				wp_enqueue_media();
			}
			wp_enqueue_script( 'jquery-ui-datepicker' );
            wp_enqueue_style( 'jquery-ui', 
                                          BSK_PDFM_PLUGIN_URL.'css/jquery-ui.css', 
                                          array(), 
                                          filemtime(BSK_PDFM_PLUGIN_DIR.'css/jquery-ui.css') );
			wp_enqueue_script( 'jstree', 
                                          BSK_PDFM_PLUGIN_URL.'classes/dashboard/settings/jstree/dist/jstree.min.js', 
                                          array('jquery'), 
                                          filemtime(BSK_PDFM_PLUGIN_DIR.'classes/dashboard/settings/jstree/dist/jstree.min.js') );
            wp_enqueue_script( 'dateformat', 
                                          BSK_PDFM_PLUGIN_URL.'js/date.format.js', 
                                          array('jquery'), 
                                          filemtime(BSK_PDFM_PLUGIN_DIR.'js/date.format.js') );
			wp_enqueue_script( 'bsk-pdfm-pro-admin', 
                                          BSK_PDFM_PLUGIN_URL.'js/bsk_pdfm_pro_admin.js', 
                                          array('jquery', 'jquery-ui-datepicker', 'jstree', 'dateformat'), 
                                          filemtime(BSK_PDFM_PLUGIN_DIR.'js/bsk_pdfm_pro_admin.js') );
            $supported_extension_and_mime_type = BSKPDFM_Common_Backend::get_supported_extension_with_mime_type();
            wp_localize_script( 
                                'bsk-pdfm-pro-admin', 
                                        'bsk_pdfm_admin', 
                                array(
                                        'extension_and_mime' => $supported_extension_and_mime_type,
                                        'ajax_loader_url' => self::$_ajax_loader_img_url,
                                     ) 
                              );
            
            wp_enqueue_style( 'jstree', 
                                        BSK_PDFM_PLUGIN_URL.'classes/dashboard/settings/jstree/dist/themes/default/style.min.css', 
                                        array(), 
                                        filemtime(BSK_PDFM_PLUGIN_DIR.'classes/dashboard/settings/jstree/dist/themes/default/style.min.css') );
			wp_enqueue_style( 'bsk-pdf-manager-pro-admin', 
                                        BSK_PDFM_PLUGIN_URL.'css/bsk-pdf-manager-pro-admin.css', 
                                        array('jstree'), 
                                        filemtime(BSK_PDFM_PLUGIN_DIR.'css/bsk-pdf-manager-pro-admin.css') );
            
            if( isset( $_GET['page'] ) && sanitize_text_field( $_GET['page'] ) == BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['edit'] ){
                wp_enqueue_style(
                                    'bsk-pdf-manager-pro-editor',
                                    BSK_PDFM_PLUGIN_URL.'css/bsk-pdf-manager-pro-editor.css',
                                    array( 'bsk-pdf-manager-pro-admin' ),
                                    filemtime( BSK_PDFM_PLUGIN_DIR.'css/bsk-pdf-manager-pro-editor.css' )
                                );
            }
		}else{
            $default_styles_version = '2.0';
            $plugin_settings = get_option( self::$_plugin_settings_option, '' );
            if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
                if( isset($plugin_settings['default_styles_version']) ){
                    $default_styles_version = $plugin_settings['default_styles_version'];
                }
            }
            
            if( $default_styles_version != '2.0' ){
                wp_enqueue_style( 'bsk-pdf-manager-pro-css', 
                                        BSK_PDFM_PLUGIN_URL.'css/bsk-pdf-manager-pro-v_'.str_replace('.', '_', $default_styles_version).'.css', 
                                        array(), 
                                        filemtime(BSK_PDFM_PLUGIN_DIR.'css/bsk-pdf-manager-pro-v_'.str_replace('.', '_', $default_styles_version).'.css') );
            }else{
                wp_enqueue_style( 'bsk-pdf-manager-pro-css', 
                                        BSK_PDFM_PLUGIN_URL.'css/bsk-pdf-manager-pro.css', 
                                        array(), 
                                        filemtime(BSK_PDFM_PLUGIN_DIR.'css/bsk-pdf-manager-pro.css') );
            }

            wp_enqueue_script( 'bsk-pdf-manager-pro', 
                                          BSK_PDFM_PLUGIN_URL.'js/bsk_pdf_manager_pro.js', 
                                          array('jquery'), 
                                          filemtime(BSK_PDFM_PLUGIN_DIR.'js/bsk_pdf_manager_pro.js') );
			
            wp_localize_script( 'bsk-pdf-manager-pro', 'bsk_pdf_pro', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		}
	}
	
	function bsk_pdf_manager_admin_notice(){
		$warning_message = array();
		$error_message = array();
		
		//admin message
		if (count(self::$_plugin_admin_notice_message) > 0){
			foreach(self::$_plugin_admin_notice_message as $msg){
				if($msg['type'] == 'ERROR'){
					$error_message[] = $msg['message'];
				}
				if($msg['type'] == 'WARNING'){
					$warning_message[] = $msg['message'];
				}
			}
		}
		
		//show error message
		if(count($warning_message) > 0){
			echo '<div class="update-nag">';
			foreach($warning_message as $msg_to_show){
				echo '<p>'.esc_html($msg_to_show).'</p>';
			}
			echo '</div>';
		}
		
		//show error message
		if(count($error_message) > 0){
			echo '<div class="error">';
			foreach($error_message as $msg_to_show){
				echo '<p>'.esc_html($msg_to_show).'</p>';
			}
			echo '</div>';
		}
	}

	function bsk_pdf_manager_pro_create_table(){
		global $wpdb;
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$charset_collate = $wpdb->get_charset_collate();
		
		$table_name = $wpdb->prefix . self::$_cats_tbl_name;
		$sql = "CREATE TABLE $table_name (
                                              `id` int(11) NOT NULL AUTO_INCREMENT,
                                              `type` varchar(8) DEFAULT 'CAT',
                                              `parent` int(11) DEFAULT 0,
                                              `title` varchar(512) NOT NULL,
                                              `description` text DEFAULT NULL,
                                              `password` varchar(32) DEFAULT NULL,
                                              `empty_message` varchar(512) DEFAULT NULL,
                                              `last_date` datetime DEFAULT NULL,
                                              UNIQUE KEY id (id)
                                            ) $charset_collate;";
        dbDelta( $sql );
		
		$table_name = $wpdb->prefix . self::$_pdfs_tbl_name;
		$sql = "CREATE TABLE $table_name (
                                          `id` int(11) NOT NULL AUTO_INCREMENT,
                                          `cat_id` varchar(32) NOT NULL,
                                          `order_num` int(11) DEFAULT NULL,
                                          `thumbnail_id` int(11) DEFAULT NULL,
                                          `title` varchar(512) DEFAULT NULL,
                                          `slug` varchar(256) DEFAULT NULL,
                                          `file_name` varchar(512) NOT NULL,
                                          `description` text DEFAULT NULL,
                                          `by_media_uploader` int(11) DEFAULT 0,
                                          `media_ext` varchar(32) DEFAULT NULL,
                                          `last_date` datetime DEFAULT NULL,
                                          `weekday` varchar(8) DEFAULT NULL,
                                          `download_count` int(11) DEFAULT 0,
                                          `publish_date` datetime DEFAULT NULL,
                                          `expiry_date` datetime DEFAULT NULL,
                                          `trash` tinyint(1) NOT NULL DEFAULT 0,
                                          `pending` tinyint(1) NOT NULL DEFAULT 0,
                                          `draft` tinyint(1) NOT NULL DEFAULT 0,
                                          `author_id` int(11) NOT NULL DEFAULT 0,
                                          `size` int(11) NOT NULL DEFAULT 0,
                                          `redirect_permalink` tinyint(1) NOT NULL DEFAULT 0,
                                          UNIQUE KEY id (id)
                                        ) $charset_collate;";
		dbDelta($sql);
        
        $table_name = $wpdb->prefix . self::$_rels_tbl_name;
		$sql = "CREATE TABLE $table_name (
                                          `id` int(11) NOT NULL AUTO_INCREMENT,
                                          `type` varchar(8) DEFAULT 'CAT',
                                          `pdf_id` int(11) NOT NULL,
                                          `cat_id` int(11) NOT NULL,
                                          UNIQUE KEY id (id)
                                        ) $charset_collate;";
		dbDelta($sql);

        $table_name = $wpdb->prefix . self::$_user_available_tbl_name;
		$sql = "CREATE TABLE $table_name (
                                          `id` int(11) NOT NULL AUTO_INCREMENT,
                                          `user_id` int(11) NOT NULL,
                                          `type` varchar(8) NOT NULL,
                                          `value` int(11) NOT NULL,
                                          UNIQUE KEY id (id)
                                        ) $charset_collate;";
		dbDelta($sql);

        $table_name = $wpdb->prefix . self::$_notifications_tbl_name;
		$sql = "CREATE TABLE $table_name (
                                            `id` int(11) NOT NULL AUTO_INCREMENT,
                                            `status` tinyint(1) NOT NULL DEFAULT 1,
                                            `name` varchar(256) NOT NULL,
                                            `trigger_by` varchar(32) NOT NULL,
                                            `auto_meta` text DEFAULT NULL,
                                            `send_to_type` varchar(32) DEFAULT NULL,
                                            `send_to_type_meta` text DEFAULT NULL,
                                            `from_name` varchar(256) DEFAULT NULL,
                                            `from_email` varchar(256) DEFAULT NULL,
                                            `subject` varchar(256) NOT NULL,
                                            `body` text DEFAULT NULL,
                                          UNIQUE KEY id (id)
                                        ) $charset_collate;";
		dbDelta($sql);

		update_option( self::$_plugin_saved_db_version_option, self::$_plugin_db_version );
        //for new install, doesn't need to build relationships
        update_option( self::$_plugin_db_rels_done_option, 'YES' );
	}
	
	function bsk_pdf_manager_update_database(){
		
		$db_version = get_option( self::$_plugin_saved_db_version_option );
		if ( version_compare( $db_version, self::$_plugin_db_version, '>=' ) ) {
			return;
		}
		
        $is_upgrading = get_option( self::$_plugin_db_upgrading, false );
        if( $is_upgrading ){
            //already have instance doing upgrading so exit this one
            return;
        }
        update_option( self::$_plugin_db_upgrading, true );
        
		global $wpdb;
					
        
        //upgrade to 2.2
        if ( version_compare( $db_version, '2.2', '<' ) ) {
            $table_name = $wpdb->prefix . self::$_pdfs_tbl_name;
            $sql = 'ALTER TABLE `'.$table_name.'` ADD `download_count` INT DEFAULT 0 AFTER `weekday`;';
            $wpdb->query( $sql );
        }
        
        //upgrade db version to 2.3
        if ( version_compare( $db_version, '2.3', '<' ) ) {
            $table_name = $wpdb->prefix . self::$_pdfs_tbl_name;
            $sql = 'ALTER TABLE `'.$table_name.'` ADD `media_ext` VARCHAR(32) DEFAULT NULL AFTER `by_media_uploader`;';
            $wpdb->query( $sql );
        }
        
        //upgrade db version to 2.4
        if ( version_compare( $db_version, '2.4', '<' ) ) {
            $table_name = $wpdb->prefix . self::$_pdfs_tbl_name;
            $sql = 'ALTER TABLE `'.$table_name.'` `description` `description` TEXT DEFAULT NULL';
            $wpdb->query( $sql );
            
            $table_name = $wpdb->prefix . self::$_cats_tbl_name;
            $sql = 'ALTER TABLE `'.$table_name.'` `description` `description` TEXT DEFAULT NULL';
            $wpdb->query( $sql );
        }
        
        //upgrade db version to 2.5
        if ( version_compare( $db_version, '2.5', '<' ) ) {
            $table_name = $wpdb->prefix . self::$_cats_tbl_name;
            $sql = 'ALTER TABLE `'.$table_name.'` ADD `type` VARCHAR(8) NULL DEFAULT \'CAT\' AFTER `id`;';
            $wpdb->query( $sql );
            
            $table_name = $wpdb->prefix . self::$_rels_tbl_name;
            $sql = 'ALTER TABLE `'.$table_name.'` ADD `type` VARCHAR(8) NULL DEFAULT \'CAT\' AFTER `id`;';
            $wpdb->query( $sql );
        }
        
        //upgrade db version to 2.6
        if ( version_compare( $db_version, '2.6', '<' ) ) {
            $table_name = $wpdb->prefix . self::$_pdfs_tbl_name;
            $sql = 'ALTER TABLE `'.$table_name.'` ADD `slug` VARCHAR(256) NULL AFTER `title`;';
            $wpdb->query( $sql );
            
            $sql = 'ALTER TABLE `'.$table_name.'` ADD `trash` TINYINT(1) NOT NULL DEFAULT \'0\' AFTER `expiry_date`;';
            $wpdb->query( $sql );
        }

        //upgrade db version to 2.7
        if ( version_compare( $db_version, '2.7', '<' ) ) {
            $table_name = $wpdb->prefix . self::$_pdfs_tbl_name;
            $sql = 'ALTER TABLE `'.$table_name.'` ADD `pending` tinyint(1) NOT NULL DEFAULT 0 AFTER `trash`;';
            $wpdb->query( $sql );

            $sql = 'ALTER TABLE `'.$table_name.'` ADD `author_id` int(11) NOT NULL DEFAULT 0 AFTER `pending`;';
            $wpdb->query( $sql );
        }

        //upgrade db version to 2.8
        if ( version_compare( $db_version, '2.8', '<' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            $charset_collate = $wpdb->get_charset_collate();
            $table_name = $wpdb->prefix . self::$_user_available_tbl_name;
            $sql = "CREATE TABLE $table_name (
                                            `id` int(11) NOT NULL AUTO_INCREMENT,
                                            `user_id` int(11) NOT NULL,
                                            `type` varchar(8) NOT NULL,
                                            `value` int(11) NOT NULL,
                                            UNIQUE KEY id (id)
                                            ) $charset_collate;";
            dbDelta($sql);
        }

        //upgrade db version to 2.9
        if ( version_compare( $db_version, '2.9', '<' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            
            $table_name = $wpdb->prefix . self::$_pdfs_tbl_name;
            $sql = 'ALTER TABLE `'.$table_name.'` ADD `size` int(11) NOT NULL DEFAULT 0 AFTER `author_id`;';
            $wpdb->query( $sql );

            $sql = 'ALTER TABLE `'.$table_name.'` ADD `redirect_permalink` tinyint(1) NOT NULL DEFAULT 0 AFTER `size`;';
            $wpdb->query( $sql );

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            $charset_collate = $wpdb->get_charset_collate();
            $table_name = $wpdb->prefix . self::$_notifications_tbl_name;
            $sql = "CREATE TABLE $table_name (
                                                `id` int(11) NOT NULL AUTO_INCREMENT,
                                                `status` tinyint(1) NOT NULL DEFAULT 1,
                                                `name` varchar(256) NOT NULL,
                                                `trigger_by` varchar(32) NOT NULL,
                                                `auto_meta` text DEFAULT NULL,
                                                `send_to_type` varchar(32) DEFAULT NULL,
                                                `send_to_type_meta` text DEFAULT NULL,
                                                `from_name` varchar(256) DEFAULT NULL,
                                                `from_email` varchar(256) DEFAULT NULL,
                                                `subject` varchar(256) NOT NULL,
                                                `body` text DEFAULT NULL,
                                                UNIQUE KEY id (id)
                                            ) $charset_collate;";
            dbDelta($sql);
        }

        //upgrade db version to 3.0
        if ( version_compare( $db_version, '3.0', '<' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            $table_name = $wpdb->prefix . self::$_pdfs_tbl_name;
            $sql = 'ALTER TABLE `'.$table_name.'` ADD `draft` tinyint(1) NOT NULL DEFAULT 0 AFTER `pending`;';
            $wpdb->query( $sql );

        }
        
        //update db version to latest
		update_option( self::$_plugin_saved_db_version_option, self::$_plugin_db_version );
        delete_option( self::$_plugin_db_upgrading );
	}

    function bsk_pdf_manager_update_file_size_fun(){
        //check db version first
        $db_version = get_option( self::$_plugin_saved_db_version_option );
        if ( version_compare( $db_version, '2.9', '<' ) ) {
            return;
        }
        $is_done = get_option( self::$_plugin_db_file_size_done_option, false );
        if( $is_done ){
            return;
        }
        $is_doing = get_option( self::$_plugin_db_file_size_doing_option, false );
        if( $is_doing ){
            return;
        }
        update_option( self::$_plugin_db_file_size_doing_option, true );

        global $wpdb;
        $sql = 'SELECT `id`, `by_media_uploader`, `file_name` '.
               'FROM `'.esc_sql($wpdb->prefix . self::$_pdfs_tbl_name).'` ' .
               'WHERE ( `by_media_uploader` > 0 OR LENGTH(`file_name`) > 3 ) ' .
               'AND `size` = 0 ' .
               'ORDER BY `id` ASC ' .
               'LIMIT 0, 100';
        $pdfs_to_process = $wpdb->get_results( $sql );
        if( !$pdfs_to_process || !is_array( $pdfs_to_process ) || count( $pdfs_to_process ) < 1 ){
            update_option( self::$_plugin_db_file_size_doing_option, false );
            update_option( self::$_plugin_db_file_size_done_option, true );
            return;
        }

        foreach( $pdfs_to_process as $pdf_record ){
            $file_size = -1;
            if ( $pdf_record->by_media_uploader > 1 ) {
                $file_path = get_attached_file( $pdf_record->by_media_uploader );
                $file_size = filesize( $file_path );
            } else {
                if( file_exists( BSKPDFManager::$_upload_root_path.$pdf_record->file_name ) ) {
                    $file_size = filesize( BSKPDFManager::$_upload_root_path.$pdf_record->file_name );
                }
            }
            $data_to_update = array( 'size' => $file_size );
            $wpdb->update( $wpdb->prefix . self::$_pdfs_tbl_name, $data_to_update, array( 'id' => $pdf_record->id ) );
        }

        update_option( self::$_plugin_db_file_size_doing_option, false );

        return;
    }
    
	function bsk_pdf_manager_post_action(){
        if (isset($_POST['bsk_pdf_manager_action'])) {
            $bsk_pdfm_action = sanitize_text_field($_POST['bsk_pdf_manager_action']);
            if ($bsk_pdfm_action && strlen($bsk_pdfm_action) > 1){
                do_action( 'bsk_pdf_manager_' . $bsk_pdfm_action, $_POST );
            }
        }
	}
	
	function bsk_pdf_create_upload_folder_and_set_secure(){
        
        $cannot_create_message = __( 'Directory <strong>%s</strong> can not be created. Please create it first yourself.', 'bskpdfmanager' );
        $not_writable_message = __( 'Directory <strong>%s</strong> is not writeable ! Check <a href="http://codex.wordpress.org/Changing_File_Permissions">http://codex.wordpress.org/Changing_File_Permissions</a> for how to set the permission.', 'bskpdfmanager' );
        
		//create folder to upload 
		if (!is_dir(self::$_upload_path)) {
			if ( !wp_mkdir_p(self::$_upload_path) ) {
                
                $message = sprintf($cannot_create_message, self::$_upload_path);
				self::$_plugin_admin_notice_message['upload_folder_missing']  = array( 'message' => $message, 'type' => 'ERROR' );
			}
		}
		
		if (!is_writeable(self::$_upload_path)) {

            $message = sprintf( $not_writable_message, self::$_upload_path );
			self::$_plugin_admin_notice_message['upload_folder_not_writeable']  = array( 'message' => $message, 'type' => 'ERROR');
		}

		//copy file to upload foloder
		if (!file_exists(self::$_upload_path.'/index.php')) {
			copy( plugin_dir_path(__FILE__).'/assets/index.php', self::$_upload_path.'/index.php' );
		}
        
        //create folder for ftp upload
		if (!is_dir(self::$_upload_path_4_ftp)) {
			if (!wp_mkdir_p(self::$_upload_path_4_ftp)) {
                
                $message = sprintf( $cannot_create_message, $_upload_folder_path );
				self::$_plugin_admin_notice_message['upload_folder_missing_4_ftp']  = array( 'message' => $message, 'type' => 'ERROR' );
			}
		}
		
		if (!is_writeable(self::$_upload_path_4_ftp)) {
            
			$message = sprintf( $not_writable_message, $_upload_folder_path );
			self::$_plugin_admin_notice_message['upload_folder_not_writeable_4_ftp']  = array( 'message' => $message, 'type' => 'ERROR' );
        }
        
        //copy file to upload foloder
		if (!file_exists(self::$_upload_path_4_ftp.'/index.php')) {
			copy( plugin_dir_path(__FILE__).'/assets/index.php', self::$_upload_path_4_ftp.'/index.php' );
		}
	}
    
    function bsk_pdf_manager_load_language(){
        
        $plugin_settings = get_option( self::$_plugin_settings_option, '' );
        if( $plugin_settings && is_array( $plugin_settings ) && count( $plugin_settings ) > 0 ){
            if( isset( $plugin_settings['language'] ) && $plugin_settings['language'] == 'ENGLISH' ){
                return;
            }
        }
        
        load_plugin_textdomain( 'bskpdfmanager', false, basename( BSK_PDFM_PLUGIN_DIR ) . '/languages/' );
    }
    
}//end of class

BSKPDFManager::instance();
