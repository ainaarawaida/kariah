<?php

add_action('wp_footer', 'checkoutluqjswoo_myaccount');
function checkoutluqjswoo_myaccount() {
    global $wpdb, $wp;
    //deb($wp->query_vars);
    if(isset($wp->query_vars['pagename']) && $wp->query_vars['pagename'] == 'my-account' && isset($wp->query_vars['view-order'])){
        global $wpdb, $wp;
        //deb($wp->query_vars);exit();
        $order_status = wc_get_order( $wp->query_vars['view-order'] )->get_status();
        
        ?>
        
            <script>
             var order_status = <?php echo json_encode($order_status) ; ?> ;

            jQuery( document ).ready( function( $ ) {


              if(order_status == 'on-hold'){
                //$('p mark.order-status:contains("On hold")').next().html("luqhide");
                $('p mark.order-status:contains("On hold")').parent().append(" <br>You can upload your receipt here or email to us.");
              }
             
              $('li strong.wc-item-meta-label:contains("Picture")').parent().addClass("luqhide");

              if($('li strong.wc-item-meta-label:contains("Dependent")').next().text().length){
                var formData = unserialize($('li strong.wc-item-meta-label:contains("Dependent")').next().text()) ;
                //console.log($('li strong.wc-item-meta-label:contains("dependent_repeat_member")').next().text());
                console.log(formData.length);
                var i;
                $('li strong.wc-item-meta-label:contains("Dependent")').next().html("");
                for (i = 0; i < formData.length; i++) {
                    $('li strong.wc-item-meta-label:contains("Dependent")').next().append('<br>'+(i +  1)+') Full Name: '+formData[i].d_full_name_member+'<br>&nbsp;&nbsp;&nbsp;&nbsp;IC No: '+formData[i].d_ic_member+'<br>&nbsp;&nbsp;&nbsp;&nbsp;Relation: '+formData[i].d_relation_member);
                    console.log(formData[i].d_full_name_member);
                    console.log(formData[i].d_ic_member);
                    console.log(formData[i].d_relation_member);
                } 
            }


            });
            </script>
        
        <?php

    }
}




/*
 * Step 1. Add Link (Tab) to My Account menu
 */
add_filter ( 'woocommerce_account_menu_items', 'misha_log_history_link', 40 );
function misha_log_history_link( $menu_links ){



  //deb($wcfm_ahli_array);
  //_post_id
	$menu_links = array_slice( $menu_links, 0, 1, true ) 
	+ array( 'memberInfo' => 'Member Info' )
	+ array_slice( $menu_links, 1, NULL, true );
 
	return $menu_links;
 
}

add_filter( 'woocommerce_get_endpoint_url', 'misha_hook_endpoint', 10, 4 );
function misha_hook_endpoint( $url, $endpoint, $value, $permalink ){
 
	if( $endpoint === 'memberInfo' ) {
      /*
      global $wpdb, $wp;
      $tablename = $wpdb->prefix . "postmeta";
      $sql = "SELECT * FROM ".$tablename." WHERE meta_key = '_customer_user' AND meta_value = '".get_current_user_id()."'" ;
      $getinfp = $wpdb->get_results( $sql , ARRAY_A );

      if(!$getinfp){
        return $menu_links; 
      }
      foreach($getinfp AS $k => $v){
        $cusid[] = $v['post_id'] ;
      }
      $cusid = implode(",", $cusid) ;
      $tablename = $wpdb->prefix . "posts";
      $sql = "SELECT * FROM ".$tablename." WHERE ID IN (".$cusid.") AND post_type = 'shop_subscription'" ;
      $getinfp = $wpdb->get_results( $sql , ARRAY_A );


      $tablename = $wpdb->prefix . "jet_cct_member";
      $sql = "SELECT * FROM ".$tablename." WHERE subscription_id = '".$getinfp[0]['ID']."'" ;
      $wcfm_ahli_array = $wpdb->get_results( $sql , ARRAY_A );

      $url = site_url('/my-account/memberInfo/'.$wcfm_ahli_array[0]['_ID'].'/?_post_id='.$wcfm_ahli_array[0]['_ID']);
      */

      $url = site_url('/my-account/memberInfo');

	}
	return $url;
 
}
/*
 * Step 2. Register Permalink Endpoint
 */
add_action( 'init', 'misha_add_endpoint' );
function misha_add_endpoint() {
 
	// WP_Rewrite is my Achilles' heel, so please do not ask me for detailed explanation
	add_rewrite_endpoint( 'memberInfo', EP_PAGES );
 
}
/*
 * Step 3. Content for the new page in My Account, woocommerce_account_{ENDPOINT NAME}_endpoint
 */
add_action( 'woocommerce_account_memberInfo_endpoint', 'misha_my_account_endpoint_content' );
function misha_my_account_endpoint_content() {
 
	// of course you can print dynamic content here, one of the most useful functions here is get_current_user_id()

    require_once( 'woo_my_account/memberInfo.php' );
 
}
/*
 * Step 4
 */
// Go to Settings > Permalinks and just push "Save Changes" button.


/*
add_action( "template_redirect", "my_functionluqwoomyaccount" );
function my_functionluqwoomyaccount(){
  global $wp, $wpdb ;
  if($wp->query_vars['pagename'] && $wp->query_vars['order-received']){
      //find ic if exist
      $tablename = $wpdb->prefix . "posts";
      $sql = "SELECT * FROM ".$tablename." WHERE post_parent = '".$wp->query_vars['order-received']."' AND post_type = 'shop_subscription'" ;
      $getinfp = $wpdb->get_results( $sql , ARRAY_A );
             // deb('aaaa');
      wp_update_post( array( 'ID' => $getinfp[0]['ID'] ,  'post_status' => 'wc-on-hold' ) );
  }
  // your code goes here
}
*/

/*

if(isset($_GET['_post_id']) && $_GET['_post_id']){
	$ahli_id = $_GET['_post_id'];
}

if( wcfm_is_vendor() && isset($ahli_id)) {
	//$sql = $wpdb->prepare( "SELECT * FROM ".$tablename." WHERE _ID ='".$ahli_id."'");
	$tablename = $wpdb->prefix . "jet_cct_member";
	$sql = $wpdb->prepare( "SELECT * FROM wp_jet_cct_member WHERE _ID = %d", $ahli_id) ;
	$wcfm_ahli_array = $wpdb->get_results( $sql , ARRAY_A );
	//deb($wcfm_ahli_array);exit();
	$vendor_id    = get_current_user_id();

	if( $vendor_id != $wcfm_ahli_array[0]['cct_author_id'] ) {
		wcfm_restriction_message_show( "Restricted Order" );
		return;
	}
}
*/





             

?>