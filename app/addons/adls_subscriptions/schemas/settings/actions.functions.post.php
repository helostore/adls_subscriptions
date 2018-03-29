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

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * @return array
 */
function fn_settings_variants_addons_adls_subscriptions_order_status_fulfill() {
	$paidStatuses = array( 'P', 'C' );
	$inventoryDecreaseStatuses = fn_get_order_paid_statuses();
	$companyId = 0;
	$orderStatuses = fn_get_statuses(STATUSES_ORDER, array(), true, false, DESCR_SL, $companyId);
	$list = array();
	foreach ( $orderStatuses as $code => $status ) {
		if ( ! in_array($code, $inventoryDecreaseStatuses) ) {
			continue;
		}
		$list[ $code ] = $status['description'];
	}

	return $list;
}


/**
 * @return array
 */
function fn_settings_variants_addons_adls_subscriptions_order_status_on_suspend()
{
	$statuses = fn_get_simple_statuses(STATUSES_ORDER);

	return $statuses;
}
