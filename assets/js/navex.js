jQuery(document).ready( function() {
    const $ = jQuery

    jQuery("#submit_form_data").click( function(e) {
        e.preventDefault(); 
        $(this).attr('disabled', true);
 
        nonce = jQuery(this).attr("data-nonce");
        orderId = jQuery(this).attr("data-order_id");

        const form = $('#NavexAdvancedOptions').find('input:not([type="hidden"]), select, textarea');
        var formData = new FormData();
        const formInput = form.serializeArray()
        /*for (let key in formInput) {
            formData.append(formInput[key]['name'], formInput[key]['value'])
        }*/

        jQuery.ajax({
            type:"POST",
            url: ajaxurl,
            data: {
                action: "navex_post_order",
                nonce: nonce,
                orderId: orderId,
                formData: formInput
            },
            success:function(data){
                $("#submit_form_data").removeAttr('disabled');
                $('#navex_export_btn').attr('disabled', false);
                $('#navex_export_btn').attr('href', data);
            },
            error: function(errorThrown){
                alert('Failed to update this order');
                $(this).attr('disabled', false);
            } 
        });
     });


    jQuery("#navex_delete_btn").click( function(e) {
       e.preventDefault(); 

       nonce = jQuery(this).attr("data-nonce");
       orderId = jQuery(this).attr("data-order_id");
       orderCode = jQuery(this).attr("data-order_code");
       jQuery.ajax({
            type:"POST",
            url: ajaxurl,
            data: {
                action: "navex_delete_order",
                nonce: nonce,
                orderId,
                code: orderCode
            },
            success:function(data){
                $("#navex_delete_btn").attr('disabled', true);
                $("#submit_form_data").removeAttr('disabled');
                $('#navex_export_btn').attr('disabled', true);
                $('#navex_export_btn').attr('href', '');
            },
            error: function(errorThrown){
                alert('Failed to update this order');
                $(this).attr('disabled', false);
            } 
        });
    });
 });
 