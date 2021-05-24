<?php





add_filter( 'jet-engine/forms/post-render/31' , 'ahli_manage_jetluq', 10 ,1 );
//add_filter( 'jet-engine/forms/booking/form-cache' , 'ahli_manage_jetluq', 10 ,1 );

function ahli_manage_jetluq($data){
	
 	return $data ;
}

add_action ('jet-engine-booking/update_vendor' , 'luqformahlimanage');
function luqformahlimanage($member){
  
	if( wcfm_is_vendor() ) {
        global $wpdb;
		$vendor_id   = get_current_user_id();
		$data = ['vendor_id' => $vendor_id , 'cct_status' => $member['cct_status'], 'cct_modified' => date("Y-m-d h:i:s") ]; // NULL value.
		$format = [ NULL ];  // Ignored when corresponding data is NULL, set to NULL for readability.
		$where = [ '_ID' => $member['post_id'] ]; // NULL value in WHERE clause.
		$where_format = [ NULL ];  // Ignored when corresponding WHERE data is NULL, set to NULL for readability.
		$wcfm_ahli_array = $wpdb->update( $wpdb->prefix . 'jet_cct_member', $data, $where ); // Also works in this case.
	}else{
        global $wpdb;
		$data = ['cct_status' => 'pending review' , 'cct_modified' => date("Y-m-d h:i:s") ]; // NULL value.
		$format = [ NULL ];  // Ignored when corresponding data is NULL, set to NULL for readability.
        $where = [ '_ID' => $member['post_id'] ]; // NULL value in WHERE clause.
		$where_format = [ NULL ];  // Ignored when corresponding WHERE data is NULL, set to NULL for readability.
		$wcfm_ahli_array = $wpdb->update( $wpdb->prefix . 'jet_cct_member', $data, $where ); // Also works in this case.

    }
}


?>