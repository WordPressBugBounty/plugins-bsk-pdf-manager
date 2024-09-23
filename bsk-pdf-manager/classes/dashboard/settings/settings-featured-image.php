<?php

class BSKPDFM_Dashboard_Settings_Featured_Image {
	
	private static $_bsk_pdf_settings_page_url = '';
	   
	public function __construct() {
		
		self::$_bsk_pdf_settings_page_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['setting'] );
		
		add_action( 'bsk_pdf_manager_register_image_sizes_save', array($this, 'bsk_pdf_manager_settings_register_image_sizes_save_fun') );
        add_action( 'wp_ajax_bsk_pdf_manager_settings_get_default_featured_image', array($this, 'bsk_pdf_manager_get_post_thumbnail_fun') );
	}

	function show_settings( $plugin_settings ){
		$default_enable_featured_image = true;
		$default_thumbnail_id = 0;
		$register_image_size_1 = array();
		$register_image_size_2 = array();
		$default_thumbnail_size = 'thumbnail';
        $supported_extension = array();

		if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
			
			if( isset($plugin_settings['enable_featured_image']) ){
				$default_enable_featured_image = $plugin_settings['enable_featured_image'];
			}
			
			if( isset($plugin_settings['default_thumbnail_id']) ){
				$default_thumbnail_id = $plugin_settings['default_thumbnail_id'];
			}
			if( isset($plugin_settings['default_thumbnail_size']) ){
				$default_thumbnail_size = $plugin_settings['default_thumbnail_size'];
			}
			if( isset($plugin_settings['register_image_size']) && 
				isset($plugin_settings['register_image_size']['size_1']) && 
				count($plugin_settings['register_image_size']['size_1']) > 0 ){
				$register_image_size_1 = $plugin_settings['register_image_size']['size_1'];
			}
			if( isset($plugin_settings['register_image_size']) && 
				isset($plugin_settings['register_image_size']['size_2']) && 
				count($plugin_settings['register_image_size']['size_2']) > 0 ){
				$register_image_size_2 = $plugin_settings['register_image_size']['size_2'];
			}

            if( isset($plugin_settings['supported_extension']) ){
                $supported_extension = $plugin_settings['supported_extension'];
			}
		}
        
        //$default_feated_image_size_dimission = BSKPDFMPro_Common_Backend::get_image_size_dimission( $default_thumbnail_size );
	?>
    <div class="bsk_pdf_manager_settings_featured_image_tab" style="width:80%;">
        <div>
            <form action="<?php echo add_query_arg( 'target', 'featured-image', self::$_bsk_pdf_settings_page_url ) ?>" method="POST" id="bsk_pdfm_register_image_sizes_form_ID">
            <h3><?php esc_html_e( 'Register Image Sizes', 'bskpdfmanager' ); ?></h3>
            <?php
            $size_name = '';
            $size_width = '';
            $size_height = '';
            $size_crop_str = '';
            if( is_array($register_image_size_1) && count($register_image_size_1) > 0 ){
                $size_name = $register_image_size_1['name'];
                $size_width = $register_image_size_1['width'];
                $size_height = $register_image_size_1['height'];
                $size_crop_str = $register_image_size_1['crop'] ? ' checked="checked"' : '';
            }
            ?>
            <p>
                <span style="display:inline-bloc;"><?php esc_html_e( 'Name', 'bskpdfmanager' ); ?>: <input type="text" name="bsk_pdf_manager_register_image_size_name_1" id="bsk_pdf_manager_register_image_size_name_1_ID" value="<?php echo $size_name; ?>" style="width:150px;" /> <?php esc_html_e( 'Width', 'bskpdfmanager' ); ?>: <input type="number" name="bsk_pdf_manager_register_image_size_width_1" id="bsk_pdf_manager_register_image_size_width_1_ID" value="<?php echo $size_width; ?>" style="width:80px;" />px <?php esc_html_e( 'Height', 'bskpdfmanager' ); ?>: <input type="number" name="bsk_pdf_manager_register_image_size_height_1"  id="bsk_pdf_manager_register_image_size_height_1_ID" value="<?php echo $size_height; ?>" style="width:80px;" />px
                </span>
                <span style="display:inline-block; margin-left:15px;"><label><input type="checkbox" name="bsk_pdf_manager_register_image_size_crop_1" id="bsk_pdf_manager_register_image_size_crop_1_ID" value="Yes"<?php echo $size_crop_str; ?> /><?php esc_html_e( 'Crop thumbnail to exact dimensions?', 'bskpdfmanager' ); ?></label></span>
            </p>
            <?php
            $size_name = '';
            $size_width = '';
            $size_height = '';
            $size_crop_str = '';
            if( is_array($register_image_size_2) && count($register_image_size_2) > 0 ){
            $size_name = $register_image_size_2['name'];
            $size_width = $register_image_size_2['width'];
            $size_height = $register_image_size_2['height'];
            $size_crop_str = $register_image_size_2['crop'] ? ' checked="checked"' : '';
            }
            ?>
            <p>
                <span style="display:inline-bloc;"><?php esc_html_e( 'Name', 'bskpdfmanager' ); ?>: <input type="text" name="bsk_pdf_manager_register_image_size_name_2" id="bsk_pdf_manager_register_image_size_name_2_ID" value="<?php echo $size_name; ?>" style="width:150px;" /> <?php esc_html_e( 'Width', 'bskpdfmanager' ); ?>: <input type="number" name="bsk_pdf_manager_register_image_size_width_2" id="bsk_pdf_manager_register_image_size_width_2_ID" value="<?php echo $size_width; ?>" style="width:80px;" />px <?php esc_html_e( 'Height', 'bskpdfmanager' ); ?>: <input type="number" name="bsk_pdf_manager_register_image_size_height_2" id="bsk_pdf_manager_register_image_size_height_2_ID" value="<?php echo $size_height; ?>" style="width:80px;" />px</span>
                <span style="display:inline-block; margin-left:15px;"><label><input type="checkbox" name="bsk_pdf_manager_register_image_size_crop_2" value="Yes"<?php echo $size_crop_str; ?> /><?php esc_html_e( 'Crop thumbnail to exact dimensions?', 'bskpdfmanager' ); ?></label></span>
            </p>
            <p style="margin-top:20px;">
                <input type="button" id="bsk_pdfm_register_image_sizes_save_form_ID" class="button-primary" value="<?php esc_attr_e( 'Save Image Sizes', 'bskpdfmanager' ); ?>" />
                <input type="hidden" name="bsk_pdf_manager_action" value="register_image_sizes_save" />
            </p>
            <?php echo wp_nonce_field( plugin_basename( __FILE__ ), 'bsk_pdfm_register_image_sizes_save_oper_nonce', true, false ); ?>
            </form>
        </div>
        <hr style="padding:20px 0 20px 0;" />
        <div>
            <form action="<?php echo add_query_arg( 'target', 'featured-image', self::$_bsk_pdf_settings_page_url ) ?>" method="POST" id="bsk_pdfm_featured_image_settings_form_ID">
            <h3><?php esc_html_e( 'Featured Image Settings', 'bskpdfmanager' ); ?></h3>
            <p>
                <label><input type="checkbox" name="bsk_pdf_manager_enable_featured_image" id="bsk_pdf_manager_enable_featured_image_ID" value="1" <?php echo $default_enable_featured_image ? 'checked="checked"' : '' ?> /> <?php esc_html_e( 'Enable featured image', 'bskpdfmanager' ); ?></label>
            </p>
            <div id="bsk_pdf_manager_featured_image_settings_containder_ID" style="display:<?php echo $default_enable_featured_image ? 'block' : 'none'; ?>; margin-top: 40px;">
                <div>
                    <?php
                    $ajax_loader_img_url = BSKPDFManager::$_ajax_loader_img_url;
                    $default_pdf_icon_url = BSKPDFManager::$_default_pdf_icon_url;
                    $remove_anchor_display = "none";
                    $defaut_size_display = 'none';
                    $thumbnail_html = '<img src="' . $default_pdf_icon_url . '" style="width: 60px;" />';
                    if( $default_thumbnail_id && get_post( $default_thumbnail_id ) ){
                        $thumbnail_html = wp_get_attachment_image( $default_thumbnail_id, 'thumbnail' );
                        $remove_anchor_display = "inline-block";
                        $defaut_size_display = 'block';
                    }

                    $image_sizes = BSKPDFM_Common_Backend::get_image_sizes();

                    $image_attributes_anchor = '<a href="https://www.bannersky.com/document/bsk-pdf-manager/display-all-specific/display-specific-pdfs-in-list/?attrid=featured-image" target="_blank">shortcode featured image attributes</a>';
                    ?>
                    <h4><?php esc_html_e( 'Default featured image for all file types', 'bskpdfmanager' ); ?></h4>
                    <p><?php esc_html_e( 'If no featured image is set for the PDF/document, the default featured image set here will be used.', 'bskpdfmanager' ); ?></p>
                    <p><?php printf( esc_html__( 'The default image size is used to determine the image size shown in the front. If a document / pdf has its own featured image, the size can be overridden by the  shortcode parameter: featured_image_size. If your shortcode is to display the PDF/document in the list, you can check the %s more.', 'bskpdfmanager' ), $image_attributes_anchor ); ?></p>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>File type</th>
                                <th>Image</th>
                                <th>&nbsp;</th>
                                <th>Default Size</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bsk-pdfm-featured-image-settings-for-type-all">
                                <td>All</td>
                                <td class="bsk-pdfm-featured-image-container"><div style="width: 60px; height: 60px;"><a href="javascript:void(0);" class="bsk-pdfm-settings-featured-image-wrap"><?php echo $thumbnail_html; ?></a></div></td>
                                <td>
                                    <a href="javascript:void(0);" class="bsk-pdfm-upload-featured-image" ><?php echo __( 'Set', 'bskpdfmanager' ); ?></a>
                                    <a href="javascript:void(0);" class="bsk-pdfm-remove-featured-image" style="display:<?php echo $remove_anchor_display; ?>; margin-left: 30px;"><?php echo __( 'Remove', 'bskpdfmanager' ); ?></a>
                                    <span class="bsk-pdf-manager-set-default-featured-image-ajax-loader" style="display:none;"><img src="<?php echo $ajax_loader_img_url; ?>" /></span>
                                    <input type="hidden" name="bsk_pdf_manager_default_thumbnail_id" class="bsk-pdfm-featured-image-thumbnail-id" value="<?php echo $default_thumbnail_id; ?>" />
                                </td>
                                <td>
                                    <select name="bsk_pdf_manager_default_thumbnail_size" class="bsk-pdfm-featured-image-size">
                                        <?php 
                                            $optons_data = $this->bsk_pdfm_get_featured_image_size_dropdown_options( $image_sizes, $default_thumbnail_size );
                                            echo $optons_data['options'];
                                        ?>
                                    </select>
                                    <p class="bsk-pdfm-featured-image-size-details" style="margin-top: 10px;"><?php echo $optons_data['selected']; ?></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <input type="hidden" id="bsk_pdf_manager_default_pdf_icon_url" value="<?php echo $default_pdf_icon_url; ?>" />

                    <?php
                    $featured_image_by_file_type_settings = false;
                    $enable = false;
                    $file_types_featured_image = array();
                    if ( $featured_image_by_file_type_settings && is_array( $featured_image_by_file_type_settings ) && count( $featured_image_by_file_type_settings ) > 0 ) {
                        if ( isset( $featured_image_by_file_type_settings['enable'] ) && strtoupper( $featured_image_by_file_type_settings['enable'] ) == 'YES' ) {
                            $enable = true;

                            if ( isset( $featured_image_by_file_type_settings['file_types_featured_image'] ) && 
                                 is_array( $featured_image_by_file_type_settings['file_types_featured_image'] ) && 
                                 count( $featured_image_by_file_type_settings['file_types_featured_image'] ) > 0 ) {

                                $file_types_featured_image = $featured_image_by_file_type_settings['file_types_featured_image'];
                            }
                        }
                    } 
                    ?>
                    <h4 style="margin-top:60px;"><?php esc_html_e( 'Set featured image by file type', 'bskpdfmanager' ); ?></h4>
                    <label>
                        <input type="checkbox" name="bsk_pdfm_enable_featured_imagge_by_file_type" id="bsk_pdfm_enable_featured_imagge_by_file_type_ID" value="YES" <?php echo ( $enable ? 'checked' : '' ) ?>/> <?php esc_html_e( 'Enable to set', 'bskpdfmanager' ); ?>
                    </label>
                    <div id="bsk_pdfm_set_featured_image_by_file_type" style="display:<?php echo ( $enable ? 'block' : 'none' ); ?>; margin-top: 20px;">
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th>File type</th>
                                    <th>Image</th>
                                    <th>&nbsp;</th>
                                    <th>Default Size</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ( $supported_extension as $extension ) {
                                    $thumbnail_html = '';
                                    $extension_thumbnail_id = 0;
                                    $extension_size = 'thumbnail';
                                    $remove_anchor_display = 'none';
                                    if ( isset( $file_types_featured_image[$extension] ) ) {
                                        $extension_thumbnail_id = $file_types_featured_image[$extension]['id'];
                                        $extension_size = $file_types_featured_image[$extension]['size'];

                                        if( $extension_thumbnail_id && get_post( $extension_thumbnail_id ) ){
                                            $thumbnail_html = wp_get_attachment_image( $extension_thumbnail_id, 'thumbnail' );
                                            $remove_anchor_display = "inline-block";
                                        }
                                    }                                    
                                ?>
                                <tr class="bsk-pdfm-featured-image-settings-for-type-all">
                                    <td><?php echo $extension; ?></td>
                                    <td class="bsk-pdfm-featured-image-container"><div style="width: 60px; height: 60px;"><a href="javascript:void(0);" class="bsk-pdfm-settings-featured-image-wrap"><?php echo $thumbnail_html; ?></a></div></td>
                                    <td>
                                        <a href="javascript:void(0);" class="bsk-pdfm-upload-featured-image" ><?php echo __( 'Set', 'bskpdfmanager' ); ?></a>
                                        <a href="javascript:void(0);" class="bsk-pdfm-remove-featured-image bsk-pdfm-featured-image-by-file-type" style="display:<?php echo $remove_anchor_display; ?>; margin-left: 30px;"><?php echo __( 'Remove', 'bskpdfmanager' ); ?></a>
                                        <span class="bsk-pdf-manager-set-default-featured-image-ajax-loader" style="display:none;"><img src="<?php echo $ajax_loader_img_url; ?>" /></span>
                                        <input type="hidden" name="bsk_pdfm_featured_image_by_file_type_thumbnail_id[<?php echo $extension; ?>]" class="bsk-pdfm-featured-image-thumbnail-id" value="<?php echo $extension_thumbnail_id; ?>" />
                                    </td>
                                    <td>
                                        <?php
                                        $size_display = 'none';
                                        if ( $extension_thumbnail_id ) {
                                            $size_display = 'block';
                                        }
                                        ?>
                                        <select name="bsk_pdfm_featured_image_by_file_type_thumbnail_size[<?php echo $extension; ?>]" class="bsk-pdfm-featured-image-size" style="display:<?php echo $size_display; ?>;">
                                            <?php 
                                                $optons_data = $this->bsk_pdfm_get_featured_image_size_dropdown_options( $image_sizes, $extension_size );
                                                echo $optons_data['options'];
                                            ?>
                                        </select>
                                        <p class="bsk-pdfm-featured-image-size-details" style="margin-top: 10px; display: <?php echo $size_display; ?>"><?php echo $optons_data['selected']; ?></p>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <p style="margin-top:20px;">
                <input type="button" id="bsk_pdf_manager_settings_featured_image_tab_save_form_ID" class="button-primary" value="<?php esc_attr_e( 'Save Featured Image Settings', 'bskpdfmanager' ); ?>" />
                <input type="hidden" name="bsk_pdf_manager_action" value="featured_image_settings_save" />
            </p>
            <?php echo wp_nonce_field( plugin_basename( __FILE__ ), 'bsk_pdf_manager_settings_featured_image_tab_save_oper_nonce', true, false ); ?>
            </form>
        </div>
    </div>
    </form>
    <?php
	}

    function bsk_pdfm_get_featured_image_size_dropdown_options( $image_sizes, $saved_size_name ) {
        $image_size_dropdown_options = '';
        $crop_no_str = __( 'Crop: No', 'bskpdfmanager' );
        $crop_yes_str = __( 'Crop: Yes', 'bskpdfmanager' );
        $selected_desc = '';
        foreach ( $image_sizes as $size_name => $size_name_dimission )  {
            if ( $size_name_dimission['width'] < 1 || 
                $size_name_dimission['height'] < 1 || 
                $size_name == 'bsk-pdf-dashboard-list-thumbnail' ){
                continue;
            }
            $selected_str = '';
            $crop_str = $size_name_dimission['crop'] ? $crop_yes_str : $crop_no_str;
            if ( $saved_size_name == $size_name ) {
                $selected_str = 'selected="selected"';
                $selected_desc = 'Width: ' . $size_name_dimission['width'] . ' px, Height: ' . $size_name_dimission['height'] . ' px, ' . $crop_str;
            }
            $image_size_dropdown_options .= '<option value="'.$size_name.'" '.$selected_str.' data-width="'.$size_name_dimission['width'].'" data-height="'.$size_name_dimission['height'].'" data-crop="'.$crop_str.'">'.$size_name.'</option>';
        }
        if( $saved_size_name == 'full' ){
            $image_size_dropdown_options .= '<option value="full" selected="selected" data-width="" data-height="" data-crop="">'.__( 'full','bskpdfmanager' ).'</option>';
        }else{
            $image_size_dropdown_options .= '<option value="full" data-width="" data-height="" data-crop="">'.__( 'full','bskpdfmanager' ).'</option>';
        }

        return array( 'options' => $image_size_dropdown_options, 'selected' => $selected_desc );
    } 

    function bsk_pdf_manager_settings_register_image_sizes_save_fun( $data ) {
        global $wpdb, $current_user;
		//check nonce field
		if ( !wp_verify_nonce( sanitize_text_field($data['bsk_pdfm_register_image_sizes_save_oper_nonce']), plugin_basename( __FILE__ ) )) {
			wp_die( esc_html__( 'Security issue, please refresh page and test again', 'bskpdfmanager' ) );
		}
		
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
		if ( ! current_user_can( 'moderate_comments' ) ) {
            wp_die( esc_html__( 'You are now allowed to do this', 'bskpdfmanager' ) );
        }
		
		$plugin_settings['register_image_size'] = array( 'size_1' => array(), 'size_2' => array() );
		
		$register_size_name_1 = trim(sanitize_text_field($data['bsk_pdf_manager_register_image_size_name_1']));
		$register_size_width_1 = sanitize_text_field($data['bsk_pdf_manager_register_image_size_width_1']);
		$register_size_height_1 = sanitize_text_field($data['bsk_pdf_manager_register_image_size_height_1']);
		$register_size_crop_1 = isset($data['bsk_pdf_manager_register_image_size_crop_1']) ? true : false;

		if( $register_size_name_1 && $register_size_width_1 && $register_size_height_1 ){
			$plugin_settings['register_image_size']['size_1'] = array( 
																	   'name' => $register_size_name_1,
																	   'width' => $register_size_width_1,
																	   'height' => $register_size_height_1,
																	   'crop' => $register_size_crop_1
																	 );
		}
		
		$register_size_name_2 = trim($data['bsk_pdf_manager_register_image_size_name_2']);
		$register_size_width_2 = $data['bsk_pdf_manager_register_image_size_width_2'];
		$register_size_height_2 = $data['bsk_pdf_manager_register_image_size_height_2'];
		$register_size_crop_2 = isset($data['bsk_pdf_manager_register_image_size_crop_2']) ? true : false;
		
		if( $register_size_name_2 && $register_size_width_2 && $register_size_height_2 ){
			$plugin_settings['register_image_size']['size_2'] = array( 
																	   'name' => $register_size_name_2,
																	   'width' => $register_size_width_2,
																	   'height' => $register_size_height_2,
																	   'crop' => $register_size_crop_2
																	 );
		}

		update_option( BSKPDFManager::$_plugin_settings_option, $plugin_settings );
    }
	
	function bsk_pdf_manager_get_post_thumbnail_fun(){
		global $wpdb, $current_user;
		//check nonce field
		if( !check_ajax_referer( 'bsk_pdf_manager_settings_page_ajax-oper-nonce', 'nonce', false ) ){
			wp_die( __( 'ERROR - Invalid nonce, please refresh page to try again', 'bskpdfmanager' ) );
		}
		
		if ( ! current_user_can( 'moderate_comments' ) ) {
            wp_die( esc_html__( 'You are now allowed to do this', 'bskpdfmanager' ) );
        }
		
		$thumbnail_id = intval(sanitize_text_field($_POST['thumbnail_id']));
		if( $thumbnail_id && get_post( $thumbnail_id ) ){
			$thumbnail_html = wp_get_attachment_image( $thumbnail_id, 'thumbnail' );
			wp_die( '<a href="javascript:void(0);" class="bsk-pdfm-settings-featured-image-wrap">' . $thumbnail_html . '</a>' );
		}
		
		wp_die( sprintf( __( 'ERROR - Invalid thumbnail ID: %s', 'bskpdfmanager' ), $thumbnail_id ) );
	}
	
	function get_image_sizes() {
		global $_wp_additional_image_sizes;
	
		$sizes = array();
	
		foreach ( get_intermediate_image_sizes() as $_size ) {
			if ( $_size == 'bsk-pdf-dashboard-list-thumbnail' ){
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
	}

}