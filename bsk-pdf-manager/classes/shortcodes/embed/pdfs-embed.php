<?php

class BSKPDFM_Shortcodes_PDFs_Embeded {

	public function __construct() {
        add_shortcode( 'bsk-pdfm-pdfs-embed', array( $this, 'bsk_pdf_manager_show_pdfs_in_embeded' ) );
	}


	function bsk_pdf_manager_show_pdfs_in_embeded( $atts ){
		
        $shortcode_default_atts = array(
            'id' => '0',
            'width' => '100%',
            'height' => '500',
            'border' => '',
            'border_color' => '',
            'disable_right_click' => 'NO',
            'show_toolbar' => 'YES',
            'text_button' => 'YES',
            'draw_button' => 'YES',
            'stamp_button' => 'YES',
            'download_button' => 'YES',
            'print_button' => 'YES',
            'open_file_button' => 'YES',
            'text_selection_tool' => 'YES',
            'document_properties_menu' => 'YES',
        );

        $shortcode_atts = shortcode_atts(
             $shortcode_default_atts,
             $atts
        );

        $pdf_document_id = intval( $shortcode_atts['id'] );
        if ( $pdf_document_id < 1 ) {
            return '<p>'.esc_html__( 'Invalid document ID: ' . $shortcode_atts['id'], 'bskpdfmanager' ).'</p>';
        }

        $pdf_obj = BSKPDFM_Common_Data_Source::get_document_obj_by_id( $pdf_document_id );
        if ( $pdf_obj == false ) {
            return '<p>'.esc_html__( 'Not found the document with ID: ' . $pdf_document_id, 'bskpdfmanager' ).'</p>';
        }
        $file_path = '';
        $file_ext = '';
        $file_url = '';
        if ( $pdf_obj->file_name ) {
            $file_path = BSKPDFManager::$_upload_root_path.$pdf_obj->file_name;
            $file_ext = pathinfo( $pdf_obj->file_name, PATHINFO_EXTENSION );
            $file_url = site_url().'/'.$pdf_obj->file_name;
        }

        if ( !file_exists( $file_path ) ) {
            return '<p>'.esc_html__( 'Not found the file: ' . $file_path, 'bskpdfmanager' ).'</p>';
        }

        $file_ext = strtolower( $file_ext );
        if ( $file_ext != 'pdf' ) {
            return '<p>'.esc_html__( 'Not a PDF file.', 'bskpdfmanager' ).'</p>';
        }
        
		//read plugin settings
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
            //
		}

        $embeded_viewer_settings_array = array(
            'disable_right_click' => '',
            'show_toolbar' => '',
            'text_button' => '',
            'draw_button' => '',
            'stamp_button' => '',
            'download_button' => '',
            'print_button' => '',
            'open_file_button' => '',
            'text_selection_tool' => '',
            'document_properties_menu' => ''
        );
        $shortcodes_embeded_viewer_settings = array();
        foreach ( $embeded_viewer_settings_array as $key => $value ) {
            $shortcode_atts_val = strtoupper( $shortcode_atts[$key] );
            if ( $shortcode_atts_val != 'YES' && $shortcode_atts_val != 'NO' ) {
                //use default
                $shortcode_atts_val = $shortcode_default_atts[$key];
            }

            if ( $shortcode_atts_val == 'YES' ) {
                $shortcodes_embeded_viewer_settings[$key] = true;
            } else if ( $shortcode_atts_val == 'NO' ) {
                $shortcodes_embeded_viewer_settings[$key] = false;
            }
        }

        //read global embeded viewer settings & get parameters
        $embedded_viewer_settings = BSKPDFM_Common_Display::get_embedded_viewer_settings( $shortcodes_embeded_viewer_settings );

        //width & height
        $width = $shortcode_atts['width'];
        if ( strpos( $width, '%') !== false ) {
            $width = intval( $width );
            if ( $width < 10 ) {
                $width = '100';
            }
            $width = $width . '%';
        } else if ( strpos( $width, 'px') !== false ) {
            $width = intval( $width );
            if ( $width < 10 ) {
                $width = '100%';
            } else {
                $width = $width . 'px';
            }
        } else {
            $width = intval( $width );
            $width = $width . 'px';
        }

        $height = intval( $shortcode_atts['height'] );
        if ( $height < 10 ) {
            $height = '500';
        }
        $height = $height . 'px';

        //border
        $style_string = '';
        if ( $shortcode_atts['border'] && $shortcode_atts['border_color'] ) {
            $border = intval( $shortcode_atts['border'] );
            $border_color = $shortcode_atts['border_color'];
            if ( substr( $border_color, 0, 1 ) != '#' ) {
                $border_color = '#' . $border_color;
            }
            if ( ! preg_match('/^#[a-f0-9]{6}$/i', $border_color) ) {
                $border_color = '#FFFFFF';
            }
            $style_string = 'style="border: ' . $border .'px solid ' . $border_color . '";';
        }

		//output docment content
        if ( is_array( $embedded_viewer_settings ) && isset( $embedded_viewer_settings ) && $file_ext == 'pdf' ) {
            ob_start();
            ?>
            <div class="bsk-pdfm-output-container embeded-pdf-container">
                <iframe class="responsive-iframe" src="<?php echo BSK_PDFM_PLUGIN_URL . 'pdfjs/web/viewer.html?file=' . $file_url . $embedded_viewer_settings['paras']; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" <?php echo $style_string; ?>></iframe>
            </div><!-- //end for bsk-pdfm-output-container embeded-pdf-container-->
            <?php
            $html_content = ob_get_contents();
            ob_end_clean();
        }

        return $html_content;
	} //end of function

}//end of class
