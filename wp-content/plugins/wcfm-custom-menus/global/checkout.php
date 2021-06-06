<?php


//##
//####//hide woocommerce checkout field
add_filter( 'woocommerce_checkout_fields' , 'remove_company_name' );
function remove_company_name( $fields ) {
   
    $full_name = '' ;
    $mobile_phone_member = '' ;
    $email_member = '' ;
    foreach(WC()->cart->cart_contents AS $key => $val){
        if(isset($val['full_name_member'])){
            $full_name = isset($val['full_name_member']) ? $val['full_name_member'] : '' ;
        }
        if(isset($val['mobile_phone_member'])){
            $mobile_phone_member = isset($val['mobile_phone_member']) ? $val['mobile_phone_member'] : '' ;
        }
        if(isset($val['email_member'])){
            $email_member = isset($val['email_member']) ? $val['email_member'] : ''  ;
        }
      
    }
    
   //deb( WC()->cart->cart_contents);
    //deb($fields['billing']);exit();
	$fields['billing']['billing_first_name']['placeholder'] = 'Name';
    $fields['billing']['billing_first_name']['class'][0] = 'form-row-wide';
$fields['billing']['billing_first_name']['label'] = 'Name';

$fields['billing']['billing_first_name']['default'] = $full_name;
$fields['billing']['billing_phone']['default'] = $mobile_phone_member;
$fields['billing']['billing_email']['default'] = $email_member;

	unset($fields['billing']['billing_company']);
     unset($fields['billing']['billing_company']);
	 unset($fields['billing']['billing_country']);
	 unset($fields['billing']['billing_address_1']);
	 unset($fields['billing']['billing_address_2']);
	 unset($fields['billing']['billing_city']);
	 unset($fields['billing']['billing_state']);
	 unset($fields['billing']['billing_postcode']);
     return $fields;
}
add_filter( 'woocommerce_billing_fields' , 'ced_remove_billing_fields' );
function ced_remove_billing_fields( $fields ) {
         unset($fields['billing_last_name']);
         return $fields;
}


add_action( 'woocommerce_after_checkout_validation', 'woocommerce_after_checkout_validation_luq_member', 10, 2);
function woocommerce_after_checkout_validation_luq_member( $fields, $errors ){
    global $wp,$woocommerce,$wpdb;

    foreach($woocommerce->cart->get_cart() as $key => $val ) {
        $_product = $val['data'];
    
        $pcategories = get_the_terms($_product->id, 'product_cat');
        if( !empty($pcategories) ) {
            foreach($pcategories as $pkey => $pcategory) {
                $check_categories[] = $pcategory->name;
            }
        }
       
        if($val['new_ic_member']){
            $member['new_ic_member'] = $val['new_ic_member'] ;
            //wc_add_order_item_meta($item_id,'Email',$member['email_member']);  
        }

    }   
    
    if(!in_array("Members", $check_categories)){
        return ; 
    }
    $vendor_id   = wcfm_get_vendor_id_by_post( $_product->id );
    $tablename = $wpdb->prefix . "jet_cct_member";
    $sql = "SELECT * FROM ".$tablename." WHERE new_ic_member = '".$member['new_ic_member']."' AND vendor_id = '".$vendor_id."'" ;
    $wcfm_ahli_array = $wpdb->get_results( $sql , ARRAY_A );
    
   
    if($wcfm_ahli_array){
        $errors->add( 'validation', 'Main IC Number exist. '.$member['new_ic_member'].' Please contact Kariah Manager for registration' );
    }
   
   
}



add_action('woocommerce_new_order_item','wdm_add_values_to_order_item_metacheckout_luq',1,3);
if(!function_exists('wdm_add_values_to_order_item_metacheckout_luq'))
{
  function wdm_add_values_to_order_item_metacheckout_luq($item_id, $values, $order_id)
  {
      

        global $wp,$woocommerce,$wpdb;
        $checkposttype = get_post_type( $order_id) ;
        if($checkposttype != 'shop_order' && $checkposttype != 'shop_subscription'){
            return ; 
        }

        foreach($woocommerce->cart->get_cart() as $key => $val ) {
            $_product = $val['data'];
        
            $pcategories = get_the_terms($_product->id, 'product_cat');
            if( !empty($pcategories) ) {
            foreach($pcategories as $pkey => $pcategory) {
                $check_categories[] = $pcategory->name;
            }
            }
        }   

        if(!in_array("Members", $check_categories)){
            return ; 
        }

        
      



        if($checkposttype == 'shop_order'){
            
        
            
            
            foreach($woocommerce->cart->cart_contents AS $key => $val){
                if($val['full_name_member']){
                    $member['full_name_member'] = $val['full_name_member'] ;
                    //wc_add_order_item_meta($item_id,'Full Name',$member['full_name_member']);  
                }
                

                if($val['new_ic_member']){
                    $member['new_ic_member'] = $val['new_ic_member'] ;
                    //wc_add_order_item_meta($item_id,'New IC',$member['new_ic_member']);  
                }
                if($val['picture_member']){
                    $countstr = strlen($val['picture_member']) ;
                    //deb($countstr);exit();
                    $member['picture_member'] = substr($val['picture_member'],2,$countstr) ;
                    $member['picture_member'] = substr($member['picture_member'], 0, $countstr-4) ;
                    //wc_add_order_item_meta($item_id,'Picture',$member['picture_member']);  
                }
                if($val['email_member']){
                    $member['email_member'] = $val['email_member'] ;
                    //wc_add_order_item_meta($item_id,'Email',$member['email_member']);  
                }
                if($val['date_birth_member']){
                    $member['date_birth_member'] = $val['date_birth_member'] ;
                    //wc_add_order_item_meta($item_id,'Date Birth',$member['date_birth_member']);  
                }
                if($val['address_member']){
                    $member['address_member'] = $val['address_member'] ;
                    //wc_add_order_item_meta($item_id,'Address',$member['address_member']);  
                }
                if($val['poscode_member']){
                    $member['poscode_member'] = $val['poscode_member'] ;
                   //wc_add_order_item_meta($item_id,'Posscode',$member['poscode_member']);  
                }
                if($val['city_member']){
                    $member['city_member'] = $val['city_member'] ;
                    //wc_add_order_item_meta($item_id,'City',$member['city_member']);  
                }
                if($val['state_member']){
                    $member['state_member'] = $val['state_member'] ;
                    //wc_add_order_item_meta($item_id,'State',$member['state_member']);  
                }
                if($val['mobile_phone_member']){
                    $member['mobile_phone_member'] = $val['mobile_phone_member'] ;
                    //wc_add_order_item_meta($item_id,'Mobile Phone',$member['mobile_phone_member']);  
                }
                if($val['house_phone_member']){
                    $member['house_phone_member'] = $val['house_phone_member'] ;
                    //wc_add_order_item_meta($item_id,'House Phone',$member['house_phone_member']);  
                }
                if($val['dependent_repeat_member']){
                    $member['dependent_repeat_member'] = serialize($val['dependent_repeat_member']) ;
                    //wc_add_order_item_meta($item_id,'Dependent',$member['dependent_repeat_member']);  
                }
            

            }

            if(!$member['new_ic_member']){
                return ;
            }
           

            

            
            
            $member['cct_status'] = 'pending payment' ; 
           
            if(in_array("Pay after approval", $check_categories)){  //kalau admin kena approve dulu 
                $member['cct_status'] = 'pending review' ; 
            }
        
            $member['cct_single_post_id'] = $order_id ; 
            $vendor_id   = wcfm_get_vendor_id_by_post( $_product->id );
            $member['vendor_id']   = $vendor_id ;
            $member['cct_author_id'] = get_current_user_id() ; 
            
            
            $tablename = $wpdb->prefix . "jet_cct_member";
            $sql = "SELECT * FROM ".$tablename." WHERE new_ic_member = '".$member['new_ic_member']."' AND vendor_id = '".$vendor_id."'" ;
            $wcfm_ahli_array = $wpdb->get_results( $sql , ARRAY_A );
            
           
            if($wcfm_ahli_array){
                
                //update
                $member['cct_modified'] = date("Y-m-d h:i:s") ;
                $data = $member; // NULL value.
                $where = [ 'new_ic_member' => $member['new_ic_member'], 'vendor_id' => $vendor_id ]; // NULL value in WHERE clause.
                $wpdb->update( $wpdb->prefix . 'jet_cct_member', $data, $where ); // Also works in this case.
        
        
            }else{
                //insert
              
                $tablename = $wpdb->prefix . "jet_cct_member";
                $data = $member;
                $format = array('%s','%d');
                $wpdb->insert($tablename,$data,$format);
                $my_id = $wpdb->insert_id;

               

               
            }

          

        }

        if($checkposttype == 'shop_subscription'){
           

           
            
           
            foreach($woocommerce->cart->cart_contents AS $key => $val){
                $_product = $val['data'];
                if($val['new_ic_member']){
                    $member['new_ic_member'] = $val['new_ic_member'] ;
                }
               
            }

            
            $vendor_id   = wcfm_get_vendor_id_by_post( $_product->id );
            
            $member['cct_author_id'] = get_current_user_id() ; 

           

             //find ic if exist
             $tablename = $wpdb->prefix . "jet_cct_member";
             $sql = $wpdb->prepare( "SELECT * FROM ".$tablename." WHERE new_ic_member = %d AND vendor_id = '".$vendor_id."'", $member['new_ic_member']) ;
             $wcfm_ahli_array = $wpdb->get_results( $sql , ARRAY_A );

             
             if($wcfm_ahli_array){
              //update
              $data = ['subscription_id' => $order_id , 'cct_modified' => date("Y-m-d h:i:s") ]; // NULL value.
              $format = [ NULL ];  // Ignored when corresponding data is NULL, set to NULL for readability.
              $where = [ 'new_ic_member' => $member['new_ic_member'] ,  'vendor_id' => $vendor_id ]; // NULL value in WHERE clause.
              $where_format = [ NULL ];  // Ignored when corresponding WHERE data is NULL, set to NULL for readability.
              $wcfm_ahli_array = $wpdb->update( $wpdb->prefix . 'jet_cct_member', $data, $where ); // Also works in this case.
                
             }

            //  /deb($wcfm_ahli_array);exit();

        }
        /*
        $tablename = $wpdb->prefix . "jet_cct_member";
        $sql = $wpdb->prepare( "SELECT * FROM ".$tablename." ");
        $results = $wpdb->get_results( $sql , ARRAY_A );
        $data = $member;
        $format = array('%s','%d');
        $wpdb->insert($tablename,$data,$format);
        $my_id = $wpdb->insert_id;
        */

        //deb($member);exit();
       

        

/*
        //save data
        $url = home_url('wp-json/jet-cct/member');
        $url = home_url('http://sistemmasjid.test/wp-json/jet-cct/member/1');
        $fields= $member ; 
        
        //url-ify the data for the POST
        foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        rtrim($fields_string, '&');

        
        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_GET, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

        //execute post
        $result = curl_exec($ch);
        deb($result);exit();
        //close connection
        curl_close($ch);

        */

  }
}




add_action('wp_footer', 'checkoutluqjs');
function checkoutluqjs() {
    global $wpdb, $wp;
    //deb($wp->query_vars);
    if(isset($wp->query_vars['pagename']) && $wp->query_vars['pagename'] == 'checkout' && isset($wp->query_vars['order-pay'])){
            ?>
            <script>
                

            jQuery( document ).ready( function( $ ) {
               
                $('li strong.wc-item-meta-label:contains("Picture")').parent().addClass("luqhide");
                if($('li strong.wc-item-meta-label:contains("Dependent")').next().text().length){
                    var formData = unserialize($('li strong.wc-item-meta-label:contains("Dependent")').next().text()) ;
                    //console.log($('li strong.wc-item-meta-label:contains("dependent_repeat_member")').next().text());
                    console.log(formData.length);
                    var i;
                    $('li strong.wc-item-meta-label:contains("Dependent")').next().html("");
                    for (i = 0; i < formData.length; i++) {
                        $('li strong.wc-item-meta-label:contains("Dependent")').next().append('<br>'+(i +  1)+')&nbsp;&nbsp;&nbsp;&nbsp;Full Name: '+formData[i].d_full_name_member+'<br>&nbsp;&nbsp;&nbsp;&nbsp;IC No: '+formData[i].d_ic_member+'<br>&nbsp;&nbsp;&nbsp;&nbsp;Relation: '+formData[i].d_relation_member);
                        console.log(formData[i].d_full_name_member);
                        console.log(formData[i].d_ic_member);
                        console.log(formData[i].d_relation_member);
                    } 
                }
         


            });
            </script>
            
            <?php

        
    }
    if(isset($wp->query_vars['pagename'] ) && $wp->query_vars['pagename'] == 'checkout' && isset($wp->query_vars['order-received'])){
        $new_member = new luq_class_member() ; 
        $member = $new_member->get_member_id_by_order_id($wp->query_vars['order-received']) ; 
        //deb($member[0]->_ID);exit();
        //get_member_id_by_subscriber_id
        
        $homeurlxx = home_url('my-account/view-order/'.$wp->query_vars['order-received']) ; 
        $memberurl = home_url('my-account/memberInfo/'.$member[0]->_ID.'/?_post_id='.$member[0]->_ID) ; 
        ?>
        
            <script>
             var homeurlxx = <?php echo json_encode($homeurlxx) ; ?> ;
             var memberurl = <?php echo json_encode($memberurl) ; ?> ;
            
            jQuery( document ).ready( function( $ ) {
             
           //tambah button member
           
           $('a.woocommerce-button.button.view').parent().prepend('<a target="_blank" href="'+memberurl+'" class="woocommerce-button button view">Member Info</a>');
               

            //var homeurl = 'my-account/view-order/' ; 
            //$("div.entry-content div.woocommerce div.woocommerce-order p:contains('Your subscription will be activated')").html("Your subscription will be activated when order has been approved and payment clears. You can upload your receipt payment <a href='"#"'here</a>") ; 
            $('div.entry-content div.woocommerce div.woocommerce-order p:contains("Your subscription will be activated")').html("Your subscription will be activated when order has been approved and payment clears.") ; 
           $('li strong.wc-item-meta-label:contains("Picture")').parent().addClass("luqhide");
            //$('li strong.wc-item-meta-label:contains("dependent_repeat_member")').parent().addClass("luqhide");
            //$('li strong.wc-item-meta-label:contains("dependent_repeat_member")').next().addClass("luqhide");
            


                //alert($('li strong.wc-item-meta-label:contains("dependent_repeat_member")').next().text())
            if($('li strong.wc-item-meta-label:contains("Dependent")').next().text().length){
                var formData = unserialize($('li strong.wc-item-meta-label:contains("Dependent")').next().text()) ;
                //console.log($('li strong.wc-item-meta-label:contains("dependent_repeat_member")').next().text());
                console.log(formData.length);
                var i;
                $('li strong.wc-item-meta-label:contains("Dependent")').next().html("");
                for (i = 0; i < formData.length; i++) {
                    $('li strong.wc-item-meta-label:contains("Dependent")').next().append('<br>'+(i +  1)+')&nbsp;&nbsp;&nbsp;&nbsp;Full Name: '+formData[i].d_full_name_member+'<br>&nbsp;&nbsp;&nbsp;&nbsp;IC No: '+formData[i].d_ic_member+'<br>&nbsp;&nbsp;&nbsp;&nbsp;Relation: '+formData[i].d_relation_member);
                    console.log(formData[i].d_full_name_member);
                    console.log(formData[i].d_ic_member);
                    console.log(formData[i].d_relation_member);
                } 
            }
         
           

            //alert(formData);




         
        
                });
        </script>
        
        <?php


          /*
        //deb($wp->query_vars['order-received']);
        //find ic if exist
        $tablename = $wpdb->prefix . "posts";
        $sql = "SELECT * FROM ".$tablename." WHERE post_parent = '".$wp->query_vars['order-received']."' AND post_type = 'shop_subscription'" ;
        $getinfp = $wpdb->get_results( $sql , ARRAY_A );
               // deb('aaaa');
        wp_update_post( array( 'ID' => $getinfp[0]['ID'] ,  'post_status' => 'wc-on-hold' ) );
           // exit();
                */



        }




}




/*
add_action( 'save_post_shop_subscription', 'post_updatedluq1', 12, 2);
function post_updatedluq1($post_id){
    if(isset($_POST['woocommerce-process-checkout-nonce'])) {
        return  ; 
    }
   deb($_POST);exit();
   

    wp_update_post( array( 'ID' => $post_id, 'post_status' => 'wc-on-hold' ) );
  
    remove_action( 'save_post_shop_subscription', 'post_updatedluq1' );

}
*/



function my_functionluqcheckout(){
    global $wp, $wpdb ;
    if(isset($wp->query_vars['pagename']) && isset( $wp->query_vars['order-received'])){
        $getsub =  wcs_get_subscriptions($wp->query_vars['order-received']) ;
        if($getsub){
            wp_update_post( array( 'ID' => key($getsub) ,  'post_status' => 'wc-on-hold' ) );
        }
    }
    // your code goes here
}
add_action( "template_redirect", "my_functionluqcheckout" );



?>