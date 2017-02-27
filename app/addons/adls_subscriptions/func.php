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

use HeloStore\ADLS\Subscription\SubscribableManager;
use HeloStore\ADLS\Subscription\SubscriptionManager;
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }


/**
 * Hooks
 */

function fn_adls_subscriptions_get_product_files_post($params, $files)
{
    $productId = $params['product_id'];
    $orderId = $params['order_id'];
}

/**
 * @TODO : move in ADLS, it's dependent by releases
 *
 * @param $params
 * @param $fields
 * @param $join
 * @param $condition
 */
function fn_adls_subscriptions_get_product_files_before_select($params, &$fields, &$join, &$condition)
{

//    return;
//    $productId = $params['product_id'];
//    $orderId = $params['order_id'];
//
//    $join .= db_quote('
//        LEFT JOIN cscart_adls_releases AS releases
//            ON cscart_product_files.file_id = releases.fileId
//
//        LEFT JOIN cscart_adlss_subscriptions AS subscription
//            ON subscription.orderId = ?n
//            AND subscription.productId = cscart_product_files.product_id
//    ', $orderId);
//
//    $fields[] = 'subscription.id AS subscriptionId';
//    $fields[] = 'subscription.startDate AS subscriptionStartDate';
//    $fields[] = 'subscription.endDate AS subscriptionEndDate';
//    $fields[] = 'releases.createdAt AS releaseDate';
//    $fields[] = 'releases.id AS releaseId';
//
//    $condition .= db_quote(' AND
//        (
//            (
//                subscription.id IS NOT NULL
//                AND releases.id IS NOT NULL
//                AND (
//                    releases.createdAt <= subscription.endDate
//                    AND releases.createdAt >= subscription.startDate
//                )
//
//            )
//        )
//
//    ');
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
 * @param $opt
 * @param $product_id
 * @param $lang_code
 *
 * @return bool
 */
function fn_adls_subscriptions_get_product_option_data_post(&$opt, $product_id, $lang_code)
{
    return SubscribableManager::instance()->onGetProductOptionDataPost($opt, $product_id, $lang_code);
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

function fn_adls_subscriptions_get_order_info(&$order, $additional_data)
{
    return SubscriptionManager::instance()->onGetOrderInfo($order, $additional_data);
}


function fn_settings_variants_addons_adls_subscriptions_order_status_on_suspend()
{
    $statuses = fn_get_simple_statuses(STATUSES_ORDER);

    return $statuses;
}

/**
 * Helpers
 */

function fn_adlss_is_subscribable($object)
{
    return SubscribableManager::instance()->isSubscribable($object);
    
}