<?php



add_filter( 'wcfmu_orders_menus', 'wcfmu_orders_menus_luq',100,1 );
function wcfmu_orders_menus_luq($wcfmu_orders_menus){
    unset($wcfmu_orders_menus['shipped']);
    unset($wcfmu_orders_menus['refunded']);
    
    //deb($wcfmu_orders_menus);exit();
    return $wcfmu_orders_menus;
}

add_filter( 'wcfm_is_allow_order_delete', 'wcfm_is_allow_order_delete_luq',10,1 );
function wcfm_is_allow_order_delete_luq(){
    return true ; 
}
add_filter( 'wcfm_is_allow_vendor_order_delete', 'wcfm_is_allow_vendor_order_delete_luq',10,1 );
function wcfm_is_allow_vendor_order_delete_luq(){
    return true ; 
}

add_action('woocommerce_order_status_changed', 'woocommerce_order_status_changed_luq', 10, 3);
function woocommerce_order_status_changed_luq( $this_get_id,  $this_status_transition_from,  $this_status_transition_to ){
    
   
   if($this_status_transition_to == 'completed'){
       global $wpdb;
        //update
        $order_id = wp_get_post_parent_id($this_get_id);
        if($order_id == 0){
            $order_id = $this_get_id ;
        }
        $member['cct_modified'] = date("Y-m-d h:i:s") ;
        $member['cct_status'] = 'publish' ;
        
        $data = $member; // NULL value.
        $format = [ NULL ];  // Ignored when corresponding data is NULL, set to NULL for readability.
        $where = [ 'cct_single_post_id' => $order_id]; // NULL value in WHERE clause.
        $where_format = [ NULL ];  // Ignored when corresponding WHERE data is NULL, set to NULL for readability.
        $wpdb->update( $wpdb->prefix . 'jet_cct_member', $data, $where ); // Also works in this case.

   }
   
   
   
}

add_action('wp_footer', 'checkoutluqjs_orderslist');
function checkoutluqjs_orderslist() {
    global $wpdb, $wp;
    //deb($wp->query_vars);
    if(isset($wp->query_vars['pagename']) && $wp->query_vars['pagename'] == 'kariah-manager' && isset($wp->query_vars['orderslist'])){
                ?>
                <script>

    jQuery( document ).ready( function( $ ) {

        $('.wcfm_orders_filter_wrap').addClass('luqhide');
                       

    });
                </script>
                
                <?php

    }
}






?>

