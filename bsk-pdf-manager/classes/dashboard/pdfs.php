<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class BSKPDFM_Dashboard_PDFs extends WP_List_Table {
    
    public static $_screen_opt_page = 'bsk_pdfm_pdfs_screen_per_page';
    public static $_screen_opt_columns = 'bsk_pdfm_pdfs_screen_columns';
    public static $_screen_available_columns = array( 
                                                        'featured_image' => 'Featured Image',
                                                        'location' => 'Location',
                                                        'description' => 'Description',
                                                        'category' => 'Category',
                                                        'tag' => 'Tag',
                                                        'order' => 'Order',
                                                        'last_date' => 'Date&amp;Time',
                                                        'publish_date' => 'Publish Date&amp;Time',
                                                        'expiry_date' => 'Expiry Date&amp;Time',
                                                        'download_count' => 'Download Count'
                                                    );
   
    function __construct() {
        global $wpdb;
		
        //Set parent defaults
        parent::__construct( array( 
            'singular' => 'bsk-pdf-manager-pdfs',  //singular name of the listed records
            'plural'   => 'bsk-pdf-manager-pdfs', //plural name of the listed records
            'ajax'     => false                          //does this table support ajax?
        ) );
    }

    function get_views(){
        $views = array();
        $current = ( !empty($_REQUEST['pdf_status']) ? $_REQUEST['pdf_status'] : 'all' );
        
        $pdfs_page_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['base']  );

        $pdfs_count = BSKPDFM_Common_Data_Source::bsk_pdfm_get_counts();

        //All link
        $class = ($current == 'all' ? ' class="current"' :'');
        $all_url = remove_query_arg( 'pdf_status', $pdfs_page_url );
        $views['all'] = "<a href='{$all_url }' {$class} >".__( 'All' )."</a> (".$pdfs_count['all'].")";

        //Published link
        $published_url = add_query_arg( 'pdf_status', 'published', $pdfs_page_url );
        $class = ($current == 'published' ? ' class="current"' :'');
        $views['published'] = "<a href='{$published_url}' {$class} >".__( 'Published' )."</a> (".$pdfs_count['published'].")";
        
        //draft link
        $draft_url = add_query_arg( 'pdf_status', 'draft', $pdfs_page_url );
        $class = ($current == 'draft' ? ' class="current"' :'');
        $views['draft'] = "<a href='{$draft_url}' {$class} >".__( 'Draft' )."</a> (".$pdfs_count['draft'].")";

        //scheduled link
        $scheduled_url = add_query_arg( 'pdf_status', 'scheduled', $pdfs_page_url );
        $class = ($current == 'scheduled' ? ' class="current"' :'');
        $views['scheduled'] = "<a href='{$scheduled_url}' {$class} >".__( 'Scheduled' )."</a> (".$pdfs_count['scheduled'].")";
        
        //expired link
        $expired_url = add_query_arg( 'pdf_status', 'expired', $pdfs_page_url );
        $class = ($current == 'expired' ? ' class="current"' :'');
        $views['expired'] = "<a href='{$expired_url}' {$class} >".__( 'Expired', 'bskpdfmanager' )."</a> (".$pdfs_count['expired'].")";

        //trash link
        $trash_url = add_query_arg( 'pdf_status', 'trash', $pdfs_page_url );
        $class = ($current == 'trash' ? ' class="current"' :'');
        $views['trash'] = "<a href='{$trash_url}' {$class} >".__( 'Trash' )."</a> (".$pdfs_count['trash'].")";

        return $views;
    }
	
	function extra_tablenav( $which ) {
		if ($which == 'bottom'){
			return;
		}
		
		global $wpdb;
		
		$sql = 'SELECT * FROM '.esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name);
		$categoreies = $wpdb->get_results($sql);
		
		$select_str_header = '<div class="alignleft actions">
								<select name="bsk_pdf_manager_categories" id="bsk_pdf_manager_categories_id">';
		$select_str_footer = '	</select>
							  </div>';
		
		if (!$categoreies || count($categoreies) < 1){
			$select_str_body = '<option value="0">'.esc_html__( 'Please add category first', 'bskpdfmanager' ).'</option>';
            
            echo $select_str_header.$select_str_body.$select_str_footer;
		}else{
			$current_category_id = 0;
			if( isset($_REQUEST['cat']) ){
				$current_category_id = sanitize_text_field($_REQUEST['cat']);
			}
			if( $current_category_id < 1 && isset($_REQUEST['bsk_pdf_manager_categories']) ){
				$current_category_id = sanitize_text_field($_REQUEST['bsk_pdf_manager_categories']);
			}

            $category_select_text = esc_html__( 'All category', 'bskpdfmanager' );
            $no_select_text = esc_html__( 'No category', 'bskpdfmanager' );
			$dropdown_str = BSKPDFM_Common_Backend::get_category_dropdown( 'bsk_pdf_manager_categories', 'bsk_pdf_manager_categories_id', $category_select_text, $no_select_text, array( $current_category_id ) );
            
            echo $dropdown_str;
            
            //extension
            $current_extension = '';
			if( isset($_REQUEST['ext']) ){
				$current_extension = sanitize_text_field($_REQUEST['ext']);
			}
            $extension_select_text = esc_html__( 'All extension', 'bskpdfmanager' );
			$extension_dropdown_str = BSKPDFM_Common_Backend::get_extension_dropdown( 'bsk_pdfm_extension_dropdown', 'bsk_pdfm_extension_dropdown_id', $extension_select_text, $current_extension, $current_category_id );
            
            echo $extension_dropdown_str;
            
            echo '<input type="submit" name="filter_action" id="bsk_pdfm_pdfs_filter_submit_id" class="button" value="Filter">';
		}
	}
    
    function get_columns() {
        
        $user_ID = get_current_user_id();
        $screen_saved_columns = get_user_meta( $user_ID, BSKPDFM_Dashboard_PDFs::$_screen_opt_columns, true );
        if( !is_array( $screen_saved_columns ) ){
            //has never saved then default to all checked
            $screen_saved_columns = array_keys( BSKPDFM_Dashboard_PDFs::$_screen_available_columns );
        }
		
		$default_enable_featured_image = true;
        $statistics_enable = false;
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '');
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
			if( isset($plugin_settings['enable_featured_image']) ){
				$default_enable_featured_image = $plugin_settings['enable_featured_image'];
			}
            if( isset( $plugin_settings['statistics_enable'] ) && $plugin_settings['statistics_enable'] ){
                $statistics_enable = $plugin_settings['statistics_enable'];
            }
		}
    	$columns = array(
                            'cb' => '<input type="checkbox"/>',
                            'id' => __( 'ID', 'bskpdfmanager' ),
                            'title' => __( 'Title', 'bskpdfmanager' )
                        );
		if ( $default_enable_featured_image && in_array( 'featured_image', $screen_saved_columns ) ) { 
			$columns['featured_image'] = __( 'Featured Image', 'bskpdfmanager' );
		}
        if ( in_array( 'location', $screen_saved_columns ) ) { 
            $columns['location'] = __( 'Location', 'bskpdfmanager' );
        }
        if ( in_array( 'description', $screen_saved_columns ) ) { 
            $columns['description'] = __( 'Description', 'bskpdfmanager' );
        }
        if ( in_array( 'category', $screen_saved_columns ) ) { 
            $columns['category'] = __( 'Category', 'bskpdfmanager' );
        }
        if ( in_array( 'tag', $screen_saved_columns ) ) { 
            $columns['tag'] = __( 'Tag', 'bskpdfmanager' );
        }
        if ( in_array( 'order', $screen_saved_columns ) ) { 
            $columns['order'] = __( 'Order', 'bskpdfmanager' );
        }
        if ( in_array( 'last_date', $screen_saved_columns ) ) { 
            $columns['last_date'] = __( 'Date&amp;Time', 'bskpdfmanager' );
        }
        if ( in_array( 'publish_date', $screen_saved_columns ) ) { 
            $columns['publish_date'] = __( 'Publish Date&amp;Time', 'bskpdfmanager' );
        }
        if ( in_array( 'expiry_date', $screen_saved_columns ) ) { 
            $columns['expiry_date'] = __( 'Expiry Date&amp;Time', 'bskpdfmanager' );
        }
        //statistics
        if( $statistics_enable && in_array( 'download_count', $screen_saved_columns ) ){
            $columns['download_count'] = __( 'Download Count', 'bskpdfmanager' );
        }
        
        return $columns;
    }
    
    function get_sortable_columns() {
		$c = array(
                    'id'        => 'id',
					'title' 	=> 'title',
					'order'  => 'order_num',
					'last_date' => 'last_date',
                    'publish_date' => 'expiry_date',
					);
        
        $plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 && 
            isset( $plugin_settings['statistics_enable'] ) && $plugin_settings['statistics_enable'] ){
            $c['download_count'] = 'download_count';
		}
		
		return $c;
	}
    
    function get_column_info() {
		$user_ID = get_current_user_id();
        $screen_saved_columns = get_user_meta( $user_ID, BSKPDFM_Dashboard_PDFs::$_screen_opt_columns, true );
        if( !is_array( $screen_saved_columns ) ){
            //has never saved then default to all checked
            $screen_saved_columns = array_keys( BSKPDFM_Dashboard_PDFs::$_screen_available_columns );
        }
        
		$default_enable_featured_image = true;
        $statistics_enable = false;
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
			if( isset($plugin_settings['enable_featured_image']) ){
				$default_enable_featured_image = $plugin_settings['enable_featured_image'];
			}
            if( isset( $plugin_settings['statistics_enable'] ) && $plugin_settings['statistics_enable'] ){
                $statistics_enable = $plugin_settings['statistics_enable'];
            }
		}
    	
        $columns = array(
                            'cb' => '<input type="checkbox"/>',
                            'id' => __( 'ID', 'bskpdfmanager' ),
                            'title' => __( 'Title', 'bskpdfmanager' )
                        );
		if ( $default_enable_featured_image && in_array( 'featured_image', $screen_saved_columns ) ) { 
			$columns['featured_image'] = __( 'Featured Image', 'bskpdfmanager' );
		}
        if ( in_array( 'location', $screen_saved_columns ) ) { 
            $columns['location'] = __( 'Location', 'bskpdfmanager' );
        }
        if ( in_array( 'description', $screen_saved_columns ) ) { 
            $columns['description'] = __( 'Description', 'bskpdfmanager' );
        }
        if ( in_array( 'category', $screen_saved_columns ) ) { 
            $columns['category'] = __( 'Category', 'bskpdfmanager' );
        }
        if ( in_array( 'tag', $screen_saved_columns ) ) { 
            $columns['tag'] = __( 'Tag', 'bskpdfmanager' );
        }
        if ( in_array( 'order', $screen_saved_columns ) ) { 
            $columns['order'] = __( 'Order', 'bskpdfmanager' );
        }
        if ( in_array( 'last_date', $screen_saved_columns ) ) { 
            $columns['last_date'] = __( 'Date&amp;Time', 'bskpdfmanager' );
        }
        if ( in_array( 'publish_date', $screen_saved_columns ) ) { 
            $columns['publish_date'] = __( 'Publish Date&amp;Time', 'bskpdfmanager' );
        }
        if ( in_array( 'expiry_date', $screen_saved_columns ) ) { 
            $columns['expiry_date'] = __( 'Expiry Date&amp;Time', 'bskpdfmanager' );
        }
        //statistics
        if( $statistics_enable && in_array( 'download_count', $screen_saved_columns ) ){
            $columns['download_count'] = __( 'Download Count', 'bskpdfmanager' );
        }
		
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
    
    protected function _column_title( $item, $classes, $data, $primary ) {
		echo '<td class="' . $classes . ' page-title" ', $data, '>';
		echo $this->column_title( $item );
		echo $this->handle_row_actions( $item, 'title', 'title' );
		echo '</td>';
	}
    
    public function column_title( $item ) {
        echo $item['title'];   
    }
    
    protected function handle_row_actions( $item, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return '';
		}
        
        $title = $item['row_title'];
        
        $pdfs_page_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['base']  );
        if( isset( $_REQUEST['pdf_status'] ) && sanitize_text_field( $_REQUEST['pdf_status'] ) ){
            $pdfs_page_url = add_query_arg( 'pdf_status', sanitize_text_field( $_REQUEST['pdf_status'] ), $pdfs_page_url );
        }
        
        $edit_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['edit'] );
        $edit_url = add_query_arg( 'pdfid', $item['id'], $edit_url );
        
        $draft_url = add_query_arg( 'action', 'draft', $pdfs_page_url );
        $draft_url = add_query_arg( 'pdfid', $item['id'], $draft_url );

        $publish_url = add_query_arg( 'action', 'publish', $pdfs_page_url );
        $publish_url = add_query_arg( 'pdfid', $item['id'], $publish_url );

		$trash_url = add_query_arg( 'action', 'trash', $pdfs_page_url );
        $trash_url = add_query_arg( 'pdfid', $item['id'], $trash_url );
        
        $untrash_url = add_query_arg( 'action', 'untrash', $pdfs_page_url );
        $untrash_url = add_query_arg( 'pdfid', $item['id'], $untrash_url );
        
        $delete_url = add_query_arg( 'action', 'delete', $pdfs_page_url );
        $delete_url = add_query_arg( 'pdfid', $item['id'], $delete_url );
        
        if ( isset( $_GET['cat'] ) ) {
            $edit_url = add_query_arg( 'cat', intval( $_GET['cat'] ), $edit_url );
            $draft_url = add_query_arg( 'cat', intval( $_GET['cat'] ), $draft_url );
            $publish_url = add_query_arg( 'cat', intval( $_GET['cat'] ), $publish_url );
            $trash_url = add_query_arg( 'cat', intval( $_GET['cat'] ), $trash_url );
            $delete_url = add_query_arg( 'cat', intval( $_GET['cat'] ), $delete_url );
        }

        if ( isset( $_GET['orderby'] ) ) {
            $edit_url = add_query_arg( 'orderby', sanitize_text_field( $_GET['orderby'] ), $edit_url );
            $draft_url = add_query_arg( 'orderby', sanitize_text_field( $_GET['orderby'] ), $draft_url );
            $publish_url = add_query_arg( 'orderby', sanitize_text_field( $_GET['orderby'] ), $publish_url );
            $trash_url = add_query_arg( 'orderby', sanitize_text_field( $_GET['orderby'] ), $trash_url );
            $delete_url = add_query_arg( 'orderby', sanitize_text_field( $_GET['orderby'] ), $delete_url );
        }

        if ( isset( $_GET['order'] ) ) {
            $edit_url = add_query_arg( 'order', sanitize_text_field( $_GET['order'] ), $edit_url );
            $draft_url = add_query_arg( 'order', sanitize_text_field( $_GET['order'] ), $draft_url );
            $publish_url = add_query_arg( 'order', sanitize_text_field( $_GET['order'] ), $publish_url );
            $trash_url = add_query_arg( 'order', sanitize_text_field( $_GET['order'] ), $trash_url );
            $delete_url = add_query_arg( 'order', sanitize_text_field( $_GET['order'] ), $delete_url );
        }
        
        $action_edit = sprintf(
                            '<a href="%s" aria-label="%s">%s</a>',
                            $edit_url,
                            /* translators: %s: Post title. */
                            esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $title ) ),
                            __( 'Edit' )
                        );
        $action_draft = sprintf(
                            '<a href="%s" aria-label="%s">%s</a>',
                            wp_nonce_url( $draft_url, 'draft-pdf_' . $item['id'] ),
                            /* translators: %s: Post title. */
                            esc_attr( sprintf( __( 'Draft &#8220;%s&#8221;' ), $title ) ),
                            __( 'Draft' )
                        );
        $action_publish = sprintf(
                            '<a href="%s" aria-label="%s">%s</a>',
                            wp_nonce_url( $publish_url, 'publish-pdf_' . $item['id'] ),
                            /* translators: %s: Post title. */
                            esc_attr( sprintf( __( 'Publish &#8220;%s&#8221;' ), $title ) ),
                            __( 'Publish' )
                        );
        $action_trash = sprintf(
                            '<a href="%s" class="submitdelete" aria-label="%s">%s</a>',
                            wp_nonce_url( $trash_url, 'trash-pdf_' . $item['id'] ),
                            /* translators: %s: Post title. */
                            esc_attr( sprintf( __( 'Move &#8220;%s&#8221; to the Trash' ), $title ) ),
                            _x( 'Trash', 'verb' )
                        );
        $action_untrash = sprintf(
                            '<a href="%s" aria-label="%s">%s</a>',
                            wp_nonce_url( $untrash_url, 'untrash-pdf_' . $item['id'] ),
                            /* translators: %s: Post title. */
                            esc_attr( sprintf( __( 'Restore &#8220;%s&#8221; from the Trash' ), $title ) ),
                            __( 'Restore' )
                          );
        $actions_delete = sprintf(
                            '<a href="%s" class="submitdelete" aria-label="%s">%s</a>',
                            wp_nonce_url( $delete_url, 'delete-pdf_' . $item['id'] ),
                            /* translators: %s: Post title. */
                            esc_attr( sprintf( __( 'Delete &#8220;%s&#8221; permanently' ), $title ) ),
                            __( 'Delete Permanently' )
                          );
        $action_view = sprintf(
                            '<a href="%s" rel="bookmark" aria-label="%s" target="_blank">%s</a>',
                            $item['view_url'],
                            /* translators: %s: Post title. */
                            esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $title ) ),
                            __( 'View' )
                       );
            
        $actions = array();
        
        if( $item['state'] == 'published' ){
            $actions['edit'] = $action_edit;
            $actions['draft'] = $action_draft;
            $actions['trash'] = $action_trash;
            $actions['view'] = $action_view;
        }else if( $item['state'] == 'draft' ){
            $actions['edit'] = $action_edit;
            $actions['publish'] = $action_publish;
            $actions['trash'] = $action_trash;
        }else if( $item['state'] == 'scheduled' ){
            $actions['edit'] = $action_edit;
            $actions['draft'] = $action_draft;
            $actions['trash'] = $action_trash;
        }else if( $item['state'] == 'expired' ){
            $actions['edit'] = $action_edit;
            $actions['draft'] = $action_draft;
            $actions['trash'] = $action_trash;
        }else if( $item['state'] == 'trash' ){
            $actions['untrash'] = $action_untrash;
            $actions['delete'] = $actions_delete;
        }
        
        
		return $this->row_actions( $actions );
	}
    
    function get_bulk_actions() {
        $current = ( !empty($_REQUEST['pdf_status']) ? $_REQUEST['pdf_status'] : 'all' );
        
        $actions = array( 
            'bulktrash' => __( 'Trash', 'bskpdfmanager' ),
			'changecat' => __( 'Change Category', 'bskpdfmanager' ),
            'changetag' => __( 'Change Tag', 'bskpdfmanager' ),
            'changedate' => __( 'Change Date', 'bskpdfmanager' ),
            'changetitle' => __( 'Change Title', 'bskpdfmanager' ),
            'generatethumb' => __( 'Generate Featured Image', 'bskpdfmanager' ),
        );
        
        if( $current == 'trash' ){
            $actions = array( 
                'bulkuntrash' => __( 'Restore', 'bskpdfmanager' ),
                'bulkdelete' => __( 'Delete Permanently', 'bskpdfmanager' ),
            );
        }
        
        
        return $actions;
    }

    function do_bulk_action() {
		global $wpdb;
		
		if (!isset($_POST['bsk-pdf-manager-pdfs']) || !is_array($_POST['bsk-pdf-manager-pdfs']) || count($_POST['bsk-pdf-manager-pdfs']) < 1) {
			return;
		}
        $lists_id = array();
        foreach( $_POST['bsk-pdf-manager-pdfs'] as $pdf_id ){
            $lists_id[] = intval(sanitize_text_field($pdf_id));
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

        if ( $action == -1 ){
			return;
		}else if ( $action == 'bulktrash' ){

			if( count($lists_id) < 1 ){
				return;
			}
            
            //update all pdfs as trash
            $sql = 'UPDATE `'.esc_sql($wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name).'` '.
                   'SET `trash` = 1 '.
                   'WHERE `id` IN('.implode( ',', $lists_id ).')';
            $wpdb->query( $sql );
		}else if ( $action == 'bulkuntrash' ){
			if( count($lists_id) < 1 ){
				return;
			}
            
            //update all pdfs as untrash
            $sql = 'UPDATE `'.esc_sql($wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name).'` '.
                   'SET `trash` = 0 '.
                   'WHERE `id` IN('.implode( ',', $lists_id ).')';
            $wpdb->query( $sql );
		}
    }
    
    function column_default( $item, $column_name ) {
        switch( $column_name ) {
			case 'id':
				echo $item['id_link'];
				break;
			case 'title':
				echo $item['title'];
				break;
            case 'featured_image':
				echo $item['featured_image'];
				break;
            case 'location':
                echo $item['file_name'];
                break;
            case 'description':
                echo $item['description'];
                break;
			case 'category':
				echo $item['category'];
				break;
            case 'tag':
				echo $item['tag'];
				break;
			case 'last_date':
               	echo $item['last_date'];
                break;
            case 'publish_date':
               	echo $item['publish_date'];
                break;
            case 'expiry_date':
               	echo $item['expiry_date'];
                break;
			case 'order':
				echo $item['order'];
                break;
            case 'download_count':
				echo $item['download_count'];
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

    function get_data() {
		global $wpdb;
		
		$list_thumbnail_size = 'bsk-pdf-dashboard-list-thumbnail';
		//read plugin settings
		$default_enable_featured_image = true;
		$default_thumbnail_html = '';
        $default_enable_permalink = false;
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '');
		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
			
			if( isset($plugin_settings['enable_featured_image']) ){
				$default_enable_featured_image = $plugin_settings['enable_featured_image'];
			}
			
			if( $default_enable_featured_image && isset($plugin_settings['default_thumbnail_id']) ){
				$default_thumbnail_id = $plugin_settings['default_thumbnail_id'];
				if( $default_thumbnail_id && get_post( $default_thumbnail_id ) ){
					$default_thumbnail_html = wp_get_attachment_image( $default_thumbnail_id, array( 50, 50 ) );
				}
			}
            
            if( isset($plugin_settings['enable_permalink']) ){
				$default_enable_permalink = $plugin_settings['enable_permalink'];
			}
            
		}
        
        $permalink_structure = get_option( 'permalink_structure' );
        if ( ! $permalink_structure ) {
            $default_enable_permalink = false;
        }
		
		$current_category_id = 0;
        $current_extension = '';
		$key_word = '';
		$orderby = '';
		$order = 'DESC';
        $pdf_status = '';
		if (isset($_REQUEST['cat'])){
			$current_category_id = intval( sanitize_text_field($_REQUEST['cat']) );
		}
        if (isset($_REQUEST['ext'])){
			$current_extension = sanitize_text_field($_REQUEST['ext']);
		}
		if (isset($_REQUEST['orderby'])){
			$orderby_by_user = sanitize_text_field($_REQUEST['orderby']);
            $sortable_headers = $this->get_sortable_columns();
            if ( in_array( $orderby_by_user, $sortable_headers ) ) {
                $orderby = $orderby_by_user;
            }
		}
		if (isset($_REQUEST['order'])){
			$order_by_user = strtoupper( sanitize_text_field($_REQUEST['order']) );
            if ( $order_by_user == 'ASC' ) {
                $order = $order_by_user;
            }
		}
		if (isset($_REQUEST['s'])){
			$key_word = sanitize_text_field($_REQUEST['s']);
		}
        if (isset($_REQUEST['pdf_status'])){
			$pdf_status = sanitize_text_field($_REQUEST['pdf_status']);
		}
		
		$sql = 'SELECT P.* FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name).'` AS P ';
		$whereCase = ' WHERE 1 ';
        if( $pdf_status == '' ){
            $whereCase .= 'AND P.`trash` = 0 ';
        }else if( $pdf_status == 'published' ){
            $whereCase .= 'AND (`publish_date` IS NULL OR `publish_date` <= "'.wp_date( 'Y-m-d H:i:s' ).'") '.
                          'AND (`expiry_date` IS NULL OR `expiry_date` > "'.wp_date( 'Y-m-d H:i:s' ).'") '.
                          'AND ( LENGTH(`file_name`) > 0 || `by_media_uploader` > 0 ) '.
                          'AND (`draft` = 0 ) ' .
                          'AND (`trash` = 0)';
        }else if( $pdf_status == 'scheduled' ){
            $whereCase .= 'AND (`publish_date` IS NOT NULL AND `publish_date` > "'.wp_date( 'Y-m-d H:i:s' ).'") '.
                          'AND (`expiry_date` IS NULL OR `expiry_date` > "'.wp_date( 'Y-m-d H:i:s' ).'") '.
                          'AND ( LENGTH(`file_name`) > 0 || `by_media_uploader` > 0 ) '.
                          'AND (`draft` = 0 ) ' .
                          'AND (`trash` = 0)';
        }else if( $pdf_status == 'expired' ){
            $whereCase .= 'AND (`expiry_date` IS NOT NULL AND `expiry_date` <= "'.wp_date( 'Y-m-d H:i:s' ).'") '.
                          'AND ( LENGTH(`file_name`) > 0 || `by_media_uploader` > 0 ) '.
                          'AND (`draft` = 0 ) ' .
                          'AND (`trash` = 0)';
        }else if( $pdf_status == 'draft' ){
            $whereCase .= 'AND ( ( LENGTH(`file_name`) < 1 AND `by_media_uploader` < 1 ) OR ( `draft` = 1 ) ) '.
                          'AND (`trash` = 0)';
        }else if( $pdf_status == 'trash' ){
            $whereCase .= 'AND (`trash` = 1)';
        }
        
		if( $current_category_id != 0 ){
            $pdf_id_sql = 'SELECT R.`pdf_id` FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_rels_tbl_name).'` AS R '.
                          'WHERE R.`cat_id` = %d';
            $pdf_id_sql = $wpdb->prepare( $pdf_id_sql, $current_category_id );
            
            if( $current_category_id < 0 ){
                //query pdfs that have no category
                $pdf_id_sql = 'SELECT `id` FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name).'` WHERE `id` '.
                              'NOT IN( SELECT DISTINCT(`pdf_id`) FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_rels_tbl_name).'` WHERE `type` LIKE "CAT" )';
            }
            
			$cat_whereCase = ' AND P.`id` IN( '.$pdf_id_sql.' ) ';
			$whereCase .= $cat_whereCase;
		}
        if( $current_extension ){
			$extension_whereCase = ' AND ( P.`file_name` LIKE %s OR P.`media_ext` LIKE %s )';
			$whereCase .= $wpdb->prepare( $extension_whereCase, '%.'.esc_sql($current_extension), $current_extension );
		}
		if( $key_word ){
            $key_word = esc_sql($key_word);
			$search_whereCase = ' AND ( P.`title` LIKE %s OR P.`file_name` LIKE %s OR P.`description` LIKE %s )';
			$whereCase .= $wpdb->prepare( $search_whereCase, '%'.$key_word.'%', '%'.$key_word.'%', '%'.$key_word.'%' );
		}
		$orderCase = ' ORDER BY P.`last_date` DESC, P.`id` DESC';
        if( $orderby == 'id' ){
			$orderCase = ' ORDER BY P.`id` '.$order;
		}else if( $orderby == 'title' ){
			$orderCase = ' ORDER BY P.title '.$order.', P.last_date DESC, P.`id` DESC';
		}else if( $orderby == 'last_date' ){
			$orderCase = ' ORDER BY P.last_date '.$order.', P.`id` DESC';
		}else if( $orderby == 'publish_date' ){
			$orderCase = ' ORDER BY P.publish_date '.$order.', P.`id` DESC';
		}else if( $orderby == 'order_num' ){
			$orderCase = ' ORDER BY P.order_num '.$order.', P.`id` DESC';
		}else if( $orderby == 'download_count' ){
            $orderCase = ' ORDER BY P.download_count '.$order.', P.`id` DESC';
        }

        //get all pdfs
		$all_pdfs = $wpdb->get_results($sql.$whereCase.$orderCase);
		if (!$all_pdfs || count($all_pdfs) < 1){
			return NULL;
		}
		
		//organise all category and tag data
		$sql = 'SELECT * FROM '.$wpdb->prefix.BSKPDFManager::$_cats_tbl_name;
		$categoreies = $wpdb->get_results( $sql );
		$categoreies_tags_data_array = array();
		foreach( $categoreies as $category_obj ){
			$categoreies_tags_data_array[$category_obj->id] = $category_obj->title;
		}
		
		$edit_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['edit']  );
        
		$lists_data = array();
		foreach($all_pdfs as $pdf_record){
			$edit_url = add_query_arg('pdfid', $pdf_record->id, $edit_url);
            if( $current_category_id ){
                $edit_url = add_query_arg('cat', $current_category_id, $edit_url);
            }
			$file_str = '';
            $file_url_view = '';
			$is_draft = false;
			if( $pdf_record->by_media_uploader > 1 ){
				$file_url = wp_get_attachment_url( $pdf_record->by_media_uploader );
                if( $file_url ){
                    $file_name_with_out_dir_structure = str_replace( site_url().'/', '', $file_url );
                    $file_url_view = $file_url;
                    if( $default_enable_permalink ){
                        $file_url_view = site_url().'/bsk-pdf-manager/'.$pdf_record->slug.'/';
                    }
                    $file_str = $pdf_record->trash ? $file_name_with_out_dir_structure : '<a href="'.$file_url_view.'" target="_blank" title="'.esc_attr__('Open Document', 'bskpdfmanager' ).'">'.$file_name_with_out_dir_structure.'</a>';
                }
			}else if( $pdf_record->file_name ){
                if( file_exists(BSKPDFManager::$_upload_root_path.$pdf_record->file_name) ){
                    $file_url = site_url().'/'.$pdf_record->file_name;
                    $file_url_view = $file_url;
                    if( $default_enable_permalink ){
                        $file_url_view = site_url().'/bsk-pdf-manager/'.$pdf_record->slug.'/';
                    }
                    $file_str = $pdf_record->trash ? esc_html($pdf_record->file_name) : '<a href="'.$file_url_view.'" target="_blank" title="'.esc_attr__('Open Document', 'bskpdfmanager' ).'">'.esc_html($pdf_record->file_name).'</a>';
                }else{
                    $file_str = esc_html($pdf_record->file_name);
                    if( $file_str ){
                        $file_str .= '<p><span style="color: #dc3232; font-weight:bold;">'.esc_html__('Missing file', 'bskpdfmanager' ).'</span></p>';
                    }
                }
			}else{
                //draft
                $is_draft = true;
            }

            if ( $pdf_record->draft ) {
                //draft
                $is_draft = true;
            }

            //category & tag
            $category_tag_ids_array = array();

			$category_str = '';
			$category_str_array = array();
            
            $tag_str = '';
			$tag_str_array = array();
            
            //get all categories & tags which the PDF associated
            $sql = 'SELECT * FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_rels_tbl_name).'` WHERE `pdf_id` = %d ';
            $sql = $wpdb->prepare($sql, $pdf_record->id);
            $results = $wpdb->get_results( $sql );
            if( $results && is_array($results) && count($results) > 0 ){
                foreach( $results as $rel_obj ){
                    $category_tag_ids_array[$rel_obj->cat_id] = $rel_obj->type;
                }
            }

            foreach( $category_tag_ids_array as $category_id => $type ){
				if( !isset($categoreies_tags_data_array[$category_id]) ){
					continue;
				}
                $cat_title_to_display = '';
                $parents_str = '';
                $parents_id = BSKPDFM_Common_Backend::get_category_parent_ids( $category_id );
                if( $parents_id && is_array($parents_id) && count($parents_id) > 0 ){
                    $cat_id_sequence = array_reverse( $parents_id );
                    $cat_id_sequence[] = $category_id;
                    $prefix = '';
                    $cat_title_to_display_array = array();
                    foreach( $cat_id_sequence as $cat_id ){
                        if( !isset($categoreies_tags_data_array[$cat_id]) ){
                            continue;
                        }
                        if( $cat_id == $category_id ){
                            $cat_title_to_display_array[] = '<span style="color:#0073aa;">'.$prefix.esc_html($categoreies_tags_data_array[$cat_id]).'</span>';
                        }else{
                            $cat_title_to_display_array[] = $prefix.esc_html($categoreies_tags_data_array[$cat_id]);
                        }
                        $prefix .= '&#8212;&nbsp;';
                    }
                    $cat_title_to_display = implode('<br />', $cat_title_to_display_array);
                }else{
                    $cat_title_to_display = '<span style="color:#0073aa;">'.esc_html($categoreies_tags_data_array[$category_id]).'</span>';
                }
                
                if( $type == 'TAG' ){
                    $tag_str_array[] = $cat_title_to_display;
                }else{
                    $category_str_array[] = $cat_title_to_display;
                }
			}
            
            sort( $tag_str_array );
            sort( $category_str_array );
            
			$category_str = implode('<hr class="bsk-pdfm-pdf-list-horizal-line"/>', $category_str_array);
            $tag_str = implode(', ', $tag_str_array);
			
			//featured image
			$thumbnail_html = '';
			if( $default_enable_featured_image ) { 
				$thumbnail_html = '<img src="'.esc_url(BSKPDFManager::$_default_pdf_icon_url).'" width="50" height="50" />';
			}
			
			//pdf order
			$pdf_order_html = '<input type="number" class="bsk_pdfm_pdf_order" rel="'.esc_attr($pdf_record->id).'" value="'.esc_attr($pdf_record->order_num).'" min="0" />';
			$pdf_order_html .= '<span id="bsk_pdfm_pdf_order_ajax_loader_ID_'.esc_attr($pdf_record->id).'" style="display:none;"><img src="'.esc_url(BSKPDFManager::$_ajax_loader_img_url).'" /></span>';
			
            $state = 'published';
            $state_label = __( 'Published' );
            if( !empty( $pdf_record->publish_date ) && $pdf_record->publish_date > wp_date( 'Y-m-d H:i:s' ) ){
                $state_label = __( 'Scheduled' );
                $state = 'scheduled';
            }
            if( !empty( $pdf_record->expiry_date ) && $pdf_record->expiry_date <= wp_date( 'Y-m-d H:i:s' ) ){
                $state_label = __( 'Expired', 'bskpdfmanager' );     
                $state = 'expired';
            }
            if( $is_draft ){
                $state_label = __( 'Draft', 'bskpdfmanager' );     
                $state = 'draft';
            }
            
            $title = '<a class="row-title" href="'.esc_url($edit_url).'">'.esc_html($pdf_record->title).'</a>';
            if( $state == 'scheduled' || $state == 'expired' || $state == 'draft' ){
                $post_states_string = ' &mdash; ';
                $title .= $post_states_string.'<span class="post-state">'.$state_label.'</span>';
            }
            if( $pdf_record->trash ){
                $state = 'trash';
                $title = esc_html($pdf_record->title);
            }     
            
            $description = wp_trim_words( $pdf_record->description, 20 );
            
            $date_time = $state_label ? $state_label.'<br />'.$pdf_record->last_date : $pdf_record->last_date;
			$row_data =  array( 
                                    'id'			=> $pdf_record->id,
                                    'id_link' 	    => $pdf_record->id,
                                    'title'     		=> '<strong>'.$title.'</strong>',
                                    'row_title'     	=> esc_html($pdf_record->title),
                                    'file_name'     	=> $file_str,
                                    'description'     	=> $description,
                                    'category'			=> $category_str,
                                    'tag'			    => $tag_str,
                                    'last_date' 		=> $date_time,
                                    'publish_date' 		=> empty( $pdf_record->publish_date ) ? '' : $pdf_record->publish_date,
                                    'expiry_date' 		=> empty( $pdf_record->expiry_date ) ? '' : $pdf_record->expiry_date,
                                    'order'				=> $pdf_order_html,
                                    'download_count'    => $pdf_record->download_count,
                                    'edit_url'          => $edit_url,
                                    'view_url'          => $file_url_view,
                                    'state'             => $state,
                                    'draft'             => $is_draft,
				              );

			if ( $default_enable_featured_image ) {
				$row_data['featured_image'] = $thumbnail_html;
			}
			$lists_data[] = $row_data;
		}
		
		return $lists_data;
    }

    function prepare_items() {
       
        /**
         * First, lets decide how many records per page to show
         */
        $user_ID = get_current_user_id();
        $per_page = get_user_meta( $user_ID, BSKPDFM_Dashboard_PDFs::$_screen_opt_page, true );
        if( !$per_page  ){
            //has never saved then default
            $per_page = 20;
        }
        
        $data = array();
		
        add_thickbox();

        $columns = $this->get_columns();
        $hidden = array(); // no hidden columns
       
        $this->_column_headers = array( $columns, $hidden );
       
        $this->do_bulk_action();
       
        $data = $this->get_data();
   
        $current_page = $this->get_pagenum();
    
        $total_items = 0;
        if( $data&& is_array($data) ){
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
	  
}
