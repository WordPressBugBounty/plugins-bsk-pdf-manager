<?php

class BSKPDFM_Dashboard_Settings {
	
	private static $_bsk_pdf_settings_page_url = '';
    
    private static $_bsk_pdfm_OBJ_settings_general = NULL;
    private static $_bsk_pdfm_OBJ_settings_upload = NULL;
    private static $_bsk_pdfm_OBJ_settings_capabilities = NULL;
	private static $_bsk_pdfm_OBJ_settings_featured_image = NULL;
	private static $_bsk_pdfm_OBJ_settings_styles = NULL;
    private static $_bsk_pdfm_OBJ_settings_permalinks_accessCtrl = NULL;
    private static $_bsk_pdfm_OBJ_settings_embeded_viewer = NULL;

    var $settings_tables_ids = array( 'general-settings', 'upload', 'capabilities', 'featured-image', 'styles', 'permalinks', 'embeded-viewer' );
    
	public function __construct() {
		require_once( 'settings-general.php' );
        require_once( 'settings-upload.php' );
        require_once( 'settings-capabilities.php' );
		require_once( 'settings-featured-image.php' );
		require_once( 'settings-styles.php' );	
        require_once( 'settings-permalinks.php' );
        require_once( 'settings-embeded-viewer.php' );
		
		self::$_bsk_pdf_settings_page_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['setting'] );
        
        self::$_bsk_pdfm_OBJ_settings_general = new BSKPDFM_Dashboard_Settings_General();
        self::$_bsk_pdfm_OBJ_settings_upload = new BSKPDFM_Dashboard_Settings_Upload();
        self::$_bsk_pdfm_OBJ_settings_capabilities = new BSKPDFM_Dashboard_Settings_Capabilities();
        self::$_bsk_pdfm_OBJ_settings_featured_image = new BSKPDFM_Dashboard_Settings_Featured_Image();
        self::$_bsk_pdfm_OBJ_settings_styles = new BSKPDFM_Dashboard_Settings_Styles();
        self::$_bsk_pdfm_OBJ_settings_permalinks_accessCtrl = new BSKPDFM_Dashboard_Settings_Permalinks_AccessCtrl();
        self::$_bsk_pdfm_OBJ_settings_embeded_viewer = new BSKPDFM_Dashboard_Embeded_Viewer();
	}
	
	function show_settings(){
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
		?>
        <div class="wrap">
            <div style="width: 80%; float: left;">
                <div id="icon-edit" class="icon32"><br/></div>
			    <h2><?php esc_html_e( 'BSK PDF Settings', 'bskpdfmanager' ); ?></h2>
                <div class="wrap">
                    <h2 class="nav-tab-wrapper">
                        <a class="nav-tab nav-tab-active" href="javascript:void(0);" id="bsk_pdfm_setings_tab-general-settings"><?php esc_html_e( 'General settings', 'bskpdfmanager' ); ?></a>
                        <a class="nav-tab" href="javascript:void(0);" id="bsk_pdfm_setings_tab-upload"><?php esc_html_e( 'Upload', 'bskpdfmanager' ); ?></a>
                        <a class="nav-tab" href="javascript:void(0);" id="bsk_pdfm_setings_tab-capabilities"><?php esc_html_e( 'Backend Access', 'bskpdfmanager' ); ?></a>
                        <a class="nav-tab" href="javascript:void(0);" id="bsk_pdfm_setings_tab-featured-image"><?php esc_html_e( 'Featured Image', 'bskpdfmanager' ); ?></a>
                        <a class="nav-tab" href="javascript:void(0);" id="bsk_pdfm_setings_tab-styles"><?php esc_html_e( 'Styles', 'bskpdfmanager' ); ?></a>
                        <a class="nav-tab" href="javascript:void(0);" id="bsk_pdfm_setings_tab-permalinks"><?php esc_html_e( 'Permalinks', 'bskpdfmanager' ); ?></a>
                        <a class="nav-tab" href="javascript:void(0);" id="bsk_pdfm_setings_tab-embeded-viewer"><?php esc_html_e( 'Embeded Viewer', 'bskpdfmanager' ); ?></a>
                    </h2>
                    <div id="bsk_pdfm_setings_tab_content_wrap_ID">
                        <section><?php self::$_bsk_pdfm_OBJ_settings_general->show_settings( $plugin_settings ); ?></section>
                        <section><?php self::$_bsk_pdfm_OBJ_settings_upload->show_settings( $plugin_settings ); ?></section>
                        <section><?php self::$_bsk_pdfm_OBJ_settings_capabilities->show_settings( $plugin_settings ); ?></section>
                        <section><?php self::$_bsk_pdfm_OBJ_settings_featured_image->show_settings( $plugin_settings ); ?></section>
                        <section><?php self::$_bsk_pdfm_OBJ_settings_styles->show_settings( $plugin_settings ); ?></section>
                        <section><?php self::$_bsk_pdfm_OBJ_settings_permalinks_accessCtrl->show_settings( $plugin_settings ); ?></section>
                        <section><?php self::$_bsk_pdfm_OBJ_settings_embeded_viewer->show_settings( $plugin_settings ); ?></section>
                    </div>
                </div>
            <?php
            $target_tab = isset( $_REQUEST['target'] ) ? sanitize_text_field( $_REQUEST['target'] ) : '';
            if ( ! in_array( $target_tab, $this->settings_tables_ids ) ) {
                $target_tab = $this->settings_tables_ids[0];
            }

            $ajax_nonce = wp_create_nonce( 'bsk_pdf_manager_settings_page_ajax-oper-nonce' );
            ?>
            <input type="hidden" id="bsk_pdfm_settings_target_tab_ID" value="<?php echo esc_attr( $target_tab ); ?>" />
            <input type="hidden" id="bsk_pdf_manager_settings_page_ajax_nonce_ID" value="<?php echo esc_attr( $ajax_nonce ); ?>" />
            </div>
            <div style="width: 20%; float: left;">
                <div class="wrap" id="bsk_pdfm_help_other_product_wrap_ID" style="padding-top: 45px;">
                    <h2>&nbsp;</h2>
                    <div>
                        <?php BSKPDFM_Dashboard_Ads::show_other_plugin_of_gravity_forms_black_list(); ?>
                    </div>
                    <div style="margin-top: 20px;">
                        <?php BSKPDFM_Dashboard_Ads::show_other_plugin_of_gravity_forms_custom_validation(); ?>
                    </div>
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>
    <?php
	}
    
}