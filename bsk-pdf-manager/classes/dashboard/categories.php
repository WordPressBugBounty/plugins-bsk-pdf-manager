<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class BSKPDFM_Dashboard_Categories extends WP_List_Table {

    public static $_screen_opt_page = 'bsk_pdfm_categories_screen_per_page';
    public static $_screen_opt_default_order_by = 'bsk_pdfm_categories_screen_default_order_by';
    public static $_screen_opt_default_order_by_columns = array( 
                                                        'id' => 'ID',
                                                        'title' => 'Title',
                                                        'last_date'    => 'Date&amp;Time'
                                                    );
                                                    
    function __construct() {
        global $wpdb;
		
        //Set parent defaults
        parent::__construct( array( 
            'singular' => 'bsk-pdf-manager-categories',  //singular name of the listed records
            'plural'   => 'bsk-pdf-manager-categories', //plural name of the listed records
            'ajax'     => false                          //does this table support ajax?
        ) );
    }

    function column_default( $item, $column_name ) {
        switch( $column_name ) {
			case 'id':
                ?>
                <a href="<?php echo esc_url( $item['category_edit_page'] ); ?>"><?php echo esc_attr( $item['id'] ); ?></a>
                <?php
				break;
			case 'title':
                ?>
                <strong><a class="row-title" href="<?php echo esc_url($item['category_edit_page']); ?>"><?php echo esc_attr($item['title']); ?></a></strong>
                <?php
				break;
            case 'description':
				echo wp_kses_post( $item['description'] );
				break;
			case 'password':
                echo esc_html( $item['password'] );
                break;
            case 'last_date':
                echo esc_html( $item['last_date'] );
                break;
            case 'count':
                ?>
                <a href="<?php echo esc_url( $item['pdfs_list_by_category_url'] ); ?>"><?php echo esc_html( $item['count'] ); ?></a>
                <?php
                break;
        }
    }
   
    function column_cb( $item ) {
        return sprintf( 
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            esc_attr( $this->_args['singular'] ),
            esc_attr( $item['id'] )
        );
    }

    function get_columns() {
    
        $columns = array( 
			'cb'        		=> '<input type="checkbox"/>',
			'id'				=> __( 'ID', 'bskpdfmanager' ), 
            'title'     	=> __( 'Title', 'bskpdfmanager' ), 
            'description'     => __( 'Description', 'bskpdfmanager' ), 
			'password'     		=> __( 'Password', 'bskpdfmanager' ), 
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
        
        if (!isset($_POST['bsk-pdf-manager-categories']) || 
            !is_array( $_POST['bsk-pdf-manager-categories'] ) || 
            count( $_POST['bsk-pdf-manager-categories'] ) < 1 ){
            
			return;
		}
        $categories_id = array();
        foreach( $_POST['bsk-pdf-manager-categories'] as $category_id ){
            $categories_id[] = intval(sanitize_text_field($category_id));
        }
		
		$action = -1;
		if (isset($_POST['action'])){
            $temp_action = sanitize_text_field($_POST['action']);
			$action = $temp_action != -1 ? $temp_action : $action;
		}
        if (isset($_POST['action2'])){
            $temp_action = sanitize_text_field($_POST['action2']);
			$action = $temp_action != -1 ? $temp_action : $action;
		}
		
		if ($action == -1){
			return;
		}else if ( $action == 'delete' && count($categories_id) ){
			
            $ids = implode(',', esc_sql($categories_id));
			$ids = trim($ids);
            $sql = 'SELECT `id`, `parent` FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name).'` WHERE `id` IN('.$ids.')';
            $categories_to_delete = $wpdb->get_results( $sql );
            if( !$categories_to_delete && !is_array($categories_to_delete) && count($categories_to_delete) < 1 ){
                return;
            }
            
            //delete all relations associated to the category
            $sql = 'DELETE FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_rels_tbl_name).'` '.
                   'WHERE `cat_id` IN('.$ids.') AND `type` LIKE "CAT"';
            $wpdb->query( $sql );
            
            //delete all categories
			$sql = 'DELETE FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name).'` WHERE id IN('.$ids.')';
			$wpdb->query( $sql );
            
		}
    }

    function get_data() {
		global $wpdb;
		
        $search = '';
		$order = 'ASC';

        $user_ID = get_current_user_id();
        $orderby = get_user_meta( $user_ID, self::$_screen_opt_default_order_by, true );
        if( ! $orderby ){
            $orderby = 'title';
        }

        // check to see if we are searching
        if (isset($_POST['s'])) {
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
               'WHERE 1 AND c.`parent` = 0 AND c.`type` LIKE "CAT" ';
        if ($search) {
            $sql .= 'c.`title` %s ';
            $sql = $wpdb->prepare($sql, '%'.esc_sql($search).'%');
        }
        $sql .= 'ORDER BY c.`'.esc_sql($orderby).'` '.esc_sql($order);
		$catgories = $wpdb->get_results($sql);
		
		if (!$catgories || count($catgories) < 1){
			return NULL;
		}
		$category_page_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['category'] );
		$pdfs_list_page_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['base'] );
       
		$categories_data = array();
		foreach ( $catgories as $category ) {
            $current_category_depth = 1;
			$category_edit_page = add_query_arg( 
                                                 array('view' => 'edit', 
                                                        'categoryid' => $category->id
                                                      ),
                                                 $category_page_url 
                                               );
            $pdfs_list_by_category_url = add_query_arg(array('cat' => $category->id), $pdfs_list_page_url);
			$categories_data[] = array( 
			    'id' 				=> $category->id,
                'category_edit_page' => $category_edit_page,
				'title'     	=> $category->title,
                'description'   => $category->description,
				'password'			=> $category->password,
				'last_date'			=> $category->last_date,
                'pdfs_list_by_category_url' => $pdfs_list_by_category_url,
                'count'             => BSKPDFM_Common_Backend::get_cat_pdfs_count( $category->id ),
			);
		}
		
		return $categories_data;
    }

    function prepare_items() {
       
        /**
         * First, lets decide how many records per page to show
         */
        $user_ID = get_current_user_id();
        $per_page = get_user_meta( $user_ID, self::$_screen_opt_page, true );
        if( ! $per_page ){
            //has never saved then default
            $per_page = 20;
        }

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
							'password'     		=> __( 'Password', 'bskpdfmanager' ), 
							'last_date' 		=> __( 'Date&amp;Time', 'bskpdfmanager' ), 
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