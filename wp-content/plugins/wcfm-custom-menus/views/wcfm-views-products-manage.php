<?php

global $wp, $WCFM, $wc_product_attributes;

$product_id = $wp->query_vars['wcfm-products-manage'];
$pcategories = get_the_terms( $product_id, 'product_cat' );
if( !empty($pcategories) ) {
	foreach($pcategories as $pkey => $pcategory) {
		$check_categories[] = $pcategory->name;
	}
}

//deb($_GET['add_new_ahli']);exit();
if(isset($_GET['add_new_ahli']) || in_array("Members", $check_categories)){

	//add new parking product
	//##product list filter
	add_filter( 'wcfm_product_types', 'wcfm_product_types_luq_parking', 1190, 1 );
	function wcfm_product_types_luq_parking($data){
		unset($data);
		$data['subscription'] = 'Borang Ahli' ;
		
		
		return $data;
	}
	
	//add new ahli product view
	add_action('after_wcfm_products_manage','after_wcfm_products_manage_luq_parking');
	function after_wcfm_products_manage_luq_parking(){
		?>
		<script>
		
		jQuery( document ).ready( function( $ ) {
			/*
			$( "#product_type" ).on( "change", function() {
		
				if( $( this ).val() == 'tuntutan_khairat'){
					$(".regular_price").addClass("luqhide");
					$("#regular_price").addClass("luqhide");
					$(".sale_price").addClass("luqhide");
					$("#sale_price").addClass("luqhide");
					$(".sales_schedule").addClass("luqhide");
					$("#wcfm_products_manage_form_inventory_head").addClass("luqhide");
					$("#wcfm_products_manage_form_policies_head").addClass("luqhide");
					
					setTimeout(function() {
						$("#wcfm_products_manage_form_wcaddons_head").click();
					}, 1000 );
					
				}else{
					$(".regular_price").removeClass("luqhide");
					$("#regular_price").removeClass("luqhide");
					$(".sale_price").removeClass("luqhide");
					$("#sale_price").removeClass("luqhide");
					$(".sales_schedule").removeClass("luqhide");
					$("#wcfm_products_manage_form_inventory_head").removeClass("luqhide");
					$("#wcfm_products_manage_form_policies_head").removeClass("luqhide");
				}
			
				
			});
			*/
			$("#pro_title").append("<input type='hidden' name='add_new_ahli' value='true' />");
	
			$("div.wcfm-container.wcfm-top-element-container h2").html("Add Members Form");
			
			$( "#catalog_visibility" ).val("visible");
			$( "#is_virtual" ).prop( "checked", true );
			$( "#sold_individually" ).prop( "checked", true );
			$("#is_catalog").addClass("luqhide");
			$("div.wcfm_product_manager_general_fields p.description").addClass("luqhide");
			$("#is_virtual").addClass("luqhide");
			$("#is_downloadable").addClass("luqhide");
			$("#add_new_product_dashboard").addClass("luqhide");
			$("#pro_title").attr("placeholder","Title");
			$("#wp-description-wrap").addClass("luqhide");
	
			$("div.wcfm_add_new_category_box.wcfm_add_new_taxonomy_box").addClass("luqhide");
			$("div.wcfm_product_manager_gallery_fields p.product_tags.wcfm_title.wcfm_full_ele.product_tags_ele").addClass("luqhide");
			$("#product_tags").addClass("luqhide");
			$("div.wcfm_product_manager_gallery_fields p.description.wcfm_full_ele.wcfm_side_tag_cloud.wcfm_fetch_tag_cloud").addClass("luqhide");
			$("div.wcfm_product_manager_gallery_fields p.catalog_visibility.wcfm_title.wcfm_full_ele.catalog_visibility_ele").addClass("luqhide");
			$("#catalog_visibility").addClass("luqhide");
			
			$( "#manage_stock" ).on( "change", function() {
				$("div#wcfm_products_manage_form_inventory_expander.wcfm-content p.backorders").addClass("luqhide");
				$("#backorders").addClass("luqhide");
			});
	
			$("select#stock_status.wcfm-select.wcfm_ele.stock_status_ele option").detach();
			$("select#stock_status.wcfm-select.wcfm_ele.stock_status_ele").append('<option value="instock">In stock</option><option value="outofstock">Out of stock</option>');
			
	
			$("div#wcfm_products_manage_form_inventory_expander.wcfm-content p.sold_individually").addClass("luqhide");
			$("#sold_individually").addClass("luqhide");
	
			$("#wcfm_products_manage_form_attribute_head").addClass("luqhide");
			$("#wcfm_products_manage_form_linked_head").addClass("luqhide");

			$("li.product_cats_checklist_item.checklist_item_15 input.wcfm-checkbox.checklist_type_product_cat").prop( "checked", true );
			$("li.product_cats_checklist_item.checklist_item_15").addClass("luqhide");
				
			
			
		});
		</script>
		
		<?php
		}
	
	
	
	}
	

?>