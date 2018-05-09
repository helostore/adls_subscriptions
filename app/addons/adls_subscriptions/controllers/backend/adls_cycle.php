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

use HeloStore\ADLSS\Subscription;
use HeloStore\ADLSS\Subscription\CycleManager;
use HeloStore\ADLSS\Subscription\SubscriptionRepository;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if (php_sapi_name() == "cli") {
    error_reporting(E_ALL ^ ( E_NOTICE | E_DEPRECATED));
}


if ($mode == 'preview_alert') {
    $id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 4;
	$subscription = SubscriptionRepository::instance()->findOneById($id);
	CycleManager::instance()->sendAlertExpired($subscription);
	exit;
}

if ($mode == 'check') {
    error_reporting(error_reporting() & ~E_DEPRECATED);

	$action = !empty($action) ? $action : '';
	if ( ! in_array( $action, array( 'expiration', 'alerts' ) ) ) {
		die( 'Invalid action' );
	}

	$cycleManager = CycleManager::instance();
	$cycleManager->runCheck($action);

	exit;
}