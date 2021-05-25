<?php

add_filter( 'wcfm_is_allow_order_delete', 'wcfm_is_allow_order_delete_luq',10,1 );
function wcfm_is_allow_order_delete_luq(){
    return true ; 
}
add_filter( 'wcfm_is_allow_vendor_order_delete', 'wcfm_is_allow_vendor_order_delete_luq',10,1 );
function wcfm_is_allow_vendor_order_delete_luq(){
    return true ; 
}


?>

