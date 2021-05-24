<?php
global $wp, $WCFM, $wp_query , $wpdb;


if(isset($wp->query_vars['pagename']) && $wp->query_vars['pagename'] == 'my-account' && isset($wp->query_vars['memberInfo']) AND (!$wp->query_vars['memberInfo'])){
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
     
      
      foreach($getinfp AS $k => $v){
        $venid[] = $v['ID'] ;
      }
      $venid = implode(",", $venid) ;
      $tablename = $wpdb->prefix . "jet_cct_member";
      $sql = "SELECT * FROM ".$tablename." WHERE subscription_id IN (".$cusid.")" ;
      $wcfm_ahli_array = $wpdb->get_results( $sql , ARRAY_A );
          // deb( $wcfm_ahli_array);
 
  // your code goes here



?>
	<table id="woo-memberinfo" class="display" cellspacing="0" width="100%">
				<thead>
						<tr>
							<th>Members ID</th>
							<th>Status</th>
							<th>Subscription</th>
							<th>Order</th>
              <th>Kariah</th>
							<th><?php _e( 'Actions', 'wc-frontend-manager' ); ?></th>
						</tr>
					</thead>
          <tbody>
          <?php foreach ($wcfm_ahli_array AS $key => $val){   ?>
            <tr>
                <td><?php echo $val['_ID'] ; ?></td>
                <td><?php echo $val['cct_status'] ; ?></td>
                <?php $url = site_url('/my-account/view-subscription/'.$val['subscription_id']);  ?>
                <th><a href="<?php echo $url  ; ?>">View<a/></th>
                <?php $url = site_url('/my-account/view-order/'.$val['cct_single_post_id']);  ?>
                <th><a href="<?php echo $url  ; ?>">View<a/></th>
                <?php $url =  wcfmmp_get_store( $val['vendor_id'] ) ;  ?>
                <th><a href="<?php echo wcfmmp_get_store_url( $val['vendor_id']) ; ?>"><?php echo($url->data->display_name)  ; ?></a></th>
                <?php $url = site_url('/my-account/memberInfo/'.$val['_ID'].'/?_post_id='.$val['_ID']);  ?>
                <th><a href="<?php echo $url  ; ?>">View<a/></th>
            </tr>
          <?php } ?>
          </tbody>
					<tfoot>
						<tr>
              <th>Members ID</th>
							<th>Status</th>
              <th>Subscription</th>
							<th>Order</th>
              <th>Kariah</th>
							<th><?php _e( 'Actions', 'wc-frontend-manager' ); ?></th>
						</tr>
					</tfoot>
				</table>

<?php 
          add_action('wp_footer', 'memberinfoluqjswoo_myaccount');
          function memberinfoluqjswoo_myaccount() {
              global $wpdb, $wp;
              //deb($wp->query_vars);
              
                  global $wpdb, $wp;
                
                
                  ?>

                    <script src="https://code.jquery.com/jquery-3.5.1.js"></script> 
                    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>      
    
    

                  
                      <script>
                    
                    $(document).ready(function() {
                        $('#woo-memberinfo').DataTable();
                    } );


                      </script>
                  
                  <?php

            
          }


} 






// paparan form 
if(isset($wp->query_vars['pagename']) && $wp->query_vars['pagename'] == 'my-account' && isset($wp->query_vars['memberInfo']) AND $wp->query_vars['memberInfo']){
  echo do_shortcode('[jet_engine component="forms" _form_id="275"]');



  add_action('wp_footer', 'memberinfoluqjswoo_myaccount');
  function memberinfoluqjswoo_myaccount() {
      global $wpdb, $wp;
      //deb($wp->query_vars);
      
          global $wpdb, $wp;
        
        
          ?>
          
              <script>
            
              jQuery( document ).ready( function( $ ) {
                    var cct_status  =  $('#cct_status option:selected').text() ; 
                    $('#cct_status').addClass("luqhide") ; 
                $('#cct_status').parent().append(cct_status);

              

              });
              </script>
          
          <?php

    
  }

}




?>


  