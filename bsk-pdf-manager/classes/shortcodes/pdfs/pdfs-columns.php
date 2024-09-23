<?php

class BSKPDFM_Shortcodes_PDFs_Columns {
    
	public function __construct() {
		add_shortcode('bsk-pdfm-pdfs-columns', array($this, 'bsk_pdf_manager_show_pdfs_in_columns') );
	}
	
	function bsk_pdf_manager_show_pdfs_in_columns($atts, $content){
		
        return sprintf( '<p>Display in columns only supported in %s</p>', '<a href="'.esc_url(BSKPDFManager::$url_to_upgrade).'" target="_blank">Pro version</a>' );
		
    } //end of function
    
}