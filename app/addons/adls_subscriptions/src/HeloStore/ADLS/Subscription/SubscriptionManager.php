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

use Exception;
use Tygh\Mailer;
use Tygh\Registry;

class SubscriptionManager extends Manager
{
    /**
     * @var SubscriptionRepository
     */
    protected $repository;

    public function __construct()
    {
        $this->setRepository(SubscriptionRepository::instance());
    }

	/**
	 * Hooks
	 */

    /**
     * @param $statusTo
     * @param $statusFrom
     * @param $orderInfo
     * @param $forceNotification
     * @param $orderStatuses
     * @param $placeOrder
     *
     * @return bool
     *
     * @throws Exception
     */
	public function onChangeOrderStatus($statusTo, $statusFrom, $orderInfo, $forceNotification, $orderStatuses, $placeOrder)
	{
        $orderInfo['status_from'] = $statusFrom;

        return $this->processOrder($orderInfo, $statusTo);
	}

    public function onGetOrderInfo(&$order, $additionalData)
    {
        $subscribableManager = SubscribableManager::instance();
        $subscribableRepository = SubscribableRepository::instance();
        $userId = $order['user_id'];
        $orderId = $order['order_id'];

        if (empty($order['products'])) {
            return;
        }

        foreach ($order['products'] as &$product) {
            $productId = $product['product_id'];
            $itemId = $product['item_id'];

            if (empty($product['product_options'])) {
                continue;
            }

            foreach ($product['product_options'] as &$option) {

                $productOption = fn_get_product_option_data($option['option_id'], $option['product_id']);

                if (!$subscribableManager->isSubscribable($productOption)) {
                    continue;
                }

                $subscribableLink = $subscribableRepository->findOneByObject($option['option_id'], Subscribable::OBJECT_TYPE_PRODUCT_OPTION);
                if (empty($subscribableLink)) {
                    throw new Exception('Unable to fetch subscribable link for option');
                }
                $planId = $subscribableLink->getPlanId();

                // @TODO the question is: to assume or not to assume one subscription per product?
                $product['subscription'] = $this->getRepository()->findOne(array(
                    'userId' => $userId,
                    'planId' => $planId,
                    'orderId' => $orderId,
                    'itemId' => $itemId,
                    'productId' => $productId,
                ));
            }
            unset($option);
        }
        unset($product);
    }


	/**
	 * Methods
	 */

    /**
     * @param $orderInfo
     * @param null $status
     * @return bool
     * @throws Exception
     */
    public function processOrder($orderInfo, $status = null)
    {
        if (!$status) {
            $status = $orderInfo['status'];
        }

        $paidStatuses = array('P');
        $isPaidStatus = in_array($status, $paidStatuses);

        /** @var SubscriptionRepository $subscriptionRepository */
        $subscriptionRepository = $this->getRepository();
        $subscribableManager = SubscribableManager::instance();
        $subscribableRepository = SubscribableRepository::instance();
//        $planManager = PlanManager::instance();
        $planRepository = PlanRepository::instance();
        $orderId = $orderInfo['order_id'];
        $userId = $orderInfo['user_id'];
        $companyId = $orderInfo['company_id'];


        foreach ($orderInfo['products'] as $product) {
            $productId = $product['product_id'];
            $itemId = $product['item_id'];

            if (empty($product['product_options'])) {
                continue;
            }

            foreach ($product['product_options'] as $option) {
                $productOption = fn_get_product_option_data($option['option_id'], $option['product_id']);

                if (!$subscribableManager->isSubscribable($productOption)) {
                    continue;
                }
                $objectId = $option['option_id'];
                $objectType = Subscribable::OBJECT_TYPE_PRODUCT_OPTION;

                // @TODO add a field for days in option-variant, and replace `position` functionality
                $initialPaidPeriod = $option['position']; // months

                $subscribableLink = $subscribableRepository->findOneByObject($objectId, $objectType);
                if (empty($subscribableLink)) {
                    throw new Exception('Unable to fetch subscribable link for option');
                }

                $plan = $planRepository->findOneById($subscribableLink->getPlanId());
                if (!$plan instanceof Plan) {
                    throw new Exception('Unable to fetch plan from subscribable link');
                }

                $planId = $plan->getId();



                /** @var Subscription $subscription */
                if (!empty($orderInfo['subscription'])) {
                    $subscription = $orderInfo['subscription'];
                } else {
                    $subscription = $subscriptionRepository->findOne(array(
                        'userId' => $userId,
                        'planId' => $planId,
                        'orderId' => $orderId,
                        'itemId' => $itemId,
                        'productId' => $productId,
                    ));
                }



                if ($isPaidStatus) {
                    // If order paid successfully
                    if (empty($subscription)) {
                        // Create a new subscription
                        $subscriptionId = $subscriptionRepository->create($userId, $planId, $orderId, $itemId, $productId, $companyId);
                        if (empty($subscriptionId)) {
                            throw new Exception('Unable to create subscription for order ' . $orderId);
                        }
                        $subscription = $subscriptionRepository->findOneById($subscriptionId);
                        $this->begin($subscription, $initialPaidPeriod);
                        fn_set_hook('adls_subscriptions_post_begin', $subscription, $product, $orderInfo);
                    } else {
                        // Activate existing subscription
                        $this->resume($subscription);
                        fn_set_hook('adls_subscriptions_post_resume', $subscription, $product, $orderInfo);
                    }


                    if (!$subscription->isActive() && $subscription->isNew()) {

                    }


                } else {
                    // If order payment failed, inactivate the subscription
                    if (!empty($subscription)) {
                        // @TODO: maybe call suspend() instead
                        if (!$subscription->isInactive()) {
                            $subscription->inactivate();
                            $subscriptionRepository->update($subscription);
                            fn_set_hook('adls_subscriptions_post_fail', $subscription, $product, $orderInfo);
                        }
                    }
                }

            }
        }

        return true;
    }


    /**
     * Activates/configures a new subscription
     *
     * @param Subscription $subscription
     * @param integer $initialPaidPeriod Initial paid period, number of months
     * @return bool
     */
    public function begin(Subscription $subscription, $initialPaidPeriod = null)
    {
        $planId = $subscription->getPlanId();
        $subscriptionRepository = SubscriptionRepository::instance();
        $planRepository = PlanRepository::instance();
        $plan = $planRepository->findOneById($planId);

        $subscription->setStartDate(new \DateTime());
        $subscription->setEndDate(new \DateTime());
        if (!empty($initialPaidPeriod)) {
            $paidCycles = $initialPaidPeriod / $plan->getCycle();
            $subscription->payCycle($paidCycles);
            $subscription->getEndDate()->modify('+ ' . $initialPaidPeriod . ' months');
        } else {
            $subscription->getEndDate()->modify('+ ' . $plan->getCycle() . ' months');
            $subscription->payCycle();
        }

        $subscription->activate();
        return $subscriptionRepository->update($subscription);
    }

    /**
     * Resume / reactivate existing subscription
     *
     * @param Subscription $subscription
     * @return bool
     */
    public function resume(Subscription $subscription)
    {
        $subscription->activate();
        $result = $this->repository->update($subscription);

        return $result;
    }

    /**
     * Suspends a past-due subscription
     *
     * @param Subscription $subscription
     * @return bool
     * @throws Exception
     * @throws \Tygh\Exceptions\DeveloperException
     */
    public function suspend(Subscription $subscription)
    {
        $subscriptionRepository = SubscriptionRepository::instance();

        $subscription->elapseCycle();
        $subscription->inactivate();


        // Change order status to Expired (A)
        $order = fn_get_order_info($subscription->getOrderId());
        if (empty($order)) {
            throw new Exception('Failed while suspending subscription: order not found');
        }

        $settings = Utils::instance()->getSettings();

        $statusTo = $settings['order_status_on_suspend'];
        $orderId = $subscription->getOrderId();
        $forceNotification = array();

        if (defined('ADLS_SUBSCRIPTIONS_NO_EMAILS')) {
            $forceNotification = array('C' => false, 'A' => false, 'V' => false);
        }
        fn_change_order_status($orderId, $statusTo, $statusFrom = '', $forceNotification, $placeOrder = false);


        fn_set_hook('adls_subscriptions_post_suspend', $subscription);

        return $subscriptionRepository->update($subscription);
    }

    /**
     * @param Subscription $subscription
     * @param array $alert
     *
     * @return bool
     *
     * @throws Exception
     */
    public function alert($subscription, $alert)
    {
        $userId = $subscription->getUserId();
        $user = fn_get_user_info($userId);
        if (empty($user)) {
            throw new Exception('User not found');
        }
        $order = fn_get_order_info($subscription->getOrderId());
        if (empty($order)) {
            throw new Exception('Order not found');
        }

        $productId = $subscription->getProductId();
        $auth = array();
        $auth = fn_fill_auth($user, $auth);
        $product = fn_get_product_data($productId, $auth);

        if (empty($product)) {
            throw new Exception('Product not found');
        }


        $plan = PlanRepository::instance()->findOneById($subscription->getPlanId());

        if (empty($plan)) {
            throw new Exception('Plan not found');
        }

        $alert['title'] = $product['product'] . ' Subscription';
        $alert['subtitle'] = $plan->getName();

        $companyId = $subscription->getCompanyId();

//        $template = 'addons/adls_subscriptions/alerts/' . $alert['template'];
        $template = 'addons/adls_subscriptions/alert.tpl';
        if (defined('ADLS_SUBSCRIPTIONS_NO_EMAILS')) {
            return true;
        }

        $result = Mailer::sendMail(array(
            'to' => $user['email'],
            'from' => 'company_orders_department',
            'reply_to' => 'company_orders_department',
            'data' => array(
                'subscription' => $subscription,
                'alert' => $alert,
            ),
            'tpl' => $template,
            'company_id' => $companyId,
        ), 'A', $order['lang_code']);

        return $result;
    }
}