<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Products Custom Menus cubaan Controller
 *
 * @author 		WC Lovers
 * @package 	wcfmcsm/controllers
 * @version   1.0.0
 */

class WCFM_cubaan_Controller {


	
	public function __construct($var = '0') {
		global $WCFM, $WCFMu;
		
		
		if($var == '0'){
			$this->processing();
		}
		
	}
	
	public function processing() {
		global $WCFM, $WCFMu, $wpdb, $_POST;
		
		
		$customers_count = 0;
		$filtered_customers_count = 0;

		$tablename = $wpdb->prefix . "woocommerce_order_items";
		$current_vendorid = get_current_user_id() ;
		$sql = "SELECT order_id FROM {$wpdb->prefix}wcfm_marketplace_orders ";
		$cubaan = $wpdb->get_results( $sql);
		

		$customers_count = count($cubaan);
		$filtered_customers_count = count($cubaan);

		
		
		foreach($cubaan AS $key => $val){
			//deb($val->order_id);
		
			$order=wc_get_order($val->order_id);
			
			//select
			$wcfm_customers_json_arr[$key][] = $order->get_id() ;;

			//pendaftar name
			$wcfm_customers_json_arr[$key][] = $order->get_billing_first_name() ;

			//pendaftar email
			$wcfm_customers_json_arr[$key][] = $order->get_billing_email() ;

			//pendaftar no tel 
			$wcfm_customers_json_arr[$key][] = $order->get_billing_phone() ;

			//action
			$luqactions = '<a class="wcfm_order_delete wcfm-action-icon" href="#" data-orderid="' . $val->order_id . '"><span class="wcfmfa fa-trash-alt text_tip" data-tip="' . esc_attr__( 'Delete', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
			
			$luqactions .= '<a class="wcfm-action-icon" href="' . get_wcfm_view_order_url($val->order_id) . '"><span class="wcfmfa fa-eye text_tip" data-tip="' . esc_attr__( 'View Details', 'wc-frontend-manager' ) . '"></span></a>';
			$wcfm_customers_json_arr[$key][] = $luqactions;
		}
		if(empty($cubaan)) {
			$wcfm_customers_json_arr[$key][] = '';
		}
		
		//deb($wcfm_customers_json_arr);exit();
		
		//contoh kalau kat js "serverSide": true,
		$wcfm_cubaan_jsonx = '{
			"draw": ' . wc_clean($_POST['draw']) . ',
			"recordsTotal": ' . $customers_count . ',
			"recordsFiltered": ' . $filtered_customers_count . ',
			"data": 
			  '.json_encode($wcfm_customers_json_arr).'
			
		  }';

		//contoh kalau kat js "serverSide": false,
		$wcfm_cubaan_json = '{
		"data": 
			'.json_encode($wcfm_customers_json_arr).'
		
		}';

	

	  echo((($wcfm_cubaan_json)));
	  
	  die;
	}

	public function get_total_cubaan_luq(){
		global $WCFM, $WCFMu, $wpdb, $_POST;
		
		$tablename = $wpdb->prefix . "woocommerce_order_items";
		$sql = $wpdb->prepare( "SELECT order_id FROM ".$tablename." where order_item_name = 'Fi Pendaftaran'");
		$cubaan = $wpdb->get_results( $sql);

		$total = count($cubaan) ;
		return  $total;
	}
}



