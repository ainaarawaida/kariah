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




?>