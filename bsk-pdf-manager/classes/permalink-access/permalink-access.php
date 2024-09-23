<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class BSKPDFM_Permalink_AccessCtrl {
    
	public function __construct() {
        
        add_action( 'init', array($this, 'bsk_pdfm_extern_link_fun') );
        
        $default_enable_permalink = false;
        $plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, false );
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
			if( isset($plugin_settings['enable_permalink']) ){
				$default_enable_permalink = $plugin_settings['enable_permalink'];
			}
		}
        
        $permalink_structure = get_option( 'permalink_structure' );
        if ( ! $permalink_structure ) {
            $default_enable_permalink = false;
        }
        
        if( $default_enable_permalink ){
            add_filter( 'query_vars', array ( $this, 'bsk_pdfm_permalink_query_vars' ), 0 );
            add_action( 'parse_request', array ( $this, 'bsk_pdfm_permalink_sniff_requests' ), 99 );
            add_action( 'init', array ( $this, 'bsk_pdfm_permalink_add_rewrite_rule' ), 0 );
        }
	}
    
    function bsk_pdfm_extern_link_fun(){
        if( !isset( $_GET['bskpdfm-id'] ) || intval( sanitize_text_field($_GET['bskpdfm-id']) ) < 1 ){
            return;
        }
        
        $pdf_id = intval( sanitize_text_field($_GET['bskpdfm-id']) );
        
        global $wpdb;
        
        $sql = 'SELECT `file_name`, `by_media_uploader`, `slug` FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name).'` '.
               'WHERE `id` = %d';
        
        $sql = $wpdb->prepare( $sql, $pdf_id );
        $pdf_results = $wpdb->get_results( $sql );
        if( !$pdf_results || !is_array( $pdf_results ) || count( $pdf_results ) < 1 ){
            wp_die( 'Invalid document ID: '.$pdf_id );
        }
        
        $pdf_obj = $pdf_results[0];
        if( $pdf_obj->file_name == "" &&  $pdf_obj->by_media_uploader < 1 ){
            wp_die( 'Invalid file name and nor uploaded by, document ID: '.$pdf_id );
        }
        
        $default_enable_permalink = false;
        $default_permalink_base = 'bsk-pdf-manager';
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
			
            if( isset($plugin_settings['enable_permalink']) ){
				$default_enable_permalink = $plugin_settings['enable_permalink'];
			}
            
		}
        
        $file_url = '';
        if( $default_enable_permalink ){
            $file_url = site_url().'/'.$default_permalink_base.'/'.$pdf_obj->slug.'/';
        }else{
            if( file_exists(BSKPDFManager::$_upload_root_path.$pdf_obj->file_name) ){
                $file_url = site_url().'/'.$pdf_obj->file_name;
            }
            if( $file_url == "" ){
                wp_die( 'Cannot get valid URL for the document, document ID: '.$pdf_id );
            }
        }
        
        wp_redirect( $file_url );
        exit;
    }
    
    public static function get_document_slug( $doc_title, $doc_id ){
        $sanitized = sanitize_title( $doc_title );
        $slug = str_replace( '_', '-', $sanitized );
        
        //unique
        global $wpdb;
        
        $pdfs_tbl_name = $wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name;
        $check_sql = 'SELECT `slug` FROM `'.$pdfs_tbl_name.'` WHERE `slug` = %s AND `id` != %d LIMIT 1';
		$doc_slug_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $slug, $doc_id ) );
        
        if ( $doc_slug_check ) {
			$suffix = 2;
			do {
				$alt_post_name   = _truncate_post_slug( $slug, 256 - ( strlen( $suffix ) + 1 ) ) . "-$suffix";
				$doc_slug_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $alt_post_name, $doc_id ) );
				$suffix++;
			} while ( $doc_slug_check );
			
            $slug = $alt_post_name;
		}
        
        return $slug;
    }
    
    public function bsk_pdfm_permalink_query_vars( $vars ){
        
		$default_permalink_base = 'bsk-pdf-manager';
        $vars[] = $default_permalink_base;
        
        return $vars;
    }
    
    public function bsk_pdfm_permalink_sniff_requests( $wp_query ) {

        global $wp;
        
        $default_permalink_base = 'bsk-pdf-manager';
        $permalink_redirect_to = 'NO';

        //read global embeded viewer settings
        $embedded_viewer_settings = BSKPDFM_Common_Display::get_embedded_viewer_settings();

        if ( isset( $wp->query_vars[$default_permalink_base] ) && $wp->query_vars[$default_permalink_base] ) {
            $pdf_slug = $wp->query_vars[$default_permalink_base];
            $pdf_obj = BSKPDFM_Common_Data_Source::get_document_obj_by_slug( $pdf_slug );
            if( $pdf_obj == false ){
                global $wp_query;
                
                $wp_query->set_404();
                status_header( 404 );
                get_template_part( 404 ); 
                
                exit();
            }
            
            $file_path = '';
            $file_ext = '';
            $file_url = '';
            if( $pdf_obj->file_name ){
                $file_path = BSKPDFManager::$_upload_root_path.$pdf_obj->file_name;
                $file_ext = pathinfo( $pdf_obj->file_name, PATHINFO_EXTENSION );
                $file_url = site_url().'/'.$pdf_obj->file_name;
			}
            $file_ext = strtolower( $file_ext );

            if( !file_exists( $file_path ) ){
                global $wp_query;
                
                $wp_query->set_404();
                status_header( 404 );
                get_template_part( 404 ); 
                
                exit();
            }
            
            if ( $permalink_redirect_to == 'YES' ||
                 ( $permalink_redirect_to == 'NO' && $pdf_obj->redirect_permalink ) ) {
                     
                if( $file_url == "" ){
                    wp_die( 'Cannot get valid URL for the document, document slug: '.$pdf_slug );
                }

                //if pdfjs enabled
                if ( $file_ext == 'pdf' ) {
                    
                    if ( $embedded_viewer_settings['enable'] ) {
                        $file_url = BSK_PDFM_PLUGIN_URL . 'pdfjs/web/viewer.html?file=' . $file_url . $embedded_viewer_settings['paras'];
                    }
                }

                wp_redirect( $file_url );
                exit;
            }

            //output docment content
            if ( is_array( $embedded_viewer_settings ) && isset( $embedded_viewer_settings ) && $embedded_viewer_settings['enable'] && $file_ext == 'pdf' ) {
                ob_start();
                ?>
                <!DOCTYPE html>
                <html>
                    <head>
                        <meta name="viewport" content="width=device-width, initial-scale=1">
                        <title><?php echo $pdf_obj->title; ?></title>
                        <style>
                        .container {
                            position: relative;
                            width: 100%;
                            overflow: hidden;
                            padding-top: 56.25%; /* 16:9 Aspect Ratio */
                        }

                        .responsive-iframe {
                            position: absolute;
                            top: 0;
                            left: 0;
                            bottom: 0;
                            right: 0;
                            width: 100%;
                            height: 100%;
                            border: none;
                        }
                        </style>
                    </head>
                    <body>
                        <div class="container"> 
                        <iframe class="responsive-iframe" src="<?php echo BSK_PDFM_PLUGIN_URL . 'pdfjs/web/viewer.html?file=' . $file_url . $embedded_viewer_settings['paras']; ?>"></iframe>
                        </div>
                    </body>
                </html>
                <?php
                $html_content = ob_get_contents();
                ob_end_clean();

                echo $html_content;
            } else {
                $wp_filetype = wp_check_filetype( $file_path, null );
                
                // Header content type
                header('Content-Type: '.$wp_filetype['type']);
                header("Content-Length: " . filesize( $file_path ) );
                header('Content-Disposition: inline; filename="' . basename( $file_path ) . '"');
                header('Content-Transfer-Encoding: binary');
                header('Accept-Ranges: bytes');

                // Read the file
                @readfile( $file_path );
            }
            exit();
        }
    }
    
    public function bsk_pdfm_permalink_add_rewrite_rule(){
        
        //bskpdf/article-slug/
        //add_rewrite_rule( "^([^/]+)/bskpdf/?$", 'index.php?' . static::ENDPOINT_QUERY_PARAM . '=1&name=$matches[1]', 'top' );
        add_rewrite_rule( 'bsk-pdf-manager/([a-z0-9-]+)[/]?$', 'index.php?bsk-pdf-manager=$matches[1]', 'top' );
        //////////////////////////////////
        //flush_rewrite_rules( true );  //// <---------- REMOVE THIS WHEN DONE TESTING
        //////////////////////////////////
    }
    
}
