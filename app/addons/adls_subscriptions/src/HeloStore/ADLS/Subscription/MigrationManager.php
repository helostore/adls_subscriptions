<?php
/**
 * HELOstore
 *
 * This source file is part of a commercial software. Only users who have purchased a valid license through
 * https://helostore.com/ and accepted to the terms of the License Agreement can install this product.
 *
 * @category   Add-ons
 * @package    HELOstore
 * @copyright  Copyright (c) 2015-2016 HELOstore. (https://helostore.com/)
 * @license    https://helostore.com/legal/license-agreement/   License Agreement
 * @version    $Id$
 */
namespace HeloStore\ADLS\Subscription;


class MigrationManager extends Manager
{

    public function migrate($order)
    {
        if (!in_array($order['status'], array('P'))) {
            return;
        }

        $subscriptionManager = SubscriptionManager::instance();
        $subscriptionRepository = SubscriptionRepository::instance();
        $orderId = $order['order_id'];
        list($subscriptions, $search) = $subscriptionRepository->findByOrder($orderId);

        if (!empty($subscriptions)) {
            aa('Order ' . $orderId . ' has ' . count($subscriptions) . ' subscriptions, skipping..');
            return;
        }

//        $order = fn_get_order_info($order['order_id']);
        $order = fn_get_order_info($order['order_id']);
        $this->updateOrderProducts($order);
        $order['prev_status'] = 'O';
        $subscriptionManager->processOrder($order);

        list($subscriptions, $search) = $subscriptionRepository->findByOrder($orderId);
        if (empty($subscriptions)) {
            aa('Fail. No subscriptions generated for order ' . $orderId . '');
            return;
        }
        aa('Order ' . $orderId . ' generated ' . count($subscriptions) . ' subscriptions');
        aa('Sending alert about the migration.. (STUB)');

    }

    /**
     * Update products in cart by adding default options to them, so that default subscription will apply
     *
     * @param $order
     * @return mixed
     * @throws \Exception
     */
	public function updateOrderProducts($order)
	{
        $initialOrder = $order;

        $initialStatus = $initialOrder['status'];
        $initialOrderTotal = floatval($initialOrder['total']);

        $cart = array();
        fn_clear_cart($cart, true);
        $customer_auth = fn_fill_auth(array(), array(), false, 'C');

        $cart_status = md5(serialize($cart));
        fn_form_cart($order['order_id'], $cart, $customer_auth);

        if (!empty($cart['product_groups'])) {
            foreach ($cart['product_groups'] as $group_key => $group) {
                if (!empty($group['chosen_shippings'])) {
                    foreach ($group['chosen_shippings'] as $shipping_key => $shipping) {
                        if (!empty($shipping['stored_shipping']) && empty($cart['stored_shipping'][$group_key][$shipping_key])) {
                            $cart['stored_shipping'][$group_key][$shipping_key] = $shipping['rate'];
                        }
                    }
                }
            }
        }

        if ($cart_status == md5(serialize($cart))) {
            // Order info was not found or customer does not have enought permissions
            throw new \Exception('Order info was not found or customer does not have enought permissions');
        }
        $cart['order_id'] = $order['order_id'];

        foreach ($cart['products'] as $k => $product) {

            $cart['products'][$k]['stored_price'] = 'Y';
            if (!empty($initialOrder['products'][$k])) {
                $cart['products'][$k]['price'] = $initialOrder['products'][$k]['price'];
            }

            $productId = $product['product_id'];
            $productDefaultOptions = fn_get_default_product_options($productId, true);
            if (empty($productDefaultOptions)) {
                continue;
            }
            if (!isset($product['extra'])) {
                $cart['products'][$k]['extra'] = array();
            }

            if (!isset($product['product_options'])) {
                $cart['products'][$k]['extra']['product_options'] = array();
            }
            if (!isset($product['extra']['product_options'])) {
                $cart['products'][$k]['extra']['product_options'] = array();
            }
            foreach ($productDefaultOptions as $optionId => $variantId) {
                // Set default variant ID
                if (empty($product['extra']['product_options'][$optionId])) {
                    $cart['products'][$k]['extra']['product_options'][$optionId] = $variantId;
                }
                if (empty($product['product_options'][$optionId])) {
                    $cart['products'][$k]['product_options'][$optionId] = $variantId;
                }
            }
        }

        list ($cart_products, $product_groups) = fn_calculate_cart_content($cart, $customer_auth);


        if ($initialOrderTotal == 0) {
            $cart['total'] = 0;
        }

        $action = 'save';
        $cart['notes'] = 'Order automatically migrated to subscription tier';

        list($order_id, $process_payment) = fn_place_order($cart, $customer_auth, $action, $order['user_id']);

        $currentStatus = db_get_field('SELECT status FROM ?:orders WHERE order_id = ?i', $order_id);
        if ($currentStatus != $initialStatus) {
            db_query('UPDATE ?:orders SET status = ?s WHERE order_id = ?i', $initialStatus, $order_id);

        }

//        $order = fn_get_order_info($order['order_id']);

        return $order;
	}
}