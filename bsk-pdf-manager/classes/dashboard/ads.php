<?php

class BSKPDFM_Dashboard_Ads {
	
	private static $_bsk_pdf_settings_page_url = '';
	private static $_bsk_plugin_support_center = 'http://www.bannersky.com/contact-us/';
	private static $_bsk_plugin_documentation_page = 'http://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/';
	   
	public function __construct() {
	}
	
    public static function show_other_plugin_of_gravity_forms_black_list(){
        $_free_url = 'https://wordpress.org/plugins/bsk-gravityforms-blacklist/';
        $_pro_url = 'https://www.bannersky.com/gravity-forms-blacklist-and-custom-validation/';
    ?>
    <div class="bsk-pdfm-prdoucts-single">
        <h3>BSK Forms Blacklist</h3>
        <p>Built to help block submissions from users using spam data or competitors info to create new entry to your site. This plugin allows you to validate a field's value against the keywords and email addresses.</p>
        <ul style="list-style: square; list-style-position: inside;">
            <li>Blacklist, white list, Email list, IP List, Invitation Codes List</li>
            <li>Block submitting by country</li>
            <li>Gravity Forms, Formidable Forms, WPForms, Contact Form 7</li>
            <li>Custom validation message</li>
            <li>Block submitting or disable notifications or go to specific confirmation page</li>
            <li>Import items( keywords ) from CSV</li>
            <li>Save / View blocked form data</li>
            <li>Notify administrators ( emails ) with blocked form data</li>
        </ul>
        <p>
            <a href="<?php echo esc_url($_pro_url); ?>" target="_blank">read more</a>
        </p>
        <div style="clear: both;"></div>
    </div>
    <?php
	}
    
    public static function show_other_plugin_of_gravity_forms_custom_validation(){
        $_pro_url = 'https://www.bannersky.com/gravity-forms-custom-validation/';
    ?>
    <div class="bsk-pdfm-prdoucts-single">
        <h3>BSK Forms Validation</h3>
        <p>This plugin allows you to validate a field's value against the rule you defined. You may use it validate phone number, age, ZIP...</p>
        <ul style="list-style: square; list-style-position: inside;">
            <li>Must be numberic value and between given values</li>
            <li>Must be given value</li>
            <li>Length must same as given number</li>
            <li>The character at the position X must be</li>
            <li>Checkbox options must all be checked</li>
            <li>Save / View blocked form data</li>
            <li>Notify administrators ( emails ) with blocked form data</li>
        </ul>
        <p>
            <a href="<?php echo esc_url($_pro_url); ?>" target="_blank">read more</a>
        </p>
        <div style="clear: both;"></div>
    </div>
    <?php
	}
    
}