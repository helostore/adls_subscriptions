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
namespace HeloStore\ADLSS\Payment;

use HeloStore\ADLSS\Base\EntityRepository;
use HeloStore\ADLSS\Utils;

class PaymentRepository extends EntityRepository
{
	protected $table = '?:adlss_subscription_payments';

	/**
	 * Create new subscription payment
	 *
	 * @param $userId
	 * @param $orderId
	 * @param $subscriptionId
	 * @param $companyId
	 * @param $methodId
	 * @param $amount
	 *
	 * @return bool|int
	 */
	public function create($userId, $orderId, $subscriptionId, $companyId, $methodId, $amount)
	{
		$date = Utils::instance()->getCurrentDate();
		$data = array(
			'userId' => $userId,
			'subscriptionId' => $subscriptionId,
			'orderId' => $orderId,
			'methodId' => $methodId,
			'amount' => $amount,
			'companyId' => $companyId,
			'status' => Payment::STATUS_OPENED,
			'createdAt' => $date->format('Y-m-d H:i:s'),
			'updateAt' => $date->format('Y-m-d H:i:s'),
		);
		$id = db_query('INSERT INTO ' . $this->table . ' ?e', $data);

		return $id;
	}


	/**
	 * @param array $params
	 *
	 * @return Payment[]|Payment|null
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
			'id' => "payment.id",
			'status' => "payment.status",
			'customer' => array("user.lastname", "user.firstname"),
			'orderId' => "payment.orderId",
			'amount' => "payment.amount",
			'updatedAt' => "payment.updatedAt",
			'createdAt' => "payment.createdAt",
		);
		$sorting = db_sort($params, $sortingFields, 'updatedAt', 'desc');

		$condition = array();
		$joins = array();
		$fields = array();
		$fields[] = 'payment.*';

		if (isset($params['id'])) {
			$condition[] = db_quote('payment.id = ?n', $params['id']);
		}
		if (isset($params['userId'])) {
			$condition[] = db_quote('payment.userId = ?n', $params['userId']);
		}
		if (isset($params['orderId'])) {
			$condition[] = db_quote('payment.orderId = ?n', $params['orderId']);
		}
		if (isset($params['status'])) {
			$condition[] = db_quote('payment.status = ?s', $params['status']);
		}

		$joins = empty($joins) ? '' : implode(' ', $joins);
		$fields = empty($fields) ? 'payment.*' : implode(', ', $fields);
		$condition = !empty($condition) ? ' WHERE ' . implode(' AND ', $condition) . '' : '';


		$limit = '';
		if (isset($params['one'])) {
			$limit = 'LIMIT 0,1';
		} else if (!empty($params['items_per_page'])) {
			$query = db_quote('SELECT COUNT(DISTINCT payment.id) FROM ?p AS payment ?p ?p ?p', $this->table, $joins, $condition, $limit);
			$params['total_items'] = db_get_field($query);
			$limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
		}

		$query = db_quote('SELECT ?p FROM ?p AS payment ?p ?p ?p ?p', $fields, $this->table, $joins, $condition, $sorting, $limit);

		$items = db_get_array($query);

		if (empty($items)) {
			return array(null, $params);
		}

		foreach ($items as $k => $v) {
			$items[$k] = new Payment($v);
		}

		if (isset($params['one'])) {
			$items = !empty($items) ? reset($items) : null;
		}

		return array($items, $params);
	}

}