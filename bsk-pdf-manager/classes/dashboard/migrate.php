<?php

class BSKPDFM_Dashboard_Migrate {

	public function __construct() {
        //
	}

	function bsk_pdfm_migrate_inteface() {
        global $wpdb;

        $default_enable_featured_image = true;
        $organise_directory_strucutre_with_year_month = true;
        $plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
		if ( $plugin_settings && is_array( $plugin_settings ) && count( $plugin_settings ) > 0 ) {
			if ( isset( $plugin_settings['enable_featured_image'] ) ){
				$default_enable_featured_image = $plugin_settings['enable_featured_image'];
			}
            if ( isset( $plugin_settings['directory_with_year_month'] ) ) {
                $organise_directory_strucutre_with_year_month = $plugin_settings['directory_with_year_month'];
			}
        }
		?>
        <p>If you're migrating an entire site, you don't need to care about the following.</p>
        <p>Considering that many users have thousands of files, here is just a guide for migrating data to a new site.</p>
        <h2 style="font-size: 1.5em; margin-top: 40px;">Export all data to local server</h2>
        <div class="bsk-pdfm-migrate-step-1 accrodion-container">
            <button class="accordion-button bsk-pdfm-migrate-steps"><span class="bsk-pdfm-migrate-step-header">Step 1</span>, make sure you have updated to the latest version.</button>
            <?php
                $license_update_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['license_update'] );
            ?>
            <div class="accordion-panel">
                <p>For the free version, please check from: <a href="<?php echo admin_url( 'plugins.php' ); ?>">Plugins</a> page.</a>
                <p>For the free Pro version, you may check it from the <span class="bsk-pdfm-bold">Update Centre</span> on <a href="<?php echo $license_update_url; ?>" target="_blank">License & Update</a> page.</p>
            </div>
        </div>
        <div class="bsk-pdfm-migrate-step-2 accrodion-container">
            <button class="accordion-button bsk-pdfm-migrate-steps"><span class="bsk-pdfm-migrate-step-header">Step 2</span>, make sure no PDFs/documents are uploaded to the media library.</button>
            <div class="accordion-panel">
                <?php
                    $sql = 'SELECT `id`, `title`, `trash` FROM `' . esc_sql( $wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name ) .'` WHERE `by_media_uploader` > 0 ';
                    $in_media_lib_results = $wpdb->get_results( $sql );
                    if ( ! $in_media_lib_results || ! is_array( $in_media_lib_results ) || count( $in_media_lib_results ) < 1 ) {

                ?>
                <p>It is OK, no PDF / document is uploaded to Media Library.</p>
                <?php
                    } else {
                        $current_upload_path = BSKPDFManager::$_upload_path;
                        if( $organise_directory_strucutre_with_year_month ){
                            $current_upload_path .= wp_date('Y/m/');
                        }
                        $current_upload_path_to_show = str_replace(BSKPDFManager::$_upload_root_path, '', $current_upload_path);
                ?>
                <p>Please edit the following files and upload the files to the current upload directory: <span style="font-weight: bold; padding-left: 5px; width: 70%;"><?php echo $current_upload_path_to_show; ?></span></p>
                <table class="widefat bsk-pdfm-migrate-doc-list-table striped" style="width:85%;table-layout:fixed;">
                    <thead>
                        <tr>
                            <td style="width:5%;"><?php esc_html_e( "ID", 'bskpdfmanager' ); ?></td>
                            <td style="width:30%;overflow:visible;"><?php esc_html_e( "Title", 'bskpdfmanager' ); ?></td>
                            <td style="width:40%;overflow:visible;"><?php esc_html_e( "Edit URL", 'bskpdfmanager' ); ?></td>
                            <td style="width:15%;"></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $edit_url_base = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['edit'] );
                        $trash_folder_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['base'].'&pdf_status=trash' );
                        foreach( $in_media_lib_results as $pdf_obj ) {
                            $edit_url = add_query_arg( 'pdfid', $pdf_obj->id, $edit_url_base );
                            $trash_text = '';
                            if ( $pdf_obj->trash ) {
                                $trash_text = 'Trash folder';
                                $edit_url = $trash_folder_url;
                            }
                        ?>
                        <tr>
                            <td><?php echo $pdf_obj->id; ?></td>
                            <td><?php echo $pdf_obj->title; ?></td>
                            <td><a href="<?php echo $edit_url; ?>" target="_blank"><?php echo $edit_url; ?></a></td>
                            <td><?php echo $trash_text; ?></td>
                        </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
                <?php
                    }
                ?>
            </div>
        </div>
        <div class="bsk-pdfm-migrate-step-3 accrodion-container">
            <button class="accordion-button bsk-pdfm-migrate-steps"><span class="bsk-pdfm-migrate-step-header">Step 3</span>, featured images. </button>
            <div class="accordion-panel">
                <?php 
                if ( ! $default_enable_featured_image ) { 
                ?>
                <p>The Featured image for PDFs/documents feature is disabled, so don't need to care about this.</P>
                <?php 
                } else { 
                    $sql = 'SELECT COUNT(*) FROM `' . esc_sql( $wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name ) .'` WHERE `thumbnail_id` > 0 ';
                    //$thumbnail_id_results = $wpdb->get_results( $sql );
                ?>
                <p>Featured images for PDF / Document cannot be migrated at this stage, we will work to do so in the future.</P>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="bsk-pdfm-migrate-step-4 accrodion-container">
            <button class="accordion-button bsk-pdfm-migrate-steps"><span class="bsk-pdfm-migrate-step-header">Step 4</span>, export database tables. </button>
            <div class="accordion-panel">
                <p>Use phpMyAdmin to export the following database tables to a sql file. The phpMyAdmin access link can be found on your hosting admin panel. If you don't know where it is, please contact your hosting support.</p>
                <p style="font-style: italic;">Save each table as a separate sql file.</p>
                <p><?php echo $wpdb->prefix; ?></p> is the database table prefix.</p> 
                <p>
                    <ul>
                        <li class="bsk-pdfm-bold"><?php echo $wpdb->prefix.BSKPDFManager::$_cats_tbl_name; ?></li>
                        <li class="bsk-pdfm-bold"><?php echo $wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name; ?></li>
                        <li class="bsk-pdfm-bold"><?php echo $wpdb->prefix.BSKPDFManager::$_rels_tbl_name; ?></li>
                        <li class="bsk-pdfm-bold"><?php echo $wpdb->prefix.BSKPDFManager::$_user_available_tbl_name; ?></li>
                        <li class="bsk-pdfm-bold"><?php echo $wpdb->prefix.BSKPDFManager::$_notifications_tbl_name; ?></li>
                    </ul>
                </p>
            </div>
        </div>
        <?php
        $uploader_folders = BSKPDFM_Common_Backend::bsk_pdfm_get_all_upload_fodlers();
        ?>
        <div class="bsk-pdfm-migrate-step-5 accrodion-container">
            <button class="accordion-button bsk-pdfm-migrate-steps"><span class="bsk-pdfm-migrate-step-header">Step 5</span>, download all files. </button>
            <div class="accordion-panel">
                <p>Download the following folders (and all subfolders/files) from the site root directory via FTP tool.</p>
                <p>Do not change any subdirectories or files.</p>
                <p>
                    <ul>
                        <?php foreach ( $uploader_folders as $folder_name ) { ?>
                        <li class="bsk-pdfm-bold"><?php echo $folder_name; ?></li>
                        <?php } ?>
                    </ul>
                </p>
            </div>
        </div>
        <h2 style="font-size: 1.5em; margin-top: 40px;">Upload all data to your new site</h2>
        <div class="bsk-pdfm-migrate-step-1 accrodion-container">
            <button class="accordion-button bsk-pdfm-migrate-steps"><span class="bsk-pdfm-migrate-step-header">Step 1</span>, install the latest version.</button>
            <div class="accordion-panel">
                <p>For the free version, please download the latest version package form: <a href="https://wordpress.org/plugins/bsk-pdf-manager/" target="_blank">WordPress.org</a></p>
                <p>For the Pro version, please download the latest version package from the <a href="https://www.bannersky.com/purchase-history/" target="_blank">purchase history</a> page and install it to your site.</p>
                <p>For the Pro version, need to activate the license.</p>
            </div>
        </div>
        <div class="bsk-pdfm-migrate-step-2 accrodion-container">
            <button class="accordion-button bsk-pdfm-migrate-steps"><span class="bsk-pdfm-migrate-step-header">Step 2</span>, import saved database tables ( sql file ).</button>
            <div class="accordion-panel">
                <p>Open your website's database with phpMyAdmin. </p>
                <p>Delete the five tables with names like xx_bsk_pdf_manager_. xx is the database table prefix for your Wordpress site, make a note of it. For example the prefix is: wp_1232_</p>
                <p>Import database table from the five saved sql files. </p>
                <p>If the database prefix <span class="bsk-pdfm-bold">is not: <?php echo $wpdb->prefix; ?></span> (old site), you need to change the names of five tables. For example the prefix is: wp_1232_</p>
                <p>
                    <ul>
                        <li>Rename table <?php echo $wpdb->prefix.BSKPDFManager::$_cats_tbl_name; ?> to wp_1232_<?php echo BSKPDFManager::$_cats_tbl_name; ?></li>
                        <li>Rename table <?php echo $wpdb->prefix.BSKPDFManager::$_pdfs_tbl_name; ?> to wp_1232_<?php echo BSKPDFManager::$_pdfs_tbl_name; ?></li>
                        <li>Rename table <?php echo $wpdb->prefix.BSKPDFManager::$_rels_tbl_name; ?> to wp_1232_<?php echo BSKPDFManager::$_rels_tbl_name; ?></li>
                        <li>Rename table <?php echo $wpdb->prefix.BSKPDFManager::$_user_available_tbl_name; ?> to wp_1232_<?php echo BSKPDFManager::$_user_available_tbl_name; ?></li>
                        <li>Rename table <?php echo $wpdb->prefix.BSKPDFManager::$_notifications_tbl_name; ?> to wp_1232_<?php echo BSKPDFManager::$_notifications_tbl_name; ?></li>
                    </ul>
                </p>
            </div>
        </div>
        <div class="bsk-pdfm-migrate-step-3 accrodion-container">
            <button class="accordion-button bsk-pdfm-migrate-steps"><span class="bsk-pdfm-migrate-step-header">Step 3</span>, upload all files to your new site.</button>
            <div class="accordion-panel">
                <p>Upload all files to the new site and keep the same root-relative paths as the old site.</p>
                <p>
                    <ul>
                        <?php foreach ( $uploader_folders as $folder_name ) { ?>
                        <li class="bsk-pdfm-bold"><?php echo $folder_name; ?></li>
                        <?php } ?>
                    </ul>
                </p>
                <p>If the new site is a Wordpress multisite installation, you need to make sure you find the correct uploads folder for the site. 
            </div>
        </div>
        <div class="bsk-pdfm-migrate-step-3 accrodion-container">
            <button class="accordion-button bsk-pdfm-migrate-steps"><span class="bsk-pdfm-migrate-step-header">Step 4</span>, check all files.</button>
            <div class="accordion-panel">
                <p>That's all. Pleae go to the <span class="bsk-pdfm-bold">Dashboard --> BSK PDF Pro --> PDFs / Documents</span> menu on your site to check all files.</p>
                <p>If some files show Missing error, it because you didn't keep the relative path right for the upload folders.</p>
            </div>
        </div>
        <?php
    }
	
}
