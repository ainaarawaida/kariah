<?php

//add new ahli product view
add_action('after_wcfm_products','after_wcfm_products_manage_luq_prod');
function after_wcfm_products_manage_luq_prod(){
	?>
	<script>
	jQuery( document ).ready( function( $ ) {
		
		$("#add_new_product_dashboard").addClass("luqhide");
		$(".wcfm_products_filter_wrap").addClass("luqhide");
		$("[data-tip|='Stock Manager']").addClass("luqhide");

		$(".wcfm_products_limit_label").addClass("luqhide");
		
	});
	</script>
	
	<?php
}


	

?>