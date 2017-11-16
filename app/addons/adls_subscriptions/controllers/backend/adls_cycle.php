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


if ($mode == 'preview_alert') {
	$subscription = SubscriptionRepository::instance()->findOneById(4);
	CycleManager::instance()->sendAlertExpired($subscription);
	exit;
}
if ($mode == 'check') {

	$action = !empty($action) ? $action : '';
	if ( ! in_array( $action, array( 'expiration', 'alerts' ) ) ) {
		die( 'Invalid action' );
	}

	$subscriptionRepository = SubscriptionRepository::instance();
	list($subscriptions, ) = $subscriptionRepository->find(array(
		'status' => Subscription::STATUS_ACTIVE
	));
	if (empty($subscriptions)) {
		exit;
	}
	$cycleManager = CycleManager::instance();

	/** @var Subscription $subscription */
	foreach ($subscriptions as $subscription) {
		if ( $action === 'expiration' ) {
			$cycleManager->checkExpiration($subscription);
		}

		if ( $action === 'alerts' ) {
			$cycleManager->checkAlerts($subscription);
		}
	}
}