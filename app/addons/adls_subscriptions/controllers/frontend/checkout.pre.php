<?php
/**
 * HELOstore
 *
 * This source file is part of a commercial software. Only users who have purchased a valid license through
 * https://helostore.com/ and accepted to the terms of the License Agreement can install this product.
 *
 * @category   Add-ons
 * @package    HELOstore
 * @copyright  Copyright (c) 2017 HELOstore. (https://helostore.com/)
 * @license    https://helostore.com/legal/license-agreement/   License Agreement
 * @version    $Id$
 */

use HeloStore\ADLSS\Subscription\SubscriptionRepository;
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }



$subscriptionId = 0;
if ( ! empty( $_REQUEST['subscription_id'] ) ) {
	$subscriptionId = intval( $_REQUEST['subscription_id'] );
} elseif ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
	$parts = parse_url( $_SERVER['HTTP_REFERER'] );
	if ( ! empty( $parts['query'] ) ) {
		parse_str( $parts['query'], $q );
		if ( ! empty( $q['subscription_id'] ) ) {
			$subscriptionId = intval( $q['subscription_id'] );
		}
	}
}


if (! empty( $subscriptionId ) ) {

	$_SESSION['adlss_cart_copy'] = !empty($_SESSION['cart']) ? $_SESSION['cart'] : array();

	$subscriptionRepository = SubscriptionRepository::instance();
	$userId = $auth['user_id'];
	$subscription = $subscriptionRepository->findOne(array(
		'one' => true,
		'userId' => $userId,
		'id' => $subscriptionId
	));

	if ( empty( $subscription ) ) {
		return array(CONTROLLER_STATUS_NO_PAGE);
	}
	$productId = $subscription->getProductId();




	$cart = array();
	fn_clear_cart($cart);
	unset($cart['product_groups']);

	$product_data[$productId] = array(
		'product_id' => $productId,
		'amount' => 1,
		'extra' => array()
	);
//	if (isset($product_item['product_options'])) {
//		$product_data[$productId]['product_options'] = $product_item['product_options'];
//	}

	if (fn_add_product_to_cart($product_data, $cart, $auth) == array()) {
		unset($product_data['products'][$productId]);
	}




	// Set default payment
	$params = array(
		'usergroup_ids' => $auth['usergroup_ids'],
	);
	$payments = fn_get_payments($params);
	$first_method = reset($payments);
	$cart['payment_id'] = $first_method['payment_id'];

	// Get payment methods list
	$payment_methods = fn_prepare_checkout_payment_methods($cart, $auth);




	$cart['user_data'] = fn_get_user_info($auth['user_id'], empty($_REQUEST['profile']), $cart['profile_id']);

	fn_calculate_cart_content($cart, $auth, 'S', true, 'F', true);

	$_SESSION['cart'] = $cart;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	if ($mode == 'place_order') {
		if ( ! empty( $subscriptionId ) ) {


			$paymentId = $_REQUEST['payment_id'];


			$order_id = 'S' . $subscriptionId;
			fn_adlss_spoof_order_id( $order_id );


//			$GLOBALS['_adlss_order_id'] = $order_id;
//			$GLOBALS['_adlss_order'] = $_SESSION['cart'];

			$order_info = fn_get_order_info($order_id);


			$pp_response = array();
			$force_notification = array();


			list($is_processor_script, $processor_data) = fn_check_processor_script($paymentId);
			if ($is_processor_script) {
//				set_time_limit(300);
//				$idata = array (
//					'order_id' => $order_id,
//					'type' => 'S',
//					'data' => TIME,
//				);
//				db_query("REPLACE INTO ?:order_data ?e", $idata);

				$mode = Registry::get('runtime.mode');

//				Embedded::leave();


				include(fn_get_processor_script_path($processor_data['processor_script']));

				fn_finish_payment($order_id, $pp_response, $force_notification);

				return;
			}









//			$order_id = "S" . $subscriptionId;

//			$process_payment = true;

//			if (empty($params['skip_payment'])
//			    && $process_payment == true
//			    || (!empty($params['skip_payment']) && empty($auth['act_as_user']))) { // administrator, logged in as customer can skip payment
//				$payment_info = !empty($cart['payment_info']) ? $cart['payment_info'] : array();
//				fn_start_payment($order_id, array(), $payment_info);
//			}

//			fn_order_placement_routines('route', $order_id);

//			return PLACE_ORDER_STATUS_OK;

//			die( 'ok' );



//			$status = fn_checkout_place_order($cart, $auth, $_REQUEST);
//
//			if ($status == PLACE_ORDER_STATUS_TO_CART) {
//				return array(CONTROLLER_STATUS_REDIRECT, 'checkout.cart');
//			} elseif ($status == PLACE_ORDER_STATUS_DENIED) {
//				return array(CONTROLLER_STATUS_DENIED);
//			}

		}



	}
}





if ( $mode === 'checkout' ) {

}