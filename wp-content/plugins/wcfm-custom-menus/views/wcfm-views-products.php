<?php

//add new ahli product view
add_action('after_wcfm_products','after_wcfm_products_manage_luq_prod');
function after_wcfm_products_manage_luq_prod(){
	$siteurlluq = wcfm_get_endpoint_url('products-manage').'?add_new_ahli=true'  ; 
	
	?>
	<script>
	jQuery( document ).ready( function( $ ) {
		
		var siteurlluq = <?php echo json_encode($siteurlluq) ; ?> ;
		
		$("div.wcfm-container.wcfm-top-element-container").append('<a class="add_new_wcfm_ele_dashboard text_tip" href="'+siteurlluq+'" data-tip="Add New Members Form" data-hasqtip="17" aria-describedby="qtip-7"><span class="wcfmfa fa-cube"></span><span class="text">Add Member Form</span></a>');
		$("#add_new_product_dashboard").addClass("luqhide");
		$(".wcfm_products_filter_wrap").addClass("luqhide");
		$("[data-tip|='Stock Manager']").addClass("luqhide");

		$(".wcfm_products_limit_label").addClass("luqhide");
		
	});
	</script>
	
	<?php
}


	

?>