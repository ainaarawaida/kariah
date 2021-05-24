jQuery(document).ready(function()
{
	"use strict";
	jQuery('.wcoa_timepicker').selectWoo();
});
function wcoa_init_time_picker()
{
	jQuery('.wcoa_timepicker').each(function(index)
	{
		const max = jQuery(this).data('max-time');
		let timepicker = jQuery(this).pickatime({
			interval: 10,
			format: jQuery(this).data('format'),
			//max: jQuery(this).data('max-time'),
			//min: jQuery(this).data('data-minimum-offset')
			disable: {from: true}
		});
		
		/* let tpicker  = timepicker.pickatime('picker');
		tpicker.set('min', timepicker.get('now')); */
		//tpicker.set('select', timepicker.get('now'));
	});
}