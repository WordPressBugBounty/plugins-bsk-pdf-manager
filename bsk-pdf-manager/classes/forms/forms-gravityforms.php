<?php

class BSKPDFM_Forms_GravityForms extends BSKPDFM_Forms_Integration {
		
	public function load_field_integration_settings(){

		add_action( 'gform_field_advanced_settings', array($this, 'bsk_pdfm_gf_field_advanced_settings'), 10, 2 );
        add_action( 'gform_editor_js', array($this, 'bsk_pdfm_forms_gf_render_editor_js') );
	}

	function bsk_pdfm_gf_field_advanced_settings( $position, $form_id ){

        if($position != 50){
            return;
        }

        $form = GFAPI::get_form( $form_id );
        ?>
        <li class="bsk-pdfm-forms-integration-form-field-setting field_setting" style="display:list-item;">
            <label class="section_label"><strong>BSK PDF Manager</strong></label>
            <div class="bsk-pdfm-tips-box">
                <p>This feature is only supported in <a href="https://bannersky.com/bsk-pdf-manager/" target="_blank">Pro version</a>.</p>
            </div>
            <?php $this->bsk_pdfm_forms_integration_form_field_settings(); ?>
        </li>
        <?php
	}

    function bsk_pdfm_forms_gf_render_editor_js(){
        ?>
        <script type='text/javascript'>
            jQuery(document).on("gform_load_field_settings", function(event, field, form){
                var bsk_pdfm_gf_fileld_setting_container = jQuery(".bsk-pdfm-forms-integration-form-field-setting");
                if (field['type'] != 'fileupload'){
					//show the setting container!
					bsk_pdfm_gf_fileld_setting_container.hide();

                    return;
				}
				bsk_pdfm_gf_fileld_setting_container.show();


                //show settings base on enable proberty
                if (field['bsk_pdfm_forms_integration_form_field_enable'] == 'YES'){
                    jQuery(".bsk-pdfm-forms-integrtion-form-field-settings-container").find(".bsk-pdfm-forms-integrtion-form-field-settings").css( "display", "block" );
                    jQuery('.bsk-pdfm-forms-integrtion-form-field-settings-container').find('.bsk-pdfm-forms-integration-form-field-enable').prop('checked', true);

                    if(field['bsk_pdfm_forms_integration_form_field_checked_categories'] != '') {
                        //initialize to set category checkboxes
                        var $checkboxContainer = jQuery('.bsk-pdfm-category-hierarchy-checkbox-container');
                        
                        $checkboxContainer.find('.bsk-pdfm-forms-integrtion-form-field-checkbox-category').prop('checked', false);
                        var valuesArray = field['bsk_pdfm_forms_integration_form_field_checked_categories'].split(',');
            
                        $checkboxContainer.find('.bsk-pdfm-forms-integrtion-form-field-checkbox-category').each(function() {
                            var $checkbox = jQuery(this);
                            var checkboxValue = $checkbox.val();
                            
                            if (valuesArray.indexOf(checkboxValue) !== -1) {
                                $checkbox.prop('checked', true);
                            } else {
                                $checkbox.prop('checked', false);
                            }
                        });
                        
                        //initialize to set hidden text
                        jQuery(".bsk-pdfm-forms-integrtion-form-field-settings-container").find(".bsk-pdfm-forms-integrtion-form-field-checked-category-checkbox-hidden-txt").val( field['bsk_pdfm_forms_integration_form_field_checked_categories'] );
                    }
                }

                //enable property
                function bsk_pdfm_forms_integration_form_gf_enable() {
                    var is_checked = jQuery('.bsk-pdfm-forms-integrtion-form-field-settings-container').find('.bsk-pdfm-forms-integration-form-field-enable').is(":checked");
                    if (is_checked) {
                        field['bsk_pdfm_forms_integration_form_field_enable'] = 'YES';
                    } else {
                        field['bsk_pdfm_forms_integration_form_field_enable'] = '';
                    }
                }
                jQuery('.bsk-pdfm-forms-integrtion-form-field-settings-container').on('click', '.bsk-pdfm-forms-integration-form-field-enable', function() {
                    setTimeout(function() {
                        bsk_pdfm_forms_integration_form_gf_enable();
                    }, 20);
                });


                //set checked category property
                function bsk_pdfm_forms_integration_form_gf_set_settings() {
                    field['bsk_pdfm_forms_integration_form_field_checked_categories'] = jQuery('.bsk-pdfm-forms-integrtion-form-field-settings-container').find('.bsk-pdfm-forms-integrtion-form-field-checked-category-checkbox-hidden-txt').val();
                }
                jQuery('.bsk-pdfm-forms-integrtion-form-field-settings-container').on('click', '.bsk-pdfm-forms-integrtion-form-field-checkbox-category', function() {
                    setTimeout(function() {
                        bsk_pdfm_forms_integration_form_gf_set_settings();
                    }, 20);
                });

            });
        </script>
        <?php
    }
    
    
}


