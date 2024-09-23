<?php
class BSKPDFManagerWidget_Category extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'bsk_pdf_manager_widget_category', // Base ID
            'BSK PDF Manager Category', // Name
            array( 'description' => __( 'Display all PDFs within a given category', 'bskpdfmanager' ) ) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        global $wpdb;

        $pdfm_widget_title = '';
        $pdfm_category_id = 0;
        $pdfm_show_cat_title = 'NO';
        $pdfm_order_by = 'title';
        $pdfm_order = 'ASC';
        $pdfm_most_top_of = '';
        $pdfm_show_date = 'NO';
        $pdfm_date_format_str = '';
        $pdfm_date_before_title = 'NO';
        $pdfm_open_in_new = 'NO';
        $pdfm_ordered_list = 'NO';
        $pdfm_no_follow_tag = 'NO';
        if( isset( $instance[ 'pdfm_widget_title' ] ) ){
            //new version 2.0
            $pdfm_widget_title = $instance['pdfm_widget_title'];
            $pdfm_category_id = $instance['pdfm_category_id'];
            $pdfm_show_cat_title = $instance['pdfm_show_cat_title'];
            $pdfm_order_by = $instance['pdfm_order_by'];
            $pdfm_order = $instance['pdfm_order'];
            $pdfm_most_top_of = $instance['pdfm_most_top_of'];
            $pdfm_show_date = $instance['pdfm_show_date'];
            $pdfm_date_format_str = $instance['pdfm_date_format_str'];
            $pdfm_date_before_title = $instance['pdfm_date_before_title'];      
            $pdfm_open_in_new = $instance['pdfm_open_in_new'];
            $pdfm_ordered_list = $instance['pdfm_ordered_list'];
            $pdfm_no_follow_tag = $instance['pdfm_no_follow_tag'];
        }else{
            //version 1.0
            $pdfm_widget_title = isset( $instance['widget_title'] ) ? $instance['widget_title'] : '';
            $pdfm_category_id = isset( $instance['bsk_pdf_manager_category'] ) ? $instance['bsk_pdf_manager_category'] : 0;
            $pdfm_show_cat_title = isset( $instance[ 'bsk_pdf_manager_show_cat_title' ] ) ? $instance[ 'bsk_pdf_manager_show_cat_title' ] : 'NO';
            $pdfm_order_by = isset( $instance['bsk_pdf_manager_cat_order_by'] ) ? $instance['bsk_pdf_manager_cat_order_by'] : 'title';
            $pdfm_order = isset( $instance['bsk_pdf_manager_cat_order'] ) ? $instance['bsk_pdf_manager_cat_order'] : 'ASC';
            $pdfm_most_top_of = isset( $instance['bsk_pdf_manager_category_top'] ) ? $instance['bsk_pdf_manager_category_top'] : 0;
            $pdfm_open_in_new = isset( $instance['bsk_pdf_manager_open_in_new'] ) ? $instance['bsk_pdf_manager_open_in_new'] : 'NO';
            $pdfm_ordered_list = isset( $instance['bsk_pdf_manager_show_ordered_list'] ) ? $instance['bsk_pdf_manager_show_ordered_list'] : 'NO';
        }
        
        $default_enable_permalink = false;
		$plugin_settings = get_option( BSKPDFManager::$_plugin_settings_option, '' );
        if( $plugin_settings && is_array($plugin_settings) && count($plugin_settings) > 0 ){
            if( isset($plugin_settings['enable_permalink']) ){
				$default_enable_permalink = $plugin_settings['enable_permalink'];
			}
		}
        
        $permalink_structure = get_option( 'permalink_structure' );
        if ( ! $permalink_structure ) {
            $default_enable_permalink = false;
        }
        
        echo $args['before_widget'];

        if( trim($pdfm_widget_title) ){
            echo '<h2 class="widget-title">'.$pdfm_widget_title.'</h2>';
        }

        $sql = 'SELECT * FROM `'.esc_sql($wpdb->prefix.BSKPDFManager::$_cats_tbl_name).'`  AS C '.
               'WHERE `id` IN('.esc_sql($pdfm_category_id).') ';
        $categories_results = $wpdb->get_results($sql);
        if( !$categories_results || !is_array( $categories_results ) || count($categories_results) < 1 ){
            echo $args['after_widget'];
            return;
        }
        $category_obj = $categories_results[0];

        $query_args = array();
        $query_args['cat_order_by'] = 'title';
        $query_args['cat_order'] = 'ASC';
        $query_args['order_by'] = $pdfm_order_by;
        $query_args['order'] = $pdfm_order;
        $query_args['most_top'] = $pdfm_most_top_of;

        $query_args['ids_array'] = array( $category_obj->id );

        $cat_pdfs_query_results = BSKPDFM_Common_Data_Source::bsk_pdfm_get_pdfs_by_cat( $query_args );
        if( !$cat_pdfs_query_results || !is_array($cat_pdfs_query_results) || count($cat_pdfs_query_results) < 1 ){
            echo $args['after_widget'];
            return;
        }

        $ul_or_ol = $pdfm_ordered_list == 'YES' ? 'ol' : 'ul';
        $open_target_str = $pdfm_open_in_new == 'YES' ? ' target="_blank"' : '';
        $nofollow_tag = $pdfm_no_follow_tag == 'YES' ? ' rel="nofollow"' : '';
        $show_date = $pdfm_show_date == 'YES' ? true : false;
        $date_format_str = $pdfm_date_format_str;
        $date_before_title = $pdfm_date_before_title == 'YES' ? true : false;
        $date_format_str = $date_before_title ? 'd/m/Y ' : ' d/m/Y';
        if( trim($pdfm_date_format_str) ){
            $date_format_str = $pdfm_date_format_str;
        }
    
        $pdfs_results_array = $cat_pdfs_query_results['pdfs'];
        
        $category_depth = 1;
        $depth_class = ' category-hierarchical-depth-'.$category_depth;
        
        echo '<div class="bsk-pdfm-widget-output-container">';
        echo '<div class="bsk-pdfm-category-output cat-'.esc_attr($category_obj->id.$depth_class).' pdfs-in-'.esc_attr($ul_or_ol).'" data-cat-id="'.esc_attr($category_obj->id).'">';
        if( $pdfm_show_cat_title == 'YES' ){
            $cat_title = '<h3 class="bsk-pdfm-cat-titile bsk-pdf-manager-cat-title-widget">'.esc_html($category_obj->title).'</h3>';
            echo $cat_title;
        }

        $str_body = BSKPDFM_Common_Display::display_pdfs_in_ul_or_ol(
                                                                     $ul_or_ol,
                                                                     false, //$only_li
                                                                     'bsk-pdfm-pdfs-'.$ul_or_ol.'-list',
                                                                     $pdfs_results_array[$category_obj->id], 
                                                                     $open_target_str, $nofollow_tag, 
                                                                     $show_date, $date_format_str, $date_before_title,
                                                                     'h3',
                                                                     $default_enable_permalink
                                                                    );
        echo $str_body;
        echo '</div><!--//bsk-pdfm-category-output cat-'.esc_html($category_obj->id).'-->';
        
        echo '</div><!-- //bsk-pdfm-widget-output-container -->';
        
        echo $args['after_widget'];
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();

        $instance['pdfm_widget_title'] = strip_tags( $new_instance['pdfm_widget_title'] );
        $instance['pdfm_category_id'] = $new_instance['pdfm_category_id'];
        $instance['pdfm_show_cat_title'] = $new_instance['pdfm_show_cat_title'];
        $instance['pdfm_order_by'] = $new_instance['pdfm_order_by'];
        $instance['pdfm_order'] = $new_instance['pdfm_order'];
        $instance['pdfm_most_top_of'] = $new_instance['pdfm_most_top_of'];
        $instance['pdfm_show_date'] = $new_instance['pdfm_show_date'];
        $instance['pdfm_date_format_str'] = $new_instance['pdfm_date_format_str'];
        $instance['pdfm_date_before_title'] = $new_instance['pdfm_date_before_title'];
        $instance['pdfm_ordered_list'] = $new_instance['pdfm_ordered_list'];
        $instance['pdfm_open_in_new'] = $new_instance['pdfm_open_in_new'];
        $instance['pdfm_no_follow_tag'] = $new_instance['pdfm_no_follow_tag'];


        return $instance;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        global $wpdb;

        $pdfm_widget_title = '';
        $pdfm_category_id = 0;
        $pdfm_show_cat_title = 'NO';
        $pdfm_order_by = 'title';
        $pdfm_order = 'ASC';
        $pdfm_most_top_of = '';
        $pdfm_show_date = 'NO';
        $pdfm_date_format_str = '';
        $pdfm_date_before_title = 'NO';
        $pdfm_open_in_new = 'NO';
        $pdfm_ordered_list = 'NO';
        $pdfm_no_follow_tag = 'NO';
        
        $pdfm_display_title_in = 'h2';
        
        if( isset( $instance[ 'pdfm_widget_title' ] ) ){
            //new version 2.0
            $pdfm_widget_title = $instance['pdfm_widget_title'];
            $pdfm_category_id = $instance['pdfm_category_id'];
            $pdfm_show_cat_title = $instance['pdfm_show_cat_title'];
            $pdfm_order_by = $instance['pdfm_order_by'];
            $pdfm_order = $instance['pdfm_order'];
            $pdfm_most_top_of = $instance['pdfm_most_top_of'];
            $pdfm_show_date = $instance['pdfm_show_date'];
            $pdfm_date_format_str = $instance['pdfm_date_format_str'];
            $pdfm_date_before_title = $instance['pdfm_date_before_title'];
            $pdfm_open_in_new = $instance['pdfm_open_in_new'];
            $pdfm_ordered_list = $instance['pdfm_ordered_list'];
            $pdfm_no_follow_tag = $instance['pdfm_no_follow_tag'];
        }else{
            //version 1.0
            $pdfm_widget_title = isset( $instance['widget_title'] ) ? $instance['widget_title'] : '';
            $pdfm_category_id = isset( $instance['bsk_pdf_manager_category'] ) ? $instance['bsk_pdf_manager_category'] : 0;
            $pdfm_show_cat_title = isset( $instance[ 'bsk_pdf_manager_show_cat_title' ] ) ? $instance[ 'bsk_pdf_manager_show_cat_title' ] : 'NO';
            $pdfm_order_by = isset( $instance['bsk_pdf_manager_cat_order_by'] ) ? $instance['bsk_pdf_manager_cat_order_by'] : 'title';
            $pdfm_order = isset( $instance['bsk_pdf_manager_cat_order'] ) ? $instance['bsk_pdf_manager_cat_order'] : 'ASC';
            $pdfm_most_top_of = isset( $instance['bsk_pdf_manager_category_top'] ) ? $instance['bsk_pdf_manager_category_top'] : 0;
            $pdfm_open_in_new = isset( $instance['bsk_pdf_manager_open_in_new'] ) ? $instance['bsk_pdf_manager_open_in_new'] : 'NO';
            $pdfm_ordered_list = isset( $instance['bsk_pdf_manager_show_ordered_list'] ) ? $instance['bsk_pdf_manager_show_ordered_list'] : 'NO';
        }
        $widget_pro_tips_array = array( 'Category description', 'Exclude PDF ID',  'Featured image', 'Order by Custom Order', 'Pagination', 'Search bar', 'Disabled widget title in H2 or H3 or H4' );
    ?>
    <div class="bsk-pdfm-widget-setting-container bsk-pdfm-pdfs-widget">
        <?php $this->bsk_pdf_manager_show_pro_tip_box( $widget_pro_tips_array ); ?>
        <p><?php esc_html_e( 'Title', 'bskpdfmanager' ); ?>:<br />
            <input name="<?php echo esc_attr($this->get_field_name( 'pdfm_widget_title' )); ?>" value="<?php echo esc_attr($pdfm_widget_title); ?>" style="width:100%;" />
        </p>
        <p><?php esc_html_e( 'Display widget title in', 'bskpdfmanager' ); ?>: 
            <label style="margin-left: 10px;">
                <select name="<?php echo esc_attr($this->get_field_name( 'pdfm_display_title_in' )); ?>" class="bsk-pdfm-display-title-in" disabled>
                    <option value="h2"<?php if( $pdfm_display_title_in == 'h2' ) echo ' selected'; ?>>H2</option>
                    <option value="h3"<?php if( $pdfm_display_title_in == 'h3' ) echo ' selected'; ?>>H3</option>
                    <option value="h4"<?php if( $pdfm_display_title_in == 'h4' ) echo ' selected'; ?>>H4</option>
                </select>
            </label>
        </p>
        <?php
        $category_id_dropdown_name = $this->get_field_name( 'pdfm_category_id' );
        $no_select_text = '';
        ?>
        <p class="bsk-pdfm-category-dropdown-container"><?php esc_html_e( 'Category', 'bskpdfmanager' ); ?>: 
            <?php echo BSKPDFM_Common_Backend::get_category_dropdown( 
                                                                      $category_id_dropdown_name, 
                                                                      $category_id_dropdown_name,
                                                                      'Select category...',
                                                                      $no_select_text,
                                                                      array( $pdfm_category_id )
                                                                    ); 
            ?>
        </p>
        <?php $checked_str = $pdfm_show_cat_title == 'YES' ? ' checked="checked"' : ''; ?>
        <p>
            <label>
                <input type="checkbox" name="<?php echo esc_attr($this->get_field_name( 'pdfm_show_cat_title' )); ?>" value="YES"<?php echo $checked_str; ?> /> <?php esc_html_e( 'Show category title?', 'bskpdfmanager' ); ?>
            </label>
        </p>
        <p>Exclude PDF of ID in:<br />
            <input name="<?php echo esc_attr($this->get_field_name( 'pdfm_exclude_ids' )); ?>" style="width:100%;" value="" placeholder="Documents' ID list, separated by comma" class="bsk-pdfm-ids-input" disabled />
        </p>
        <p>Order by:&nbsp;
            <select name="<?php echo $this->get_field_name( 'pdfm_order_by' ); ?>">
                <option value="title" <?php if( $pdfm_order_by == 'title' ) echo 'selected'; ?>><?php esc_html_e( 'Title', 'bskpdfmanager' ); ?></option>
                <option value="date" <?php if( $pdfm_order_by == 'date' ) echo 'selected'; ?>><?php esc_html_e( 'Date', 'bskpdfmanager' ); ?></option>
                <option value="order_num" <?php if( $pdfm_order_by == 'order_num' ) echo 'selected'; ?>><?php esc_html_e( 'Custom Order', 'bskpdfmanager' ); ?></option>
            </select>
        </p>
        <p>Order:&nbsp;
            <select name="<?php echo $this->get_field_name( 'pdfm_order' ); ?>">
                <option value="ASC" <?php if( $pdfm_order == 'ASC' ) echo 'selected'; ?>>ASC</option>
                <option value="DESC" <?php if( $pdfm_order == 'DESC' ) echo 'selected'; ?>>DESC</option>
            </select>
        </p>
        <p><?php esc_html_e( 'Most top of', 'bskpdfmanager' ); ?> <input type="number" name="<?php echo esc_attr($this->get_field_name( 'pdfm_most_top_of' )); ?>" value="<?php echo esc_attr($pdfm_most_top_of); ?>" style="width:50px;" min="0" step="1" /> <?php esc_html_e( 'documents will be shown', 'bskpdfmanager' ); ?>&nbsp;&nbsp;<span style="font-style:italic;">( <?php esc_html_e( '0 means not apply', 'bskpdfmanager' ); ?> )</span>
        </p>
        <?php 
        $checked_str = $pdfm_show_date == 'YES' ? ' checked="checked"' : ''; 
        $checked_str_for_date_before = $pdfm_date_before_title == 'YES' ? ' checked="checked"' : '';
        $data_format_str_p_display = $pdfm_show_date == 'YES' ? 'block' : 'none';
        $date_before_title_p_display = $pdfm_show_date == 'YES' ? 'block' : 'none';
        ?>
        <p>
            <label>
                <input type="checkbox" name="<?php echo esc_attr($this->get_field_name( 'pdfm_show_date' )); ?>" value="YES"<?php echo $checked_str; ?> class="bsk-pdfm-widget-show-date" /> <?php esc_html_e( 'Shows date', 'bskpdfmanager' ); ?>?
            </label>
        </p>
        <p class="bsk-pdfm-widget-date-format-p" style="display:<?php echo $data_format_str_p_display; ?>">
            <?php esc_html_e( 'Date format', 'bskpdfmanager' ); ?>:<br />
            <input type="text" name="<?php echo esc_attr($this->get_field_name( 'pdfm_date_format_str' )); ?>" value="<?php echo esc_attr($pdfm_date_format_str); ?>" placeholder="eg: --F d, Y, --d/m/Y, Y-m-d_" style="width: 100%;">
            <span style="display: block; font-style: italic;"><?php esc_html_e( 'leave blank to use default format  d/m/Y', 'bskpdfmanager' ); ?></span>
        </p>
        <p class="bsk-pdfm-widget-date-before-title-p" style="display:<?php echo $date_before_title_p_display; ?>">
            <label>
                <input type="checkbox" name="<?php echo esc_attr($this->get_field_name( 'pdfm_date_before_title' )); ?>" value="YES"<?php echo $checked_str_for_date_before; ?> id="<?php echo esc_attr($this->get_field_id( 'pdfm_date_before_title' )); ?>" /> <?php esc_html_e( 'Shows date before PDF title', 'bskpdfmanager' ); ?>
            </label>
        </p>
        <p>
            <label>
                <input type="checkbox" name="<?php echo esc_attr($this->get_field_name( 'pdfm_show_thumbnail' )); ?>" value="YES"<?php echo $checked_str; ?> class="bsk-pdfm-widget-show-thumbnail"  disabled /> <?php esc_html_e( 'Shows featured image', 'bskpdfmanager' ); ?>?
            </label>
        </p>
        <p class="bsk-pdfm-widget-thumbnail-size-p">
            <?php esc_html_e( 'Featured image size', 'bskpdfmanager' ); ?>:<br />
            <select name="<?php echo esc_attr($this->get_field_name( 'pdfm_show_thumbnail_size' )); ?>" disabled>
            <?php
            $image_sizes = BSKPDFM_Common_Backend::get_image_sizes();
            foreach ( $image_sizes as $size_name => $size_name_dimission )  {
                if ( $size_name_dimission['width'] < 1 || 
                     $size_name_dimission['height'] < 1 ){
                    continue;
                }
                echo '<option value="'.$size_name.'">'.$size_name.'</option>';
            }
            echo '<option value="full">full</option>';
            ?>
            </select>
        </p>
        <p class="bsk-pdfm-widget-thumbnail-with-pdf-title-p">
            <label>
                <input type="checkbox" name="<?php echo esc_attr($this->get_field_name( 'pdfm_thumbnail_with_title' )); ?>" value="YES" id="<?php echo esc_attr($this->get_field_id( 'pdfm_thumbnail_with_title' )); ?>" disabled /> <?php esc_html_e( 'Shows PDF title with featured image', 'bskpdfmanager' ); ?>?
            </label>
        </p>
        <?php

        $checked_str = $pdfm_ordered_list == 'YES' ? ' checked="checked"' : '';
        ?>
        <p>
            <label>
                <input type="checkbox" name="<?php echo esc_attr($this->get_field_name( 'pdfm_ordered_list' )); ?>" value="YES"<?php echo $checked_str; ?> /> <?php esc_html_e( 'Show PDF as ordered list', 'bskpdfmanager' ); ?>?
            </label>
        </p>
        <?php $checked_str = $pdfm_open_in_new == 'YES' ? ' checked="checked"' : ''; ?>
        <p>
            <label>
                <input type="checkbox" name="<?php echo esc_attr($this->get_field_name( 'pdfm_open_in_new' )); ?>" value="YES"<?php echo $checked_str; ?> /> <?php esc_html_e( 'Opens PDF in a new window or tab', 'bskpdfmanager' ); ?>?
            </label>
        </p>
        <?php $checked_str = $pdfm_no_follow_tag == 'YES' ? ' checked="checked"' : ''; ?>
        <p>
            <label>
                <input type="checkbox" name="<?php echo esc_attr($this->get_field_name( 'pdfm_no_follow_tag' )); ?>" value="YES"<?php echo $checked_str; ?> /> <?php esc_html_e( 'Add', 'bskpdfmanager' ); ?> rel="nofollow" <?php esc_html_e( 'tag to document link', 'bskpdfmanager' ); ?>?
            </label>
        </p>
        <p>
            <label>
                <input type="checkbox" name="<?php echo esc_attr($this->get_field_name( 'pdfm_show_pagination' )); ?>" value="YES" disabled /> <?php esc_html_e( 'Show pagination', 'bskpdfmanager' ); ?>?
            </label>
        </p>
        <p>
            <?php esc_html_e( 'Page size', 'bskpdfmanager' ); ?><br />
            <input type="number" name="<?php echo esc_attr($this->get_field_name( 'pdfm_pagination_pdfs_per_page' )); ?>" value="" placeholder="10" style="width: 100%;" disabled>
        </p>
        <?php
        $previous_placeholder = '&#x000AB; '.esc_html__( 'Previous Page', 'bskpdfmanager' );
        $next_placeholder = esc_html__( 'Next Page', 'bskpdfmanager' ).' &#x000BB;';
        ?>
        <p>
            <?php esc_html_e( 'Text for previous anchor', 'bskpdfmanager' ); ?><br />
            <input type="text" name="<?php echo esc_attr($this->get_field_name( 'pdfm_pagination_previous_text' )); ?>" value="" placeholder="<?php echo esc_attr($previous_placeholder); ?>" style="width: 100%;" disabled>
        </p>
        <p>
            <?php esc_html_e( 'Text for next anchor', 'bskpdfmanager' ); ?><br />
            <input type="text" name="<?php echo esc_attr($this->get_field_name( 'pdfm_pagination_next_text' )); ?>" value="" placeholder="<?php echo esc_attr($next_placeholder); ?>" style="width: 100%;" disabled>
        </p>
        <p style="padding: 10px 0 10px 0;"><hr /></p>
        <p>
            <label>
                <input type="checkbox" name="<?php echo esc_attr($this->get_field_name( 'pdfm_show_search_bar_only') ); ?>" value="YES" disabled /> <?php esc_html_e( 'Show search bar only', 'bskpdfmanager' ); ?>?
            </label>
        </p>
        <p>
            <?php esc_html_e( 'Search bar type', 'bskpdfmanager' ); ?><br />
            <select name="<?php echo esc_attr($this->get_field_name( 'pdfm_show_search_bar_type' )); ?>" class="bsk-pdfm-widget-search-bar-type" style="width: 100%;" disabled>
                <option value="KEYWORDS"><?php esc_html__( 'Keywords', 'bskpdfmanager' ); ?></option>
                <option value="YEAR_KEYWORDS"><?php esc_html__( 'Year and Keywords', 'bskpdfmanager' ); ?></option>
            </select>
        </p>
        <p>
            <?php esc_html_e( 'Search bar year range', 'bskpdfmanager' ); ?><br />
            <input type="text" name="<?php echo esc_attr($this->get_field_name( 'pdfm_search_bar_year_range' )); ?>" value="" placeholder="" style="width: 100%;" disabled>
        </p>
        <p>
            <?php esc_html_e( 'Search bar year select order', 'bskpdfmanager' ); ?><br />
            <select name="<?php echo esc_attr($this->get_field_name( 'pdfm_search_bar_year_order' )); ?>" class="bsk-pdfm-search-bar-year-order">
                <option value="DESC">DESC</option>
                <option value="ASC">ASC</option>
            </select>
        </p>
        <p>
            <?php esc_html_e( 'Search bar text input placeholder', 'bskpdfmanager' ); ?><br />
            <input type="text" name="<?php echo esc_attr($this->get_field_name( 'pdfm_search_bar_placeholder' )); ?>" value="" style="width: 100%;" disabled>
        </p>
        <p>
            <?php esc_html_e( 'Search bar option none for category select', 'bskpdfmanager' ); ?><br />
            <input type="text" name="<?php echo esc_attr($this->get_field_name( 'pdfm_search_bar_category_option_none' )); ?>" value="" style="width: 100%;" disabled>
        </p>
        <p>
            <?php esc_html_e( 'Search bar option none for year select', 'bskpdfmanager' ); ?><br />
            <input type="text" name="<?php echo esc_attr($this->get_field_name( 'pdfm_search_bar_year_option_none' )); ?>" value="" style="width: 100%;" disabled>
        </p>
        <p>
            <?php esc_html_e( 'Search bar results clear text', 'bskpdfmanager' ); ?><br />
            <input type="text" name="<?php echo esc_attr($this->get_field_name( 'pdfm_search_bar_results_clear_text' )); ?>" value="" style="width: 100%;" disabled>
        </p>
    </div>
    <?php
    } // end of function
    
    function bsk_pdf_manager_show_pro_tip_box( $tips_array ){
        $tips = implode( ', ', $tips_array );
		$str = 
        '<div class="bsk-pdfm-tips-box">
			<p><b>Tip: </b> The following features only supported in <a href="'.BSKPDFManager::$url_to_upgrade.'" target="_blank">Pro version</a>.</p>
            <p><span class="bsk-pdfm-tips-box-tip">'.esc_html($tips).'</span></p>
		 </div>';
		
		echo $str;
	}
    
} // class
