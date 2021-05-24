   <?php
/**
 * WCFM plugin view
 *
 * WCFM Order Details View
 *
 * @author 		WC Lovers
 * @package 	wcfm/view
 * @version   1.0.0
 */

global $wp, $WCFM, $WCFMmp, $theorder, $wpdb;





add_action('wp_footer', 'ordersdetailsluq_ahlimanagejs');
function ordersdetailsluq_ahlimanagejs() {


?>

	<script>

	jQuery( document ).ready( function( $ ) {
	
		
		$('li strong.wc-item-meta-label:contains("Picture")').parent().addClass("luqhide");
		if($('li strong.wc-item-meta-label:contains("Dependent")').next().text().length){
			var getdependent = $('li strong.wc-item-meta-label:contains("Dependent")').next().text() ;
			$('li strong.wc-item-meta-label:contains("Dependent")').parent().append("<div class='luqhide' id='datadep'>"+getdependent+"</div>")
			var formData = unserialize($('li strong.wc-item-meta-label:contains("Dependent")').next().text()) ;
			//console.log($('li strong.wc-item-meta-label:contains("dependent_repeat_member")').next().text());
			console.log(formData.length);
			var i;
			$('li strong.wc-item-meta-label:contains("Dependent")').next().html("");
			for (i = 0; i < formData.length; i++) {
				$('li strong.wc-item-meta-label:contains("Dependent")').next().append('<br>'+(i +  1)+') Full Name: '+formData[i].d_full_name_member+'<br>&nbsp;&nbsp;&nbsp;&nbsp;IC No: '+formData[i].d_ic_member+'<br>&nbsp;&nbsp;&nbsp;&nbsp;Relation: '+formData[i].d_relation_member);
				console.log(formData[i].d_full_name_member);
				console.log(formData[i].d_ic_member);
				console.log(formData[i].d_relation_member);
			} 
		}

		
		
		$( ".wcfm_order_edit_request" ).on( "click", function() {
		
		
			setTimeout(function() {
				//alert($('li strong.wc-item-meta-label:contains("Dependent")').next().html())
				$('.wc-item-meta-label:contains("Picture")').parent().addClass("luqhide");
				$('li strong.wc-item-meta-label:contains("Dependent")').next().html("");
				
		if($('#datadep').text().length){
			var formData = unserialize($('#datadep').text()) ;
			//console.log($('li strong.wc-item-meta-label:contains("dependent_repeat_member")').next().text());
			console.log(formData.length);
			var i;
			for (i = 0; i < formData.length; i++) {
				$('li strong.wc-item-meta-label:contains("Dependent")').next().append('<br>'+(i +  1)+') Full Name: '+formData[i].d_full_name_member+'<br>&nbsp;&nbsp;&nbsp;&nbsp;IC No: '+formData[i].d_ic_member+'<br>&nbsp;&nbsp;&nbsp;&nbsp;Relation: '+formData[i].d_relation_member);
				console.log(formData[i].d_full_name_member);
				console.log(formData[i].d_ic_member);
				console.log(formData[i].d_relation_member);
			} 
		}

			}, 2000 );
			
	
		
	});

	
	
});
	</script>
	
	<?php


}  

?>






