<?php
/**
 * HELOstore
 *
 * This source file is part of a commercial software. Only users who have purchased a valid license through
 * https://helostore.com/ and accepted to the terms of the License Agreement can install this product.
 *
 * @category   Add-ons
 * @package    HELOstore
 * @copyright  Copyright (c) 2017 HELOstore. (https://helostore.com/)
 * @license    https://helostore.com/legal/license-agreement/   License Agreement
 * @version    $Id$
 */


if (!defined('BOOTSTRAP')) { die('Access denied'); }

if (!empty($_REQUEST['payment']) && !empty($_REQUEST['order_id'])) {
	$orderId = $_REQUEST['order_id'];
	if ( substr( $orderId, 0, 1 ) === 'S' ) {
		fn_adlss_spoof_order_id($orderId);
//		$_REQUEST['subscription_id'] = $orderId
	}
}