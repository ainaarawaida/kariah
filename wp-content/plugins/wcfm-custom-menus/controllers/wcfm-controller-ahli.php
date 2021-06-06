<?php
/**
 * WCFM plugin controllers
 *
 * Plugin ahli Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   1.0.0
 */

class WCFM_ahli_Controller {
	
	public function __construct() {
		global $WCFM;
		if(isset($_POST['orderid'])){
			$this->delete_wcfm_ahli($_POST['orderid']) ; 
		}else{
			$this->processing();
		}
		
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$length = wc_clean($_POST['length']);
		$offset = wc_clean($_POST['start']);
		
		$filtering_on = false;
		
		$args = array(
							'posts_per_page'   => $length,
							'offset'           => $offset,
							'category'         => '',
							'category_name'    => '',
							'orderby'          => 'date',
							'order'            => 'DESC',
							'include'          => '',
							'exclude'          => '',
							'meta_key'         => '',
							'meta_value'       => '',
							'post_type'        => 'shop_order',
							'post_mime_type'   => '',
							'post_parent'      => '',
							//'author'	   => get_current_user_id(),
							'post_status'      => 'any',
							'suppress_filters' => 0 
						);
		if( isset( $_POST['search'] ) && !empty( $_POST['search']['value'] )) {
			$wc_order_ids = wc_order_search( $_POST['search']['value'] );
			if( !empty( $wc_order_ids ) ) {
				$args['post__in'] = $wc_order_ids;
			} else {
				$args['post__in'] = array(0);
			}
			$filtering_on = true;
		} else {
			if ( ! empty( $_POST['filter_date_form'] ) && ! empty( $_POST['filter_date_to'] ) ) {
				$fyear  = absint( substr( $_POST['filter_date_form'], 0, 4 ) );
				$fmonth = absint( substr( $_POST['filter_date_form'], 5, 2 ) );
				$fday   = absint( substr( $_POST['filter_date_form'], 8, 2 ) );
				
				$tyear  = absint( substr( $_POST['filter_date_to'], 0, 4 ) );
				$tmonth = absint( substr( $_POST['filter_date_to'], 5, 2 ) );
				$tday   = absint( substr( $_POST['filter_date_to'], 8, 2 ) );
				
				$args['date_query'] = array(
																		'after' => array(
																											'year'  => $fyear,
																											'month' => $fmonth,
																											'day'   => $fday,
																										),
																		'before' => array(
																											'year'  => $tyear,
																											'month' => $tmonth,
																											'day'   => $tday,
																										),
																		'inclusive' => true
																);
				$filtering_on = true;
			}
			
			if ( ! empty( $_POST['order_vendor'] ) ) {
				$sql  = "SELECT order_id FROM {$wpdb->prefix}wcfm_marketplace_ahli";
				$sql .= " WHERE 1=1";
				$sql .= " AND `vendor_id` = " . wc_clean($_POST['order_vendor']);
			
				$vendor_ahli_list = $wpdb->get_results( $sql );
				if( !empty( $vendor_ahli_list ) ) {
					$vendor_ahli = array();
					foreach( $vendor_ahli_list as $vendor_order_list ) {
						$vendor_ahli[] = $vendor_order_list->order_id;
					}
					$args['post__in'] = $vendor_ahli;
				} else {
					$args['post__in'] = array(0);
				}
				$filtering_on = true;
			}
		}
		
		if ( ! empty( $_POST['delivery_boy'] ) ) {
			$args['meta_query'] = array(
				'relation' => 'AND',
				array(
					'key'     => '_wcfm_delivery_boys',
					'value'   => wc_clean($_POST['delivery_boy']),
					'compare' => 'LIKE'
				)
			);
			$filtering_on = true;
		}
		
		$args = apply_filters( 'wcfm_ahli_args', $args );


		
		
		$wcfm_ahli_array = get_posts( $args );
		
		// Get Order Count
		$order_count = 0;
		$filtered_order_count = 0;
		
		$wcfm_ahli_counts = wp_count_posts('shop_order');
		foreach($wcfm_ahli_counts as $wcfm_ahli_count ) {
			$order_count += $wcfm_ahli_count;
		}
		
		if ( $filtering_on ) {
			$args['offset'] = 0;
			$args['posts_per_page'] = -1;
			$args['fields'] = 'ids';
			$wcfm_ahli_count_array = get_posts( $args );
			$filtered_order_count = count( $wcfm_ahli_count_array );
		} else {
			$order_status = ! empty( $_POST['order_status'] ) ? sanitize_text_field( $_POST['order_status'] ) : 'all';
			if( $order_status == 'all' ) {
				$filtered_order_count = $order_count;
			} else {
				foreach($wcfm_ahli_counts as $wcfm_ahli_count_status => $wcfm_ahli_count ) {
					if( $wcfm_ahli_count_status == 'wc-' . $order_status ) {
						$filtered_order_count = $wcfm_ahli_count;
					}
				}
			}
		}

		if( defined('WCFM_REST_API_CALL') ) {
      return $wcfm_ahli_array;
    }
		
		$admin_fee_mode = apply_filters( 'wcfm_is_admin_fee_mode', false );
		
		// Generate Products JSON
		$wcfm_ahli_json = '';
		
		/*
		//kalau dekat js pakai "serverSide": true,
		$wcfm_ahli_jsonx = '{
															"draw": ' . wc_clean($_POST['draw']) . ',
															"recordsTotal": ' . $order_count . ',
															"recordsFiltered": ' . $filtered_order_count . ',
															"data": ';
		*/
		$wcfm_ahli_json = '{
			
			"data": ';
		
		if( wcfm_is_vendor() ) {
			$vendor_id    = get_current_user_id();
			$tablename = $wpdb->prefix . "jet_cct_member";
			$sql = $wpdb->prepare( "SELECT * FROM wp_jet_cct_member WHERE vendor_id = %d", $vendor_id) ;
			$wcfm_ahli_array = $wpdb->get_results( $sql , ARRAY_A );
		}else{
			$tablename = $wpdb->prefix . "jet_cct_member";
			$sql = "SELECT * FROM wp_jet_cct_member" ;
			$wcfm_ahli_array = $wpdb->get_results( $sql , ARRAY_A );

		}
		
		
		if(!empty($wcfm_ahli_array)) {
			$index = 0;
			$wcfm_ahli_json_arr = array();
			foreach($wcfm_ahli_array as $wcfm_ahli_single) {
				
				// _ID
				$wcfm_ahli_json_arr[$index][] =  $wcfm_ahli_single['_ID'];
				
				// new_ic_member
				$wcfm_ahli_json_arr[$index][] =  $wcfm_ahli_single['new_ic_member'];
				
				// full_name_member
				$wcfm_ahli_json_arr[$index][] =  $wcfm_ahli_single['full_name_member'];

				// cct_created
				$wcfm_ahli_json_arr[$index][] =  date("d-M-Y H:i:s", strtotime($wcfm_ahli_single['cct_created'])); 
				
				// Kariah
				$wcfm_ahli_json_arr[$index][] =  wcfm_get_vendor_store( $wcfm_ahli_single['vendor_id'] );

				// Status Ahli
				$wcfm_ahli_json_arr[$index][] =   $wcfm_ahli_single['cct_status'];

				// Order Related Id
				$subscription = new WC_Subscription( $wcfm_ahli_single['subscription_id'] );
				$relared_orders_ids_array = $subscription->get_related_orders();
				sort($relared_orders_ids_array);
				$gethtml = '' ;
				foreach($relared_orders_ids_array AS $key2 => $val2){ 
					$url = get_wcfm_custom_menus_url('orders-details').$val2; 
					//$gethtml .= "123" ;
					//deb($url);
					$gethtml .= '<a href="'.$url.'">#'.$val2.'<a/><br>' ;
					//$gethtml .= '<a href="'.echo $url .'">#'.echo $val2.'<a/><br>' ;
				} 
				
				
				$wcfm_ahli_json_arr[$index][] = $gethtml;
				/*
				// Gross Sales Amount
				$wcfm_ahli_json_arr[$index][] =  $wcfm_ahli_single['_ID'];
				
				// Commission && Commission Amount
				$wcfm_ahli_json_arr[$index][] =  $wcfm_ahli_single['_ID'];
				$wcfm_ahli_json_arr[$index][] =  $wcfm_ahli_single['_ID'];
				*/

				//deb($wcfm_ahli_array);exit();
				// Action
				$actions = '';
				//$actions .= '<a target="_blank" class="wcfm-action-icon" href="'. get_wcfm_custom_menus_url('orders-details').$wcfm_ahli_single['cct_single_post_id'].'" data-orderid="' . $wcfm_ahli_single['_ID'] . '"><span class="wcfmfa fa-first-order text_tip" data-tip="' . esc_attr__( 'Order Info', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
				$actions .= '<a target="_blank" class="wcfm-action-icon" href="'. get_wcfm_custom_menus_url('subscriptions-manage').$wcfm_ahli_single['subscription_id'].'" data-orderid="' . $wcfm_ahli_single['_ID'] . '"><span class="wcfmfa fa-file-pdf text_tip" data-tip="' . esc_attr__( 'Payment Subscription Info', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
				$actions .= '<a class="wcfm_ahli_delete wcfm-action-icon" href="#" data-orderid="' . $wcfm_ahli_single['_ID'] . '"><span class="wcfmfa fa-trash-alt text_tip" data-tip="' . esc_attr__( 'Delete', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
				$wcfm_ahli_json_arr[$index][] =  $actions .= '<a class="wcfm-action-icon" href="' . get_wcfm_custom_menus_url( 'wcfm-ahli_manage' )."?_post_id=".$wcfm_ahli_single['_ID'] . '"><span class="wcfmfa fa-eye text_tip" data-tip="' . esc_attr__( 'View Details', 'wc-frontend-manager' ) . '"></span></a>';
				
				/*
				// Custom Column Support Before
				$wcfm_ahli_json_arr = apply_filters( 'wcfm_ahli_custom_columns_data_before', $wcfm_ahli_json_arr, $index, $wcfm_ahli_single->ID, $wcfm_ahli_single, $the_order );
				
				// Date
				$wcfm_ahli_json_arr[$index][] =  $wcfm_ahli_single['_ID'];
				
				// Additional Info
				$actions = '';
				$actions .= '<a class="wcfm_order_delete wcfm-action-icon" href="#" data-orderid="' . $wcfm_ahli_single['_ID'] . '"><span class="wcfmfa fa-trash-alt text_tip" data-tip="' . esc_attr__( 'Delete', 'wc-frontend-manager-ultimate' ) . '"></span></a>';
				$wcfm_ahli_json_arr[$index][] =  $actions .= '<a class="wcfm-action-icon" href="' . get_wcfm_custom_menus_url( 'wcfm-ahli_manage' ) . '"><span class="wcfmfa fa-eye text_tip" data-tip="' . esc_attr__( 'View Details', 'wc-frontend-manager' ) . '"></span></a>';
				*/
				
				$index++;
			}												
		}
		if( !empty($wcfm_ahli_json_arr) ) $wcfm_ahli_json .= json_encode($wcfm_ahli_json_arr);
		else $wcfm_ahli_json .= '[]';
		$wcfm_ahli_json .= '
													}';
													
		echo $wcfm_ahli_json;
	}



	public function delete_wcfm_ahli($ahli_id){
		global $WCFM, $WCFMu, $wpdb, $_POST;
		
		$id = $ahli_id;
		$table = $wpdb->prefix . "jet_cct_member";
		$total = $wpdb->delete( $table, array( '_ID' => $id ) );

		return  $total;
	}


}