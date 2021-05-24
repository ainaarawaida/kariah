<?php
/**
 * WCFM plugin view
 *
 * WCFM Order Details View
 *
 * @author 		WC Lovers
 * @package 	wcfm/view
 * @version   1.0.0
 */

global $wp, $WCFM, $WCFMmp, $theorder, $wpdb;





add_action('wp_footer', 'productluq_ahlimanagejs');
function productluq_ahlimanagejs() {


?>

	<script>

	jQuery( document ).ready( function( $ ) {
	
	//$("#full_name_member").attr("readonly", "true");
	
	
	//clode
	
	//var $clone = $('div.summary.entry-summary form.cart').children().clone(true,true);
	
	//$('button.jet-form__submit.submit-type-reload.add_member_submit').before($clone);
	//$('form.cart div.wc-pao-addon-container').addClass("luqhide");
	
	
	
});
	</script>
	
	<?php


}






$wcfm_is_allow_ahli = apply_filters( 'wcfm_is_allow_ahli', true );
if( !$wcfm_is_allow_ahli ) {
	wcfm_restriction_message_show( "ahli" );
	return;
}


if( isset( $wp->query_vars['wcfm-ahli_manage'] ) && !empty( $wp->query_vars['wcfm-ahli_manage'] ) ) {
	$ahli_id = $wp->query_vars['wcfm-ahli_manage'];
}
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

	if( $vendor_id != $wcfm_ahli_array[0]['vendor_id'] ) {
		wcfm_restriction_message_show( "Restricted Order" );
		return;
	}
}




?>

<div class="collapse wcfm-collapse" id="wcfm_order_details">

  <div class="wcfm-page-headig">
		<span class="wcfmfa fa-cart-arrow-down"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Members Details', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php _e( 'Member ID #', 'wc-frontend-manager' ); echo (isset($ahli_id) ? $ahli_id : '') ;  ?></h2>
			<span class="order-status"><?php echo isset($wcfm_ahli_array[0]['cct_status']) ? $wcfm_ahli_array[0]['cct_status'] : 'Add New Member' ; ?></span>
		
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />

	  
		<div class="wcfm-container">
			<div id="ahli_details_general_expander" class="wcfm-content">
			
			<?php  if(isset($wcfm_ahli_array[0]['subscription_id'])) { ?>
			<a id="subscription_detail" class="add_new_wcfm_ele_dashboard text_tip" target="_blank" href="<?php echo get_wcfm_custom_menus_url('subscriptions-manage').$wcfm_ahli_array[0]['subscription_id'] ; ?>" data-tip="Payment Subscription Info" data-hasqtip="17" aria-describedby="qtip-17"><span class="wcfmfa fa-file-pdf"></span><span class="text">Payment Subscription Info</span></a>
			<?php } ?>
			<h6>Personal Information</h6>

					<?php echo do_shortcode('[jet_engine component="forms" _form_id="275"]') ;  ?>
					<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<!-- end collapsible -->
		
	
	</div>
</div>


