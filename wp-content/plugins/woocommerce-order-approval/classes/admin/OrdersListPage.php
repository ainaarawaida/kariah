<?php 
namespace WCOA\classes\admin;

class OrdersListPage
{
	//rplc: woocommerce-order-approval, wcoa, WCOA
	public function __construct()
	{
		add_filter( 'bulk_actions-edit-shop_order', array( &$this, 'get_custom_order_status_bulk' ) );
		add_filter( 'woocommerce_admin_order_actions', array( &$this, 'add_custom_order_status_actions_button' ), 100, 2 );
		add_action( 'admin_head',  array( &$this,'add_custom_order_status_actions_button_css') );
		//columns
		add_filter( 'manage_shop_order_posts_custom_column', array( &$this,'add_column_content') , 10, 2 );
		add_filter( 'manage_edit-shop_order_columns', array($this, 'add_column_header'), 15 ); 
	}
	public function add_column_header($columns)
	 {
		
	   //remove column
	   //unset( $columns['tags'] );

	   //add column
	   $columns['wcoa-approval-date'] = esc_html__('Approval/Rejection date', 'woocommerce-files-upload');  

	   return $columns;
	}
	public function add_column_content( $column, $orderid ) 
	{
		global $wcoa_order_model, $wcoa_time_model;
		if ( $column == 'wcoa-approval-date' ) 
		{			
			$order = wc_get_order($orderid);
			if(!$order)
				return;
			$date = $wcoa_order_model->get_approval_date($order, true);
			echo $date && $wcoa_order_model->is_approved($order) ? $wcoa_time_model->format_datetime($date) : " - ";
			
		}
	}
	function get_custom_order_status_bulk( $bulk_actions ) 
	{
   
	   $bulk_actions['mark_approval-waiting'] = esc_html__( 'Change status to waiting for approval', 'woocommerce-order-approval' );  
	   $bulk_actions['mark_approved'] = esc_html__( 'Change status to approved', 'woocommerce-order-approval' );  
	   $bulk_actions['mark_rejected'] = esc_html__( 'Change status to rejected', 'woocommerce-order-approval' );  
	   $bulk_actions['mark_pending'] = esc_html__( 'Change status to pending payment', 'woocommerce-order-approval' );  
	   return $bulk_actions;
	}
	function add_custom_order_status_actions_button( $actions, $order ) 
	{
		global $wcoa_order_model;
		// Display the button for all orders that have a 'processing', 'pending' or 'on-hold' status
		//if ( $order->has_status( array( 'on-hold', 'processing', 'pending' ) ) ) 
		$wcoa_order_model->has_to_be_automatically_cancelled($order);
		{

			// Set the action button
			
			$action_slug = 'approval-waiting';
			if ( $order->get_status( ) !=  $action_slug) 
				$actions[$action_slug] = array(
					'url'       => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status='.$action_slug.'&order_id='.$order->get_id() ), 'woocommerce-mark-order-status' ),
					'name'      => esc_html__( 'Waiting for approval', 'woocommerce-order-approval' ),
					'action'    => $action_slug,
				);
			$action_slug = 'approved';
			if ( $order->get_status( ) !=  $action_slug) 
				$actions[$action_slug] = array(
					'url'       => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status='.$action_slug.'&order_id='.$order->get_id() ), 'woocommerce-mark-order-status' ),
					'name'      => esc_html__( 'Approved', 'woocommerce-order-approval' ),
					'action'    => 'approved',
				);
			$action_slug = 'rejected';
			if ( $order->get_status( ) !=  $action_slug) 
				$actions[$action_slug] = array(
					'url'       => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status='.$action_slug.'&order_id='.$order->get_id() ), 'woocommerce-mark-order-status' ),
					'name'      => esc_html__( 'Reject', 'woocommerce-order-approval' ),
					'action'    => $action_slug ,
				);
		}
		return $actions;
	}
	function add_custom_order_status_actions_button_css() 
	{
		//incon list: https://rawgit.com/woothemes/woocommerce-icons/master/demo.html
		wp_enqueue_style('wcoa-orders-list-page',  WCOA_PLUGIN_PATH.'/css/admin-orders-list-page.css');
		
	}
}
?>