<?php
namespace WCOA\classes\admin;

class CouponSettingsPage
{
	public function __construct()
	{
		add_action('woocommerce_coupon_options', array(&$this, 'manage_options'), 10, 2);
		add_action('woocommerce_process_shop_coupon_meta', array( &$this, 'save_options' ), 5, 2 );
	}
	public function manage_options($coupon_id, $coupon)
	{
		$automatic_approval = $coupon->get_meta( 'wcoa_automatic_approval' ) ? $coupon->get_meta( 'wcoa_automatic_approval' ) : false;
		woocommerce_wp_checkbox(
			array(
				'id'                => 'wcoa_automatic_approval',
				'value'             => wc_bool_to_string( $automatic_approval ),
				'label'             => __( 'Automatically order approval', 'woocommerce-order-approval' ),
				'description'       => __( 'Automatically approve the order if the coupon is applied.', 'woocommerce-order-approval' ),
				
			)
		);
	}
	public function save_options($coupon_id, $post ) 
	{
		if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['woocommerce_meta_nonce'] ), 'woocommerce_save_data' ) ) 
		{
			return;
		}
		
		$coupon    = new \WC_Coupon( $coupon_id );
		$coupon->update_meta_data( 'wcoa_automatic_approval', isset($_POST['wcoa_automatic_approval']) ) ;
		$coupon->save();
	}
}
?>