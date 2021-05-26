<?php


//add_action( 'jet-engine/forms/luq_row/275', 'luqformahlimanage2',10,1 );
function luqformahlimanage2($field){
	return ;
	if($field['settings']['name'] == 'cct_status'){
		?>
<div class="jet-form-col jet-form-col-12  field-type-text  jet-form-field-container" data-field="test" data-conditional="false"><div class="jet-form-col__start"><div class="jet-form__label">
<span class="jet-form__label-text">test<span class="jet-form__required">*</span></span>
</div></div>
<div class="jet-form-col__end"><input class="jet-form__field text-field " required="required" name="test" id="test" value="<?php echo isset($_GET['_post_id']) ? 'ssss' : '' ; ?>" type="text" data-field-name="test"></div></div>

		<?php
	}
	
}

//add_filter( 'jet-engine/forms/post-render/275' , 'ahli_manage_jetluq', 10 ,1 );
//add_filter( 'jet-engine/forms/booking/form-cache' , 'ahli_manage_jetluq', 10 ,1 );
function ahli_manage_jetluq($data){
	//deb('aaaa');exit();
 	return $data ;
}

add_action ('jet-engine-booking/update_vendor' , 'luqformahlimanage');
function luqformahlimanage($member){
	global $wpdb;
	if(isset($member['_ID']) && $member['_ID'] == ''){
		$sql = "SELECT _ID FROM {$wpdb->prefix}jet_cct_member ORDER BY _ID DESC LIMIT 1";
		$cubaan = $wpdb->get_results( $sql);
		$member['_ID'] = $cubaan['0']->_ID ;
		
	}

	if( wcfm_is_vendor() ) {
		
      
		$vendor_id   = get_current_user_id();
		$data = ['vendor_id' => $vendor_id , 'cct_status' => $member['cct_status'], 'cct_modified' => date("Y-m-d h:i:s") ]; // NULL value.
		$format = [ NULL ];  // Ignored when corresponding data is NULL, set to NULL for readability.
		$where = [ '_ID' => $member['_ID'] ]; // NULL value in WHERE clause.
		$where_format = [ NULL ];  // Ignored when corresponding WHERE data is NULL, set to NULL for readability.
		$wcfm_ahli_array = $wpdb->update( $wpdb->prefix . 'jet_cct_member', $data, $where ); // Also works in this case.
	}else{
        global $wpdb;
		$data = ['cct_status' => 'pending review' , 'cct_modified' => date("Y-m-d h:i:s") ]; // NULL value.
		$format = [ NULL ];  // Ignored when corresponding data is NULL, set to NULL for readability.
        $where = [ '_ID' => $member['_ID'] ]; // NULL value in WHERE clause.
		$where_format = [ NULL ];  // Ignored when corresponding WHERE data is NULL, set to NULL for readability.
		$wcfm_ahli_array = $wpdb->update( $wpdb->prefix . 'jet_cct_member', $data, $where ); // Also works in this case.

    }
}


?>