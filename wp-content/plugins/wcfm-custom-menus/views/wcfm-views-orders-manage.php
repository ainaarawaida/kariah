<?php


add_action('wp_footer', 'ordermanageluq_ahlimanagejs');
function ordermanageluq_ahlimanagejs() {


?>

	<script>

	jQuery( document ).ready( function( $ ) {
	
	$("#wcfm_om_payment_head").addClass("luqhide");
    $("#wcfm_om_shipping_head").addClass("luqhide");
    setTimeout(function() {
        $("#wcfm_customer_address_head").click();
					}, 1000 );



    /*
	$(window).on('load', function() {
        $("#wcfm_customer_address_head").click();
    });

    */
	
	//clode
	
	//var $clone = $('div.summary.entry-summary form.cart').children().clone(true,true);
	
	//$('button.jet-form__submit.submit-type-reload.add_member_submit').before($clone);
	//$('form.cart div.wc-pao-addon-container').addClass("luqhide");
	
	
	
});
	</script>
	
	<?php


}



?>