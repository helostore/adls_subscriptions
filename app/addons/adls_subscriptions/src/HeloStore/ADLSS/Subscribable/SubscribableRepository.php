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
namespace HeloStore\ADLSS\Subscribable;

use Exception;
use HeloStore\ADLSS\Base\EntityRepository;
use HeloStore\ADLSS\Subscribable;
use HeloStore\ADLSS\Utils;

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
		$date = Utils::instance()->getCurrentDate();

        $link = $this->findOneByObject($objectId, $objectId);

        if (!empty($link)) {
            return $link->getId();
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
	 * Update subscribable
	 *
	 * @param $subscribable Subscribable
	 *
	 * @return bool|int
	 */
	public function update(Subscribable $subscribable)
	{
		$date = Utils::instance()->getCurrentDate();

        $subscribable
            ->setUpdatedAt($date);

        $data = $subscribable->toArray();

        $query = db_quote('UPDATE ?p SET ?u WHERE id = ?i', $this->table, $data, $subscribable->getId());
		$result = db_query($query);

		return $result;
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
		if (isset($params['id'])) {
			$condition[] = db_quote('id = ?n', $params['id']);
		}
		if (isset($params['objectType'])) {
			$condition[] = db_quote('objectType = ?s', $params['objectType']);

			if (isset($params['objectId'])) {
				$params['objectId'] = is_array($params['objectId']) ? $params['objectId'] : array($params['objectId']);
				$condition[] = db_quote('objectId IN (?a)', $params['objectId']);
			}
		}

		$condition = !empty($condition) ? ' WHERE '. implode(' AND ', $condition) . '' : '';
		$query = db_quote('SELECT * FROM ?p ?p LIMIT 0,1', $this->table, $condition);

		$items = db_get_array($query);
		if (empty($items)) {
			return null;
		}

        foreach ($items as $k => $v) {
            $items[$k] = new Subscribable($v);
        }

		if (isset($params['one'])) {
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
	 * @return Subscribable|null
	 *
	 */
	public function findOneByObject($objectId, $objectType)
	{
		return $this->findOne(array(
			'objectId' => $objectId,
			'objectType' => $objectType
		));
	}

	/**
	 * @param $id
	 *
	 * @return Subscribable|null
	 *
	 */
	public function findOneById($id)
	{
		return $this->findOne(array(
			'id' => $id,
		));
	}

    public function findProductSubscribableOptions($productId)
    {
        $query = db_quote('
            SELECT po.option_id
            FROM ?:product_options AS po 
            LEFT JOIN ?:product_global_option_links AS gpo ON gpo.option_id = po.option_id AND gpo.product_id = ?i
            LEFT JOIN ?p AS sl ON sl.objectType = ?s AND sl.objectId = po.option_id
            WHERE 
                (po.product_id = ?i OR gpo.product_id = ?i)
                AND sl.id IS NOT NULL
            '
            , $productId
            , $this->getTable()
            , Subscribable::OBJECT_TYPE_PRODUCT_OPTION
            , $productId
            , $productId
        );
        return db_get_fields($query);
    }
    public function isProductSubscribable($productId)
    {
        $subscribableOptionIds = $this->findProductSubscribableOptions($productId);

        return !empty($subscribableOptionIds);
    }
}