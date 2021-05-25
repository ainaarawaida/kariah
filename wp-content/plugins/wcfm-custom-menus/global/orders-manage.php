<?php

add_filter( 'wcfm_sv_product_types' , 'dsadsadas' , 1 , 1 ) ; 
function dsadsadas(){
    return array( 'simple', 'variable', 'subscription' ) ; 
}
//add_filter( 'wcfm_sv_product_types', array( 'simple', 'variable', 'subscription' ) );

add_filter( 'wcfm_variable_product_types' , 'wcfm_variable_product_typesdsadsadas' , 1 , 1 ) ; 
function wcfm_variable_product_typesdsadsadas(){
    return array( 'simple','variable', 'variable-subscription', 'pw-gift-cards', 'subscription' ) ; 
}


add_filter( 'wcfm_products_args' , 'wcfm_products_argssdsadsadas' , 1 , 1 ) ; 
function wcfm_products_argssdsadsadas(){
    $args = array(
        'posts_per_page'   => 50,
        'offset'           => 0,
        'category'         => '',
        'category_name'    => '',
        'orderby'          => 'date',
        'order'            => 'DESC',
        'include'          => '',
        'exclude'          => '',
        'meta_key'         => '',
        'meta_value'       => '',
        'post_type'        => 'product',
        'post_mime_type'   => '',
        'post_parent'      => '',
        //'author'	   => get_current_user_id(),
        'post_status'      => array('publish', 'private'),
        'suppress_filters' => 0 
    );

    return $args ; 
}

add_action( 'wcfm_orders_manage_after_products_list', 'wcfm_orders_manage_after_products_list_luq' );
function wcfm_orders_manage_after_products_list_luq(){
    ?>
<p class="wcfm_om_payment_details wcfm_title wcfm_ele"><strong>Custom Total Price (RM)</strong></p>
<input type="number"  name="total_price" class="wcfm-text wcfm_ele" value="" placeholder="Leave empty for default product price">
    <?php

}

//woocommerce_saved_order_items
add_action( 'woocommerce_update_order_item', 'post_updatedluq1', 120, 2);
function post_updatedluq1($id , $data){
  
 
    if(isset($_POST['controller']) && $_POST['controller'] == 'wcfm-orders-manage' && isset($_POST['wcfm_orders_manage_form']) && isset($_POST['action']) && $_POST['action'] == 'wcfm_ajax_controller'){
        parse_str($_POST['wcfm_orders_manage_form'], $dataarray) ;
        
        if(isset($dataarray['total_price']) && $dataarray['total_price'] != ''){
            wc_update_order_item_meta($id, '_line_subtotal', '-');
            wc_update_order_item_meta($id, '_line_total', '-');
            update_metadata( 'post', $data->get_order_id(), '_order_total', $dataarray['total_price'] );
            //deb('aaaa');exit();
            /*
            global $wpdb;
            //deb($data->get_order_id());
            $datax = ['item_total' => $dataarray['total_price'] ]; // NULL value.
            $where = [ 'order_id' => $data->get_order_id() ]; // NULL value in WHERE clause.
            $wcfm_ahli_array = $wpdb->update( $wpdb->prefix . 'wcfm_marketplace_orders', $datax, $where ); // Also works in this case.
            //deb($wcfm_ahli_array);deb('aaaa');
           
           
            $data = ['item_total' => $dataarray['total_price'] ]; // NULL value.
            $where = [ 'order_commission_id' => $data->get_order_id() ]; // NULL value in WHERE clause.
            $wcfm_ahli_array = $wpdb->update( $wpdb->prefix . 'wcfm_marketplace_orders_meta', $data, $where ); // Also works in this case.
            */
        }
    
    }
    
  
 
}


//woocommerce_saved_order_items
add_action( 'wcfmmp_order_item_processed', 'wcfm_manual_order_processedluq1', 120, 6);
function wcfm_manual_order_processedluq1($commission_id, $order_id, $order, $vendor_id, $product_id, $order_item_id){
    if(isset($_POST['controller']) && $_POST['controller'] == 'wcfm-orders-manage' && isset($_POST['wcfm_orders_manage_form']) && isset($_POST['action']) && $_POST['action'] == 'wcfm_ajax_controller'){
        parse_str($_POST['wcfm_orders_manage_form'], $dataarray) ;
        if(isset($dataarray['total_price']) && $dataarray['total_price'] != ''){
            
            global $wpdb;
            //deb($data->get_order_id());
            $datax = ['item_total' => $dataarray['total_price'] ]; // NULL value.
            $where = [ 'order_id' => $order_id ]; // NULL value in WHERE clause.
            $wcfm_ahli_array = $wpdb->update( $wpdb->prefix . 'wcfm_marketplace_orders', $datax, $where ); // Also works in this case.
           
        
        
            $datax = ['value' => $dataarray['total_price'] ]; // NULL value.
            $where = [ 'order_commission_id' => $commission_id,  'key' => 'gross_total' ]; // NULL value in WHERE clause.
            $wcfm_ahli_array = $wpdb->update( $wpdb->prefix . 'wcfm_marketplace_orders_meta', $datax, $where ); // Also works in this case.
        }
        
        
    }
 
}

add_filter( 'wcfm_is_allow_order_details_admin_fee', 'wcfm_is_allow_order_details_admin_feeluq' );
function wcfm_is_allow_order_details_admin_feeluq($data){
    return false ;
}

add_filter( 'wcfm_is_allow_order_details_commission_breakup_gross_earning', 'wcfm_is_allow_order_details_commission_breakup_gross_earningluq' );
function wcfm_is_allow_order_details_commission_breakup_gross_earningluq($data){
    return false ;
}

//add_filter( 'wcfm_is_allow_gross_total', 'wcfm_is_allow_gross_totalluq' );
function wcfm_is_allow_gross_totalluq($data){
    return false ;
}

/*
add_action( 'shutdown', function(){
    foreach( $GLOBALS['wp_actions'] as $action => $count )
        printf( '%s (%d) <br/>' . PHP_EOL, $action, $count );

});

*/

/*
add_action( 'shutdown', function(){
    foreach( $GLOBALS['wp_actions'] as $action => $count )
        printf( '%s (%d) <br/>' . PHP_EOL, $action, $count );

});
*/


 


?>