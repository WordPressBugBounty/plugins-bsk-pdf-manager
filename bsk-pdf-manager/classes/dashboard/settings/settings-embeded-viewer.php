<?php

class BSKPDFM_Dashboard_Embeded_Viewer {

    private static $_bsk_pdf_settings_page_url = '';

	public function __construct() {

        self::$_bsk_pdf_settings_page_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['setting'] );
		
        add_action( 'bsk_pdf_manager_embedded_viewer_settings_save', array( $this, 'bsk_pdf_manager_embedded_viewer_settings_save_fun' ) );
	}
	
	function show_settings( $plugin_settings ){
		
        $embeded_viewer_settings = array( 'enable' => false );
		if ( $plugin_settings && is_array( $plugin_settings ) && count( $plugin_settings ) > 0 ) {
			if ( isset( $plugin_settings['embedded_viewer_settings'] ) && is_array( $plugin_settings['embedded_viewer_settings'] ) && count( $plugin_settings['embedded_viewer_settings'] ) > 0 ) {
				$embeded_viewer_settings = $plugin_settings['embedded_viewer_settings'];
			}
		}

        ?>
        <div class="bsk_pdf_manager_settings_embeded_viewer_tab" style="width:90%;">
        	<div id="icon-edit" class="icon32"><br/></div>
			<h2><?php esc_html_e( 'Embedded PDF Viewer', 'bskpdfmanager' ); ?></h2>
            <p><a href="https://bannersky.com/bsk-pdf-manager/" target="_blank">BSK PDF Manager</a> helps you to use <a href="https://mozilla.github.io/pdf.js/" target="_blank">Mozilla's PDF.js</a> to display PDF content.</p>
            <p><a href="https://mozilla.github.io/pdf.js/" target="_blank">Mozilla's PDF.js</a> is licensed under the <strong>Apache License 2.0</strong> and you may check its license from <a href="https://github.com/mozilla/pdf.js/blob/master/LICENSE" target="_blank">https://github.com/mozilla/pdf.js/blob/master/LICENSE</a></p>
            <form action="<?php echo add_query_arg( 'target', 'embeded-viewer', self::$_bsk_pdf_settings_page_url ); ?>" method="POST" id="bsk_pdfm_embedded_viewer_settings_form_ID">
            <div>
                <p style="margin-top: 40px;">
                    <label>
                        <input type="checkbox" name="bsk_pdfm_enable_embedded_viewer" id="bsk_pdfm_enable_embedded_viewer_ID" value="1" <?php echo $embeded_viewer_settings['enable'] ? 'checked="checked"' : '' ?> /> <strong><?php esc_html_e( 'Enable Embedded Viewer', 'bskpdfmanager' ); ?></strong>
                    </label>
                </p>
                <p id="bsk_pdfm_enable_embedded_viewer_settings_mime_type_error_containder_ID" style="display:<?php echo $embeded_viewer_settings['enable'] ? 'block' : 'none'; ?>; margin-top: 40px;">
                    <a href="https://mozilla.github.io/pdf.js/" target="_blank">Mozilla's PDF.js</a> uses module JavaScript ( .mjs ) files in its library, but some hostings don't have the correct MIME type. If nothing displays when opening a PDF file, check the following two links to fix it.
                    <br />
                    <br />
                    <a href="https://bannersky.com/failed-to-load-module-script-expected-a-javascript-module-script-but-the-server-responded-with-a-mime-type-of/" target="_blank">Failed to load module script: Expected a JavaScript module script...</a>
                    <br />
                    <a href="https://bannersky.com/faq/bsk-pdf-manager/how-to-add-mime-type-for-mjs-file/" target="_blank">How to add MIME-Type for extension mjs?</a>
                </p>
                <?php

                    $disable_right_click = isset( $embeded_viewer_settings['disable_right_click'] ) ? $embeded_viewer_settings['disable_right_click'] : false;
                    $show_toolbar = isset( $embeded_viewer_settings['show_toolbar'] ) ? $embeded_viewer_settings['show_toolbar'] : true;
                    $text_button = isset( $embeded_viewer_settings['text_button'] ) ? $embeded_viewer_settings['text_button'] : true;
                    $draw_button = isset( $embeded_viewer_settings['draw_button'] ) ? $embeded_viewer_settings['draw_button'] : true;
                    $stamp_button = isset( $embeded_viewer_settings['stamp_button'] ) ? $embeded_viewer_settings['stamp_button'] : true;
                    $download_button = isset( $embeded_viewer_settings['download_button'] ) ? $embeded_viewer_settings['download_button'] : true;
                    $print_button = isset( $embeded_viewer_settings['print_button'] ) ? $embeded_viewer_settings['print_button'] : true;
                    $open_file_button = isset( $embeded_viewer_settings['open_file_button'] ) ? $embeded_viewer_settings['open_file_button'] : true;
                    $text_selection_tool = isset( $embeded_viewer_settings['text_selection_tool'] ) ? $embeded_viewer_settings['text_selection_tool'] : true;
                    $document_properties_menu = isset( $embeded_viewer_settings['document_properties_menu'] ) ? $embeded_viewer_settings['document_properties_menu'] : true;
  
                ?>
                <div id="bsk_pdfm_enable_embedded_viewer_settings_containder_ID" style="display:<?php echo $embeded_viewer_settings['enable'] ? 'block' : 'none'; ?>; margin-top: 60px;">
                    <h3>Settings</h3>
                    <p>The following options are only used to control the viewer toolbar in <a href="https://mozilla.github.io/pdf.js/" target="_blank">Mozilla's PDF.js</a>. It does not prevent experienced visitors from downloading, printing or copying your PDF text.</p>
                    <p>
                        <label><input name="bsk_pdfm_embedded_viewer_disable_right_click" type="checkbox" id="bsk_pdfm_embedded_viewer_disable_right_click_ID" value="1" <?php echo $disable_right_click ? 'checked="checked"' : '' ?>> <?php esc_html_e( 'Disable Right Click', 'bskpdfmanager' ); ?></label>
                    </p>
                    <p>
                        <label><input name="bsk_pdfm_embedded_viewer_show_toolbar" type="checkbox" id="bsk_pdfm_embedded_viewer_show_toolbar_ID" value="1" <?php echo $show_toolbar ? 'checked="checked"' : '' ?>> <?php esc_html_e( 'Show Tool Bar', 'bskpdfmanager' ); ?></label>
                    </p>
                    <div id="bsk_pdfm_embedded_viewer_toolbar_settings_containder_ID" style="display:<?php echo $show_toolbar ? 'block' : 'none'; ?>; margin-top: 40px;">
                        <h4>Toolbar options</h4>
                        <p>
                            <label><input name="bsk_pdfm_embedded_viewer_text_button" type="checkbox" id="bsk_pdfm_embedded_viewer_text_button_ID" value="1" <?php echo $text_button ? 'checked="checked"' : '' ?>> <?php esc_html_e( 'Text Button', 'bskpdfmanager' ); ?></label>
                        </p>
                        <p>
                            <label><input name="bsk_pdfm_embedded_viewer_draw_button" type="checkbox" id="bsk_pdfm_embedded_viewer_draw_button_ID" value="1" <?php echo $draw_button ? 'checked="checked"' : '' ?>> <?php esc_html_e( 'Draw Button', 'bskpdfmanager' ); ?></label>
                        </p>
                        <p>
                            <label><input name="bsk_pdfm_embedded_viewer_stamp_button" type="checkbox" id="bsk_pdfm_embedded_viewer_stamp_button_ID" value="1" <?php echo $stamp_button ? 'checked="checked"' : '' ?>> <?php esc_html_e( 'Add or edit images Button', 'bskpdfmanager' ); ?></label>
                        </p>
                        <p>
                            <label><input name="bsk_pdfm_embedded_viewer_print_button" type="checkbox" id="bsk_pdfm_embedded_viewer_print_button_ID" value="1" <?php echo $print_button ? 'checked="checked"' : '' ?>> <?php esc_html_e( 'Print Button', 'bskpdfmanager' ); ?></label>
                        </p>
                        <p>
                            <label><input name="bsk_pdfm_embedded_viewer_download_button" type="checkbox" id="bsk_pdfm_embedded_viewer_download_button_ID" value="1" <?php echo $download_button ? 'checked="checked"' : '' ?>> <?php esc_html_e( 'Download Button', 'bskpdfmanager' ); ?></label>
                        </p>
                        <p>
                            <label><input name="bsk_pdfm_embedded_viewer_open_file_button" type="checkbox" id="bsk_pdfm_embedded_viewer_open_file_button_ID" value="1" <?php echo $open_file_button ? 'checked="checked"' : '' ?>> <?php esc_html_e( 'Open File Button', 'bskpdfmanager' ); ?></label>
                        </p>
                        <p>
                            <label><input name="bsk_pdfm_embedded_viewer_text_selection_tool" type="checkbox" id="bsk_pdfm_embedded_viewer_text_selection_tool_ID" value="1" <?php echo $text_selection_tool ? 'checked="checked"' : '' ?>> <?php esc_html_e( 'Text Selection Tool', 'bskpdfmanager' ); ?></label>
                        </p>
                        <p>
                            <label><input name="bsk_pdfm_embedded_viewer_document_properties_menu" type="checkbox" id="bsk_pdfm_embedded_viewer_document_properties_menu_ID" value="1" <?php echo $document_properties_menu  ? 'checked="checked"' : '' ?>> <?php esc_html_e( 'Document Properties Menu', 'bskpdfmanager' ); ?></label>
                        </p>
                    </div>
                </div>
                <p style="margin-top:20px;">
                    <input type="submit" id="bsk_pdfm_enable_embedded_viewer_settings_save_form_ID" class="button-primary" value="<?php esc_attr_e( 'Save Embedded Viewer Settings', 'bskpdfmanager' ); ?>" />
                    <input type="hidden" name="bsk_pdf_manager_action" value="embedded_viewer_settings_save" />
                </p>
                <?php echo wp_nonce_field( plugin_basename( __FILE__ ), 'bsk_pdfm_settings_enable_embedded_viewer_save_oper_nonce', true, false ); ?>
            </div>
            </form>
        </div>
		<?php
	}

    function bsk_pdf_manager_embedded_viewer_settings_save_fun( $data ) {
        global $wpdb, $current_user;

        //check nonce field
		if ( !wp_verify_nonce( sanitize_text_field($data['bsk_pdfm_settings_enable_embedded_viewer_save_oper_nonce']), plugin_basename( __FILE__ ) )) {
			wp_die( esc_html__( 'Security issue, please refresh page and test again', 'bskpdfmanager' ) );
		}
		
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
		if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You are now allowed to do this', 'bskpdfmanager' ) );
        }

        $enable_embedded_viewer = isset( $data['bsk_pdfm_enable_embedded_viewer']) ? true : false;
		$disable_right_click = isset( $data['bsk_pdfm_embedded_viewer_disable_right_click']) ? true : false;
        $show_toolbar = isset( $data['bsk_pdfm_embedded_viewer_show_toolbar']) ? true : false;

        $text_button = isset( $data['bsk_pdfm_embedded_viewer_text_button']) ? true : false;
        $draw_button = isset( $data['bsk_pdfm_embedded_viewer_draw_button']) ? true : false;
        $stamp_button = isset( $data['bsk_pdfm_embedded_viewer_stamp_button']) ? true : false;
        $download_button = isset( $data['bsk_pdfm_embedded_viewer_download_button']) ? true : false;
        $print_button = isset( $data['bsk_pdfm_embedded_viewer_print_button']) ? true : false;
        $open_file_button = isset( $data['bsk_pdfm_embedded_viewer_open_file_button']) ? true : false;
        $text_selection_tool = isset( $data['bsk_pdfm_embedded_viewer_text_selection_tool']) ? true : false;
        $document_properties_menu = isset( $data['bsk_pdfm_embedded_viewer_document_properties_menu']) ? true : false;

        $embedded_viewer_settings = array();
        $embedded_viewer_settings['enable'] = $enable_embedded_viewer;
        $embedded_viewer_settings['disable_right_click'] = $disable_right_click;
        $embedded_viewer_settings['show_toolbar'] = $show_toolbar;
        $embedded_viewer_settings['text_button'] = $text_button;
        $embedded_viewer_settings['draw_button'] = $draw_button;
        $embedded_viewer_settings['stamp_button'] = $stamp_button;
        $embedded_viewer_settings['download_button'] = $download_button;
        $embedded_viewer_settings['print_button'] = $print_button;
        $embedded_viewer_settings['open_file_button'] = $open_file_button;
        $embedded_viewer_settings['text_selection_tool'] = $text_selection_tool;
        $embedded_viewer_settings['document_properties_menu'] = $document_properties_menu;

        //save to plugin settings
        $plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, array() );
        $plugin_settings['embedded_viewer_settings'] = $embedded_viewer_settings;

        update_option( BSKPDFManager::$_plugin_settings_option, $plugin_settings );
    }
    
}