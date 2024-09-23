<?php

class BSKPDFM_Dashboard_Settings_Capabilities {
	
	private static $_bsk_pdf_settings_page_url = '';
	   
	public function __construct() {
		global $wpdb;
		
		self::$_bsk_pdf_settings_page_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['setting'] );
		
		add_action( 'wp_ajax_bsk_pdfm_cap_settings_get_custom_role_capabilities', array( $this, 'bsk_pdfm_cap_settings_get_custom_role_capabilities_fun' ) );
		add_action( 'wp_ajax_bsk_pdfm_cap_settings_role_special_capability', array( $this, 'bsk_pdfm_bsk_pdfm_cap_settings_role_special_capability_fun' ) );

		add_action( 'wp_ajax_bsk_pdfm_enable_available_categories_for_users', array( $this, 'bsk_pdfm_enable_available_categories_for_users_fun' ) );
		add_action( 'wp_ajax_bsk_pdfm_get_users_by_role', array( $this, 'bsk_pdfm_settings_capabilities_settings_get_users_option_by_role_fun' ) );
		add_action( 'wp_ajax_bsk_pdfm_get_user_available_cats_checkboxs', array( $this, 'bsk_pdfm_settings_capabilities_settings_get_user_available_cats_checkboxs_fun' ) );
	}
	
	
	function show_settings( $plugin_settings ){
		
		$capability_settings = array();
		if( $plugin_settings && is_array( $plugin_settings ) && count( $plugin_settings ) > 0 ){
			if( isset( $plugin_settings['capability_settings'] ) ) {
				$capability_settings = $plugin_settings['capability_settings'];
			}
		}

		$enable_editor_checked = '';
		$editor_disable_class = ' bsk-pdfm-td-disabled-capability';
		$editor_do_settings_chk_disabled = ' disabled';
		if ( isset( $capability_settings['editor'] ) && $capability_settings['editor'] ) {
			$enable_editor_checked = ' checked';
			$editor_disable_class = '';
			if ( current_user_can( 'administrator' ) ) {
				$editor_do_settings_chk_disabled = '';
			}
		}

		$enable_author_checked = '';
		$author_disable_class = ' bsk-pdfm-td-disabled-capability';
		if ( isset( $capability_settings['author'] ) && $capability_settings['author'] ) {
			$enable_author_checked = ' checked';
			$author_disable_class = '';
		}

		$enable_contributor_checked = '';
		$contributor_disable_class = ' bsk-pdfm-td-disabled-capability';
		if ( isset( $capability_settings['contributor'] ) && $capability_settings['contributor'] ) {
			$enable_contributor_checked = ' checked';
			$contributor_disable_class = '';
		}

		$editor_capabilities = get_role( 'editor' )->capabilities;
		$author_capabilities = get_role( 'author' )->capabilities;
		$contributor_capabilities = get_role( 'contributor' )->capabilities;
	?>
	<h3><?php esc_html_e( 'Backend Access Settings by Role', 'bskpdfmanager' ); ?></h3>
    <form action="<?php echo add_query_arg( 'target', 'capabilities', self::$_bsk_pdf_settings_page_url ); ?>" method="POST" id="bsk_pdfm_capabilities_settings_form_ID">
    <div class="bsk-pdfm-backend-access-settings-by-role">
		<p><?php esc_html_e( 'By default, only Administrator users can access all menu items and do everyting. Check the checkbox before the Role name to enable all users in the role to access backend. The menu items that users can access are determined by the capabilities they have.', 'bskpdfmanager' ); ?></p>
		<p><?php esc_html_e( 'Requried capability in the following table is the WordPress capability value to be checked.', 'bskpdfmanager' ); ?></p>
		<div id="bsk_pdfm_backend_access_setting_by_role_section_ID" class="bsk-pdfm-backend-access-setting-by-role-seciton" style="background: #FFFFFF;">
			<p class="bsk-pdfm-error" style="display: none;" id="bsk_pdfm_capabilities_setting_error_ID"></p>
			<p>
				<table class="wp-list-table widefat fixed">
					<thead>
						<th style="width: 10%;">Menu Items</th>
						<th style="width: 15%;">What can do</th>
						<th style="width: 15%;">Requried capability</th>
						<th style="width: 10%;">Administrator</th>
						<th style="width: 10%;">
							<?php if( current_user_can( 'administrator' ) ) { ?>
							<input type="checkbox" class="bsk-pdfm-cap-settings-enable-checkbox"<?php echo $enable_editor_checked; ?> data-role="editor" disabled >
							<?php } ?>
							<label for="bsk_pdfm_cap_settings_enable_editor_ID"> Editor</label>
							<span class="bsk-pdfm-cap-settings-enable-ajax-loader" style="display: none;"><img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" /></span>
						</th>
						<th style="width: 10%;">
							<input type="checkbox" class="bsk-pdfm-cap-settings-enable-checkbox"<?php echo $enable_author_checked; ?> data-role="author" disabled >
							<label for="bsk_pdfm_cap_settings_enable_author_ID"> Author</label>
							<span class="bsk-pdfm-cap-settings-enable-ajax-loader" style="display: none;"><img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" /></span>
						</th>
						<th style="width: 10%;">
							<input type="checkbox" class="bsk-pdfm-cap-settings-enable-checkbox"<?php echo $enable_contributor_checked; ?> data-role="contributor" disabled >
							<label for="bsk_pdfm_cap_settings_enable_contributor_ID"> Contributor</label>
							<span class="bsk-pdfm-cap-settings-enable-ajax-loader" style="display: none;"><img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" /></span>
						</th>
						<th style="width: 20%; padding-right: 20px;">
							<select name="bsk_pdfm_cap_settings_custom_role_select" id="bsk_pdfm_cap_settings_custom_role_select_ID" style="max-width: 320px;" disabled >
								<option value="">Enable custom role of: </option>
								<?php
									$editable_roles = array_reverse( get_editable_roles() );
									foreach ( $editable_roles as $role => $details ) {
										if ( in_array( $role, array( 'administrator', 'editor', 'author', 'contributor', 'subscriber' ) ) ) {
											continue;
										}
										$name = translate_user_role( $details['name'] );
										echo "<option value='" . esc_attr( $role ) . "'>$name</option>";
									}
								?>
							</select>
							<span class="bsk-pdfm-cap-settings-enable-ajax-loader" style="display: none;"><img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" /></span>
						</th>
					</thead>
					<tbody id="bsk_pdfm_capabilities_setting_table_body_ID">
						<tr class="bsk-pdfm-th-background">
							<th rowspan="7"><?php esc_html_e( 'PDF / Documents', 'bskpdfmanager' ); ?></th>
							<td>View PDFs / Documents</td>
							<td>bsk_pdfm_view</td>
							<td class="bsk-pdfm-td-admin-capability">Y</td>
							<td class="bsk-pdfm-td-editor-capability<?php echo $editor_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-author-capability<?php echo $author_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-contributor-capability<?php echo $contributor_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-custom-role-capability bsk_pdfm_view" data-requried="bsk_pdfm_view">
								<input type="checkbox" class="bsk-pdfm-role-special-capability-chk" style="display: none;" data-role="" data-capability="bsk_pdfm_view" />
								<span class="bsk-pdfm-ajax-loader" style="display: none;"><img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" /></span>
							</td>
						</tr>
						<tr class="bsk-pdfm-th-background">
							<td>Edit their own</td>
							<td>bsk_pdfm_edit</td>
							<td class="bsk-pdfm-td-admin-capability">Y</td>
							<td class="bsk-pdfm-td-editor-capability<?php echo $editor_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-author-capability<?php echo $author_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-contributor-capability<?php echo $contributor_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-custom-role-capability bsk_pdfm_edit" data-requried="bsk_pdfm_edit">
								<input type="checkbox" class="bsk-pdfm-role-special-capability-chk" style="display: none;" data-role="" data-capability="bsk_pdfm_edit" />
								<span class="bsk-pdfm-ajax-loader" style="display: none;"><img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" /></span>
							</td>
						</tr>
						<tr class="bsk-pdfm-th-background">
							<td scope="row">Edit not pending</td>
							<td scope="row">bsk_pdfm_edit_published</td>
							<td class="bsk-pdfm-td-admin-capability">Y</td>
							<td class="bsk-pdfm-td-editor-capability<?php echo $editor_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-author-capability<?php echo $author_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-contributor-capability<?php echo $contributor_disable_class; ?>"></td>
							<td class="bsk-pdfm-td-custom-role-capability bsk_pdfm_edit_published" data-requried="bsk_pdfm_edit_published">
								<input type="checkbox" class="bsk-pdfm-role-special-capability-chk" style="display: none;" data-role="" data-capability="bsk_pdfm_edit_published" />
								<span class="bsk-pdfm-ajax-loader" style="display: none;"><img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" /></span>
							</td>
						</tr>
						<tr class="bsk-pdfm-th-background">
							<td scope="row">Edit of other users</td>
							<td scope="row">bsk_pdfm_edit_others</td>
							<td class="bsk-pdfm-td-admin-capability">Y</td>
							<td class="bsk-pdfm-td-editor-capability<?php echo $editor_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-author-capability<?php echo $author_disable_class; ?>"></td>
							<td class="bsk-pdfm-td-contributor-capability<?php echo $contributor_disable_class; ?>"></td>
							<td class="bsk-pdfm-td-custom-role-capability bsk_pdfm_edit_others" data-requried="bsk_pdfm_edit_others">
								<input type="checkbox" class="bsk-pdfm-role-special-capability-chk" style="display: none;" data-role="" data-capability="bsk_pdfm_edit_others" />
								<span class="bsk-pdfm-ajax-loader" style="display: none;"><img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" /></span>
							</td>
						</tr>
						<tr class="bsk-pdfm-th-background">
							<td scope="row">Delete their own</td>
							<td scope="row">bsk_pdfm_delete</td>
							<td class="bsk-pdfm-td-admin-capability">Y</td>
							<td class="bsk-pdfm-td-editor-capability<?php echo $editor_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-author-capability<?php echo $author_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-contributor-capability<?php echo $contributor_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-custom-role-capability bsk_pdfm_delete" data-requried="bsk_pdfm_delete">
								<input type="checkbox" class="bsk-pdfm-role-special-capability-chk" style="display: none;" data-role="" data-capability="bsk_pdfm_delete" />
								<span class="bsk-pdfm-ajax-loader" style="display: none;"><img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" /></span>
							</td>
						</tr>
						<tr class="bsk-pdfm-th-background">
							<td scope="row">Delete not pending</td>
							<td scope="row">bsk_pdfm_delete_published</td>
							<td class="bsk-pdfm-td-admin-capability">Y</td>
							<td class="bsk-pdfm-td-editor-capability<?php echo $editor_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-author-capability<?php echo $author_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-contributor-capability<?php echo $contributor_disable_class; ?>"></td>
							<td class="bsk-pdfm-td-custom-role-capability bsk_pdfm_delete_published" data-requried="bsk_pdfm_delete_published">
								<input type="checkbox" class="bsk-pdfm-role-special-capability-chk" style="display: none;" data-role="" data-capability="bsk_pdfm_delete_published" />
								<span class="bsk-pdfm-ajax-loader" style="display: none;"><img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" /></span>
							</td>
						</tr>
						<tr class="bsk-pdfm-th-background">
							<td scope="row">Delete of other users</td>
							<td scope="row">bsk_pdfm_delete_others</td>
							<td class="bsk-pdfm-td-admin-capability">Y</td>
							<td class="bsk-pdfm-td-editor-capability<?php echo $editor_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-author-capability<?php echo $author_disable_class; ?>"></td>
							<td class="bsk-pdfm-td-contributor-capability<?php echo $contributor_disable_class; ?>"></td>
							<td class="bsk-pdfm-td-custom-role-capability bsk_pdfm_delete_others" data-requried="bsk_pdfm_delete_others">
								<input type="checkbox" class="bsk-pdfm-role-special-capability-chk" style="display: none;" data-role="" data-capability="bsk_pdfm_delete_others" />
								<span class="bsk-pdfm-ajax-loader" style="display: none;"><img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" /></span>
							</td>
						</tr>
						<tr>
							<th rowspan="3" scope="rowgroup"><?php esc_html_e( 'Add New', 'bskpdfmanager' ); ?></th>
							<td>Upload from computer</th>
							<td>bsk_pdfm_upload_from_computer</th>
							<td class="bsk-pdfm-td-admin-capability">Y</td>
							<td class="bsk-pdfm-td-editor-capability<?php echo $editor_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-author-capability<?php echo $author_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-contributor-capability<?php echo $contributor_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-custom-role-capability bsk_pdfm_upload_from_computer" data-requried="bsk_pdfm_upload_from_computer">
								<input type="checkbox" class="bsk-pdfm-role-special-capability-chk" style="display: none;" data-role="" data-capability="bsk_pdfm_upload_from_computer" />
								<span class="bsk-pdfm-ajax-loader" style="display: none;"><img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" /></span>
							</td>
						</tr>
						<tr>
							<td>Upload from Media Library</td>
							<td>bsk_pdfm_upload_from_media_lib</td>
							<td class="bsk-pdfm-td-admin-capability">Y</td>
							<td class="bsk-pdfm-td-editor-capability<?php echo $editor_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-author-capability<?php echo $author_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-contributor-capability<?php echo $contributor_disable_class; ?>"></td>
							<td class="bsk-pdfm-td-custom-role-capability bsk_pdfm_upload_from_media_lib" data-requried="bsk_pdfm_upload_from_media_lib">
								<input type="checkbox" class="bsk-pdfm-role-special-capability-chk" style="display: none;" data-role="" data-capability="bsk_pdfm_upload_from_media_lib" />
								<span class="bsk-pdfm-ajax-loader" style="display: none;"><img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" /></span>
							</td>
						</tr>
						<tr>
							<td>Publish</td>
							<td>bsk_pdfm_publish</td>
							<td class="bsk-pdfm-td-admin-capability">Y</td>
							<td class="bsk-pdfm-td-editor-capability<?php echo $editor_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-author-capability<?php echo $author_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-contributor-capability<?php echo $contributor_disable_class; ?>"></td>
							<td class="bsk-pdfm-td-custom-role-capability bsk_pdfm_publish" data-requried="bsk_pdfm_publish">
								<input type="checkbox" class="bsk-pdfm-role-special-capability-chk" style="display: none;" data-role="" data-capability="bsk_pdfm_publish" />
								<span class="bsk-pdfm-ajax-loader" style="display: none;"><img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" /></span>
							</td>
						</tr>
						<tr class="bsk-pdfm-th-background">
							<th rowspan="2" scope="rowgroup"><?php esc_html_e( 'Add by FTP', 'bskpdfmanager' ); ?></th>
							<td>Add New</td>
							<td>bsk_pdfm_add_by_ftp</td>
							<td class="bsk-pdfm-td-admin-capability">Y</td>
							<td class="bsk-pdfm-td-editor-capability<?php echo $editor_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-author-capability<?php echo $author_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-contributor-capability<?php echo $contributor_disable_class; ?>"></td>
							<td class="bsk-pdfm-td-custom-role-capability bsk_pdfm_add_by_ftp" data-requried="bsk_pdfm_add_by_ftp">
								<input type="checkbox" class="bsk-pdfm-role-special-capability-chk" style="display: none;" data-role="" data-capability="bsk_pdfm_add_by_ftp" />
								<span class="bsk-pdfm-ajax-loader" style="display: none;"><img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" /></span>
							</td>
						</tr>
						<tr class="bsk-pdfm-th-background">
							<td>Publish</td>
							<td>bsk_pdfm_publish</td>
							<td class="bsk-pdfm-td-admin-capability">Y</td>
							<td class="bsk-pdfm-td-editor-capability<?php echo $editor_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-author-capability<?php echo $author_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-contributor-capability<?php echo $contributor_disable_class; ?>"></td>
							<td class="bsk-pdfm-td-custom-role-capability bsk_pdfm_publish" data-requried="bsk_pdfm_publish">
								<input type="checkbox" class="bsk-pdfm-role-special-capability-chk" style="display: none;" data-role="" data-capability="bsk_pdfm_publish" />
								<span class="bsk-pdfm-ajax-loader" style="display: none;"><img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" /></span>
							</td>
						</tr>
						<tr>
							<th rowspan="2" scope="rowgroup"><?php esc_html_e( 'Add by Media Library', 'bskpdfmanager' ); ?></th>
							<td>Add New</td>
							<td>bsk_pdfm_add_by_media_lib</td>
							<td class="bsk-pdfm-td-admin-capability">Y</td>
							<td class="bsk-pdfm-td-editor-capability<?php echo $editor_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-author-capability<?php echo $author_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-contributor-capability<?php echo $contributor_disable_class; ?>"></td>
							<td class="bsk-pdfm-td-custom-role-capability bsk_pdfm_add_by_media_lib" data-requried="bsk_pdfm_add_by_media_lib">
								<input type="checkbox" class="bsk-pdfm-role-special-capability-chk" style="display: none;" data-role="" data-capability="bsk_pdfm_add_by_media_lib" />
								<span class="bsk-pdfm-ajax-loader" style="display: none;"><img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" /></span>
							</td>
						</tr>
						<tr>
							<td>Publish</td>
							<td>bsk_pdfm_publish</td>
							<td class="bsk-pdfm-td-admin-capability">Y</td>
							<td class="bsk-pdfm-td-editor-capability<?php echo $editor_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-author-capability<?php echo $author_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-contributor-capability<?php echo $contributor_disable_class; ?>"></td>
							<td class="bsk-pdfm-td-custom-role-capability bsk_pdfm_publish" data-requried="bsk_pdfm_publish">
								<input type="checkbox" class="bsk-pdfm-role-special-capability-chk" style="display: none;" data-role="" data-capability="bsk_pdfm_publish" />
								<span class="bsk-pdfm-ajax-loader" style="display: none;"><img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" /></span>
							</td>
						</tr>
						<tr class="bsk-pdfm-th-background">
							<td><?php esc_html_e( 'Categories', 'bskpdfmanager' ); ?></td>
							<td>Manage Categories</td>
							<td>bsk_pdfm_manage_categories</td>
							<td class="bsk-pdfm-td-admin-capability">Y</td>
							<td class="bsk-pdfm-td-editor-capability<?php echo $editor_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-author-capability<?php echo $author_disable_class; ?>"></td>
							<td class="bsk-pdfm-td-contributor-capability<?php echo $contributor_disable_class; ?>"></td>
							<td class="bsk-pdfm-td-custom-role-capability bsk_pdfm_manage_categories" data-requried="bsk_pdfm_manage_categories">
								<input type="checkbox" class="bsk-pdfm-role-special-capability-chk" style="display: none;" data-role="" data-capability="bsk_pdfm_manage_categories" />
								<span class="bsk-pdfm-ajax-loader" style="display: none;"><img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" /></span>
							</td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Tags', 'bskpdfmanager' ); ?></td>
							<td>Manage Tags</td>
							<td>bsk_pdfm_manage_tags</td>
							<td class="bsk-pdfm-td-admin-capability">Y</td>
							<td class="bsk-pdfm-td-editor-capability<?php echo $editor_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-author-capability<?php echo $author_disable_class; ?>"></td>
							<td class="bsk-pdfm-td-contributor-capability<?php echo $contributor_disable_class; ?>"></td>
							<td class="bsk-pdfm-td-custom-role-capability bsk_pdfm_manage_tags" data-requried="bsk_pdfm_manage_tags">
								<input type="checkbox" class="bsk-pdfm-role-special-capability-chk" style="display: none;" data-role="" data-capability="bsk_pdfm_manage_tags" />
								<span class="bsk-pdfm-ajax-loader" style="display: none;"><img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" /></span>
							</td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'Notifications', 'bskpdfmanager' ); ?></td>
							<td>Manage Notifications</td>
							<td>bsk_pdfm_manage_notifications</td>
							<td class="bsk-pdfm-td-admin-capability">Y</td>
							<td class="bsk-pdfm-td-editor-capability<?php echo $editor_disable_class; ?>">Y</td>
							<td class="bsk-pdfm-td-author-capability<?php echo $author_disable_class; ?>"></td>
							<td class="bsk-pdfm-td-contributor-capability<?php echo $contributor_disable_class; ?>"></td>
							<td class="bsk-pdfm-td-custom-role-capability bsk_pdfm_manage_notifications" data-requried="bsk_pdfm_manage_notifications">
								<input type="checkbox" class="bsk-pdfm-role-special-capability-chk" style="display: none;" data-role="" data-capability="bsk_pdfm_manage_notifications" />
								<span class="bsk-pdfm-ajax-loader" style="display: none;"><img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" /></span>
							</td>
						</tr>
						<tr class="bsk-pdfm-th-background">
							<th><?php esc_html_e( 'Settings', 'bskpdfmanager' ); ?></th>
							<td>Do settings</td>
							<td>bsk_pdfm_do_settings</td>
							<td class="bsk-pdfm-td-admin-capability">Y</td>
							<td class="bsk-pdfm-td-editor-capability<?php echo $editor_disable_class; ?>">
								<?php
								$checked = '';
								if ( $editor_capabilities && is_array( $editor_capabilities ) && array_key_exists( 'bsk_pdfm_do_settings', $editor_capabilities ) ) {
									$checked = ' checked';
								}
								?>
								<input type="checkbox" class="bsk-pdfm-editor-do-settings-checkbox bsk-pdfm-role-special-capability-chk"<?php echo $editor_do_settings_chk_disabled . $checked; ?> data-role="editor" data-capability="bsk_pdfm_do_settings" disabled />
								<span class="bsk-pdfm-ajax-loader" style="display: none;"><img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" /></span>
							</td>
							<td class="bsk-pdfm-td-author-capability<?php echo $author_disable_class; ?>"></td>
							<td class="bsk-pdfm-td-contributor-capability<?php echo $contributor_disable_class; ?>"></td>
							<td class="bsk-pdfm-td-custom-role-capability bsk_pdfm_do_settings" data-requried="bsk_pdfm_do_settings"></td>
						</tr>
						<tr>
							<td><?php esc_html_e( 'License & Update', 'bskpdfmanager' ); ?></td>
							<td>Activate / Deactivate license</td>
							<td>bsk_pdfm_license_update</td>
							<td class="bsk-pdfm-td-author-capability<?php echo $author_disable_class; ?>"></td>
							<td class="bsk-pdfm-td-contributor-capability<?php echo $contributor_disable_class; ?>"></td>
							<td class="bsk-pdfm-td-custom-role-capability" data-requried="bsk_pdfm_license_update"></td>
						</tr>
					</tbody>
				</table>
			</p>
		</div>
    </div>
	<p style="margin-top: 20px;">&nbsp;</p>
	<?php
		$checkbox_checked = false;
		$checkbox_disabled = '';
		if ( isset( $capability_settings['users_available_categories'] ) && $capability_settings['users_available_categories'] ) {
			$checkbox_checked = true;
		}
	?>
	<div class="bsk-pdfm-backend-access-users-available-categories" id="bsk_pdfm_category_available_for_users_settings_ID">
		<h3><?php esc_html_e( 'User available categories', 'bskpdfmanager' ); ?></h3>
		<p><?php esc_html_e( 'By default, users in enabled role can add / manage documents / PDFs in all categories. Here provides a way to make them can only add / manage PDFs / documents in the assiged categories to them', 'bskpdfmanager' ); ?></p>
		<div class="bsk-pdfm-tips-box" style="text-align: left;">
			<p>This feature requires a <span style="font-weight: bold;">BUSINESS</span>( or above ) license for Pro version.</p>
		</div>
		<p>
			<input type="checkbox" id="bsk_pdfm_available_category_for_users_check_ID" value="YES" <?php echo $checkbox_checked ? 'checked' : ''; echo $checkbox_disabled; ?> disabled >
			<label for="bsk_pdfm_available_category_for_users_check_ID" style="width: 80%;">
				<span style="font-weight:bold;"> <?php esc_html_e( 'Enable', 'bskpdfmanager' ); ?></span>
				<span id="bsk_pdfm_available_category_for_users_enable_ajax_loader_ID" style="display: none;">
					<img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" />
				</span>
			</label>
		</p>
		<div id="bsk_pdfm_available_category_for_users_section_ID" style="display: block; background: #FFFFFF;">
			<p>
				<select name="bsk_pdfm_available_category_for_users_role_select" id="bsk_pdfm_available_category_for_users_role_select_ID" disabled >
					<option value=""><?php echo esc_html__( 'Please select a user role...', 'bskpdfmanager' ); ?></option>
					<?php
						$editable_roles = array_reverse( get_editable_roles() );
						foreach ( $editable_roles as $role => $details ) {
							if ( $role == 'administrator' ) {
								continue;
							}

							$name = translate_user_role( $details['name'] );
							echo "\n\t<option value='" . esc_attr( $role ) . "'>$name</option>";
						}
					?>
				</select>
				<input type="hidden" id="bsk_pdfm_available_category_for_users_loading_text_ID" value="<?php echo esc_attr__( 'Loading users in role: ', 'bskpdfmanager' ); ?>" />
				<input type="hidden" id="bsk_pdfm_available_category_for_users_opt_none_text_ID" value="<?php echo esc_attr__( 'Please select a role first', 'bskpdfmanager' ); ?>" />
				<span style="display: inline-block;width: 10px;">&nbsp;</span>
				<select name="bsk_pdfm_available_category_for_users_list_select" id="bsk_pdfm_available_category_for_users_user_list_select_ID" disabled >
					<option value=""><?php echo esc_html__( 'Please select a user role first...', 'bskpdfmanager' ); ?></option>
				</select>
				<span id="bsk_pdfm_available_category_for_users_list_select_ajax_loader_ID" style="display: none;"><img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" /></span>
				<span style="display: inline-block;width: 10px;">&nbsp;</span>
			</p>
			<p class="bsk-pdfm-error" style="display: none;" id="bsk_pdfm_available_category_for_users_list_error_ID"></p>
			<span id="bsk_pdfm_available_category_checkbox_click_ajax_loader_ID" style="display: none;"><img src="<?php echo BSKPDFManager::$_ajax_loader_img_url; ?>" /></span>
			<div style="margin-top: 20px;" id="bsk_pdfm_available_category_for_user_cat_checkboxes_section_ID"></div>
			<div style="margin-top: 40px;" id="bsk_pdfm_available_category_for_user_saved_user_list_section_ID">
				<h4><?php esc_html_e( 'Saved users with available categories list', 'bskpdfmanager' ); ?></h4>
				<table class="wp-list-table widefat fixed striped">
					<thead>
						<th><?php esc_html_e( 'Display name with login', 'bskpdfmanager' ); ?></th>
						<th><?php esc_html_e( 'Available Categories', 'bskpdfmanager' ); ?></th>
						<th></th>
					</thead>
					<tbody id="bsk_pdfm_available_category_for_user_saved_list_body_ID"></tobody>
				</table>
			</div>
		</div>
	</div>
	<?php 
		$ajax_nonce = wp_create_nonce( 'bsk_pdfm_capabilities_settings_save_ajax_oper_nonce' );
	?>
	<p>
		<input type="hidden" id="bsk_pdfm_capabilities_settings_save_ajax_oper_nonce_ID" value="<?php echo $ajax_nonce; ?>" />
	</p>
    </form>
    <?php
	}

	function bsk_pdfm_cap_settings_get_custom_role_capabilities_fun() {
		$data_to_return = array();

		if( !check_ajax_referer( 'bsk_pdfm_capabilities_settings_save_ajax_oper_nonce', 'nonce' ) ){
            $data_to_return['success'] = false;
            $data_to_return['message'] = __( 'Invalid nonce, please refresh page to try again', 'bskpdfmanager' );
            
            wp_die( json_encode( $data_to_return ) );
        }

		$role = sanitize_text_field( $_POST['role'] );
		if ( $role == '' ) {
			$data_to_return['success'] = false;
            $data_to_return['message'] = __( 'Invalid role name', 'bskpdfmanager' );
            
            wp_die( json_encode( $data_to_return ) );
		}

		if ( ! current_user_can( 'bsk_pdfm_do_settings') ) {
			$data_to_return['success'] = false;
            $data_to_return['message'] = __( 'You are not allowed to do this', 'bskpdfmanager' );
            
            wp_die( json_encode( $data_to_return ) );
		}

		$role_capabilities = get_role( $role )->capabilities;

		$data_to_return['capabilities'] = array_keys( $role_capabilities );
		$data_to_return['success'] = true;
		
		wp_die( json_encode( $data_to_return ) );
	}

	function bsk_pdfm_bsk_pdfm_cap_settings_role_special_capability_fun() {
		$data_to_return = array();

		if( !check_ajax_referer( 'bsk_pdfm_capabilities_settings_save_ajax_oper_nonce', 'nonce' ) ){
            $data_to_return['success'] = false;
            $data_to_return['message'] = __( 'Invalid nonce, please refresh page to try again', 'bskpdfmanager' );
            
            wp_die( json_encode( $data_to_return ) );
        }

		$role = sanitize_text_field( $_POST['role'] );
		if ( $role == '' ) {
			$data_to_return['success'] = false;
            $data_to_return['message'] = __( 'Invalid role name', 'bskpdfmanager' );
            
            wp_die( json_encode( $data_to_return ) );
		}

		if ( ! current_user_can( 'bsk_pdfm_do_settings') ) {
			$data_to_return['success'] = false;
            $data_to_return['message'] = __( 'You are not allowed to do this', 'bskpdfmanager' );
            
            wp_die( json_encode( $data_to_return ) );
		}

		$enalbe = sanitize_text_field( $_POST['enable'] ) == 'true' ? true : false;
		$capability = sanitize_text_field( $_POST['capability'] );

		//add or remove capabilities to role
		$role_obj = get_role( $role );
		$all_capabilities = BSKPDFM_Dashboard::bsk_pdfm_capabilities();
		if ( ! array_key_exists( $capability, $all_capabilities ) ) {
			$data_to_return['success'] = false;
            $data_to_return['message'] = __( 'Invalid capability', 'bskpdfmanager' );
            
            wp_die( json_encode( $data_to_return ) );
		}

		if ( $enalbe ) {
			//add
			$role_obj->add_cap( $capability );
		} else {
			//remove
			$role_obj->remove_cap( $capability );
		}
		
		$data_to_return['success'] = true;
		$data_to_return['message'] = $role . ' -- ' . $capability;
		wp_die( json_encode( $data_to_return ) );
	}

	function bsk_pdfm_enable_available_categories_for_users_fun() {
		$data_to_return = array();

		if( !check_ajax_referer( 'bsk_pdfm_capabilities_settings_save_ajax_oper_nonce', 'nonce' ) ){
            $data_to_return['success'] = false;
            $data_to_return['message'] = __( 'Invalid nonce, please refresh page to try again', 'bskpdfmanager' );
            
            wp_die( json_encode( $data_to_return ) );
        }

		if ( ! current_user_can( 'bsk_pdfm_do_settings') ) {
			$data_to_return['success'] = false;
            $data_to_return['message'] = __( 'You are not allowed to do this', 'bskpdfmanager' );
            
            wp_die( json_encode( $data_to_return ) );
		}

		$enalbe = sanitize_text_field( $_POST['enable'] ) == 'true' ? true : false;

		//read plugin settings
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, false );
		$capability_settings = array();
		if ( $plugin_settings && is_array( $plugin_settings ) && count( $plugin_settings ) ) {
			if ( isset( $plugin_settings['capability_settings'] ) ) {
				$capability_settings = $plugin_settings['capability_settings'];
			}
		} else {
			$plugin_settings = array();
		}
		$capability_settings['users_available_categories'] = $enalbe;
		$plugin_settings['capability_settings'] = $capability_settings;
		
		update_option( BSKPDFManager::$_plugin_settings_option, $plugin_settings );

		$data_to_return['success'] = true;
		
		wp_die( json_encode( $data_to_return ) );
	}

	function bsk_pdfm_settings_capabilities_settings_get_users_option_by_role_fun() {

		$data_to_return = array();

		if( !check_ajax_referer( 'bsk_pdfm_capabilities_settings_save_ajax_oper_nonce', 'nonce' ) ){
            $data_to_return['success'] = false;
            $data_to_return['message'] = __( 'Invalid nonce, please refresh page to try again', 'bskpdfmanager' );
            
            wp_die( json_encode( $data_to_return ) );
        }

		if ( ! current_user_can( 'bsk_pdfm_do_settings') ) {
			$data_to_return['success'] = false;
            $data_to_return['message'] = __( 'You are not allowed to do this', 'bskpdfmanager' );
            
            wp_die( json_encode( $data_to_return ) );
		}

		$role = sanitize_text_field( $_POST['role'] );
		if ( $role == '' ) {
			$data_to_return['success'] = false;
            $data_to_return['message'] = __( 'Invalid role name', 'bskpdfmanager' );
            
            wp_die( json_encode( $data_to_return ) );
		}

		$users = get_users( array( 'role__in' => array( $role ), 'orderby' => 'display_name' ) );
		if ( ! $users || ! is_array( $users ) || count( $users ) < 1 ) {
			$data_to_return['success'] = true;
            $data_to_return['message'] = '';

			$option_str = '<option value="">'.esc_html__( 'No user found in role: ', 'bskpdfmanager' ) . $role.'</option>';
            $data_to_return['options'] = $option_str;

            wp_die( json_encode( $data_to_return ) );
		}
		
		$option_str = '<option value="">'.esc_html__( 'Please select a user...', 'bskpdfmanager' ).'</option>';
		foreach ( $users as $user ) {
			$option_str .= '<option value="' . $user->ID . '">' . esc_html( $user->display_name ) . ' ('.$user->user_login.')</option>';
		}

		$data_to_return['success'] = true;
		$data_to_return['message'] = '';
		$data_to_return['options'] = $option_str;
		
		wp_die( json_encode( $data_to_return ) );
	}

	function bsk_pdfm_settings_capabilities_settings_get_user_available_cats_checkboxs_fun() {
		$data_to_return = array();

		if( !check_ajax_referer( 'bsk_pdfm_capabilities_settings_save_ajax_oper_nonce', 'nonce' ) ){
            $data_to_return['success'] = false;
            $data_to_return['message'] = __( 'Invalid nonce, please refresh page to try again', 'bskpdfmanager' );
            
            wp_die( json_encode( $data_to_return ) );
        }

		if ( ! current_user_can( 'bsk_pdfm_do_settings') ) {
			$data_to_return['success'] = false;
            $data_to_return['message'] = __( 'You are not allowed to do this', 'bskpdfmanager' );
            
            wp_die( json_encode( $data_to_return ) );
		}

		$user_id = intval( sanitize_text_field( $_POST['userid'] ) );
		if ( $user_id < 1 ) {
			$data_to_return['success'] = false;
            $data_to_return['message'] = __( 'Invalid user', 'bskpdfmanager' );
            
            wp_die( json_encode( $data_to_return ) );
		}

		global $wpdb;

		$html_str = '';
		//get saved available categories for the user
		$saved_user_categories_array = array();

		//get saved categories list
		$sql = 'SELECT UA.`value` ' .
			   'FROM `' . $wpdb->prefix . BSKPDFManager::$_user_available_tbl_name . '` AS UA ' .
			   'WHERE UA.`type` LIKE "CAT" AND UA.`user_id` = %d ';
		$sql = $wpdb->prepare( $sql, $user_id );
		$results = $wpdb->get_results( $sql );
		if ( $results && is_array( $results ) && count( $results ) ) {
			foreach ( $results as $user_available_cat_obj ) {
				$saved_user_categories_array[] = $user_available_cat_obj->value;
			}
		}

		$user_obj = get_user_by( 'id', $user_id );
		$user_name_login = '';
		if ( $user_obj ) {
			$user_name_login = $user_obj->display_name . '( ' . $user_obj->user_login . ' )';
		}
		$html_str = '<h4>' . esc_html__( 'Set available categories for user: ' . $user_name_login, 'bskpdfmanager' ) . '</h4>';
		$html_str .= '<p style="display: none;"><input type="hidden" id="bsk_pdfm_user_available_categories_checkbox_for_userid_ID" value="' . $user_id . '" /></p>';
		//get all categories
		$sql = 'SELECT COUNT(*) FROM '.esc_sql( $wpdb->prefix.BSKPDFManager::$_cats_tbl_name ).' WHERE 1 AND `type` LIKE "CAT"';
		$categories_count = $wpdb->get_var( $sql );
		if( $categories_count ){
			//add none
			$none_checked = in_array( -1, $saved_user_categories_array ) ? 'checked' : '';
			$html_str .= '
				<ul>
					<li>
						<label>
							<input type="checkbox" name="bsk_pdfm_user_available_categories[]" class="bsk-pdfm-user-available-categories-none-checkbox" value="-1" ' . $none_checked . '>' . esc_html__( 'None', 'bskpdfmanager' ) . '
						</label>
					</li>
				</ul>';
			$html_str .= BSKPDFMPro_Common_Backend::get_category_hierarchy_checkbox( false, 'bsk_pdfm_user_available_categories[]', 'bsk-pdfm-user-available-categories-checkbox', $saved_user_categories_array, 'CAT', false );
		}else{
			$create_category_url = add_query_arg( 
													'page', 
													BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['category'], 
													admin_url('admin.php') 
												);
			$create_category_url = add_query_arg( 'view', 'addnew', $create_category_url );
			$create_category_str = sprintf( __( 'Please %s first', 'bskpdfmanager' ), '<a href="'.esc_url($create_category_url).'">'.__('create category', 'bskpdfmanager' ).'</a>' );
			
			$html_str .= $create_category_str;
		}
		

		$data_to_return['success'] = true;
		$data_to_return['message'] = '';
		$data_to_return['html'] = $html_str;
		
		wp_die( json_encode( $data_to_return ) );
	}

}