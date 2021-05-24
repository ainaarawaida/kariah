<?php
/**
 * WCFM plugin view
 *
 * WCFM Orders Dashboard View
 *
 * @author 		WC Lovers
 * @package 	wcfm/view
 * @version   1.0.0
 */

global $WCFM;

$order_vendor = ! empty( $_GET['order_vendor'] ) ? sanitize_text_field( $_GET['order_vendor'] ) : '';

//include_once( $WCFM->plugin_path . 'controllers/orders/wcfm-controller-wcmarketplace-orders.php' );
//new WCFM_Orders_WCMarketplace_Controller();

$admin_fee_mode = apply_filters( 'wcfm_is_admin_fee_mode', false );
?>

<div class="collapse wcfm-collapse" id="wcfm_orders_listing">

  <div class="wcfm-page-headig">
		<span class="wcfmfa fa-shopping-cart"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Members', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		
		<div class="wcfm-container wcfm-top-element-container">
				<h2><?php _e('Members List', 'wc-frontend-manager' ); ?></h2>
				<?php
						echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_custom_menus_url( 'wcfm-ahli_manage' ).'" data-tip="' . __('Add New Member', 'wc-frontend-manager-ultimate') . '"><span class="wcfmfa fa-cube"></span><span class="text">' . __( 'Add Member', 'wc-frontend-manager') . '</span></a>';
						echo '<a class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_edit_product_url('?add_new_ahli=true').'" data-tip="' . __('Add New Members Form', 'wc-frontend-manager-ultimate') . '"><span class="wcfmfa fa-cube"></span><span class="text">' . __( 'Add Member Form', 'wc-frontend-manager') . '</span></a>';
				?>
						
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
		<div class="wcfm-container">
			<div id="wwcfm_orders_listing_expander" class="wcfm-content">
				<table id="wcfm-ahli" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>               
							<th><?php _e( 'Member No', 'wc-frontend-manager' ); ?></th> 
							<th><?php _e( 'IC No', 'wc-frontend-manager' ); ?></th>     
							<th><?php _e( 'Name', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Date Register', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Kariah', 'wc-frontend-manager' ); ?></th>                                                                
							<th><span class="wcicon-status-processing text_tip" data-tip="<?php _e( 'Status', 'wc-frontend-manager' ); ?>"></span></th>
							<?php do_action( 'wcfm_order_columns_after' ); ?>
							<th><?php printf( apply_filters( 'wcfm_order_label', __( 'Order', 'wc-frontend-manager' ) ) ); ?></th>
							<th><?php _e( 'Purchased', 'wc-frontend-manager' ); ?></th>
							
							<th><?php _e( 'Billing Address', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Fee', 'wc-frontend-manager' ); ?></th>
							
						
							
							
							<th><?php printf( apply_filters( 'wcfm_order_action_label', __( 'Actions', 'wc-frontend-manager' ) ) ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
						<th><?php _e( 'Member No', 'wc-frontend-manager' ); ?></th> 
							<th><?php _e( 'IC No', 'wc-frontend-manager' ); ?></th>     
							<th><?php _e( 'Name', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Date Register', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Kariah', 'wc-frontend-manager' ); ?></th>                 
							<th><span class="wcicon-status-processing text_tip" data-tip="<?php _e( 'Status', 'wc-frontend-manager' ); ?>"></span></th>
							<?php do_action( 'wcfm_order_columns_after' ); ?>
							<th><?php printf( apply_filters( 'wcfm_order_label', __( 'Order', 'wc-frontend-manager' ) ) ); ?></th>
							<th><?php _e( 'Purchased', 'wc-frontend-manager' ); ?></th>
							
							<th><?php _e( 'Billing Address', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Fee', 'wc-frontend-manager' ); ?></th>
							
							
							
							<th><?php printf( apply_filters( 'wcfm_order_action_label', __( 'Actions', 'wc-frontend-manager' ) ) ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<?php
		do_action( 'after_wcfm_orders' );
		?>
	</div>
</div>