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
namespace HeloStore\ADLSS\Subscription;

use HeloStore\ADLSS\Base\EntityRepository;
use HeloStore\ADLSS\Subscription;
use HeloStore\ADLSS\Utils;

class SubscriptionRepository extends EntityRepository
{
	protected $table = '?:adlss_subscriptions';

	/**
	 * Create new subscription
	 *
	 * @param integer $userId
	 * @param integer $planId
	 * @param $orderId
	 * @param $itemId
	 * @param integer $productId
	 * @param float $amount
	 * @param $companyId
	 *
	 * @return bool|int
	 */
	public function create($userId, $planId, $orderId, $itemId, $productId, $amount, $companyId)
	{
		$date = Utils::instance()->getCurrentDate();

		$neverExpires = 0;

		$data = array(
			'userId' => $userId,
			'planId' => $planId,
			'orderId' => $orderId,
			'itemId' => $itemId,
			'productId' => $productId,
			'amount' => $amount,
			'companyId' => $companyId,
            'status' => Subscription::STATUS_INACTIVE,
			'startDate' => null,
			'endDate' => null,
			'neverExpires' => $neverExpires,
			'paidCycles' => 0,
			'elapsedCycles' => 0,
			'createdAt' => $date->format('Y-m-d H:i:s'),
			'updateAt' => $date->format('Y-m-d H:i:s'),
		);
		$query = db_quote( 'INSERT INTO ' . $this->table . ' ?e', $data );
		$subscriptionId = db_query($query);

		return $subscriptionId;
	}

    /**
     * @param Subscription $subscription
     *
     * @return bool
     */
	public function update(Subscription $subscription)
	{
		$subscription->setUpdatedAt(new \DateTime());
		$data = $subscription->toArray();
		$data['updatedAt'] = Utils::instance()->getCurrentDate()->format('Y-m-d H:i:s');
        $query = db_quote('UPDATE ' . $this->table . ' SET ?u WHERE id = ?d', $data, $subscription->getId());
		$result = db_query($query);
        // $result is unreliable, it returns false when the data didn't changed
//		return $result;

        return true;
	}


    /**
     * Delete
     *
     * @param Subscription $subscription
     *
     * @return bool|int
     */
    public function delete($subscription)
    {
        return db_query('DELETE FROM ?p WHERE id = ?d', $this->table, $subscription->getId());
    }

	/**
	 * @param array $params
	 *
	 * @return Subscription[]|Subscription|null
	 */
	public function find($params = array())
	{
        // Set default values to input params
        $defaultParams = array (
            'page' => 1,
            'items_per_page' => 0
        );

        $params = array_merge($defaultParams, $params);

        $sortingFields = array (
            'id' => "subscription.id",
            'status' => "subscription.status",
            'startDate' => "subscription.startDate",
            'endDate' => "subscription.endDate",
            'customer' => array("user.lastname", "user.firstname"),
            'product' => "productDesc.product",
            'orderId' => "subscription.orderId",
            'price' => "orderItem.price",
            'neverExpires' => "subscription.neverExpires",
            'paidCycles' => "subscription.paidCycles",
            'elapsedCycles' => "subscription.elapsedCycles",
            'updatedAt' => "subscription.updatedAt",
            'createdAt' => "subscription.createdAt",
        );
        $sorting = db_sort($params, $sortingFields, 'updatedAt', 'desc');

		$condition = array();
        $joins = array();
        $fields = array();
        $fields[] = 'subscription.*';
        $langCode = !empty($params['langCode']) ? $params['langCode'] : CART_LANGUAGE;

        if (isset($params['id'])) {
			$condition[] = db_quote('subscription.id = ?n', $params['id']);
		}
        if (isset($params['userId'])) {
            $condition[] = db_quote('subscription.userId = ?n', $params['userId']);
        }
        if (isset($params['planId'])) {
            $condition[] = db_quote('subscription.planId = ?n', $params['planId']);
        }
        if (isset($params['orderId'])) {
            $condition[] = db_quote('subscription.orderId = ?n', $params['orderId']);
        }
        if (isset($params['itemId'])) {
            $condition[] = db_quote('subscription.itemId = ?s', $params['itemId']);
        }
        if (isset($params['productId'])) {
            $condition[] = db_quote('subscription.productId = ?n', $params['productId']);
        }
        if (isset($params['status'])) {
            $condition[] = db_quote('subscription.status = ?s', $params['status']);
        }
		if (isset($params['extended'])) {
            $joins[] = db_quote('LEFT JOIN ?:users AS user ON user.user_id = subscription.userId');
            $fields[] = 'user.user_id AS user$id';
            $fields[] = 'user.email AS user$email';
            $fields[] = 'user.firstname AS user$firstName';
            $fields[] = 'user.lastname AS user$lastName';

            $joins[] = db_quote('LEFT JOIN ?:adlss_plans AS plan ON plan.id = subscription.planId');
            $fields[] = 'plan.id AS plan$id';
            $fields[] = 'plan.name AS plan$name';
            $fields[] = 'plan.cycle AS plan$cycle';

            $joins[] = db_quote('LEFT JOIN ?:product_descriptions AS productDesc 
                ON productDesc.product_id = subscription.productId 
                AND productDesc.lang_code = ?s'
                , $langCode
            );
            $fields[] = 'productDesc.product_id AS product$id';
            $fields[] = 'productDesc.product AS product$name';

            $joins[] = db_quote('LEFT JOIN ?:order_details AS orderItem 
                ON orderItem.item_id = subscription.itemId 
                AND orderItem.order_id = subscription.orderId'
            );
            $fields[] = 'orderItem.price AS orderItem$price';
		}

		fn_set_hook('adlss_get_subscriptions', $fields, $this->table, $joins, $condition, $sorting, $limit, $params);

        $joins = empty($joins) ? '' : implode(' ', $joins);
        $fields = empty($fields) ? 'subscription.*' : implode(', ', $fields);
		$condition = !empty($condition) ? ' WHERE ' . implode(' AND ', $condition) . '' : '';

        $limit = '';
        if (isset($params['one'])) {
            $limit = 'LIMIT 0,1';
        } else if (!empty($params['items_per_page'])) {
            $query = db_quote('SELECT COUNT(DISTINCT subscription.id) FROM ?p AS subscription ?p ?p ?p', $this->table, $joins, $condition, $limit);
            $params['total_items'] = db_get_field($query);
            $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
        }

		$query = db_quote('SELECT ?p FROM ?p AS subscription ?p ?p GROUP BY subscription.id ?p ?p', $fields, $this->table, $joins, $condition, $sorting, $limit);
		$items = db_get_array($query);

		if (empty($items)) {
			return array(null, $params);
		}

		foreach ($items as $k => $v) {
			$items[$k] = new Subscription($v);
		}
		fn_set_hook('adlss_get_subscriptions_post', $items, $params);

		if (isset($params['one'])) {
			$items = !empty($items) ? reset($items) : null;
		}

		return array($items, $params);
	}

	/**
	 * @param $userId
	 * @param $orderId
	 *
	 * @return Subscription|Subscription[]|null
	 */
	public function findByUserOrder($userId, $orderId)
	{
		return $this->find(array(
			'userId' => $userId,
			'orderId' => $orderId
		));
	}

	/**
	 * @param $orderId
	 *
	 * @param array $params
	 * @return array|null
	 */
	public function findByOrder($orderId, $params = array())
	{
		$params['orderId'] = $orderId;

		return $this->find($params);
	}

	/**
	 * @param array $params
	 *
	 * @return Subscription|null
	 */
	public function findOne($params = array())
	{
		$params['one'] = true;
		$params['extended'] = true;
        list($subscription, ) = $this->find($params);

		return $subscription;
	}

	/**
	 * @param $id
	 *
	 * @return Subscription|null
	 */
	public function findOneById($id)
	{
		return $this->findOne(array(
			'extended' => true,
			'id' => $id
		));
	}

	/**
	 * @param $orderId
	 *
	 * @return Subscription|null
	 */
	public function findOneByOrderItem($orderId, $itemId)
	{
		return $this->findOne(array(
			'orderId' => $orderId,
			'itemId' => $itemId
		));
	}
}