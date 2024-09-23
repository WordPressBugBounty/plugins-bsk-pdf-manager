<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class BSKPDFM_Dashboard_Tags extends WP_List_Table {
   
    function __construct() {
        global $wpdb;
		
        //Set parent defaults
        parent::__construct( array( 
            'singular' => 'bsk-pdf-manager-tags',  //singular name of the listed records
            'plural'   => 'bsk-pdf-manager-tags', //plural name of the listed records
            'ajax'     => false                          //does this table support ajax?
        ) );
    }

    function column_default( $item, $column_name ) {
        switch( $column_name ) {
			case 'id':
				echo $item['id_link'];
				break;
			case 'title':
				echo $item['title'];
				break;
            case 'description':
				echo $item['description'];
				break;
            case 'last_date':
                echo $item['last_date'];
                break;
            case 'count':
                echo $item['count'];
                break;
        }
    }
   
    function column_cb( $item ) {
        return sprintf( 
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],
            $item['id']
        );
    }

    function get_columns() {
    
        $columns = array( 
			'cb'        		=> '<input type="checkbox"/>',
			'id'				=> __( 'ID', 'bskpdfmanager' ), 
            'title'     	=> __( 'Name', 'bskpdfmanager' ), 
            'description'     => __( 'Description', 'bskpdfmanager' ), 
            'last_date' 		=> __( 'Date&amp;Time', 'bskpdfmanager' ), 
            'count' 		=> __( 'Documents Count', 'bskpdfmanager' ), 
        );
        
        return $columns;
    }
   
	function get_sortable_columns() {
		$c = array(
                    'id' => 'id',
					'title' => 'title',
                    'last_date'    => 'last_date'
				  );
		
		return $c;
	}
	
    function get_views() {
		//$views = array('filter' => '<select name="a"><option value="1">1</option></select>');
		
        return array();
    }
   
    function get_bulk_actions() {
    
        $actions = array( 
            'delete'=> esc_html__( 'Delete', 'bskpdfmanager' ), 
        );
        
        return $actions;
    }

    function do_bulk_action() {
		global $wpdb;
		
		if (!isset($_POST['bsk-pdf-manager-tags']) || !is_array( $_POST['bsk-pdf-manager-tags'] ) || count( $_POST['bsk-pdf-manager-tags'] ) < 1 ) {
			return;
		}
        $tags_id = array();
        foreach( $_POST['bsk-pdf-manager-tags'] as $temp_tag_id ) {
            $tags_id[] = intval(sanitize_text_field($temp_tag_id));
        }
        
		$action = -1;
		if (isset($_POST['action'])) {
			$temp_action = sanitize_text_field($_POST['action']);
            if ($temp_action != -1) {
                $action = $temp_action;
            }
		}
        if (isset($_POST['action2'])) {
			$temp_action = sanitize_text_field($_POST['action2']);
            if ($temp_action != -1){
                $action = $temp_action;
            }
		}
        
		if ( $action == -1 ) {
			return;
		} else if ( $action == 'delete' ) {
			$ids = implode(',', esc_sql($tags_id));
			$ids = trim($ids);
            
            //delete all tags
			$sql = 'DELETE FROM `'.$wpdb->prefix.BSKPDFManager::$_cats_tbl_name.'` WHERE `id` IN('.$ids.') AND `type` LIKE "TAG"';
			$wpdb->query( $sql );
            
            //also delete relation ships
            $sql = 'DELETE FROM `'.$wpdb->prefix.BSKPDFManager::$_rels_tbl_name.'` WHERE `cat_id` IN('.$ids.') AND `type` LIKE "TAG"';
			$wpdb->query( $sql );
		}
    }
    
    function get_data() {
		global $wpdb;
		
        $search = '';
		$orderby = 'title';
		$order = 'ASC';
        // check to see if we are searching
        if (isset( $_POST['s'] )) {
            $search = trim(sanitize_text_field($_POST['s']));
        }
		if (isset($_REQUEST['orderby'])) {
			$orderby_by_user = sanitize_text_field($_REQUEST['orderby']);
            $sortable_headers = $this->get_sortable_columns();
            if ( in_array( $orderby_by_user, $sortable_headers ) ) {
                $orderby = $orderby_by_user;
            }
		}
		if (isset($_REQUEST['order'])) {
			$order_by_user = strtoupper( sanitize_text_field($_REQUEST['order']) );
            if ( $order_by_user == 'DESC' ) {
                $order = $order_by_user;
            }
		}
		
		$sql = 'SELECT * FROM '.
		       esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name).' AS c '.
               'WHERE 1 AND c.`parent` = 0 AND c.`type` LIKE "TAG" ';
        if ($search){
            $sql .= 'AND c.`title` LIKE %s ';
            $sql = $wpdb->prepare($sql, '%'.esc_sql($search).'%');
        }
		if ($orderby){
			$sql .= ' ORDER BY c.`'.esc_sql($orderby).'` '.esc_sql($order);
		}
		$tags = $wpdb->get_results($sql);
		if (!$tags || count($tags) < 1) {
			return NULL;
		}
		$tag_page_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['tag'] );
		
		$tags_data = array();
		foreach ( $tags as $tag ) {
            $current_tag_depth = 1;
			$tag_edit_page = add_query_arg( 
                                            array('view' => 'edit', 'tagid' => $tag->id ),
                                            $tag_page_url 
                                          );
            $date_time = $tag->last_date;
			$tags_data[] = array( 
			    'id' 				=> $tag->id,
				'id_link' 			=> '<a href="'.$tag_edit_page.'">'.$tag->id.'</a>',
				'title'     	=> '<strong><a class="row-title" href="'.esc_url($tag_edit_page).'">'.esc_html($tag->title).'</a></strong>',
                'description'    => $tag->description,
                'last_date'			=> esc_html($date_time),
                'count'             => esc_html(BSKPDFM_Common_Backend::get_cat_pdfs_count( $tag->id )),
			);
		}
		
		return $tags_data;
    }

    function prepare_items() {
       
        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 20;
        $data = array();
		
        add_thickbox();

		$this->do_bulk_action();
       
        $data = $this->get_data();
   
        $current_page = $this->get_pagenum();
        $total_items = 0;
        if( $data && is_array( $data ) ){
            $total_items = count( $data );
        }
	    if ($total_items > 0){
        	$data = array_slice( $data,( ( $current_page-1 )*$per_page ),$per_page );
		}
        $this->items = $data;

        $this->set_pagination_args( array( 
            'total_items' => $total_items,                  // We have to calculate the total number of items
            'per_page'    => $per_page,                     // We have to determine how many items to show on a page
            'total_pages' => ceil( $total_items/$per_page ) // We have to calculate the total number of pages
        ) );
    }
	

	
	function get_column_info() {
		
		$columns = array( 
							'cb'        		=> '<input type="checkbox"/>',
							'id'				=> __( 'ID', 'bskpdfmanager' ),
							'title'     	    => __( 'Title', 'bskpdfmanager' ),
                            'description'     	=> __( 'Description', 'bskpdfmanager' ),
                            'last_date'			=> __( 'Date&amp;Time', 'bskpdfmanager' ), 
                            'count'             => __( 'PDFs Count', 'bskpdfmanager' ), 
						);
		
		$hidden = array();

		$_sortable = apply_filters( "manage_{$this->screen->id}_sortable_columns", $this->get_sortable_columns() );

		$sortable = array();
		foreach ( $_sortable as $id => $data ) {
			if ( empty( $data ) )
				continue;

			$data = (array) $data;
			if ( !isset( $data[1] ) )
				$data[1] = false;

			$sortable[$id] = $data;
		}

		$_column_headers = array( $columns, $hidden, $sortable, array() );

		return $_column_headers;
	}
}