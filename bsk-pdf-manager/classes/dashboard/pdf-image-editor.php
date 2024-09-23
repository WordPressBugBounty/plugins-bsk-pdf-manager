<?php

class BSKPDFM_Dashboard_PDF_Image_Editor {

    public function __construct() {
		
	}
	
    public static function bsk_pdfm_check_imagick(){
        
        if( class_exists( 'Imagick' ) ) {
            try {
                $image_editor = new Imagick();
                $image_editor->clear();
                $image_editor->destroy();
            } catch ( Exception $e ) {
                return new WP_Error( 'imagick_not_enabled', $e->getMessage() );
            }
            
            //try to open a PDF to see if it supported
            $imagick = new Imagick();
            try {
                $pdf_path = BSK_PDFM_PLUGIN_DIR.'assets/bsk-pdfm-sample.pdf';
                $imagick->readImage( $pdf_path );
                $image_editor->clear();
                $image_editor->destroy();
            }catch ( Exception $e ) {
                $cannot_open_pdf = '<p>'.esc_html__( 'ImageMagick cannot open the test PDF file, seems PDF is not enabled for ImageMagick on your server.', 'bskpdfmanager' ).'</p>';
                $cannot_open_pdf .= '<p>'.esc_html__( 'Error message', 'bskpdfmanager' ).': '.$e->getMessage().'</p>';
                
                $link_text = '';
                if( strpos( $e->getMessage(), 'Failed to read the file' ) !== false ){
                    $link_text = sprintf( esc_html__( 'Seems ghostscript is not availbe on your server, please ask your hosting support staff to intall it. Please visit %s for more.', 'bskpdfmanager' ), '<a href="https://www.bannersky.com/imagemagick-failed-to-read-the-file-error/" target="_blank">ImageMagick "Failed to read the file" error</a>' );
                    $cannot_open_pdf .= '<p>'.$link_text.'</p>';
                }else{
                    $link_text = sprintf( esc_html__( 'You may ask your hosting support staff to re-eanble PDF for ImageMagick. Check %s to have a reference.', 'bskpdfmanager' ), '<a href="https://www.bannersky.com/imagemagick-not-authorized-error/" target="_blank">ImageMagick "not authorized" error</a>' );
                    $cannot_open_pdf .= '<p>'.$link_text.'</p>';
                }
                
                return new WP_Error( 'pdf_not_supported_in_imagick', $cannot_open_pdf ); 
            }
            
            return true;
        }
        
        $install_imagick_message = '<p>'.esc_html__( 'You cannot use this feautre becasue ImageMagick is not available on your server.', 'bskpdfmanager' ).'</p>';
        $link_text = sprintf( esc_html__( 'Please ask your hosting support staff to install ImageMagick on your server or refer to %s to install it yourself.', 'bskpdfmanager' ), '<a href="https://imagemagick.org/script/install-source.php" target="_blank">how to install ImageMagick</a>' );
        $install_imagick_message .= '<p>'.$link_text.'</p>';
    
        return new WP_Error( 'imagick_not_installed ', $install_imagick_message );
    }

} //end of class
