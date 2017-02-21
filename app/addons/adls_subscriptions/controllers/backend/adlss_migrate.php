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

use HeloStore\ADLS\ProductManager;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

}

if ($mode == 'subscriptions') {


	list($orders, $search) = fn_get_orders(array(
//		'order_id' => 61
	));

	$migration = \HeloStore\ADLS\Subscription\MigrationManager::instance();

	$i = 0;
	foreach ($orders as $order) {
		$migration->migrate($order);
		if ($i++ > 10) {
//			die('halt');

		}
	}

	aa(count($orders), 1);


	exit;
}