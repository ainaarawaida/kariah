<?php

add_filter( 'woocommerce_loop_add_to_cart_link', 'ts_replace_add_to_cart_button', 10, 2 );
function ts_replace_add_to_cart_button( $button, $product ) {
if (is_product_category() || is_shop()) {
$button_text = __("View Product", "woocommerce");
$button_link = $product->get_permalink();
$button = '<a class="button" href="' . $button_link . '">' . $button_text . '</a>';
//<a href="?add-to-cart=30" data-quantity="1" class="button product_type_subscription add_to_cart_button ajax_add_to_cart" data-product_id="30" data-product_sku="" aria-label="Add “5w4fr” to your cart" rel="nofollow">Sign up now</a>
return $button;
}
}

add_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end_luq', 10 );
function woocommerce_output_content_wrapper_end_luq(){

  
  ?>
        
  <script>
 
  jQuery( document ).ready( function( $ ) {
    
    //$('a.woocommerce-LoopProduct-link.woocommerce-loop-product__link img.woocommerce-placeholder.wp-post-image').attr("href");
    
   // $('a.button.product_type_subscription').attr("href","dfdf");

  });
        </script>
        
  <?php

}


?>