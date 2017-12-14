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
namespace HeloStore\ADLSS;


use HeloStore\ADLSS\Base\Manager;
use HeloStore\ADLSS\Plan\PlanRepository;
use HeloStore\ADLSS\Subscription\SubscriptionManager;
use HeloStore\ADLSS\Subscription\SubscriptionRepository;
use Tygh\Mailer;

class MigrationManager extends Manager
{
	/**
	 * @var integer
	 */

    public $optionId;

	/**
	 * @var integer
	 */
    public $variantId;

    /**
     * Assign subscribable option to product, as a global link
     *
     * @param $productId
     * @param $optionId
     * @return mixed
     */
    public function migrateProduct($productId, $optionId)
    {
        $result = db_query("REPLACE INTO ?:product_global_option_links (option_id, product_id) VALUES(?i, ?i)", $optionId, $productId);

        if (fn_allowed_for('ULTIMATE')) {
            fn_ult_share_product_option($optionId, $productId);
        }

        return $result;
    }

	/**
	 * @param $order
	 *
	 * @return bool
	 * @throws \Exception
	 */
    public function migrateOrder($order)
    {

        if (!in_array($order['status'], array('P'))) {
            return false;
        }

        $subscriptionRepository = SubscriptionRepository::instance();
        $orderId = $order['order_id'];
        list($subscriptions, $search) = $subscriptionRepository->findByOrder($orderId, array('extended' => true));

        if (!empty($subscriptions)) {
            fn_print_r('Order #' . $orderId . ' has ' . count($subscriptions) . ' subscriptions, skipping..');
            return false;
        }

        $order = fn_get_order_info($order['order_id']);
        $this->updateOrderProducts($order);
        $order['prev_status'] = 'O';

        list($subscriptions, $search) = $subscriptionRepository->findByOrder($orderId, array('extended' => true));
        if (empty($subscriptions)) {
            fn_print_r('Warning: No subscriptions generated for order #' . $orderId);
            return false;
        }

        // Refresh order data, missing required products may have been automatically added
        $order = fn_get_order_info($order['order_id']);

        fn_print_r('Order #' . $orderId . ' generated ' . count($subscriptions) . ' subscriptions for ' . count($order['products']) .' order items');
        fn_print_r(' - sending alert about the migration..');

        $result = $this->alert($order, $subscriptions);
        if ($result == false) {
            fn_print_r(' - failed');
        } else {
            fn_print_r(' - success: ' . $result);
        }
        sleep(1);
        ob_flush();

        return true;
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
            // Order info was not found or customer does not have enough permissions
            throw new \Exception('Order info was not found or customer does not have enought permissions');
        }
        $cart['order_id'] = $order['order_id'];

        $upgrade = false;

        foreach ($cart['products'] as $k => &$product) {

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
            if (isset($cart['products'][$k]['product_options'][$this->optionId])) {
//                fn_print_r('Order #' . $cart['order_id'] . ', product #' . $product['product_id'] . ', item #' . $k . ' - already has subscribable option set, rewriting');
            } else {
//                fn_print_r('Order #' . $cart['order_id'] . ', product #' . $product['product_id'] . ', item #' . $k . ' - NO subscribable option set, setting');
            }

//            fn_print_r($productDefaultOptions);
            $upgrade = true;
//            $cart['products'][$k]['product_options'][$this->optionId] = $productDefaultOptions[$this->optionId];
//            fn_print_r($product, 1);

	        // Set default variant IDs for missing options (ex. for Upgrade subscription option)
            foreach ($productDefaultOptions as $optionId => $variantId) {
                if (empty($variantId)) {
                    continue;
                }
                // Set default variant ID
//                if (empty($product['extra']['product_options'][$optionId])) {
//                    $cart['products'][$k]['extra']['product_options'][$optionId] = $variantId;
//                }
                if (empty($product['product_options'][$optionId])) {
                    $cart['products'][$k]['product_options'][$optionId] = $variantId;
                }
            }

            // Enforce variant ID corresponding to specified complementary months
	        if (isset($cart['products'][$k]['product_options'][$this->optionId])) {
		        $cart['products'][$k]['product_options'][$this->optionId] = $this->variantId;
	        }
        }

        unset($product);
        if (!$upgrade) {
            fn_print_r('Order #' . $cart['order_id'] . ', nothing to upgrade, skipping');
            return true;
        }

        list ($cart_products, $product_groups) = fn_calculate_cart_content($cart, $customer_auth);

        if ($initialOrderTotal == 0) {
            $cart['total'] = 0;
        }

        $action = 'save';
        $cart['notes'] = 'Order automatically migrated to subscription tier on ' . date('d.m.Y');

        $cart['status'] = $initialStatus;

        list($order_id, $process_payment) = fn_place_order($cart, $customer_auth, $action, $order['user_id']);

        $currentStatus = db_get_field('SELECT status FROM ?:orders WHERE order_id = ?i', $order_id);
        if ($currentStatus != $initialStatus) {
            db_query('UPDATE ?:orders SET status = ?s WHERE order_id = ?i', $initialStatus, $order_id);

        }

//        $order = fn_get_order_info($order['order_id']);

        return $order;
	}


    /**
     *
     * @param $order
     * @param $subscriptions
     * @return bool
     */
    public function alert($order, $subscriptions)
    {
        $affectedProducts = count($subscriptions);

        if (empty($affectedProducts)) {
            return true;
        }
        $alert = array();
        $alert['subject'] = $alert['title'] = 'Order #' . $order['order_id'] . ' Update';
        $alert['subtitle'] = '';
//        $alert['subtitle'] = 'This shouldn\'t affect the current state of your order.';
//        $alert['subtitle'] = 'HELOstore is moving to subscription-based products.';
//        $alert['subtitle'] = ($affectedProducts == 1 ? 'One item has' : $affectedProducts. ' items have') . ' been migrated to subscription tier.';
        $alert['excerpt'] = 'Your order has been migrated to subscription tier';
        $alert['template'] = 'migration.tpl';


        $companyId = $order['company_id'];
        $user = fn_get_user_info($order['user_id']);
        $template = 'addons/adls_subscriptions/alert.tpl';

        if (!defined('ADLS_SUBSCRIPTIONS_NO_EMAILS')) {
            $result = Mailer::sendMail(array(
                'to' => $user['email'],
                'from' => 'company_orders_department',
                'reply_to' => 'company_orders_department',
                'data' => array(
                    'alert' => $alert,
                    'order' => $order,
                    'subscriptions' => $subscriptions,
                ),
                'tpl' => $template,
                'company_id' => $companyId,
            ), 'A', $order['lang_code']);

        } else {
            $result = 'dry_run';
        }

        if (class_exists('\\HeloStore\\ADLS\\Logger')) {
            \HeloStore\ADLS\Logger::instance()->log(
                $_REQUEST
                , $_SERVER
                , \HeloStore\ADLS\Logger::OBJECT_TYPE_SUBSCRIPTION_MIGRATE_ALERT
                , 'send'
                , array(
                    'result' => $result
                    , 'alert' => json_encode($alert)
                )
            );
        }

        return $result;
    }
}