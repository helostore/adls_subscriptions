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

class SubscriptionManager extends Singleton
{
	/**
	 * Create new subscription
	 *
	 * @param integer $productId
	 * @param integer $userId
	 * @param integer $planId
	 *
	 * @return bool|int
	 * 
	 * @throws Exception
	 */
	public function create($userId, $productId, $planId)
	{
		$date = new \DateTime();
		$startDate = clone $date;
		$endDate = clone $date;

		$neverExpires = 0;

		$data = array(
			'product_id' => $productId,
			'user_id' => $userId,
			'plan_id' => $planId,
			'start_date' => $startDate->format('Y-m-d H:i:s'),
			'end_date' => $endDate->format('Y-m-d H:i:s'),
			'never_expires' => $neverExpires,
			'created_at' => $date->format('Y-m-d H:i:s'),
			'update_at' => $date->format('Y-m-d H:i:s'),
		);

		$subscriptionId = db_query('INSERT INTO ?:adls_subscriptions ?e', $data);

		return $subscriptionId;
	}
}