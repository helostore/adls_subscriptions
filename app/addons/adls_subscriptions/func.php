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

use HeloStore\ADLSS\Subscribable;
use HeloStore\ADLSS\Subscribable\SubscribableManager;
use HeloStore\ADLSS\Subscription;
use HeloStore\ADLSS\Subscription\SubscriptionManager;
use HeloStore\ADLSS\Subscription\SubscriptionRepository;
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }


/**
 * Hooks
 */

function fn_adlss_format_product_title( $productId, $productName ) {

	$text = ' (subscription renewal)';
	if ( strstr( $productName, $text ) !== false ) {
		return $productName;
	}

	$productName = $productName . $text;

	return $productName;
}

function fn_adls_subscriptions_calculate_cart_post( $cart, $auth, $calculate_shipping, $calculate_taxes, $options_style, $apply_cart_promotions, &$cart_products, $product_groups ) {

	if ( empty( $cart ) || empty( $cart['products'] ) ) {
		return;
	}

	foreach ( $cart['products'] as $itemId => $item) {
		if ( ! empty( $item['extra'] ) && ! empty( $item['extra']['custom_product_name'] ) ) {
			$cart_products[ $itemId ]['custom_product_name'] = $item['extra']['custom_product_name'];
		}
	}
}
function fn_adls_subscriptions_get_product_name_post( $product_id, $lang_code, $as_array, &$result ) {

	$controller = Registry::get( 'runtime.controller' );
	if ( $controller != 'adls_subscriptions' ) {
		return;
	}

	$result = fn_adlss_format_product_title($product_id, $result);
}

function fn_adls_subscriptions_delete_order($orderId)
{
    $subscriptionRepository = SubscriptionRepository::instance();
    $subscriptionManager = SubscriptionManager::instance();
    list($subscriptions, ) = $subscriptionRepository->findByOrder($orderId);
	if ( empty( $subscriptions ) ) {
		return;
	}
    foreach ($subscriptions as $subscription) {
	    $subscriptionManager->delete($subscription);
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
 * @param $product_ids
 * @param $lang_code
 * @param $only_selectable
 * @param $inventory
 * @param $only_avail
 * @param $options
 */
function fn_adls_subscriptions_get_product_options_post( $product_ids, $lang_code, $only_selectable, $inventory, $only_avail, &$options ) {
	foreach ( $options as $productId => $_options ) {
		foreach ( $_options as $optionId => $option ) {
			$sub = db_get_row( 'SELECT id AS subscribableId, planId FROM ?:adlss_subscribables WHERE objectType = ?s AND objectId = ?i',
				Subscribable::OBJECT_TYPE_PRODUCT_OPTION,
				$optionId
			);
			if ( ! empty( $sub ) ) {
				$options[ $productId ][ $optionId ]['subscribableId'] = $sub['subscribableId'];
				$options[ $productId ][ $optionId ]['planId'] = $sub['planId'];
			}
		}
	}
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
 * @return void
 */
function fn_adls_subscriptions_place_order($order_id, $action, $order_status, $cart, $auth)
{
	if (empty($cart['products'])) {
        return;
    }

    SubscriptionManager::instance()->onPlaceOrder($order_id, $action, $order_status, $cart, $auth);
}

function fn_adls_subscriptions_get_order_info(&$order, $additional_data)
{
	if ( empty( $order['products'] ) ) {
		return;
	}
	foreach ( $order['products'] as $itemId => $item ) {
		if ( ! empty( $order['products'][ $itemId ]['extra'] )
		     && ! empty( $order['products'][ $itemId ]['extra']['custom_product_name'] ) ) {
			$order['products'][ $itemId ]['product'] = $order['products'][ $itemId ]['extra']['custom_product_name'];
		}
	}

	SubscriptionManager::instance()->onGetOrderInfo($order, $additional_data);
}

/**
 * Helpers
 */

function fn_adlss_is_subscribable($object)
{
    return SubscribableManager::instance()->isSubscribable($object);
}

function fn_adlss_add_to_cart_renewal_subscription( Subscription $subscription, $item, $cart, $auth ) {

	$productId = $subscription->getProductId();

	// Prepare product data for cart
	//	fn_clear_cart($cart);
	$name = fn_adlss_format_product_title($item['product_id'], $item['product']);
	fn_define( 'ORDER_MANAGEMENT', true );
	$amount = 1;
	$product_data = array(
		'product_id' => $productId,
		'amount' => $amount,
		'product_options' => $item['extra']['product_options'],
		'price' => 0,
		'stored_price' => 'Y',
		'extra' => array(
			'custom_product_name' => $name,
			'stored_price' => 0,
			'subscription_id' => $subscription->getId()
		)
	);


	$product_options = fn_get_product_options(array($productId), CART_LANGUAGE, true);
	$subscribableOptionId = '';
	foreach ( $product_options[$productId] as $optionId => $option ) {
		if ( !empty( $option['planId'] ) &&  $option['planId'] == $subscription->getPlanId() ) {
			$subscribableOptionId = $optionId;
			break;
		}
	}

	if ( empty( $subscribableOptionId ) ) {
		throw new \Exception('Unable to find $subscribableOptionId');
	}
	$defaultCycleVariantId = null;

	foreach ( $product_options[$productId][ $subscribableOptionId ]['variants'] as $variant ) {
		if ( ! empty( $variant['modifier'] ) && $variant['modifier'] == $subscription->getAmount() ) {
			$defaultCycleVariantId = $variant['variant_id'];
			break;
		}
	}


	$product_data['product_options'][ $subscribableOptionId ] = $defaultCycleVariantId;

	$price = $product_data['price'];
	$price = fn_apply_options_modifiers($product_data['product_options'], $price, 'P', array(), array('product_data' => $product_data));
	$product_data['price'] = $price;
	$product_data['extra']['price'] = $price;
	$product_data['extra']['stored_price'] = 'Y';
	$request_products[ $productId ] = $product_data;


	// Add product to cart & recalculate
	$prev_cart_products = empty($cart['products']) ? array() : $cart['products'];
	if (fn_add_product_to_cart($request_products, $cart, $auth) == array()) {
		unset($request_products['products'][$productId]);
	}
	fn_calculate_cart_content($cart, $auth, 'S', true, 'F', true);

	return $cart;
}
