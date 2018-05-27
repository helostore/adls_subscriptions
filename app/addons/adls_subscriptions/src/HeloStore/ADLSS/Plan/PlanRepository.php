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
namespace HeloStore\ADLSS\Plan;

use HeloStore\ADLSS\Base\EntityRepository;
use HeloStore\ADLSS\Plan;
use HeloStore\ADLSS\Subscribable\SubscribableRepository;
use HeloStore\ADLSS\Utils;

class PlanRepository extends EntityRepository
{
    protected $table = '?:adlss_plans';

    /**
	 * Create new Plan
	 *
	 * @param string $name
	 * @param string $cycle
	 *
	 * @return bool|int
	 */
	public function create($name, $cycle)
	{
		$date = Utils::instance()->getCurrentDate();

		$data = array(
			'name' => $name,
			'cycle' => $cycle,
			'createdAt' => $date->format('Y-m-d H:i:s'),
			'updateAt' => null,
            'status' => Plan::STATUS_ACTIVE
		);

		$id = db_query('INSERT INTO ?:adlss_plans ?e', $data);

		return $id;
	}


	/**
	 * @param Plan $plan
	 *
	 * @return bool
	 */
	public function update(Plan $plan)
	{
        $plan->setUpdatedAt(new \DateTime());
		$data = $plan->toArray();
		$data['updatedAt'] = Utils::instance()->getCurrentDate()->format('Y-m-d H:i:s');
		$query = db_quote('UPDATE ' . $this->table . ' SET ?u WHERE id = ?d', $data, $plan->getId());
		db_query($query);

		return true;
	}

	/**
	 * Delete plan
	 *
	 * @param integer $planId
	 *
	 * @return bool|int
	 */
	public function delete($planId)
	{
		return db_query('DELETE FROM ?p WHERE id = ?d', $this->table, $planId);
	}

	/**
	 * @param array $params
	 *
	 * @return array|Plan|null
	 */
	public function find($params = array())
	{
		$condition = array();
		if (isset($params['id'])) {
			$condition[] = db_quote('plan.id = ?i', $params['id']);
		}

		if (!empty($params['status'])) {
			$params['status'] = is_array($params['status']) ? $params['status'] : array($params['status']);
			$condition[] = db_quote('plan.status IN (?a)', $params['status']);
		}

		if (isset($params['cycle'])) {
			$condition[] = db_quote('plan.cycle = ?i', $params['cycle']);
		}

		$condition = !empty($condition) ? ' WHERE '. implode(' AND ', $condition) . '' : '';


        $limit = '';
        $joins = '';
        if (isset($params['one'])) {
            $limit = 'LIMIT 0,1';
        } else if (!empty($params['items_per_page'])) {
            $query = db_quote('SELECT COUNT(DISTINCT plan.id) FROM ?p AS plan ?p ?p GROUP BY plan.id ?p', $this->table, $joins, $condition, $limit);
            $params['total_items'] = db_get_field($query);
            $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
        }

		$query = db_quote('SELECT * FROM ?p AS plan ?p GROUP BY plan.id ?p', $this->table, $condition, $limit);

		$items = db_get_array($query);
		if (empty($items)) {
			return null;
		}

		foreach ($items as $k => $v) {
			$items[$k] = new Plan($v);
		}

		if (isset($params['one'])) {
			$item = !empty($items) ? reset($items) : null;

            return $item;
		}

        return array($items, $params);
	}

	/**
	 * @param array $params
	 *
	 * @return Plan|null
	 */
	public function findOne($params = array())
	{
		$params['one'] = true;

		return $this->find($params);
	}

	/**
	 * @param $id
	 *
	 * @return Plan|null
	 */
	public function findOneById($id)
	{
		return $this->findOne(array(
			'id' => $id
		));
	}
}