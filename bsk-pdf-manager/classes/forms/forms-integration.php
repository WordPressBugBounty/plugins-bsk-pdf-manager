<?php

abstract class BSKPDFM_Forms_Integration {

	protected $_plugin_settings;
	protected $_default_categories;
    
    public function __construct($plugin_settings, $categories) {
		$this->_plugin_settings = $plugin_settings;
        $this->_default_categories = $categories;
    }
	abstract public function load_field_integration_settings();

	protected function bsk_pdfm_forms_integration_form_field_settings() {

		?>
		<div class="bsk-pdfm-forms-integrtion-form-field-settings-container">
			<p>
				<label><input type="checkbox" name="bsk_pdfm_forms_integration_form_field_enable" id="bsk_pdfm_forms_integration_form_field_enable_ID" value="YES" class="bsk-pdfm-forms-integration-form-field-enable"/>Add the files to be upload to BSK PDF Manager</label>
			</p>
			<div class="bsk-pdfm-forms-integrtion-form-field-settings" style="display: none;">
				<h4>Select category for the files to be uploaded</h4>
				<?php
				global $wpdb, $current_user;
				
				//get all categories
				$sql = 'SELECT COUNT(*) FROM '.esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name).' WHERE 1 AND `type` LIKE "CAT"';
				$categories_count = $wpdb->get_var( $sql );
				if( $categories_count ){
					echo BSKPDFM_Common_Backend::get_category_hierarchy_checkbox( 'bsk_pdfm_forms_integration_forms_field_categories_array[]', 'bsk-pdfm-forms-integrtion-form-field-checkbox-category', array(), 'CAT', false );
				}else{
					$create_category_url = add_query_arg( 'page', 
															BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['category'], 
															admin_url('admin.php') );
					$create_category_url = add_query_arg( 'view', 'addnew', $create_category_url );
					$create_category_str = sprintf( __( 'Please %s first', 'bskpdfmanager' ), '<a href="'.esc_url($create_category_url).'">'.__('create category', 'bskpdfmanager' ).'</a>' );
					
					echo '<p>'.$create_category_str.'</p>';
				}
				?>
				<p class="bsk-pdfm-forms-integrtion-form-field-error" style="color: #FF0000"></p>
				<input type="hidden" name="bsk_pdfm_forms_integration_forms_field_checked_categories" value="<?php echo (implode(',', $this->_default_categories)); ?>" class="bsk-pdfm-forms-integrtion-form-field-checked-category-checkbox-hidden-txt" />
			</div>
		</div>
		<?php
	}
	
} //end of class

require_once 'forms-gravityforms.php';

//forms integrations objects
$gravity_forms_integration = NULL;

global $current_user;
$plugin_settings = get_option(BSKPDFManager::$_plugin_settings_option, '');
if ($plugin_settings && is_array($plugin_settings) && isset($plugin_settings['public_upload_enabled']) &&  $plugin_settings['public_upload_enabled'] == true) {
	$pub_allow_for_subscribers = false;
	$pub_allow_for_guests = false;
	$supported_form_plugins = array();
	$default_upload_categories_array = array();

	if (isset($plugin_settings['pub_allow_for_subscribers'])) {
		$pub_allow_for_subscribers = true;
	}
	if (isset($plugin_settings['pub_allow_for_guests'])) {
		$pub_allow_for_guests = true;
	}
	if (isset($plugin_settings['pub_default_upload_categories'])) {
		$default_upload_categories_array = $plugin_settings['pub_default_upload_categories'];
	}
	
	if ($pub_allow_for_guests || ($pub_allow_for_subscribers && $current_user->ID)) {
		if(isset($plugin_settings['pub_supported_form_plugins'])) {
			$supported_form_plugins = $plugin_settings['pub_supported_form_plugins'];
		}

		//Gravity Forms
		if (in_array('gravity_forms', $supported_form_plugins)) {
			$gravity_forms_integration = new BSKPDFM_Forms_GravityForms($plugin_settings, $default_upload_categories_array );
			$gravity_forms_integration->load_field_integration_settings();
		}

		//Formidable Forms
		if (in_array('formidable_forms', $supported_form_plugins)) {

		}

		//WPForms
		if (in_array('wpforms', $supported_form_plugins)) {

		}

		//Ninja Forms
		if (in_array('ninja_forms', $supported_form_plugins)) {

		}

		//Forminator Forms
		if (in_array('forminator_forms', $supported_form_plugins)) {

		}

		//Fluent Forms
		if (in_array('fluent_forms', $supported_form_plugins)) {

		}

		//Gutenverse Forms
		if (in_array('gutenverse_forms', $supported_form_plugins)) {

		}
	}
}