<?php

class BSKPDFM_Shortcodes {
    
    public $_pdfs_OBJ_ul_ol = NULL;
    public $_pdfs_OBJ_dropdown = NULL;
    public $_pdfs_OBJ_columns = NULL;

    public $_category_OBJ_ul_ol = NULL;
    public $_category_OBJ_dropdown = NULL;
    public $_category_OBJ_columns = NULL;

    public $_pdfs_OBJ_embed = NULL;
    
	public function __construct() {
        
        require_once( 'pdfs/pdfs-dropdown.php' );
        require_once( 'pdfs/pdfs-ul-ol.php' );
        require_once( 'pdfs/pdfs-columns.php' );
        
        $this->_pdfs_OBJ_ul_ol = new BSKPDFM_Shortcodes_PDFs_UL_OL();
        $this->_pdfs_OBJ_dropdown = new BSKPDFM_Shortcodes_PDFs_Dropdown();
        $this->_pdfs_OBJ_columns = new BSKPDFM_Shortcodes_PDFs_Columns();
        
        require_once( 'category/category.php' );
        require_once( 'category/category-dropdown.php' );
        require_once( 'category/category-ul-ol.php' );
        require_once( 'category/category-columns.php' );
        
        $this->_category_OBJ_dropdown = new BSKPDFM_Shortcodes_Category_Dropdown();
        $this->_category_OBJ_ul_ol = new BSKPDFM_Shortcodes_Category_UL_OL();
        $this->_category_OBJ_columns = new BSKPDFM_Shortcodes_Category_Columns();
        
        require_once( 'embed/pdfs-embed.php' );

        $this->_pdfs_OBJ_embed = new BSKPDFM_Shortcodes_PDFs_Embeded();
	}
    
}
