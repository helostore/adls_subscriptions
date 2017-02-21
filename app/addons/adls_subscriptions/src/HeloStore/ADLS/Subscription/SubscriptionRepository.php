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
	 * @param $companyId
	 *
	 * @return bool|int
	 */
	public function create($userId, $planId, $orderId, $itemId, $productId, $companyId)
	{
		$date = new \DateTime();

		$neverExpires = 0;

		$data = array(
			'userId' => $userId,
			'planId' => $planId,
			'orderId' => $orderId,
			'itemId' => $itemId,
			'productId' => $productId,
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

		$subscriptionId = db_query('INSERT INTO ' . $this->table . ' ?e', $data);

		return $subscriptionId;
	}

	/**
	 * @param Subscription $subscription
	 */
	public function update(Subscription $subscription)
	{
		$subscription->setUpdatedAt(new \DateTime());
		$data = $subscription->toArray();
		$data['updatedAt'] = Utils::instance()->getCurrentDate()->format('Y-m-d H:i:s');
        $query = db_quote('UPDATE ' . $this->table . ' SET ?u WHERE id = ?d', $data, $subscription->getId());
		$result = db_query($query);

//		return $result;
	}

	/**
	 * @param array $params
	 *
	 * @return Subscription[]|Subscription|null
	 */
	public function find($params = array())
	{
		$condition = array();
		if (isset($params['id'])) {
			$condition[] = db_quote('id = ?n', $params['id']);
		}

        if (isset($params['userId'])) {
            $condition[] = db_quote('userId = ?n', $params['userId']);
        }
        if (isset($params['planId'])) {
            $condition[] = db_quote('planId = ?n', $params['planId']);
        }
        if (isset($params['orderId'])) {
            $condition[] = db_quote('orderId = ?n', $params['orderId']);
        }
        if (isset($params['itemId'])) {
            $condition[] = db_quote('itemId = ?s', $params['itemId']);
        }
        if (isset($params['productId'])) {
            $condition[] = db_quote('productId = ?n', $params['productId']);
        }
        if (isset($params['status'])) {
            $condition[] = db_quote('status = ?s', $params['status']);
        }

        $limit = '';
        if (isset($params['one'])) {
            $limit = 'LIMIT 0,1';
        }

		$condition = !empty($condition) ? ' WHERE ' . implode(' AND ', $condition) . '' : '';
		$query = db_quote('SELECT * FROM ?p ?p ?p', $this->table, $condition, $limit);

		$items = db_get_array($query);
		if (empty($items)) {
			return null;
		}

		foreach ($items as $k => $v) {
			$items[$k] = new Subscription($v);
		}

		if (isset($params['one'])) {
			$items = !empty($items) ? reset($items) : null;
		}

		return $items;
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
	 * @return Subscription|Subscription[]|null
	 */
	public function findByOrder($orderId)
	{
		return $this->find(array(
			'orderId' => $orderId
		));
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


}