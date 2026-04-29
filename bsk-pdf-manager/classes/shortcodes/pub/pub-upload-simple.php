<?php

class BSKPDFM_Pub_Upload_Shortcode_Simple extends BSKPDFM_Pub_Upload_Shortcodes {

	function init_shortcode() {

        add_shortcode('bsk-pdfm-pub-upload', array($this, 'bsk_pdfm_pub_upload_fun'));
        
    }

    function bsk_pdfm_pub_upload_fun($atts, $content) {


        global $current_user;
        if ($this->_pub_allow_for_guests || ($this->_pub_allow_for_subscribers && $current_user->ID)) {
            //
        } else {
            return '<p>Please make the option "Allow Front File Uploads for Subscribers and Guests" is enabled in Dashboard → BSK PDF Mngr → Settings → Global File Upload.</p>';
        }

        $atts = shortcode_atts( array(
            'category_id' => '',
            'submit_text' => 'Upload',
        ), $atts );


        ob_start();
        $message = '';
        $message_class = '';
        if (isset($_POST['bsk_pdfm_pub_upload_submit']) && isset($_FILES['bsk_pdfm_pub_upload_file']) && strlen( $_FILES['bsk_pdfm_pub_upload_file']['name'] ) > 0) {

            $categories = $this->_default_categories;
            if (isset($atts['category_id']) && $atts['category_id']) {
                $temp_categories = explode(',', $atts['category_id']);
                if (is_array($temp_categories) && count($temp_categories) > 0) {
                    foreach( $temp_categories as $key => $temp_category ) {
                        if(intval($temp_category) < 1){
                            unset($temp_categories[$key]);
                            continue;
                        }
                        $temp_categories[$key] = intval($temp_category);
                    }
                    if (count($temp_categories) > 0) {
                        $categories = $temp_categories;
                    }
                }
            }

            $upload_result = $this->bsk_pdf_manager_pdf_upload_file($_FILES['bsk_pdfm_pub_upload_file'], $categories);
            if (!$upload_result['success']) {
                $message_class = 'bsk-pdfm-pub-upload-error';
                $message = $upload_result['message'];
            } else {
                $message = 'The file has been successfully uploaded.';
                $message_class = 'bsk-pdfm-pub-upload-success';
            }
        }
        ?>
        <div class="bsk-pdfm-pub-upload-container">
            <?php if ($message) { ?>
            <p class="<?php echo ' ' . $message_class; ?>"><?php echo $message; ?></p>
            <?php } ?>
            <form id="bsk_pdfm_pub_upload_form_ID" method="post" enctype="multipart/form-data" action="">
                <p>
                    <input type="file" name="bsk_pdfm_pub_upload_file" id="bsk_pdfm_pub_upload_file_ID" required />
                </p>
                <p>
                    <input type="submit" name="bsk_pdfm_pub_upload_submit" value="<?php echo $atts['submit_text']; ?>" />
                </p>
            </form>
        </div>
        <?php

        return ob_get_clean();
    }

}
