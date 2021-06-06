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







add_action('wp_footer', 'ordersdetailsluq_ahlimanagejs');
function ordersdetailsluq_ahlimanagejs() {
	global $wp, $WCFM, $WCFMmp, $theorder, $wpdb;
	if(isset($wp->query_vars['wcfm-orders-details'])){

		//get subscription link
		$getsub =  wcs_get_subscriptions($wp->query_vars['wcfm-orders-details']) ;
		$subsription_link = get_wcfm_custom_menus_url( 'subscriptions-manage' ).key($getsub) ;
		

?>

	<script>

		var subsription_link = <?php echo json_encode($subsription_link) ; ?> ;
	jQuery( document ).ready( function( $ ) {
	
		//td.subscription-id a betulkan link subscription 
		//link_id = $('td.subscription-id a').text();
		//link_id = link_id.replace("#", "");
		$('td.subscription-id a').attr('href',subsription_link);
		$('td.subscription-actions a.button.view').attr('href',subsription_link);
		
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

}  

?>






