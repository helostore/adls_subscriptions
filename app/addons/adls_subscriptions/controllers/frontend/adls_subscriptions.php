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

// If user is not logged in and trying to see the order, redirect him to login form
if (empty($auth['user_id'])) {
	return array(CONTROLLER_STATUS_REDIRECT, 'auth.login_form?return_url=' . urlencode(Registry::get('config.current_url')));
}


/**
 * Add subscription renewal to cart
 *
 * @param array $product_data array with data for the certificate to add)
 * @param array $auth user session data
 * @return array array with gift certificate ID and data if addition is successful and empty array otherwise
 */
function fn_add_adls_subscription_to_cart($product_data, &$auth)
{
	if (empty($product_data) || !is_array($product_data)) {
		return array();
	}

//	fn_correct_gift_certificate($product_data);
//	$gift_cert_cart_id = fn_generate_gift_certificate_cart_id($product_data);

	if (isset($product_data['products']) && !empty($product_data['products'])) {
		foreach ((array) $product_data['products'] as $pr_id => $product_item) {
			$product_data = array();
			$product_data[$product_item['product_id']] = array(
				'product_id' => $product_item['product_id'],
				'amount' => $product_item['amount'],
				'extra' => array('parent' => array('certificate' => $gift_cert_cart_id))
			);
			if (isset($product_item['product_options'])) {
				$product_data[$product_item['product_id']]['product_options'] = $product_item['product_options'];
			}

			if (fn_add_product_to_cart($product_data, $_SESSION['cart'], $auth) == array()) {
				unset($product_data['products'][$pr_id]);
			}
		}
	}

	return array (
		$gift_cert_cart_id,
		$product_data
	);
}

if ($mode == 'renew') {

	$subscriptionRepository = SubscriptionRepository::instance();
	$userId = $auth['user_id'];
	$id = intval($_REQUEST['id']);
	if ( empty( $id ) ) {
		return array(CONTROLLER_STATUS_NO_PAGE);
	}

	$subscription = $subscriptionRepository->findOne(array(
		'one' => true,
		'userId' => $userId,
		'id' => $id
	));

	if ( empty( $subscription ) ) {
		return array(CONTROLLER_STATUS_NO_PAGE);
	}

	$productId = $subscription->getProductId();


	$product_data = array();

	if (fn_allowed_for('ULTIMATE') && Registry::get('runtime.company_id')) {
		$product_data['company_id'] = Registry::get('runtime.company_id');
	}






	$cart = array();
	fn_clear_cart($cart);
	unset($cart['product_groups']);

	$product_data[$productId] = array(
		'product_id' => $productId,
		'amount' => 1,
		'extra' => array()
	);
	if (isset($product_item['product_options'])) {
//		$product_data[$productId]['product_options'] = $product_item['product_options'];
	}

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














	fn_calculate_cart_content($cart, $auth, 'S', true, 'F', true);

//	fn_save_cart_content($_SESSION['cart'], $auth['user_id']);

//	aa($cart,1);

//	list($gift_cert_id, $gift_cert) = fn_add_adls_subscription_to_cart($product_data, $auth);



	$display_steps = array(
		'step_one'   => false,
		'step_two'   => false,
		'step_three' => false,
		'step_four'  => true,
	);
	$cart['edit_step'] = 'step_four';

	Tygh::$app['view']->assign('edit', 'step_four');
	Tygh::$app['view']->assign('display_steps', $display_steps);
	Tygh::$app['view']->assign('subscription', $subscription);
//	Tygh::$app['view']->assign('payment_methods', $payment_methods);
	Tygh::$app['view']->assign('cart', $cart);
	Tygh::$app['view']->assign('payment_methods', $payment_methods);





//	$cart = array();
//
//	// Set default payment
//	$params = array(
//		'usergroup_ids' => $auth['usergroup_ids'],
//	);
//	$payments = fn_get_payments($params);
//	$first_method = reset($payments);
//	$cart['payment_id'] = $first_method['payment_id'];
//
//	// Get payment methods list
//	$payment_methods = fn_prepare_checkout_payment_methods($cart, $auth);
//
//	Tygh::$app['view']->assign('subscription', $subscription);
//	Tygh::$app['view']->assign('payment_methods', $payment_methods);
//	Tygh::$app['view']->assign('cart', $cart);
}

//if ($mode == 'renew') {
//
//	$subscriptionRepository = SubscriptionRepository::instance();
//	$userId = $auth['user_id'];
//	$id = intval($_REQUEST['id']);
//	if ( empty( $id ) ) {
//		return array(CONTROLLER_STATUS_NO_PAGE);
//	}
//
//	$subscription = $subscriptionRepository->findOne(array(
//		'one' => true,
//		'userId' => $userId,
//		'id' => $id
//	));
//
//	if ( empty( $subscription ) ) {
//		return array(CONTROLLER_STATUS_NO_PAGE);
//	}
//
//	$cart = array();
//
//	// Set default payment
//	$params = array(
//		'usergroup_ids' => $auth['usergroup_ids'],
//	);
//	$payments = fn_get_payments($params);
//	$first_method = reset($payments);
//	$cart['payment_id'] = $first_method['payment_id'];
//
//	// Get payment methods list
//	$payment_methods = fn_prepare_checkout_payment_methods($cart, $auth);
//
//	Tygh::$app['view']->assign('subscription', $subscription);
//	Tygh::$app['view']->assign('payment_methods', $payment_methods);
//	Tygh::$app['view']->assign('cart', $cart);
//}