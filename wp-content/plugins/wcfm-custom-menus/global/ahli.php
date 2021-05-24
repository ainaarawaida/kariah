<?php

add_filter( 'wcfm_menu_dependancy_map', 'wcfmvm_vendor_membership_menu_dependancy_mapluq' );
function wcfmvm_vendor_membership_menu_dependancy_mapluq( $menu_dependency_mapping ) {
    $menu_dependency_mapping['wcfm-ahli-add'] = 'wcfm-ahli';
    return $menu_dependency_mapping;
}


?>