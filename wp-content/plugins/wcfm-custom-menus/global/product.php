<?php



add_filter('wcfm_product_save_pending_redirect','after_wcfm_product_submit_redirect');
add_filter('wcfm_product_save_publish_redirect','after_wcfm_product_submit_redirect');
function after_wcfm_product_submit_redirect($redirect_to){
  global $WCFM;
  //deb('aaa');deb($_GET);exit();
  $wcfm_page = get_wcfm_page();
  parse_str($_POST['wcfm_products_manage_form'], $datax) ; 
   // deb($_POST['wcfm_products_manage_form']);exit();
 if(isset($datax['add_new_ahli'])){
    $redirect_to = $redirect_to . "?add_new_ahli=true" ;
 }
  
  return $redirect_to;
}




  add_action('wp_footer', 'productluqjs');
  function productluqjs() {
   global $wp;
    
    if(isset($wp->query_vars['post_type']) && $wp->query_vars['post_type'] == 'product'){

      
      ?>
      
      <script>

      jQuery( document ).ready( function( $ ) {
      
        $(".woocommerce-product-gallery").addClass("luqhide");
        $("div.product-main div.product-gallery").addClass("luqhide");
        
        $("div.summary.entry-summary").css('width','100%');
        
        var cct_status  =  $('#cct_status option:selected').text() ; 
                    $('#cct_status').parent().parent().addClass("luqhide") ; 
                //$('#cct_status').parent().append(cct_status);

        
        //$('button.single_add_to_cart_button.button.alt').addClass("luqhide");
        $('.add_member_submit').addClass("luqhide");
        $( "form.cart button.single_add_to_cart_button" ).click(function(event) {
          var $clone = $('div.summary.entry-summary form.cart').children().clone(true,true);
          $('button.jet-form__submit.submit-type-reload.add_member_submit').before($clone);

          getsubtotal = $('li.wc-pao-subtotal-line p.price span.amount').html();
        
          $('button.jet-form__submit.submit-type-reload.add_member_submit').before("<input type='text' name='subtotal' value='"+getsubtotal+"'>");


            $( ".add_member_submit" ).click();
            event.preventDefault();
          });

          
          $('#tab-title-wcfm_policies_tab.wcfm_policies_tab_tab a').text("Kariah Policies") ; 
        
        
        //clode
        
        //var $clone = $('div.summary.entry-summary form.cart').children().clone(true,true);
        
      //$('button.jet-form__submit.submit-type-reload.add_member_submit').before($clone);
        //$('form.cart div.wc-pao-addon-container').addClass("luqhide");
      
        
        
      });
      </script>
      
      <?php
    }

  }



  add_action( 'woocommerce_single_product_summary', 'woocommerce_single_product_summary_luq_product' );
  function woocommerce_single_product_summary_luq_product(){
    global $product;
    
    $pcategories = get_the_terms($product->get_ID(), 'product_cat');
    if( !empty($pcategories) ) {
      foreach($pcategories as $pkey => $pcategory) {
        $check_categories[] = $pcategory->name;
      }
    }else{
      $check_categories = array();
    }

    if(in_array("Members", $check_categories)){
     
      echo do_shortcode('[jet_engine component="forms" _form_id="275"]') ; 



    }
      





  }






add_action ('jet-engine-booking/member' , 'luqformproduct');
function luqformproduct(){

  $getlink = explode('/', $_POST['_jet_engine_refer']);
  if(!in_array("product", $getlink)){
   return;
  }
 
  global $woocommerce;
  $str = filter_var($_POST['subtotal'], FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
  //deb($_POST);exit();
  WC()->cart->add_to_cart( $_POST['post_id'], $quantity = 1, $variation_id = 0, $variation = array(), $cart_item_data = $_POST  ) ;
  //deb(WC()->cart);exit();
  wp_redirect(wc_get_checkout_url());
  exit();

  foreach($_POST AS $key => $val){

  }

  



 
  deb((float)$str);exit();
  preg_match_all('!\d+!', $str, $matches);
  print_r($matches);
  deb($_POST);exit();
}


function iconic_display_engraving_text_cart( $item_data, $cart_item ) {
//deb($cart_item);exit();
	

	$custom_data[] = array(
		'key'     => __( 'Full Name', 'iconic' ),
		'value'   => wc_clean( $cart_item['full_name_member'] ),
		'display' => ''
	);
  $custom_data[] = array(
		'key'     => __( 'New IC', 'iconic' ),
		'value'   => wc_clean( $cart_item['new_ic_member'] ),
		'display' => ''
	);
  
  

	return $item_data = array_merge( $item_data, $custom_data );;
}

//add_filter( 'woocommerce_get_item_data', 'iconic_display_engraving_text_cart', 10, 2 );


?>