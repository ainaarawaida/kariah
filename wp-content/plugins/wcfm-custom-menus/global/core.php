<?php



//##function debug
if ( !function_exists( 'deb' ) ) {
	function deb($data = 'tiada') {
		print_r("<pre>");print_r($data);
	}
  }


//##hide class
add_action( 'wp_footer', 'luq_style' );
function luq_style(){
	?>
<style>
.luqhide{
	visibility: hidden;
	position:absolute;
}

</style>





<script>
jQuery( document ).ready( function( $ ) {


  if(Cookies.get('setbahasaweb') == null){
    $(window).on('load', function() {
      $( 'div.switcher.notranslate div.option a.nturl:contains("Malay")' ).click();
      Cookies.set('setbahasaweb', 'sudah');

        $(window).on('load', function() {
            window.location.reload();
          
      });

    });

    /*
    setTimeout(function() {
    $( 'div.switcher.notranslate div.option a.nturl:contains("Malay")' ).click();
    
    Cookies.set('setbahasaweb', 'sudah');

    setTimeout(function() {
       location.reload(); 
        }, 3000 );

       
        }, 3500 );
    */
  }



 
});
            

   function unserialize (data = '') {
  // From: http://phpjs.org/functions
  // +     original by: Arpad Ray (mailto:arpad@php.net)
  // +     improved by: Pedro Tainha (http://www.pedrotainha.com)
  // +     bugfixed by: dptr1988
  // +      revised by: d3x
  // +     improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +        input by: Brett Zamir (http://brett-zamir.me)
  // +     improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +     improved by: Chris
  // +     improved by: James
  // +        input by: Martin (http://www.erlenwiese.de/)
  // +     bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +     improved by: Le Torbi
  // +     input by: kilops
  // +     bugfixed by: Brett Zamir (http://brett-zamir.me)
  // +      input by: Jaroslaw Czarniak
  // +     improved by: Eli Skeggs
  // %            note: We feel the main purpose of this function should be to ease the transport of data between php & js
  // %            note: Aiming for PHP-compatibility, we have to translate objects to arrays
  // *       example 1: unserialize('a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}');
  // *       returns 1: ['Kevin', 'van', 'Zonneveld']
  // *       example 2: unserialize('a:3:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";s:7:"surName";s:9:"Zonneveld";}');
  // *       returns 2: {firstName: 'Kevin', midName: 'van', surName: 'Zonneveld'}
  var that = this,
    utf8Overhead = function (chr) {
      // http://phpjs.org/functions/unserialize:571#comment_95906
      var code = chr.charCodeAt(0);
      if (code < 0x0080) {
        return 0;
      }
      if (code < 0x0800) {
        return 1;
      }
      return 2;
    },
    error = function (type, msg, filename, line) {
      throw new that.window[type](msg, filename, line);
    },
    read_until = function (data, offset, stopchr) {
      var i = 2, buf = [], chr = data.slice(offset, offset + 1);

      while (chr != stopchr) {
        if ((i + offset) > data.length) {
          error('Error', 'Invalid');
        }
        buf.push(chr);
        chr = data.slice(offset + (i - 1), offset + i);
        i += 1;
      }
      return [buf.length, buf.join('')];
    },
    read_chrs = function (data, offset, length) {
      var i, chr, buf;

      buf = [];
      for (i = 0; i < length; i++) {
        chr = data.slice(offset + (i - 1), offset + i);
        buf.push(chr);
        length -= utf8Overhead(chr);
      }
      return [buf.length, buf.join('')];
    },
    _unserialize = function (data, offset) {
      var dtype, dataoffset, keyandchrs, keys, contig,
        length, array, readdata, readData, ccount,
        stringlength, i, key, kprops, kchrs, vprops,
        vchrs, value, chrs = 0,
        typeconvert = function (x) {
          return x;
        };

      if (!offset) {
        offset = 0;
      }
      dtype = (data.slice(offset, offset + 1)).toLowerCase();

      dataoffset = offset + 2;

      switch (dtype) {
        case 'i':
          typeconvert = function (x) {
            return parseInt(x, 10);
          };
          readData = read_until(data, dataoffset, ';');
          chrs = readData[0];
          readdata = readData[1];
          dataoffset += chrs + 1;
          break;
        case 'b':
          typeconvert = function (x) {
            return parseInt(x, 10) !== 0;
          };
          readData = read_until(data, dataoffset, ';');
          chrs = readData[0];
          readdata = readData[1];
          dataoffset += chrs + 1;
          break;
        case 'd':
          typeconvert = function (x) {
            return parseFloat(x);
          };
          readData = read_until(data, dataoffset, ';');
          chrs = readData[0];
          readdata = readData[1];
          dataoffset += chrs + 1;
          break;
        case 'n':
          readdata = null;
          break;
        case 's':
          ccount = read_until(data, dataoffset, ':');
          chrs = ccount[0];
          stringlength = ccount[1];
          dataoffset += chrs + 2;

          readData = read_chrs(data, dataoffset + 1, parseInt(stringlength, 10));
          chrs = readData[0];
          readdata = readData[1];
          dataoffset += chrs + 2;
          if (chrs != parseInt(stringlength, 10) && chrs != readdata.length) {
            error('SyntaxError', 'String length mismatch');
          }
          break;
        case 'a':
          readdata = {};

          keyandchrs = read_until(data, dataoffset, ':');
          chrs = keyandchrs[0];
          keys = keyandchrs[1];
          dataoffset += chrs + 2;

          length = parseInt(keys, 10);
          contig = true;

          for (i = 0; i < length; i++) {
            kprops = _unserialize(data, dataoffset);
            kchrs = kprops[1];
            key = kprops[2];
            dataoffset += kchrs;

            vprops = _unserialize(data, dataoffset);
            vchrs = vprops[1];
            value = vprops[2];
            dataoffset += vchrs;

            if (key !== i)
              contig = false;

            readdata[key] = value;
          }

          if (contig) {
            array = new Array(length);
            for (i = 0; i < length; i++)
              array[i] = readdata[i];
            readdata = array;
          }

          dataoffset += 1;
          break;
        default:
          error('SyntaxError', 'Unknown / Unhandled data type(s): ' + dtype);
          break;
      }
      return [dtype, dataoffset - offset, typeconvert(readdata)];
    }
  ;

  return _unserialize((data + ''), 0)[2];
}

</script>
<?php
}





//##
//####add new payment gateway
add_filter( 'wcfm_marketplace_withdrwal_payment_methods', function( $payment_methods ) {
	$payment_methods['toyyibPay'] = 'Toyyib Pay';
	return $payment_methods;
	});

add_filter( 'wcfm_marketplace_settings_fields_withdrawal_payment_keys', function( $payment_keys, $wcfm_withdrawal_options ) {
	$gateway_slug = 'toyyibPay';
	
	$withdrawal_toyyibPay_secretkey_prod = isset( $wcfm_withdrawal_options[$gateway_slug.'_secretkey_prod'] ) ? $wcfm_withdrawal_options[$gateway_slug.'_secretkey_prod'] : '';
	$withdrawal_toyyibPay_universal_category_prod = isset( $wcfm_withdrawal_options[$gateway_slug.'_universal_category_prod'] ) ? $wcfm_withdrawal_options[$gateway_slug.'_universal_category_prod'] : '';
	$payment_toyyibPay_keys = array(
	"withdrawal_".$gateway_slug."_secretkey_prod" => array('label' => __('User SecretKey', 'wc-multivendor-marketplace'), 'name' => 'wcfm_withdrawal_options['.$gateway_slug.'_secretkey_prod]', 'desc' => 'Obtain your secret key from your toyyibPay dashboard.', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele withdrawal_mode withdrawal_mode_live withdrawal_mode_'.$gateway_slug, 'label_class' => 'wcfm_title withdrawal_mode withdrawal_mode_live withdrawal_mode_'.$gateway_slug, 'value' => $withdrawal_toyyibPay_secretkey_prod ),
	"withdrawal_".$gateway_slug."_universal_category_prod" => array('label' => __('Category Code', 'wc-multivendor-marketplace'), 'name' => 'wcfm_withdrawal_options['.$gateway_slug.'_universal_category_prod]', 'desc' => 'Create a category at your toyyibPay dashboard and fill in your category code here.', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele withdrawal_mode withdrawal_mode_live withdrawal_mode_'.$gateway_slug, 'label_class' => 'wcfm_title withdrawal_mode withdrawal_mode_live withdrawal_mode_'.$gateway_slug, 'value' => $withdrawal_toyyibPay_universal_category_prod )
	);
	$payment_keys = array_merge( $payment_keys, $payment_toyyibPay_keys );
	return $payment_keys;
	}, 50, 2);

/*
add_filter( 'wcfm_marketplace_settings_fields_billing', function( $vendor_billing_fileds, $vendor_id ) {
	$gateway_slug = 'toyyibPay';
	$vendor_data = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );
	if( !$vendor_data ) $vendor_data = array();

	
	$_secretkey_prod = isset( $vendor_data['payment'][$gateway_slug]['_secretkey_prod'] ) ? esc_attr( $vendor_data['payment'][$gateway_slug]['_secretkey_prod'] ) : '' ;
	$_universal_category_prod = isset( $vendor_data['payment'][$gateway_slug]['_universal_category_prod'] ) ? esc_attr( $vendor_data['payment'][$gateway_slug]['_universal_category_prod'] ) : '' ;
	$vendor_toyyibPay_billing_fileds = array(
		"withdrawal_".$gateway_slug."_secretkey_prod" => array('label' => __('User SecretKey', 'wc-multivendor-marketplace'), 'name' => 'payment['.$gateway_slug.'][_secretkey_prod]', 'desc' => 'Obtain your secret key from your toyyibPay dashboard.', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele withdrawal_mode withdrawal_mode_live withdrawal_mode_'.$gateway_slug, 'label_class' => 'wcfm_title withdrawal_mode withdrawal_mode_live withdrawal_mode_'.$gateway_slug, 'value' => $_secretkey_prod ),
		"withdrawal_".$gateway_slug."_universal_category_prod" => array('label' => __('Category Code', 'wc-multivendor-marketplace'), 'name' => 'payment['.$gateway_slug.'][_universal_category_prod]', 'desc' => 'Create a category at your toyyibPay dashboard and fill in your category code here.', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele withdrawal_mode withdrawal_mode_live withdrawal_mode_'.$gateway_slug, 'label_class' => 'wcfm_title withdrawal_mode withdrawal_mode_live withdrawal_mode_'.$gateway_slug, 'value' => $_universal_category_prod )
		);

	$vendor_billing_fileds = array_merge( $vendor_billing_fileds, $vendor_toyyibPay_billing_fileds );
	return $vendor_billing_fileds;
	}, 10, 2);
*/

class WCFMmp_Gateway_ToyyibPay {
	public $id;
	public $message = array();
	public $gateway_title;
	public $payment_gateway;
	public $withdrawal_id;
	public $vendor_id;
	public $withdraw_amount = 0;
	public $currency;
	public $transaction_mode;
	private $reciver_email;
	public $test_mode = false;
	public $client_id;
	public $client_secret;
	public function __construct() {
	$this->id = 'toyyibPay';
	$this->gateway_title = __('Toyyib Pay', 'wc-multivendor-marketplace');
	$this->payment_gateway = $this->id;
	}
	public function gateway_logo() { global $WCFMmp; return $WCFMmp->plugin_url . 'assets/images/'.$this->id.'.png'; }
	public function process_payment( $withdrawal_id, $vendor_id, $withdraw_amount, $withdraw_charges, $transaction_mode = 'auto' ) {
	global $WCFM, $WCFMmp;
	$this->withdrawal_id = $withdrawal_id;
	$this->vendor_id = $vendor_id;
	$this->withdraw_amount = $withdraw_amount;
	$this->currency = get_woocommerce_currency();
	$this->transaction_mode = $transaction_mode;
	$this->reciver_email = $WCFMmp->wcfmmp_vendor->get_vendor_payment_account( $this->vendor_id, $this->id );
	$withdrawal_test_mode = isset( $WCFMmp->wcfmmp_withdrawal_options['test_mode'] ) ? 'yes' : 'no';
	$this->client_id = isset( $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_secretkey_prod'] ) ? $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_secretkey_prod'] : '';
	$this->client_secret = isset( $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_universal_category_prod'] ) ? $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_universal_category_prod'] : '';
	if ( $withdrawal_test_mode == 'yes') {
		exit();
	$this->test_mode = true;
	$this->client_id = isset( $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_test_client_id'] ) ? $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_test_client_id'] : '';
	$this->client_secret = isset( $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_test_secret_key'] ) ? $WCFMmp->wcfmmp_withdrawal_options[$this->id.'_test_secret_key'] : '';
	}
	if ( $this->validate_request() ) {
	// Updating withdrawal meta
	$WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta( $this->withdrawal_id, 'withdraw_amount', $this->withdraw_amount );
	$WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta( $this->withdrawal_id, 'currency', $this->currency );
	$WCFMmp->wcfmmp_withdraw->wcfmmp_update_withdrawal_meta( $this->withdrawal_id, 'reciver_email', $this->reciver_email );
	return array( 'status' => true, 'message' => __('New transaction has been initiated', 'wc-multivendor-marketplace') );
	} else {
	return $this->message;
	}
	}
	public function validate_request() {
		global $WCFMmp;
		return true;
	}
}
function misha_change_wc_gateway_if_emptyluq( $allowed_gateways ){
	global $wp, $woocommerce;
	if(isset($wp->query_vars) && $wp->query_vars['pagename'] == 'checkout' && isset($wp->query_vars['order-pay'])){
		
	
		//deb( $wp->query_vars['order-pay']);exit();
		$order = new WC_Order($wp->query_vars['order-pay']);
		$items = $order->get_items( 'line_item' );
						if( !empty( $items ) ) {
							foreach( $items as $order_item_id => $item ) {
								$line_item = new WC_Order_Item_Product( $item );
								$product  = $line_item->get_product();
								$product_id = $line_item->get_product_id();
								$vendor_id  = wcfm_get_vendor_id_by_post( $product_id );
							}
						}
		$vendorinfo = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );

		if(isset($vendorinfo['payment']['method']) && $vendorinfo['payment']['method'] == 'toyyibPay'){
			$allowed_gateways['toyyibPay']->settings['secretkey_prod'] = $vendorinfo['payment']['toyyibPay']['_secretkey_prod'] ;
			$allowed_gateways['toyyibPay']->settings['universal_category_prod'] = $vendorinfo['payment']['toyyibPay']['_universal_category_prod'] ;
			$allowed_gateways['toyyibPay']->form_fields['secretkey_prod'] = $vendorinfo['payment']['toyyibPay']['_secretkey_prod'] ;
			$allowed_gateways['toyyibPay']->form_fields['universal_category_prod'] = $vendorinfo['payment']['toyyibPay']['_universal_category_prod'] ;

		}
        
        if(isset($allowed_gateways['toyyibPay']) && $allowed_gateways['toyyibPay']->settings['secretkey_prod'] == ''){
            unset($allowed_gateways['toyyibPay']);
        }
		$allowed_gateways['bacs']->account_details[0]['account_name'] = $vendorinfo['payment']['bank']['ac_name'] ;
		$allowed_gateways['bacs']->account_details[0]['account_number'] = $vendorinfo['payment']['bank']['ac_number'] ;
		$allowed_gateways['bacs']->account_details[0]['bank_name'] = $vendorinfo['payment']['bank']['bank_name'] ;
		$allowed_gateways['bacs']->account_details[0]['iban'] = $vendorinfo['payment']['bank']['iban'] ;
		$allowed_gateways['bacs']->description= "Make your payment directly into our bank account. <br>
		Account Name: {$vendorinfo['payment']['bank']['ac_name']} <br>
		Account Number: {$vendorinfo['payment']['bank']['ac_number']} <br>
		Bank Name: {$vendorinfo['payment']['bank']['bank_name']} <br>
		Please use your Order ID as the payment reference. Your order will not be shipped until the funds have cleared in our account." ;
		
		
		

	}
	
	
	return $allowed_gateways;
 
}
add_filter('woocommerce_available_payment_gateways','misha_change_wc_gateway_if_emptyluq', 100, 1 );	




?>