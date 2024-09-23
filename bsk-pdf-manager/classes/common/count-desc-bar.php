<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class BSKPDFM_Common_Count_Desc_Bar {
    
    public static function show_count_desc_bar( 
                                                  $pdfs_count,
                                                  $shortcode_atts,
                                                  $only_container,
                                                  $only_desc
                                              ){
        //show count desc bar
        $show_or_not = BSKPDFM_Common_Display::process_shortcodes_bool_attrs('show_count_desc', $shortcode_atts);
        if( !$show_or_not ){
            return '';
        }
        
        $container = '<div class="bsk-pdfm-count-desc-container"><h3 class="bsk-pdfm-count-desc">%s</h3></div>';
        if( $only_container ){
            return sprintf( $container, '' );
        }
        
        $desc = sprintf( '%d'.esc_html__( ' record found', 'bskpdfmanager' ), $pdfs_count );
        if( $pdfs_count > 1 ){
            $desc = sprintf( '%d'.esc_html__( ' records found', 'bskpdfmanager' ), $pdfs_count );
        }else if( $pdfs_count < 1 ){
            $desc = sprintf( esc_html__( 'No record found', 'bskpdfmanager' ), $pdfs_count );
        }
        
        if( $only_desc ){
            return $desc; 
        }
        
        return sprintf( $container, $desc );
    }//end of function
    
}//end of class
