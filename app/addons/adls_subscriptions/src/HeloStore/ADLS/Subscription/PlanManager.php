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

class PlanManager extends Singleton
{
	/**
	 * Create new Plan
	 *
	 * @param $name
	 *
	 * @return bool|int
	 * 
	 * @throws Exception
	 */
	public function create($name)
	{
		$date = new \DateTime();

		$data = array(
			'name' => $name,
			'created_at' => $date->format('Y-m-d H:i:s'),
			'update_at' => $date->format('Y-m-d H:i:s'),
		);

		$id = db_query('INSERT INTO ?:adlss_plans ?e', $data);

		return $id;
	}
}