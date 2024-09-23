<?php

class BSKPDFM_Dashboard_Settings_General {
	
	private static $_bsk_pdf_settings_page_url = '';
	   
	public function __construct() {
		global $wpdb;
		
		self::$_bsk_pdf_settings_page_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['setting'] );
		
		add_action( 'bsk_pdf_manager_general_settings_save', array($this, 'bsk_pdf_manager_settings_general_settings_tab_save_fun') );
	}
	
	
	function show_settings( $plugin_settings ){
		$author_access_pdf_category = false;
        $editor_access_all = false;
        $organise_directory_strucutre_with_year_month = true;
        $supported_extension = false;
        $language = 'DEFAULT';
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
			if( isset($plugin_settings['author_access_pdf_category']) ){
				$author_access_pdf_category = $plugin_settings['author_access_pdf_category'];
			}
            if( isset($plugin_settings['editor_access_all']) ){
				$editor_access_all = $plugin_settings['editor_access_all'];
			}
            if( isset($plugin_settings['directory_with_year_month']) ){
                $organise_directory_strucutre_with_year_month = $plugin_settings['directory_with_year_month'];
			}
            if( isset($plugin_settings['supported_extension']) ){
                $supported_extension = $plugin_settings['supported_extension'];
			}
            if( isset($plugin_settings['language']) ){
                $language = $plugin_settings['language'];
			}
		}
		
		$author_access_pdf_category_checked = $author_access_pdf_category ? ' checked' : '';
        $editor_access_all_checked = $editor_access_all ? ' checked="checked"' : '';
        
        $language_default_checked = $language == 'DEFAULT' ? 'checked' : '';
        $language_english_checked = $language == 'ENGLISH' ? 'checked' : '';
	?>
    <form action="<?php echo esc_url( self::$_bsk_pdf_settings_page_url ); ?>" method="POST" id="bsk_pdfm_general_settings_form_ID">
    <div class="bsk_pdf_manager_settings">
        <h3><?php esc_html_e( 'Language', 'bskpdfmanager' ); ?></h3>
        <p class="bsk-pdfm-supported-extension"><?php esc_html_e( 'Currently we support Italian, French, German, Dutch，Spanish, Portuguese. It will default use same language as your Wordpress. But you may change to use English always', 'bskpdfmanager' ); ?></p>
        <p>
            <label>
                <input type="radio" name="bsk_pdf_manager_language" value="DEFAULT" <?php echo esc_attr( $language_default_checked ); ?>/> <?php esc_html_e( 'Same as Wordpress', 'bskpdfmanager' ); ?>
            </label>
            <label style="margin-left: 10px;">
                <input type="radio" name="bsk_pdf_manager_language" value="ENGLISH"  <?php echo esc_attr( $language_english_checked ); ?> /> English
            </label>
        </p>
        <hr />
        <h3><?php esc_html_e( 'Supported Document Extensions', 'bskpdfmanager' ); ?></h3>
        <p class="bsk-pdfm-supported-extension">
            <label><input type="checkbox" name="bsk_pdf_supported_extension[]" value="pdf" checked="true" disabled="true" /> pdf</label>
        </p>
        <p><?php esc_html_e( 'It not only supports PDF documents but also documents with the following extension even it is called BSK PDF Manager', 'bskpdfmanager' ); ?></p>
        <?php
        $all_extension_by_category = BSKPDFM_Common_Backend::get_available_extension_category();
        foreach( $all_extension_by_category as $category_data ){
        ?>
        <h4><?php echo esc_html( $category_data['label'] ); ?></h4>
        <p class="bsk-pdfm-supported-extension">
            <?php
            if( !$supported_extension || !is_array($supported_extension) || !in_array( 'pdf', $supported_extension ) ){
                $supported_extension = array( 'pdf' );
            }
            foreach( $category_data['extensions'] as $extension ){
                $checked_str = is_array($supported_extension) && in_array($extension, $supported_extension) ? ' checked=' : '';
            ?>
            <label>
                <input type="checkbox" name="bsk_pdf_supported_extension[]" value="<?php echo esc_attr( $extension ); ?>"<?php echo esc_attr( $checked_str ); ?>/><?php echo esc_attr( $extension ); ?>
            </label>
            <?php
            }
            ?>
        </p>
        <?php
        }
        ?>
        <p><span style="font-weight: bold;font-size: 1.2em;color: #ff5b00;">***</span><?php esc_html_e( 'You should ensure no infected file upload to your server as the plugin only check file extension.', 'bskpdfmanager' ); ?></p>
        <hr />
        <h3 style="margin-top:40px;"><?php esc_html_e( 'Statistics Setting', 'bskpdfmanager' ); ?></h3>
        <?php $download_count_label = ' ( Downloads: #COUNT# )'; ?>
        <p>
            <label>
            	<input type="checkbox" name="bsk_pdf_manager_statistics_enable" id="bsk_pdf_manager_statistics_enable_ID" value="Yes" disabled /> <?php esc_html_e( 'Enable Download Statistics', 'bskpdfmanager' ); ?>
            </label>
        </p>
        <p id="bsk_pdf_manager_download_count_label_block_ID">
            <label>
            	<input type="checkbox" name="bsk_pdf_manager_download_count_front_enable" id="bsk_pdf_manager_download_count_front_enable_ID" value="Yes" disabled /> <?php esc_html_e( 'Display download count after title in front', 'bskpdfmanager' ); ?>
            </label>
            <span id="bsk_pdf_manager_download_count_label_in_front_ID" style="display: block; margin-top: 10px;">
                <label style="display: inline-block; width: 150px;"><?php esc_html_e( 'Download count text', 'bskpdfmanager' ); ?>: </label>
                <input type="text" name="bsk_pdf_manager_download_count_label" id="bsk_pdf_manager_download_count_label_ID" value="<?php echo $download_count_label; ?>" placeholder="<?php echo $download_count_label; ?>" style="width: 25%;" disabled />
                <span style="font-style: italic;">#COUNT# <?php esc_html_e( 'will be replaced to real download count and must be included', 'bskpdfmanager' ); ?></span>
            </span>
        </p>
        <hr />
        <p style="margin-top:20px;">
        	<input type="button" id="bsk_pdf_manager_settings_general_tab_save_form_ID" class="button-primary" value="Save General Settings" />
            <input type="hidden" name="bsk_pdf_manager_action" value="general_settings_save" />
        </p>
        <?php wp_nonce_field( plugin_basename( __FILE__ ), 'bsk_pdf_manager_settings_general_tab_save_oper_nonce', true, true ); ?>
    </div>
    </form>
    <?php
	}
	
	function bsk_pdf_manager_settings_general_settings_tab_save_fun( $data ) {
		global $wpdb, $current_user;
		//check nonce field
		if( !wp_verify_nonce( sanitize_text_field($data['bsk_pdf_manager_settings_general_tab_save_oper_nonce']), plugin_basename( __FILE__ ) ) ){
			return;
		}
		
		if( !current_user_can( 'moderate_comments' ) ){
			return;
		}

        $author_access_pdf_category = false;
        $editor_access_all = false;
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
		if( !$plugin_settings || !is_array($plugin_settings) || count($plugin_settings) < 1 ){
			$plugin_settings = array();
		}
        
		$plugin_settings['author_access_pdf_category'] = $author_access_pdf_category;
        
        $plugin_settings['language'] = sanitize_text_field($data['bsk_pdf_manager_language']);
        
        if( current_user_can('manage_options') ){
            $plugin_settings['editor_access_all'] = $editor_access_all;
        }
        $plugin_settings['directory_with_year_month'] = true;
        $plugin_settings['supported_extension'] = array();
        if( isset( $data['bsk_pdf_supported_extension'] ) && is_array( $data['bsk_pdf_supported_extension'] ) ){
            foreach( $data['bsk_pdf_supported_extension'] as $extension_val ){
                $plugin_settings['supported_extension'][] = sanitize_text_field( $extension_val );
            }
        } 
        if( !in_array( 'pdf', $plugin_settings['supported_extension'] ) ){
            $plugin_settings['supported_extension'][] = 'pdf';
        }

		update_option( BSKPDFManager::$_plugin_settings_option, $plugin_settings );
	}
    
}