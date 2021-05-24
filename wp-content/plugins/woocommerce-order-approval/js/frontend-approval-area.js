let wcoa_current_order_status = "";
let wcoa_is_performing_check = false;
jQuery(document).ready(function()
{
	"use strict";
	wcoa_current_order_status = wcoa.order_status;
	jQuery(document).on('click', '#cancel_button', wcoa_on_cancel_order);
	
	//if(wcoa.is_order_received_page == 'no') //no need anymore
		jQuery('#all_button_container').show();
	
	setInterval(wcoa_check_order_status_change, 1800);
});

function wcoa_on_cancel_order(event)
{
	//event.preventDefault();

	return confirm(wcoa.confirm_text);
}
function wcoa_check_order_status_change(event)
{
	if(wcoa_is_performing_check)
		return;
	
	wcoa_is_performing_check = true;
	
	var formData = new FormData();
	formData.append('action', 'wcoa_check_order_status');	
	formData.append('order_id', wcoa.order_id); 			
	jQuery.ajax({
			url: wcoa.ajaxurl,
			type: 'POST',
			data: formData,
			async: true,
			success: function (data) 
			{
				if(data != "error" && wcoa_current_order_status != data)
				{
					wcoa_current_order_status = data;
					wcoa_reload_approval_area();
				}
				else
				{
					//UI
					jQuery('#all_button_container').show();
					wcoa_is_performing_check = false;
				}
			},
			error: function (data) 
			{
				wcoa_is_performing_check = false;
			},
			cache: false,
			contentType: false,
			processData: false
		}); 	
}
function wcoa_reload_approval_area()
{
	var formData = new FormData();
	formData.append('action', 'wcoa_reload_approval_area');	
	formData.append('order_id', wcoa.order_id); 			
	jQuery.ajax({
			url: wcoa.ajaxurl,
			type: 'POST',
			data: formData,
			async: true,
			success: function (data) 
			{
				if(data != "error")
				{
					jQuery('#wcoa_status_timeline_container').html(data);
				}
				wcoa_is_performing_check = false;
				//UI
				jQuery('#all_button_container').show();
	
			},
			error: function (data) 
			{
				wcoa_is_performing_check = false;
			},
			cache: false,
			contentType: false,
			processData: false
		}); 	
}