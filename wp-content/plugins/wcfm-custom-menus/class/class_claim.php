<?php

class luq_class_claim
{
    
	// CREATE MEETING IN ZOOM
	public function get_claim_id_by_memberid($memberid)
	{
        global $wpdb ; 
        $sql = "SELECT * FROM {$wpdb->prefix}jet_cct_claim WHERE member_id = '".$memberid."' ";
		$claim = $wpdb->get_results( $sql);
		return $claim ; 

    }

	
}



?>