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

use HeloStore\ADLSS\Subscribable\SubscribableManager;
use HeloStore\ADLSS\Subscription\SubscriptionManager;
use HeloStore\ADLSS\Subscription\SubscriptionRepository;

if (!defined('BOOTSTRAP')) { die('Access denied'); }


/**
 * Hooks
 */

function fn_adls_subscriptions_delete_order($orderId)
{
    $subscriptionRepository = SubscriptionRepository::instance();
    list($subscriptions, ) = $subscriptionRepository->findByOrder($orderId);
	if ( empty( $subscriptions ) ) {
		return;
	}
    foreach ($subscriptions as $subscription) {
        $subscriptionRepository->delete($subscription);
    }
}

/**
 * @param $licenseId
 * @param $orderId
 * @param $productId
 */
function fn_adls_subscriptions_adls_api_license_pre_activation($licenseId, $orderId, $productId)
{

}

/**
 * @param $option_id
 * @param $product_id
 * @param $fields
 * @param $condition
 * @param $join
 * @param $extra_variant_fields
 * @param $lang_code
 * 
 * @return bool
 */
function fn_adls_subscriptions_get_product_option_data_pre($option_id, $product_id, &$fields, $condition, &$join, $extra_variant_fields, $lang_code)
{
    return SubscribableManager::instance()->onGetProductOptionDataPre($option_id, $product_id, $fields, $condition, $join, $extra_variant_fields, $lang_code);
}


/**
 * @param $status_to
 * @param $status_from
 * @param $orderInfo
 * @param $force_notification
 * @param $order_statuses
 * @param $place_order
 *
 * @return bool
 *
 * @throws Exception
 */
function fn_adls_subscriptions_change_order_status($status_to, $status_from, $orderInfo, $force_notification, $order_statuses, $place_order)
{
    return SubscriptionManager::instance()->onChangeOrderStatus($status_to, $status_from, $orderInfo, $force_notification, $order_statuses, $place_order);
}

/**
 * @param $order_id
 * @param $action
 * @param $order_status
 * @param $cart
 * @param $auth
 *
 * @return bool
 */
function fn_adls_subscriptions_place_order($order_id, $action, $order_status, $cart, $auth)
{
    if (empty($cart['products'])) {
        return false;
    }

    return SubscriptionManager::instance()->onPlaceOrder($order_id, $action, $order_status, $cart, $auth);
}

function fn_adls_subscriptions_get_order_info(&$order, $additional_data)
{

	if ( ! empty( $GLOBALS['_adlss_order_id'] ) ) {
		$order = fn_adlss_emulate_order( $GLOBALS['_adlss_order_id'], $GLOBALS['_adlss_order'] );
	} else {
	    SubscriptionManager::instance()->onGetOrderInfo($order, $additional_data);
	}
}


function fn_settings_variants_addons_adls_subscriptions_order_status_on_suspend()
{
    $statuses = fn_get_simple_statuses(STATUSES_ORDER);

    return $statuses;
}

/**
 * Helpers
 */

function fn_adlss_spoof_order_id( $adlss_order_id ) {
	$GLOBALS['_adlss_order_id'] = $adlss_order_id;
	$GLOBALS['_adlss_order'] = $_SESSION['cart'];
}
function fn_adlss_emulate_order( $adlss_order_id, $cart ) {

	$lang_code = $cart['lang_code'];
	foreach ( $cart['products'] as $k => $v) {
		$cart['products'][$k]['subtotal'] = $v['price'];
		$cart['products'][$k]['product_options'] = fn_get_selected_product_options_info($v['extra']['product_options'], $lang_code);
	}

	$order_info = array(
		'order_id'      => $adlss_order_id,
		'shipping_cost' => $cart['shipping_cost'],
		'total'         => $cart['total'],
		'products'         => $cart['products'],
		'repaid'        => 0,
	);

	$order_info = array_merge( $order_info, $cart['user_data'] );
	$order_info['status'] = 'P';


//	aa( $order_info, 1 );

	return $order_info;
}
function fn_adlss_is_subscribable($object)
{
    return SubscribableManager::instance()->isSubscribable($object);
    
}