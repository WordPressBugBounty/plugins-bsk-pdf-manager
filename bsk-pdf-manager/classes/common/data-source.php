<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class BSKPDFM_Common_Data_Source {
    
    public static function bsk_pdfm_get_counts(){
        global $wpdb;
        
        $all_sql = 'SELECT COUNT(*) FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name).'` WHERE 1 '.
                   'AND (`trash` = 0)';
        $all_count = $wpdb->get_var( $all_sql );
        
        $published_sql = 'SELECT COUNT(*) FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name).'` WHERE 1 '.
                         'AND (`publish_date` IS NULL OR `publish_date` <= "'.wp_date( 'Y-m-d H:i:s' ).'") '.
                         'AND (`expiry_date` IS NULL OR `expiry_date` > "'.wp_date( 'Y-m-d H:i:s' ).'") '.
                         'AND ( LENGTH(`file_name`) > 0 || `by_media_uploader` > 0 ) '.
                         'AND (`draft` = 0 ) ' .
                         'AND (`trash` = 0)';
        $published_count = $wpdb->get_var( $published_sql );
        
        $draft_sql = 'SELECT COUNT(*) FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name).'` WHERE 1 '.
                     'AND ( ( LENGTH(`file_name`) < 1 AND `by_media_uploader` < 1 ) OR `draft` = 1 ) '.
                     'AND (`trash` = 0)';
        $draft_count = $wpdb->get_var( $draft_sql );
        
        $scheduled_sql = 'SELECT COUNT(*) FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name).'` WHERE 1 '.
                         'AND (`publish_date` IS NOT NULL AND `publish_date` > "'.wp_date( 'Y-m-d H:i:s' ).'") '.
                         'AND (`expiry_date` IS NULL OR `expiry_date` > "'.wp_date( 'Y-m-d H:i:s' ).'") '.
                         'AND ( LENGTH(`file_name`) > 0 || `by_media_uploader` > 0 ) '.
                         'AND (`draft` = 0 ) ' .
                         'AND (`trash` = 0)';
        $scheduled_count = $wpdb->get_var( $scheduled_sql );
        
        $expired_sql =   'SELECT COUNT(*) FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name).'` WHERE 1 '.
                         'AND (`expiry_date` IS NOT NULL AND `expiry_date` <= "'.wp_date( 'Y-m-d H:i:s' ).'") '.
                         'AND ( LENGTH(`file_name`) > 0 || `by_media_uploader` > 0 ) '.
                         'AND (`draft` = 0 ) ' .
                         'AND (`trash` = 0)';
        $expired_count = $wpdb->get_var( $expired_sql );
        
        $trash_sql =     'SELECT COUNT(*) FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name).'` WHERE 1 '.
                         'AND (`trash` = 1)';
        $trash_count = $wpdb->get_var( $trash_sql );
        
        return array( 
                        'all' => $all_count, 
                        'published' => $published_count, 
                        'draft' => $draft_count,
                        'scheduled' => $scheduled_count, 
                        'expired' => $expired_count, 
                        'trash' => $trash_count 
                    );
    }
    
    public static function bsk_pdfm_get_pdfs( $args ){
        global $wpdb;
        
        $where_case_array = array();
        $prepare_values_array = array();
        
        //process id
        if( $args['show_all_pdfs'] ) {
            $where_case_array[] = 'WHERE 1 ';
        }else{
            $ids_array = $args['ids_array'];
            $where_case_array[] = 'WHERE `id` IN('.implode(',', esc_sql($ids_array)).')';
        }
        
        //exclude draft
        $where_case_array[] = 'AND ( LENGTH(`file_name`) > 0 || `by_media_uploader` > 0 ) ';
            
        //exclude trash
        $where_case_array[] = 'AND ( `trash` = 0 ) ';

        //exclude draft
        $where_case_array[] = 'AND ( `draft` = 0 ) ';
        
        //publish & exired
        if( !isset($args['skip_status']) || $args['skip_status'] != true ){
            $where_case_array[] = 'AND (`publish_date` IS NULL OR `publish_date` <= "'.wp_date( 'Y-m-d H:i:s' ).'") ';
            $where_case_array[] = 'AND (`expiry_date` IS NULL OR `expiry_date` > "'.wp_date( 'Y-m-d H:i:s' ).'") ';
        }
        
        if( isset($args['extension']) && trim($args['extension']) ){
            $where_case_array[] = 'AND ( `file_name` LIKE %s )';
            $prepare_values_array[] = '%.'.strtolower(esc_sql($args['extension']));
        }
        
        if( isset($args['tags']) ){
            //query out all PDFs id with given tag
            $tags_rel_tble = esc_sql($wpdb->prefix.BSKPDFManager::$_rels_tbl_name);
            $tags_associated_pdfs_sql = 'SELECT `pdf_id` FROM `'.$tags_rel_tble.'` WHERE `cat_id` = %d AND `type` LIKE "TAG" ';
            $tags_associated_pdfs_sql = $wpdb->prepare( $tags_associated_pdfs_sql, esc_sql($args['tags']) );
            $tags_associated_pdfs_results = $wpdb->get_results( $tags_associated_pdfs_sql );
            $tags_associated_pdfs_id_a = array();
            if( $tags_associated_pdfs_results && is_array( $tags_associated_pdfs_results ) && count( $tags_associated_pdfs_results ) ){
                foreach( $tags_associated_pdfs_results as $tags_associated_obj ){
                    $tags_associated_pdfs_id_a[] = $tags_associated_obj->pdf_id;
                }
            }else{
                $tags_associated_pdfs_id_a[] = -1;
            }
            
            $where_case_array[] = 'AND ( `id` IN( '.implode( ',', esc_sql($tags_associated_pdfs_id_a) ).' ) )';
        }
        
        $most_top = isset( $args['most_top'] ) ? absint($args['most_top']) : false;
        $limit_case = '';
        $total_results_count = 0;
        $total_pages = 0;
        if( $most_top > 0 ){
            $limit_case = ' LIMIT 0, '.$most_top;
        }
        
        //process order by case
		$order_by_str = ' ORDER BY `title`'; //default set to title
		$order_str = ' ASC';
		if( $args['order_by'] == 'title' ){
			//default
		}else if( $args['order_by'] == 'date' ){
			$order_by_str = ' ORDER BY `last_date`';
		}else if( $args['order_by'] == 'id' ){
			$order_by_str = ' ORDER BY `id`';
		}
        
		if( strtoupper(trim($args['order'])) == 'DESC' ){
			$order_str = ' DESC';
		}
        $order_case = $order_by_str.$order_str;
        
        if( $args['order_by'] == "" && $args['ids_array'] && is_array( $args['ids_array'] ) && count( $args['ids_array'] ) > 0 ){
            $order_case = ' ORDER BY FIELD(`id`, '.implode(',', esc_sql($args['ids_array'])).')';
        }
        
        
        $sql = 'SELECT * FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name).'` '.
                 implode( ' ', $where_case_array ).
                 esc_sql($order_case).
                 $limit_case;
        if( count($prepare_values_array) > 0 ){
            $sql = $wpdb->prepare( $sql, $prepare_values_array );
        }
        $results = $wpdb->get_results( $sql );
        if( !$results || !is_array( $results ) || count( $results ) < 1 ){
            return false;
        }

        $pdf_id_as_key_array = array();
        $total_results_count = 0;
        foreach( $results as $obj ){
            $pdf_id_as_key_array[$obj->id] = $obj;
        }
        
        //sort pdfs by id sequence order
        if( $args['ids_array'] && is_array( $args['ids_array'] ) && $args['order_by'] == "" ){
            $pdfs_results_array = array();
            foreach( $args['ids_array'] as $pdf_id ){
                if( !isset($pdf_id_as_key_array[$pdf_id]) ){
                    continue;
                }
                $pdfs_results_array[$pdf_id] = $pdf_id_as_key_array[$pdf_id];
            }
            return array( 'pdfs' => $pdfs_results_array, 'total' => count( $pdfs_results_array ) );
        }
            
        $total_results_count = count( $pdf_id_as_key_array );
        
        return array( 'pdfs' => $pdf_id_as_key_array, 'total' => $total_results_count );
    }
    
    public static function bsk_pdfm_get_pdfs_by_cat( $args ){
        global $wpdb;
        
        $pdfs_tbl = esc_sql($wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name);
        $cats_tbl = esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name);
        $rels_tbl = esc_sql($wpdb->prefix.BSKPDFManager::$_rels_tbl_name);
        
        $where_case_array = array();
        $prepare_values_array = array();
        
        //process category id
        $ids_array = $args['ids_array'];
        if( !$ids_array || !is_array( $ids_array ) || count( $ids_array ) < 1 ){
            return false;
        }
        
        $where_case_array[] = 'WHERE C.`id` IN('.trim(str_repeat( '%d,', count( $ids_array ) ), ',').')';
        foreach( $ids_array as $cat_id_to_query ){
            $prepare_values_array[] = $cat_id_to_query;
        } 
        
        //exclude draft
        $where_case_array[] = 'AND ( LENGTH(`file_name`) > 0 || `by_media_uploader` > 0 ) ';
            
        //exclude trash
        $where_case_array[] = 'AND ( P.`trash` = 0 ) ';

        //exclude draft
        $where_case_array[] = 'AND ( `draft` = 0 ) ';
        
        //publish & exired
        $where_case_array[] = 'AND (P.`publish_date` IS NULL OR P.`publish_date` <= "'.wp_date( 'Y-m-d H:i:s' ).'") ';
        $where_case_array[] = 'AND (P.`expiry_date` IS NULL OR P.`expiry_date` > "'.wp_date( 'Y-m-d H:i:s' ).'") ';

        if( isset($args['extension']) && trim($args['extension']) ){
            $where_case_array[] = 'AND ( P.`file_name` LIKE %s )';
            $prepare_values_array[] = '%.'.strtolower(esc_sql($args['extension']));
        }
        
        if( isset($args['tags']) ){
            //query out all PDFs id with given tag
            $tags_rel_tble = esc_sql($wpdb->prefix.BSKPDFManager::$_rels_tbl_name);
            $tags_associated_pdfs_sql = 'SELECT `pdf_id` FROM `'.$tags_rel_tble.'` WHERE `cat_id` = %d AND `type` LIKE "TAG" ';
            $tags_associated_pdfs_sql = $wpdb->prepare( $tags_associated_pdfs_sql, esc_sql($args['tags']) );
            $tags_associated_pdfs_results = $wpdb->get_results( $tags_associated_pdfs_sql );
            $tags_associated_pdfs_id_a = array();
            if( $tags_associated_pdfs_results && is_array( $tags_associated_pdfs_results ) && count( $tags_associated_pdfs_results ) ){
                foreach( $tags_associated_pdfs_results as $tags_associated_obj ){
                    $tags_associated_pdfs_id_a[] = $tags_associated_obj->pdf_id;
                }
            }else{
                $tags_associated_pdfs_id_a[] = -1;
            }
            
            $where_case_array[] = 'AND ( P.`id` IN( '.implode( ',', $tags_associated_pdfs_id_a ).' ) )';
        }
        
        $most_top = isset( $args['most_top'] ) ? intval($args['most_top']) : false;
        $limit_case = '';
        $total_results_count = 0;
        $total_pages = 1;
        if( $most_top > 0 ){
            $limit_case = ' LIMIT 0, '.$most_top;
        }
        
        //process order by case
        $cat_order_by_str = ' FIELD( C.`id`, '.implode(',', esc_sql($ids_array)).' )';
		$pdf_order_by_str = ' P.`title`'; //default set to title
        $pdf_order_str = ' ASC';
        if( $args['order_by'] == 'last_date' || $args['order_by'] == 'date' ){
			$pdf_order_by_str = ' P.`last_date`';
		}else if( $args['order_by'] == 'custom' ){
			$pdf_order_by_str = ' P.`order_num`';
		}else if( $args['order_by'] == 'id' ){
			$pdf_order_by_str = ' P.`id`';
		}
		if( strtoupper(trim($args['order'])) == 'DESC' ){
			$pdf_order_str = ' DESC';
		}
        $order_case = ' ORDER BY '.$cat_order_by_str.','.$pdf_order_by_str.$pdf_order_str;
        
        $sql = 'SELECT P.`id`, P.`title`, P.`slug`, P.`file_name`, P.`description`, P.`last_date`, P.`thumbnail_id`, P.`by_media_uploader`,  P.`download_count`, '.
                 'C.`id` AS `cat_id`, C.`title` AS `cat_title`, C.`parent` AS `cat_parent`, '.
                 'C.`description` AS `cat_desc`, C.`empty_message` as `cat_empty_msg` FROM `'.
                 $rels_tbl.'` AS R LEFT JOIN `'.$pdfs_tbl.'` AS P ON R.`pdf_id` = P.`id` LEFT JOIN `'.$cats_tbl.'` AS C ON R.`cat_id` = C.`id`'.
                 implode( ' ', $where_case_array ).
                 esc_sql($order_case).
                 $limit_case;
        if( count( $prepare_values_array ) > 0 ){
            $sql = $wpdb->prepare( $sql, $prepare_values_array );
        }
        
        //update_option( '11111111111', $sql );
        
        $results = $wpdb->get_results( $sql );
        if( !$results || !is_array( $results ) || count( $results ) < 1 ){
            return array( 
                        'pdfs' => false, 
                        'pages' =>0, 
                        'total' => 0, 
                        'categories_for_pdfs' => false 
                     );
        }
        if( $total_results_count < count( $results ) ){
            $total_results_count = count( $results );
        }
        
        $pdf_by_category_array = array();
        $categories_for_pdfs = array();
        foreach( $results as $obj ){
            if( !isset( $pdf_by_category_array[$obj->cat_id] ) ){
                $pdf_by_category_array[$obj->cat_id] = array();
            }
            $pdf_by_category_array[$obj->cat_id][$obj->id] = $obj;
            $categories_for_pdfs[$obj->cat_id] = $obj->cat_id;
         }

        return array( 
                        'pdfs' => $pdf_by_category_array, 
                        'pages' => $total_pages, 
                        'total' => $total_results_count, 
                        'categories_for_pdfs' => $categories_for_pdfs 
                     );
    }
    
    
    public static function bsk_pdfm_get_cat_obj( $cat_id ){
        global $wpdb;
        
        $cats_tbl = esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name);
        $sql = 'SELECT * FROM `'.$cats_tbl.'` AS C '.
               'WHERE C.`id` = %d ';
        $sql = $wpdb->prepare( $sql, $cat_id );
        $results = $wpdb->get_results( $sql );
        if( !$results || !is_array( $results ) || count( $results ) < 1 ){
            return false;
        }
        
        return $results[0];
    }
    
    public static function bsk_pdfm_organise_categories_id( $shortcode_attrs ){
        global $wpdb;
        
        $id_string = $shortcode_attrs['cat_id'];
        if( trim($id_string) == "" ){
            return false;
        }
        
        //process order by case
        $cat_order_by_str = ' C.`title`';
		$cat_order_str = ' ASC';
		if( trim($shortcode_attrs['cat_order_by']) == 'date' || 
            trim($shortcode_attrs['cat_order_by']) == 'C.`last_date`' ){
			$cat_order_by_str = ' C.`last_date`';
		}
		if( strtoupper(trim($shortcode_attrs['cat_order'])) == 'DESC' ){
			$cat_order_str = ' DESC';
        }

        $ids_array = array();
        $categories_loop_array = array();
        
        $temp_valid_array = array();
        $sql_base = 'SELECT * FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name).'` AS C '.
                    'WHERE 1 AND C.`type` LIKE "CAT" ';
        $sql = '';
		if( strtoupper($id_string) == 'ALL' ){
            $sql = $sql_base.
                   ' ORDER BY '.esc_sql($cat_order_by_str.$cat_order_str);
		}else{
			$temp_array = explode(',', $id_string);
            $temp_valid_array = array();
            foreach($temp_array as $key => $cat_id){
                $cat_id = absint(trim($cat_id));
                $temp_valid_array[] = $cat_id;
            }
            
            if( !is_array($temp_valid_array) || count($temp_valid_array) < 1 ){
				return false;
			}
			$sql = $sql_base.
                   ' AND C.`id` IN('.implode(',', $temp_valid_array).') '.
                   ' ORDER BY '.esc_sql($cat_order_by_str.$cat_order_str);
		}

        //query
        $categories_results = $wpdb->get_results( $sql );
        if( !$categories_results || !is_array( $categories_results) || count( $categories_results ) < 1 ){
            return false;
        }
        
        foreach( $categories_results as $cat_obj ){
            $ids_array[] = $cat_obj->id;
            $categories_loop_array[$cat_obj->id] = $cat_obj;
        }
        return array( 'ids_array' => $ids_array, 'categories_loop' => $categories_loop_array );
    }
    
    public static function get_document_obj_by_slug( $slug ){
        
        global $wpdb;
        
        $pdfs_tbl_name = $wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name;
        $sql = 'SELECT * FROM `'.$pdfs_tbl_name.'` WHERE `slug` = %s ';
        $sql .= 'AND (`publish_date` IS NULL OR `publish_date` <= "'.wp_date( 'Y-m-d H:i:s' ).'") ';
        $sql .= 'AND (`expiry_date` IS NULL OR `expiry_date` > "'.wp_date( 'Y-m-d H:i:s' ).'") ';
        $sql .= 'AND `draft` = 0 ';
        $sql .= 'AND `trash` = 0 ';
        $sql = $wpdb->prepare( $sql, $slug );
		$results = $wpdb->get_results( $sql );
        if( !$results || !is_array( $results ) || count( $results ) < 1 ){
            return false;
        }
        
        return $results[0];
    }

    public static function get_document_obj_by_id( $id ){
        
        $id = intval( $id );
        if ( $id < 1 ) {
            return false;
        }

        global $wpdb;
        
        $pdfs_tbl_name = $wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name;
        $sql = 'SELECT `id`, `title`, `by_media_uploader`, `media_ext`, `file_name`, `redirect_permalink` FROM `'.$pdfs_tbl_name.'` WHERE `id` = %d ';
        $sql .= 'AND (`publish_date` IS NULL OR `publish_date` <= "'.wp_date( 'Y-m-d H:i:s' ).'") ';
        $sql .= 'AND (`expiry_date` IS NULL OR `expiry_date` > "'.wp_date( 'Y-m-d H:i:s' ).'") ';
        $sql .= 'AND `trash` = 0 ';
        $sql .= 'AND `draft` = 0 ';
        $sql = $wpdb->prepare( $sql, $id );
		$results = $wpdb->get_results( $sql );
        if ( ! $results || ! is_array( $results ) || count( $results ) < 1 ) {
            return false;
        }
        
        return $results[0];
    }

}//end of class
