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

use HeloStore\ADLSS\Subscription\SubscriptionManager;
use HeloStore\ADLSS\Subscription\SubscriptionRepository;
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ( $mode === 'add' ) {
	$subscriptionId = 0;
	if ( ! empty( $_REQUEST['subscription_id'] ) ) {
		$subscriptionId = intval( $_REQUEST['subscription_id'] );
	}

	if ( empty( $subscriptionId ) ) {
		return array(CONTROLLER_STATUS_NO_PAGE);
	}



	// Fetch needed data
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
	$itemId = $subscription->getItemId();
	$subscriptionManager = SubscriptionManager::instance();
	$item  = $subscriptionManager->getSubscriptionItem($subscription);
	if ( empty( $item ) ) {
		return array(CONTROLLER_STATUS_DENIED);
	}

	$cart = &$_SESSION['cart'];
	$cart = fn_adlss_add_to_cart_renewal_subscription( $subscription, $item, $cart, $auth );


	$product_cnt = 0;
	$added_products = array();
	foreach ($cart['products'] as $key => $data) {
		if (empty($prev_cart_products[$key]) || !empty($prev_cart_products[$key]) && $prev_cart_products[$key]['amount'] != $data['amount']) {
			$added_products[$key] = $data;
			$added_products[$key]['product_option_data'] = fn_get_selected_product_options_info($data['product_options']);
			if (!empty($prev_cart_products[$key])) {
				$added_products[$key]['amount'] = $data['amount'] - $prev_cart_products[$key]['amount'];
			}
			$product_cnt += $added_products[$key]['amount'];
		}
	}

	if (!empty($added_products)) {
		Tygh::$app['view']->assign('added_products', $added_products);
		if (Registry::get('config.tweaks.disable_dhtml') && Registry::get('config.tweaks.redirect_to_cart')) {
			Tygh::$app['view']->assign('continue_url', (!empty($_REQUEST['redirect_url']) && empty($_REQUEST['appearance']['details_page'])) ? $_REQUEST['redirect_url'] : $_SESSION['continue_url']);
		}

		$msg = Tygh::$app['view']->fetch('views/checkout/components/product_notification.tpl');
		fn_set_notification('I', __($product_cnt > 1 ? 'products_added_to_cart' : 'product_added_to_cart'), $msg, 'I');
		$cart['recalculate'] = true;
	} else {
		fn_set_notification('N', __('notice'), __('product_in_cart'));
	}


	if (!empty($_SERVER['HTTP_REFERER'])) {
		return array( CONTROLLER_STATUS_REDIRECT, $_SERVER['HTTP_REFERER'] );
	}

	return array(CONTROLLER_STATUS_OK, 'checkout.cart');
}


if ( $mode === 'manage' ) {
	$userId = $auth['user_id'];
	$subscriptionRepository = SubscriptionRepository::instance();
	$userId = $auth['user_id'];
	list($subscriptions, $search) = $subscriptionRepository->find(array(
		'userId' => $userId
	));


	Tygh::$app['view']->assign('subscriptions', $subscriptions);
	Tygh::$app['view']->assign('search', $search);

}