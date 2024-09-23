<?php

class BSKPDFM_Dashboard_Settings_Permalinks_AccessCtrl {
	
	private static $_bsk_pdf_settings_page_url = '';
    
    private static $_bsk_default_permalink_base = 'bsk-pdf-manager';
	   
	public function __construct() {
		
		self::$_bsk_pdf_settings_page_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['setting'] );
		
		add_action( 'bsk_pdf_manager_permalink_settings_save', array($this, 'bsk_pdf_manager_settings_permalink_tab_save_fun') );
	}

	function show_settings( $plugin_settings ){
        
        flush_rewrite_rules( true );
        
		$default_enable_permalink = false;
		$default_permalink_base = self::$_bsk_default_permalink_base;
        $permalink_redirect_to = 'NO';
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
			
			if( isset($plugin_settings['enable_permalink']) ){
				$default_enable_permalink = $plugin_settings['enable_permalink'];
			}

			if( isset($plugin_settings['permalink_redirect_to']) ){
				$permalink_redirect_to = $plugin_settings['permalink_redirect_to'];
			}
		}
	?>
    <form action="<?php echo self::$_bsk_pdf_settings_page_url ?>" method="POST" id="bsk_pdfm_permalink_settings_form_ID">
    <div class="bsk_pdf_manager_settings_permalink_tab" style="width:90%;">
        <?php
        $permalink_structure = get_option( 'permalink_structure' );
        if ( ! $permalink_structure ) {
            $permalinks_url = admin_url( 'options-permalink.php' );
        ?>
        <p>
            <span class="bskpdfm_invalid">
					<?php esc_html_e( 'Permalinks are not in the correct format.', 'bskpdfmanager' ); ?>
				</span>
			<br/>
			<span class='bskpdfm_settings_description'>
				<?php
				printf( esc_html__( 'Change the %sWordPress Permalink Settings%s from default to any of the other options to get started.', 'bskpdfmanager' ), '<a href="' . esc_url( $permalinks_url ) . '">', '</a>' );
				?>
			</span>
        </p>
        <?php
        } else {
        ?>
        <p>
        	<input type="checkbox" name="bsk_pdf_manager_enable_permalink" id="bsk_pdf_manager_enable_permalink_ID" value="1" <?php echo $default_enable_permalink ? 'checked="checked"' : '' ?> /> <label for="bsk_pdf_manager_enable_permalink_ID"><?php esc_html_e( 'Enable Permalink', 'bskpdfmanager' ); ?></label>
        </p>
        <p><?php esc_html_e( 'This offers you the ability to create a custom URL structure for your permalinks. Custom URL structures can improve the aesthetics, usability, and forward-compatibility of your links.', 'bskpdfmanager' ); ?>
        </p>
        <div id="bsk_pdf_manager_permalink_settings_containder_ID" style="display:<?php echo $default_enable_permalink ? 'block' : 'none'; ?>">
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <td style="width: 25%;"><strong>URL structure base</strong></td>
                        <td>
							<input name="bsk_pdf_manager_permalink_base" id="url_structure_base_ID" type="text" value="<?php echo $default_permalink_base; ?>" class="regular-text code bsk-pdfm-permalink-base" autocomplete="off" disabled />
							<p>The above base <span class="bsk-pdfm-permalink-demo-base"><?php echo $default_permalink_base; ?></span> would make your documents' links like: <span class="bsk-pdfm-permalink-demo-url"><?php echo site_url(); ?>/<span class="bsk-pdfm-permalink-demo-base"><?php echo $default_permalink_base; ?></span>/2021-septempber-newsletter/</span></p>
						</td>
                    </tr>
					<?php
					$redirect_permalink_to_url_display = 'none';
					$permalink_only_display = 'none';
					if ( $permalink_redirect_to == 'YES' ) {
						$redirect_permalink_to_url_display= 'block';
					} else {
						$permalink_only_display = 'block';
					}
					?>
					<tr>
                        <td style="width: 25%;"><strong>Redirect the permalink to PDF URL</strong></td>
                        <td>
							<label><input class="bsk-pdfm-permalink-to-url-global-settings-radio" type="radio" name="bsk_pdf_manager_permalink_redirect" value="YES" disabled /> Yes</label>
							<label style="margin-left: 20px;"><input class="bsk-pdfm-permalink-to-url-global-settings-radio" type="radio" name="bsk_pdf_manager_permalink_redirect" value="NO" checked disabled /> No</label>
							<p style="display: <?php echo $redirect_permalink_to_url_display; ?>;" id="bsk_pdfm_redirect_permalink_to_url_desc_ID">Permalinks will be redirected to the PDF/document file URL( ex: <span class="bsk-pdfm-permalink-demo-url"><?php echo site_url(); ?>/wp-content/uploads/bsk-pdf-manager/2022/01/2021-septempber-newsletter.pdf</span> ) instead of reading the file content and returning it to the front end user. Front end users will <strong>see the file URL</strong>.</span></p>
							<p style="display: <?php echo $permalink_only_display; ?>;" id="bsk_pdfm_permalink_only_desc_ID">Read the file data and return the data to the browser, the front end user will <strong>only see the permalink</strong>. It supports changing a specific PDF/document to redirect the permalink to the file URL on the PDF/document editing interface.</p>
						</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p style="margin-top:20px;">
        	<input type="button" id="bsk_pdf_manager_settings_permalink_tab_save_form_ID" class="button-primary" value="<?php esc_attr_e( 'Save Permalink Settings', 'bskpdfmanager' ); ?>" />
            <input type="hidden" name="bsk_pdf_manager_action" value="permalink_settings_save" />
        </p>
        <?php echo wp_nonce_field( plugin_basename( __FILE__ ), 'bsk_pdf_manager_settings_permalink_tab_save_oper_nonce', true, false ); ?>
        <?php 
        } //end of  if ( ! $permalink_structure ) { } else { 
        ?>
    </div>
    </form>
    <?php
	}
	
	function bsk_pdf_manager_settings_permalink_tab_save_fun( $data ){
		global $wpdb, $current_user;
		//check nonce field
		if ( !wp_verify_nonce( sanitize_text_field($data['bsk_pdf_manager_settings_permalink_tab_save_oper_nonce']), plugin_basename( __FILE__ ) )) {
			wp_die( esc_html__( 'Security issue, please refresh page and test again', 'bskpdfmanager' ) );
		}
		
		if (!BSKPDFM_Common_Backend::bsk_pdfm_current_user_can()) {
            wp_die( esc_html__( 'You are now allowed to do this', 'bskpdfmanager' ) );
        }
		
		$enable_permalink = isset( $data['bsk_pdf_manager_enable_permalink'] ) ? true : false;
		$default_permalink_base = sanitize_text_field( $data['bsk_pdf_manager_permalink_base'] );
        $permalink_redirect_to = sanitize_text_field( $data['bsk_pdf_manager_permalink_redirect'] );
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
		if( !$plugin_settings || !is_array($plugin_settings) || count($plugin_settings) < 1 ){
			$plugin_settings = array();
		}
		$plugin_settings['enable_permalink'] = $enable_permalink;
		$plugin_settings['permalink_base'] = 'bsk-pdf-manager';
		$plugin_settings['permalink_redirect_to'] = $permalink_redirect_to;

		update_option( BSKPDFManager::$_plugin_settings_option, $plugin_settings );
		
		$redirect_url = add_query_arg( 'target', 'permalinks', self::$_bsk_pdf_settings_page_url );
		wp_redirect( $redirect_url );
	}
	
}