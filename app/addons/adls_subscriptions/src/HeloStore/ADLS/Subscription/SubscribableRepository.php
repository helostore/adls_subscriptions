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

class SubscribableRepository extends EntityRepository
{
    protected $table = '?:adlss_subscribables';

	/**
	 * Create new subscribable
	 *
	 * @param $planId
	 * @param $objectId
	 * @param $objectType
	 *
	 * @return bool|int
	 */
	public function create($planId, $objectId, $objectType)
	{
		$date = new \DateTime();

        $link = $this->findOneByObject($objectId, $objectId);

        if (!empty($link)) {
            return $link['id'];
        }

		$data = array(
			'planId' => $planId,
			'objectId' => $objectId,
			'objectType' => $objectType,
			'createdAt' => $date->format('Y-m-d H:i:s'),
			'updatedAt' => null,
		);

		$query = db_quote('INSERT INTO ?p ?e', $this->table, $data);
		$id = db_query($query);

		return $id;
	}

	/**
	 * Delete subscribable
	 *
	 * @param integer $objectId
	 * @param string $objectType
	 *
	 * @return bool|int
	 */
	public function delete($objectId, $objectType)
	{
		return db_query('DELETE FROM ?p WHERE objectType = ?s AND objectId = ?d', $this->table, $objectType, $objectId);
	}

	/**
	 * @param array $params
	 *
	 * @return array|null
	 */
	public function find($params = array())
	{
		$condition = array();
		if (!empty($params['id'])) {
			$condition[] = db_quote('id = ?n', $params['id']);
		}
		if (!empty($params['objectType'])) {
			$condition[] = db_quote('objectType = ?s', $params['objectType']);

			if (!empty($params['objectId'])) {
				$condition[] = db_quote('objectId = ?n', $params['objectId']);
			}
		}

		$condition = !empty($condition) ? ' OR ('. implode(' AND ', $condition) . ')' : '';
		$query = db_quote('SELECT * FROM ?p WHERE 1=2 ?p LIMIT 0,1', $this->table, $condition);

		$items = db_get_array($query);
		if (empty($items)) {
			return null;
		}

		if (!empty($params['one'])) {
			$items = !empty($items) ? reset($items) : null;
		}

		return $items;
	}

	/**
	 * @param array $params
	 *
	 * @return array|null
	 */
	public function findOne($params = array())
	{
		$params['one'] = true;

		return $this->find($params);
	}


	/**
	 * @param $objectId
	 * @param $objectType
	 *
	 * @return array|null
	 *
	 */
	public function findOneByObject($objectId, $objectType)
	{
		return $this->findOne(array(
			'objectId' => $objectId,
			'objectType' => $objectType
		));
	}
}