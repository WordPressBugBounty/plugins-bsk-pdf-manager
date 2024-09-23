<?php
class BSKPDFM_Dashboard {
    
    public static $_bsk_pdfm_pro_pages = array(  
                                                 'base'		=> 'bsk-pdf-manager',
                                                 'category' => 'bsk-pdf-manager-category', 
                                                 'pdf' 		=> 'bsk-pdf-manager-pdfs', 
                                                 'tag'      => 'bsk-pdfm-tag',
                                                 'edit' 	=> 'bsk-pdf-manager-edit',
                                                 'add_by_ftp' 	=> 'bsk-pdf-manager-add-by-ftp',
                                                 'add_by_media_library' 	=> 'bsk-pdf-manager-add-by-media-library',
                                                 'setting' 		=> 'bsk-pdf-manager-settings-support', 
                                                 'migrate'      => 'bsk-pdfm-migrate',
                                                 'help' 		=> 'bsk-pdfm-help',
                                                 'license_update' => 'bsk-pdf-manager-license-update',
                                                 'notification' => 'bsk-pdfm-notification' );
    
    public static $notification_trigger_auto_action = array(
                                                        'NEW_SAVED' => 'New PDF / Document Saved',
                                                        'UPDATED' => 'PDF / Document Updated',
                                                        'BULK_FTP' => 'Bulk uploaded by FTP',
                                                        'BULK_MEDIA' => 'Bulk uploaded by Media Library',
                                                    );
    
    private static $_pro_tips_for_category = array( 
                                    'Hierarchical Category',
                                    'Description', 
                                    'Password', 
                                    'Empty Message',
                                    'Time',
                                   );
    private static $_pro_tips_for_tag = array( 
                                     'Time',
                                     'Display tags order by date(time) in front'
                                   );
    private static $_pro_tips_for_pdf = array( 
                                    'Custom order',
                                    'Mulitple Categories',
                                    'Mulitple Tags',
                                    'File name ( exclude extension ) as title',
                                    'Description', 
                                    'Upload from Media Library', 
                                    'Featured Image',
                                    'Bulk change Category',
                                    'Bulk change Tag',
                                    'Bulk change title',
                                    'Bulk change date',
                                    'Date of file last modified',
                                    'Parse date from filename',
                                    'Time',
                                    'Publish Date',
                                    'Expiry Date',
                                    'Download Count',
                                    'Featured Image',
                                    'Generate Featured Image',
                                    'Redirect permalink to file URL',
                                    'Search Bar',
                                    'Count Bar',
                                    'Date weekday filter',
                                    'Date weekday query filter',
                                    'Extension filter',
                                    'Title start filter',
                                    'Tags filter',
                                   );
    public static $_pro_tips_for_pdf_bulk_change_category = array( 
                                    'Bulk change category',
                                   );
    public static $_pro_tips_for_pdf_bulk_change_tag = array( 
                                    'Bulk change tag',
                                   );
    public static $_pro_tips_for_pdf_bulk_change_date_time = array( 
                                    'Bulk change date&amp;time',
                                   );
    public static $_pro_tips_for_pdf_bulk_change_title = array( 
                                    'Bulk change title',
                                   );
    public static $_pro_tips_for_add_by_ftp = array( 
                                    'Add by FTP',
                                    'Parse date from filename',
                                    'Generate Featured Image',
                                   );
    public static $_pro_tips_for_add_by_media_library = array( 
                                    'Bulk Add by Media Library',
                                    'Parse date from filename'
                                   );
    public static $_pro_tips_for_settings = array( 
                                    'Disable year/month directory strtucutre',
                                    'Change upload folder',
                                    'Backend access',
                                    'Featured image',
                                    'Statistics',
                                    'Change permalink URL structure base',
                                    'Enable Editor, Author, Contributor to access backend'
                                   );
    private static $_pro_tips_for_notifications = array( 
                                    'All'
                                  );
	private static $_bsk_pdfm_OBJ = NULL;
	private static $_bsk_pdfm_OBJ_category = NULL;
    private static $_bsk_pdfm_OBJ_tag = NULL;
	private static $_bsk_pdfm_OBJ_pdf = NULL;
    private static $_bsk_pdfm_OBJ_pdf_list_action = NULL;
    private static $_bsk_pdfm_OBJ_ftp = NULL;
    private static $_bsk_pdfm_OBJ_media_library = NULL;
	private static $_bsk_pdfm_OBJ_settings = NULL;
    private static $_bsk_pdfm_OBJ_notification = NULL;
	private static $_bsk_pdfm_OBJ_migrate = NULL;
    private static $_bsk_pdfm_OBJ_promote = NULL;

    private $_bsk_pdfm_pdfs_menu_hook = NULL;
	private $_bsk_pdfm_categories_menu_hook = NULL;

	public function __construct() {
		
		require_once( 'categories.php' );
		require_once( 'category.php' );
        require_once( 'pdfm-tags.php' );
		require_once( 'pdfm-tag.php' );
		require_once( 'pdfs.php' );	
		require_once( 'pdf.php' );
        require_once( 'pdfs-action.php' );
        require_once( 'ftp.php' );
        require_once( 'media-library.php' );
		require_once( 'settings/settings.php' );
        require_once( 'notifications.php' );
        require_once( 'notification.php' );
        require_once( 'migrate.php' );
        require_once( 'ads.php' );
        require_once( 'promote.php' );

        self::$_bsk_pdfm_OBJ_category = new BSKPDFM_Dashboard_Category();
        self::$_bsk_pdfm_OBJ_tag = new BSKPDFM_Dashboard_Tag();
		self::$_bsk_pdfm_OBJ_pdf = new BSKPDFM_Dashboard_PDF();
        self::$_bsk_pdfm_OBJ_pdf_list_action = new BSKPDFM_Dashboard_PDF_List_Action();
        self::$_bsk_pdfm_OBJ_ftp = new BSKPDFM_Dashboard_FTP();
        self::$_bsk_pdfm_OBJ_media_library = new BSKPDFM_Dashboard_Media_Library();
		self::$_bsk_pdfm_OBJ_settings = new BSKPDFM_Dashboard_Settings();
        self::$_bsk_pdfm_OBJ_notification = new BSKPDFM_Dashboard_Notification();
        self::$_bsk_pdfm_OBJ_migrate = new BSKPDFM_Dashboard_Migrate();
        self::$_bsk_pdfm_OBJ_promote = new BSKPDFM_Dashboard_Promote();
		
		add_action( 'admin_menu', array( $this, 'bsk_pdf_manager_dashboard_menu' ) );
        
        add_filter( 'screen_settings', array( $this, 'bsk_pdfm_add_other_screen_options_fun' ), 10, 2 );
        add_filter( 'set-screen-option', array ( $this, 'bsk_pdfm_save_screen_options_fun' ), 10, 3 );

        add_action( 'wp_print_scripts', array ( $this, 'bsk_pdfm_remove_the_event_calendar_js_fun' ), 999 );

        add_action( 'admin_notices', array ( $this, 'bsk_pdfm_dropdown_warning_fun' ) );
	}
	
	function bsk_pdf_manager_dashboard_menu() {
		
        add_menu_page( 
                         'BSK PDF Mngr', 
                         'BSK PDF Mngr', 
                         'manage_options', 
                         self::$_bsk_pdfm_pro_pages['base'], 
                         '', 
                         'dashicons-media-document'
                     );

        $this->_bsk_pdfm_pdfs_menu_hook = add_submenu_page( 
                                                            self::$_bsk_pdfm_pro_pages['base'],
                                                            __( 'PDF / Documents', 'bskpdfmanager' ),
                                                            __( 'PDF / Documents', 'bskpdfmanager' ),
                                                            'manage_options', 
                                                            self::$_bsk_pdfm_pro_pages['base'],
                                                            array($this, 'bsk_pdf_manager_pdfs_list') 
                                                        );
        if( $this->_bsk_pdfm_pdfs_menu_hook ){
            add_action( 'load-'.$this->_bsk_pdfm_pdfs_menu_hook, array( $this, 'bsk_pdfm_pages_pdfs_add_screen_option_fun' ) );
        }

        add_submenu_page( 
                            self::$_bsk_pdfm_pro_pages['base'],
                            __( 'Add New / Edit', 'bskpdfmanager' ),
                            __( 'Add New / Edit', 'bskpdfmanager' ),
                            'manage_options', 
                            self::$_bsk_pdfm_pro_pages['edit'],
                            array($this, 'bsk_pdf_manager_pdfs_edit') 
        );

        add_submenu_page( self::$_bsk_pdfm_pro_pages['base'],
                          __( 'Add by FTP', 'bskpdfmanager' ), 
                          __( 'Add by FTP', 'bskpdfmanager' ), 
                          'manage_options', 
                          self::$_bsk_pdfm_pro_pages['add_by_ftp'],
                          array($this, 'bsk_pdf_manager_pdfs_add_by_ftp_interface') );	

        add_submenu_page( self::$_bsk_pdfm_pro_pages['base'],
                          __( 'Add by Media Library', 'bskpdfmanager' ), 
                          __( 'Add by Media Library', 'bskpdfmanager' ), 
                          'manage_options', 
                          self::$_bsk_pdfm_pro_pages['add_by_media_library'],
                          array($this, 'bsk_pdf_manager_pdfs_add_by_media_library_interface') );

        $this->_bsk_pdfm_categories_menu_hook = add_submenu_page( 
                            self::$_bsk_pdfm_pro_pages['base'],
                            __( 'Categories', 'bskpdfmanager' ), 
                            __( 'Categories', 'bskpdfmanager' ), 
                            'manage_options', 
                            self::$_bsk_pdfm_pro_pages['category'],
                            array($this, 'bsk_pdf_manager_categories') );
        if( $this->_bsk_pdfm_categories_menu_hook ){
            add_action( 'load-'.$this->_bsk_pdfm_categories_menu_hook, array( $this, 'bsk_pdfm_pages_categories_add_screen_option_fun' ) );
        }
        
        add_submenu_page( self::$_bsk_pdfm_pro_pages['base'],
							  __( 'Tags', 'bskpdfmanager' ), 
							  __( 'Tags', 'bskpdfmanager' ),  
							  'manage_options', 
							  self::$_bsk_pdfm_pro_pages['tag'],
							  array($this, 'bsk_pdf_manager_tags') );

        add_submenu_page( 
                        self::$_bsk_pdfm_pro_pages['base'],
                        __( 'Notifications', 'bskpdfmanager' ), 
                        __( 'Notifications', 'bskpdfmanager' ),  
                        'manage_options', 
                        self::$_bsk_pdfm_pro_pages['notification'],
                        array($this, 'bsk_pdfm_manage_notifications') 
                    );

        add_submenu_page( self::$_bsk_pdfm_pro_pages['base'],
                          __( 'Settings', 'bskpdfmanager' ), 
                          __( 'Settings', 'bskpdfmanager' ), 
                          'manage_options', 
                          self::$_bsk_pdfm_pro_pages['setting'],
                          array($this, 'bsk_pdf_manager_settings_support') );
        
        add_submenu_page( 
                        self::$_bsk_pdfm_pro_pages['base'],
                        __( 'Migrate', 'bskpdfmanager' ),  
                        __( 'Migrate', 'bskpdfmanager' ),  
                        'manage_options', 
                        self::$_bsk_pdfm_pro_pages['migrate'],
                        array($this, 'bsk_pdf_manager_migrage') 
                    );

	}

    function bsk_pdf_manager_pdfs_edit() {
        $pdf_id = -1;
        if(isset($_GET['pdfid']) && sanitize_text_field($_GET['pdfid'])){
            $pdf_id = trim(sanitize_text_field($_GET['pdfid']));
            $pdf_id = intval($pdf_id);
        }

        $page_title = 'Add New BSK PDF Document';
        $action_url = admin_url( 'admin.php?page='.self::$_bsk_pdfm_pro_pages['base'] );
        if( $pdf_id > 0 ){
            $page_title = 'Edit BSK PDF Document';
            $action_url = add_query_arg( 'pdfid', $pdf_id, $action_url );
        }
        $add_new_url = admin_url( 'admin.php?page='.self::$_bsk_pdfm_pro_pages['edit'] );
        $add_new_link = '<a href="'.esc_url( $add_new_url ).'" class="add-new-h2">'.esc_html__( 'Add New', 'bskpdfmanager' ).'</a>';
        ?>
        <div class="wrap">
            <div id="icon-edit" class="icon32"><br/></div>
            <h2><?php echo esc_html__( $page_title, 'bskpdfmanager' ).$add_new_link; ?></h2>
        <?php $this->bsk_pdf_manager_show_pro_tip_box( self::$_pro_tips_for_pdf ); ?>
            <form id="bsk-pdf-manager-pdfs-form-id" method="post" enctype="multipart/form-data" action="<?php echo esc_url( $action_url ); ?>">
                <?php self::$_bsk_pdfm_OBJ_pdf->pdf_edit( $pdf_id ); ?>
            </form>
            </div>
        <?php
    }
	
	function bsk_pdf_manager_categories(){
		global $current_user;
		

		$categories_curr_view = 'list';
		if(isset($_REQUEST['view'])){
            $temp_view = trim(sanitize_text_field($_REQUEST['view']));
			$categories_curr_view = $temp_view ? $temp_view : $categories_curr_view;
		}
		
		$category_base_page = admin_url( 'admin.php?page='.self::$_bsk_pdfm_pro_pages['category'] );
        $category_add_new_page = add_query_arg( 'view', 'addnew', $category_base_page );
		if ($categories_curr_view == 'list'){
            $_bsk_pdfm_OBJ_categories = new BSKPDFM_Dashboard_Categories();

			//Fetch, prepare, sort, and filter our data...
			$_bsk_pdfm_OBJ_categories->prepare_items();
				
			echo '<div class="wrap">
					<div id="icon-edit" class="icon32"><br/></div>
					<h2>'.esc_html__( 'BSK PDF Categories', 'bskpdfmanager' ).'<a href="'.esc_url($category_add_new_page).'" class="add-new-h2">'.esc_html__( 'Add New', 'bskpdfmanager' ).'</a></h2>';
            $this->bsk_pdf_manager_show_pro_tip_box( self::$_pro_tips_for_category );
			echo '<form id="bsk_pdf_manager_categories_form_id" method="post" action="'.esc_url($category_base_page).'">';
						$_bsk_pdfm_OBJ_categories->views();
						$_bsk_pdfm_OBJ_categories->display();
			echo '</form>
				  </div>';
		}else if ( $categories_curr_view == 'addnew' || $categories_curr_view == 'edit'){
			$category_id = -1;
			if (isset($_GET['categoryid'])) {
                $temp_category_id = intval(sanitize_text_field($_GET['categoryid']));
				$category_id = $temp_category_id ? $temp_category_id : $category_id;
			}	
            $title = $category_id > 0 ? esc_html__( 'Edit Category', 'bskpdfmanager' ) : esc_html__( 'New Category', 'bskpdfmanager' );
            ?>
			<div class="wrap">
                <div id="icon-edit" class="icon32"><br/></div>
                <h2><?php echo $title . '<a href="'.esc_url($category_add_new_page).'" class="add-new-h2">'.esc_html__( 'Add New', 'bskpdfmanager' ).'</a>'; ?></h2>
                <?php $this->bsk_pdf_manager_show_pro_tip_box( self::$_pro_tips_for_category ); ?>
			    <form id="bsk_pdf_manager_categories_form_id" method="post" action="<?php echo esc_url($category_base_page); ?>">
					<?php self::$_bsk_pdfm_OBJ_category->bsk_pdf_manager_category_edit( $category_id ); ?>
			        <p style="margin-top:20px;"><input type="button" id="bsk_pdf_manager_category_save" class="button-primary" value="<?php echo esc_attr__( 'Save', 'bskpdfmanager' ); ?>" /></p>
			    </form>
			</div>
            <?php
		}
	}
	
    function bsk_pdf_manager_tags(){
        global $current_user;
		
		$tags_curr_view = 'list';
		if (isset($_REQUEST['view'])) {
            $temp_tags_curr_view = trim(sanitize_text_field($_GET['view']));
            $tags_curr_view = $temp_tags_curr_view ? $temp_tags_curr_view : $tags_curr_view;
		}
        
		$tag_base_page = admin_url( 'admin.php?page='.self::$_bsk_pdfm_pro_pages['tag'] );
		if( $tags_curr_view == 'list' ){
            $_bsk_pdfm_OBJ_tags = new BSKPDFM_Dashboard_Tags();

			//Fetch, prepare, sort, and filter our data...
			$_bsk_pdfm_OBJ_tags->prepare_items();
			
			$tag_add_new_page = add_query_arg( 'view', 'addnew', $tag_base_page );
	
			echo '<div class="wrap">
					<div id="icon-edit" class="icon32"><br/></div>
					<h2>'.esc_html__( 'BSK PDF Tags', 'bskpdfmanager' ).'<a href="'.esc_url($tag_add_new_page).'" class="add-new-h2">'.esc_html__( 'Add New', 'bskpdfmanager' ).'</a></h2>';
            $this->bsk_pdf_manager_show_pro_tip_box( self::$_pro_tips_for_tag );
			echo '	<form id="bsk_pdf_manager_tags_form_id" method="post" action="'.esc_url($tag_base_page).'">';
						$_bsk_pdfm_OBJ_tags->views();
						$_bsk_pdfm_OBJ_tags->display();
			echo '  </form>
				  </div>';
		}else if ( $tags_curr_view == 'addnew' || $tags_curr_view == 'edit'){
			$tag_id = -1;
			if (isset($_GET['tagid'])) {
				$temp_tag_id = intval(sanitize_text_field($_GET['tagid']));
				$tag_id = $temp_tag_id ? $temp_tag_id : $tag_id;
			}	
			echo '<div class="wrap">
					<div id="icon-edit" class="icon32"><br/></div>
					<h2>'.esc_html__( 'BSK PDF tag', 'bskpdfmanager' ).'</h2>';
            $this->bsk_pdf_manager_show_pro_tip_box( self::$_pro_tips_for_tag );
			echo '	<form id="bsk_pdf_manager_tags_form_id" method="post" action="'.esc_url($tag_base_page).'">';
					self::$_bsk_pdfm_OBJ_tag->bsk_pdf_manager_tag_edit( $tag_id );
			echo '<p style="margin-top:20px;">
                       <input type="button" id="bsk_pdf_manager_tag_save" class="button-primary" value="'.esc_attr__( 'Save', 'bskpdfmanager' ).'" />
                       <span id="bsk_pdfm_tag_save_ajax_loader_ID" style="margin-left:10px; display: none;">
                           <img src="'.esc_url(BSKPDFManager::$_ajax_loader_img_url).'" />
                       </span>
                  </p>';
            echo "\n";
			echo '	</form>
				  </div>';
		}
    }
    
	function bsk_pdf_manager_pdfs_list(){
        
        global $current_user;

		$row_action = '';
        if( isset( $_GET['action'] ) ){
            $row_action = sanitize_text_field( $_GET['action'] );
        }
        
        $selected_PDFs = false;
        $selected_PDFs_to_hidden = '';
        if (isset($_POST['bsk-pdf-manager-pdfs'])) {
            $selected_PDFs = array();
            foreach ($_POST['bsk-pdf-manager-pdfs'] as $selected_pdf_id) {
                $selected_PDFs[] = intval(sanitize_text_field($selected_pdf_id));
            }
        }
        if ($selected_PDFs && is_array($selected_PDFs) && count($selected_PDFs) > 0) {
            $selected_PDFs_to_hidden = implode(',', $selected_PDFs);
        }
        $url_cat_parameter = isset($_REQUEST['cat']) ? '&cat='.$_REQUEST['cat'] : '';
        $action_url = admin_url( 'admin.php?page='.self::$_bsk_pdfm_pro_pages['base'].$url_cat_parameter );
        if( isset($_REQUEST['pdf_status']) ){
            $pdf_status = sanitize_text_field($_REQUEST['pdf_status']);
            $action_url = add_query_arg( 'pdf_status', $pdf_status, $action_url );
        }

        $bulk_action = isset($_POST['action']) ? sanitize_text_field($_POST['action']) : '';
        if( isset($_POST['action2']) && ( $bulk_action == '' || $bulk_action == -1 ) ){
            $bulk_action = sanitize_text_field($_POST['action2']);
        }
        //print_r( $bulk_action );exit;
            
        if ( $bulk_action != '' ) {
            switch ( $bulk_action ) {
                case 'changecat' :
                    self::$_bsk_pdfm_OBJ_pdf_list_action->bulk_action_changecat( $selected_PDFs, $selected_PDFs_to_hidden, $action_url );
                    return;
                break;
                case 'changetag' :
                    self::$_bsk_pdfm_OBJ_pdf_list_action->bulk_action_changetag( $selected_PDFs, $selected_PDFs_to_hidden, $action_url );
                    return;
                break;
                case 'changedate' :
                    self::$_bsk_pdfm_OBJ_pdf_list_action->bulk_action_changedate( $selected_PDFs, $selected_PDFs_to_hidden, $action_url );
                    return;
                break;
                case 'changedate' :
                    self::$_bsk_pdfm_OBJ_pdf_list_action->bulk_action_changedate( $selected_PDFs, $selected_PDFs_to_hidden, $action_url );
                    return;
                break;
                case 'changetitle' :
                    self::$_bsk_pdfm_OBJ_pdf_list_action->bulk_action_changetitle( $selected_PDFs, $selected_PDFs_to_hidden, $action_url );
                    return;
                break;
                case 'generatethumb' :
                    self::$_bsk_pdfm_OBJ_pdf_list_action->bulk_action_generatethumb( $selected_PDFs, $selected_PDFs_to_hidden, $action_url );
                    return;
                break;
                case 'bulkdelete' :
                    self::$_bsk_pdfm_OBJ_pdf_list_action->bulk_action_bulkdelete_row_action_delete( $selected_PDFs, $selected_PDFs_to_hidden, $action_url );
                    return;
                break;
            }
        }
                                            
        //for row_action of delete, here need to show confirm form
        //for other row_action, process in pdf.php
        if( $row_action == 'delete' ) {
            if( isset( $_GET['pdfid'] ) ){
                $selected_PDFs = array();
                $selected_PDFs[] = intval( sanitize_text_field( $_GET['pdfid'] ) );
                $selected_PDFs_to_hidden = $selected_PDFs[0];
            }
            self::$_bsk_pdfm_OBJ_pdf_list_action->bulk_action_bulkdelete_row_action_delete( $selected_PDFs, $selected_PDFs_to_hidden, $action_url );
            return;
        }
            
        //come to show PDFs list
        $bsk_pdfm_pdfs_list_page_url = admin_url( 'admin.php?page='.self::$_bsk_pdfm_pro_pages['base'] );
        $current_category_id = 0;
        if( isset($_REQUEST['cat']) ){
            $current_category_id = sanitize_text_field($_REQUEST['cat']);
            $current_category_id = intval( $current_category_id );
        }
        
        $add_new_page = admin_url( 'admin.php?page='.self::$_bsk_pdfm_pro_pages['edit'] );
        if( $current_category_id ){
            $add_new_page = add_query_arg( 'cat', $current_category_id, $add_new_page );
            $bsk_pdfm_pdfs_list_page_url = add_query_arg( 'cat', $current_category_id, $bsk_pdfm_pdfs_list_page_url );
        }
        
        if( isset($_REQUEST['pdf_status']) ){
            $pdf_status = sanitize_text_field($_REQUEST['pdf_status']);
            $bsk_pdfm_pdfs_list_page_url = add_query_arg( 'pdf_status', $pdf_status, $bsk_pdfm_pdfs_list_page_url );
        }
        
        $_bsk_pdfm_OBJ_pdfs = new BSKPDFM_Dashboard_PDFs();
        //Fetch, prepare, sort, and filter our data...
        $_bsk_pdfm_OBJ_pdfs->prepare_items();
        ?>
        <div class="wrap">
        <div id="icon-edit" class="icon32"><br/></div>
        <h2><?php esc_html_e( 'BSK PDF Documents', 'bskpdfmanager' ); ?><a href="<?php echo esc_url($add_new_page); ?>" class="add-new-h2"><?php esc_html_e( 'Add New', 'bskpdfmanager' ); ?></a></h2>
        <?php $this->bsk_pdf_manager_show_pro_tip_box( self::$_pro_tips_for_pdf ); ?>
        <form id="bsk-pdf-manager-pdfs-form-id" method="post" action="<?php echo esc_url($bsk_pdfm_pdfs_list_page_url); ?>">
        <?php
        $_bsk_pdfm_OBJ_pdfs->search_box( esc_attr__( 'search', 'bskpdfmanager' ), 'bsk-pdf-manager-pdfs' );
        $_bsk_pdfm_OBJ_pdfs->views();
        $_bsk_pdfm_OBJ_pdfs->display();
        
        if( $current_category_id ){
        ?>
            <p><?php esc_html_e( 'Shortocde to show this category in list', 'bskpdfmanager' ); ?>: <span class="bsk-pdf-documentation-attr">[bsk-pdfm-category-ul id="<?php echo esc_attr($current_category_id); ?>"]</span>, <a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-pdfs-by-category-in-list/" target="_blank"><?php esc_html_e( 'click here for more shortcode attributes', 'bskpdfmanager' ); ?></a></p>
            <p><?php esc_html_e( 'Shortocde to show this category in columns', 'bskpdfmanager' ); ?>: <span class="bsk-pdf-documentation-attr">[bsk-pdfm-category-columns id="<?php echo esc_attr($current_category_id); ?>" columns="2"]</span>, <a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-pdfs-by-category-in-columns/" target="_blank"><?php esc_html_e( 'click here for more shortcode attributes', 'bskpdfmanager' ); ?></a></p>
            <p><?php esc_html_e( 'Shortocde to show this category in dropdown', 'bskpdfmanager' ); ?>: <span class="bsk-pdf-documentation-attr">[bsk-pdfm-category-dropdown id="<?php echo esc_attr($current_category_id); ?>" target="_blank"]</span>, <a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-pdfs-by-category-in-dropdown/" target="_blank"><?php esc_html_e( 'click here for more shortcode attributes', 'bskpdfmanager' ); ?></a> </p>
        <?php
        }else{
        ?>
            <p><?php esc_html_e( 'Shortocde to show PDFs / Documents in list', 'bskpdfmanager' ); ?>: <span class="bsk-pdf-documentation-attr">[bsk-pdfm-pdfs-ul id="1,2,3,4"]</span>, <a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-specific-pdfs-in-list/" target="_blank"><?php esc_html_e( 'click here for more shortcode attributes', 'bskpdfmanager' ); ?></a></p>
            <p><?php esc_html_e( 'Shortocde to show PDFs / Documents in columns', 'bskpdfmanager' ); ?>: <span class="bsk-pdf-documentation-attr">[bsk-pdfm-pdfs-columns id="1,2,3,4" columns="2" target="_blank"]</span>, <a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-specific-pdfs-in-columns/" target="_blank"><?php esc_html_e( 'click here for more shortcode attributes', 'bskpdfmanager' ); ?></a></p>
            <p><?php esc_html_e( 'Shortocde to show PDFs / Documents in dropdown', 'bskpdfmanager' ); ?>: <span class="bsk-pdf-documentation-attr">[bsk-pdfm-pdfs-dropdown id="1,2,3,4"  target="_blank"]</span>, <a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-specific-pdfs-in-dropdown/" target="_blank"><?php esc_html_e( 'click here for more shortcode attributes', 'bskpdfmanager' ); ?></a></p>
        <?php
        }
        $ajax_nonce = wp_create_nonce( 'bsk_pdf_manager_pdfs_page_ajax-oper-nonce' );
        ?>
        <input type="hidden" id="bsk_pdf_manager_pdfs_page_ajax_nonce_ID" value="<?php echo $ajax_nonce; ?>" />
        </form>
        </div>
        <?php
	}

    function bsk_pdfm_manage_notifications() {
        global $current_user;
		
		$notifications_curr_view = 'list';
		if( isset( $_GET['view'] ) && sanitize_text_field( $_GET['view'] ) ) {
			$notifications_curr_view = trim( sanitize_text_field( $_GET['view'] ) );
		}
        
		if( isset( $_POST['view'] ) && sanitize_text_field( $_POST['view'] ) ) {
			$notifications_curr_view = trim( sanitize_text_field( $_POST['view'] ) );
		}
		
		$notification_base_page = admin_url( 'admin.php?page='.self::$_bsk_pdfm_pro_pages['notification'] );
        $notification_add_new_page = add_query_arg( 'view', 'addnew', $notification_base_page );

		if( $notifications_curr_view == 'list' ){

            add_action( 'admin_notices', array( $this, 'bsk_pdfm_manage_notification_error_notice' ) );

            $_bsk_pdfm_OBJ_notifications = new BSKPDFM_Dashboard_notifications();

			//Fetch, prepare, sort, and filter our data...
			$_bsk_pdfm_OBJ_notifications->prepare_items();
			?>
			<div class="wrap">
                <div id="icon-edit" class="icon32"><br/></div>
                <h2><?php esc_html_e( 'BSK PDF Notifications', 'bskpdfmanager' ); ?><a href="<?php echo esc_url( $notification_add_new_page ); ?>" class="add-new-h2"><?php esc_html_e( 'Add New', 'bskpdfmanager' ); ?></a></h2>
                <?php $this->bsk_pdf_manager_show_pro_tip_box( self::$_pro_tips_for_notifications ); ?>
                <form id="bsk_pdf_manager_notifications_form_id" method="post" action="<?php echo esc_attr( $notification_base_page ); ?>">
                    <?php
                    $_bsk_pdfm_OBJ_notifications->views();
                    $_bsk_pdfm_OBJ_notifications->display();
                    $ajax_nonce = wp_create_nonce( 'bsk_pdfm_notifications_list_ajax_oper_nonce' );
                    ?>
                    <input type="hidden" id="bsk_pdfm_notifications_list_ajax_oper_nonce_ID" value="<?php echo $ajax_nonce; ?>" />
                </form>
                </div>
            <?php
		}else if ( $notifications_curr_view == 'addnew' || $notifications_curr_view == 'edit' ){
			$notification_id = -1;
			if( isset( $_GET['notificationid'] ) && sanitize_text_field( $_GET['notificationid'] ) ){
				$notification_id = trim( sanitize_text_field( $_GET['notificationid'] ) );
				$notification_id = intval( $notification_id );
			}	
            $title = $notification_id > 0 ? esc_html__( 'Edit Notification', 'bskpdfmanager' ) : esc_html__( 'New Notification', 'bskpdfmanager' );
            ?>
			<div class="wrap">
                <div id="icon-edit" class="icon32"><br/></div>
                <h2><?php echo $title; ?><a href="<?php echo esc_url( $notification_add_new_page ); ?>" class="add-new-h2"><?php echo esc_html__( 'Add New', 'bskpdfmanager' ); ?></a></h2>
                <?php
                $this->bsk_pdf_manager_show_pro_tip_box( self::$_pro_tips_for_notifications );
                $save_button_disabled = ' disabled';
                ?>
                <form id="bsk_pdf_manager_notification_form_id" method="post" action="<?php echo esc_url( $notification_base_page ); ?>">
                    <?php self::$_bsk_pdfm_OBJ_notification->bsk_pdf_manager_notification_edit( $notification_id ); ?>
                    <p style="margin-top:20px;">
                        <input type="button" id="bsk_pdfm_notifiy_cancel_btn_ID" class="button-primary" value="<?php esc_attr_e( 'Cancel', 'bskpdfmanager' ); ?>" />
                        <input type="button" id="bsk_pdfm_notifiy_save_btn_ID" class="button-primary" value="<?php esc_attr_e( 'Save', 'bskpdfmanager' ); ?>" style="margin-left: 20px;"<?php echo $save_button_disabled; ?> />
                    </p>
			    </form>
			</div>
            <?php
		}
    }

    function bsk_pdfm_manage_notification_error_notice() {
        if ( ! isset( $_GET['error'] ) ) {
            return;
        }
        $error_id = intval( sanitize_text_field( $_GET['error'] ) );
        if ( $error_id < 1 ) {
            return;
        }
        $class = 'notice notice-error';
        $message = '';
        switch ( $error_id ) {
            case 1:
                $message = esc_html__( 'Save notification failed, please try again later.', 'bskpdfmanager' );
            break;
        }
    
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
    }
	
	function bsk_pdf_manager_settings_support(){
		global $current_user;
        ?>
        <div class="wrap" id="bsk_pdfm_setings_wrap_ID">
        	<div id="icon-edit" class="icon32"><br/></div>
			<h2><?php esc_html_e( 'BSK PDF Settings & Support', 'bskpdfmanager' ); ?></h2>
        <?php
            $this->bsk_pdf_manager_show_pro_tip_box( self::$_pro_tips_for_settings );
            self::$_bsk_pdfm_OBJ_settings->show_settings(); 
        ?>
        </div>
        <?php
	}
	
    function bsk_pdf_manager_migrage() {
        ?>
        <div class="wrap">
            <div id="icon-edit" class="icon32"><br/></div>
            <h2><?php esc_html_e( 'Migrate', 'bskpdfmanager' ); ?></h2>
		    <form id="bsk_pdfm_migrate_form_ID" method="post" enctype="multipart/form-data">
                <?php self::$_bsk_pdfm_OBJ_migrate->bsk_pdfm_migrate_inteface(); ?>
		    </form>
		</div>
        <?php
    }

	function bsk_pdf_manager_pdfs_add_by_ftp_interface(){
		global $current_user;
		
		echo '<div class="wrap">
				<div id="icon-edit" class="icon32"><br/></div>
				<h2>'.esc_html__( 'Bulk Add by FTP', 'bskpdfmanager' ).'</h2>';
        $this->bsk_pdf_manager_show_pro_tip_box( self::$_pro_tips_for_add_by_ftp );
		echo '	<form id="bsk-pdf-manager-add-by-ftp-form-id" method="post" enctype="multipart/form-data">';
		echo '	<input type="hidden" name="page" value="'.esc_attr(self::$_bsk_pdfm_pro_pages['add_by_ftp']).'" />';
		
		self::$_bsk_pdfm_OBJ_ftp->bsk_pdf_manager_pdfs_add_by_ftp();
		
		echo '	</form>';
		
		echo '</div>';
	}
    
    function bsk_pdf_manager_show_pro_tip_box( $tips_array ){
        $tips = implode( ', ', $tips_array );
		$str = 
        '<div class="bsk-pdfm-tips-box">
			<p><b>Tip: </b> The following features only supported in <a href="'.esc_url(BSKPDFManager::$url_to_upgrade).'" target="_blank">Pro version</a>.</p>
            <p><span class="bsk-pdfm-tips-box-tip">'.$tips.'</span></p>
		 </div>';
		
		echo $str;
	}
    
    function bsk_pdf_manager_pdfs_add_by_media_library_interface(){
		global $current_user;
		
		echo '<div class="wrap">
				<div id="icon-edit" class="icon32"><br/></div>
				<h2>'.esc_html__( 'Bulk Add by Media Library', 'bskpdfmanager' ).'</h2>';
        $this->bsk_pdf_manager_show_pro_tip_box( self::$_pro_tips_for_add_by_media_library );
		echo '	<form id="bsk_pdfm_bulk_add_by_media_library_form_ID" method="post" enctype="multipart/form-data">';
		echo '	<input type="hidden" name="page" value="'.esc_attr(self::$_bsk_pdfm_pro_pages['add_by_media_library']).'" />';
		
		self::$_bsk_pdfm_OBJ_media_library->bsk_pdf_manager_pdfs_add_by_media_library();
		
		echo '	</form>';
		
		echo '</div>';
	}
    
    function bsk_pdfm_pages_pdfs_add_screen_option_fun(){
        $option = 'per_page';
        $args = array(
            'label' => __( 'Number of documents per page:', 'bskpdfmanager' ),
            'default' => 20,
            'option' => BSKPDFM_Dashboard_PDFs::$_screen_opt_page,
        );

        add_screen_option( $option, $args );
    }

    function bsk_pdfm_pages_categories_add_screen_option_fun() {
        $option = 'per_page';
        $args = array(
            'label' => __( 'Number of categories per page:', 'bskpdfmanager' ),
            'default' => 20,
            'option' => BSKPDFM_Dashboard_Categories::$_screen_opt_page,
        );

        add_screen_option( $option, $args );
    }
    
    function bsk_pdfm_add_other_screen_options_fun( $status, $screen ) {
        
        $return = $status;
        
        if ( $screen->base == $this->_bsk_pdfm_pdfs_menu_hook ) {  
            
            $user_ID = get_current_user_id();
            $saved_columns = get_user_meta( $user_ID, BSKPDFM_Dashboard_PDFs::$_screen_opt_columns, true );
            if( !is_array( $saved_columns ) ){
                //has never saved then default to all checked
                $saved_columns = array_keys( BSKPDFM_Dashboard_PDFs::$_screen_available_columns );
            }
            $return .= '
            <fieldset>
                <legend>Columns</legend>
                <div class="metabox-prefs">';
            foreach( BSKPDFM_Dashboard_PDFs::$_screen_available_columns as $column_name => $column_label ){
                $checked = $saved_columns && is_array( $saved_columns ) && in_array( $column_name, $saved_columns ) ? 'checked ' : '';
                $return .= '
                    <label for="bsk_pdfm_pdfs_screen_columns_'.$column_name.'_ID">
                        <input type="checkbox" '.$checked.'value="'.$column_name.'" name="bsk_pdfm_pdfs_screen_columns[]" id="bsk_pdfm_pdfs_screen_columns_'.$column_name.'_ID" /> '.__( $column_label, 'bskpdfmanager' ).'
                    </label>';
            }
            $return .= '
                </div>
            </fieldset>
            <br class="clear">';
        }

        if ( $screen->base == $this->_bsk_pdfm_categories_menu_hook ) {  
            
            $user_ID = get_current_user_id();
            $saved_default_order_by = get_user_meta( $user_ID, BSKPDFM_Dashboard_Categories::$_screen_opt_default_order_by, true );
            if( ! $saved_default_order_by ){
                //has never saved then default to all checked
                $saved_default_order_by = 'title';
            }
            $return .= '
            <fieldset>
                <legend>Default order by</legend>
                <div class="metabox-prefs">';
            foreach( BSKPDFM_Dashboard_Categories::$_screen_opt_default_order_by_columns as $column_name => $column_label ){
                $checked = $saved_default_order_by == $column_name ? 'checked ' : '';
                $return .= '
                    <label for="bsk_pdfm_pdfs_default_order_by_'.$column_name.'_ID">
                        <input type="radio" '.$checked.'value="'.$column_name.'" name="bsk_pdfm_pdfs_default_order_by" id="bsk_pdfm_pdfs_default_order_by_'.$column_name.'_ID" /> '.__( $column_label, 'bskpdfmanager' ).'
                    </label>';
            }
            $return .= '
                </div>
            </fieldset>
            <br class="clear">';
        }
        
        return $return;
    }
    
    function bsk_pdfm_save_screen_options_fun( $status, $option, $value ){
        if ( BSKPDFM_Dashboard_PDFs::$_screen_opt_page == $option ){
            
            $user_ID = get_current_user_id();
            $columns = array();
            if( isset( $_POST[BSKPDFM_Dashboard_PDFs::$_screen_opt_columns] ) && 
                is_array( $_POST[BSKPDFM_Dashboard_PDFs::$_screen_opt_columns] ) && 
                count( $_POST[BSKPDFM_Dashboard_PDFs::$_screen_opt_columns] ) > 0 ){
                
                foreach( $_POST[BSKPDFM_Dashboard_PDFs::$_screen_opt_columns] as $text_value ){
                    $columns[] = sanitize_text_field( $text_value );
                }                
            }
            update_user_meta( $user_ID, BSKPDFM_Dashboard_PDFs::$_screen_opt_columns, $columns );
            
            return $value;
        } 

        if ( BSKPDFM_Dashboard_Categories::$_screen_opt_page == $option ){
            
            $user_ID = get_current_user_id();
            $order_by = sanitize_text_field( $_POST['bsk_pdfm_pdfs_default_order_by'] );
            update_user_meta( $user_ID, BSKPDFM_Dashboard_Categories::$_screen_opt_default_order_by, $order_by );

            return $value;
        } 

        return $status;
    }

    function bsk_pdfm_remove_the_event_calendar_js_fun() {
        if ( ! isset( $_GET['page'] ) ) {
            return;
        }
        $_page = sanitize_text_field( $_GET['page'] );
        if ( ! $_page ) {
            return;
        }
        if ( in_array( $_page, self::$_bsk_pdfm_pro_pages ) ) {
            wp_deregister_script( 'tribe-events-php-date-formatter' );
            wp_deregister_script( 'tribe-events-jquery-resize' );
            wp_deregister_script( 'tribe-events-bootstrap-datepicker' );
        }
    }

    function bsk_pdfm_dropdown_warning_fun() {

        $_dropdown_shortcodes_pages = get_option( BSKPDFManager::$_dropdown_shortcodes_pages_option, array() );
        if ( count( $_dropdown_shortcodes_pages ) < 1 ) {
            return;
        }

        $page = isset( $_GET['page'] ) ? $_GET['page'] : '';
        if ( $page == '' || ! in_array( $page, self::$_bsk_pdfm_pro_pages ) ) {
            return;
        }
        ?>
        <div class="notice notice-warning is-dismissible">
            <p>You are using dropdown layout to display PDFs / documents on the below page(s): </p>
            <ul>
            <?php
            foreach( $_dropdown_shortcodes_pages as $page_ID ) {
            ?>
            <a href="<?php echo get_permalink( $page_ID ); ?>"><?php echo get_permalink( $page_ID ); ?></a>
            <?php
            }
            ?>
            </ul>
            <p>The PDF/document is designed to open in a new tab/window when the dropdown changes. But some browsers block new tabs/windows by default. To avoid this trouble, please update to our <a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/how-to-upgrade-to-pro-version/" target="_blank">Pro version</a>. The pro version doesn't have this problem because it uses a different method.
        </div>
        <?php
    }
}
