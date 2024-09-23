<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class BSKPDFM_Common_Backend {
    
    //for get parent category structure
    private static $current_parent_category_depth_to_get_options = 0;
    
    public static  function get_image_sizes() {
        global $_wp_additional_image_sizes;

        $sizes = array();

        foreach ( get_intermediate_image_sizes() as $_size ) {
            if ( $_size == 'bsk-pdf-dashboard-list-thumbnail' ) {
                continue;
            }
            if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
                $sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
                $sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
                $sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
            } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
                $sizes[ $_size ] = array(
                    'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
                    'height' => $_wp_additional_image_sizes[ $_size ]['height'],
                    'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
                );
            }
        }

        return $sizes;
    } //end of function
    
    public static  function get_image_size_dimission( $size_name ) {
        $sizes = self::get_image_sizes();
        if ( isset( $sizes[ $size_name ] ) ) {
            return $sizes[ $size_name ];
        }
        return false;
    } //end of function
    
    public static function bsk_pdf_manager_pdf_convert_hr_to_bytes( $size ) {
		$size  = strtolower( $size );
		$bytes = (int) $size;
		if ( strpos( $size, 'k' ) !== false )
			$bytes = intval( $size ) * 1024;
		elseif ( strpos( $size, 'm' ) !== false )
			$bytes = intval($size) * 1024 * 1024;
		elseif ( strpos( $size, 'g' ) !== false )
			$bytes = intval( $size ) * 1024 * 1024 * 1024;
		return $bytes;
	}
    
    public static  function get_parent_category_dropdown( $parent_max_depth, $dropdown_name, $dropdown_id, $select_text,
                                                                                   $chosen_parent_id, $current_edit_cat_id ) {
        global $wpdb;
        
        if( $parent_max_depth < 1 ){
            $selectr_str = '<select name="'.esc_attr($dropdown_name).'" id="'.esc_attr($dropdown_id).'">';
            $options_str = '<option value="0">'.esc_attr($select_text).'</option>';
            $selectr_str .= $options_str;
            $selectr_str .= '</select>';
            
            return $selectr_str;
        }
        $sql = 'SELECT * FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name).'` '.
               'WHERE `parent` = 0 AND `id` != %d '.
               'ORDER BY `title` ASC';
        $sql = $wpdb->prepare( $sql, $current_edit_cat_id );
        $results = $wpdb->get_results( $sql );
        
        $options_str = '<option value="0">'.$select_text.'</option>';
        foreach( $results as $cat_obj ){
            $selected_str = $chosen_parent_id == $cat_obj->id ? ' selected="selected"' : '';
            $options_str .= '<option value="'.esc_attr($cat_obj->id).'"'.$selected_str.'>'.esc_attr($cat_obj->title).'</option>';
            
            $current_category_depth = 1;
            $child_category_depth = $current_category_depth + 1;
            if( $parent_max_depth == 2 ){
                $options_str .= self::get_parent_category_dropdown_options( 
                                                                                                   $cat_obj->id, 
                                                                                                   $chosen_parent_id, 
                                                                                                   $current_edit_cat_id, 
                                                                                                   $child_category_depth );
            }
        }
        
        $selectr_str = '<select name="'.esc_attr($dropdown_name).'" id="'.esc_attr($dropdown_id).'" disabled>';
        $selectr_str .= $options_str;
        $selectr_str .= '</select>';
        
        return $selectr_str;
    }
    
    public static  function get_parent_category_dropdown_options( $parent_cat_id, 
                                                                                               $chosen_parent_id, 
                                                                                               $current_edit_cat_id, 
                                                                                               $current_depth ) {
        global $wpdb;
        
        $sql = 'SELECT * FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name).'` '.
               'WHERE `parent` = %d AND `id` != %d '.
               'ORDER BY `title` ASC';
        $sql = $wpdb->prepare($sql, $parent_cat_id, $current_edit_cat_id);
        $results = $wpdb->get_results( $sql );
        if( !$results || !is_array($results) || count($results) < 1 ){
            return '';
        }
        
        $prefix = '';
        for( $i = 1; $i < $current_depth; $i++ ){
            $prefix .= '&#8212;&nbsp;';
        }
        
        $options_str = '';
        foreach( $results as $cat_obj ){
            $selected_str = $chosen_parent_id == $cat_obj->id ? ' selected="selected"' : '';
            $options_str .= '<option value="'.esc_attr($cat_obj->id).'"'.$selected_str.'>'.esc_attr($prefix.$cat_obj->title).'</option>';
            /*
              * no need to get grand category anymore
              
              
            $options_str .= self::get_parent_category_dropdown_options( $max_depth, $cat_obj->id, $chosen_parent_id, $current_edit_cat_id );
            */
        }
        
        return $options_str;
    }
    
    public static  function get_category_dropdown( $dropdown_name, $dropdown_id, $select_text, $no_select, $current_cat_ids_array, $type = 'CAT' ) {
                                                                        
        global $wpdb;
        
        $sql = 'SELECT * FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name).'` '.
               'WHERE `parent` = 0 AND `type` LIKE %s'.
               'ORDER BY `title` ASC';
        $sql = $wpdb->prepare( $sql, $type );
        $results = $wpdb->get_results( $sql );
        
        $options_str = '<option value="0">'.esc_attr($select_text).'</option>';
        if( $no_select ){
            $selected_str = in_array( -1, $current_cat_ids_array ) ? ' selected="selected"' : '';
            $options_str .= '<option value="-1"'.$selected_str.'>'.esc_attr($no_select).'</option>';
        }
        foreach( $results as $cat_obj ){
            $selected_str = in_array( $cat_obj->id, $current_cat_ids_array ) ? ' selected="selected"' : '';
            $options_str .= '<option value="'.esc_attr($cat_obj->id).'"'.$selected_str.'>'.esc_html($cat_obj->title).'</option>';
        }
        
        $selectr_str = '<select name="'.esc_attr($dropdown_name).'" id="'.esc_attr($dropdown_id).'">';
        $selectr_str .= $options_str;
        $selectr_str .= '</select>';
        
        return $selectr_str;
    }
    
    public static  function get_category_dropdown_options( $parent_cat_id, $current_cat_ids_array, $current_depth ) {
        global $wpdb;
        
        $sql = 'SELECT * FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name).'` '.
                 'WHERE `parent` = %d '.
                 'ORDER BY `title` ASC';
        $sql = $wpdb->prepare($sql, $parent_cat_id);
        $results = $wpdb->get_results( $sql );
        if( !$results || !is_array($results) || count($results) < 1 ){
            return '';
        }
        
        $prefix = '';
        for( $i = 1; $i < $current_depth; $i++ ){
            $prefix .= '&#8212;&nbsp;';
        }
        
        $options_str = '';
        foreach( $results as $cat_obj ){
            $selected_str = in_array( $cat_obj->id, $current_cat_ids_array ) ? ' selected="selected"' : '';
            $options_str .= '<option value="'.esc_attr($cat_obj->id).'"'.$selected_str.'>'.esc_attr($prefix.$cat_obj->title).'</option>';
            
            $grand_category_depth = $current_depth + 1;
            $options_str .= self::get_category_dropdown_options( $cat_obj->id, $current_cat_ids_array, $grand_category_depth );
        }
        
        return $options_str;
    }
    
    public static  function get_category_children_depth( $cat_id ) {
        global $wpdb;
        
        $depth = 0;
        
        $sql = 'SELECT `id` FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name).'` '.
                 'WHERE `parent` = %d ';
        $sql = $wpdb->prepare($sql, $cat_id);
        $children_results = $wpdb->get_results( $sql );
        while( $children_results && is_array($children_results) && count($children_results) > 0 ){
            $depth++;
            $children_ids = array();
            foreach( $children_results as $cat_obj ){
                $children_ids[] = $cat_obj->id;
            }
            $children_ids_str = implode(',', esc_sql($children_ids));
            $sql = 'SELECT `id` FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name).'` '.
                     'WHERE `parent` IN( '.$children_ids_str.')';
            $children_results = $wpdb->get_results( $sql );
        }
        
        return $depth;
    }
    
    public static function get_category_parent_ids( $cat_id ) {
        global $wpdb;
        
        $parents_id = array();
        
        $sql = 'SELECT `parent` as parent_id FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name).'` '.
                 'WHERE `id` = %d ';
        $sql = $wpdb->prepare($sql, $cat_id);
        $parent_id = $wpdb->get_var( $sql );
        while( $parent_id ){
            $parents_id[] = $parent_id;
            $sql = 'SELECT `parent` FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name).'` '.
                     'WHERE `id` = %d ';
            $sql = $wpdb->prepare($sql, $parent_id);
            $parent_id = $wpdb->get_var( $sql );
        }
        
        return $parents_id;
    }
    
    public static function get_category_hierarchy_checkbox( $checkbox_name, $checkbox_class, $checked_ids_array, $type = 'CAT', $with_desc = true ) {
                                                                        
        global $wpdb;
        
        $sql = 'SELECT * FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name).'` '.
               'WHERE `parent` = 0 AND `type` LIKE %s '.
               'ORDER BY `title` ASC';
        $sql = $wpdb->prepare( $sql, $type );
        $results = $wpdb->get_results( $sql );
        if( !$results || !is_array($results) || count($results) < 1 ){
            return '';
        }
        
        $out_str  = '<div class="bsk-pdfm-category-hierarchy-checkbox-container">';
        foreach( $results as $category ){
            $label = $category->title;
            $checkbox_val = $category->id;
            $checked_str = in_array( $category->id, $checked_ids_array ) ? 'checked ' : '';
            $out_str .= '<ul>';
            $out_str .= '<li>
                                <label>
                                    <input type="checkbox" name="'.esc_attr($checkbox_name).'" class="'.esc_attr($checkbox_class).'" value="'.esc_attr($checkbox_val).'" '.$checked_str.'/>'.$label.'
                                </label>';
            if( $with_desc && $category->description ){
                $out_str .= '<p>'.$category->description.'</p>';
            }
            $out_str .= '</li>';
            $out_str .= '</ul>';
        }
        $out_str .= '</div>';
        
        return $out_str;
    }
    
    public static function get_available_extension_category(){
        $all_available_extension_by_category = array();
        
        $all_available_extension_by_category['images'] = array( 
                                                                  'label' => 'Image',
                                                                  'extensions' => array( 'png', 'jpg', 'jpeg', 'gif', 'tif', 'tiff', 'svg' )
                                                              );
        
        $all_available_extension_by_category['compressed'] = array( 
                                                                      'label' => 'Compressed file',
                                                                      'extensions' => array( 'zip', 'gz', 'rar', '7z' )
                                                                  );
        
        $all_available_extension_by_category['office_txt'] = array( 
                                                                      'label' => 'Microsoft Office and text',
                                                                      'extensions' => array( 'txt', 'rtf', 'doc', 'docx', 'xlsx', 'pptx', 'csv', 'crtfsv' )
                                                                  );
        
        $all_available_extension_by_category['iwork_office'] = array( 
                                                                        'label' => 'iWork productivity suite',
                                                                        'extensions' => array( 'pages', 'numbers', 'keynote' )
                                                                    );
        
        
        $all_available_extension_by_category['audio'] = array( 
                                                                'label' => 'Audio',
                                                                'extensions' => array( 'mid', 'midi', 'mp3', 'flac', 'mpa', 'ogg', 'wav', 'wma', 'wpl', 'oga', 'ogx', 'weba' )
                                                             );
        
        $all_available_extension_by_category['video'] = array( 
                                                                'label' => 'Video',
                                                                'extensions' => array( '3g2', '3gp', 'avi', 'flv', 'h264', 'm4v', 'mkv', 'mov', 'mp4', 'mpg', 'mpeg', 'rm ', 'swf', 'vob', 'wmv', 'webm', 'mts', )
                                                             );

        $all_available_extension_by_category['cad'] = array( 
                                                                'label' => 'CAD',
                                                                'extensions' => array( 'dwg', 'dxf', 'dgn', 'stl', 'rfa', 'rvt', 'step', 'stp' )
                                                          );
         
        $all_available_extension_by_category['others'] = array( 
                                                                    'label' => 'Others',
                                                                    'extensions' => array( 'ies' )
                                                              );
        
        return $all_available_extension_by_category;
    }
    
    public static function get_available_extension_with_mime_type(){
        $all_available_extension_with_mime_type = array();
        
        $all_available_extension_with_mime_type['pdf'] = array( 'application/pdf' );
        
        /* Compressed file extensions */
        $all_available_extension_with_mime_type['zip'] = array( 
                                                                     'application/x-compressed', 
                                                                     'application/x-zip-compressed', 
                                                                     'application/zip',
                                                                     'multipart/x-zip'
                                                                   );
		$all_available_extension_with_mime_type['gz'] = array(  
                                                                     'application/x-compressed', 
                                                                     'application/x-gzip'
                                                                  );
        $all_available_extension_with_mime_type['rar'] = array(  
                                                                      'application/x-rar-compressed', 
                                                                      'application/octet-stream'
                                                                  );
        $all_available_extension_with_mime_type['7z'] = array(  
                                                                      'application/x-7z-compressed'
                                                                  );
        /* Image file extensions */
        $all_available_extension_with_mime_type['png'] = array(  
                                                                      'image/png'
                                                                  );
        $all_available_extension_with_mime_type['jpg'] = array(  
                                                                      'image/pjpeg', 
                                                                      'image/jpeg'
                                                                  );
        $all_available_extension_with_mime_type['jpeg'] = array(  
                                                                      'image/pjpeg', 
                                                                      'image/jpeg'
                                                                  );
        $all_available_extension_with_mime_type['gif'] = array(  
                                                                      'image/gif'
                                                                  );
        $all_available_extension_with_mime_type['tif'] = array(  
                                                                      'image/tiff', 
                                                                      'image/x-tiff'
                                                                  );
        $all_available_extension_with_mime_type['tiff'] = array(  
                                                                      'image/tiff', 
                                                                      'image/x-tiff'
                                                                  );
        $all_available_extension_with_mime_type['svg'] = array(  
                                                                      'image/svg+xml'
                                                                  );
        
        /* Microsoft Office and text file */
        $all_available_extension_with_mime_type['txt'] = array(  
                                                                      'text/plain'
                                                              );
        $all_available_extension_with_mime_type['rtf'] = array(  
                                                                      'application/rtf'
                                                              );
        $all_available_extension_with_mime_type['doc'] = array(  
                                                                      'application/msword'
                                                              );
        $all_available_extension_with_mime_type['docx'] = array(  
                                                                      'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                                                                  );
        $all_available_extension_with_mime_type['xlsx'] = array(  
                                                                      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                                                                  );
        $all_available_extension_with_mime_type['pptx'] = array(  
                                                                      'application/vnd.openxmlformats-officedocument.presentationml.presentation'
                                                                  );
        $all_available_extension_with_mime_type['csv'] = array(  
                                                                      'text/csv'
                                                                  );
        $all_available_extension_with_mime_type['crtfsv'] = array(  
                                                                      'application/rtf',
                                                                      'application/x-rtf',
                                                                      'text/richtext',
                                                                  );
        
        /*  iWork productivity suite */
        $all_available_extension_with_mime_type['pages'] = array(  
                                                                'application/x-iwork-pages-sffpages',
                                                                'application/vnd.apple.pages'
                                                            );
        $all_available_extension_with_mime_type['numbers'] = array(  
                                                                'application/x-iwork-numbers-sffnumbers',
                                                                'application/vnd.apple.numbers',
                                                            );
        $all_available_extension_with_mime_type['keynote'] = array(  
                                                                'application/x-iwork-keynote-sffkey',
                                                                'application/vnd.apple.keynote'
                                                            );
        
        /* OpenOffice */
        $all_available_extension_with_mime_type['odt'] = array(  
                                                                      'application/vnd.oasis.opendocument.text'
                                                                  );
        $all_available_extension_with_mime_type['ods'] = array(  
                                                                      'application/vnd.oasis.opendocument.spreadsheet'
                                                              );
        $all_available_extension_with_mime_type['odp'] = array(  
                                                                      'application/vnd.oasis.opendocument.presentation'
                                                              );
        
        /* Audio */
        $all_available_extension_with_mime_type['mid'] = array(  
                                                                      'music/crescendo',
                                                                      'x-music/x-midi',
                                                                      'application/x-midi',
                                                                      'audio/x-mid',
                                                                      'audio/midi',
                                                                      'audio/x-midi'
                                                                  );
        $all_available_extension_with_mime_type['midi'] = array(  
                                                                      'music/crescendo',
                                                                      'x-music/x-midi',
                                                                      'application/x-midi',
                                                                      'audio/x-mid',
                                                                      'audio/midi',
                                                                      'audio/x-midi'
                                                              );
        $all_available_extension_with_mime_type['mp3'] = array(  
                                                                      'audio/mpeg3',
                                                                      'audio/x-mpeg-3',
                                                                      'audio/mpeg',
                                                                      'audio/x-mpeg',
                                                              );
        $all_available_extension_with_mime_type['flac'] = array(  
                                                                      'audio/x-flac',
                                                                      'audio/flac',
                                                               );
        
        $all_available_extension_with_mime_type['mpa'] = array(  
                                                                      'audio/mpeg',
                                                                      'video/mpeg'
                                                                  );
        $all_available_extension_with_mime_type['wav'] = array(  
                                                                      'audio/wav',
                                                                      'audio/x-wav',
                                                              );
        $all_available_extension_with_mime_type['wma'] = array(  
                                                                      'audio/x-ms-wma'
                                                                  );
        $all_available_extension_with_mime_type['wpl'] = array(  
                                                                      'application/vnd.ms-wpl'
                                                              );
        $all_available_extension_with_mime_type['oga'] = array(  
                                                                      'audio/ogg'
                                                              );
        $all_available_extension_with_mime_type['ogx'] = array(  
                                                                      'audio/ogg'
                                                              );
        $all_available_extension_with_mime_type['ogg'] = array(  
                                                                      'video/ogg'
                                                              );
        $all_available_extension_with_mime_type['weba'] = array(  
                                                                      'audio/webm'
                                                              );
        

        /* Video */        
        $all_available_extension_with_mime_type['3g2'] = array(  
                                                                      'video/3gpp2',
                                                                      'audio/3gpp2',
                                                              );
        $all_available_extension_with_mime_type['3gp'] = array(  
                                                                      'video/3gpp',
                                                                      'audio/3gpp',
                                                              );
        $all_available_extension_with_mime_type['avi'] = array(  
                                                                      'video/x-msvideo',
                                                                      'video/avi',
                                                              );
        $all_available_extension_with_mime_type['flv'] = array(  
                                                                      'video/x-flv'
                                                              );
        $all_available_extension_with_mime_type['h264'] = array(  
                                                                      'video/H264'
                                                              );
        $all_available_extension_with_mime_type['m4v'] = array(  
                                                                      'video/mp4'
                                                              );
        $all_available_extension_with_mime_type['mkv'] = array(  
                                                                      'video/x-matroska',
                                                                      'application/octet-stream',
                                                              );
        $all_available_extension_with_mime_type['mov'] = array(  
                                                                      'video/quicktime'
                                                              );
        $all_available_extension_with_mime_type['mp4'] = array(  
                                                                      'video/mp4'
                                                              );
        $all_available_extension_with_mime_type['mpg'] = array(  
                                                                      'video/mpeg',
                                                                      'application/octet-stream',
                                                              );
        $all_available_extension_with_mime_type['mpeg'] = array(  
                                                                      'application/octet-stream',
                                                                      'video/mpeg',
                                                              );
        
        $all_available_extension_with_mime_type['rm'] = array(  
                                                                      'application/vnd.rn-realmedia',
                                                                      'audio/x-pn-realaudio'
                                                              );
        $all_available_extension_with_mime_type['swf'] = array(  
                                                                      'application/x-shockwave-flash'
                                                              );
        $all_available_extension_with_mime_type['vob'] = array(  
                                                                      'video/dvd',
                                                                      'video/mpeg',
                                                                      'video/x-ms-vob',
                                                                      'application/octet-stream'
                                                              );
        $all_available_extension_with_mime_type['wmv'] = array(  
                                                                      'video/x-ms-wmv'
                                                              );
        $all_available_extension_with_mime_type['ogv'] = array(  
                                                                      'video/ogg'
                                                              );
        $all_available_extension_with_mime_type['webm'] = array(  
                                                                      'video/webm',
                                                                      'application/octet-stream'
                                                              );
        $all_available_extension_with_mime_type['mts'] = array(  
                                                                  'application/metastream', 
                                                                  'video/avchd-stream', 
                                                                  'video/mts', 
                                                                  'video/vnd.dlna.mpeg-tts',
                                                                  'application/octet-stream'
                                                              );

        /* CAD */
        $all_available_extension_with_mime_type['dwg'] = array(  
            'application/autocad_dwg',
            'application/dwg',
            'application/x-acad',
            'application/x-autocad',
            'application/x-dwg',
            'drawing/dwg',
            'image/vnd.dwg',
            'image/x-dwg',
            'application/octet-stream'
        );
        $all_available_extension_with_mime_type['dxf'] = array(  
            'application/x-autocad',
            'application/x-dxf',
            'drawing/x-dxf',
            'image/vnd.dxf',
            'image/x-autocad',
            'image/x-dxf',
            'zz-application/zz-winassoc-dxf',
            'application/octet-stream'
        );
        $all_available_extension_with_mime_type['dgn'] = array(  
            'image/vnd.dgn',
            'application/x-bentley-dgn',
            'application/dgn',
            'application/octet-stream'
        );
        $all_available_extension_with_mime_type['stl'] = array(  
            'application/sla',
            'application/vnd.ms-pki.stl',
            'application/vnd.ms-pkistl',
            'application/octet-stream'
        );
        $all_available_extension_with_mime_type['rfa'] = array(  
            'application/vnd.ms-pki.rfa',
            'application/vnd.ms-pkirfa',
            'application/octet-stream'
        );
        $all_available_extension_with_mime_type['rvt'] = array(  
            'application/vnd.ms-pki.rvt',
            'application/vnd.ms-pkirvt',
            'application/octet-stream'
        );
        $all_available_extension_with_mime_type['step'] = array(  
            'application/vnd.ms-pki.step',
            'application/vnd.ms-pkistep',
            'application/octet-stream'
        );
        $all_available_extension_with_mime_type['stp'] = array(  
            'application/vnd.ms-pki.stp',
            'application/vnd.ms-pkistp',
            'application/octet-stream'
        );

        /* Others */
        $all_available_extension_with_mime_type['ies'] = array(  
                                                                      'application/octet-stream'
                                                                  );
        return $all_available_extension_with_mime_type;
    }
    
    public static function get_supported_extension_with_mime_type(){
        $plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
        $supported_extension = array();
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
            if( isset($plugin_settings['supported_extension']) ){
                $supported_extension = $plugin_settings['supported_extension'];
			}
		}
        //pdf is mandatory supported
        if( !in_array( 'pdf', $supported_extension ) ){
            $supported_extension[] = 'pdf';
        }
        
        $all_available_extension_and_mime_type = self::get_available_extension_with_mime_type();
        foreach( $all_available_extension_and_mime_type as $key => $mime_type ){
            if( !in_array( $key, $supported_extension ) ){
                unset( $all_available_extension_and_mime_type[$key] );
                continue;
            }
        }
        
        return $all_available_extension_and_mime_type;
    }
    
    public static function get_cat_pdfs_count( $cat_id ){
        global $wpdb;
        
        $pdfs_tbl = esc_sql($wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name);
        $cats_tbl = esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name);
        $rels_tbl = esc_sql($wpdb->prefix.BSKPDFManager::$_rels_tbl_name);
        
        $sql = 'SELECT COUNT(P.`id`) FROM `'.$pdfs_tbl.'` AS P LEFT JOIN `'.$rels_tbl.'` AS R ON P.`id` = R.`pdf_id` '.
                 'WHERE R.`cat_id` = %d ';
        $sql = $wpdb->prepare($sql, $cat_id);
        
        return $wpdb->get_var( $sql );
    }
    
    public static function get_extension_dropdown( $dropdown_name, $dropdown_id, $select_text, $current_extension, $current_cat_id ) {
                                                                        
        global $wpdb;
        
        $pdfs_table = esc_sql($wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name);
        $res_table = esc_sql($wpdb->prefix.BSKPDFManager::$_rels_tbl_name);
        $sql = 'SELECT DISTINCT( SUBSTRING_INDEX(P.`file_name`, \'.\', -1 ) ) AS ext FROM `'.$pdfs_table.'` AS P '.
               'LEFT JOIN `'.$res_table.'` AS R ON P.`id` = R.`pdf_id` '.
               'WHERE 1 '.
               'ORDER BY ext ASC';
        if( $current_cat_id ){
            $sql = 'SELECT DISTINCT( SUBSTRING_INDEX(P.`file_name`, \'.\', -1 ) ) AS ext FROM `'.$pdfs_table.'` AS P '.
                   'LEFT JOIN `'.$res_table.'` AS R ON P.`id` = R.`pdf_id` '.
                   'WHERE 1 AND R.`cat_id` = %d '.
                   'ORDER BY ext ASC';
            $sql = $wpdb->prepare( $sql, $current_cat_id );
        }
        $results = $wpdb->get_results( $sql );
        
        if( !$results || !is_array( $results ) || count( $results ) < 2 ){
            return '';
        }
        
        $extensions_array = array();
        foreach( $results as $extension_name_record ){
            $extensions_array[] = $extension_name_record->ext;
        }
        
        $options_str = '<option value="">'.esc_attr($select_text).'</option>';
        if( in_array( 'pdf', $extensions_array ) ){
            $checked = '';
            if( 'pdf' == $current_extension ){
                $checked = ' selected';
            }
            $options_str .= '<option value="pdf"'.$checked.'>&nbsp;&nbsp;&nbsp;&nbsp;pdf</option>';
        }
        
        $all_extension_by_category = self::get_available_extension_category();
        foreach( $all_extension_by_category as $category_data ){
            $group_options = false;
            foreach( $category_data['extensions'] as $extension ){
                if( in_array( $extension, $extensions_array ) ){
                    $checked = '';
                    if( $extension == $current_extension ){
                        $checked = ' selected';
                    }
                    $group_options .= '<option value="'.esc_attr($extension).'"'.$checked.'>'.esc_attr($extension).'</option>';
                }
            }
            
            if( $group_options ){
                $options_str .= '<optgroup label="'.esc_attr($category_data['label']).'">';
                $options_str .= $group_options;
                $options_str .= '</optgroup>';
            }
        }
        
        $selectr_str = '<select name="'.esc_attr($dropdown_name).'" id="'.esc_attr($dropdown_id).'">';
        $selectr_str .= $options_str;
        $selectr_str .= '</select>';
        
        return $selectr_str;
    }
    
    public static function bsk_pdfm_current_user_can(){
        global $current_user;
        
        if (current_user_can('manage_options')) {
            return true;
        }
        
        return false;
    }
    
    public static function bsk_pdfm_get_all_upload_fodlers() {
        global $wpdb;

		$pdfs_table = esc_sql( $wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name );
		$default_upload_prefix = str_replace( BSKPDFManager::$_upload_root_path, '', BSKPDFManager::$_upload_path );
		$custom_upload_prefix = '';

		$sql = 'SELECT DISTINCT( SUBSTRING_INDEX( `file_name`, SUBSTRING_INDEX( `file_name`, "/", -1 ), 1 ) ) AS folder_name FROM `' . $pdfs_table . '` WHERE 1 '.
			   'AND ( `by_media_uploader` < 1 ) AND ( LENGTH( `file_name` ) > 1 ) '.
			   'AND ( `file_name` NOT LIKE %s ) ';
		$sql = $wpdb->prepare( $sql, $default_upload_prefix.'%' );
		if ( $custom_upload_prefix ) {
			$sql .= 'AND ( `file_name` NOT LIKE %s )';
			$sql = $wpdb->prepare( $sql, $custom_upload_prefix.'%' );
		}
		$results = $wpdb->get_results( $sql );

        $upload_folders = array();
		//add default
		$upload_folders[] = $default_upload_prefix;
        if ( $custom_upload_prefix ) {
		    $upload_folders[] = $custom_upload_prefix;
        }
		$month_name_array = array( '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12' );
		if ( $results && is_array( $results ) && count( $results ) > 0 ) {
			foreach ( $results as $upload_folder_obj ) {
				$upload_folder_name = trim( $upload_folder_obj->folder_name, '/' );
				$upload_folder_name_array = explode( '/', $upload_folder_name );
				if ( count( $upload_folder_name_array ) == 1 ) {
					$upload_folders[] = $upload_folder_name_array[0];
					continue;
				}

				//check month folder
				if ( in_array( $upload_folder_name_array[count( $upload_folder_name_array ) - 1], $month_name_array ) ) {
					unset( $upload_folder_name_array[count( $upload_folder_name_array ) - 1] );
                    if ( count( $upload_folder_name_array ) == 1 ) {
                        $upload_folders[] = $upload_folder_name_array[0];
                        continue;
                    }
				}

				//check year folder
				$year_folder = intval( $upload_folder_name_array[count( $upload_folder_name_array ) - 1] );
				if ( $year_folder >= 1970 & $year_folder <= intval( wp_date( 'Y' ) ) ) {
					unset( $upload_folder_name_array[count( $upload_folder_name_array ) - 1] );
                    if ( count( $upload_folder_name_array ) == 1 ) {
                        $upload_folders[] = $upload_folder_name_array[0];
                        continue;
                    }
				}

				$upload_folder_name = implode( '/', $upload_folder_name_array );
				$upload_folders[] = $upload_folder_name;
			}
		}
        
        foreach ( $upload_folders as $key => $val ) {
            $upload_folders[$key] = trim( $val, '/' );
        }
        $upload_folders = array_unique( $upload_folders );

        return $upload_folders;
    }

}//end of class
