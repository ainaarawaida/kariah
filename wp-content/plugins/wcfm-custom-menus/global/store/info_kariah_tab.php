<?php


add_action('luq_info_kariah_tab_page', 'luq_info_kariah_tab_page_function');

function luq_info_kariah_tab_page_function($store_id){
    //global $WCFM, $WCFMmp, $post;
    $luqmember = new luq_class_member() ;
    $luqclaim = new luq_class_claim() ;

    $store_user      = wcfmmp_get_store( $store_id );
    $store_info      = $store_user->get_shop_info();

    if(!isset($_GET['claim'])){    
    ?>

        <div class="reviews_heading">Check Member Registration For Kariah </div>
        <form  method="post">
        <div class="add_review">
            <input placeholder="Find By IC No" type="text" value="<?php echo isset($_POST['check_members']) ? $_POST['check_members'] : '' ; ?>" name="check_members">
        </div>
        <input class="button" type="submit" value="Find">
        </form>




        <?php

        if(isset($_POST['check_members'])){
            $check = $luqmember->find_members_by_ic($store_id, $_POST['check_members']) ;
           
            if($check){
                $check_claim = $luqclaim->get_claim_id_by_memberid($check[0]->_ID) ;
                ?>

        <table>
        <tr>
            <th>Full Name</th>
            <td><?php echo $check[0]->full_name_member ; ?></td>
        </tr>
        <tr>
            <th>IC Number</th>
            <td><?php echo $check[0]->new_ic_member ; ?></td>
        </tr>
        <tr>
        <th>Register on</th>
            <td><?php echo date('d-M-Y', strtotime($check[0]->cct_created)) ; ?></td>
        </tr>
        <tr>
        <th>Status</th>
            <td><b><?php 
            //deb($store_user->get_shop_url().'info_kariah/?claim=true');
            if($check[0]->cct_status == 'death'){
                echo '<b style="color:red;"> DEATH </b>' ;
            }else{
                echo strtoupper(str_replace("_"," ", $check[0]->cct_status)) ;
            }
            if($check[0]->cct_status == 'claim_death_processing'){
                echo '  &nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$store_user->get_shop_url().'info_kariah/?claim=true&item_id='.$check_claim[0]->_ID.'&member_id='.$check[0]->_ID.'" class="button" value="Claim">Claim Death Processing</a></td>' ;
            
            }elseif($check[0]->cct_status != 'claim_death'){
                echo '  &nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$store_user->get_shop_url().'info_kariah/?claim=true&member_id='.$check[0]->_ID.'" class="button" value="Claim">Claim Death</a></td>' ;
            
            }
            ?> 
            
            
            
        </tr>
        
        </table>
        <?php
            }else{
                echo "<h1 style='color:red;'>Data Not Found<h1>" ;
            }
        
        }

    }else{ //end !isset($_GET['claim']
        $member = $luqmember->get_members_by_id($_GET['member_id']) ;
        ?>
        <div class="reviews_heading">Claim Form for <?php echo $member[0]->full_name_member ; ?></div>

        <?php 
        echo do_shortcode('[jet_engine component="forms" _form_id="494"]') ; 
    }

} // end function



add_action( 'wp_footer', 'luq_style_infokariah' );
function luq_style_infokariah(){
    global $wp, $wpdb ;
    if(isset($wp->query_vars['post_type']) && isset($wp->query_vars['info_kariah']) && $wp->query_vars['post_type'] == 'product' && $wp->query_vars['info_kariah']){
        if(isset($_GET['member_id'])){
            $luqmember = new luq_class_member() ;
            $check = $luqmember->get_members_by_id($_GET['member_id']) ;
        }
        ?>
        <script>
        var $check = <?php echo json_encode($check); ?> ; 

        jQuery( document ).ready( function( $ ) {
           console.log($check[0])
           $('#full_name_death_claim').val($check[0].full_name_member).attr('readonly',true)
           $('#new_ic_death_claim').val($check[0].new_ic_member).attr('readonly',true)
           $('#cct_status option').detach()
           $('#cct_status').append('<option value="claim_request">Claim Request</option>');
           
        });   
        </script>
        <?php


    }

    


}

add_action( 'jet-engine-booking/claim', 'luq_jet_engine_booking_claim' );
function luq_jet_engine_booking_claim($data){
    global $wpdb;
    $user = wp_get_current_user();
    $tablename = $wpdb->prefix . "jet_cct_claim";
    $sql = "SELECT * FROM ".$tablename." WHERE member_id = '".$data['member_id']."'" ;
    $getinfp = $wpdb->get_results( $sql , ARRAY_A );
    unset($data['Submit']);
    unset($data['item_id']);
    if(!$getinfp){
        $tablename = $wpdb->prefix . "jet_cct_claim";
        $format = array('%s','%d');
        $wpdb->insert($tablename,$data,$format);
        $my_id = $wpdb->insert_id;

        //update member status
        $tablename2 = $wpdb->prefix . "jet_cct_member";
        $data2['cct_status'] = 'claim_death_processing' ;
        $where = [ '_ID' => $data['member_id'] ]; // NULL value in WHERE clause.
		$wcfm_ahli_array = $wpdb->update( $tablename2, $data2, $where ); // Also works in this case.
    
        wp_redirect($_POST['_jet_engine_refer'].'&status=success&item_id='.$my_id);   
        exit();
    }else{
        //update claim
        $my_id = $getinfp[0]['_ID'] ;
        $tablename = $wpdb->prefix . "jet_cct_claim";
        $format = [ NULL ];  // Ignored when corresponding data is NULL, set to NULL for readability.
        $where = [ '_ID' => $my_id ]; // NULL value in WHERE clause.
		$where_format = [ NULL ];  // Ignored when corresponding WHERE data is NULL, set to NULL for readability.
		$wcfm_ahli_array = $wpdb->update( $tablename, $data, $where ); // Also works in this case.

        //update member status
        $tablename2 = $wpdb->prefix . "jet_cct_member";
        $data2['cct_status'] = 'claim_death_processing' ;
        $where = [ '_ID' => $data['member_id'] ]; // NULL value in WHERE clause.
		$wcfm_ahli_array = $wpdb->update( $tablename2, $data2, $where ); // Also works in this case.

        wp_redirect($_POST['_jet_engine_refer'].'&status=success&item_id='.$my_id);   
        //wp_redirect($_POST['_jet_engine_refer'].'&item_id='.$my_id);    
        exit();
    }
    
   
}



?>