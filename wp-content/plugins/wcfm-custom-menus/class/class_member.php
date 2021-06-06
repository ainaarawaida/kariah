<?php

class luq_class_member
{
    
	// CREATE MEETING IN ZOOM
	public function get_member_id_by_order_id($order_id)
	{
        global $wpdb ; 
        $getsub =  wcs_get_subscriptions($order_id) ;
        $sql = "SELECT * FROM {$wpdb->prefix}jet_cct_member WHERE subscription_id = '".key($getsub)."' ";
		$member = $wpdb->get_results( $sql);
		return $member ; 

    }

	// CREATE MEETING IN ZOOM
	public function get_total_members_by_vendor($vendor_id)
	{
        global $wpdb ; 
       
        $sql = "SELECT count(*) as count FROM {$wpdb->prefix}jet_cct_member WHERE cct_status = 'publish' AND vendor_id = '".$vendor_id."' ";
		$member = $wpdb->get_results( $sql);
		//deb($member);exit();
		return $member[0]->count ; 

    }

}



?>