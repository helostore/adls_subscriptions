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
        $paidStatuses = array('P');
        $isPaidStatus = in_array($statusTo, $paidStatuses);
        /** @var SubscriptionRepository $subscriptionRepository */
        $subscriptionRepository = $this->getRepository();
        $subscribableManager = SubscribableManager::instance();
        $subscribableRepository = SubscribableRepository::instance();
//        $planManager = PlanManager::instance();
        $planRepository = PlanRepository::instance();
        $orderId = $orderInfo['order_id'];
        $userId = $orderInfo['user_id'];


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

                $subscribableLink = $subscribableRepository->findOneByObject($objectId, $objectType);
                if (empty($subscribableLink)) {
                    throw new Exception('Unable to fetch subscribable link for option');
                }

                $plan = $planRepository->findOneById($subscribableLink['planId']);
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
                    if (empty($subscription)) {
                        $subscriptionId = $subscriptionRepository->create($userId, $planId, $orderId, $itemId, $productId);
                        if (empty($subscriptionId)) {
                            throw new Exception('Unable to create subscription for order ' . $orderId);
                        }
                        $subscription = $subscriptionRepository->findOneById($subscriptionId);
                        CycleManager::instance()->begin($subscription);
                    } else {
                        $subscription->activate();
                        if (!$subscriptionRepository->update($subscription)) {
                            throw new Exception('Failed updating subscription ' . __LINE__);
                        }
                    }


                    if (!$subscription->isActive() && $subscription->isNew()) {

                    }


                } else {
                    if (!empty($subscription)) {
                        if (!$subscription->isDisabled()) {
                            $subscription->disable();
                            if (!$subscriptionRepository->update($subscription)) {
                                throw new Exception('Failed updating subscription ' . __LINE__);
                            }
                        }
                    }
                }

            }
        }

        return true;
	}

    public function onGetOrderInfo(&$order, $additionalData)
    {
        $subscribableManager = SubscribableManager::instance();
        $subscribableRepository = SubscribableRepository::instance();
        $userId = $order['user_id'];
        $orderId = $order['order_id'];

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
                $planId = $subscribableLink['planId'];

                $option['subscription'] = $this->getRepository()->findOne(array(
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
     * @param Subscription $subscription
     * @param integer $thresholdDays
     *
     * @return bool
     */
    public function alert($subscription, $thresholdDays)
    {
        throw new Exception('Do me!');
    }
}