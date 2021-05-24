jQuery(document).ready(function()
{
	"use strict";
	wcoa_init_time_picker();
	jQuery(document).on('click','.master_option', wcoa_manage_master_option);
	
	//init
	wcoa_manage_master_option(null);
	jQuery('.wcoa_select2').select2(
		{
		  width: "resolve",
		  closeOnSelect: true,
		});
});
function wcoa_init_time_picker()
{
	jQuery('.timepicker').pickatime({
		interval: 10,
		format: 'H:i'
	});
	
}
function wcoa_manage_master_option(event)
{
	const transition_time = event == null ? 0 : 300;
	//console.log(jQuery('.master_option'));
	jQuery('.master_option').each(function(index, elem)
	{
		//console.log(jQuery(elem).is(':checkbox'));
		if(jQuery(elem).is(':checkbox'))
		{
			//const related_item_id = jQuery(elem).data('related-id');
			const hide_process_result = elem.checked ? jQuery(".master_related_"+jQuery(elem).data('related-id')).fadeIn(transition_time) : jQuery(".master_related_"+jQuery(elem).data('related-id')).fadeOut(transition_time);
		}
	});
}