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
		);

		$id = db_query('INSERT INTO ?:adlss_plans ?e', $data);

		return $id;
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
	 * @return Plan[]|Plan|null
	 */
	public function find($params = array())
	{
		$condition = array();
		if (isset($params['id'])) {
			$condition[] = db_quote('plan.id = ?n', $params['id']);
		}

		if (!empty($params['status'])) {
			$params['status'] = is_array($params['status']) ? $params['status'] : array($params['status']);
			$condition[] = db_quote('plan.status IN (?a)', $params['status']);
		}

		$condition = !empty($condition) ? ' WHERE '. implode(' AND ', $condition) . '' : '';

		$query = db_quote('SELECT * FROM ?p AS plan ?p', $this->table, $condition);


		$items = db_get_array($query);
		if (empty($items)) {
			return null;
		}

		foreach ($items as $k => $v) {
			$items[$k] = new Plan($v);
		}

		if (isset($params['one'])) {
			$items = !empty($items) ? reset($items) : null;
		}

		return $items;
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