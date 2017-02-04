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
     *
     * @return bool|int
     */
	public function create($userId, $planId, $orderId, $itemId, $productId)
	{
		$date = new \DateTime();
		$startDate = clone $date;
		$endDate = clone $date;

		$neverExpires = 0;

		$data = array(
			'userId' => $userId,
			'planId' => $planId,
			'orderId' => $orderId,
			'itemId' => $itemId,
			'productId' => $productId,
            'status' => Subscription::STATUS_INACTIVE,
			'startDate' => $startDate->format('Y-m-d H:i:s'),
			'endDate' => $endDate->format('Y-m-d H:i:s'),
			'neverExpires' => $neverExpires,
			'createdAt' => $date->format('Y-m-d H:i:s'),
			'updateAt' => $date->format('Y-m-d H:i:s'),
		);

		$subscriptionId = db_query('INSERT INTO ' . $this->table . ' ?e', $data);

		return $subscriptionId;
	}

	/**
	 * @param array $params
	 *
	 * @return array|Subscription|null
	 */
	public function find($params = array())
	{
		$condition = array();
		if (!empty($params['id'])) {
			$condition[] = db_quote('id = ?n', $params['id']);
		}

        if (!empty($params['userId'])) {
            $condition[] = db_quote('userId = ?n', $params['userId']);
        }
        if (!empty($params['planId'])) {
            $condition[] = db_quote('planId = ?n', $params['planId']);
        }
        if (!empty($params['orderId'])) {
            $condition[] = db_quote('orderId = ?n', $params['orderId']);
        }
        if (!empty($params['itemId'])) {
            $condition[] = db_quote('itemId = ?s', $params['itemId']);
        }
        if (!empty($params['productId'])) {
            $condition[] = db_quote('productId = ?n', $params['productId']);
        }
        if (!empty($params['status'])) {
            $condition[] = db_quote('status = ?s', $params['status']);
        }


		$condition = !empty($condition) ? ' OR ('. implode(' AND ', $condition) . ')' : '';
		$query = db_quote('SELECT * FROM ?p WHERE 1=2 ?p LIMIT 0,1', $this->table, $condition);

		$items = db_get_array($query);
		if (empty($items)) {
			return null;
		}

		foreach ($items as $k => $v) {
			$items[$k] = new Subscription($v);
		}

		if (!empty($params['one'])) {
			$items = !empty($items) ? reset($items) : null;
		}

		return $items;
	}

	/**
	 * @param array $params
	 *
	 * @return Subscription|null
	 */
	public function findOne($params = array())
	{
		$params['one'] = true;

		return $this->find($params);
	}

	/**
	 * @param $id
	 *
	 * @return Subscription|null
	 */
	public function findOneById($id)
	{
		return $this->findOne(array(
			'id' => $id
		));
	}

    /**
     * @param Subscription $subscription
     */
    public function update(Subscription $subscription)
    {
        $date = new \DateTime();

        $data = $subscription->toArray();
        $data['updateAt'] = $date->format('Y-m-d H:i:s');

        return db_query('UPDATE ' . $this->table . ' SET ?u', $data);
    }


}