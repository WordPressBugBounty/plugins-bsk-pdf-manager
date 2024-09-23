jQuery(document).ready( function($) {
    
    /*
      * PDFs Dropdown
      */
    $(".bsk-pdfm-output-container").on("change", ".bsk-pdfm-pdfs-dropdown", function(){
        var target = $(this).data("target");
		var url = $(this).val();

        target = target == '_blank' ? '_blank' : '_self';
		if( url ){
			window.open( url, target);
		}
    });
    
    $(".bsk-pdfm-output-container").on("click", ".bsk-pdfm-extension-filter-anchor, .bsk-pdfm-tags-filter-anchor", function(){
        
        var output_container = $(this).parents(".bsk-pdfm-output-container");
        var shortcode_type = '';
        var output = '';
        if( output_container.hasClass("shortcode-pdfs") ){
            shortcode_type = 'pdfs';
        }else if( output_container.hasClass("shortcode-category") ){
            shortcode_type = 'category';
        }else{
            return;
        }

        if( output_container.hasClass( 'layout-ul' ) ){
            output =  'ul';
        }else if( output_container.hasClass( 'layout-ol' ) ){
            output = 'ol';
        }else if( output_container.hasClass( 'layout-dropdown' ) ){
            output = 'dropdown';
        }else{
            return;
        }

        //clear error message
        output_container.find(".bsk-pdfm-error-message").remove();
        
        var extension = '';
        var tags_filter_val = '';
    
        /*
         * for extension filter
         */
        if( $(this).hasClass( "bsk-pdfm-extension-filter-anchor" ) ){
            if( $(this).hasClass("active") ){
                return;
            }
            extension = $(this).data("extension");
            $(this).addClass( "bsk-pdfm-just-clicked" );
            output_container.find(".bsk-pdfm-extension-filter-ajax-loader").css("display", "block");
            
            //refresh pagination
            output_container.find(".bsk-pdfm-pagination").find("li").removeClass("active");
        }else if( output_container.find( ".bsk-pdfm-extension-filter-anchor.active" ).length > 0 ){
            extension = output_container.find( ".bsk-pdfm-extension-filter-anchor.active" ).data("extension");
        }
    
         /*
         * for tags filter
         */
        if( $(this).hasClass( "bsk-pdfm-tags-filter-anchor" ) ){
            if( $(this).hasClass("active") ){
                return;
            }
            tags_filter_val = $(this).data("tagid");
            $(this).addClass( "bsk-pdfm-just-clicked" );
            output_container.find(".bsk-pdfm-tags-filter-ajax-loader").css("display", "block");
            
            //refresh pagination
            output_container.find(".bsk-pdfm-pagination").find("li").removeClass("active");
        }else if( output_container.find( ".bsk-pdfm-tags-filter-anchor.active" ).length > 0 ){
            tags_filter_val = output_container.find( ".bsk-pdfm-tags-filter-anchor.active" ).data("tagid");
        }
        
        var ajax_nonce = output_container.find(".bsk-pdfm-" + shortcode_type + "-ajax-nonce").val();
        var action_val = "pdfs_get_" + shortcode_type + "_" + output;
        var data = { action: action_val, layout: output, nonce: ajax_nonce };
        
        //organise ajax parameters
        output_container.find(".bsk-pdfm-shortcode-attr").each(function(index, value ){
            var attr_name = $(this).data("attr_name");
            data[attr_name] = $(this).val();
        });
        
        data['extension'] = extension;
        if( tags_filter_val ){
            data['tags_default'] = tags_filter_val;
        }
    
        /*console.log( data );
        return;*/
        $.post( bsk_pdf_pro.ajaxurl, data, function(response) {
            /*console.log( response );
            return;*/
            var return_data = $.parseJSON( response );
            
            /*
             * process extension filter
             */
            if( output_container.find(".bsk-pdfm-extension-filter-container").length ){
                
                var extension_filter_container = output_container.find(".bsk-pdfm-extension-filter-container");
                extension_filter_container.find(".bsk-pdfm-extension-filter-ajax-loader").css("display", "none");
                
                if( extension_filter_container.find(".bsk-pdfm-just-clicked").length ){
                    
                    extension_filter_container.find( ".bsk-pdfm-extension-filter-anchor" ).removeClass( 'active' );
                    extension_filter_container.find( ".bsk-pdfm-just-clicked" ).addClass( 'active' );
                    extension_filter_container.find( ".bsk-pdfm-extension-filter-anchor" ).removeClass( 'bsk-pdfm-just-clicked' );
                }
            }
            
            /*
             * process tags filter
             */
            if( output_container.find(".bsk-pdfm-tags-filter-container").length ){
                
                var tags_filter_container = output_container.find(".bsk-pdfm-tags-filter-container");
                tags_filter_container.find(".bsk-pdfm-tags-filter-ajax-loader").css("display", "none");
                
                if( tags_filter_container.find(".bsk-pdfm-just-clicked").length ){
                    
                    tags_filter_container.find( ".bsk-pdfm-tags-filter-anchor" ).removeClass( 'active' );
                    tags_filter_container.find( ".bsk-pdfm-just-clicked" ).addClass( 'active' );
                    tags_filter_container.find( ".bsk-pdfm-tags-filter-anchor" ).removeClass( 'bsk-pdfm-just-clicked' );
                }
            }
            
            /*
             * output for pdfs
             */
            if( shortcode_type == 'pdfs' ){
                output_container.find(".bsk-pdfm-date-filter").remove();
                output_container.find(".bsk-pdfm-pagination").remove();
                
                if( output == 'dropdown' ){
                    var dropdown_obj = output_container.find( ".bsk-pdfm-pdfs-dropdown" );
                    dropdown_obj.html( return_data.pdfs );
                    dropdown_obj.css( "display", "block" );
                    output_container.find(".bsk-pdfm-date-filter").remove();
                    
                    if( return_data.error_message ){
                        $( return_data.error_message ).insertBefore( dropdown_obj );
                    }else{
                        $( return_data.date_filter ).insertBefore( dropdown_obj );
                        //desc
                        if( output_container.find(".bsk-pdfm-count-desc-container").length ){
                            output_container.find(".bsk-pdfm-count-desc-container").find( "h3" ).html( return_data.results_desc );
                        }
                    }
                }else if( output == 'ul' || output == 'ol' ){
                    var pdfs_list_container = output_container.find(".bsk-pdfm-pdfs-" + output + "-list");
                    pdfs_list_container.html( "" );
                    if( return_data.error_message ){
                        $( return_data.error_message ).insertBefore( pdfs_list_container );
                    }else{
                        pdfs_list_container.html( return_data.pdfs );
                        $( return_data.date_filter ).insertBefore( pdfs_list_container );
                        $( return_data.pagination ).insertAfter( pdfs_list_container );

                        //desc
                        if( output_container.find(".bsk-pdfm-count-desc-container").length ){
                            output_container.find(".bsk-pdfm-count-desc-container").find( "h3" ).html( return_data.results_desc );
                        }
                    }                    
                }
                
                return;
            }
            
            /*
             * output for category
             */
            if( shortcode_type == 'category' ){
                output_container.find(".bsk-pdfm-category-output").remove();
                
                var obj_insert_before = output_container.find(".bsk-pdfm-category-shortcode-attr");
                if( output_container.find(".bsk-pdfm-credit-link-container").length > 0 ){
                    obj_insert_before = output_container.find(".bsk-pdfm-credit-link-container");
                }
                
                if( output == 'dropdown' ){
                    if( return_data.error_message ){
                        $( return_data.error_message ).insertBefore( obj_insert_before );

                        return;
                    }
                    $( return_data.category_out ).insertBefore( obj_insert_before );

                    //desc
                    if( output_container.find(".bsk-pdfm-count-desc-container").length ){
                        output_container.find(".bsk-pdfm-count-desc-container").find( "h3" ).html( return_data.results_desc );
                    }
                }else if( output == 'ul' || output == 'ol' ){
                    if( return_data.error_message ){
                        $( return_data.error_message ).insertBefore( obj_insert_before );

                        return;
                    }
                    
                    $( return_data.category_out ).insertBefore( obj_insert_before );
                    $( return_data.pagination ).insertBefore( obj_insert_before );

                    //desc
                    if( output_container.find(".bsk-pdfm-count-desc-container").length ){
                        output_container.find(".bsk-pdfm-count-desc-container").find( "h3" ).html( return_data.results_desc );
                    }
                }
                
                return;
            } //end for output for category
            
       }); /* //$.post */
        
    });
    
});
