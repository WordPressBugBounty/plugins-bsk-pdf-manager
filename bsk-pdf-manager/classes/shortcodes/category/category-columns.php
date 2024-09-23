<?php

class BSKPDFM_Shortcodes_Category_Columns extends BSKPDFM_Shortcodes_Category {

	public function __construct() {
        add_shortcode('bsk-pdfm-category-columns', array($this, 'bsk_pdf_manager_list_pdfs_by_cat_as_columns') );
	}
    
    function bsk_pdf_manager_list_pdfs_by_cat_as_columns( $atts, $content ){
        
        return sprintf( '<p>Display in columns only supported in %s</p>', '<a href="'.esc_url(BSKPDFManager::$url_to_upgrade).'" target="_blank">Pro version</a>' );
    }
    
}