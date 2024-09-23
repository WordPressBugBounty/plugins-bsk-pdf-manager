<?php

class BSKPDFM_Dashboard_Promote {

    var $_bsk_pdfm_plugin_name = 'BSK PDF Manager Pro';
	var $_bsk_pdfm_plugin_product_id = 212;
	var $_bsk_pdfm_plugin_folder = 'bsk-pdf-manager-pro';
	var $_bsk_pdfm_plugin_main_file = 'bsk-pdf-manager-pro.php';
	var $_bsk_pdfm_plugin_home_url = 'https://www.bannersky.com/';
	var $_bsk_pdfm_plugin_product_details_page_url = 'https://www.bannersky.com/bsk-pdf-manager/';
	var $_bsk_pdfm_plugin_author = 'BannerSky.com';

    var $_remote_purchase_history_page = 'https://www.bannersky.com/purchase-history/';
    var $_remote_license_policy_page = 'https://www.bannersky.com/plugin-licensing/';
	var $_remote_account_page = 'https://www.bannersky.com/your-account/';
    
    var $_bsk_pdfm_plugin_base_url = '';
	var $_bsk_pdfm_plugin_slug = 'bsk-pdf-manager-pro';
	var $plugin_slug_for_action = '';

    var $_bsk_pdfm_free_promote_data_option = '_bsk_pdfm_free_promote_data_';
    var $_bsk_pdfm_free_promote_data_dismissed_prefix = '_bsk_pdfm_free_promote_data_dismissed_prefix_';
	
	public function __construct() {
		
		$this->plugin_slug_for_action = str_replace( '-', '_', $this->_bsk_pdfm_plugin_slug );
		$this->_bsk_pdfm_plugin_base_url = admin_url( 'admin.php?page='.BSKPDFM_Dashboard::$_bsk_pdfm_pro_pages['base'] );
		
		if ( is_admin() ) {
			add_action( 'admin_notices', array( $this, 'bsk_pdfm_admin_notice_fun' ) );
			add_action( "wp_ajax_bsk_pdfm_free_dismiss_promote_" . $this->plugin_slug_for_action, array( $this, 'bsk_pdfm_notice_dismiss_promote_fun' ) );
		}
        
        add_action( 'bsk_pdfm_free_schedule_check_promote_weekly', array( $this, 'bsk_pdfm_free_schedule_check_promote_weekly_fun') );
        if ( function_exists('wp_next_scheduled') && function_exists('wp_schedule_event') ) {
			if ( ! wp_next_scheduled( 'bsk_pdfm_free_schedule_check_promote_weekly' ) ) {
				wp_schedule_event( time(), 'weekly', 'bsk_pdfm_free_schedule_check_promote_weekly' );
			}
		}

	}
	
	function bsk_pdfm_admin_notice_fun(){
		
		$promote_data = get_option( $this->_bsk_pdfm_free_promote_data_option, false );
        if ( ! $promote_data || ! is_array( $promote_data ) || ! isset( $promote_data['id'] ) || $promote_data['id'] < 1 ) {
            return;
        } 

        $dismissed = get_option( $this->_bsk_pdfm_free_promote_data_dismissed_prefix . 'BSKPDFM_PROMOTE_' . $promote_data['id'], false );
        if ( $dismissed ) {
            return;
        }
		
		$promote_message = $promote_data['promote'];
		$promote_message = str_replace( '#SCOUPON#', '<span class="bskpdfm_promote_head">', $promote_message );
		$promote_message = str_replace( '#ECOUPON#', '</span>', $promote_message );
		$promote_message = str_replace( '#SDISCOUNT#', '<span class="bskpdfm_promote_head">', $promote_message );
		$promote_message = str_replace( '#EDISCOUNT#', '</span>', $promote_message );

		$promote_end_date_y = substr( $promote_data['end_date'], 0, 4 );
		$promote_end_date_m = intval( substr( $promote_data['end_date'], 5, 2 ) ) - 1;
		$promote_end_date_d = intval( substr( $promote_data['end_date'], 8, 2 ) );
		$months_string = array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );
		$promote_end_date_formated = $months_string[$promote_end_date_m] . ' ' . $promote_end_date_d . ', ' . $promote_end_date_y;
		?>
		<div class='notice notice-info' style='padding:15px; position:relative;' id='bsk_pdfm_dashboard_message_<?php echo $this->plugin_slug_for_action; ?>'>
			<a href="javascript:void(0);" onclick="bsk_pdfm_dashboard_promote_<?php echo $this->plugin_slug_for_action; ?>();" style='float:right;'><?php esc_html_e( 'Dismiss', 'bskpdfmanager' ); ?></a>
			<?php echo $promote_message; ?>
			<p>By the end of <span class="bskpdfm_promote_end_date"><?php echo $promote_end_date_formated; ?></span>.</p>
			<p>Click <a href="<?php echo $this->_bsk_pdfm_plugin_product_details_page_url; ?>" target="_blank"><?php echo $this->_bsk_pdfm_plugin_product_details_page_url; ?></a> to save your money!</p>
		</div>
		<script type="text/javascript">
			function bsk_pdfm_dashboard_promote_<?php echo $this->plugin_slug_for_action; ?>(){
				jQuery("#bsk_pdfm_dashboard_message_<?php echo $this->plugin_slug_for_action; ?>").slideUp();
				jQuery.post( ajaxurl, { action: "bsk_pdfm_free_dismiss_promote_<?php echo $this->plugin_slug_for_action; ?>", id: "BSKPDFM_PROMOTE_<?php echo $promote_data['id']; ?>" }, function( response ){
					console.log( response );
				});
			}
		</script>
		<?php
	}
	
	function bsk_pdfm_notice_dismiss_promote_fun() {

        $promote_id = sanitize_text_field( $_POST["id"] );
        update_option( $this->_bsk_pdfm_free_promote_data_dismissed_prefix . $promote_id, true );

		wp_die( $promote_id );
    }

	private function bsk_pdfm_get_promote_data() {

		$return_data = array( 'success' => false, 'message' => 'Unknown error !' );
		
		$api_params = array( 
			'bskddaction' 	=> 'get_promote',
			'product_id' 	=> $this->_bsk_pdfm_plugin_product_id,
            'site'          => site_url(),
			
		);

        $response = wp_remote_get( add_query_arg( $api_params, $this->_bsk_pdfm_plugin_home_url ),
                                   array( 'timeout' => 15 ) );

		if ( is_wp_error( $response ) ){
			$return_data['message'] = $response->get_error_message();

            return $return_data;
		}
        $response_body = wp_remote_retrieve_body( $response );
        if ( trim( $response_body ) == "" ) {
            $return_data['message'] = 'Null data from wp_remote_retrieve_body.';

            return $return_data;
        }
        
		// decode the license data
		$promote_data = json_decode( $response_body, true  );
        if ( ! isset( $promote_data['id'] ) || $promote_data['id'] < 1 ||
			 ! isset( $promote_data['success'] ) || ! $promote_data['success'] ||
             ! isset( $promote_data['promote'] ) || trim( $promote_data['promote'] ) == '' ) {
            $return_data['message'] = 'No promote data found';

            return $return_data;
        }

		$return_data['success'] = true;
        $return_data['message'] = 'Succeed to get promote data.';
        $return_data['data'] = $promote_data;

        return $return_data;
	}
    
    function bsk_pdfm_free_schedule_check_promote_weekly_fun(){

		$return_data = $this->bsk_pdfm_get_promote_data();
        if ( ! $return_data || ! $return_data['success'] ) {
            return;
        }
		$promote_data = $return_data['data'];

        //save promote data to db
        update_option( $this->_bsk_pdfm_free_promote_data_option, $promote_data );
    }
    
}
